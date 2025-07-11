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
        Schema::table('employees', function (Blueprint $table) {
            // إضافة حقول ساعات العمل
            $table->time('work_start_time')->nullable()->comment('وقت بدء الدوام اليومي');
            $table->time('work_end_time')->nullable()->comment('وقت انتهاء الدوام اليومي');
            $table->json('working_days')->nullable()->comment('أيام العمل في الأسبوع');
            
            // إضافة معلومات إضافية مهمة
            $table->string('position')->nullable()->comment('المسمى الوظيفي');
            $table->string('email')->nullable()->comment('البريد الإلكتروني للموظف');
            $table->string('photo')->nullable()->comment('صورة الموظف');
            $table->integer('experience_years')->nullable()->default(0)->comment('سنوات الخبرة');
            $table->string('specialization')->nullable()->comment('التخصص');
            
            // إضافة حقول إدارة الحجوزات
            $table->integer('max_bookings_per_day')->nullable()->default(10)->comment('الحد الأقصى للحجوزات اليومية');
            $table->boolean('is_available')->default(true)->comment('متاح للحجز');
            
            // إضافة بيانات تتبع الأداء
            $table->integer('completed_bookings_count')->default(0)->comment('عدد الحجوزات المكتملة');
            $table->decimal('rating', 3, 2)->nullable()->comment('تقييم الموظف (متوسط التقييمات)');
        });

        // إضافة فهارس للبحث السريع
        Schema::table('employees', function (Blueprint $table) {
            $table->index('position', 'idx_employee_position');
            $table->index('specialization', 'idx_employee_specialization');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // حذف الفهارس
            $table->dropIndex('idx_employee_position');
            $table->dropIndex('idx_employee_specialization');
            
            // حذف الأعمدة
            $table->dropColumn([
                'work_start_time',
                'work_end_time',
                'working_days',
                'position',
                'email',
                'photo',
                'experience_years',
                'specialization',
                'max_bookings_per_day',
                'is_available',
                'completed_bookings_count',
                'rating',
            ]);
        });
    }
}; 