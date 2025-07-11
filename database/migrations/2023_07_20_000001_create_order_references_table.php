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
        Schema::create('order_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->string('reference_id')->index(); // PayMob order reference ID
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_references');
    }
}; 