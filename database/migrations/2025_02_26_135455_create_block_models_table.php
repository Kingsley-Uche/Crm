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
        Schema::create('block_models', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('n/a');
            $table->string('address')->nullable();

            // Make sure landlord_id type matches landlords.id (usually unsignedBigInteger)
            $table->unsignedBigInteger('landlord_id')->nullable();

            $table->string('state_name')->nullable();
            $table->string('lgvt_name')->nullable();
            $table->string('country_name')->nullable();
            $table->string('location_id')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('landlord_id')->references('id')->on('estate_owners')->onDelete('cascade');

            // Add indexes
            $table->index('landlord_id');
            $table->index('state_name');
            $table->index('lgvt_name');
            $table->index('country_name');
            $table->index('location_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_models');
    }
};
