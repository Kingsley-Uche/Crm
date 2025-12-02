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
    Schema::create('rent_cycles', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('tenant_id');
        $table->unsignedBigInteger('apartment_id');
        $table->unsignedBigInteger('rent_account_id');
         $table->unsignedBigInteger('booking_models_id');
        $table->string('unit_number');
        $table->string('created_by_user_type')->nullable(); // Assuming this is a string to indicate user type (e.g., admin, tenant)
        $table->date('start_date');
        $table->date('end_date');
        $table->decimal('rent_fee', 10, 2);
         $table->decimal('payment_made', 10, 2);
       $table->decimal('duration_months', 6, 4);
        $table->string('account_type');
        $table->string('payment_method');
        $table->string('escalation_policy');
        $table->enum('status', ['pending', 'active', 'expired', 'terminated'])->default('active');
        $table->unsignedBigInteger('created_by')->nullable(); // Optional user who created it
        $table->timestamps();
        $table->softDeletes();

        // Foreign key constraints
        $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
         $table->foreign('booking_models_id')->references('id')->on('booking_models')->onDelete('cascade');
        $table->foreign('apartment_id')->references('id')->on('apartment_identities')->onDelete('cascade');
         $table->foreign('rent_account_id')->references('id')->on('rent_accounts')->onDelete('cascade');
        // Uncomment the line below if you have a users table

        // Indexes
        $table->index(['tenant_id', 'apartment_id']);
        $table->index(['start_date', 'end_date']);
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rent_cycles');
    }
};
