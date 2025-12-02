<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rent_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id'); 
            $table->unsignedBigInteger('apartment_id');
            $table->string('unit_number');
            $table->date('start_date');
            $table->string('account_type');
            $table->enum('status', ['pending', 'active', 'expired', 'terminated'])->default('active');
            $table->unsignedBigInteger('created_by')->nullable(); // Who created this account (optional)
            $table->timestamps();
            $table->softDeletes(); // Soft delete for audit/logging

            // Foreign keys
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('apartment_id')->references('id')->on('apartment_identities')->onDelete('cascade');
            // $table->foreign('created_by')->references('id')->on('users')->nullOnDelete(); // Uncomment if users table exists

            // Indexes
            $table->index(['tenant_id', 'apartment_id']);
            $table->index('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rent_accounts');
    }
};
