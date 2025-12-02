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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // e.g., Edit Users
            $table->string('slug')->unique(); // e.g., edit_users
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();            // Optional: keeps deleted records for audit or recovery
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
