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
            $table->decimal('amenity_size', 8, 2); // Increased precision for better flexibility
            $table->string('amenity_name'); 
            
            $table->foreignId('amenity_id')
                  ->constrained('amenities')
                  ->onDelete('cascade');

            $table->foreignId('apartment_id')
                  ->constrained('apartment_identities')
                  ->onDelete('cascade');
            
            $table->foreignId('block_models_id')
                  ->constrained('block_models')
                  ->onDelete('cascade');

            $table->foreignId('shelter_id')
                  ->constrained('shelters')
                  ->onDelete('cascade');

            $table->timestamps();

            // Add indexes
            $table->index('amenity_id');
            $table->index('apartment_id');
            $table->index('block_models_id');
            $table->index('shelter_id');
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
