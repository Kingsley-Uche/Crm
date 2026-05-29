<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('fname', 50);
            $table->string('lname', 50);

            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();

            $table->unsignedTinyInteger('user_type')
                ->index()
                ->comment('1=system admin,2=property manager,3=regular tenant');

            $table->foreignId('role_id')
                ->nullable()
                ->constrained('roles_models')
                ->nullOnDelete();

            $table->string('password');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('admin_models')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('admin_models')
                ->nullOnDelete();

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();

            $table->foreignId('user_id')
                ->nullable()
                ->index();

            $table->string('ip_address', 45)->nullable();

            $table->text('user_agent')->nullable();

            $table->longText('payload');

            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};