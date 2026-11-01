<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $voucher->voucher_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Readex+Pro:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Readex Pro', sans-serif; background-color: #e2e8f0; }
        @media print {
            body { background-color: #ffffff; padding: 0; margin: 0; }
            .no-print { display: none !important; }
            .voucher-paper { box-shadow: none !important; margin: 0 !important; width: 100% !important; max-width: 100% !important; }
        }
    </style>
</head>
<body class="p-6 md:p-12 text-slate-900">

    @php
        $isReceipt = $voucher->voucher_type === 'receipt';
        $titleAr = $isReceipt ? 'سـنـد قـبـض' : 'سـنـد صـرف';
        $titleEn = $isReceipt ? 'RECEIPT VOUCHER' : 'PAYMENT VOUCHER';
        $entityName = $voucher->office_name ?: 'الإدارة المركزية — الشركة الأم لمنظومة العقارات';
        $partyName = $voucher->tenant_name ?: ($voucher->owner_name ?: ($voucher->office_name ?: 'حساب داخلي / عام'));
        $translatedPaymentMethod = \App\Http\Controllers\CompanyErpController::translatePaymentMethod($voucher->payment_method);
    @endphp

    <!-- شريط التحكم العلوي -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between bg-slate-900 text-white px-6 py-4 rounded-xl shadow no-print">
        <div>
            <h1 class="font-bold text-sm">{{ $voucher->voucher_number }}</h1>
            <p class="text-xs text-slate-400">تصميم سند ورقي رسمي جاهز للطباعة أو الحفظ كـ PDF</p>
        </div>
        <div class="flex items-center gap-2.5">
            <button onclick="window.print()" class="px-5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition">
                🖨️ طباعة / حفظ PDF
            </button>
            <button onclick="window.close(); history.back();" class="px-4 py-2 rounded-lg bg-slate-700 hover:bg-slate-600 text-white text-xs font-semibold transition">
                رجوع
            </button>
        </div>
    </div>

    <!-- ورقة السند الطبيعي الكلاسيكي -->
    <div class="voucher-paper max-w-4xl mx-auto bg-[#fffdf9] border-4 border-double border-slate-900 rounded-lg shadow-xl p-8 md:p-10 space-y-6">
        
        <!-- الترويسة العلوية -->
        <div class="flex flex-wrap items-start justify-between gap-4 border-b-2 border-slate-900 pb-5">
            <div class="space-y-1">
                <h2 class="text-xl font-extrabold text-slate-900">{{ $entityName }}</h2>
                @if($voucher->office_cr || $voucher->office_fal)
                    <p class="text-xs text-slate-700 font-semibold">
                        سجل تجاري: <span class="font-mono">{{ $voucher->office_cr ?: '-' }}</span> |
                        رخصة فال: <span class="font-mono">{{ $voucher->office_fal ?: '-' }}</span>
                    </p>
                @else
                    <p class="text-xs text-slate-600">الإدارة المالية والحسابات المعتمدة</p>
                @endif
            </div>

            <div class="text-center px-8 py-2.5 bg-white border-2 border-slate-900 rounded shadow-sm">
                <div class="text-2xl font-black text-slate-900">{{ $titleAr }}</div>
                <div class="text-[11px] font-mono font-bold tracking-widest text-slate-500">{{ $titleEn }}</div>
            </div>

            <div class="text-left space-y-1.5 text-xs font-semibold">
                <div class="px-3 py-1 bg-white border border-slate-800 rounded font-mono text-sm font-extrabold text-red-700" dir="ltr">
                    {{ $voucher->voucher_number }}
                </div>
                <div>التاريخ: <span class="font-mono font-bold border-b border-dotted border-slate-500 px-2">{{ $voucher->voucher_date }}</span></div>
            </div>
        </div>

        <!-- المبلغ وطريقة الدفع -->
        <div class="flex flex-wrap items-center justify-between gap-4 py-2">
            <div class="flex items-center gap-3">
                <span class="text-sm font-bold text-slate-800">المبلغ رقماً:</span>
                <div class="px-6 py-2 bg-white border-2 border-slate-900 rounded font-mono text-2xl font-black text-slate-900 shadow-sm">
                    #{{ number_format($voucher->amount, 2) }}# <span class="text-xs font-bold">ريال سعودي</span>
                </div>
            </div>

            <div class="flex items-center gap-2 text-sm">
                <span class="font-bold text-slate-700">طريقة الدفع:</span>
                <span class="px-4 py-1.5 bg-white border border-slate-800 rounded font-bold text-slate-900">
                    {{ $translatedPaymentMethod }}
                </span>
            </div>
        </div>

        <!-- الحقول السطرية للسند -->
        <div class="bg-white p-6 rounded border border-slate-300 space-y-5 text-base">
            <div class="flex items-baseline gap-3 border-b border-dotted border-slate-400 pb-2.5">
                <span class="font-bold text-slate-700 text-sm shrink-0">
                    {{ $isReceipt ? 'استلمنا من المكرم / السادة:' : 'يصرف للمكرم / السادة:' }}
                </span>
                <span class="font-extrabold text-slate-900 flex-1 px-2">{{ $partyName }}</span>
            </div>

            <div class="flex items-baseline gap-3 border-b border-dotted border-slate-400 pb-2.5">
                <span class="font-bold text-slate-700 text-sm shrink-0">وذلك مقابل (البيان والتفاصيل):</span>
                <span class="font-bold text-slate-900 flex-1 px-2">{{ $voucher->notes }}</span>
            </div>
        </div>

        <!-- التوقيعات السفلية -->
        <div class="grid grid-cols-3 gap-6 pt-10 text-center text-xs font-bold text-slate-700">
            <div>
                <p class="mb-10">توقيع المستلم</p>
                <p class="border-t border-slate-400 pt-2 mx-6 text-slate-400 font-normal">............................</p>
            </div>
            <div>
                <p class="mb-10">أمين الصندوق / المحاسب</p>
                <p class="border-t border-slate-400 pt-2 mx-6 text-slate-800">{{ $voucher->creator_name ?? 'الإدارة المالية' }}</p>
            </div>
            <div>
                <p class="mb-10">الختم والاعتماد الرسمي</p>
                <p class="border-t border-slate-400 pt-2 mx-6 text-emerald-700">معتمد إلكترونياً ✔</p>
            </div>
        </div>
    </div>

</body>
</html>