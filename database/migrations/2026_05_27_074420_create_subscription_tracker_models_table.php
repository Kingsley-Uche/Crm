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
        Schema::create('trackers', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('plan_id');

            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();

            $table->decimal('fee', 10, 2)->default(0);
            $table->string('plan_name');
            $table->json('plan_features')->nullable();

            $table->enum('status', [
                'active',
                'inactive',
                'expired',
                'pending',
                'suspended'
            ])->default('pending');

            $table->timestamps();

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracker');
    }
};