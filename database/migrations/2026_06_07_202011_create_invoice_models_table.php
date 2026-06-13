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
        Schema::create('invoice_models', function (Blueprint $table) {
            $table->id();

            $table->string('invoice_ref')->unique();

            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('apartment_id');
            $table->unsignedBigInteger('location_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();

            $table->decimal('amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('balance', 12, 2)->default(0);

            $table->text('description')->nullable();

            $table->enum('status', [
                'pending',
                'partially_paid',
                'paid',
                'overdue',
                'cancelled',
            ])->default('pending');

            $table->date('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            // Foreign Keys
            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->cascadeOnDelete();

            $table->foreign('apartment_id')
                ->references('id')
                ->on('apartment_identities')
                ->cascadeOnDelete();

            $table->foreign('location_id')
                ->references('id')
                ->on('location_models')
                ->nullOnDelete();

            $table->foreign('branch_id')
                ->references('id')
                ->on('branch_models')
                ->nullOnDelete();
                 $table->index(['tenant_id', 'location_id','apartment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_models');
    }
};