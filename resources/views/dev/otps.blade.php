<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رموز التحقق الأخيرة</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            direction: rtl;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .otp-card {
            background-color: #f9f9f9;
            border-right: 4px solid #4CAF50;
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .otp-code {
            font-size: 24px;
            font-weight: bold;
            color: #4CAF50;
            background-color: #e8f5e9;
            padding: 10px 15px;
            border-radius: 4px;
            letter-spacing: 2px;
        }
        .otp-details {
            flex-grow: 1;
            margin-left: 20px;
        }
        .otp-phone {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 5px;
        }
        .otp-type {
            color: #666;
            font-size: 14px;
        }
        .refresh-btn {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }
        .refresh-btn:hover {
            background-color: #45a049;
        }
        .no-otps {
            text-align: center;
            color: #666;
            padding: 20px;
        }
        .warning {
            background-color: #fff3cd;
            color: #856404;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>رموز التحقق الأخيرة</h1>
        
        <div class="warning">
            <strong>تنبيه!</strong> هذه الصفحة للاستخدام في بيئة التطوير فقط. يجب إزالتها قبل نشر التطبيق في بيئة الإنتاج.
        </div>
        
        @if(count($otps) > 0)
            @foreach($otps as $otp)
                <div class="otp-card">
                    <div class="otp-details">
                        <div class="otp-phone">{{ $otp['phone'] }}</div>
                        <div class="otp-type">
                            @if(!empty($otp['action']))
                                نوع العملية: {{ $otp['action'] }}
                            @elseif(!empty($otp['type']))
                                نوع العملية: {{ $otp['type'] }}
                            @endif
                            
                            @if(!empty($otp['user_type']))
                                | نوع المستخدم: {{ $otp['user_type'] }}
                            @endif
                        </div>
                    </div>
                    <div class="otp-code">{{ $otp['code'] }}</div>
                </div>
            @endforeach
        @else
            <div class="no-otps">لا توجد رموز تحقق مسجلة حتى الآن.</div>
        @endif
        
        <a href="{{ url('/dev/recent-otps') }}" class="refresh-btn">تحديث</a>
    </div>
</body>
</html> 