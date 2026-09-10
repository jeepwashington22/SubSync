<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Explicit pivot model for `workspace_user` so role metadata can be
 * read/written without relying on low-level pivot helpers.
 */
class WorkspaceMembership extends Model
{
    protected $table = 'workspace_user';

    protected $fillable = ['workspace_id', 'user_id', 'role'];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
