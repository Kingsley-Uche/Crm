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
        Schema::create('block_shelters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('block_models_id'); // Foreign key, referencing the blocks table
            $table->integer('shelter_id');
            $table->integer('shelter_qty');
            $table->unsignedBigInteger('estate_owner_id'); // Foreign key, referencing the estate owners table
            $table->timestamps();

            // Foreign key constraints (optional, if these fields reference other tables)
            // If block_id references the id in a 'blocks' table:
            $table->foreign('block_models_id')->references('id')->on('block_models')->onDelete('cascade');

            // If estate_owner_id references the id in an 'estate_owners' table:
            $table->foreign('estate_owner_id')->references('id')->on('estate_owners')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_shelters');
    }
};
