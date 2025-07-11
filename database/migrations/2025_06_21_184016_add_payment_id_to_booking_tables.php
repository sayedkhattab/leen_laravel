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
        // Add payment_id to home_service_bookings table
        Schema::table('home_service_bookings', function (Blueprint $table) {
            // Since payment_status already exists, we only need to add payment_id
            $table->foreignId('payment_id')->nullable()->after('employee_id')->constrained('payments')->nullOnDelete();
        });

        // Add payment_id to studio_service_bookings table
        Schema::table('studio_service_bookings', function (Blueprint $table) {
            // Since payment_status already exists, we only need to add payment_id
            $table->foreignId('payment_id')->nullable()->after('employee_id')->constrained('payments')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove payment_id from home_service_bookings table
        Schema::table('home_service_bookings', function (Blueprint $table) {
            $table->dropForeign(['payment_id']);
            $table->dropColumn('payment_id');
        });

        // Remove payment_id from studio_service_bookings table
        Schema::table('studio_service_bookings', function (Blueprint $table) {
            $table->dropForeign(['payment_id']);
            $table->dropColumn('payment_id');
        });
    }
};
