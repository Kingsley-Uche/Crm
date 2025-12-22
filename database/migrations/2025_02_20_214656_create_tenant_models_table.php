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
            $table->string('full_name');
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->default('other');
            
            // Step 2: Identification Details
            $table->string('nationality')->nullable();
            $table->string('state')->nullable();
            $table->text('address')->nullable();
            $table->enum('id_method', ['driver_licence', 'nin', 'nis', 'passport'])->nullable();
            $table->string('identification_image')->nullable(); // Path to identification image
            $table->string('passport_photograph')->nullable();  // Path to passport photograph
            
            // Step 3: Contact Details
            $table->string('mobile_number')->unique()->nullable();
            $table->string('home_number')->nullable();
            $table->string('occupant_email')->unique()->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('emergency_email')->nullable();
            
            // Step 4: Guarantor Details
            $table->string('guarantor_full_name')->nullable(); 
            $table->text('guarantor_address')->nullable();
              $table->text('guarantor_email')->nullable();
            $table->string('guarantor_phone')->nullable();
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
