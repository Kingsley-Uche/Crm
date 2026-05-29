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
        Schema::create('subscription_account', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('plan_id');
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();

            $table->unsignedBigInteger('tracker_id');
            $table->decimal('fee', 10, 2)->default(0);

            $table->enum('status', ['active', 'inactive', 'expired', 'pending', 'suspended'])
                  ->default('pending');

            $table->timestamps();

            // Optional foreign keys if these tables exist
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
            $table->foreign('tracker_id')->references('id')->on('trackers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_account');
    }
};