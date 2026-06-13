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
        Schema::create('payment_listing_models', function (Blueprint $table) {
            $table->id();

            $table->string('name');
             $table->integer('qty');
             $table->integer('unit_charge');
            $table->decimal('amount', 12, 2);

            $table->foreignId('invoice_id')
                ->constrained('invoice_models')
                ->cascadeOnDelete();
            $table->foreignId('location_id')
                ->references('id')->on('location_models')
                ->cascadeOnDelete();
            $table->foreignId('apartment_id')
                ->references('id')
                ->on('apartment_identities')
                ->cascadeOnDelete();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->timestamps();
              
$table->index('tenant_id');
$table->index('location_id');
$table->index('apartment_id');
$table->index('invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_listing_models');
    }
};