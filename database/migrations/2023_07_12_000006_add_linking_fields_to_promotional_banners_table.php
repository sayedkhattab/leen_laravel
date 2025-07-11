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
        Schema::table('promotional_banners', function (Blueprint $table) {
            // إضافة حقول الربط مع مقدم خدمة أو خدمة
            $table->enum('link_type', ['url', 'seller', 'home_service', 'studio_service'])->default('url')->after('target_audience');
            $table->unsignedBigInteger('linked_seller_id')->nullable()->after('link_type');
            $table->unsignedBigInteger('linked_home_service_id')->nullable()->after('linked_seller_id');
            $table->unsignedBigInteger('linked_studio_service_id')->nullable()->after('linked_home_service_id');
            
            // إضافة العلاقات
            $table->foreign('linked_seller_id')->references('id')->on('sellers')->onDelete('set null');
            $table->foreign('linked_home_service_id')->references('id')->on('home_services')->onDelete('set null');
            $table->foreign('linked_studio_service_id')->references('id')->on('studio_services')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotional_banners', function (Blueprint $table) {
            // حذف العلاقات
            $table->dropForeign(['linked_seller_id']);
            $table->dropForeign(['linked_home_service_id']);
            $table->dropForeign(['linked_studio_service_id']);
            
            // حذف الحقول
            $table->dropColumn('link_type');
            $table->dropColumn('linked_seller_id');
            $table->dropColumn('linked_home_service_id');
            $table->dropColumn('linked_studio_service_id');
        });
    }
}; 