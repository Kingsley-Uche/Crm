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
            $table->unsignedBigInteger('block_models_id');
            $table->unsignedBigInteger('block_shelter_id');
            $table->unsignedBigInteger('shelter_id');
            $table->unsignedBigInteger('pay_frequency_id')->nullable();
            
            $table->string('tenancy_type')->nullable();
            
            // Apartment fee column
            $table->decimal('fee', 20, 2)->nullable();
            $table->string('pro_sco_code')->nullable();
            $table->string('property_ref')->nullable();
            $table->string('ownership')->nullable();
            $table->string('admin_unit')->nullable();
            $table->string('unit_number')->nullable();
            $table->string('post_code')->nullable();
            $table->string('address')->nullable();
            
            // Unique code column
            $table->string('unique_code', 16)->unique();
            
            // Foreign key constraints with references and onDelete cascade
            $table->foreign('block_models_id')->references('id')->on('block_models')->onDelete('cascade');
            $table->foreign('shelter_id')->references('id')->on('shelters')->onDelete('cascade');
             $table->foreign('block_shelter_id')->references('id')->on('block_shelters')->onDelete('cascade');
            $table->foreign('pay_frequency_id')->references('id')->on('payment_times')->onDelete('set null');
            
            // Indexes for foreign keys
            $table->index('block_models_id');
            $table->index('block_shelter_id');
            $table->index('shelter_id');
            $table->index('pay_frequency_id');
            
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
