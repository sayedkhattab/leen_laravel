# دليل تكامل بوابة الدفع PayMob مع تطبيق Flutter

هذا الدليل يشرح كيفية التكامل مع بوابة الدفع PayMob في تطبيق Flutter الخاص بنا، بالاعتماد على الواجهة البرمجية (API) التي تم تطويرها في الخلفية (Backend).

## 1. نظرة عامة على عملية الدفع

تتكون عملية الدفع من الخطوات التالية:

1. **إنشاء حجز** في التطبيق (خدمة منزلية أو خدمة استوديو).
2. **طلب رابط دفع** من الخادم الخلفي.
3. **عرض صفحة الدفع** للمستخدم (عبر WebView).
4. **التعامل مع نتيجة الدفع** (نجاح/فشل).
5. **التحقق من حالة الدفع** والحجز بعد الانتهاء.

## 2. نقاط النهاية (Endpoints)

### 2.1 إنشاء طلب دفع لخدمة منزلية

```
POST /api/customer/payments/home-service
```

**الترويسة (Headers)**:
```
Accept: application/json
Authorization: Bearer <CUSTOMER_TOKEN>
```

**المعاملات (Parameters)**:
```json
{
  "booking_id": 123 // معرف حجز الخدمة المنزلية
}
```

**استجابة نموذجية**:
```json
{
  "success": true,
  "data": {
    "payment_id": 456,
    "payment_link": "https://ksa.paymob.com/api/acceptance/iframes/123456?payment_token=abc123def456",
    "amount": 150.00,
    "currency": "SAR"
  },
  "message": "تم إنشاء رابط الدفع بنجاح"
}
```

### 2.2 إنشاء طلب دفع لخدمة استوديو

```
POST /api/customer/payments/studio-service
```

**الترويسة (Headers)**:
```
Accept: application/json
Authorization: Bearer <CUSTOMER_TOKEN>
```

**المعاملات (Parameters)**:
```json
{
  "booking_id": 123 // معرف حجز خدمة الاستوديو
}
```

**استجابة نموذجية**:
مماثلة للخدمة المنزلية.

### 2.3 التحقق من حالة الدفع

```
GET /api/customer/payments/{payment_id}
```

**الترويسة (Headers)**:
```
Accept: application/json
Authorization: Bearer <CUSTOMER_TOKEN>
```

**استجابة نموذجية**:
```json
{
  "success": true,
  "data": {
    "payment": {
      "id": 456,
      "amount": 150.00,
      "status": "Paid", // Pending, Paid, Failed
      "reference_id": "12345678",
      "transaction_id": "87654321",
      "payment_method": "paymob",
      "created_at": "2023-07-20T15:30:45.000000Z",
      "updated_at": "2023-07-20T15:35:22.000000Z"
    },
    "home_service_bookings": [
      {
        "id": 123,
        "payment_id": 456,
        "payment_status": "paid",
        "booking_status": "confirmed",
        // حقول أخرى...
      }
    ],
    "studio_service_bookings": []
  },
  "message": "تم استرجاع حالة الدفع بنجاح"
}
```

## 3. خطوات التكامل في تطبيق Flutter

### 3.1 إنشاء نموذج للدفع (Payment Model)

```dart
class Payment {
  final int id;
  final double amount;
  final String status;
  final String paymentLink;
  final String currency;
  
  Payment({
    required this.id,
    required this.amount,
    required this.status,
    required this.paymentLink,
    this.currency = 'SAR',
  });
  
  factory Payment.fromJson(Map<String, dynamic> json) {
    return Payment(
      id: json['payment_id'],
      amount: double.parse(json['amount'].toString()),
      status: 'Pending',
      paymentLink: json['payment_link'],
      currency: json['currency'] ?? 'SAR',
    );
  }
}
```

### 3.2 إنشاء خدمة للتعامل مع الدفع (Payment Service)

