<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permit_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permit_id')
                  ->constrained('park_permits', 'id', 'permit_taxes_permit_id_fk')
                  ->onDelete('cascade');

            $table->foreignId('tax_id')
                  ->constrained('park_taxes', 'id', 'permit_taxes_tax_id_fk')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permit_taxes');
    }
};