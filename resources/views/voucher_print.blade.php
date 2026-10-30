<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $voucher->voucher_type == 'receipt' ? 'سند قبض' : 'سند صرف' }} رقم {{ $voucher->voucher_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Readex+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Readex Pro', sans-serif; background-color: #f1f5f9; }
        @media print {
            body { background-color: #ffffff; padding: 0; margin: 0; }
            .no-print { display: none !important; }
            .print-sheet { box-shadow: none !important; border: 2px solid #0f172a !important; margin: 0 !important; max-width: 100% !important; }
        }
    </style>
</head>
<body class="p-6 md:p-12 text-slate-900">

    @php
        $isReceipt = $voucher->voucher_type === 'receipt';
        $titleAr = $isReceipt ? 'سـنـد قـبـض رسـمـي' : 'سـنـد صـرف رسـمـي';
        $titleEn = $isReceipt ? 'OFFICIAL RECEIPT VOUCHER' : 'OFFICIAL PAYMENT VOUCHER';
        $themeColor = $isReceipt ? 'emerald' : 'rose';
        $entityName = $voucher->office_name ?: 'الإدارة المركزية — الشركة الأم لمنظومة العقارات';
        $partyName = $voucher->tenant_name ?: ($voucher->owner_name ?: ($voucher->office_name ?: 'حساب داخلي / عام'));
        $translatedPaymentMethod = \App\Http\Controllers\CompanyErpController::translatePaymentMethod($voucher->payment_method);
    @endphp

    <!-- شريط أزرار التحكم والطباعة وتصدير PDF -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between bg-slate-900 text-white px-6 py-4 rounded-2xl shadow-lg no-print">
        <div class="flex items-center gap-3">
            <span class="text-xl">🧾</span>
            <div>
                <h1 class="font-bold text-sm">{{ $titleAr }} — <span class="font-mono text-emerald-400">{{ $voucher->voucher_number }}</span></h1>
                <p class="text-xs text-slate-400">اضغط على "تصدير PDF / طباعة" لحفظ السند كملف PDF رسمي أو طباعته</p>
            </div>
        </div>
        <div class="flex items-center gap-2.5">
            <button onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-lg transition flex items-center gap-2">
                <span>🖨️ تصدير PDF / طباعة السند</span>
            </button>
            <button onclick="window.close(); history.back();" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold transition">
                عودة للنظام
            </button>
        </div>
    </div>

    <!-- وثيقة السند الرسمي -->
    <div class="print-sheet max-w-4xl mx-auto bg-white rounded-3xl border-2 border-slate-800 shadow-2xl overflow-hidden p-8 md:p-10 relative">
        
        <!-- ترويسة السند -->
        <div class="flex flex-wrap items-center justify-between gap-4 pb-6 border-b-2 border-slate-800">
            <div>
                <h2 class="text-xl font-bold text-slate-900">{{ $entityName }}</h2>
                @if($voucher->office_cr || $voucher->office_fal)
                    <p class="text-xs text-slate-600 mt-1">
                        سجل تجاري: <span class="font-mono font-bold">{{ $voucher->office_cr ?: '-' }}</span> |
                        رخصة فال: <span class="font-mono font-bold">{{ $voucher->office_fal ?: '-' }}</span>
                    </p>
                @else
                    <p class="text-xs text-slate-500 mt-1">الإدارة المالية المركزية والتوثيق المحاسبي</p>
                @endif
            </div>

            <div class="text-center px-6 py-3 rounded-2xl {{ $isReceipt ? 'bg-emerald-50 border-2 border-emerald-700 text-emerald-900' : 'bg-rose-50 border-2 border-rose-700 text-rose-900' }}">
                <div class="text-lg font-extrabold tracking-wide">{{ $titleAr }}</div>
                <div class="text-[10px] font-mono font-bold tracking-widest uppercase mt-0.5">{{ $titleEn }}</div>
            </div>

            <div class="text-left space-y-1">
                <div class="inline-block px-3.5 py-1.5 rounded-xl bg-slate-100 border border-slate-300 font-mono text-sm font-bold text-slate-900" dir="ltr">
                    No: {{ $voucher->voucher_number }}
                </div>
                <div class="text-xs text-slate-600 font-semibold">
                    التاريخ: <span class="font-mono font-bold">{{ $voucher->voucher_date }}</span>
                </div>
            </div>
        </div>

        <!-- مربع المبلغ -->
        <div class="my-6 flex flex-wrap items-center justify-between gap-4 p-5 rounded-2xl bg-slate-50 border border-slate-200">
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-slate-600">المبلغ رقماً:</span>
                <span class="px-5 py-2 rounded-xl bg-white border-2 border-slate-900 font-mono text-xl font-extrabold text-slate-900">
                    {{ number_format($voucher->amount, 2) }} SAR
                </span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-slate-600">طريقة الدفع:</span>
                <span class="px-4 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-bold">
                    {{ $translatedPaymentMethod }}
                </span>
            </div>
        </div>

        <!-- تفاصيل السند -->
        <div class="space-y-4 text-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 rounded-2xl border border-slate-200 bg-white">
                    <span class="block text-xs text-slate-400 font-bold mb-1">
                        {{ $isReceipt ? 'استلمنا من المكرم / الجهة:' : 'يصرف للمكرم / الجهة:' }}
                    </span>
                    <div class="font-bold text-base text-slate-900">{{ $partyName }}</div>
                </div>

                <div class="p-4 rounded-2xl border border-slate-200 bg-white">
                    <span class="block text-xs text-slate-400 font-bold mb-1">وذلك عن (البيان والتفاصيل):</span>
                    <div class="font-bold text-base text-slate-900">{{ $voucher->notes }}</div>
                </div>
            </div>
        </div>

        <!-- التوقيعات والختم الرسمي -->
        <div class="grid grid-cols-3 gap-6 mt-12 pt-6 border-t border-dashed border-slate-300 text-center text-xs">
            <div>
                <p class="font-bold text-slate-700 mb-8">إعداد المحاسب / الموظف</p>
                <p class="text-slate-500 font-semibold border-t border-slate-300 pt-2 inline-block px-6">{{ $voucher->creator_name ?? 'الإدارة المالية' }}</p>
            </div>
            <div>
                <p class="font-bold text-slate-700 mb-8">توقيع المستلم</p>
                <p class="text-slate-400 border-t border-slate-300 pt-2 inline-block px-10">............................</p>
            </div>
            <div>
                <p class="font-bold text-slate-700 mb-8">الختم والاعتماد الرسمي</p>
                <p class="text-emerald-700 font-bold border-t border-slate-300 pt-2 inline-block px-6">معتمد إلكترونياً</p>
            </div>
        </div>
    </div>

</body>
</html>