```dart
class PaymentService {
  final Dio _dio = Dio();
  final String _baseUrl = 'http://192.168.1.7:8000/api';
  
  Future<Payment> createHomeServicePayment(int bookingId, String token) async {
    try {
      final response = await _dio.post(
        '$_baseUrl/customer/payments/home-service',
        data: {'booking_id': bookingId},
        options: Options(
          headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer $token',
          },
        ),
      );
      
      if (response.statusCode == 200 && response.data['success']) {
        return Payment.fromJson(response.data['data']);
      } else {
        throw Exception(response.data['message'] ?? 'فشل في إنشاء طلب الدفع');
      }
    } catch (e) {
      throw Exception('فشل في الاتصال بالخادم: $e');
    }
  }
  
  Future<Payment> createStudioServicePayment(int bookingId, String token) async {
    try {
      final response = await _dio.post(
        '$_baseUrl/customer/payments/studio-service',
        data: {'booking_id': bookingId},
        options: Options(
          headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer $token',
          },
        ),
      );
      
      if (response.statusCode == 200 && response.data['success']) {
        return Payment.fromJson(response.data['data']);
      } else {
        throw Exception(response.data['message'] ?? 'فشل في إنشاء طلب الدفع');
      }
    } catch (e) {
      throw Exception('فشل في الاتصال بالخادم: $e');
    }
  }
  
  Future<Map<String, dynamic>> getPaymentStatus(int paymentId, String token) async {
    try {
      final response = await _dio.get(
        '$_baseUrl/customer/payments/$paymentId',
        options: Options(
          headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer $token',
          },
        ),
      );
      
      if (response.statusCode == 200 && response.data['success']) {
        return response.data['data'];
      } else {
        throw Exception(response.data['message'] ?? 'فشل في استرجاع حالة الدفع');
      }
    } catch (e) {
      throw Exception('فشل في الاتصال بالخادم: $e');
    }
  }
}
```

### 3.3 إنشاء شاشة الدفع (Payment Screen)

```dart
class PaymentScreen extends StatefulWidget {
  final Payment payment;
  
  const PaymentScreen({Key? key, required this.payment}) : super(key: key);
  
  @override
  _PaymentScreenState createState() => _PaymentScreenState();
}

class _PaymentScreenState extends State<PaymentScreen> {
  final PaymentService _paymentService = PaymentService();
  bool _isLoading = true;
  bool _paymentComplete = false;
  String _paymentStatus = 'Pending';
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('الدفع'),
      ),
      body: _isLoading
          ? Center(child: CircularProgressIndicator())
          : _buildWebView(),
    );
  }
  
  Widget _buildWebView() {
    return WebView(
      initialUrl: widget.payment.paymentLink,
      javascriptMode: JavascriptMode.unrestricted,
      navigationDelegate: (NavigationRequest request) {
        // التقاط عنوان URL الخاص بإعادة التوجيه بعد الدفع
        if (request.url.contains('payment/callback')) {
          _handlePaymentCallback(request.url);
          return NavigationDecision.prevent;
        }
        return NavigationDecision.navigate;
      },
      onPageFinished: (String url) {
        setState(() {
          _isLoading = false;
        });
      },
    );
  }
  
  void _handlePaymentCallback(String url) async {
    // استخراج معلومات الدفع من URL إن وجدت
    setState(() {
      _isLoading = true;
    });
    
    try {
      // التحقق من حالة الدفع من الخادم
      final token = await AuthService().getToken();
      final paymentStatus = await _paymentService.getPaymentStatus(widget.payment.id, token);
      
      setState(() {
        _paymentComplete = true;
        _paymentStatus = paymentStatus['payment']['status'];
        _isLoading = false;
      });
      
      // عرض رسالة نجاح أو فشل
      if (_paymentStatus == 'Paid') {
        _showSuccessDialog();
      } else {
        _showFailureDialog();
      }
    } catch (e) {
      setState(() {
        _isLoading = false;
      });
      _showErrorDialog(e.toString());
    }
  }
  
  void _showSuccessDialog() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('تم الدفع بنجاح'),
        content: Text('تم تأكيد الحجز بنجاح، يمكنك متابعة حالة الحجز من صفحة الحجوزات.'),
        actions: [
          TextButton(
            onPressed: () {
              Navigator.of(context).pop();
              Navigator.of(context).pop(true); // العودة إلى الشاشة السابقة مع إشارة النجاح
            },
            child: Text('حسناً'),
          ),
        ],
      ),
    );
  }
  
  void _showFailureDialog() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('فشل الدفع'),
        content: Text('لم يتم إكمال عملية الدفع بنجاح، يرجى المحاولة مرة أخرى.'),
        actions: [
          TextButton(
            onPressed: () {
              Navigator.of(context).pop();
              Navigator.of(context).pop(false); // العودة إلى الشاشة السابقة مع إشارة الفشل
            },
            child: Text('حسناً'),
          ),
        ],
      ),
    );
  }
  
  void _showErrorDialog(String error) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('خطأ'),
        content: Text('حدث خطأ أثناء التحقق من حالة الدفع: $error'),
        actions: [
          TextButton(
            onPressed: () {
              Navigator.of(context).pop();
              Navigator.of(context).pop(false);
            },
            child: Text('حسناً'),
          ),
        ],
      ),
    );
  }
}
```

