<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Audit log of every billed amount per subscription. Used by the price
     * hike detection to compare the latest amount against historical records.
     */
    public function up(): void
    {
        Schema::create('billing_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subcription_id')->constrained('subcriptions')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->date('billed_at');
            $table->string('source', 40)->default('manual'); // manual | gmail | bank | webhook
            $table->timestamps();

            $table->index(['subcription_id', 'billed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_histories');
    }
};
