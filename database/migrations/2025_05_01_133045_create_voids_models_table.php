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
        Schema::create('voids', function (Blueprint $table) {
    $table->id();
    $table->string('void_path')->nullable();
    $table->string('void_classification');
    $table->string('hfi_code');
    $table->string('uprn')->index();
    $table->string('property_ref')->index();
    $table->string('ten_reason')->nullable();
    $table->string('void_ref')->index();
    $table->string('address')->index();
    $table->text('updates')->nullable();
    $table->text('previous_call_over')->nullable();
    $table->string('property_type');
    $table->string('property_subtype');
    $table->integer('bedrooms')->nullable();
    $table->string('void_status')->index();
    $table->string('vin_sco_code')->nullable();
    $table->integer('days_void')->nullable();
    $table->date('termination_date')->nullable(); 
    $table->date('ready_for_let_date')->nullable()->index();
    $table->string('management_unit');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voids');
    }
};
