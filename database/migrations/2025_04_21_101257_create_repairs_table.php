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
        Schema::create('repairs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('block_id')->index();
            $table->unsignedBigInteger('apartment_id')->nullable()->index();
            $table->string('unit_number')->nullable()->index();
            $table->date('received_date')->nullable();
            $table->string('progress')->nullable();
            $table->string('status')->nullable()->index();
            $table->string('repair_type')->nullable();
            $table->string('deadline_timeframe')->nullable();
            $table->string('issue')->nullable();
            $table->string('appointment_timeframe')->nullable();
            $table->text('description')->nullable();
            $table->string('action_timeline')->nullable();
            $table->string('assigned_to')->nullable();
            $table->string('ref')->nullable()->index();
            $table->date('due_date')->nullable();
            $table->string('appointment')->nullable();
            $table->date('completion_date')->nullable();
            $table->timestamps();

            // Optional: foreign keys if related
            // $table->foreign('block_id')->references('id')->on('blocks')->onDelete('cascade');
            // $table->foreign('apartment_id')->references('id')->on('apartments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repairs');
    }
};
