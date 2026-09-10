<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workspace extends Model
{
    protected $fillable = ['name', 'slug', 'owner_id'];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /** Pivot rows describing memberships and their roles. */
    public function memberships(): HasMany
    {
        return $this->hasMany(WorkspaceMembership::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(WorkspaceInvitation::class);
    }

    public function subcriptions(): HasMany
    {
        return $this->hasMany(Subcriptions::class, 'workspace_id');
    }

    public function roleOf(User $user): ?string
    {
        $membership = $this->memberships()->where('user_id', $user->id)->first();

        return $membership?->role;
    }

    public function hasMember(User $user): bool
    {
        return $this->memberships()->where('user_id', $user->id)->exists();
    }

    public function isOwner(User $user): bool
    {
        return ($this->owner_id ?? null) === $user->id;
    }
}
