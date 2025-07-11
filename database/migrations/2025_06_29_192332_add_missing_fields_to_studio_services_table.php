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
        Schema::table('studio_services', function (Blueprint $table) {
            $table->integer('duration')->nullable()->comment('مدة الخدمة بالدقائق');
            $table->text('description')->nullable()->comment('وصف مفصل للخدمة');
            $table->json('images')->nullable()->comment('صور الخدمة');
            $table->decimal('discount_percentage', 5, 2)->default(0)->comment('نسبة الخصم');
            $table->decimal('discounted_price', 10, 2)->nullable()->comment('السعر بعد الخصم');
            $table->string('location')->nullable()->comment('موقع الاستوديو');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('studio_services', function (Blueprint $table) {
            $table->dropColumn([
                'duration',
                'description',
                'images',
                'discount_percentage',
                'discounted_price',
                'location'
            ]);
        });
    }
};
