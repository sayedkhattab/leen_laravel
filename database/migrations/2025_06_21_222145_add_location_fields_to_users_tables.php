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
        // Add location fields to sellers table
        Schema::table('sellers', function (Blueprint $table) {
            $table->decimal('last_latitude', 10, 7)->nullable();
            $table->decimal('last_longitude', 10, 7)->nullable();
            $table->timestamp('last_location_update')->nullable();
            $table->boolean('location_tracking_enabled')->default(true);
        });
        
        // Add location fields to customers table
        Schema::table('customers', function (Blueprint $table) {
            $table->decimal('last_latitude', 10, 7)->nullable();
            $table->decimal('last_longitude', 10, 7)->nullable();
            $table->timestamp('last_location_update')->nullable();
            $table->boolean('location_tracking_enabled')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove location fields from sellers table
        Schema::table('sellers', function (Blueprint $table) {
            $table->dropColumn([
                'last_latitude',
                'last_longitude',
                'last_location_update',
                'location_tracking_enabled'
            ]);
        });
        
        // Remove location fields from customers table
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'last_latitude',
                'last_longitude',
                'last_location_update',
                'location_tracking_enabled'
            ]);
        });
    }
};
