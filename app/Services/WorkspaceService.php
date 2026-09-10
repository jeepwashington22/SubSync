<?php

namespace App\Services;

use App\Models\Subcriptions;
use App\Models\SubscriptionSplit;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;
use App\Models\WorkspaceMembership;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Str;

/**
 * Encapsulates workspace lifecycle: creation, membership, invitations and
 * subscription cost splits.
 */
class WorkspaceService
{
    /** Minutes an invitation remains valid (7 days). */
    public const INVITE_TTL_MINUTES = 60 * 24 * 7;

    /**
     * One workspace per user on first access ("My Workspace").
     */
    public function personalWorkspace(User $user): Workspace
    {
        $existing = Workspace::where('owner_id', $user->id)->orderBy('id')->first();

        if ($existing !== null) {
            return $existing;
        }

        return $this->create($user, 'My Workspace');
    }

    /**
     * Create a workspace and attach the creator as owner/member.
     */
    public function create(User $user, string $name): Workspace
    {
        $workspace = Workspace::create([
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(6),
            'owner_id' => $user->id,
        ]);

        WorkspaceMembership::create([
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'role' => 'owner',
        ]);

        return $workspace;
    }

    /**
     * Invite a user (or future user) by email. Returns the created invitation
     * or null when the invitee is already a member or has a pending invite.
     *
     * @throws RuntimeException when the actor is not the workspace owner.
     */
    public function invite(User $inviter, Workspace $workspace, string $email, string $role = 'member'): ?WorkspaceInvitation
    {
        $email = Str::lower(trim($email));

        if ($inviter->id !== $workspace->owner_id) {
            throw new \RuntimeException('Only the workspace owner can invite members.');
        }

        $member = User::where('email', $email)->first();

        if ($member !== null && $workspace->hasMember($member)) {
            return null;
        }

        $pending = $workspace->invitations()
            ->where('email', $email)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->exists();

        if ($pending) {
            return null;
        }

        return WorkspaceInvitation::create([
            'workspace_id' => $workspace->id,
            'inviter_id' => $inviter->id,
            'email' => $email,
            'role' => $role,
            'token' => Str::random(64),
            'expires_at' => now()->addMinutes(self::INVITE_TTL_MINUTES),
        ]);
    }

    /**
     * Accept an invitation token and attach the user as a member.
     */
    public function acceptInvitation(User $user, string $token): bool
    {
        $invitation = WorkspaceInvitation::where('token', $token)
            ->whereNull('accepted_at')
            ->first();

        if ($invitation === null) {
            return false;
        }

        if ($invitation->isExpired()) {
            return false;
        }

        $invitation->update(['accepted_at' => now()]);

        WorkspaceMembership::updateOrCreate(
            [
                'workspace_id' => $invitation->workspace_id,
                'user_id' => $user->id,
            ],
            ['role' => $invitation->role],
        );

        return true;
    }

    /**
     * Remove a member and purge their subscription splits. The owner cannot
     * be removed.
     */
    public function removeMember(User $actor, Workspace $workspace, User $member): void
    {
        if ($actor->id !== $workspace->owner_id) {
            throw new \RuntimeException('Only the workspace owner can remove members.');
        }

        if ($member->id === $workspace->owner_id) {
            throw new \RuntimeException('The owner cannot be removed.');
        }

        WorkspaceMembership::where('workspace_id', $workspace->id)
            ->where('user_id', $member->id)
            ->delete();

        $subscriptionIds = $workspace->subcriptions()->pluck('id');

        SubscriptionSplit::where('user_id', $member->id)
            ->whereIn('subcription_id', $subscriptionIds)
            ->delete();
    }

    /**
     * Record or refresh a percentage cost split for a shared subscription.
     */
    public function setSplit(Subcriptions $subcription, User $user, float $percentShare): void
    {
        SubscriptionSplit::updateOrCreate(
            [
                'subcription_id' => $subcription->id,
                'user_id' => $user->id,
            ],
            ['percent_share' => $percentShare],
        );

        Log::info('Subscription split updated', [
            'subcription_id' => $subcription->id,
            'user_id' => $user->id,
            'percent_share' => $percentShare,
        ]);
    }
}
