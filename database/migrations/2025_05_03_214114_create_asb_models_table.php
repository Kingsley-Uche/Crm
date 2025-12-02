<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsbModelsTable extends Migration
{
    public function up()
    {
        Schema::create('asb', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->unsignedBigInteger('block_id');
            $table->foreign('block_id')->references('id')->on('block_models')->onDelete('cascade');

            $table->foreignId('apartment_id')->constrained('apartment_identities')->onDelete('cascade');

            // Other fields
            $table->string('unit_number');
            $table->string('status')->nullable();
            $table->string('appointment')->nullable();
            $table->date('completion_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('assigned_to')->nullable();

            $table->string('ref')->unique()->nullable();

            $table->string('reporter_email');
            $table->string('crime_reference')->nullable();
            $table->dateTime('received_date')->nullable();

            $table->string('video')->nullable();
            $table->string('image')->nullable();
            $table->string('audio')->nullable();
            $table->string('document')->nullable();

            $table->string('issue')->nullable();
            $table->text('description');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('asb');
    }
}
