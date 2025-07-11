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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 2);  // مبلغ الدفع
            $table->string('status')->default('Pending');  // حالة الدفع (Pending, Paid, Failed)
            $table->string('reference_id')->nullable();  // معرف المرجع من باي موب
            $table->foreignId('user_id')->constrained('customers')->onDelete('cascade');  // معرف العميل
            $table->string('transaction_id')->nullable();  // معرف المعاملة من باي موب
            $table->string('payment_method')->nullable(); // طريقة الدفع (credit_card, wallet, etc.)
            $table->text('payment_data')->nullable();  // بيانات إضافية عن الدفع (JSON)
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index('reference_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
