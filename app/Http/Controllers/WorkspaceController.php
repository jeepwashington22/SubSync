<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;
use App\Services\WorkspaceService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkspaceController extends Controller
{
    public function __construct(private WorkspaceService $service) {}

    /**
     * List the user's workspaces, memberships and pending invitations.
     */
    public function index(): View
    {
        $user = Auth::user();

        $memberships = $user->workspaceMemberships()->with('workspace')->orderBy('created_at')->get();

        $pendingInvitations = WorkspaceInvitation::where('email', $user->email)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->with('workspace')
            ->orderBy('id')
            ->get();

        return view('workspaces.index', compact('memberships', 'pendingInvitations'));
    }

    /**
     * Store a new workspace.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:60',
        ]);

        $this->service->create(Auth::user(), trim($validated['name']));

        return redirect()->route('workspaces.index')->with('success', 'Workspace created.');
    }

    /**
     * Invite a member by email.
     */
    public function invite(Request $request, Workspace $workspace): RedirectResponse
    {
        $blocked = $this->authorize($workspace);

        if ($blocked !== null) {
            return $blocked;
        }

        $validated = $request->validate([
            'email' => 'required|email',
            'role' => 'nullable|string|in:admin,member',
        ]);

        $invitation = $this->service->invite(
            Auth::user(),
            $workspace,
            $validated['email'],
            $validated['role'] ?? 'member',
        );

        if ($invitation === null) {
            return redirect()->route('workspaces.index')->with('error', 'That user is already a member or has a pending invite.');
        }

        return redirect()->route('workspaces.index')->with(
            'success',
            'Invitation sent! Share this link: '.route('workspaces.accept', ['token' => $invitation->token]),
        );
    }

    /**
     * Accept an invitation via its token (not necessarily logged-in email).
     */
    public function accept(string $token): RedirectResponse
    {
        $accepted = $this->service->acceptInvitation(Auth::user(), $token);

        if (! $accepted) {
            return redirect()->route('dashboard')->with('error', 'That invitation is invalid or has expired.');
        }

        return redirect()->route('workspaces.index')->with('success', 'You joined the workspace!');
    }

    /**
     * Remove a member from a workspace.
     */
    public function removeMember(Request $request, Workspace $workspace, User $user): RedirectResponse
    {
        try {
            $this->service->removeMember(Auth::user(), $workspace, $user);
        } catch (\RuntimeException $e) {
            return redirect()->route('workspaces.index')->with('error', $e->getMessage());
        }

        return redirect()->route('workspaces.index')->with('success', 'Member removed.');
    }

    /**
     * Record a percentage cost split for a shared subscription.
     */
    public function setSplit(Request $request, Workspace $workspace): RedirectResponse
    {
        $blocked = $this->authorize($workspace);

        if ($blocked !== null) {
            return $blocked;
        }

        $validated = $request->validate([
            'subcription_id' => 'required|integer',
            'user_id' => 'required|integer',
            'percent_share' => 'required|numeric|min:0|max:100',
        ]);

        $subscription = $workspace->subcriptions()->where('id', $validated['subcription_id'])->first();

        if ($subscription === null) {
            return redirect()->route('workspaces.index')->with('error', 'Subscription not found in this workspace.');
        }

        $member = $workspace->memberships()->where('user_id', $validated['user_id'])->first();

        if ($member === null) {
            return redirect()->route('workspaces.index')->with('error', 'User is not a member of this workspace.');
        }

        $memberUser = User::find($member->user_id);

        $this->service->setSplit($subscription, $memberUser, (float) $validated['percent_share']);

        return redirect()->route('workspaces.index')->with('success', 'Cost split updated.');
    }

    /**
     * Ensure the current user owns the workspace.
     */
    private function authorize(Workspace $workspace): RedirectResponse
    {
        if (! $workspace->isOwner(Auth::user())) {
            return redirect()->route('dashboard')->with('error', 'Only the workspace owner can do that.');
        }

        return null;
    }
}
