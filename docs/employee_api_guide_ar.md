# دليل تكامل موظفي مقدم الخدمة (Seller Employees API)

هذا المستند موجه لمطوّر تطبيق Flutter لشرح كيفية التكامل مع نقاط النهاية (Endpoints) الخاصة بإدارة الموظفين في النظام الخلفي (Laravel API). ستجد فيه كل ما تحتاجه لإضافة موظف جديد، استعراضهم في لوحة مقدم الخدمة، التحكم بحالتهم، وكذلك عرضهم في واجهة العملاء.

> جميع الأمثلة تفترض أن الـ Base URL هو:
>
> ```
> http://192.168.1.7:8000/api
> ```
>
> مع تمرير الترويسة (Header):
>
> ```
> Accept: application/json
> Authorization: Bearer <SELLER_ACCESS_TOKEN>
> ```
>
> واستبدال `<SELLER_ACCESS_TOKEN>` بالتوكن الذي تحصل عليه بعد تسجيل دخول البائع.

---

## 1. تسجيل دخول البائع (مختصر)
1. `POST /seller/login`
2. المخرجات تحتوي على `access_token` يتم استخدامه مع كل طلب يتطلب مصادقة.

(راجع مستند المصادقة للحصول على التفاصيل الكاملة.)

---

## 2. لوحة مقدم الخدمة – إدارة الموظفين

### 2.1 عرض قائمة الموظفين
```
GET /seller/employees
```
#### استجابة نموذجية
```json
{
  "success": true,
  "data": [
    {
      "id": 7,
      "seller_id": 3,
      "name": "أحمد محمد",
      "phone": "966501234567",
      "position": "مصفف شعر",
      "email": "ahmed@example.com",
      "photo_url": "https://YOUR_DOMAIN.COM/images/employees/1699876543_a1b2c3.jpg",
      "experience_years": 5,
      "specialization": "قصّ وتصفيف",
      "work_start_time": "09:00",
      "work_end_time": "18:00",
      "working_days": ["Sunday","Monday","Tuesday","Wednesday","Thursday"],
      "max_bookings_per_day": 10,
      "is_available": true,
      "status": "active",
      "created_at": "2025-07-01T10:15:22.000000Z",
      "updated_at": "2025-07-01T10:15:22.000000Z"
    }
  ],
  "message": "تم استرجاع قائمة الموظفين بنجاح"
}
```

---

### 2.2 إضافة موظف جديد
```
POST /seller/employees
Content-Type: multipart/form-data
```
#### حقول الإدخال
| الحقل | النوع | إلزامي | الوصف |
|-------|-------|--------|-------|
| name | string | نعم | اسم الموظف |
| phone | string | نعم | رقم الجوال |
| position | string | نعم | المسمى الوظيفي |
| email | string | لا | البريد الإلكتروني |
| photo | file (jpeg/png) | لا | صورة شخصية |
| work_start_time | `HH:mm` | لا | وقت بدء الدوام (24h) |
| work_end_time | `HH:mm` | لا | وقت نهاية الدوام |
| working_days | array<string> | لا | أيام العمل بالإنجليزية (Sunday … Saturday) |
| experience_years | integer | لا | سنوات الخبرة |
| specialization | string | لا | التخصص |
| max_bookings_per_day | integer | لا | الحد الأقصى للحجوزات يوميًا |
| is_available | boolean | لا | متاح للحجز؟ |

#### استجابة نجاح
```json
{
  "success": true,
  "data": {
    "id": 8,
    "photo_url": "https://…",
    ...
  },
  "message": "تم إضافة الموظف بنجاح"
}
```

---

### 2.3 عرض موظف محدد
```
GET /seller/employees/{id}
```

### 2.4 تعديل بيانات موظف
```
PUT /seller/employees/{id}
Content-Type: multipart/form-data أو application/json
```
• نفس الحقول أعلاه ولكن كلها **اختيارية**.

### 2.5 تحديث حالة التوفر السريع
```
PUT /seller/employees/{id}/availability
Content-Type: application/json
```
```json
{
  "is_available": false
}
```

### 2.6 حذف موظف
```
DELETE /seller/employees/{id}
```

### 2.7 جلب الموظفين المتاحين في وقت وتاريخ محددين
```
GET /seller/available-employees?date=2025-07-10&time=15:30
```

---

## 3. واجهة العميل – عرض موظفي مقدم الخدمة

هذه النقطة لا تتطلب مصادقة؛ يستخدمها تطبيق العميل لعرض الموظفين حسب كل مقدم خدمة.

```
GET /sellers/{seller_id}/employees
```
#### مثال استجابة
```json
{
  "success": true,
  "data": [
    {
      "id": 7,
      "name": "أحمد محمد",
      "position": "مصفف شعر",
      "phone": "966501234567",
      "email": "ahmed@example.com",
      "experience_years": 5,
      "specialization": "قصّ وتصفيف",
      "photo_url": "https://…",
      "is_available": true,
      "is_available_text": "متاح",
      "work_start_time": "09:00",
      "work_end_time": "18:00",
      "working_days": ["Sunday","Monday","Tuesday","Wednesday","Thursday"],
      "rating": 4.5,
      "status": "active"
    }
  ],
  "message": "Seller employees retrieved successfully"
}
```

---

## 4. ملاحظات عامة للمطور
1. عند رفع صورة استخدم `multipart/form-data` مع الحقل `photo`.
2. قيمة `photo_url` تعيد رابط الصورة جاهز للعرض في Flutter (يمكن إظهاره عبر `Image.network`).
3. إذا لم تُرسل قيمة `is_available` في إنشاء الموظف ستُعتبر `true` افتراضيًا.
4. فحص الحقول الفارغة قبل الإرسال لأن الـ API يتحقق من الصيغ بشكل دقيق.
5. الرموز الزمنية (`work_start_time`, `work_end_time`) بترميز 24-ساعة ولا تحتاج ثوانٍ.

---

### جاهز!
عند الالتزام بالإرشادات أعلاه سيتمكن مطوّر Flutter من:
• إنشاء موظفين جدد.
• عرضهم وتحديثهم أو حذفهم من لوحة البائع.
• التحكم بتوفرهم الفوري.
• استعراضهم في تطبيق العملاء.

> لأي أسئلة إضافية يرجى الرجوع إلى فريق الباك-إند. 