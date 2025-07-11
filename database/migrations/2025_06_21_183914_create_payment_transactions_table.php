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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->string('transaction_id')->nullable(); // معرف المعاملة من باي موب
            $table->string('type'); // authorization, payment, refund, etc.
            $table->decimal('amount', 10, 2);
            $table->string('status');
            $table->json('transaction_data')->nullable(); // بيانات المعاملة الكاملة
            $table->timestamps();
            
            $table->index(['payment_id', 'type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
