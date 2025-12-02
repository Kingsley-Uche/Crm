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
        Schema::create('park_tracks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('permit_id');
            $table->timestamp('inbound_time')->nullable();
            $table->timestamp('outbound_time')->nullable();
             $table->string('inbound_admin_id')->nullable();
            $table->string('outbound_admin_id')->nullable();
            $table->timestamps();

            // Optional: Add foreign key constraint if permit_id references park_permits table
            $table->foreign('permit_id')->references('id')->on('park_permits')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('park_tracks');
    }
};
