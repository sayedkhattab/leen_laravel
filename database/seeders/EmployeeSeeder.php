<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Seller;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // الحصول على جميع البائعين النشطين
        $sellers = Seller::where('status', 'active')->get();
        
        if ($sellers->isEmpty()) {
            $this->command->info('لا يوجد بائعين نشطين لإضافة موظفين لهم.');
            return;
        }
        
        foreach ($sellers as $seller) {
            // إضافة عدد عشوائي من الموظفين لكل بائع (1-5)
            $employeesCount = rand(1, 5);
            
            for ($i = 0; $i < $employeesCount; $i++) {
                // تحديد التخصصات المحتملة بناءً على نوع خدمة البائع
                $specializations = [];
                
                if ($seller->service_type === 'home' || $seller->service_type === 'both') {
                    $specializations = array_merge($specializations, [
                        'مصفف شعر', 'خبير مكياج', 'خبير عناية بالبشرة', 'خبير مانيكير وباديكير',
                        'مدلك', 'خبير تجميل', 'خبير حناء'
                    ]);
                }
                
                if ($seller->service_type === 'studio' || $seller->service_type === 'both') {
                    $specializations = array_merge($specializations, [
                        'مصور فوتوغرافي', 'مصمم أزياء', 'خبير إضاءة', 'منسق ديكور',
                        'مصفف شعر للاستوديو', 'خبير مكياج للاستوديو'
                    ]);
                }
                
                // إذا لم يكن هناك تخصصات محددة، استخدم قائمة افتراضية
                if (empty($specializations)) {
                    $specializations = [
                        'مصفف شعر', 'خبير مكياج', 'خبير عناية بالبشرة', 'مصور فوتوغرافي'
                    ];
                }
                
                // اختيار تخصص عشوائي
                $specialization = $specializations[array_rand($specializations)];
                
                // تحديد المسمى الوظيفي بناءً على التخصص
                $position = match($specialization) {
                    'مصفف شعر', 'مصفف شعر للاستوديو' => 'مصفف شعر',
                    'خبير مكياج', 'خبير مكياج للاستوديو' => 'خبير تجميل',
                    'خبير عناية بالبشرة' => 'أخصائي تجميل',
                    'مصور فوتوغرافي' => 'مصور',
                    'مصمم أزياء' => 'مصمم',
                    default => 'فني متخصص'
                };
                
                // تحديد أيام العمل العشوائية
                $allDays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                $workingDaysCount = rand(3, 7);
                $workingDaysKeys = array_rand($allDays, $workingDaysCount);
                
                if (!is_array($workingDaysKeys)) {
                    $workingDaysKeys = [$workingDaysKeys];
                }
                
                $workingDays = [];
                foreach ($workingDaysKeys as $key) {
                    $workingDays[] = $allDays[$key];
                }
                
                // تحديد وقت بدء وانتهاء الدوام
                $startHour = rand(8, 10);
                $endHour = rand(16, 20);
                
                // إنشاء الموظف
                Employee::create([
                    'seller_id' => $seller->id,
                    'name' => 'موظف ' . ($i + 1) . ' - ' . $seller->first_name,
                    'phone' => '966' . rand(500000000, 599999999),
                    'status' => 'active',
                    'work_start_time' => sprintf('%02d:00:00', $startHour),
                    'work_end_time' => sprintf('%02d:00:00', $endHour),
                    'working_days' => $workingDays,
                    'position' => $position,
                    'email' => 'employee' . ($i + 1) . '.' . strtolower(str_replace(' ', '', $seller->first_name)) . '@example.com',
                    'experience_years' => rand(1, 10),
                    'specialization' => $specialization,
                    'max_bookings_per_day' => rand(5, 15),
                    'is_available' => (bool) rand(0, 1),
                    'completed_bookings_count' => rand(0, 50),
                    'rating' => rand(30, 50) / 10,
                ]);
            }
            
            $this->command->info('تم إضافة ' . $employeesCount . ' موظفين للبائع: ' . $seller->first_name . ' ' . $seller->last_name);
        }
    }
} 