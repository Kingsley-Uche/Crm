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
        Schema::create('brand_models', function (Blueprint $table) {

            $table->id();

            // Basic Information
            $table->string('name');
            $table->text('description')->nullable();

            // Branding
            $table->string('logo_url')->nullable();
            $table->string('website_url')->nullable();

            // Contact
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('address')->nullable();

            // Core SEO
            $table->string('slug')->unique()->nullable();

            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();

            // Open Graph (Facebook, WhatsApp, LinkedIn)
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();

            $table->string('og_type')
                ->default('website');

            // Twitter / X
            $table->string('twitter_title')->nullable();
            $table->text('twitter_description')->nullable();
            $table->string('twitter_image')->nullable();

            $table->string('twitter_card')
                ->default('summary_large_image');

            // Search Engine
            $table->string('canonical_url')->nullable();

            $table->string('robots')
                ->default('index,follow');

            // Status
            $table->boolean('is_indexed')
                ->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_models');
    }
};