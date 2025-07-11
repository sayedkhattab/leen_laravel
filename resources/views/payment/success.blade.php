<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تمت عملية الدفع بنجاح</title>
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
        .success-icon {
            width: 80px;
            height: 80px;
            background-color: #28a745;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .success-icon svg {
            width: 40px;
            height: 40px;
            fill: white;
        }
        h1 {
            color: #28a745;
            margin-bottom: 20px;
        }
        .details {
            text-align: right;
            margin: 30px 0;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .details p {
            margin: 10px 0;
            display: flex;
            justify-content: space-between;
        }
        .details span {
            font-weight: bold;
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
        .note {
            margin-top: 20px;
            padding: 15px;
            background-color: #fff3cd;
            border-radius: 8px;
            color: #856404;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
            </svg>
        </div>
        
        <h1>تمت عملية الدفع بنجاح</h1>
        @if(isset($is_partial) && $is_partial)
            <p>شكراً لك! تم استلام دفعة العربون بنجاح.</p>
        @else
            <p>شكراً لك! تم استلام دفعتك بنجاح.</p>
        @endif
        
        <div class="details">
            <p>
                <span>رقم العملية:</span>
                <span>{{ $payment->id }}</span>
            </p>
            <p>
                <span>إجمالي المبلغ:</span>
                <span>{{ $payment->amount }} ريال سعودي</span>
            </p>
            <p>
                <span>المبلغ المدفوع:</span>
                <span>{{ $payment->paid_amount }} ريال سعودي</span>
            </p>
            @if(isset($is_partial) && $is_partial)
                <p>
                    <span>المبلغ المتبقي:</span>
                    <span>{{ $payment->amount - $payment->paid_amount }} ريال سعودي</span>
                </p>
            @endif
            <p>
                <span>تاريخ الدفع:</span>
                <span>{{ $payment->updated_at->format('Y-m-d H:i') }}</span>
            </p>
            <p>
                <span>حالة الدفع:</span>
                <span>
                    @if($payment->status == 'Paid')
                        مدفوع بالكامل
                    @elseif($payment->status == 'Partially_Paid')
                        مدفوع جزئياً (عربون)
                    @else
                        {{ $payment->status }}
                    @endif
                </span>
            </p>
        </div>
        
        @if(isset($is_partial) && $is_partial)
            <div class="note">
                <p><strong>ملاحظة:</strong> تم دفع العربون بنجاح. سيتم دفع المبلغ المتبقي بعد اكتمال الخدمة مباشرة لمقدم الخدمة.</p>
            </div>
        @endif
        
        <a href="/" class="btn">العودة للصفحة الرئيسية</a>
    </div>
</body>
</html> 