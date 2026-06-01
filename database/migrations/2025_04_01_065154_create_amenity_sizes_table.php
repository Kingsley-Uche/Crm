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
        Schema::create('amenity_sizes', function (Blueprint $table) {

            $table->id();

            $table->decimal('amenity_size', 8, 2);
            $table->string('amenity_name');

            $table->foreignId('amenity_id')
                  ->constrained('amenities')
                  ->cascadeOnDelete();

            $table->foreignId('apartment_id')
                  ->constrained('apartment_identities')
                  ->cascadeOnDelete();

            $table->foreignId('branch_id')
                  ->nullable()
                  ->constrained('branch_models')
                  ->nullOnDelete();

            $table->foreignId('location_models_id')
                  ->nullable()
                  ->constrained('location_models')
                  ->nullOnDelete();

            $table->foreignId('shelter_id')
                  ->nullable()
                  ->constrained('shelters')
                  ->nullOnDelete();

            $table->timestamps();

            $table->index('amenity_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amenity_sizes');
    }
};