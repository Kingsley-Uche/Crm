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
    Schema::create('booking_models', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('shelter_id');
        $table->unsignedBigInteger('block_model_id');
        $table->unsignedBigInteger('payment_time_id')->nullable();
        $table->date('start_date');
        $table->date('end_date');
        $table->unsignedBigInteger('apartment_id');
        $table->unsignedBigInteger('block_shelter_id'); // If nullable is necessary
        $table->unsignedBigInteger('booked_by_user_id');
        $table->string('booked_by_user_type'); // Assuming this is a string to indicate user type (e.g., admin, tenant)
        $table->unsignedBigInteger('tenant_id'); // Making it nullable
        $table->boolean('is_cancelled')->default(false);
        $table->unsignedBigInteger('updated_by_user_id')->nullable();
        $table->decimal('fee', 15, 2);
        $table->timestamps();
        
        // Foreign key constraints
        $table->foreign('shelter_id')->references('id')->on('shelters')->onDelete('cascade');
        $table->foreign('block_model_id')->references('id')->on('block_models')->onDelete('cascade');
        $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        $table->foreign('block_shelter_id')->references('id')->on('block_shelters');
        // Add more foreign keys as needed
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_models');
    }
};
