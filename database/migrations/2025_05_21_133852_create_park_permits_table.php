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
    Schema::create('park_permits', function (Blueprint $table) {
        $table->id();
        $table->string('fname');
        $table->string('lname');
        $table->string('phone', 20);
        $table->string('permit_name');
        $table->string('email');
        $table->uuid('uniqueId')->unique();
         $table->string('pass_code')->unique();
        $table->foreignId('park_category_id')->constrained('park_categories')->onDelete('restrict');
        $table->foreignId('park_model_id')->constrained('park_models')->onDelete('restrict');
        $table->dateTime('start_time');
        $table->dateTime('end_time');

        // Enum column for 'read' status with default value
        $table->enum('read', ['yes', 'no'])->default('no');

        $table->decimal('fee', 8, 2);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('park_permits');
}

};