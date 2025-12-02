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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            
            // Step 1: Occupant Basic Details
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other']);
            
            // Step 2: Identification Details
            $table->string('nationality');
            $table->string('state');
            $table->text('address');
            $table->enum('id_method', ['driver_licence', 'nin', 'nis', 'passport']);
            $table->string('identification_image'); // Path to identification image
            $table->string('passport_photograph');  // Path to passport photograph
            
            // Step 3: Contact Details
            $table->string('mobile_number')->unique();
            $table->string('home_number')->nullable();
            $table->string('occupant_email')->unique()->nullable();
            $table->string('emergency_contact');
            $table->string('emergency_email')->nullable();
            
            // Step 4: Guarantor Details
            $table->string('guarantor_full_name'); 
            $table->text('guarantor_address')->nullable();
              $table->text('guarantor_email')->nullable();
            $table->string('guarantor_phone');
            $table->string('guarantor_passport')->nullable(); // Path to guarantor passport image
            
            $table->timestamps();
            
            // Index frequently queried columns
            $table->index('occupant_email');
            $table->index('mobile_number');
            $table->index('id_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
