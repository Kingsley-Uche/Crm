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
        Schema::create('admin_models', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('fName'); // First name
            $table->string('lName'); // Last name
            $table->string('email')->unique(); // Email (unique)
            $table->string('phone')->nullable(); // Phone number (optional)
            $table->unsignedBigInteger('created_by_admin_id')->nullable(); // Foreign key (unsigned)
            $table->string('password'); // Password
            $table->boolean('is_active')->default(1); // Active status, defaulting to 1 (active)
            $table->boolean('is_system_admin')->default(0); // Is system admin flag, defaulting to 0 (not a system admin)
            $table->timestamp('email_verified_at')->nullable(); // Email verification timestamp
            $table->rememberToken(); // For 'remember me' functionality
            $table->timestamps(); // Created at and Updated at timestamps
            $table->unsignedBigInteger('role_id')->nullable(); // Foreign key for role, nullable if not assigned
            // Foreign key constraint for created_by_admin_id if referencing another admin
            $table->foreign('created_by_admin_id')->references('id')->on('admin_models')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_models');
    }
};
