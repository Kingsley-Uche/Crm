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
        Schema::create('shelters', function (Blueprint $table) {
            $table->id();
            $table->string('is_active');
            $table->string('name');       // Name of the shelter (single room, flat, etc.)
            $table->unsignedBigInteger('created_by')->nullable(); // Admin user ID (nullable)
            $table->timestamps();
            
            // Foreign key constraint for 'created_by' to reference 'id' in 'users' table (if not null)
            $table->foreign('created_by')->references('id')->on('admin_models')->onDelete('set null');
            
            // Add indexes
            $table->index('created_by');
            $table->index('is_active');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shelters');
    }
};
