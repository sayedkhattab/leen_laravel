<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فشلت عملية الدفع</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Cairo', sans-serif;
        }
        body {
            background-color: #f8f9fa;
            padding: 20px;
            direction: rtl;
            text-align: center;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        .error-icon {
            width: 80px;
            height: 80px;
            background-color: #dc3545;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .error-icon svg {
            width: 40px;
            height: 40px;
            fill: white;
        }
        h1 {
            color: #dc3545;
            margin-bottom: 20px;
        }
        .details {
            text-align: right;
            margin: 30px 0;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            margin-top: 20px;
        }
        .btn-danger {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/>
            </svg>
        </div>
        
        <h1>فشلت عملية الدفع</h1>
        <p>{{ $message ?? 'حدث خطأ أثناء معالجة الدفع الخاص بك.' }}</p>
        
        @if(isset($payment_link))
        <div class="details">
            <p>يمكنك المحاولة مرة أخرى باستخدام الرابط أدناه:</p>
        </div>
        
        <a href="{{ $payment_link }}" class="btn">إعادة المحاولة</a>
        @endif
        
        <a href="/" class="btn btn-danger">العودة للصفحة الرئيسية</a>
    </div>
</body>
</html> 