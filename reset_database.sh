#!/bin/bash

echo "==================================="
echo " إعادة تعيين قاعدة البيانات"
echo "==================================="

echo
echo "[تحذير] سيتم حذف جميع البيانات الموجودة في قاعدة البيانات!"
echo
read -p "هل أنت متأكد من رغبتك في المتابعة؟ (y/n): " confirm

if [ "$confirm" != "y" ] && [ "$confirm" != "Y" ]; then
    echo
    echo "تم إلغاء العملية."
    read -p "اضغط Enter للخروج..."
    exit 0
fi

echo
echo "[1/3] إعادة تعيين قاعدة البيانات..."
php artisan migrate:fresh

echo
echo "[2/3] تنفيذ ترحيل قاعدة البيانات..."
php artisan migrate

echo
echo "[3/3] زراعة بيانات المسؤولين..."
php artisan db:seed --class=AdminSeeder

echo
echo "==================================="
echo " تم الانتهاء من إعادة تعيين قاعدة البيانات"
echo "==================================="

read -p "اضغط Enter للخروج..." 