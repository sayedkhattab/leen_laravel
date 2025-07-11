<?php

namespace App\Http\Controllers\API\Customer;

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

trait CustomerControllerTrait
{
    /**
     * الحصول على كائن العميل الحالي
     * يدعم كلا النوعين من المستخدمين: مستخدمين من نوع User مع علاقة customer ومستخدمين من نوع Customer مباشرة
     * 
     * @return Customer|null
     */
    protected function getCurrentCustomer()
    {
        $user = Auth::user();
        
        // إذا كان المستخدم هو عميل مباشرة
        if ($user instanceof Customer) {
            return $user;
        }
        
        // إذا كان المستخدم له علاقة عميل
        if ($user && $user->customer) {
            return $user->customer;
        }
        
        return null;
    }
} 