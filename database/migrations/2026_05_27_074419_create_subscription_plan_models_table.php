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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->text('description')->nullable();

            $table->integer('number_admins')->default(0);
            $table->integer('number_branches')->default(0);
            $table->integer('number_apartments')->default(0);
            $table->integer('number_property_managers')->default(0);

            $table->decimal('price_per_month', 10, 2)->default(0);

            $table->integer('discount_min_months')->default(0);

            $table->decimal('discount_percentage', 5, 2)->default(0);

            $table->boolean('is_active')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};