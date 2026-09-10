<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;
use App\Models\WorkspaceMembership;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkspaceFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_create_a_workspace(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('workspaces.store'), [
            'name' => 'Family Streaming',
        ]);

        $workspace = Workspace::where('owner_id', $user->id)->first();

        $this->assertNotNull($workspace);
        $this->assertSame('Family Streaming', $workspace->name);
        $this->assertTrue($workspace->hasMember($user));
        $this->assertSame('owner', $workspace->roleOf($user));
    }

    /** @test */
    public function a_workspace_has_a_unique_slug(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('workspaces.store'), ['name' => 'Shared AWS']);
        $this->actingAs($user)->post(route('workspaces.store'), ['name' => 'Shared AWS']);

        $this->assertDatabaseCount('workspaces', 2);
    }

    /** @test */
    public function a_workspace_owner_can_invite_a_member_by_email(): void
    {
        $owner = User::factory()->create(['email' => 'owner@example.test']);
        $target = User::factory()->create(['email' => 'teammate@example.test']);

        $workspace = Workspace::factory()->create(['owner_id' => $owner->id]);
        WorkspaceMembership::create([
            'workspace_id' => $workspace->id,
            'user_id' => $owner->id,
            'role' => 'owner',
        ]);

        $this->actingAs($owner)->post(route('workspaces.invite', $workspace), [
            'email' => $target->email,
            'role' => 'member',
        ]);

        $invitation = WorkspaceInvitation::where('email', $target->email)
            ->where('workspace_id', $workspace->id)
            ->first();

        $this->assertNotNull($invitation);
        $this->assertSame('member', $invitation->role);
        $this->assertFalse($invitation->isExpired());
    }

    /** @test */
    public function a_non_owner_cannot_invite_members(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();

        $workspace = Workspace::factory()->create(['owner_id' => $owner->id]);
        WorkspaceMembership::create([
            'workspace_id' => $workspace->id,
            'user_id' => $owner->id,
            'role' => 'owner',
        ]);
        WorkspaceMembership::create([
            'workspace_id' => $workspace->id,
            'user_id' => $member->id,
            'role' => 'member',
        ]);

        $this->actingAs($member)->post(route('workspaces.invite', $workspace), [
            'email' => 'newperson@example.test',
        ]);

        $this->assertDatabaseMissing('workspace_invitations', [
            'workspace_id' => $workspace->id,
        ]);

        $this->assertStringContainsString('Only the workspace owner', session('error'));
    }

    /** @test */
    public function a_user_can_accept_an_invitation_via_token(): void
    {
        $inviter = User::factory()->create();
        $invitee = User::factory()->create(['email' => 'joining@example.test']);

        $workspace = Workspace::factory()->create(['owner_id' => $inviter->id]);
        WorkspaceMembership::create([
            'workspace_id' => $workspace->id,
            'user_id' => $inviter->id,
            'role' => 'owner',
        ]);

        $invitation = WorkspaceInvitation::create([
            'workspace_id' => $workspace->id,
            'inviter_id' => $inviter->id,
            'email' => $invitee->email,
            'role' => 'admin',
            'token' => 'abc-token-123',
        ]);

        $this->actingAs($invitee)->get(route('workspaces.accept', ['token' => $invitation->token]));

        $this->assertTrue($workspace->hasMember($invitee));
        $this->assertSame('admin', $workspace->roleOf($invitee));
    }

    /** @test */
    public function an_expired_invitation_cannot_be_accepted(): void
    {
        $user = User::factory()->create();

        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        WorkspaceMembership::create([
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'role' => 'owner',
        ]);

        $invitation = WorkspaceInvitation::create([
            'workspace_id' => $workspace->id,
            'inviter_id' => $user->id,
            'email' => 'other@example.test',
            'role' => 'member',
            'token' => 'expired-token',
            'expires_at' => now()->subMinute(),
        ]);

        $this->actingAs(User::factory()->create(['email' => 'other@example.test']))
            ->get(route('workspaces.accept', ['token' => $invitation->token]));

        $this->assertFalse(WorkspaceMembership::where('user_id', $user->id)
            ->where('workspace_id', $workspace->id)->exists());
        $this->assertStringContainsString('invalid or has expired', session('error'));
    }

    /** @test */
    public function workspace_owner_can_remove_a_member(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();

        $workspace = Workspace::factory()->create(['owner_id' => $owner->id]);
        WorkspaceMembership::create([
            'workspace_id' => $workspace->id,
            'user_id' => $owner->id,
            'role' => 'owner',
        ]);
        WorkspaceMembership::create([
            'workspace_id' => $workspace->id,
            'user_id' => $member->id,
            'role' => 'member',
        ]);

        $this->actingAs($owner)->delete(route('workspaces.members.remove', [
            $workspace,
            $member,
        ]));

        $this->assertFalse($workspace->hasMember($member));
        $this->assertDatabaseMissing('workspace_members', [
            'workspace_id' => $workspace->id,
            'user_id' => $member->id,
        ]);
    }
}
