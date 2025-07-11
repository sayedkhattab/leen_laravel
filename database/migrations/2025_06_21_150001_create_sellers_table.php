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
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->unique();
            $table->timestamp('phone_verified_at')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->enum('request_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('seller_logo')->nullable();
            $table->string('seller_banner')->nullable();
            $table->string('license')->nullable();
            $table->string('location')->nullable();
            $table->text('request_rejection_reason')->nullable();
            $table->enum('service_type', ['home', 'studio', 'both'])->default('both');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
}; 