### 3.4 استخدام شاشة الدفع في تطبيقك

```dart
// مثال: عند الضغط على زر "الدفع الآن" بعد إنشاء حجز
ElevatedButton(
  onPressed: () async {
    try {
      setState(() {
        _isLoading = true;
      });
      
      final token = await AuthService().getToken();
      final payment = await PaymentService().createHomeServicePayment(_booking.id, token);
      
      setState(() {
        _isLoading = false;
      });
      
      // فتح شاشة الدفع
      final result = await Navigator.of(context).push(
        MaterialPageRoute(
          builder: (context) => PaymentScreen(payment: payment),
        ),
      );
      
      // التعامل مع نتيجة الدفع
      if (result == true) {
        // تم الدفع بنجاح
        // تحديث واجهة المستخدم أو الانتقال إلى صفحة التأكيد
      } else {
        // فشل الدفع
        // عرض خيارات إعادة المحاولة
      }
    } catch (e) {
      setState(() {
        _isLoading = false;
      });
      // عرض رسالة خطأ
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('فشل في إنشاء طلب الدفع: $e')),
      );
    }
  },
  child: Text('الدفع الآن'),
)
```

## 4. ملاحظات هامة

1. **معالجة حالات الاتصال**: تأكد من معالجة حالات انقطاع الاتصال أثناء عملية الدفع.

2. **التحقق من حالة الدفع**: قم دائمًا بالتحقق من حالة الدفع من الخادم بعد العودة من صفحة الدفع، حيث قد يتم التلاعب بعنوان URL الخاص بإعادة التوجيه.

3. **اختبار الدفع**: استخدم بطاقات اختبار PayMob للتحقق من سير عملية الدفع:
   - رقم البطاقة: 5123456789012346
   - تاريخ الانتهاء: أي تاريخ مستقبلي
   - CVV: أي 3 أرقام
   - اسم حامل البطاقة: أي اسم

4. **تعامل مع WebView**: تأكد من إضافة الأذونات المناسبة في ملف `AndroidManifest.xml`:
   ```xml
   <uses-permission android:name="android.permission.INTERNET" />
   ```

5. **التعامل مع الـ Callbacks**: قد يتم إعادة توجيه المستخدم إلى تطبيقك عبر Deep Link بعد الدفع. تأكد من إعداد Deep Linking بشكل صحيح في تطبيقك.

## 5. اعتبارات الأمان

1. **لا تخزن بيانات البطاقة**: لا تقم أبدًا بتخزين بيانات بطاقات الائتمان في تطبيقك.

2. **استخدم HTTPS**: تأكد من أن جميع الاتصالات مع الخادم تتم عبر HTTPS.

3. **التحقق من الاستجابات**: تحقق دائمًا من صحة الاستجابات القادمة من الخادم قبل عرضها للمستخدم.

## 6. الخلاصة

باتباع هذا الدليل، يمكنك تكامل بوابة الدفع PayMob في تطبيق Flutter الخاص بك بسهولة. تذكر دائمًا اختبار عملية الدفع بشكل كامل قبل إطلاق التطبيق للمستخدمين. 