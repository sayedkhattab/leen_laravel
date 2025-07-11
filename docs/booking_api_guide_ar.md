# دليل واجهات برمجة التطبيقات (API) للحجوزات في تطبيق Leen

هذا الدليل يوفر معلومات شاملة حول كيفية استخدام واجهات برمجة التطبيقات (APIs) للحجوزات في تطبيق Leen. يغطي الدليل كلاً من حجوزات الخدمات المنزلية وخدمات الاستوديو، بما في ذلك إنشاء الحجوزات، وإدارتها، والدفع.

## المحتويات

1. [نظرة عامة على الحجوزات](#نظرة-عامة-على-الحجوزات)
2. [حجوزات الخدمات المنزلية](#حجوزات-الخدمات-المنزلية)
3. [حجوزات خدمات الاستوديو](#حجوزات-خدمات-الاستوديو)
4. [الدفع للحجوزات](#الدفع-للحجوزات)
5. [إلغاء الحجوزات](#إلغاء-الحجوزات)
6. [استعلامات الحالة](#استعلامات-الحالة)
7. [أمثلة للتكامل مع Flutter](#أمثلة-للتكامل-مع-flutter)

## نظرة عامة على الحجوزات

يدعم تطبيق Leen نوعين من الحجوزات:

1. **حجوزات الخدمات المنزلية**: خدمات يتم تقديمها في منزل العميل
2. **حجوزات خدمات الاستوديو**: خدمات يتم تقديمها في مقر مزود الخدمة (الصالون/الاستوديو)

لكل نوع من أنواع الحجوزات، يمكن للعميل:
- إنشاء حجز جديد
- عرض تفاصيل الحجز
- تعديل الحجز
- إلغاء الحجز
- دفع قيمة الحجز

## حجوزات الخدمات المنزلية

### إنشاء حجز خدمة منزلية

```
POST /api/v1/customer/home-service-bookings
```

#### المعلمات المطلوبة:

| المعلمة | النوع | الوصف |
|---------|------|---------|
| home_service_id | integer | معرف الخدمة المنزلية |
| booking_date | date | تاريخ الحجز (YYYY-MM-DD) |
| booking_time | time | وقت الحجز (HH:MM) |
| address | string | عنوان تقديم الخدمة |
| notes | string | ملاحظات إضافية (اختياري) |

#### مثال للطلب:

```dart
var response = await http.post(
  Uri.parse('$baseUrl/api/v1/customer/home-service-bookings'),
  headers: {
    'Authorization': 'Bearer $token',
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  body: jsonEncode({
    'home_service_id': 1,
    'booking_date': '2023-12-15',
    'booking_time': '14:30',
    'address': 'شارع الملك فهد، الرياض',
    'notes': 'يرجى الاتصال قبل الوصول'
  }),
);
```

#### مثال للاستجابة:

```json
{
  "success": true,
  "data": {
    "id": 1,
    "customer_id": 5,
    "home_service_id": 1,
    "booking_date": "2023-12-15",
    "booking_time": "14:30:00",
    "address": "شارع الملك فهد، الرياض",
    "notes": "يرجى الاتصال قبل الوصول",
    "status": "pending",
    "payment_status": "pending",
    "created_at": "2023-12-01T10:30:45.000000Z",
    "updated_at": "2023-12-01T10:30:45.000000Z",
    "homeService": {
      "id": 1,
      "name": "تنظيف منزلي",
      "price": 150.00,
      "seller": {
        "id": 3,
        "name": "شركة التنظيف المثالية"
      }
    }
  },
  "message": "تم إنشاء الحجز بنجاح"
}
```

### الحصول على قائمة حجوزات الخدمات المنزلية

```
GET /api/v1/customer/home-service-bookings
```

#### المعلمات الاختيارية:

| المعلمة | النوع | الوصف |
|---------|------|---------|
| status | string | تصفية حسب الحالة (pending, confirmed, completed, cancelled, rescheduled) |
| page | integer | رقم الصفحة للتصفح |

#### مثال للطلب:

```dart
var response = await http.get(
  Uri.parse('$baseUrl/api/v1/customer/home-service-bookings?status=pending'),
  headers: {
    'Authorization': 'Bearer $token',
    'Accept': 'application/json',
  },
);
```

### الحصول على تفاصيل حجز خدمة منزلية

```
GET /api/v1/customer/home-service-bookings/{id}
```

#### مثال للطلب:

```dart
var response = await http.get(
  Uri.parse('$baseUrl/api/v1/customer/home-service-bookings/1'),
  headers: {
    'Authorization': 'Bearer $token',
    'Accept': 'application/json',
  },
);
```

### تعديل حجز خدمة منزلية

```
PUT /api/v1/customer/home-service-bookings/{id}
```

#### المعلمات المتاحة للتعديل:

| المعلمة | النوع | الوصف |
|---------|------|---------|
| booking_date | date | تاريخ الحجز الجديد |
| booking_time | time | وقت الحجز الجديد |
| address | string | عنوان تقديم الخدمة |
| notes | string | ملاحظات إضافية |

#### مثال للطلب:

```dart
var response = await http.put(
  Uri.parse('$baseUrl/api/v1/customer/home-service-bookings/1'),
  headers: {
    'Authorization': 'Bearer $token',
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  body: jsonEncode({
    'booking_date': '2023-12-20',
    'booking_time': '16:00',
  }),
);
```

## حجوزات خدمات الاستوديو

### إنشاء حجز خدمة استوديو

```
POST /api/v1/customer/studio-service-bookings
```

#### المعلمات المطلوبة:

| المعلمة | النوع | الوصف |
|---------|------|---------|
| studio_service_id | integer | معرف خدمة الاستوديو |
| booking_date | date | تاريخ الحجز (YYYY-MM-DD) |
| booking_time | time | وقت الحجز (HH:MM) |
| notes | string | ملاحظات إضافية (اختياري) |

#### مثال للطلب:

```dart
var response = await http.post(
  Uri.parse('$baseUrl/api/v1/customer/studio-service-bookings'),
  headers: {
    'Authorization': 'Bearer $token',
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  body: jsonEncode({
    'studio_service_id': 1,
    'booking_date': '2023-12-15',
    'booking_time': '14:30',
    'notes': 'أفضل الخدمة مع المختصة سارة'
  }),
);
```

### الحصول على قائمة حجوزات خدمات الاستوديو

```
GET /api/v1/customer/studio-service-bookings
```

#### المعلمات الاختيارية:

| المعلمة | النوع | الوصف |
|---------|------|---------|
| status | string | تصفية حسب الحالة (pending, confirmed, completed, cancelled, rescheduled) |
| page | integer | رقم الصفحة للتصفح |

### الحصول على تفاصيل حجز خدمة استوديو

```
GET /api/v1/customer/studio-service-bookings/{id}
```

### تعديل حجز خدمة استوديو

```
PUT /api/v1/customer/studio-service-bookings/{id}
```

## الدفع للحجوزات

### إنشاء طلب دفع لحجز خدمة منزلية

```
POST /api/v1/payments/home-service
```

#### المعلمات المطلوبة:

| المعلمة | النوع | الوصف |
|---------|------|---------|
| booking_id | integer | معرف حجز الخدمة المنزلية |

#### مثال للطلب:

```dart
var response = await http.post(
  Uri.parse('$baseUrl/api/v1/payments/home-service'),
  headers: {
    'Authorization': 'Bearer $token',
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  body: jsonEncode({
    'booking_id': 1
  }),
);
```

#### مثال للاستجابة:

```json
{
  "success": true,
  "data": {
    "payment_id": 123,
    "payment_link": "https://accept.paymob.com/api/acceptance/iframes/123456?payment_token=xyz123",
    "amount": 150.00,
    "currency": "SAR"
  },
  "message": "تم إنشاء رابط الدفع بنجاح"
}
```

### إنشاء طلب دفع لحجز خدمة استوديو

```
POST /api/v1/payments/studio-service
```

#### المعلمات المطلوبة:

| المعلمة | النوع | الوصف |
|---------|------|---------|
| booking_id | integer | معرف حجز خدمة الاستوديو |

### التعامل مع رابط الدفع في تطبيق Flutter

بعد الحصول على رابط الدفع، يمكنك استخدام WebView لعرض صفحة الدفع:

```dart
import 'package:webview_flutter/webview_flutter.dart';

// ...

void openPaymentPage(String paymentUrl) {
  Navigator.push(
    context,
    MaterialPageRoute(
      builder: (context) => Scaffold(
        appBar: AppBar(title: Text('صفحة الدفع')),
        body: WebView(
          initialUrl: paymentUrl,
          javascriptMode: JavascriptMode.unrestricted,
          navigationDelegate: (NavigationRequest request) {
            // التعامل مع إعادة التوجيه بعد الدفع
            if (request.url.contains('payment/success')) {
              // تم الدفع بنجاح
              Navigator.pop(context);
              showSuccessMessage();
              return NavigationDecision.prevent;
            } else if (request.url.contains('payment/error')) {
              // فشل الدفع
              Navigator.pop(context);
              showErrorMessage();
              return NavigationDecision.prevent;
            }
            return NavigationDecision.navigate;
          },
        ),
      ),
    ),
  );
}
```

### التحقق من حالة الدفع

```
GET /api/v1/payments/{payment_id}
```

#### مثال للطلب:

```dart
var response = await http.get(
  Uri.parse('$baseUrl/api/v1/payments/123'),
  headers: {
    'Authorization': 'Bearer $token',
    'Accept': 'application/json',
  },
);
```

## إلغاء الحجوزات

### إلغاء حجز خدمة منزلية

```
PUT /api/v1/customer/home-service-bookings/{id}/cancel
```

#### المعلمات الاختيارية:

| المعلمة | النوع | الوصف |
|---------|------|---------|
| cancellation_reason | string | سبب الإلغاء |

#### مثال للطلب:

```dart
var response = await http.put(
  Uri.parse('$baseUrl/api/v1/customer/home-service-bookings/1/cancel'),
  headers: {
    'Authorization': 'Bearer $token',
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  body: jsonEncode({
    'cancellation_reason': 'تغيير الجدول الزمني'
  }),
);
```

### إلغاء حجز خدمة استوديو

```
PUT /api/v1/customer/studio-service-bookings/{id}/cancel
```

## أمثلة للتكامل مع Flutter

### نموذج لإدارة الحجوزات في Flutter

```dart
class BookingService {
  final String baseUrl;
  final String token;
  
  BookingService({required this.baseUrl, required this.token});
  
  // إنشاء حجز خدمة منزلية
  Future<Map<String, dynamic>> createHomeServiceBooking({
    required int serviceId,
    required String date,
    required String time,
    required String address,
    String? notes,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/api/v1/customer/home-service-bookings'),
      headers: {
        'Authorization': 'Bearer $token',
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: jsonEncode({
        'home_service_id': serviceId,
        'booking_date': date,
        'booking_time': time,
        'address': address,
        if (notes != null) 'notes': notes,
      }),
    );
    
    return jsonDecode(response.body);
  }
  
  // الحصول على قائمة الحجوزات
  Future<Map<String, dynamic>> getHomeServiceBookings({String? status}) async {
    final Uri uri = Uri.parse('$baseUrl/api/v1/customer/home-service-bookings')
        .replace(queryParameters: status != null ? {'status': status} : {});
        
    final response = await http.get(
      uri,
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );
    
    return jsonDecode(response.body);
  }
  
  // إنشاء طلب دفع
  Future<Map<String, dynamic>> createPaymentForHomeService(int bookingId) async {
    final response = await http.post(
      Uri.parse('$baseUrl/api/v1/payments/home-service'),
      headers: {
        'Authorization': 'Bearer $token',
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: jsonEncode({
        'booking_id': bookingId,
      }),
    );
    
    return jsonDecode(response.body);
  }
}
```

### استخدام خدمة الحجوزات في واجهة المستخدم

```dart
class BookingPage extends StatefulWidget {
  @override
  _BookingPageState createState() => _BookingPageState();
}

class _BookingPageState extends State<BookingPage> {
  final BookingService _bookingService = BookingService(
    baseUrl: 'https://api.example.com',
    token: 'your_auth_token',
  );
  
  Future<void> _createBooking() async {
    try {
      final result = await _bookingService.createHomeServiceBooking(
        serviceId: 1,
        date: '2023-12-15',
        time: '14:30',
        address: 'شارع الملك فهد، الرياض',
        notes: 'يرجى الاتصال قبل الوصول',
      );
      
      if (result['success']) {
        // تم إنشاء الحجز بنجاح
        final bookingId = result['data']['id'];
        
        // إنشاء طلب دفع للحجز
        final paymentResult = await _bookingService.createPaymentForHomeService(bookingId);
        
        if (paymentResult['success']) {
          // فتح صفحة الدفع
          final paymentLink = paymentResult['data']['payment_link'];
          openPaymentPage(paymentLink);
        }
      }
    } catch (e) {
      print('Error: $e');
    }
  }
  
  @override
  Widget build(BuildContext context) {
    // واجهة المستخدم
    return Scaffold(
      // ...
    );
  }
}
```

## ملاحظات هامة

1. يجب التأكد من إرسال رمز المصادقة (Bearer Token) في رأس كل طلب.
2. تأكد من التعامل مع الأخطاء المحتملة وعرض رسائل مناسبة للمستخدم.
3. بعد إكمال عملية الدفع، تحقق من حالة الدفع باستخدام نقطة النهاية الخاصة بالتحقق من حالة الدفع.
4. تذكر أن تقوم بتحديث واجهة المستخدم بعد كل عملية ناجحة (إنشاء، تعديل، إلغاء).

للمزيد من المعلومات حول عمليات الدفع، يرجى الاطلاع على [دليل تكامل المدفوعات](payment_integration_guide_ar.md). 