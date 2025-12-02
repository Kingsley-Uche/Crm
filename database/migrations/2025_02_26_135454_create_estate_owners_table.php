<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('estate_owners', function (Blueprint $table) {
            $table->id();
            $table->string('fName', 191)->nullable();
            $table->string('lName', 191)->nullable();
            $table->string('email')->unique()->index()->nullable();
            $table->string('phones')->nullable();
            $table->enum('means_of_identification', ['passport', 'nin', 'driver_licence', 'nis'])->nullable();
            $table->string('identification_image')->nullable();
            $table->text('address')->nullable();
            $table->string('next_of_kin', 255)->nullable();
            $table->string('next_of_kin_phone')->nullable();
            $table->string('bank_name', 255)->nullable();
            $table->char('account_number', 10)->nullable();
            $table->enum('status', ['active', 'inactive', 'pending'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });

        // Manually define the index with prefix lengths
        DB::statement('ALTER TABLE estate_owners ADD INDEX estate_owners_name_idx (fName(100), lName(100))');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estate_owners');
    }
};
