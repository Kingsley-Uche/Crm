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
        Schema::create('pest_models', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('block_id');
            $table->unsignedBigInteger('apartment_id');
            $table->string('issue_type');
            $table->text('description')->nullable();
            $table->string('status')->nullable();
            $table->string('ref')->unique();
            $table->string('image')->nullable();
            $table->date('received_date')->nullable();
            $table->enum('progress', ['In Progress', 'Not Started', 'Completed'])->nullable();
            $table->string('deadline_timeframe')->nullable();
            $table->string('appointment_timeframe')->nullable();
            $table->string('action_timeline')->nullable();
            $table->string('assigned_to')->nullable();
            $table->date('due_date')->nullable();
            $table->date('appointment')->nullable();
            $table->date('completion_date')->nullable();
            $table->decimal('pest_control_fee', 10, 2)->nullable();
            $table->timestamps();

            $table->foreign('block_id')->references('id')->on('block_models')->onDelete('cascade');
            // You can optionally add a foreign key for apartment_id if that model/table exists
            $table->foreign('apartment_id')->references('id')->on('apartment_identities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pest_models');
    }
};
