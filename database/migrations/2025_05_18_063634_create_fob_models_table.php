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
        Schema::create('fobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade')->index();

            $table->string('fob_uid')->unique(); // Unique hardware ID
            $table->string('make')->nullable()->index();   // Manufacturer
            $table->string('model')->nullable()->index();  // Model name/code
            $table->enum('type', ['rfid', 'nfc', 'ble', 'smartcard','other'])->default('rfid')->index();

            $table->enum('fob_status', ['active', 'lost', 'malfunctioning', 'deactivated'])->default('active')->index();
            $table->string('request_reason')->nullable();
            $table->dateTime('request_date')->nullable()->index();
            $table->dateTime('issued_date')->nullable()->index();
            $table->decimal('fee', 8, 2)->default(0);

            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fobs');
    }
};
