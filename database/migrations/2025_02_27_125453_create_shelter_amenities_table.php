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
    Schema::create('shelter_amenities', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('block_models_id'); // Foreign key to the block_models table
        $table->unsignedBigInteger('block_shelter_id'); // Foreign key to the block_shelters table
        $table->unsignedBigInteger('amenity_id'); // Foreign key to the amenities table
        $table->integer('amenity_number'); // The quantity of the amenity
        $table->unsignedBigInteger('id_apartment_id');
        $table->unsignedBigInteger('branch_id')->nullable();
        $table->unsignedBigInteger('location_models_id')->nullable();
        $table->timestamps();

        // Foreign key constraints
        $table->foreign('block_models_id')->references('id')->on('block_models')->onDelete('cascade');
        $table->foreign('block_shelter_id')->references('id')->on('block_shelters')->onDelete('cascade');
        $table->foreign('amenity_id')->references('id')->on('amenities')->onDelete('cascade');
        $table->foreign('id_apartment_id')->references('id')->on('apartment_identities')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shelter_amenities');
    }
};
