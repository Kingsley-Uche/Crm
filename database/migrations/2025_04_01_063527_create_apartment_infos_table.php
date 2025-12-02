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
        Schema::create('apartment_infos', function (Blueprint $table) {
            $table->id();
            $table->string('tenancy_type')->nullable();
            $table->string('pro_sco_code')->nullable();
            $table->string('property_ref')->nullable();
            $table->string('ownership')->nullable();
            $table->string('admin_unit')->nullable();
            $table->string('unit_number')->nullable();
            $table->string('post_code')->nullable();
            $table->string('address')->nullable();
            
            $table->foreignId('apartment_id')
                  ->constrained('apartment_identities') // Assuming 'apartments' is correct
                  ->onDelete('cascade');
            
            $table->foreignId('block_models_id')
                  ->constrained('block_models') // Assuming 'block_models' table exists
                  ->onDelete('cascade');

            $table->foreignId('shelter_id')
                  ->constrained('shelters') // Assuming 'shelters' table exists
                  ->onDelete('cascade');

            $table->timestamps();

            // Add indexes
            $table->index('apartment_id');
            $table->index('block_models_id');
            $table->index('shelter_id');
            $table->index('property_ref');
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apartment_infos');
    }
};
