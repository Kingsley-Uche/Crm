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
        Schema::create('apartment_identities', function (Blueprint $table) {
            $table->id();
            
            // Foreign key columns
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('location_models_id')->nullable();
            $table->unsignedBigInteger('shelter_id')->nullable();
            $table->unsignedBigInteger('pay_frequency_id')->nullable();
            $table->unsignedBigInteger('landlord_id')->nullable();
             $table->unsignedBigInteger('property_manager_id')->nullable();
            
            $table->string('tenancy_type')->nullable();
            
            // Apartment fee column
            $table->decimal('fee', 20, 2)->nullable();
            $table->string('pro_sco_code')->nullable();
            $table->string('property_ref')->nullable();
            $table->string('unit_number')->nullable();
            $table->string('post_code')->nullable();
            $table->string('address')->nullable();

            
            // Unique code column
            $table->string('unique_code', 16)->unique();
            
            // Foreign key constraints with references and onDelete cascade
            $table->foreign('branch_id')->references('id')->on('branch_models')->onDelete('set null');
            $table->foreign('location_models_id')->references('id')->on('location_models')->onDelete('set null');
            $table->foreign('shelter_id')->references('id')->on('shelters')->onDelete('set null');
            $table->foreign('pay_frequency_id')->references('id')->on('payment_times')->onDelete('set null');
            $table->foreign('landlord_id')->references('id')->on('estate_owners')->onDelete('set null');
            // Indexes for foreign keys
            $table->index('branch_id');
            $table->index('location_models_id');
            
            $table->index('shelter_id');
            $table->index('pay_frequency_id');
            $table->index('landlord_id');

            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apartment_identities');
    }
};
