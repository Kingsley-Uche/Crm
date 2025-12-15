<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_models', function (Blueprint $table) {
            $table->id();

            // Foreign Keys & Fields
            $table->foreignId('block_id')->nullable()->constrained('block_models')->onDelete('cascade');
            $table->foreignId('apartment_id')->nullable()->constrained('apartment_identities')->onDelete('cascade');
            $table->string('unit_number')->nullable();

            $table->string('subject');
            $table->text('description')->nullable();
            $table->string('status')->default('pending');
            $table->string('assigned_to')->nullable();

            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('set null');
            $table->foreignId('created_by_admin_id')->nullable()->constrained('admin_models')->onDelete('set null');

            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->date('received_date')->nullable();
            $table->date('resolved_date')->nullable();
            $table->string('action_taken', 400)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_models');
    }
};
