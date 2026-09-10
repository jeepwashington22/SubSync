<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Workspace collaboration: shared ledgers, memberships, invitations
     * and per-subscription cost splits.
     */
    public function up(): void
    {
        Schema::create('workspaces', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // Pivot: workspace <-> user membership with a role.
        Schema::create('workspace_user', function (Blueprint $table) {
            $table->foreignId('workspace_id')->constrained('workspaces')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role')->default('member'); // owner | admin | member
            $table->timestamps();

            $table->primary(['workspace_id', 'user_id']);
            $table->index('user_id');
        });

        // Email-based invitations. A token allows a user who is not yet
        // registered to join after signing up.
        Schema::create('workspace_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained('workspaces')->cascadeOnDelete();
            $table->foreignId('inviter_id')->constrained('users')->cascadeOnDelete();
            $table->string('email');
            $table->string('role')->default('member');
            $table->string('token', 64)->unique();
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();

            $table->index(['email', 'token']);
        });

        // Percentage split of a shared subscription across workspace users.
        Schema::create('subscription_splits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subcription_id')->constrained('subcriptions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('percent_share', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['subcription_id', 'user_id']);
        });

        // Re-parent subscriptions to a workspace while preserving the creator.
        Schema::table('subcriptions', function (Blueprint $table) {
            $table->foreignId('workspace_id')->nullable()->constrained('workspaces')->nullOnDelete()->after('user_id');
            $table->foreignId('added_by_user_id')->nullable()->constrained('users')->nullOnDelete()->after('workspace_id');
            $table->decimal('split_share', 5, 2)->default(100)->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subcriptions', function (Blueprint $table) {
            $table->dropColumn(['workspace_id', 'added_by_user_id', 'split_share']);
        });

        Schema::dropIfExists('subscription_splits');
        Schema::dropIfExists('workspace_invitations');
        Schema::dropIfExists('workspace_user');
        Schema::dropIfExists('workspaces');
    }
};
