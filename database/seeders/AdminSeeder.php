<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إضافة المسؤول الرئيسي
        Admin::create([
            'name' => 'مدير النظام',
            'email' => 'admin@leen.com',
            'password' => Hash::make('Leen@123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // إضافة مسؤول المبيعات
        Admin::create([
            'name' => 'مسؤول المبيعات',
            'email' => 'sales@leen.com',
            'password' => Hash::make('Leen@123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // إضافة مسؤول خدمة العملاء
        Admin::create([
            'name' => 'خدمة العملاء',
            'email' => 'support@leen.com',
            'password' => Hash::make('Leen@123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // إضافة مسؤول المحتوى
        Admin::create([
            'name' => 'مسؤول المحتوى',
            'email' => 'content@leen.com',
            'password' => Hash::make('Leen@123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
} 