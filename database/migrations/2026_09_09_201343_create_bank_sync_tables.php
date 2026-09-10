<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Open Banking integration: linked bank/e-wallet accounts and the raw
     * transactions ingested from them (Plaid, Maya, GCash, etc.).
     */
    public function up(): void
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('provider', 30); // plaid | maya | gcash | manual
            $table->string('provider_account_id');
            $table->string('display_name', 120);
            $table->string('institution', 120)->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'provider_account_id']);
            $table->index('user_id');
        });

        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->string('provider', 30);
            $table->string('provider_tx_id', 120);
            $table->string('description', 255);
            $table->string('merchant', 120)->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('PHP');
            $table->date('transaction_date');
            $table->boolean('is_subscription')->default(false);
            $table->foreignId('matched_subcription_id')->nullable()->constrained('subcriptions')->nullOnDelete();
            $table->string('status', 30)->default('unclassified'); // unclassified | subscription | candidate | matched
            $table->json('raw')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'provider_tx_id']);
            $table->index(['user_id', 'transaction_date']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_transactions');
        Schema::dropIfExists('bank_accounts');
    }
};
