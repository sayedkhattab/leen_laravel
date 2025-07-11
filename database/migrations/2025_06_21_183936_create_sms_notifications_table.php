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
        Schema::create('sms_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('phone');
            $table->string('message');
            $table->string('type'); // otp, marketing, notification
            $table->string('status'); // sent, failed
            $table->json('response_data')->nullable(); // استجابة فورجوالي
            $table->timestamps();
            
            $table->index(['phone', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_notifications');
    }
};
