<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>منظومة العقارات الذكية | لوحة الإدارة المركزية</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Readex+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Readex Pro', sans-serif; background-color: #f8fafc; }
        .glass-sidebar { background: linear-gradient(180deg, #0f172a 0%, #090d16 100%); }
        @media print {
            body * { visibility: hidden; }
            #printableVoucherArea, #printableVoucherArea * { visibility: visible; }
            #printableVoucherArea { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none !important; }
            .no-print { display: none !important; }
        }
    </style>
    
    <style id="modern-minimal-ui">
    /* 1. توحيد الخلفية والخطوط بهدوء بصري */
    body {
        background-color: #f8fafc !important;
        color: #0f172a !important;
        letter-spacing: -0.01em;
    }

    /* 2. توحيد القوائم الجانبية بلون كحلي داكن رسمي بدون تدرجات صارخة */
    .glass-sidebar, .office-sidebar, .owner-sidebar {
        background: #0f172a !important;
        border-left: 1px solid #1e293b !important;
        box-shadow: none !important;
    }

    /* تنسيق روابط القائمة الجانبية النشطة */
    aside nav a.from-emerald-600,
    aside nav a.from-amber-500,
    aside nav a.bg-white {
        background: #1e293b !important;
        background-image: none !important;
        color: #ffffff !important;
        box-shadow: none !important;
        border-right: 3px solid #38bdf8 !important;
        border-radius: 8px !important;
    }
    aside nav a {
        border-radius: 8px !important;
    }

    /* 3. تحويل البطاقات والحاويات والـ Modals إلى زوايا عصرية ناعمة وحدود رفيعة */
    .rounded-3xl, .rounded-2xl {
        border-radius: 10px !important;
    }
    .shadow-2xl, .shadow-lg, .shadow-md, .shadow-sm {
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
    }

    /* 4. ترتيب وتحديث جميع الجداول لتصبح نظيفة وعصرية */
    table {
        border-collapse: separate !important;
        border-spacing: 0 !important;
        width: 100% !important;
    }
    table thead tr {
        background-color: #f1f5f9 !important;
        color: #475569 !important;
    }
    table thead th {
        background-color: #f1f5f9 !important;
        color: #475569 !important;
        font-weight: 600 !important;
        font-size: 0.78rem !important;
        padding: 12px 16px !important;
        border-bottom: 1px solid #e2e8f0 !important;
    }
    table tbody td {
        padding: 12px 16px !important;
        border-bottom: 1px solid #f1f5f9 !important;
        color: #1e293b !important;
        font-size: 0.85rem !important;
        vertical-align: middle !important;
    }
    table tbody tr:hover {
        background-color: #f8fafc !important;
    }

    /* 5. توحيد الأزرار وإلغاء تدرجات الألوان المزعجة */
    button[class*="bg-gradient"],
    a[class*="bg-gradient"],
    button.bg-emerald-600,
    button.bg-indigo-600,
    a.bg-emerald-600 {
        background: #0f172a !important;
        background-image: none !important;
        color: #ffffff !important;
        border: 1px solid #0f172a !important;
        border-radius: 8px !important;
        box-shadow: none !important;
        transition: all 0.15s ease !important;
    }
    button[class*="bg-gradient"]:hover,
    a[class*="bg-gradient"]:hover,
    button.bg-emerald-600:hover,
    button.bg-indigo-600:hover,
    a.bg-emerald-600:hover {
        background: #334155 !important;
        border-color: #334155 !important;
    }

    /* أزرار الإجراءات الفرعية داخل الجداول */
    td button, td a {
        border-radius: 6px !important;
        box-shadow: none !important;
    }

    /* 6. تهدئة الملصقات (Badges) لتكون رسمية وغير مزدحمة بالألوان */
    span.rounded-full, span.rounded-xl, span.rounded-lg {
        border-radius: 6px !important;
        font-weight: 500 !important;
        box-shadow: none !important;
    }

    /* 7. تحسين حقول الإدخال والبحث (Inputs & Selects) */
    input:not([type="checkbox"]):not([type="radio"]), select, textarea {
        border-radius: 8px !important;
        border-color: #cbd5e1 !important;
        box-shadow: none !important;
    }
    input:focus, select:focus, textarea:focus {
        border-color: #0f172a !important;
        outline: none !important;
        ring: 0 !important;
    }
</style>

</head>
<body class="text-slate-800 flex min-h-screen">

    <!-- القائمة الجانبية العصرية -->
    <aside class="w-72 glass-sidebar text-white flex flex-col shrink-0 shadow-2xl sticky top-0 h-screen no-print">
        <div class="p-6 border-b border-slate-800/80 flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center shadow-lg shadow-emerald-500/20 shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <div>
                <h1 class="font-bold text-base tracking-tight text-white">نظام إدارة العقارات</h1>
            </div>
        </div>

        <nav class="p-4 space-y-1.5 flex-1 overflow-y-auto">
            <div class="px-3 py-2 text-[11px] font-bold text-slate-500 uppercase tracking-wider">الإدارة المركزية</div>
            
            <a href="{{ route('erp.dashboard', ['tab' => 'overview']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'overview' ? 'bg-gradient-to-l from-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-600/25' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                <span>لوحة التحكم</span>
            </a>

            <a href="{{ route('erp.dashboard', ['tab' => 'offices']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'offices' ? 'bg-gradient-to-l from-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-600/25' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                <span>مـكـاتب العقـار</span>
            </a>

            <a href="{{ route('erp.dashboard', ['tab' => 'properties']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'properties' ? 'bg-gradient-to-l from-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-600/25' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                <span>أنواع العقارات والمتطلبات</span>
            </a>

            <div class="px-3 pt-5 pb-2 text-[11px] font-bold text-slate-500 uppercase tracking-wider">النظام المالي والمحاسبي</div>

            <a href="{{ route('erp.dashboard', ['tab' => 'accounting']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'accounting' ? 'bg-gradient-to-l from-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-600/25' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                <span>شجرة الحسابات </span>
            </a>

            <a href="{{ route('erp.dashboard', ['tab' => 'vouchers']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'vouchers' ? 'bg-gradient-to-l from-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-600/25' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                <span>سندات القبض و الصرف</span>
            </a>

            <a href="{{ route('erp.dashboard', ['tab' => 'journal']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'journal' ? 'bg-gradient-to-l from-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-600/25' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                <span>القيود اليومية</span>
            </a>

            <a href="{{ route('erp.dashboard', ['tab' => 'invoicing']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'invoicing' ? 'bg-gradient-to-l from-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-600/25' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                <span> الفواتير الضريبية</span>
            </a>

            <a href="{{ route('erp.dashboard', ['tab' => 'account_statement']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'account_statement' ? 'bg-gradient-to-l from-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-600/25' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                <span>كشف الحساب </span>
            </a>

            <a href="{{ route('erp.dashboard', ['tab' => 'statements']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'statements' ? 'bg-gradient-to-l from-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-600/25' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                <span>📖</span>
                <span>الأستاذ العام والقوائم المالية</span>
            </a>

            <div class="px-3 pt-5 pb-2 text-[11px] font-bold text-slate-500 uppercase tracking-wider">الربط والأمان</div>

            <a href="{{ route('erp.dashboard', ['tab' => 'whatsapp']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'whatsapp' ? 'bg-gradient-to-l from-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-600/25' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                <span>💬</span>
                <span>إعدادات الربط واتساب API</span>
            </a>

            <a href="{{ route('erp.dashboard', ['tab' => 'settings']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'settings' ? 'bg-gradient-to-l from-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-600/25' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                <span>⚙️</span>
                <span>بيانات الدخول الشخصية</span>
            </a>
        </nav>

        <div class="p-4 border-t border-slate-800/80 bg-slate-950/50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-white">{{ auth()->user()->name }}</p>
                    <p class="text-[11px] text-emerald-400 font-mono" dir="ltr">{{ auth()->user()->phone }}</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-red-500/10 hover:bg-red-500/20 text-red-400 text-xs font-semibold transition">
                        خروج
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- المحتوى الرئيسي -->
    <div class="flex-1 flex flex-col min-w-0">
       <header class="min-h-20 py-3 bg-white/90 backdrop-blur-md border-b border-slate-200/80 px-8 flex flex-wrap items-center justify-between gap-4 sticky top-0 z-20 no-print">
            <div class="flex items-center gap-3">
                <span class="px-3.5 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    متصل كـ: المدير العام ({{ auth()->user()->username }})
                </span>
                <a href="{{ route('landing') }}" target="_blank"
                   class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition flex items-center gap-1.5">
                    <span>🌐</span>
                    <span>معاينة الواجهة الرئيسية للموقع</span>
                </a>
            </div>

            <!-- شريط أدوات الأدمن الذكي: الفحص الأوتوماتيكي الشامل + سجل الأخطاء + النسخة الاحتياطية -->
            <div class="flex flex-wrap items-center gap-2.5">
                <button type="button" onclick="openModal('systemDiagnosticsModal')"
                        class="px-4 py-2 rounded-2xl bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white border border-indigo-200 text-xs font-bold transition flex items-center gap-2 shadow-sm">
                    <span>🤖</span>
                    <span>الفحص الأوتوماتيكي الشامل للنظام</span>
                </button>

                @php $errCount = isset($systemLogs) ? $systemLogs->count() : 0; @endphp
                <button type="button" onclick="openModal('systemLogsModal')"
                        class="px-4 py-2 rounded-2xl {{ $errCount > 0 ? 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-600 hover:text-white' : 'bg-slate-100 text-slate-700 border-slate-200 hover:bg-slate-800 hover:text-white' }} border text-xs font-bold transition flex items-center gap-2 shadow-sm">
                    <span>🚨</span>
                    <span>سجل الأخطاء (Logs)</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono {{ $errCount > 0 ? 'bg-rose-600 text-white' : 'bg-slate-300 text-slate-800' }}">
                        {{ $errCount }}
                    </span>
                </button>

                <button type="button" onclick="openModal('systemBackupModal')"
                        class="px-4 py-2 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition flex items-center gap-2 shadow-md shadow-emerald-600/20">
                    <span>💾</span>
                    <span>نسخة احتياطية / استعادة</span>
                </button>
            </div>
        </header>

        <main class="p-8 space-y-6">

            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center justify-between shadow-sm no-print">
                    <span>✅ {{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-700 text-sm font-semibold shadow-sm no-print">
                    ⚠️ {{ $errors->first() }}
                </div>
            @endif

            <!-- 1. النظرة العامة -->
            @if($tab == 'overview')
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-slate-500">مكاتب العقار</span>
                        </div>
                        <div class="text-3xl font-bold text-slate-900">{{ $stats['offices_count'] }}</div>
                        <p class="text-xs text-emerald-600 mt-2 font-medium">{{ $stats['active_offices'] }} مكتب نشط حالياً</p>
                    </div>

                    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-slate-500">تصنيفات العقارات المعرّفة</span>
                        </div>
                        <div class="text-3xl font-bold text-slate-900">{{ $stats['property_types_count'] }}</div>
                        <p class="text-xs text-slate-400 mt-2">سكني، تجاري، زراعي، صناعي</p>
                    </div>

                    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-slate-500">إجمالي سندات القبض (RV)</span>
                        </div>
                        <div class="text-2xl font-bold text-emerald-600">{{ number_format($stats['total_receipts'], 2) }} <span class="text-xs">ر.س</span></div>
                        <p class="text-xs text-slate-400 mt-2">المقبوضات المرحلة للشجرة</p>
                    </div>

                    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-slate-500">إجمالي سندات الصرف (SA)</span>
                        </div>
                        <div class="text-2xl font-bold text-slate-900">{{ number_format($stats['total_payments'], 2) }} <span class="text-xs">ر.س</span></div>
                        <p class="text-xs text-slate-400 mt-2">المصروفات والعمولات المصروفة</p>
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="font-bold text-slate-900">أحدث مكاتب العقار </h3>
                        <a href="{{ route('erp.dashboard', ['tab' => 'offices']) }}" class="text-xs font-bold text-emerald-600 hover:underline">إدارة المكاتب وكشوف الحسابات ←</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-right">
                            <thead class="bg-slate-50 text-slate-600 text-xs uppercase border-b border-slate-200/60">
                                <tr>
                                    <th class="py-3.5 px-6">اسم المكتب</th>
                                    <th class="py-3.5 px-6">رقم الجوال</th>
                                    <th class="py-3.5 px-6"> رقم الحساب </th>
                                    <th class="py-3.5 px-6">نوع الاشتراك</th>
                                    <th class="py-3.5 px-6">صافي الربح من المكتب</th>
                                    <th class="py-3.5 px-6">الحالة</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($offices->take(5) as $office)
                                    @php
                                        $officeReceipts = $vouchers->where('office_id', $office->id)->where('voucher_type', 'receipt')->sum('amount');
                                        $officePayments = $vouchers->where('office_id', $office->id)->where('voucher_type', 'payment')->sum('amount');
                                        $netProfit = $officeReceipts - $officePayments;
                                    @endphp
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-4 px-6 font-bold text-slate-900">{{ $office->name }}</td>
                                        <td class="py-4 px-6 font-mono text-xs text-emerald-700 font-semibold" dir="ltr">{{ $office->phone }}</td>
                                        <td class="py-4 px-6 font-mono text-xs">{{ $office->account_code ? $office->account_code . ' - ' . $office->account_name : 'غير مرتبط' }}</td>
                                        <td class="py-4 px-6">
                                            @if($office->billing_type == 'annual_subscription')
                                                <span class="px-2.5 py-1 rounded-xl text-xs font-semibold bg-blue-50 text-blue-700">اشتراك سنوي ({{ number_format($office->annual_fee, 0) }} ر.س)</span>
                                            @elseif($office->billing_type == 'per_property_percentage')
                                                <span class="px-2.5 py-1 rounded-xl text-xs font-semibold bg-amber-50 text-amber-700">نسبة ({{ $office->company_commission_rate }}%)</span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-xl text-xs font-semibold bg-emerald-50 text-emerald-700">مدمج ({{ number_format($office->annual_fee, 0) }} ر.س + {{ $office->company_commission_rate }}%)</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 font-bold {{ $netProfit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                            {{ number_format($netProfit, 2) }} ر.س
                                        </td>
                                        <td class="py-4 px-6">
                                            @if($office->status == 'active')
                                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">نشط</span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">موقوف</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="py-8 text-center text-slate-400">لا توجد مكاتب مضافة بعد.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- 2. إدارة مكاتب العقار (الجدول فقط + بحث فوري + زر إضافة مكتب أقصى اليسار + كشف حساب عند الضغط على الاسم) -->
            @if($tab == 'offices')
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <!-- شريط الأدوات العلوي: العنوان + البحث الفوري + زر إضافة مكتب جديد أقصى اليسار -->
                    <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="font-bold text-lg text-slate-900">سجل مكاتب العقار وإدارة اتفاقياتهم</h3>
                            <p class="text-xs text-slate-500 mt-0.5">اضغط على <b class="text-emerald-700">اسم المكتب</b> لعرض كشف الحساب المالي الخاص به، أو ابحث مباشرة بالاسم أو رقم الجوال</p>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <!-- حقل البحث الفوري بدون انتر -->
                            <div class="relative w-72">
                                <input type="text" id="officeInstantSearch" oninput="filterOfficesTable()"
                                       placeholder="🔍 ابحث باسم المكتب أو الجوال..."
                                       class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:outline-none focus:border-emerald-600 transition">
                            </div>

                            <!-- زر محترم أقصى اليسار لإضافة مكتب جديد -->
                            <button type="button" onclick="openModal('addOfficeModal')"
                                    class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-sm shadow-lg shadow-emerald-600/25 flex items-center gap-2 transition">
                                <span>➕</span>
                                <span>إضافة مكتب جديد</span>
                            </button>
                        </div>
                    </div>

                    <!-- جدول المكاتب -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-right" id="officesTable">
                            <thead class="bg-slate-50 text-slate-600 text-xs border-b border-slate-200/60">
                                <tr>
                                    <th class="py-4 px-5">المكتب والترخيص (اضغط لكشف الحساب)</th>
                                    <th class="py-4 px-4">الجوال واسم المستخدم</th>
                                    <th class="py-4 px-4">رقم الحساب</th>
                                    <th class="py-4 px-4">نوع الاشتراك</th>
                                    <th class="py-4 px-4">الاشتراك / النسبة</th>
                                    <th class="py-4 px-4">صافي الربح و% (متغير بالسندات)</th>
                                    <th class="py-4 px-4">الحالة</th>
                                    <th class="py-4 px-4 text-center">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($offices as $office)
                                    @php
                                        $localPhone = \App\Http\Controllers\CompanyErpController::toLocalPhone($office->phone);
                                        $officeVouchers = $vouchers->where('office_id', $office->id);
                                        $recTotal = $officeVouchers->where('voucher_type', 'receipt')->sum('amount');
                                        $payTotal = $officeVouchers->where('voucher_type', 'payment')->sum('amount');
                                        $netOfficeProfit = $recTotal - $payTotal;
                                        $profitRate = $recTotal > 0 ? round(($netOfficeProfit / $recTotal) * 100, 1) : 0;
                                    @endphp
                                    <tr class="hover:bg-slate-50/90 transition office-row"
                                        data-search="{{ mb_strtolower($office->name . ' ' . $office->phone . ' ' . $localPhone . ' ' . $office->manager_username . ' ' . $office->fal_license) }}">
                                        <td class="py-4 px-5">
                                            <button type="button" onclick="openOfficeStatementModal('{{ $office->id }}')"
                                                    class="text-right group focus:outline-none">
                                                <div class="font-bold text-slate-900 group-hover:text-emerald-600 underline decoration-dotted flex items-center gap-1.5 text-sm">
                                                    <span>📑 {{ $office->name }}</span>
                                                </div>
                                                <div class="text-xs text-slate-400 mt-0.5">فال: {{ $office->fal_license ?: 'غير محدد' }} | انقر لكشف الحساب ({{ $officeVouchers->count() }} حركة)</div>
                                            </button>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="font-mono text-xs font-bold text-emerald-700" dir="ltr">{{ $office->phone }}</div>
                                            <div class="text-xs text-slate-500">المستخدم: <span class="font-mono">{{ $office->manager_username }}</span></div>
                                        </td>
                                        <td class="py-4 px-4">
                                            @if($office->account_code)
                                                <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 font-mono text-xs font-bold border border-slate-200">
                                                    {{ $office->account_code }} - {{ $office->account_name }}
                                                </span>
                                            @else
                                                <span class="text-xs text-slate-400">غير مرتبط</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4">
                                            @if($office->billing_type == 'annual_subscription')
                                                <span class="px-2.5 py-1 rounded-xl text-xs font-semibold bg-blue-50 text-blue-700">اشتراك سنوي</span>
                                            @elseif($office->billing_type == 'per_property_percentage')
                                                <span class="px-2.5 py-1 rounded-xl text-xs font-semibold bg-amber-50 text-amber-700">نسبة لكل عقار</span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-xl text-xs font-semibold bg-emerald-50 text-emerald-700">مدمج (اشتراك + نسبة)</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4">
                                            @if($office->billing_type == 'annual_subscription')
                                                <div class="text-xs font-bold text-slate-900">{{ number_format($office->annual_fee, 2) }} ر.س</div>
                                            @elseif($office->billing_type == 'per_property_percentage')
                                                <div class="text-xs text-amber-700 font-bold">النسبة: {{ $office->company_commission_rate }}%</div>
                                            @else
                                                <div class="text-xs font-bold text-slate-900">{{ number_format($office->annual_fee, 2) }} ر.س</div>
                                                <div class="text-xs text-amber-700 font-bold">+ النسبة: {{ $office->company_commission_rate }}%</div>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="font-bold text-xs {{ $netOfficeProfit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                                {{ number_format($netOfficeProfit, 2) }} ر.س
                                            </div>
                                            <div class="text-[11px] text-slate-500">
                                                نسبة الربح الفعلي: <b class="{{ $profitRate >= 0 ? 'text-emerald-700' : 'text-red-600' }}">{{ $profitRate }}%</b>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            @if($office->status == 'active')
                                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">نشط</span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">موقوف</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            @php
                                                $localPhone = \App\Http\Controllers\CompanyErpController::toLocalPhone($office->phone);
                                            @endphp
                                            <div class="flex items-center justify-center gap-1.5">
                                                <a href="{{ route('erp.switch_office', $office->id) }}"
                                                   class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 text-xs font-bold transition"
                                                   title="دخول لبوابة المكتب">
                                                     دخول
                                                </a>
                                                <form action="{{ route('erp.offices.toggle_owner_invoicing', $office->id) }}" method="POST" title="تفعيل أو إخفاء ميزة إصدار فاتورة ضريبية باسم المالك في بوابة هذا المكتب">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1.5 rounded-xl border text-[11px] font-bold transition {{ !empty($office->allow_owner_tax_invoice) ? 'bg-amber-100 text-amber-900 border-amber-300 hover:bg-amber-200' : 'bg-slate-100 text-slate-500 border-slate-200 hover:bg-slate-200' }}">
                                                        {{ !empty($office->allow_owner_tax_invoice) ? ' الفوترة بإسم المالك مفعلة ' : ' تفعيل الفوترة بإٍسم المالك   ' }}
                                                    </button>
                                                </form>
                                                <button type="button"
                                                        onclick="openEditOfficeModal({
                                                            id: '{{ $office->id }}',
                                                            name: '{{ addslashes($office->name) }}',
                                                            cr_number: '{{ addslashes($office->cr_number) }}',
                                                            fal_license: '{{ addslashes($office->fal_license) }}',
                                                            tax_number: '{{ addslashes($office->tax_number ?? '') }}',
                                                            allow_owner_tax_invoice: '{{ $office->allow_owner_tax_invoice ?? 0 }}',
                                                            phone: '{{ $localPhone }}',
                                                            username: '{{ addslashes($office->manager_username) }}',
                                                            billing_type: '{{ $office->billing_type }}',
                                                            annual_fee: '{{ $office->annual_fee }}',
                                                            company_commission_rate: '{{ $office->company_commission_rate }}',
                                                            subscription_ends_at: '{{ $office->subscription_ends_at }}',
                                                            status: '{{ $office->status }}',
                                                            parent_account_id: '{{ $office->parent_account_id ?? '' }}'
                                                        })"
                                                        class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-white text-xs font-semibold transition">
                                                     تعديل
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="py-10 text-center text-slate-400">لا توجد مكاتب مسجلة بعد. اضغط على "إضافة مكتب جديد" للبدء.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- نافذة (Modal) إضافة مكتب عقار جديد مع الربط الشجري التلقائي والاتفاقية الديناميكية -->
                <div id="addOfficeModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-slate-200 max-h-[92vh] overflow-y-auto">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                            <div>
                                <h3 class="font-bold text-base text-slate-900">تسجيل مكتب عقار جديد</h3>
                                <p class="text-xs text-slate-500">يرتبط تلقائياً بشجرة الحسابات وبإعدادات واتساب الإدارة</p>
                            </div>
                            <button type="button" onclick="closeModal('addOfficeModal')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 font-bold">✕</button>
                        </div>

                        <form action="{{ route('erp.offices.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">اسم مكتب العقار *</label>
                                <input type="text" name="name" required placeholder="مثال: شركة روافد العقارية"
                                       class="w-full rounded-2xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:border-emerald-600">
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">السجل التجاري</label>
                                    <input type="text" name="cr_number" placeholder="1010XXXXXX"
                                           class="w-full rounded-2xl border border-slate-300 px-3.5 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">رخصة فال</label>
                                    <input type="text" name="fal_license" placeholder="12000XXXXX"
                                           class="w-full rounded-2xl border border-slate-300 px-3.5 py-2 text-sm">
                                </div>
                            </div>

                            <!-- الربط بشجرة الحسابات (يتذكر المجلد الأب ويولد كوداً متسلسلاً تلقائياً) -->
                            <div class="p-3.5 rounded-2xl bg-blue-50/60 border border-blue-200 space-y-2">
                                <div class="text-xs font-bold text-blue-900"> الربط الآلي بشجرة الحسابات </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">اختر المجلد الأب في شجرة الحسابات (سيتم التسلسل تحته تلقائياً لكل مكتب جديد)</label>
                                    <select name="parent_account_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-semibold">
                                        <option value="">-- إنشاء تحت مجلد ذمم مكاتب العقار (تلقائي متسلسل) --</option>
                                        @foreach($accounts as $acc)
                                            <option value="{{ $acc->id }}" {{ ($defaultOfficeParentAccId ?? '') == $acc->id ? 'selected' : '' }}>
                                                {{ $acc->code }} - {{ $acc->name }} {{ $acc->is_group ? '(مجلد رئيسي)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-[11px] text-blue-700 mt-1">سيتم فتح حساب فرعي باسم المكتب بكود متسلسل آلياً في الشجرة لتسجيل سندات القبض والصرف عليه.</p>
                                </div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                                <div class="text-xs font-bold text-emerald-700">بيانات الدخول للمكتب</div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">رقم الجوال (10 أرقام يبدأ بـ 05) *</label>
                                    <input type="tel" name="phone" maxlength="10" required placeholder="05XXXXXXXX" dir="ltr"
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                                           class="w-full text-left font-mono rounded-xl border border-slate-300 px-3.5 py-2 text-sm bg-white">
                                </div>
                                <div class="grid grid-cols-2 gap-2.5">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">اسم المستخدم الاحتياطي *</label>
                                        <input type="text" name="username" required placeholder="rawafed_office"
                                               class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">كلمة المرور الاحتياطية *</label>
                                        <input type="text" name="password" value="123456" required
                                               class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white">
                                    </div>
                                </div>
                            </div>

                            <!-- الاتفاقية المالية مع إظهار وإخفاء الحقول تلقائياً -->
                            <div class="p-3.5 rounded-2xl bg-emerald-50/50 border border-emerald-200/70 space-y-3">
                                <div class="text-xs font-bold text-slate-800">الاتفاقية المالية مع صاحب المكتب</div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">نوع الاتفاق المالي *</label>
                                    <select name="billing_type" id="add_billing_type" onchange="toggleBillingInputs('add')" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white font-semibold">
                                        <option value="annual_subscription">اشتراك سنوي </option>
                                        <option value="per_property_percentage">نسبة من قيمة كل عقار </option>
                                        <option value="hybrid"> (اشتراك سنوي + نسبة )</option>
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-2.5">
                                    <div id="add_annual_fee_box">
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">الاشتراك السنوي (ر.س) *</label>
                                        <input type="number" step="0.01" name="annual_fee" id="add_annual_fee" value="1000"
                                               class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white font-bold text-emerald-700">
                                    </div>
                                    <div id="add_commission_rate_box" class="hidden">
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">نسبة الشركة (%) *</label>
                                        <input type="number" step="0.01" name="company_commission_rate" id="add_commission_rate" value="0"
                                               class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white font-bold text-amber-700">
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-2 pt-2">
                                <button type="button" onclick="closeModal('addOfficeModal')" class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold">إلغاء</button>
                                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-600/20">
                                    اعتماد المكتب وربطه بالشجرة
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- نافذة (Modal) تعديل حساب المكتب والاتفاقية المالية -->
                <div id="editOfficeModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 max-h-[92vh] overflow-y-auto">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                            <h3 class="font-bold text-base text-slate-900">تعديل حساب المكتب والاتفاقية المالية</h3>
                            <button type="button" onclick="closeEditOfficeModal()" class="text-slate-400 hover:text-slate-700 font-bold text-lg">✕</button>
                        </div>

                        <form id="editOfficeForm" method="POST" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-2 gap-3">
                                <div class="col-span-2">
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">اسم مكتب العقار *</label>
                                    <input type="text" name="name" id="edit_name" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">السجل التجاري</label>
                                    <input type="text" name="cr_number" id="edit_cr_number" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">رخصة فال</label>
                                    <input type="text" name="fal_license" id="edit_fal_license" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm">
                                </div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                                <div class="text-xs font-bold text-emerald-700">تعديل بيانات الدخول</div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">رقم الجوال (10 أرقام) *</label>
                                        <input type="tel" name="phone" id="edit_phone" maxlength="10" required dir="ltr"
                                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                                               class="w-full text-left font-mono rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">اسم المستخدم *</label>
                                        <input type="text" name="username" id="edit_username" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">كلمة مرور جديدة (اتركها فارغة إذا لا تريد تغييرها)</label>
                                    <input type="text" name="password" placeholder="أدخل كلمة مرور جديدة فقط عند الرغبة بالتغيير" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white">
                                </div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-amber-50/60 border border-amber-200 space-y-3">
                                <div class="text-xs font-bold text-amber-900">تعديل الاتفاق المالي والاشتراك</div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">نوع الاتفاق المالي *</label>
                                    <select name="billing_type" id="edit_billing_type" onchange="toggleBillingInputs('edit')" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white font-semibold">
                                        <option value="annual_subscription">اشتراك سنوي مقطوع</option>
                                        <option value="per_property_percentage">نسبة من قيمة كل عقار / عقد</option>
                                        <option value="hybrid">مدمج (اشتراك سنوي + نسبة)</option>
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div id="edit_annual_fee_box">
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">قيمة الاشتراك السنوي (ر.س)</label>
                                        <input type="number" step="0.01" name="annual_fee" id="edit_annual_fee" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white">
                                    </div>
                                    <div id="edit_commission_rate_box">
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">نسبة الشركة من العقار (%)</label>
                                        <input type="number" step="0.01" name="company_commission_rate" id="edit_company_commission_rate" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">تاريخ انتهاء الاشتراك</label>
                                        <input type="date" name="subscription_ends_at" id="edit_subscription_ends_at" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">حالة حساب المكتب</label>
                                        <select name="status" id="edit_status" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white">
                                            <option value="active">نشط (مفعل)</option>
                                            <option value="suspended">موقوف مؤقتاً</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-2 pt-2">
                                <button type="button" onclick="closeEditOfficeModal()" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">إلغاء</button>
                                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-lg shadow-emerald-600/20">حفظ التعديلات والاتفاقية</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- نوافذ كشف الحساب الخاص بين الإدارة وكل مكتب عقار (عند الضغط على اسم المكتب) -->
                @foreach($offices as $office)
                    @php
                        $offVouchers = $vouchers->where('office_id', $office->id);
                        $offRec = $offVouchers->where('voucher_type', 'receipt')->sum('amount');
                        $offPay = $offVouchers->where('voucher_type', 'payment')->sum('amount');
                        $offNet = $offRec - $offPay;
                    @endphp
                    <div id="officeStatementModal_{{ $office->id }}" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-4xl w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] flex flex-col">
                            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                                <div>
                                    <h3 class="font-bold text-base text-slate-900">📑 كشف الحساب المالي الخاص بين الإدارة ومكتب: <span class="text-emerald-600">{{ $office->name }}</span></h3>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        حساب الشجرة: <span class="font-mono font-bold">{{ $office->account_code ?? '-' }}</span> |
                                        إجمالي المقبوضات من المكتب: <b class="text-emerald-600">{{ number_format($offRec, 2) }} ر.س</b> |
                                        إجمالي المصروفات والعمولات للمكتب: <b class="text-red-600">{{ number_format($offPay, 2) }} ر.س</b> |
                                        الصافي: <b class="text-slate-900">{{ number_format($offNet, 2) }} ر.س</b>
                                    </p>
                                </div>
                                <button type="button" onclick="closeModal('officeStatementModal_{{ $office->id }}')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 font-bold">✕</button>
                            </div>

                            <!-- نموذج سريع لتسجيل عملية في كشف حساب المكتب -->
                            <form action="{{ route('erp.vouchers.store') }}" method="POST" class="my-4 p-4 rounded-2xl bg-slate-50 border border-slate-200 grid grid-cols-1 md:grid-cols-12 gap-2.5 items-end">
                                @csrf
                                <input type="hidden" name="office_id" value="{{ $office->id }}">
                                <input type="hidden" name="stay_in_offices" value="1">
                                <div class="md:col-span-3">
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">نوع العملية *</label>
                                    <select name="voucher_type" class="w-full rounded-xl border border-slate-300 px-2.5 py-2 text-xs bg-white font-semibold">
                                        <option value="receipt"> قبض دفعة من المكتب</option>
                                        <option value="payment"> صرف عمولة / مصروف للمكتب</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">المبلغ (ر.س) *</label>
                                    <input type="number" step="0.01" name="amount" required placeholder="0.00" class="w-full rounded-xl border border-slate-300 px-2.5 py-2 text-xs bg-white font-bold">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">حساب الصندوق/البنك *</label>
                                    <select name="cash_account_id" class="w-full rounded-xl border border-slate-300 px-2 py-2 text-xs bg-white">
                                        @foreach($accounts->where('is_group', 0) as $a)
                                            <option value="{{ $a->id }}">{{ $a->code }} - {{ $a->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">البيان (وذلك عن...) *</label>
                                    <input type="text" name="notes" required placeholder="مثال: استلام دفعة اشتراك / صرف عمولة..." class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white">
                                </div>
                                <div class="md:col-span-2">
                                    <button type="submit" class="w-full py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow">
                                        + تسجيل بالكشف
                                    </button>
                                </div>
                            </form>

                            <div class="overflow-y-auto flex-1 rounded-2xl border border-slate-200">
                                <table class="w-full text-xs text-right">
                                    <thead class="bg-slate-100 text-slate-700 border-b">
                                        <tr>
                                            <th class="py-3 px-4">رقم السند</th>
                                            <th class="py-3 px-4">التاريخ</th>
                                            <th class="py-3 px-4">نوع العملية</th>
                                            <th class="py-3 px-4">البيان والتفاصيل</th>
                                            <th class="py-3 px-4">مقبوضات (+)</th>
                                            <th class="py-3 px-4">مصروفات (-)</th>
                                            <th class="py-3 px-4 text-center">إجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($offVouchers as $ov)
                                            <tr class="hover:bg-slate-50">
                                                <td class="py-3 px-4 font-mono font-bold">{{ $ov->voucher_number }}</td>
                                                <td class="py-3 px-4 font-mono">{{ $ov->voucher_date }}</td>
                                                <td class="py-3 px-4">
                                                    @if($ov->voucher_type == 'receipt')
                                                        <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 font-bold">سند قبض</span>
                                                    @else
                                                        <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 font-bold">سند صرف</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-4 font-semibold text-slate-800">{{ $ov->notes }}</td>
                                                <td class="py-3 px-4 font-bold text-emerald-600">{{ $ov->voucher_type == 'receipt' ? number_format($ov->amount, 2) . ' ر.س' : '-' }}</td>
                                                <td class="py-3 px-4 font-bold text-red-600">{{ $ov->voucher_type == 'payment' ? number_format($ov->amount, 2) . ' ر.س' : '-' }}</td>
                                                <td class="py-3 px-4 text-center">
                                                    <a href="{{ route('erp.dashboard', ['tab' => 'vouchers']) }}" class="text-blue-600 hover:underline font-bold">عرض / PDF</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="7" class="py-8 text-center text-slate-400">لا توجد حركات مالية مسجلة في كشف حساب هذا المكتب حتى الآن.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif

            <!-- 3. أنواع العقارات والمتطلبات الديناميكية -->
            @if($tab == 'properties')
                @php
                    $catMap = [
                        'residential' => 'سكني',
                        'commercial' => 'تجاري',
                        'agricultural' => 'زراعي',
                        'industrial' => 'صناعي',
                    ];
                    $fieldTypeMap = [
                        'text' => 'نص (Text)',
                        'number' => 'رقم / مساحة (Number)',
                        'date' => 'تاريخ (Date)',
                        'boolean' => 'نعم / لا',
                        'select' => 'قائمة خيارات منسدلة',
                        'file' => 'مرفق ملف PDF أو صورة (حد أقصى 5MB)',
                    ];
                @endphp

                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="font-bold text-lg text-slate-900">سجل أنواع العقارات </h3>
                            <p class="text-xs text-slate-500 mt-0.5"> اضف نوع العقار ( سكني - تجاري - صناعي  ) مع متطلبات كل نوع ( صك - مساحة العقار )  </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2.5">
                            <input type="text" id="propTypeInstantSearch" oninput="filterPropTypesTable()"
                                   placeholder="🔍 ابحث باسم العقار أو التصنيف..."
                                   class="w-60 rounded-2xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-xs focus:bg-white focus:outline-none focus:border-emerald-600">

                            <button type="button" onclick="openModal('manageCategoriesModal')"
                                    class="px-3.5 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                                ⚙️ التصنيفات الرئيسية ({{ $categories->count() }})
                            </button>

                            <button type="button" onclick="openModal('manageFieldTypesModal')"
                                    class="px-3.5 py-2.5 rounded-2xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold border border-blue-200 transition">
                                ⚙️ أنواع الحقول ({{ $fieldTypes->count() }})
                            </button>

                            <button type="button" onclick="openModal('addPropertyTypeWithAttrsModal')"
                                    class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs shadow-lg shadow-emerald-600/20 flex items-center gap-2 transition">
                                <span>➕</span>
                                <span>إضافة نوع عقار  </span>
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-right" id="propTypesTable">
                            <thead class="bg-slate-50 text-slate-600 text-xs border-b border-slate-200/80">
                                <tr>
                                    <th class="py-4 px-5 w-14 text-center">#</th>
                                    <th class="py-4 px-5">نوع العقار</th>
                                    <th class="py-4 px-5 text-center">التصنيف</th>
                                    <th class="py-4 px-5 text-center">رقم الحساب </th>
                                    <th class="py-4 px-5 text-center">نظام الوحدات الفرعية</th>
                                    <th class="py-4 px-5 text-center">المتطلبات والحقول المربوطة</th>
                                    <th class="py-4 px-5 text-center">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($propertyTypes as $index => $type)
                                    @php
                                        $typeAttrs = $attributes->where('property_type_id', $type->id);
                                        $arabicCat = $catMap[$type->category] ?? $type->category;
                                    @endphp
                                    <tr class="hover:bg-slate-50/90 transition prop-type-row"
                                        data-search="{{ mb_strtolower($type->name . ' ' . $arabicCat . ' ' . ($type->account_code ?? '')) }}">
                                        <td class="py-4 px-5 text-center font-mono text-xs text-slate-400 font-bold">{{ $index + 1 }}</td>
                                        <td class="py-4 px-5 font-bold text-slate-900 text-sm">{{ $type->name }}</td>
                                        <td class="py-4 px-5 text-center">
                                            <span class="px-3 py-1 rounded-xl text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/60">{{ $arabicCat }}</span>
                                        </td>
                                        <td class="py-4 px-5 text-center">
                                            @if(!empty($type->account_code))
                                                <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 font-mono text-xs font-bold border border-slate-200">
                                                    {{ $type->account_code }} - {{ $type->account_name }}
                                                </span>
                                            @else
                                                <span class="text-xs text-slate-400">غير مرتبط</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-5 text-center">
                                            @if($type->has_units)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/60"> يتفرع لوحدات داخلية</span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs bg-slate-100 text-slate-600 font-medium">📍 عقار مستقل</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-5 text-center">
                                            <button type="button" onclick="openModal('attrsModal_{{ $type->id }}')"
                                                    class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl {{ $typeAttrs->count() > 0 ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-600 hover:text-white' : 'bg-slate-100 text-slate-600 border-slate-200' }} border text-xs font-bold transition shadow-sm">
                                                <span>📋 استعراض / إضافة</span>
                                                <span class="px-2 py-0.5 rounded-lg bg-white/90 text-slate-900 font-mono text-[11px]">{{ $typeAttrs->count() }}</span>
                                            </button>
                                        </td>
                                        <td class="py-4 px-5 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <button type="button" onclick="openModal('editPropertyTypeModal_{{ $type->id }}')"
                                                        class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-white text-xs font-semibold transition">تعديل</button>
                                                <form action="{{ route('erp.property_types.delete', $type->id) }}" method="POST" onsubmit="return confirm('حذف هذا العقار وجميع متطلباته؟');">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-red-50 hover:bg-red-600 text-red-600 hover:text-white border border-red-200 text-xs font-semibold transition">حذف</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="py-12 text-center text-slate-400">لا توجد عقارات مضافة بعد. اضغط على "إضافة نوع عقار " للبدء.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- نافذة موحدة: إضافة نوع العقار مع ربطه بحساب الشجرة ومتطلباته دفعة واحدة -->
                <div id="addPropertyTypeWithAttrsModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200 max-h-[92vh] overflow-y-auto">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                            <div>
                                <h3 class="font-bold text-base text-slate-900">➕ إضافة نوع عقار جديد مع حسابه ومتطلباته</h3>
                                <p class="text-xs text-slate-500 mt-0.5">حدد بيانات العقار ورقم حسابه في الشجرة والحقول المطلوبة</p>
                            </div>
                            <button type="button" onclick="closeModal('addPropertyTypeWithAttrsModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                        </div>

                        <form action="{{ route('erp.property_types.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">التصنيف الرئيسي *</label>
                                    <select name="category" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm bg-white font-semibold">
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">مسمى نوع العقار *</label>
                                    <input type="text" name="name" required placeholder="مثال: عمارة سكنية، أرض تجارية، فيلا..."
                                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm bg-white">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">🌳 رقم الحساب المرتبط بنوع العقار في شجرة الحسابات *</label>
                                    <select name="account_id" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs bg-white font-bold text-emerald-800">
                                        <option value="">-- اختر الحساب المرتبط بهذا النوع من العقارات --</option>
                                        @foreach($accounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="md:col-span-2 pt-1">
                                    <label class="flex items-center gap-2 text-xs font-bold text-emerald-800 cursor-pointer">
                                        <input type="checkbox" name="has_units" value="1" class="rounded text-emerald-600">
                                        <span> هذا العقار يتفرع منه وحدات داخلية (مثل العمارة تتفرع لشقق ومحلات)</span>
                                    </label>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-800">📋 المتطلبات والحقول المطلوبة لهذا العقار (اختياري):</span>
                                    <button type="button" onclick="addMoreAttributeRow()"
                                            class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-bold transition">
                                        + إضافة حقل / متطلب آخر
                                    </button>
                                </div>

                                <div id="dynamicAttributesContainer" class="space-y-2.5 max-h-60 overflow-y-auto pr-1">
                                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 grid grid-cols-12 gap-2.5 items-end attribute-row">
                                        <div class="col-span-7">
                                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">اسم المتطلب / الحقل</label>
                                            <input type="text" name="attribute_names[]" placeholder="مثال: عدد الأدوار، صورة الصك PDF..."
                                                   class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white">
                                        </div>
                                        <div class="col-span-5">
                                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">نوع الإدخال</label>
                                            <select name="field_types[]" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white">
                                                @foreach($fieldTypes as $ft)
                                                    <option value="{{ $ft->name }}">{{ $ft->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                                <button type="button" onclick="closeModal('addPropertyTypeWithAttrsModal')" class="px-5 py-2.5 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-lg shadow-emerald-600/20">
                                    حفظ نوع العقار ومتطلباته
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                @foreach($propertyTypes as $type)
                    @php
                        $typeAttrs = $attributes->where('property_type_id', $type->id);
                        $arabicCat = $catMap[$type->category] ?? $type->category;
                    @endphp
                    <div id="attrsModal_{{ $type->id }}" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] flex flex-col">
                            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                                <h3 class="font-bold text-base text-slate-900">متطلبات وحقول: <span class="text-emerald-600">{{ $type->name }}</span></h3>
                                <button type="button" onclick="closeModal('attrsModal_{{ $type->id }}')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                            </div>

                            <form action="{{ route('erp.property_attributes.store') }}" method="POST" class="mb-4 p-3.5 rounded-2xl bg-emerald-50/60 border border-emerald-200 grid grid-cols-12 gap-2 items-end">
                                @csrf
                                <input type="hidden" name="property_type_id" value="{{ $type->id }}">
                                <div class="col-span-6">
                                    <label class="block text-[11px] font-bold text-emerald-900 mb-1">إضافة متطلب جديد لهذا العقار *</label>
                                    <input type="text" name="attribute_names[]" required placeholder="اسم المتطلب الجديد..." class="w-full rounded-xl border border-slate-300 px-3 py-1.5 text-xs bg-white">
                                </div>
                                <div class="col-span-4">
                                    <label class="block text-[11px] font-bold text-emerald-900 mb-1">نوع الحقل *</label>
                                    <select name="field_types[]" class="w-full rounded-xl border border-slate-300 px-2.5 py-1.5 text-xs bg-white">
                                        @foreach($fieldTypes as $ft)
                                            <option value="{{ $ft->name }}">{{ $ft->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-2">
                                    <button type="submit" class="w-full py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold">+ إضافة</button>
                                </div>
                            </form>

                            <div class="overflow-y-auto flex-1 rounded-2xl border border-slate-200/80">
                                <table class="w-full text-sm text-right">
                                    <thead class="bg-slate-50 text-slate-600 text-xs border-b">
                                        <tr>
                                            <th class="py-3 px-4">#</th>
                                            <th class="py-3 px-4">اسم المتطلب</th>
                                            <th class="py-3 px-4">نوع الإدخال</th>
                                            <th class="py-3 px-4 text-center">إجراء</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($typeAttrs->values() as $attrIdx => $attr)
                                            @php $currentFieldType = $fieldTypeMap[$attr->field_type] ?? $attr->field_type; @endphp
                                            <tr>
                                                <td class="py-3 px-4 font-mono text-xs">{{ $attrIdx + 1 }}</td>
                                                <td class="py-3 px-4">
                                                    <input type="text" name="attribute_name" form="updateAttrForm_{{ $attr->id }}" value="{{ $attr->attribute_name }}" required class="w-full rounded-xl border px-3 py-1.5 text-xs font-bold">
                                                </td>
                                                <td class="py-3 px-4">
                                                    <select name="field_type" form="updateAttrForm_{{ $attr->id }}" class="w-full rounded-xl border px-3 py-1.5 text-xs bg-white">
                                                        @foreach($fieldTypes as $ft)
                                                            <option value="{{ $ft->name }}" {{ $currentFieldType == $ft->name ? 'selected' : '' }}>{{ $ft->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="py-3 px-4 text-center">
                                                    <div class="flex items-center justify-center gap-1.5">
                                                        <form id="updateAttrForm_{{ $attr->id }}" action="{{ route('erp.property_attributes.update', $attr->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 text-white text-xs font-semibold">حفظ</button>
                                                        </form>
                                                        <form action="{{ route('erp.property_attributes.delete', $attr->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-red-50 text-red-600 text-xs font-semibold">حذف</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="py-8 text-center text-slate-400 text-xs">لا توجد متطلبات مضافة لهذا العقار بعد.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="editPropertyTypeModal_{{ $type->id }}" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                            <div class="flex items-center justify-between pb-4 border-b mb-4">
                                <h3 class="font-bold text-base text-slate-900">تعديل: {{ $type->name }}</h3>
                                <button type="button" onclick="closeModal('editPropertyTypeModal_{{ $type->id }}')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                            </div>
                            <form action="{{ route('erp.property_types.update', $type->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">التصنيف الرئيسي *</label>
                                    <select name="category" required class="w-full rounded-xl border px-3.5 py-2.5 text-sm bg-white">
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->name }}" {{ $arabicCat == $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">مسمى نوع العقار *</label>
                                    <input type="text" name="name" value="{{ $type->name }}" required class="w-full rounded-xl border px-3.5 py-2.5 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">🌳 الحساب المرتبط في الشجرة *</label>
                                    <select name="account_id" class="w-full rounded-xl border px-3.5 py-2 text-xs bg-white font-bold">
                                        <option value="">-- اختر الحساب --</option>
                                        @foreach($accounts as $acc)
                                            <option value="{{ $acc->id }}" {{ ($type->account_id ?? '') == $acc->id ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <label class="flex items-center gap-2 text-xs font-medium text-slate-700 cursor-pointer">
                                    <input type="checkbox" name="has_units" value="1" {{ $type->has_units ? 'checked' : '' }} class="rounded text-emerald-600">
                                    <span>يتفرع منه وحدات داخلية</span>
                                </label>
                                <div class="flex justify-end gap-2 pt-3 border-t">
                                    <button type="button" onclick="closeModal('editPropertyTypeModal_{{ $type->id }}')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 text-white text-xs font-semibold">حفظ</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach

                <div id="manageCategoriesModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200">
                        <div class="flex items-center justify-between pb-4 border-b mb-4">
                            <h3 class="font-bold text-base text-slate-900">إدارة التصنيفات الرئيسية للعقارات</h3>
                            <button type="button" onclick="closeModal('manageCategoriesModal')" class="font-bold text-lg">✕</button>
                        </div>
                        <form action="{{ route('erp.property_categories.store') }}" method="POST" class="flex gap-2 mb-5">
                            @csrf
                            <input type="text" name="name" required placeholder="أدخل اسم تصنيف جديد..." class="flex-1 rounded-xl border px-3.5 py-2 text-sm">
                            <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">+ إضافة</button>
                        </form>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @foreach($categories as $cat)
                                <div class="flex items-center justify-between gap-2 p-2.5 rounded-xl bg-slate-50 border">
                                    <form action="{{ route('erp.property_categories.update', $cat->id) }}" method="POST" class="flex-1 flex gap-2">
                                        @csrf
                                        <input type="text" name="name" value="{{ $cat->name }}" required class="flex-1 rounded-lg border px-3 py-1.5 text-xs font-bold bg-white">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-900 text-white text-xs">تعديل</button>
                                    </form>
                                    <form action="{{ route('erp.property_categories.delete', $cat->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 text-red-600 text-xs">حذف</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div id="manageFieldTypesModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200">
                        <div class="flex items-center justify-between pb-4 border-b mb-4">
                            <h3 class="font-bold text-base text-slate-900">إدارة أنواع الحقول والمدخلات</h3>
                            <button type="button" onclick="closeModal('manageFieldTypesModal')" class="font-bold text-lg">✕</button>
                        </div>
                        <form action="{{ route('erp.field_types.store') }}" method="POST" class="grid grid-cols-12 gap-2 mb-5">
                            @csrf
                            <input type="text" name="name" required placeholder="مسمى نوع الحقل" class="col-span-6 rounded-xl border px-3 py-2 text-xs">
                            <select name="input_type" class="col-span-3 rounded-xl border px-2 py-2 text-xs bg-white">
                                <option value="text">نص</option>
                                <option value="number">رقم</option>
                                <option value="date">تاريخ</option>
                                <option value="boolean">نعم/لا</option>
                                <option value="file">مرفق PDF أو صورة (حد 5MB)</option>
                            </select>
                            <button type="submit" class="col-span-3 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">+ إضافة</button>
                        </form>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @foreach($fieldTypes as $ft)
                                <div class="flex items-center justify-between gap-2 p-2.5 rounded-xl bg-slate-50 border">
                                    <form action="{{ route('erp.field_types.update', $ft->id) }}" method="POST" class="flex-1 flex gap-2">
                                        @csrf
                                        <input type="text" name="name" value="{{ $ft->name }}" required class="flex-1 rounded-lg border px-3 py-1.5 text-xs font-bold bg-white">
                                        <input type="hidden" name="input_type" value="{{ $ft->input_type }}">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-900 text-white text-xs">تعديل</button>
                                    </form>
                                    <form action="{{ route('erp.field_types.delete', $ft->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 text-red-600 text-xs">حذف</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- 4. شجرة الحسابات  -->
            @if($tab == 'accounting')
                @php
                    $typeTranslations = [
                        'asset' => 'الأصول',
                        'liability' => 'الخصوم والالتزامات',
                        'equity' => 'حقوق الملكية',
                        'revenue' => 'الإيرادات',
                        'expense' => 'المصروفات التشغيلية',
                    ];
                    $leafAccounts = $accounts->where('is_group', 0);
                @endphp

                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-5 flex flex-wrap items-center justify-between gap-4 mb-6">
                    <div>
                        <h3 class="font-bold text-base text-slate-900">شجرة الحسابات </h3>
                        <p class="text-xs text-slate-500 mt-0.5">تضم حسابات الصندوق والبنوك وحسابات مكاتب العقار المتسلسلة تلقائياً</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <button type="button" onclick="openAccModal('manageAccountTypesModal')"
                                class="px-4 py-2 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition">
                            ⚙️ أنواع الحسابات ({{ $accountTypes->count() }})
                        </button>

                        <button type="button" onclick="openAddBranchesModal('', 'إضافة حساب / مجلد رئيسي جديد', '')"
                                class="px-4 py-2 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-lg shadow-emerald-600/20 transition">
                            + إضافة حساب رئيسي أو فروع
                        </button>

                        @if($accounts->count() == 0)
                            <form action="{{ route('erp.accounts.seed') }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 rounded-2xl bg-amber-500 hover:bg-amber-400 text-white font-bold text-xs shadow-lg transition">
                                    ⚡ توليد الشجرة القياسية
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                @if($accounts->count() == 0)
                    <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center space-y-4">
                        <div class="text-4xl">🌳</div>
                        <h4 class="font-bold text-slate-800 text-base">شجرة الحسابات فارغة</h4>
                        <form action="{{ route('erp.accounts.seed') }}" method="POST">
                            @csrf
                            <button type="submit" class="px-6 py-3 rounded-2xl bg-emerald-600 text-white font-bold text-xs">⚡ تأسيس الشجرة القياسية</button>
                        </form>
                    </div>
                @else
                    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-6">
                        @php
                            $rootNodes = $accounts->filter(fn($a) => empty($a->parent_id) || !$accounts->contains('id', $a->parent_id));
                        @endphp

                        @foreach($rootNodes as $root)
                            @php
                                $level1Children = $accounts->where('parent_id', $root->id);
                                $rootTypeAr = $typeTranslations[$root->type] ?? $root->type;
                            @endphp

                            <div class="border-2 border-slate-200 rounded-3xl p-5 bg-slate-50/50 space-y-3">
                                <div class="flex items-center justify-between flex-wrap gap-2 pb-3 border-b border-slate-200">
                                    <div class="flex items-center gap-3">
                                        <span class="px-3 py-1.5 rounded-xl bg-slate-900 text-white font-mono text-xs font-bold">{{ $root->code }}</span>
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-base">📁 {{ $root->name }}</h4>
                                            <span class="text-[11px] text-emerald-700 font-semibold">{{ $rootTypeAr }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <button type="button" onclick="openAddBranchesModal('{{ $root->id }}', '{{ addslashes($root->name) }}', '{{ addslashes($rootTypeAr) }}')"
                                                class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition">+ فرع جديد</button>
                                        <button type="button" onclick="openAccModal('editAccModal_{{ $root->id }}')" class="px-2.5 py-1.5 rounded-xl bg-white border text-xs">تعديل</button>
                                    </div>
                                </div>

                                <div class="pr-6 space-y-2.5 border-r-2 border-emerald-500/40 mr-2">
                                    @forelse($level1Children as $child)
                                        @php $level2Children = $accounts->where('parent_id', $child->id); @endphp
                                        <div class="bg-white rounded-2xl p-3 border border-slate-200 shadow-sm space-y-2">
                                            <div class="flex items-center justify-between flex-wrap gap-2">
                                                <div class="flex items-center gap-2.5">
                                                    <span class="text-slate-400 font-bold">├──</span>
                                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 font-mono text-xs font-bold text-slate-700">{{ $child->code }}</span>
                                                    <span class="font-bold text-slate-800 text-sm">{{ $child->is_group ? '📂 ' : '🔹 ' }}{{ $child->name }}</span>
                                                    <span class="text-xs font-mono font-bold text-emerald-600">({{ number_format($child->balance, 2) }} ر.س)</span>
                                                </div>

                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    @if(!$child->is_group)
                                                        <button type="button" onclick="openQuickVoucherModal('receipt', '{{ $child->id }}', '{{ addslashes($child->code . ' - ' . $child->name) }}')"
                                                                class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 text-[11px] font-bold transition"> قبض</button>
                                                        <button type="button" onclick="openQuickVoucherModal('payment', '{{ $child->id }}', '{{ addslashes($child->code . ' - ' . $child->name) }}')"
                                                                class="px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-600 text-amber-700 hover:text-white border border-amber-200 text-[11px] font-bold transition"> صرف</button>
                                                    @endif
                                                    <button type="button" onclick="openAddBranchesModal('{{ $child->id }}', '{{ addslashes($child->name) }}', '{{ addslashes($rootTypeAr) }}')"
                                                            class="px-2 py-1 rounded-lg bg-blue-50 text-blue-700 text-[11px] font-bold">+ فرع</button>
                                                    <button type="button" onclick="openAccModal('editAccModal_{{ $child->id }}')" class="px-2 py-1 rounded-lg bg-slate-100 text-xs">✏️</button>
                                                </div>
                                            </div>

                                            @if($level2Children->count() > 0)
                                                <div class="pr-6 space-y-1.5 border-r-2 border-emerald-300 mr-4 mt-2 pt-1">
                                                    @foreach($level2Children as $subChild)
                                                        <div class="flex items-center justify-between gap-2 bg-slate-50 p-2 rounded-xl border border-slate-100">
                                                            <div class="flex items-center gap-2">
                                                                <span class="text-slate-300">└──</span>
                                                                <span class="px-2 py-0.5 rounded bg-white border font-mono text-[11px] font-bold text-slate-600">{{ $subChild->code }}</span>
                                                                <span class="text-xs font-semibold text-slate-800">{{ $subChild->name }}</span>
                                                                <span class="text-[11px] font-mono text-emerald-600 font-bold">({{ number_format($subChild->balance, 2) }} ر.س)</span>
                                                            </div>
                                                            <div class="flex items-center gap-1">
                                                                <button type="button" onclick="openQuickVoucherModal('receipt', '{{ $subChild->id }}', '{{ addslashes($subChild->code . ' - ' . $subChild->name) }}')"
                                                                        class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[10px] font-bold">قبض</button>
                                                                <button type="button" onclick="openQuickVoucherModal('payment', '{{ $subChild->id }}', '{{ addslashes($subChild->code . ' - ' . $subChild->name) }}')"
                                                                        class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 text-[10px] font-bold">صرف</button>
                                                                <button type="button" onclick="openAccModal('editAccModal_{{ $subChild->id }}')" class="px-1.5 py-0.5 rounded bg-white text-[10px]">✏️</button>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="py-3 text-xs text-slate-400">لا توجد فروع مضافة تحت هذا الجذع بعد.</div>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div id="addBranchesModal" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-slate-200">
                        <div class="flex items-center justify-between pb-4 border-b mb-4">
                            <h3 id="addBranchesTitle" class="font-bold text-base text-slate-900">إضافة حسابات وفروع</h3>
                            <button type="button" onclick="closeAccModal('addBranchesModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                        </div>
                        <form action="{{ route('erp.accounts.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">الحساب الأب</label>
                                    <select name="parent_id" id="branch_parent_id" class="w-full rounded-xl border px-3 py-2 text-xs bg-white">
                                        <option value="">-- جذع رئيسي جديد --</option>
                                        @foreach($accounts as $accItem)
                                            <option value="{{ $accItem->id }}">{{ $accItem->code }} - {{ $accItem->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">نوع الحساب *</label>
                                    <select name="type" id="branch_acc_type" class="w-full rounded-xl border px-3 py-2 text-xs bg-white">
                                        @foreach($accountTypes as $at)
                                            <option value="{{ $at->name }}">{{ $at->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div id="multiBranchesContainer" class="space-y-2.5 max-h-64 overflow-y-auto">
                                <div class="p-3 rounded-2xl bg-slate-50 border grid grid-cols-12 gap-2 items-center branch-row">
                                    <div class="col-span-6">
                                        <input type="text" name="names[]" required placeholder="اسم الحساب..." class="w-full rounded-xl border px-3 py-1.5 text-xs bg-white">
                                    </div>
                                    <div class="col-span-3">
                                        <input type="text" name="codes[]" placeholder="الكود تلقائي" class="w-full rounded-xl border px-2.5 py-1.5 text-xs font-mono bg-white">
                                    </div>
                                    <div class="col-span-3">
                                        <select name="is_groups[]" class="w-full rounded-xl border px-2 py-1.5 text-xs bg-white">
                                            <option value="0">حساب فرعي</option>
                                            <option value="1">مجلد رئيسي</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="button" onclick="addAnotherBranchRow()" class="w-full py-2 rounded-xl border-2 border-dashed border-emerald-500 text-emerald-700 text-xs font-bold">+ إضافة فرع آخر</button>
                            <div class="flex justify-end gap-2 pt-3 border-t">
                                <button type="button" onclick="closeAccModal('addBranchesModal')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">حفظ</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="quickVoucherModal" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200">
                        <div class="flex items-center justify-between pb-4 border-b mb-4">
                            <div>
                                <h3 id="qv_title" class="font-bold text-base text-slate-900">إصدار سند مالي</h3>
                                <p id="qv_subtitle" class="text-xs text-emerald-600 font-bold mt-0.5"></p>
                            </div>
                            <button type="button" onclick="closeAccModal('quickVoucherModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                        </div>
                        <form action="{{ route('erp.vouchers.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="stay_in_accounting" value="1">
                            <input type="hidden" name="voucher_type" id="qv_type" value="receipt">
                            <div class="grid grid-cols-2 gap-3 p-3.5 rounded-2xl bg-slate-50 border">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">من حساب (المدين) *</label>
                                    <select name="debit_account_id" id="qv_debit" required class="w-full rounded-xl border px-3 py-2 text-xs bg-white">
                                        @foreach($leafAccounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">إلى حساب (الدائن) *</label>
                                    <select name="credit_account_id" id="qv_credit" required class="w-full rounded-xl border px-3 py-2 text-xs bg-white">
                                        @foreach($leafAccounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">المبلغ (ر.س) *</label>
                                    <input type="number" step="0.01" name="amount" required class="w-full rounded-xl border px-3.5 py-2 text-sm font-bold">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">طريقة الدفع *</label>
                                    <select name="payment_method" class="w-full rounded-xl border px-3.5 py-2 text-sm bg-white">
                                        <option value="bank_transfer">حوالة بنكية</option>
                                        <option value="sadad">سداد</option>
                                        <option value="mada">مدى</option>
                                        <option value="cash">نقدي</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">البيان *</label>
                                <input type="text" name="notes" required class="w-full rounded-xl border px-3.5 py-2 text-sm">
                            </div>
                            <div class="flex justify-end gap-2 pt-3 border-t">
                                <button type="button" onclick="closeAccModal('quickVoucherModal')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">اعتماد السند</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- نوافذ تعديل الحسابات مع تعديل المدين والدائن مباشرة -->
                @foreach($accounts as $acc)
                    @php $accTypeAr = $typeTranslations[$acc->type] ?? $acc->type; @endphp
                    <div id="editAccModal_{{ $acc->id }}" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                                <h3 class="font-bold text-base text-slate-900">تعديل الحساب: {{ $acc->name }}</h3>
                                <button type="button" onclick="closeAccModal('editAccModal_{{ $acc->id }}')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                            </div>
                            <form action="{{ route('erp.accounts.update', $acc->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <div class="grid grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">رقم الحساب *</label>
                                        <input type="text" name="code" value="{{ $acc->code }}" required class="w-full rounded-xl border px-3 py-2 text-sm font-mono">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">اسم الحساب *</label>
                                        <input type="text" name="name" value="{{ $acc->name }}" required class="w-full rounded-xl border px-3 py-2 text-sm font-bold">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">نوع الحساب *</label>
                                        <select name="type" class="w-full rounded-xl border px-3 py-2 text-xs bg-white">
                                            @foreach($accountTypes as $at)
                                                <option value="{{ $at->name }}" {{ $accTypeAr == $at->name ? 'selected' : '' }}>{{ $at->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">الحساب الأب</label>
                                        <select name="parent_id" class="w-full rounded-xl border px-3 py-2 text-xs bg-white">
                                            <option value="">-- جذع رئيسي --</option>
                                            @foreach($accounts->where('id', '!=', $acc->id) as $pAcc)
                                                <option value="{{ $pAcc->id }}" {{ $acc->parent_id == $pAcc->id ? 'selected' : '' }}>{{ $pAcc->code }} - {{ $pAcc->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-emerald-700 mb-1">إجمالي المدين (Debit)</label>
                                        <input type="number" step="0.01" name="debit_balance" value="{{ $acc->debit_balance ?? ($acc->balance > 0 ? $acc->balance : 0) }}"
                                               class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-mono font-bold text-emerald-700 bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-amber-700 mb-1">إجمالي الدائن (Credit)</label>
                                        <input type="number" step="0.01" name="credit_balance" value="{{ $acc->credit_balance ?? ($acc->balance < 0 ? abs($acc->balance) : 0) }}"
                                               class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-mono font-bold text-amber-700 bg-white">
                                    </div>
                                </div>

                                <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                                    <input type="checkbox" name="is_group" value="1" {{ $acc->is_group ? 'checked' : '' }} class="rounded text-emerald-600">
                                    <span>مجلد رئيسي يتفرع منه حسابات</span>
                                </label>
                                <div class="flex justify-end gap-2 pt-3 border-t">
                                    <button type="button" onclick="closeAccModal('editAccModal_{{ $acc->id }}')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">حفظ التعديلات</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            @endif

            <!-- 5. سندات القبض والصرف -->
            @if($tab == 'vouchers')
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden no-print">
                    <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="font-bold text-lg text-slate-900">سجل سندات القبض والصرف الرسمية</h3>
                            <p class="text-xs text-slate-500 mt-0.5">تسلسل منفصل لسندات الصرف (<span class="font-mono font-bold text-amber-700">SA-{{ date('Y') }}-000001</span>) وسندات القبض (<span class="font-mono font-bold text-emerald-700">RV-{{ date('Y') }}-000001</span>)</p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2.5">
                            <input type="text" id="voucherInstantSearch" oninput="filterVouchersTable()"
                                   placeholder="🔍 ابحث برقم السند أو المكتب أو البيان..."
                                   class="w-60 rounded-2xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-xs focus:bg-white focus:outline-none focus:border-emerald-600">

                            <button type="button" onclick="openModal('managePaymentMethodsModal')"
                                    class="px-4 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition">
                                💳 طرق الدفع ({{ $paymentMethods->count() }})
                            </button>

                            <button type="button" onclick="openModal('addVoucherModal')"
                                    class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs shadow-lg shadow-emerald-600/20 flex items-center gap-2 transition">
                                <span>➕</span>
                                <span>إصدار سند قبض / صرف جديد</span>
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-right" id="vouchersTable">
                            <thead class="bg-slate-50 text-slate-600 text-xs border-b border-slate-200/60">
                                <tr>
                                    <th class="py-4 px-4">رقم السند</th>
                                    <th class="py-4 px-3">النوع</th>
                                    <th class="py-4 px-4">المكتب المرتبط</th>
                                    <th class="py-4 px-4">القيد المحاسبي (بالمنصة)</th>
                                    <th class="py-4 px-4">المبلغ</th>
                                    <th class="py-4 px-3 text-center">طريقة الدفع</th>
                                    <th class="py-4 px-4">البيان والتاريخ</th>
                                    <th class="py-4 px-4 text-center">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($vouchers as $v)
                                    @php
                                        $pmArabic = \App\Http\Controllers\CompanyErpController::translatePaymentMethod($v->payment_method);
                                    @endphp
                                    <tr class="hover:bg-slate-50/90 transition voucher-row"
                                        data-search="{{ mb_strtolower($v->voucher_number . ' ' . ($v->office_name ?? '') . ' ' . $v->notes . ' ' . $v->amount . ' ' . $pmArabic) }}">
                                        <td class="py-4 px-4 font-mono font-bold text-slate-900 text-xs">{{ $v->voucher_number }}</td>
                                        <td class="py-4 px-3">
                                            @if($v->voucher_type == 'receipt')
                                                <span class="px-2.5 py-1 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"> قبض</span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-xl text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200"> صرف</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 font-bold text-slate-800 text-xs">{{ $v->office_name ?? ' الادارة ' }}</td>
                                        <td class="py-4 px-4 text-[11px] text-slate-500">
                                            <div>من: <b class="text-slate-700">{{ $v->debit_code }} - {{ $v->debit_name }}</b></div>
                                            <div>إلى: <b class="text-slate-700">{{ $v->credit_code }} - {{ $v->credit_name }}</b></div>
                                        </td>
                                        <td class="py-4 px-4 font-bold text-sm {{ $v->voucher_type == 'receipt' ? 'text-emerald-600' : 'text-amber-700' }}">
                                            {{ number_format($v->amount, 2) }} ر.س
                                        </td>
                                        <td class="py-4 px-3 text-center">
                                            <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 text-xs font-bold">{{ $pmArabic }}</span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="text-xs font-semibold text-slate-800">{{ $v->notes }}</div>
                                            <div class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $v->voucher_date }}</div>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                                <button type="button"
                                                        onclick="openOfficialVoucherPreview({
                                                            number: '{{ $v->voucher_number }}',
                                                            type: '{{ $v->voucher_type }}',
                                                            amount: '{{ number_format($v->amount, 2) }}',
                                                            date: '{{ $v->voucher_date }}',
                                                            party: '{{ addslashes($v->office_name ?? 'الشركة الأم') }}',
                                                            phone: '{{ $v->office_phone ?? '' }}',
                                                            payment_method: '{{ addslashes($pmArabic) }}',
                                                            notes: '{{ addslashes($v->notes) }}',
                                                            issuer: 'الإدارة المركزية - منظومة العقارات'
                                                        })"
                                                        class="px-2.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-white text-xs font-bold transition">
                                                    📄 PDF / طباعة
                                                </button>

                                                <form action="{{ route('erp.vouchers.send_whatsapp', $v->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 text-xs font-bold transition">
                                                        💬 واتساب
                                                    </button>
                                                </form>

                                                <button type="button" onclick="openModal('editVoucherModal_{{ $v->id }}')"
                                                        class="px-2 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-600 text-blue-700 hover:text-white text-xs font-bold transition">
                                                    تعديل
                                                </button>

                                                <form action="{{ route('erp.vouchers.delete', $v->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف السند وعكس قيده المحاسبي؟');">
                                                    @csrf
                                                    <button type="submit" class="px-2 py-1.5 rounded-xl bg-red-50 hover:bg-red-600 text-red-600 hover:text-white text-xs font-bold transition">
                                                        حذف
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="py-10 text-center text-slate-400">لا توجد سندات مصدرة بعد. اضغط على "إصدار سند قبض / صرف جديد".</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            <!-- نافذة إدارة طرق الدفع وربطها برقم حساب في الشجرة -->
                <div id="managePaymentMethodsModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4 no-print">
                    <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200">
                        <div class="flex items-center justify-between pb-4 border-b mb-4">
                            <div>
                                <h3 class="font-bold text-base text-slate-900">💳 إدارة طرق الدفع وربطها بأرقام الحسابات</h3>
                                <p class="text-xs text-slate-500">اربط كل طريقة دفع بحسابها في الشجرة ليتم تحديد الحساب تلقائياً عند إصدار السند</p>
                            </div>
                            <button type="button" onclick="closeModal('managePaymentMethodsModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                        </div>
                        <form action="{{ route('erp.payment_methods.store') }}" method="POST" class="grid grid-cols-12 gap-2 mb-5 p-3.5 rounded-2xl bg-slate-50 border">
                            @csrf
                            <div class="col-span-5">
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">اسم طريقة الدفع *</label>
                                <input type="text" name="name" required placeholder="مثال: حوالة الراجحي، شبكة مدى..." class="w-full rounded-xl border px-3 py-2 text-xs bg-white">
                            </div>
                            <div class="col-span-5">
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">رقم الحساب المرتبط في الشجرة *</label>
                                <select name="account_id" required class="w-full rounded-xl border px-2.5 py-2 text-xs bg-white font-semibold">
                                    <option value="">-- اختر حساب الصندوق/البنك --</option>
                                    @foreach($accounts->where('is_group', 0) as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-2 flex items-end">
                                <button type="submit" class="w-full py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">+ إضافة</button>
                            </div>
                        </form>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @foreach($paymentMethods as $pm)
                                <div class="flex items-center justify-between gap-2 p-2.5 rounded-xl bg-slate-50 border">
                                    <form action="{{ route('erp.payment_methods.update', $pm->id) }}" method="POST" class="flex-1 grid grid-cols-12 gap-2 items-center">
                                        @csrf
                                        <input type="text" name="name" value="{{ $pm->name }}" required class="col-span-5 rounded-lg border px-3 py-1.5 text-xs font-bold bg-white">
                                        <select name="account_id" class="col-span-5 rounded-lg border px-2 py-1.5 text-xs bg-white font-mono">
                                            <option value="">-- بدون حساب مرتبط --</option>
                                            @foreach($accounts->where('is_group', 0) as $acc)
                                                <option value="{{ $acc->id }}" {{ ($pm->account_id ?? '') == $acc->id ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="col-span-2 py-1.5 rounded-lg bg-slate-900 text-white text-xs">تعديل</button>
                                    </form>
                                    <form action="{{ route('erp.payment_methods.delete', $pm->id) }}" method="POST" onsubmit="return confirm('حذف طريقة الدفع؟');">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 text-red-600 text-xs">حذف</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- نافذة إضافة سند قبض / صرف جديد (مع ظهور حساب المكتب وحساب طريقة الدفع تلقائياً) -->
                <div id="addVoucherModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4 no-print">
                    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200">
                        <div class="flex items-center justify-between pb-4 border-b mb-4">
                            <div>
                                <h3 class="font-bold text-base text-slate-900">إصدار سند قبض / صرف رسمي جديد</h3>
                                <p class="text-xs text-emerald-600">اختيار المكتب وطريقة الدفع يحدد أرقام الحسابات تلقائياً</p>
                            </div>
                            <button type="button" onclick="closeModal('addVoucherModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                        </div>
                        <form action="{{ route('erp.vouchers.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">نوع السند *</label>
                                    <select name="voucher_type" id="auto_voucher_type" onchange="syncAutoVoucherAccounts()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white font-bold">
                                        <option value="receipt"> سند قبض (RV)</option>
                                        <option value="payment"> سند صرف (SA)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">المكتب المرتبط (يحدد حسابه تلقائياً)</label>
                                    <select name="office_id" id="auto_voucher_office" onchange="syncAutoVoucherAccounts()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white font-semibold">
                                        <option value="" data-account="">-- عام للشركة --</option>
                                        @foreach($offices as $off)
                                            <option value="{{ $off->id }}" data-account="{{ $off->account_id ?? '' }}">{{ $off->name }} ({{ $off->account_code ?? 'بدون حساب' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">المبلغ (ر.س) *</label>
                                    <input type="number" step="0.01" name="amount" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-bold text-emerald-700">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">طريقة الدفع *</label>
                                    <select name="payment_method" id="auto_voucher_pm" onchange="syncAutoVoucherAccounts()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-semibold">
                                        @foreach($paymentMethods as $pm)
                                            <option value="{{ $pm->name }}" data-account="{{ $pm->account_id ?? '' }}">
                                                {{ $pm->name }} {{ $pm->account_code ? '(' . $pm->account_code . ')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">التاريخ *</label>
                                    <input type="date" name="voucher_date" value="{{ date('Y-m-d') }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 p-3.5 rounded-2xl bg-emerald-50/60 border border-emerald-200">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">من حساب (المدين - تلقائي) *</label>
                                    <select name="debit_account_id" id="auto_voucher_debit" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-bold">
                                        @foreach($accounts->where('is_group', 0) as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">إلى حساب (الدائن - تلقائي) *</label>
                                    <select name="credit_account_id" id="auto_voucher_credit" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-bold">
                                        @foreach($accounts->where('is_group', 0) as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">البيان (وذلك عن...) *</label>
                                <input type="text" name="notes" required placeholder="تفاصيل السند..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm">
                            </div>
                            <div class="flex justify-end gap-2 pt-3 border-t">
                                <button type="button" onclick="closeModal('addVoucherModal')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg">إصدار السند وترحيل القيد</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- نوافذ تعديل السندات -->
                @foreach($vouchers as $v)
                    @php $vMethodAr = \App\Http\Controllers\CompanyErpController::translatePaymentMethod($v->payment_method); @endphp
                    <div id="editVoucherModal_{{ $v->id }}" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4 no-print">
                        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200">
                            <div class="flex items-center justify-between pb-4 border-b mb-4">
                                <h3 class="font-bold text-base text-slate-900">تعديل السند: <span class="font-mono text-emerald-700">{{ $v->voucher_number }}</span></h3>
                                <button type="button" onclick="closeModal('editVoucherModal_{{ $v->id }}')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                            </div>
                            <form action="{{ route('erp.vouchers.update', $v->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">المكتب المرتبط</label>
                                        <select name="office_id" class="w-full rounded-xl border px-3 py-2 text-xs bg-white">
                                            <option value="">-- عام للشركة --</option>
                                            @foreach($offices as $off)
                                                <option value="{{ $off->id }}" {{ $v->office_id == $off->id ? 'selected' : '' }}>{{ $off->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">المبلغ (ر.س) *</label>
                                        <input type="number" step="0.01" name="amount" value="{{ $v->amount }}" required class="w-full rounded-xl border px-3 py-2 text-sm font-bold">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">تاريخ السند *</label>
                                        <input type="date" name="voucher_date" value="{{ $v->voucher_date }}" required class="w-full rounded-xl border px-3 py-2 text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">طريقة الدفع</label>
                                        <select name="payment_method" class="w-full rounded-xl border px-3 py-2 text-xs bg-white font-semibold">
                                            @foreach($paymentMethods as $pm)
                                                <option value="{{ $pm->name }}" {{ ($vMethodAr == $pm->name || $v->payment_method == $pm->code) ? 'selected' : '' }}>{{ $pm->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">البيان *</label>
                                    <input type="text" name="notes" value="{{ $v->notes }}" required class="w-full rounded-xl border px-3.5 py-2 text-sm">
                                </div>
                                <div class="flex justify-end gap-2 pt-3 border-t">
                                    <button type="button" onclick="closeModal('editVoucherModal_{{ $v->id }}')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                    <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">حفظ التعديلات وتحديث الرصيد</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach

                <!-- نافذة المعاينة الرسمية للسند وتصدير PDF (بدون حساب مدين ودائن) -->
                <div id="officialVoucherModal" class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200">
                        <div class="flex items-center justify-between pb-4 border-b mb-4 no-print">
                            <h3 class="font-bold text-base text-slate-900">معاينة السند المالي الرسمي</h3>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="downloadVoucherPDF()" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow">
                                     تحميل PDF
                                </button>
                                <button type="button" onclick="window.print()" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-700 text-white text-xs font-bold">
                                    🖨️ طباعة
                                </button>
                                <button type="button" onclick="closeModal('officialVoucherModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                            </div>
                        </div>

                        <!-- قالب السند الرسمي للطباعة والـ PDF (تم حذف الحساب المدين والدائن منه) -->
                        <div id="printableVoucherArea" class="p-8 rounded-3xl border-2 border-slate-800 bg-white space-y-6 text-slate-900">
                            <div class="flex items-center justify-between border-b-2 border-slate-800 pb-5">
                                <div>
                                    <h2 id="pdf_issuer" class="text-xl font-bold text-slate-900">الإدارة المركزية - منظومة العقارات</h2>
                                    <p class="text-xs text-slate-500 mt-1">سند مالي رسمي معتمد إلكترونياً</p>
                                </div>
                                <div class="text-center px-6 py-2.5 rounded-2xl border-2 border-slate-900 bg-slate-50">
                                    <div id="pdf_voucher_title" class="text-lg font-extrabold text-emerald-800">سند قبض</div>
                                    <div id="pdf_voucher_no" class="font-mono text-xs font-bold text-slate-700 mt-0.5">RV-2026-000001</div>
                                </div>
                                <div class="text-left text-xs space-y-1">
                                    <div>التاريخ: <b id="pdf_date" class="font-mono">2026-01-01</b></div>
                                    <div>طريقة الدفع: <b id="pdf_method" class="text-emerald-800">حوالة بنكية</b></div>
                                </div>
                            </div>

                            <div class="grid grid-cols-12 gap-4 items-center bg-slate-50 p-4 rounded-2xl border border-slate-200">
                                <div class="col-span-4 text-xs font-bold text-slate-600">المبلغ المعتمد بالريال السعودي:</div>
                                <div class="col-span-8 text-2xl font-extrabold text-emerald-700 font-mono"><span id="pdf_amount">0.00</span> ر.س</div>
                            </div>

                            <div class="space-y-3 text-sm">
                                <div class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200">
                                    <span class="text-slate-500 font-semibold">الطرف المرتبط (استلمنا من / صرفنا إلى):</span>
                                    <span id="pdf_party" class="font-bold text-slate-900"></span>
                                </div>
                                <div class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200">
                                    <span class="text-slate-500 font-semibold">وذلك عن (البيان والتفاصيل):</span>
                                    <span id="pdf_notes" class="font-bold text-slate-900"></span>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-4 pt-6 border-t border-dashed border-slate-300 text-center text-xs text-slate-500">
                                <div>
                                    <p class="font-bold text-slate-700 mb-6">إعداد المحاسب</p>
                                    <p>............................</p>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-700 mb-6">المستلم</p>
                                    <p>............................</p>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-700 mb-6">اعتماد الإدارة والختم</p>
                                    <p class="text-emerald-700 font-bold">معتمد إلكترونياً ✔</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- 5-ب. صفحة القيود اليومية (Journal Entries) -->
            @if($tab == 'journal')
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden no-print">
                    <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="font-bold text-lg text-slate-900"> سجل القيود اليومية </h3>
                        </div>

                        <div class="flex flex-wrap items-center gap-2.5">
                            <input type="text" id="journalInstantSearch" oninput="filterJournalTable()"
                                   placeholder="🔍 ابحث برقم القيد أو الحساب أو البيان..."
                                   class="w-64 rounded-2xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-xs focus:bg-white focus:outline-none focus:border-emerald-600">

                            <button type="button" onclick="openModal('addJournalEntryModal')"
                                    class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs shadow-lg shadow-emerald-600/20 flex items-center gap-2 transition">
                                <span>➕</span>
                                <span>إضافة قيد يومي جديد</span>
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-right" id="journalEntriesTable">
                            <thead class="bg-slate-50 text-slate-600 text-xs border-b border-slate-200/80">
                                <tr>
                                    <th class="py-4 px-4 text-center">رقم القيد</th>
                                    <th class="py-4 px-4">رقم الحساب (المدين)</th>
                                    <th class="py-4 px-4">حساب الدائن</th>
                                    <th class="py-4 px-4">المبلغ</th>
                                    <th class="py-4 px-4">الضريبة (إن وجدت)</th>
                                    <th class="py-4 px-4">البيان العام والتاريخ</th>
                                    <th class="py-4 px-4 text-center">الإجراءات </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($journalEntries as $je)
                                    @php
                                        $items = $journalItemsByEntry[$je->id] ?? collect();
                                        $debitAccountsList = $items->where('debit', '>', 0)->map(fn($i) => $i->account_code . ' - ' . $i->account_name)->implode(' ، ');
                                        $creditAccountsList = $items->where('credit', '>', 0)->map(fn($i) => $i->account_code . ' - ' . $i->account_name)->implode(' ، ');
                                        $entryAmount = max($je->total_debit, $je->total_credit);
                                    @endphp
                                    <tr class="hover:bg-slate-50/90 transition journal-row"
                                        data-search="{{ mb_strtolower($je->entry_number . ' ' . $debitAccountsList . ' ' . $creditAccountsList . ' ' . $je->general_notes) }}">
                                        <td class="py-4 px-4 text-center font-mono font-extrabold text-slate-900">{{ $je->entry_number }}</td>
                                        <td class="py-4 px-4 text-xs font-bold text-emerald-700">{{ $debitAccountsList ?: '-' }}</td>
                                        <td class="py-4 px-4 text-xs font-bold text-amber-700">{{ $creditAccountsList ?: '-' }}</td>
                                        <td class="py-4 px-4 font-mono font-bold text-slate-900">{{ number_format($entryAmount, 2) }} ر.س</td>
                                        <td class="py-4 px-4 font-mono text-xs text-slate-600">{{ $je->total_tax > 0 ? number_format($je->total_tax, 2) . ' ر.س' : '0.00' }}</td>
                                        <td class="py-4 px-4">
                                            <div class="text-xs font-bold text-slate-800">{{ $je->general_notes }}</div>
                                            <div class="text-[11px] font-mono text-slate-400">{{ $je->entry_date }}</div>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                                <button type="button" onclick="openModal('viewJournalModal_{{ $je->id }}')"
                                                        class="px-2.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 text-xs font-bold transition">
                                                     عرض ({{ $items->count() }})
                                                </button>

                                                <button type="button" onclick="openModal('printChoiceJournalModal_{{ $je->id }}')"
                                                        class="px-2.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-white text-xs font-bold transition">
                                                     طباعة
                                                </button>

                                                <button type="button" onclick="openModal('editJournalModal_{{ $je->id }}')"
                                                        class="px-2.5 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-600 text-blue-700 hover:text-white text-xs font-bold transition">
                                                     تعديل
                                                </button>

                                                <form action="{{ route('erp.journal.delete', $je->id) }}" method="POST" onsubmit="return confirm('حذف هذا القيد وعكس أثره من شجرة الحسابات؟');">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-red-50 hover:bg-red-600 text-red-600 hover:text-white text-xs font-bold transition">
                                                        🗑️ حذف
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="py-12 text-center text-slate-400">لا توجد قيود يومية مسجلة بعد. اضغط على "إضافة قيد يومي جديد" للبدء.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- نافذة إضافة قيد يومي متعدد الجداول (مع زر حذف لأي خانة إضافية) -->
                <div id="addJournalEntryModal" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4 no-print">
                    <div class="bg-white rounded-3xl max-w-4xl w-full p-6 shadow-2xl border border-slate-200 max-h-[92vh] overflow-y-auto">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                            <div>
                                <h3 class="font-bold text-base text-slate-900">➕ إضافة قيد يومي محاسبي متعدد العمليات</h3>
                                <p class="text-xs text-slate-500">أدخل البيان العام بالأعلى ثم أضف العمليات في الجداول بالأسفل، ويمكنك حذف أي خانة إضافية لا تحتاجها</p>
                            </div>
                            <button type="button" onclick="closeModal('addJournalEntryModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                        </div>

                        <form action="{{ route('erp.journal.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200">
                                <div class="md:col-span-9">
                                    <label class="block text-xs font-bold text-emerald-950 mb-1">البيان العام للقيد اليومي *</label>
                                    <input type="text" name="general_notes" required placeholder="مثال: قيد إثبات مصروفات تشغيلية أو تسوية حسابات..."
                                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm bg-white font-semibold">
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-xs font-bold text-emerald-950 mb-1">تاريخ القيد *</label>
                                    <input type="date" name="entry_date" value="{{ date('Y-m-d') }}" required
                                           class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white font-mono">
                                </div>
                            </div>

                            <div id="journalTablesContainer" class="space-y-3">
                                <!-- الجدول الأول الأساسي -->
                                <div class="p-4 rounded-2xl bg-slate-50 border-2 border-slate-200 journal-table-block">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-bold text-slate-700 journal-block-title">جدول العملية (1) — الطرف الأول</span>
                                        <button type="button" onclick="removeJournalTableBlock(this)" class="hidden remove-journal-row-btn px-2.5 py-1 rounded-lg bg-red-50 hover:bg-red-600 text-red-600 hover:text-white border border-red-200 text-[11px] font-bold transition">
                                            🗑️ حذف هذه الخانة
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2.5 items-end">
                                        <div class="md:col-span-4">
                                            <label class="block text-[11px] font-bold text-slate-600 mb-1">رقم الحساب *</label>
                                            <select name="account_ids[]" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-semibold">
                                                <option value="">-- اختر الحساب من الشجرة --</option>
                                                @foreach($accounts->where('is_group', 0) as $acc)
                                                    <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-[11px] font-bold text-emerald-700 mb-1">المبلغ المدين</label>
                                            <input type="number" step="0.01" name="debits[]" value="500" required class="w-full rounded-xl border border-slate-300 px-2.5 py-2 text-xs bg-white font-bold text-emerald-700">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-[11px] font-bold text-amber-700 mb-1">المبلغ الدائن</label>
                                            <input type="number" step="0.01" name="credits[]" value="0" required class="w-full rounded-xl border border-slate-300 px-2.5 py-2 text-xs bg-white font-bold text-amber-700">
                                        </div>
                                        <div class="md:col-span-1">
                                            <label class="block text-[11px] font-bold text-slate-600 mb-1">الضريبة</label>
                                            <input type="number" step="0.01" name="taxes[]" value="0" class="w-full rounded-xl border border-slate-300 px-2 py-2 text-xs bg-white">
                                        </div>
                                        <div class="md:col-span-3">
                                            <label class="block text-[11px] font-bold text-slate-600 mb-1">البيان التفصيلي</label>
                                            <input type="text" name="item_notes[]" placeholder="بيان تفصيلي للعملية..." class="w-full rounded-xl border border-slate-300 px-2.5 py-2 text-xs bg-white">
                                        </div>
                                    </div>
                                </div>

                                <!-- الجدول الثاني الأساسي -->
                                <div class="p-4 rounded-2xl bg-slate-50 border-2 border-slate-200 journal-table-block">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-bold text-slate-700 journal-block-title">جدول العملية (2) — الطرف المقابل</span>
                                        <button type="button" onclick="removeJournalTableBlock(this)" class="hidden remove-journal-row-btn px-2.5 py-1 rounded-lg bg-red-50 hover:bg-red-600 text-red-600 hover:text-white border border-red-200 text-[11px] font-bold transition">
                                            🗑️ حذف هذه الخانة
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2.5 items-end">
                                        <div class="md:col-span-4">
                                            <label class="block text-[11px] font-bold text-slate-600 mb-1">رقم الحساب *</label>
                                            <select name="account_ids[]" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-semibold">
                                                <option value="">-- اختر الحساب من الشجرة --</option>
                                                @foreach($accounts->where('is_group', 0) as $acc)
                                                    <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-[11px] font-bold text-emerald-700 mb-1">المبلغ المدين</label>
                                            <input type="number" step="0.01" name="debits[]" value="0" required class="w-full rounded-xl border border-slate-300 px-2.5 py-2 text-xs bg-white font-bold text-emerald-700">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-[11px] font-bold text-amber-700 mb-1">المبلغ الدائن</label>
                                            <input type="number" step="0.01" name="credits[]" value="500" required class="w-full rounded-xl border border-slate-300 px-2.5 py-2 text-xs bg-white font-bold text-amber-700">
                                        </div>
                                        <div class="md:col-span-1">
                                            <label class="block text-[11px] font-bold text-slate-600 mb-1">الضريبة</label>
                                            <input type="number" step="0.01" name="taxes[]" value="0" class="w-full rounded-xl border border-slate-300 px-2 py-2 text-xs bg-white">
                                        </div>
                                        <div class="md:col-span-3">
                                            <label class="block text-[11px] font-bold text-slate-600 mb-1">البيان التفصيلي</label>
                                            <input type="text" name="item_notes[]" placeholder="بيان تفصيلي للعملية..." class="w-full rounded-xl border border-slate-300 px-2.5 py-2 text-xs bg-white">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" onclick="addMoreJournalTableBlock()"
                                    class="w-full py-2.5 rounded-2xl border-2 border-dashed border-emerald-500 text-emerald-700 hover:bg-emerald-50 text-xs font-bold transition">
                                + إضافة جدول عملية أخرى في نفس القيد اليومي
                            </button>

                            <div class="flex justify-end gap-2 pt-3 border-t">
                                <button type="button" onclick="closeModal('addJournalEntryModal')" class="px-5 py-2.5 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg">
                                    حفظ القيد والانتقال لشجرة الحسابات
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- نوافذ العرض والطباعة والتعديل لكل قيد يومي -->
                @foreach($journalEntries as $je)
                    @php $items = $journalItemsByEntry[$je->id] ?? collect(); @endphp

                    <div id="viewJournalModal_{{ $je->id }}" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-3xl w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto">
                            <div class="flex items-center justify-between pb-4 border-b mb-4 no-print">
                                <div>
                                    <h3 class="font-bold text-base text-slate-900">📒 تفاصيل القيد اليومي رقم (#{{ $je->entry_number }})</h3>
                                    <p class="text-xs text-slate-500">التاريخ: {{ $je->entry_date }} | البيان العام: {{ $je->general_notes }}</p>
                                </div>
                                <button type="button" onclick="closeModal('viewJournalModal_{{ $je->id }}')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                            </div>

                            <div id="printableJournalArea_{{ $je->id }}" class="p-4 rounded-2xl border border-slate-200 bg-white space-y-4">
                                <div class="flex items-center justify-between border-b pb-3">
                                    <div>
                                        <div class="font-bold text-slate-900">سند قيد يومية رقم: #{{ $je->entry_number }}</div>
                                        <div class="text-xs text-slate-500">البيان العام: {{ $je->general_notes }}</div>
                                    </div>
                                    <div class="text-xs font-mono font-bold">التاريخ: {{ $je->entry_date }}</div>
                                </div>

                                <table class="w-full text-xs text-right border" id="journalDetailTable_{{ $je->id }}">
                                    <thead class="bg-slate-100 text-slate-700 border-b">
                                        <tr>
                                            <th class="py-2.5 px-3">#</th>
                                            <th class="py-2.5 px-3">رقم واسم الحساب</th>
                                            <th class="py-2.5 px-3">مدين (Debit)</th>
                                            <th class="py-2.5 px-3">دائن (Credit)</th>
                                            <th class="py-2.5 px-3">الضريبة</th>
                                            <th class="py-2.5 px-3">البيان التفصيلي</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y">
                                        @foreach($items as $idx => $item)
                                            <tr>
                                                <td class="py-2.5 px-3 font-mono">{{ $idx + 1 }}</td>
                                                <td class="py-2.5 px-3 font-bold">{{ $item->account_code }} - {{ $item->account_name }}</td>
                                                <td class="py-2.5 px-3 font-mono font-bold text-emerald-700">{{ number_format($item->debit, 2) }}</td>
                                                <td class="py-2.5 px-3 font-mono font-bold text-amber-700">{{ number_format($item->credit, 2) }}</td>
                                                <td class="py-2.5 px-3 font-mono">{{ number_format($item->tax, 2) }}</td>
                                                <td class="py-2.5 px-3">{{ $item->notes }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-slate-50 font-bold border-t">
                                        <tr>
                                            <td colspan="2" class="py-2.5 px-3">الإجمالي</td>
                                            <td class="py-2.5 px-3 font-mono text-emerald-700">{{ number_format($je->total_debit, 2) }} ر.س</td>
                                            <td class="py-2.5 px-3 font-mono text-amber-700">{{ number_format($je->total_credit, 2) }} ر.س</td>
                                            <td class="py-2.5 px-3 font-mono">{{ number_format($je->total_tax, 2) }} ر.س</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="printChoiceJournalModal_{{ $je->id }}" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4 no-print">
                        <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-200 text-center space-y-4">
                            <h3 class="font-bold text-base text-slate-900">تصدير وطباعة القيد اليومي (#{{ $je->entry_number }})</h3>
                            <p class="text-xs text-slate-500">اختر الصيغة المطلوبة لتصدير القيد اليومي:</p>
                            <div class="grid grid-cols-2 gap-3">
                                <button type="button" onclick="exportJournalPDF('{{ $je->id }}', '{{ $je->entry_number }}')"
                                        class="p-4 rounded-2xl bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border border-rose-200 font-bold text-xs transition flex flex-col items-center gap-2">
                                    <span class="text-2xl">📄</span>
                                    <span>تصدير ملف PDF</span>
                                </button>
                                <button type="button" onclick="exportTableToCSV('journalDetailTable_{{ $je->id }}', 'Journal_Entry_{{ $je->entry_number }}.csv')"
                                        class="p-4 rounded-2xl bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 font-bold text-xs transition flex flex-col items-center gap-2">
                                    <span class="text-2xl">📊</span>
                                    <span>تصدير ملف Excel</span>
                                </button>
                            </div>
                            <button type="button" onclick="closeModal('printChoiceJournalModal_{{ $je->id }}')" class="w-full py-2 rounded-xl bg-slate-100 text-xs font-semibold">إغلاق</button>
                        </div>
                    </div>

                    <div id="editJournalModal_{{ $je->id }}" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4 no-print">
                        <div class="bg-white rounded-3xl max-w-4xl w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto">
                            <div class="flex items-center justify-between pb-4 border-b mb-4">
                                <h3 class="font-bold text-base text-slate-900">تعديل القيد اليومي رقم (#{{ $je->entry_number }})</h3>
                                <button type="button" onclick="closeModal('editJournalModal_{{ $je->id }}')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                            </div>
                            <form action="{{ route('erp.journal.update', $je->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <div class="grid grid-cols-12 gap-3 p-3.5 rounded-2xl bg-slate-50 border">
                                    <div class="col-span-9">
                                        <label class="block text-xs font-bold text-slate-700 mb-1">البيان العام *</label>
                                        <input type="text" name="general_notes" value="{{ $je->general_notes }}" required class="w-full rounded-xl border px-3 py-2 text-xs bg-white font-bold">
                                    </div>
                                    <div class="col-span-3">
                                        <label class="block text-xs font-bold text-slate-700 mb-1">التاريخ *</label>
                                        <input type="date" name="entry_date" value="{{ $je->entry_date }}" required class="w-full rounded-xl border px-3 py-2 text-xs bg-white font-mono">
                                    </div>
                                </div>
                                <div class="space-y-2.5">
                                    @foreach($items as $item)
                                        <div class="p-3 rounded-2xl bg-slate-50 border grid grid-cols-12 gap-2 items-end">
                                            <div class="col-span-4">
                                                <label class="block text-[10px] font-bold text-slate-500">الحساب</label>
                                                <select name="account_ids[]" class="w-full rounded-xl border px-2 py-1.5 text-xs bg-white">
                                                    @foreach($accounts->where('is_group', 0) as $acc)
                                                        <option value="{{ $acc->id }}" {{ $item->account_id == $acc->id ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-span-2">
                                                <label class="block text-[10px] font-bold text-emerald-700">مدين</label>
                                                <input type="number" step="0.01" name="debits[]" value="{{ $item->debit }}" class="w-full rounded-xl border px-2 py-1.5 text-xs bg-white font-bold">
                                            </div>
                                            <div class="col-span-2">
                                                <label class="block text-[10px] font-bold text-amber-700">دائن</label>
                                                <input type="number" step="0.01" name="credits[]" value="{{ $item->credit }}" class="w-full rounded-xl border px-2 py-1.5 text-xs bg-white font-bold">
                                            </div>
                                            <div class="col-span-1">
                                                <label class="block text-[10px] font-bold text-slate-500">ضريبة</label>
                                                <input type="number" step="0.01" name="taxes[]" value="{{ $item->tax }}" class="w-full rounded-xl border px-2 py-1.5 text-xs bg-white">
                                            </div>
                                            <div class="col-span-3">
                                                <label class="block text-[10px] font-bold text-slate-500">بيان تفصيلي</label>
                                                <input type="text" name="item_notes[]" value="{{ $item->notes }}" class="w-full rounded-xl border px-2 py-1.5 text-xs bg-white">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="flex justify-end gap-2 pt-3 border-t">
                                    <button type="button" onclick="closeModal('editJournalModal_{{ $je->id }}')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                    <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">حفظ التعديلات</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            @endif

            <!-- 5-ج (1). صفحة الفوترة الضريبية للشركة الأم (بدون حذف أو تعديل للفاتورة + نسبة ضريبة مركزية من إعدادات الأدمن) -->
            @if($tab == 'invoicing')
                @php
                    $systemVatRate = $companyTaxProfile['default_vat_rate'] ?? 15;
                @endphp
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden no-print">
                    <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="font-bold text-lg text-slate-900">سجل الفواتير الضريبية الإلكترونية</h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                الرقم الضريبي للشركة: <span class="font-mono font-bold text-emerald-700">{{ $companyTaxProfile['tax_number'] ?? '-' }}</span> |
                                نسبة الضريبة الحالية المعتمدة: <span class="font-mono font-bold text-amber-700">{{ $systemVatRate }}%</span> (تُطبق على الفواتير الجديدة فقط)
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2.5">
                            <input type="text" id="companyInvoiceSearch" oninput="filterCompanyInvoicesTable()"
                                   placeholder="🔍 ابحث برقم الفاتورة، اسم المكتب، أو البيان..."
                                   class="w-64 rounded-2xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-xs focus:bg-white focus:outline-none focus:border-emerald-600">

                            <button type="button" onclick="openModal('companyTaxSettingsModal')"
                                    class="px-4 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition">
                                ⚙️ إعدادات الضريبة ({{ $systemVatRate }}%) وصلاحيات المكاتب
                            </button>

                            <button type="button" onclick="openModal('createCompanyInvoiceModal')"
                                    class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs shadow-lg flex items-center gap-2 transition">
                                <span>➕</span>
                                <span>إصدار فاتورة ضريبية</span>
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-right" id="companyInvoicesTable">
                            <thead class="bg-slate-50 text-slate-600 text-xs border-b">
                                <tr>
                                    <th class="py-4 px-4">رقم الفاتورة</th>
                                    <th class="py-4 px-4">التاريخ</th>
                                    <th class="py-4 px-4">المكتب (العميل)</th>
                                    <th class="py-4 px-4">نوع الفاتورة</th>
                                    <th class="py-4 px-4">البيان</th>
                                    <th class="py-4 px-4">قبل الضريبة</th>
                                    <th class="py-4 px-4">الضريبة</th>
                                    <th class="py-4 px-4">الإجمالي</th>
                                    <th class="py-4 px-4 text-center">الربط بالشجرة</th>
                                    <th class="py-4 px-4 text-center">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse(($companyInvoices ?? collect()) as $inv)
                                    <tr class="hover:bg-slate-50 transition cmp-invoice-row"
                                        data-search="{{ mb_strtolower($inv->invoice_number . ' ' . $inv->buyer_name . ' ' . $inv->description . ' ' . ($inv->buyer_tax_number ?? '') . ' ' . $inv->issue_date) }}">
                                        <td class="py-4 px-4 font-mono font-bold text-slate-900 text-xs">{{ $inv->invoice_number }}</td>
                                        <td class="py-4 px-4 font-mono text-xs text-slate-500">{{ $inv->issue_date }}</td>
                                        <td class="py-4 px-4">
                                            <div class="font-bold text-slate-900 text-xs">{{ $inv->buyer_name }}</div>
                                            @if($inv->buyer_tax_number)
                                                <div class="text-[10px] font-mono text-slate-400">ضريبي: {{ $inv->buyer_tax_number }}</div>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="px-2.5 py-1 rounded-lg font-bold text-[11px] {{ $inv->invoice_type == 'tax' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                                {{ $inv->invoice_type == 'tax' ? 'فاتورة ضريبية' : 'ضريبية مبسطة' }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-xs font-semibold text-slate-700">{{ $inv->description }}</td>
                                        <td class="py-4 px-4 font-mono text-xs">{{ number_format($inv->subtotal, 2) }}</td>
                                        <td class="py-4 px-4 font-mono text-xs text-amber-700 font-bold">
                                            {{ number_format($inv->vat_amount, 2) }}
                                            <span class="text-[10px] text-slate-400 block">({{ (float)$inv->vat_rate }}%)</span>
                                        </td>
                                        <td class="py-4 px-4 font-mono font-extrabold text-emerald-700 text-xs">{{ number_format($inv->total_amount, 2) }} ر.س</td>
                                        <td class="py-4 px-4 text-center">
                                            @if(!empty($inv->journal_entry_id))
                                                <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-200">مرحّلة للشجرة ✔</span>
                                            @else
                                                <span class="text-[10px] text-slate-400">-</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                                <button type="button" onclick="openTaxInvoicePreviewModal({
                                                    number: '{{ $inv->invoice_number }}',
                                                    type: '{{ $inv->invoice_type == 'tax' ? 'فاتورة ضريبية' : 'فاتورة ضريبية مبسطة' }}',
                                                    date: '{{ $inv->issue_date }}',
                                                    seller_name: '{{ addslashes($inv->seller_name) }}',
                                                    seller_tax: '{{ addslashes($inv->seller_tax_number ?? '') }}',
                                                    seller_addr: '{{ addslashes($inv->seller_address ?? '') }}',
                                                    buyer_name: '{{ addslashes($inv->buyer_name) }}',
                                                    buyer_tax: '{{ addslashes($inv->buyer_tax_number ?? '') }}',
                                                    buyer_addr: '{{ addslashes($inv->buyer_address ?? '') }}',
                                                    desc: '{{ addslashes($inv->description) }}',
                                                    subtotal: '{{ number_format($inv->subtotal, 2) }}',
                                                    vat_rate: '{{ (float)$inv->vat_rate }}',
                                                    vat: '{{ number_format($inv->vat_amount, 2) }}',
                                                    total: '{{ number_format($inv->total_amount, 2) }}',
                                                    qr: '{{ $inv->zatca_qr }}'
                                                })" class="px-2.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-white text-xs font-bold transition">
                                                    📥 تحميل PDF / طباعة
                                                </button>

                                                <form action="{{ route('erp.invoices.whatsapp', $inv->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 text-xs font-bold transition">
                                                        💬 واتساب
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="10" class="py-12 text-center text-slate-400">لا توجد فواتير ضريبية صادرة بعد. اضغط على "إصدار فاتورة ضريبية" للبدء.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- نافذة (Modal) إصدار فاتورة ضريبية جديدة (نسبة الضريبة مقفلة وتقرأ من إعدادات الأدمن) -->
                <div id="createCompanyInvoiceModal" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4 no-print">
                    <div class="bg-white rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-slate-200 max-h-[92vh] overflow-y-auto">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                            <div>
                                <h3 class="font-bold text-base text-slate-900">➕ إصدار فاتورة ضريبية لمكتب عقار</h3>
                                <p class="text-xs text-slate-500">نسبة الضريبة ثابتة من الإعدادات ({{ $systemVatRate }}%) ولا يمكن حذف الفاتورة بعد إصدارها</p>
                            </div>
                            <button type="button" onclick="closeModal('createCompanyInvoiceModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                        </div>

                        <form action="{{ route('erp.invoices.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">اختر مكتب العقار *</label>
                                <select name="office_id" id="cmp_inv_office" required onchange="fillCompanyInvoiceOfficeTax()" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-xs bg-white font-bold">
                                    <option value="" data-tax="" data-addr="" data-fee="0" data-acc="">-- اختر المكتب --</option>
                                    @foreach($offices as $off)
                                        <option value="{{ $off->id }}"
                                                data-tax="{{ $off->tax_number ?? '' }}"
                                                data-addr="{{ $off->national_address ?: $off->address }}"
                                                data-fee="{{ $off->annual_fee ?? 0 }}"
                                                data-acc="{{ $off->account_id ?? '' }}">
                                            {{ $off->name }} {{ !empty($off->tax_number) ? '(ضريبي: ' . $off->tax_number . ')' : '(بدون رقم ضريبي)' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">الرقم الضريبي للمكتب (اختياري)</label>
                                    <input type="text" name="buyer_tax_number" id="cmp_inv_buyer_tax" maxlength="15" placeholder="300XXXXXXXXXXXX" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-mono">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">عنوان المكتب</label>
                                    <input type="text" name="buyer_address" id="cmp_inv_buyer_addr" placeholder="الرياض..." class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs">
                                </div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">🌳 حساب المدين (ذمة المكتب) *</label>
                                    <select name="debit_account_id" id="cmp_inv_debit_acc" class="w-full rounded-xl border border-slate-300 px-2.5 py-2 text-xs bg-white font-semibold">
                                        @foreach($accounts->where('is_group', 0) as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">🌳 حساب الإيراد (الدائن) *</label>
                                    <select name="credit_account_id" class="w-full rounded-xl border border-slate-300 px-2.5 py-2 text-xs bg-white font-semibold">
                                        @foreach($accounts->where('is_group', 0) as $acc)
                                            <option value="{{ $acc->id }}" {{ $acc->code == '4101' ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">البيان / تفاصيل الخدمة أو الاشتراك *</label>
                                <input type="text" name="description" required placeholder="مثال: رسوم الاشتراك السنوي لمنصة العقارات / عمولة عقود..." class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs">
                            </div>

                            <div class="grid grid-cols-3 gap-2.5 p-3.5 rounded-2xl bg-emerald-50/60 border border-emerald-200">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">المبلغ قبل الضريبة *</label>
                                    <input type="number" step="0.01" name="subtotal" id="cmp_inv_sub" required oninput="calcCompanyInvTotal()" class="w-full rounded-xl border border-slate-300 px-2.5 py-1.5 text-xs bg-white font-bold">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-500 mb-1">نسبة الضريبة % (ثابتة)</label>
                                    <input type="number" id="cmp_inv_rate" value="{{ $systemVatRate }}" readonly disabled class="w-full rounded-xl border border-slate-200 px-2.5 py-1.5 text-xs bg-slate-100 font-mono text-slate-600 cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-emerald-800 mb-1">الإجمالي شامل الضريبة</label>
                                    <input type="text" id="cmp_inv_total_preview" readonly value="0.00" class="w-full rounded-xl border border-emerald-300 px-2.5 py-1.5 text-xs bg-emerald-100 font-mono font-extrabold text-emerald-900">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">تاريخ الإصدار</label>
                                <input type="date" name="issue_date" value="{{ date('Y-m-d') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-mono">
                            </div>

                            <div class="flex justify-end gap-2 pt-3 border-t">
                                <button type="button" onclick="closeModal('createCompanyInvoiceModal')" class="px-5 py-2.5 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg">
                                    اعتماد الفاتورة وترحيلها للشجرة
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- نافذة (Modal) الإعدادات الضريبية للشركة وتعديل نسبة الضريبة المركزية -->
                <div id="companyTaxSettingsModal" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4 no-print">
                    <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto space-y-6">
                        <div class="flex items-center justify-between pb-3 border-b">
                            <h3 class="font-bold text-base text-slate-900">⚙️ الإعدادات الضريبية ونسبة الضريبة العامة للنظام</h3>
                            <button type="button" onclick="closeModal('companyTaxSettingsModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                        </div>

                        <form action="{{ route('erp.tax_profile.save') }}" method="POST" class="space-y-3.5 p-4 rounded-2xl bg-slate-50 border">
                            @csrf
                            <div class="text-xs font-bold text-slate-900">🏛️ البيانات الضريبية للشركة الأم ونسبة الضريبة المركزية</div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">الاسم القانوني للشركة *</label>
                                    <input type="text" name="legal_name" value="{{ $companyTaxProfile['legal_name'] ?? '' }}" required class="w-full rounded-xl border px-3 py-2 text-xs bg-white font-bold">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-emerald-800 mb-1">نسبة الضريبة المعتمدة (%) *</label>
                                    <input type="number" step="0.01" min="0" max="100" name="default_vat_rate" value="{{ $systemVatRate }}" required class="w-full rounded-xl border-2 border-emerald-500 px-3 py-2 text-xs font-mono font-extrabold text-emerald-900 bg-emerald-50">
                                </div>
                            </div>
                            <p class="text-[11px] text-amber-800 bg-amber-50 p-2 rounded-lg border border-amber-200">
                                💡 إذا قمت بتعديل نسبة الضريبة هنا (مثلاً إلى 5% أو 15%) فستتغير فوراً لدى الشركة وجميع مكاتب العقار في الفواتير الجديدة فقط، ولن تتأثر الفواتير السابقة (بدون أثر رجعي).
                            </p>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">الرقم الضريبي (15 رقماً) *</label>
                                    <input type="text" name="tax_number" value="{{ $companyTaxProfile['tax_number'] ?? '' }}" required maxlength="15" class="w-full rounded-xl border px-3 py-2 text-xs font-mono bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">السجل التجاري</label>
                                    <input type="text" name="cr_number" value="{{ $companyTaxProfile['cr_number'] ?? '' }}" class="w-full rounded-xl border px-3 py-2 text-xs font-mono bg-white">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">العنوان الوطني</label>
                                <input type="text" name="national_address" value="{{ $companyTaxProfile['national_address'] ?? '' }}" class="w-full rounded-xl border px-3 py-2 text-xs bg-white">
                            </div>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold">💾 حفظ الإعدادات ونسبة الضريبة</button>
                        </form>

                        <div class="space-y-2">
                            <h4 class="font-bold text-xs text-slate-900">🔐 تفعيل أو إخفاء ميزة (إصدار فاتورة ضريبية باسم المالك) للمكاتب:</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5 max-h-48 overflow-y-auto">
                                @foreach($offices as $off)
                                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border text-xs">
                                        <div>
                                            <div class="font-bold text-slate-900">{{ $off->name }}</div>
                                            <div class="text-[10px] text-slate-500">ضريبي: {{ $off->tax_number ?: 'غير مسجل' }}</div>
                                        </div>
                                        <form action="{{ route('erp.offices.toggle_owner_invoicing', $off->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-lg font-bold text-[11px] {{ !empty($off->allow_owner_tax_invoice) ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-700' }}">
                                                {{ !empty($off->allow_owner_tax_invoice) ? '✔ مفعلة' : 'مخفية (تفعيل)' }}
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- نافذة معاينة وتحميل الفاتورة الضريبية PDF -->
                <div id="taxInvoicePreviewModal" class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200">
                        <div class="flex items-center justify-between pb-4 border-b mb-4 no-print">
                            <h3 class="font-bold text-base text-slate-900">معاينة وتحميل الفاتورة الضريبية المعتمدة</h3>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="downloadCompanyTaxInvoicePDF()" class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">📥 تحميل PDF</button>
                                <button type="button" onclick="window.print()" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold">🖨️ طباعة</button>
                                <button type="button" onclick="closeModal('taxInvoicePreviewModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                            </div>
                        </div>
                        <div id="printableVoucherArea" class="p-6 rounded-3xl border-2 border-slate-900 bg-white space-y-5 text-slate-900">
                            <div class="flex items-start justify-between border-b-2 border-slate-900 pb-4">
                                <div>
                                    <div id="ti_type" class="text-lg font-extrabold text-emerald-800">فاتورة ضريبية</div>
                                    <div class="text-xs text-slate-500">Tax Invoice — متوافقة مع متطلبات هيئة الزكاة والضريبة والجمارك</div>
                                    <div class="mt-2 text-xs font-mono font-bold">رقم الفاتورة: <span id="ti_number" class="text-slate-900"></span></div>
                                    <div class="text-xs font-mono">تاريخ الإصدار: <span id="ti_date"></span></div>
                                </div>
                                <div class="text-center">
                                    <img id="ti_qr_img" src="" alt="ZATCA QR" class="w-24 h-24 border border-slate-300 rounded-xl p-1 mx-auto">
                                    <span class="text-[9px] font-mono text-slate-400 block mt-1">ZATCA E-Invoice QR</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 text-xs">
                                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                                    <div class="font-bold text-slate-500">بيانات المورّد (المُصدِر):</div>
                                    <div id="ti_seller_name" class="font-extrabold text-slate-900 text-sm"></div>
                                    <div>الرقم الضريبي: <b id="ti_seller_tax" class="font-mono text-emerald-700"></b></div>
                                    <div>العنوان: <span id="ti_seller_addr"></span></div>
                                </div>
                                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                                    <div class="font-bold text-slate-500">بيانات العميل (المستفيد):</div>
                                    <div id="ti_buyer_name" class="font-extrabold text-slate-900 text-sm"></div>
                                    <div id="ti_buyer_tax_row">الرقم الضريبي: <b id="ti_buyer_tax" class="font-mono text-emerald-700"></b></div>
                                    <div>العنوان: <span id="ti_buyer_addr"></span></div>
                                </div>
                            </div>

                            <table class="w-full text-xs text-right border border-slate-300 rounded-xl overflow-hidden">
                                <thead class="bg-slate-900 text-white">
                                    <tr>
                                        <th class="py-2.5 px-3">البيان / الوصف</th>
                                        <th class="py-2.5 px-3">المبلغ الخاضع للضريبة</th>
                                        <th class="py-2.5 px-3">ضريبة القيمة المضافة (<span id="ti_vat_rate_label">15</span>%)</th>
                                        <th class="py-2.5 px-3">الإجمالي شامل الضريبة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b">
                                        <td id="ti_desc" class="py-3 px-3 font-bold"></td>
                                        <td id="ti_subtotal" class="py-3 px-3 font-mono"></td>
                                        <td id="ti_vat" class="py-3 px-3 font-mono text-amber-700 font-bold"></td>
                                        <td id="ti_total" class="py-3 px-3 font-mono font-extrabold text-emerald-700 text-sm"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <script>
                    let currentCompanyInvoiceNo = 'Tax_Invoice';
                    function filterCompanyInvoicesTable() {
                        const q = (document.getElementById('companyInvoiceSearch')?.value || '').trim().toLowerCase();
                        document.querySelectorAll('.cmp-invoice-row').forEach(r => {
                            const hay = (r.getAttribute('data-search') || r.innerText || '').toLowerCase();
                            r.style.display = hay.includes(q) ? '' : 'none';
                        });
                    }
                    function fillCompanyInvoiceOfficeTax() {
                        const sel = document.getElementById('cmp_inv_office');
                        if (!sel) return;
                        const opt = sel.options[sel.selectedIndex];
                        document.getElementById('cmp_inv_buyer_tax').value = opt.getAttribute('data-tax') || '';
                        document.getElementById('cmp_inv_buyer_addr').value = opt.getAttribute('data-addr') || '';
                        const accId = opt.getAttribute('data-acc') || '';
                        if (accId && document.getElementById('cmp_inv_debit_acc')) {
                            document.getElementById('cmp_inv_debit_acc').value = accId;
                        }
                        const fee = parseFloat(opt.getAttribute('data-fee') || 0);
                        if (fee > 0 && !document.getElementById('cmp_inv_sub').value) {
                            document.getElementById('cmp_inv_sub').value = fee;
                        }
                        calcCompanyInvTotal();
                    }
                    function calcCompanyInvTotal() {
                        const sub = parseFloat(document.getElementById('cmp_inv_sub')?.value || 0);
                        const rate = parseFloat(document.getElementById('cmp_inv_rate')?.value || 15);
                        const vat = (sub * rate) / 100;
                        const tot = sub + vat;
                        const prev = document.getElementById('cmp_inv_total_preview');
                        if (prev) prev.value = tot.toFixed(2) + ' ر.س';
                    }
                    function openTaxInvoicePreviewModal(d) {
                        currentCompanyInvoiceNo = d.number || 'Tax_Invoice';
                        document.getElementById('ti_type').innerText = d.type;
                        document.getElementById('ti_number').innerText = d.number;
                        document.getElementById('ti_date').innerText = d.date;
                        document.getElementById('ti_seller_name').innerText = d.seller_name;
                        document.getElementById('ti_seller_tax').innerText = d.seller_tax || 'غير مسجل';
                        document.getElementById('ti_seller_addr').innerText = d.seller_addr || '-';
                        document.getElementById('ti_buyer_name').innerText = d.buyer_name;
                        document.getElementById('ti_buyer_tax').innerText = d.buyer_tax || 'لا يوجد (فاتورة مبسطة)';
                        document.getElementById('ti_buyer_addr').innerText = d.buyer_addr || '-';
                        document.getElementById('ti_desc').innerText = d.desc;
                        document.getElementById('ti_subtotal').innerText = d.subtotal + ' ر.س';
                        if (document.getElementById('ti_vat_rate_label')) {
                            document.getElementById('ti_vat_rate_label').innerText = d.vat_rate || '15';
                        }
                        document.getElementById('ti_vat').innerText = d.vat + ' ر.س';
                        document.getElementById('ti_total').innerText = d.total + ' ر.س';
                        document.getElementById('ti_qr_img').src = 'https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=' + encodeURIComponent(d.qr);
                        openModal('taxInvoicePreviewModal');
                    }
                    function downloadCompanyTaxInvoicePDF() {
                        const el = document.getElementById('printableVoucherArea');
                        if (!el) return;
                        html2pdf().set({
                            margin: 10,
                            filename: currentCompanyInvoiceNo + '.pdf',
                            image: { type: 'jpeg', quality: 0.98 },
                            html2canvas: { scale: 2, useCORS: true },
                            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                        }).from(el).save();
                    }
                </script>
            @endif

            <!-- 5-ج (2). صفحة كشف الحساب التفصيلي المستقلة (مفصولة عن الأستاذ العام) -->
            @if($tab == 'account_statement')
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-5">
                    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4 no-print">
                        <div>
                            <h3 class="font-bold text-lg text-slate-900"> كشف الحساب  (Account Statement)</h3>
                            <p class="text-xs text-slate-500">صفحة مستقلة لاستخراج كشف حساب تفصيلي لأي مكتب عقار أو أي حساب فرعي في الشجرة مع الرصيد التراكمي</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="window.print()" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold">🖨️ طباعة الكشف</button>
                            <button type="button" onclick="exportTableToCSV('standaloneAccountStatementTable', 'Account_Statement.csv')" class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">📊 تصدير Excel</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-200 no-print">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">اختر الحساب من الشجرة</label>
                            <select id="sa_acc_filter" onchange="filterStandaloneStatement()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-bold">
                                <option value="">-- جميع الحسابات --</option>
                                @foreach($accounts->where('is_group', 0) as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">أو اختر مكتب العقار</label>
                            <select id="sa_office_filter" onchange="filterStandaloneStatement()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-bold">
                                <option value="">-- جميع المكاتب --</option>
                                @foreach($offices as $off)
                                    <option value="{{ $off->id }}">{{ $off->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">من تاريخ</label>
                            <input type="date" id="sa_from_date" onchange="filterStandaloneStatement()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">إلى تاريخ</label>
                            <input type="date" id="sa_to_date" onchange="filterStandaloneStatement()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-mono">
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-2xl border border-slate-200">
                        <table class="w-full text-xs text-right" id="standaloneAccountStatementTable">
                            <thead class="bg-slate-900 text-white">
                                <tr>
                                    <th class="py-3.5 px-4">التاريخ</th>
                                    <th class="py-3.5 px-4">رقم السند / المرجع</th>
                                    <th class="py-3.5 px-4">الحساب</th>
                                    <th class="py-3.5 px-4">المكتب المرتبط</th>
                                    <th class="py-3.5 px-4">البيان</th>
                                    <th class="py-3.5 px-4 text-emerald-300">مدين (+)</th>
                                    <th class="py-3.5 px-4 text-amber-300">دائن (-)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($vouchers as $v)
                                    <tr class="sa-stmt-row hover:bg-slate-50"
                                        data-date="{{ $v->voucher_date }}"
                                        data-office="{{ $v->office_id ?? '' }}"
                                        data-debit-acc="{{ $v->debit_account_id }}"
                                        data-credit-acc="{{ $v->credit_account_id }}">
                                        <td class="py-3 px-4 font-mono">{{ $v->voucher_date }}</td>
                                        <td class="py-3 px-4 font-mono font-bold">{{ $v->voucher_number }}</td>
                                        <td class="py-3 px-4 font-bold">{{ $v->debit_code }} - {{ $v->debit_name }} / {{ $v->credit_code }} - {{ $v->credit_name }}</td>
                                        <td class="py-3 px-4 font-semibold">{{ $v->office_name ?: 'الإدارة المركزية' }}</td>
                                        <td class="py-3 px-4">{{ $v->notes }}</td>
                                        <td class="py-3 px-4 font-mono font-bold text-emerald-700">{{ $v->voucher_type == 'receipt' ? number_format($v->amount, 2) : '0.00' }}</td>
                                        <td class="py-3 px-4 font-mono font-bold text-amber-700">{{ $v->voucher_type == 'payment' ? number_format($v->amount, 2) : '0.00' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <script>
                    function filterStandaloneStatement() {
                        const acc = document.getElementById('sa_acc_filter')?.value || '';
                        const off = document.getElementById('sa_office_filter')?.value || '';
                        const from = document.getElementById('sa_from_date')?.value || '';
                        const to = document.getElementById('sa_to_date')?.value || '';
                        document.querySelectorAll('.sa-stmt-row').forEach(r => {
                            const d = r.getAttribute('data-date') || '';
                            const rOff = r.getAttribute('data-office') || '';
                            const dAcc = r.getAttribute('data-debit-acc') || '';
                            const cAcc = r.getAttribute('data-credit-acc') || '';
                            let show = true;
                            if (acc && dAcc !== acc && cAcc !== acc) show = false;
                            if (off && rOff !== off) show = false;
                            if (from && d < from) show = false;
                            if (to && d > to) show = false;
                            r.style.display = show ? '' : 'none';
                        });
                    }
                </script>
            @endif

            <!-- 5-ج. دفتر الأستاذ العام المطور والقوائم المالية الاحترافية (General Ledger Pro & Financial Analytics) -->
            @if($tab == 'statements')
                @php
                    $rawMovements = collect();

                    foreach ($vouchers as $v) {
                        $rawMovements->push((object)[
                            'date' => $v->voucher_date,
                            'ref' => $v->voucher_number,
                            'source' => $v->voucher_type == 'receipt' ? 'سند قبض' : 'سند صرف',
                            'account_id' => $v->debit_account_id,
                            'account_code' => $v->debit_code,
                            'account_name' => $v->debit_name,
                            'opposite_account' => ($v->credit_code ?? '') . ' - ' . ($v->credit_name ?? ''),
                            'debit' => (float) $v->amount,
                            'credit' => 0.0,
                            'tax' => 0.0,
                            'notes' => $v->notes,
                            'office_id' => $v->office_id,
                            'party' => $v->office_name ?? 'الإدارة المركزية',
                        ]);
                        $rawMovements->push((object)[
                            'date' => $v->voucher_date,
                            'ref' => $v->voucher_number,
                            'source' => $v->voucher_type == 'receipt' ? 'سند قبض' : 'سند صرف',
                            'account_id' => $v->credit_account_id,
                            'account_code' => $v->credit_code,
                            'account_name' => $v->credit_name,
                            'opposite_account' => ($v->debit_code ?? '') . ' - ' . ($v->debit_name ?? ''),
                            'debit' => 0.0,
                            'credit' => (float) $v->amount,
                            'tax' => 0.0,
                            'notes' => $v->notes,
                            'office_id' => $v->office_id,
                            'party' => $v->office_name ?? 'الإدارة المركزية',
                        ]);
                    }

                    foreach ($journalItems as $ji) {
                        $rawMovements->push((object)[
                            'date' => $ji->entry_date,
                            'ref' => 'قيد #' . $ji->entry_number,
                            'source' => 'قيد يومي',
                            'account_id' => $ji->account_id,
                            'account_code' => $ji->account_code,
                            'account_name' => $ji->account_name,
                            'opposite_account' => 'قيد مزدوج #' . $ji->entry_number,
                            'debit' => (float) $ji->debit,
                            'credit' => (float) $ji->credit,
                            'tax' => (float) $ji->tax,
                            'notes' => $ji->notes ?: $ji->general_notes,
                            'office_id' => null,
                            'party' => 'قيد يومية مركزي',
                        ]);
                    }

                    $runningByAccount = [];
                    $chronologicalMovements = $rawMovements->sortBy('date')->values()->map(function ($m) use (&$runningByAccount) {
                        $accId = $m->account_id;
                        if (!isset($runningByAccount[$accId])) {
                            $runningByAccount[$accId] = 0.0;
                        }
                        $m->opening_before = $runningByAccount[$accId];
                        $runningByAccount[$accId] += ($m->debit - $m->credit);
                        $m->running_balance = $runningByAccount[$accId];
                        return $m;
                    });

                    $allMovements = $chronologicalMovements->reverse()->values();
                    $movementsByAccount = $chronologicalMovements->groupBy('account_id');

                    $leafAccountsList = $accounts->where('is_group', 0);
                    $expenseAccounts = $leafAccountsList->filter(fn($a) => str_contains($a->type, 'مصروف') || str_starts_with($a->code, '5'));
                    $revenueAccounts = $leafAccountsList->filter(fn($a) => str_contains($a->type, 'إيراد') || str_starts_with($a->code, '4'));
                    $assetAccounts   = $leafAccountsList->filter(fn($a) => str_contains($a->type, 'أصول') || str_starts_with($a->code, '1'));

                    $totalExpenses = $expenseAccounts->sum(fn($a) => abs(($a->debit_balance ?? 0) - ($a->credit_balance ?? 0)) ?: abs($a->balance));
                    $totalRevenues = $revenueAccounts->sum(fn($a) => abs(($a->credit_balance ?? 0) - ($a->debit_balance ?? 0)) ?: abs($a->balance));
                    $netIncomeStatement = $totalRevenues - $totalExpenses;
                    $profitMarginPercent = $totalRevenues > 0 ? round(($netIncomeStatement / $totalRevenues) * 100, 1) : 0;
                    $expenseRatioPercent = $totalRevenues > 0 ? round(($totalExpenses / $totalRevenues) * 100, 1) : 0;
                @endphp

                <!-- مؤشرات الأداء المالي الذكية (KPIs) -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6 no-print">
                    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-500">إجمالي الإيرادات</span>
                            <span class="px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 text-[10px] font-bold">قائمة الدخل</span>
                        </div>
                        <div class="text-2xl font-extrabold text-emerald-600 mt-2 font-mono">{{ number_format($totalRevenues, 2) }} <span class="text-xs">ر.س</span></div>
                    </div>
                    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-500">إجمالي المصروفات</span>
                            <span class="px-2 py-0.5 rounded-lg bg-rose-50 text-rose-700 text-[10px] font-bold">{{ $expenseRatioPercent }}% من الإيراد</span>
                        </div>
                        <div class="text-2xl font-extrabold text-rose-600 mt-2 font-mono">{{ number_format($totalExpenses, 2) }} <span class="text-xs">ر.س</span></div>
                    </div>
                    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-500">صافي الربح الفعلي</span>
                            <span class="px-2 py-0.5 rounded-lg bg-blue-50 text-blue-700 text-[10px] font-bold">هامش {{ $profitMarginPercent }}%</span>
                        </div>
                        <div class="text-2xl font-extrabold {{ $netIncomeStatement >= 0 ? 'text-emerald-700' : 'text-red-600' }} mt-2 font-mono">
                            {{ number_format($netIncomeStatement, 2) }} <span class="text-xs">ر.س</span>
                        </div>
                    </div>
                    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-500">عدد الحركات المرحلة</span>
                            <span class="px-2 py-0.5 rounded-lg bg-indigo-50 text-indigo-700 text-[10px] font-bold">قيد مزدوج</span>
                        </div>
                        <div class="text-2xl font-extrabold text-slate-900 mt-2 font-mono">{{ $allMovements->count() }} <span class="text-xs font-normal">شطر</span></div>
                    </div>
                    <div class="bg-slate-900 text-white p-5 rounded-3xl shadow-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-emerald-400">إجمالي الأصول والسيولة</span>
                            <span class="px-2 py-0.5 rounded-lg bg-white/10 text-white text-[10px] font-bold">المركز المالي</span>
                        </div>
                        <div class="text-2xl font-extrabold mt-2 font-mono">{{ number_format($assetAccounts->sum('balance'), 2) }} <span class="text-xs">ر.س</span></div>
                    </div>
                </div>

                <!-- الحاوية الرئيسية للأستاذ العام المطور -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden" id="financialStatementPrintable">
                    <div class="p-6 border-b border-slate-200 bg-slate-50/70 space-y-4 no-print">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <h3 class="font-bold text-lg text-slate-900"> دفتر الأستاذ العام  </h3>
                                <p class="text-xs text-slate-500 mt-0.5">يدعم حساب الرصيد الافتتاحي للفترة، الحساب المقابل، الفلترة بمركز التكلفة (المكتب)، وميزان المراجعة بالمجاميع والأرصدة</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="window.print()" class="px-4 py-2.5 rounded-2xl bg-slate-200 hover:bg-slate-300 text-slate-800 text-xs font-bold transition">
                                     طباعة 
                                </button>
                                <button type="button" onclick="exportStatementPDF()" class="px-4 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow flex items-center gap-1.5">
                                <span>تصدير PDF</span>
                                </button>
                                <button type="button" onclick="exportTableToCSV('generalStatementTable', 'General_Ledger_Pro.csv')" class="px-4 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow flex items-center gap-1.5">
                                <span>تصدير Excel</span>
                                </button>
                            </div>
                        </div>

                        <!-- أزرار التبديل بين طرق العرض المحاسبية -->
                        <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <button type="button" onclick="switchLedgerView('ledger')" id="btn_view_ledger"
                                        class="px-4 py-2 rounded-2xl bg-emerald-600 text-white text-xs font-bold shadow-sm transition">
                                    📖 دفتر الأستاذ العام التفصيلي
                                </button>
                                <button type="button" onclick="switchLedgerView('cards')" id="btn_view_cards"
                                        class="px-4 py-2 rounded-2xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 text-xs font-bold transition">
                                    🗂️ بطاقات حسابات الأستاذ (T-Accounts)
                                </button>
                                <button type="button" onclick="switchLedgerView('trial')" id="btn_view_trial"
                                        class="px-4 py-2 rounded-2xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 text-xs font-bold transition">
                                    ⚖️ ميزان المراجعة بالمجاميع والأرصدة
                                </button>
                                <button type="button" onclick="switchLedgerView('income')" id="btn_view_income"
                                        class="px-4 py-2 rounded-2xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 text-xs font-bold transition">
                                     قائمة الدخل وتحليل المصاريف
                                </button>
                            </div>

                            <!-- أزرار الفترات الزمنية السريعة -->
                            <div class="flex items-center gap-1.5 bg-white p-1 rounded-2xl border border-slate-200 text-[11px]">
                                <span class="px-2 text-slate-400 font-bold">فترة سريعة:</span>
                                <button type="button" onclick="setLedgerQuickDate('today')" class="px-2.5 py-1 rounded-xl hover:bg-emerald-50 text-slate-700 font-bold">اليوم</button>
                                <button type="button" onclick="setLedgerQuickDate('month')" class="px-2.5 py-1 rounded-xl hover:bg-emerald-50 text-slate-700 font-bold">هذا الشهر</button>
                                <button type="button" onclick="setLedgerQuickDate('year')" class="px-2.5 py-1 rounded-xl hover:bg-emerald-50 text-slate-700 font-bold">هذه السنة</button>
                                <button type="button" onclick="setLedgerQuickDate('all')" class="px-2.5 py-1 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold">الكل</button>
                            </div>
                        </div>

                        <!-- فلاتر البحث المتقدم في الأستاذ العام -->
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 pt-2">
                            <div class="md:col-span-3">
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">🔍 بحث فوري شامل</label>
                                <input type="text" id="ledgerInstantSearch" oninput="applyStatementFilters()"
                                       placeholder="حساب، بيان، رقم سند/قيد، مبلغ..."
                                       class="w-full rounded-xl border-2 border-emerald-500/70 bg-white px-3 py-2 text-xs font-semibold focus:outline-none focus:border-emerald-600">
                            </div>

                            <div class="md:col-span-3">
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">🎯 نوع الحساب / القائمة</label>
                                <select id="stmt_filter_mode" onchange="applyStatementFilters()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-bold text-slate-900">
                                    <option value="all">جميع حسابات الأستاذ العام</option>
                                    <option value="specific_account">🌳 كشف حساب تفصيلي لحساب محدد</option>
                                    <option value="all_expenses">🔥 جميع حسابات المصروفات فقط</option>
                                    <option value="specific_expense">🎯 مصروف محدد فقط</option>
                                    <option value="all_revenues">💰 جميع حسابات الإيرادات فقط</option>
                                </select>
                            </div>

                            <div id="stmt_specific_expense_box" class="hidden md:col-span-2">
                                <label class="block text-[11px] font-bold text-rose-700 mb-1">اختر المصروف</label>
                                <select id="stmt_expense_select" onchange="applyStatementFilters()" class="w-full rounded-xl border border-rose-300 px-2.5 py-2 text-xs bg-rose-50/40 font-bold">
                                    <option value="">-- كل المصاريف --</option>
                                    @foreach($expenseAccounts as $expAcc)
                                        <option value="{{ $expAcc->id }}">{{ $expAcc->code }} - {{ $expAcc->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="stmt_specific_account_box" class="hidden md:col-span-2">
                                <label class="block text-[11px] font-bold text-emerald-700 mb-1">اختر الحساب من الشجرة</label>
                                <select id="stmt_account_select" onchange="applyStatementFilters()" class="w-full rounded-xl border border-emerald-300 px-2.5 py-2 text-xs bg-emerald-50/40 font-bold">
                                    <option value="">-- كل الحسابات --</option>
                                    @foreach($leafAccountsList as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-bold text-slate-700 mb-1"> فلترة حسب مكتب عقار</label>
                                <select id="stmt_office_select" onchange="applyStatementFilters()" class="w-full rounded-xl border border-slate-300 px-2.5 py-2 text-xs bg-white font-semibold">
                                    <option value="">-- جميع المكاتب والإدارة --</option>
                                    @foreach($offices as $off)
                                        <option value="{{ $off->id }}">{{ $off->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">من تاريخ</label>
                                <input type="date" id="stmt_from_date" onchange="applyStatementFilters()" class="w-full rounded-xl border border-slate-300 px-2.5 py-2 text-xs font-mono bg-white">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">إلى تاريخ</label>
                                <input type="date" id="stmt_to_date" onchange="applyStatementFilters()" class="w-full rounded-xl border border-slate-300 px-2.5 py-2 text-xs font-mono bg-white">
                            </div>
                        </div>
                    </div>

                    <!-- القسم 1: جدول دفتر الأستاذ العام المطور (مع الرصيد الافتتاحي والختامي والطرف المقابل) -->
                    <div id="ledger_view_section" class="p-6 space-y-4">
                        <!-- شريط ملخص الفترة المحاسبية (الرصيد الافتتاحي + حركة الفترة + الرصيد الختامي) -->
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 p-4 rounded-2xl bg-slate-900 text-white text-xs">
                            <div class="p-2.5 rounded-xl bg-white/5 border border-white/10">
                                <span class="text-slate-400 block text-[11px]">الرصيد الافتتاحي قبل الفترة:</span>
                                <b id="ledgerOpeningBalance" class="font-mono text-base text-sky-400">0.00</b> <span class="text-[10px]">ر.س</span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-white/5 border border-white/10">
                                <span class="text-slate-400 block text-[11px]">إجمالي مدين الفترة (+):</span>
                                <b id="filteredDebitSum" class="font-mono text-base text-emerald-400">0.00</b> <span class="text-[10px]">ر.س</span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-white/5 border border-white/10">
                                <span class="text-slate-400 block text-[11px]">إجمالي دائن الفترة (-):</span>
                                <b id="filteredCreditSum" class="font-mono text-base text-amber-400">0.00</b> <span class="text-[10px]">ر.س</span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-white/5 border border-white/10">
                                <span class="text-slate-400 block text-[11px]">صافي حركة الفترة:</span>
                                <b id="filteredNetSum" class="font-mono text-base text-white">0.00</b> <span class="text-[10px]">ر.س</span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-emerald-500/20 border border-emerald-400/40">
                                <span class="text-emerald-300 block text-[11px] font-bold">الرصيد الختامي التراكمي:</span>
                                <b id="ledgerClosingBalance" class="font-mono text-base text-emerald-300">0.00</b> <span class="text-[10px]">ر.س</span>
                            </div>
                        </div>

                        <div class="overflow-x-auto rounded-2xl border border-slate-200">
                            <table class="w-full text-xs text-right" id="generalStatementTable">
                                <thead class="bg-slate-100 text-slate-700 border-b border-slate-200">
                                    <tr>
                                        <th class="py-3.5 px-3">التاريخ</th>
                                        <th class="py-3.5 px-3">رقم المرجع</th>
                                        <th class="py-3.5 px-3">المستند</th>
                                        <th class="py-3.5 px-3">الحساب في الأستاذ العام</th>
                                        <th class="py-3.5 px-3">الحساب المقابل / الطرف</th>
                                        <th class="py-3.5 px-3">البيان والشرح</th>
                                        <th class="py-3.5 px-3 text-emerald-700">مدين (Debit)</th>
                                        <th class="py-3.5 px-3 text-amber-700">دائن (Credit)</th>
                                        <th class="py-3.5 px-3 bg-slate-200/70 text-slate-900">الرصيد التراكمي</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($allMovements as $mv)
                                        @php
                                            $accObj = $accounts->firstWhere('id', $mv->account_id);
                                            $isExp = $accObj && (str_contains($accObj->type, 'مصروف') || str_starts_with($accObj->code, '5'));
                                            $isRev = $accObj && (str_contains($accObj->type, 'إيراد') || str_starts_with($accObj->code, '4'));
                                            $searchText = mb_strtolower(($mv->date ?? '') . ' ' . ($mv->ref ?? '') . ' ' . ($mv->source ?? '') . ' ' . ($mv->account_code ?? '') . ' ' . ($mv->account_name ?? '') . ' ' . ($mv->party ?? '') . ' ' . ($mv->notes ?? '') . ' ' . ($mv->debit ?? '') . ' ' . ($mv->credit ?? ''));
                                        @endphp
                                        <tr class="stmt-movement-row hover:bg-emerald-50/40 transition"
                                            data-search="{{ $searchText }}"
                                            data-date="{{ $mv->date }}"
                                            data-account-id="{{ $mv->account_id }}"
                                            data-office-id="{{ $mv->office_id ?? '' }}"
                                            data-debit="{{ $mv->debit }}"
                                            data-credit="{{ $mv->credit }}"
                                            data-is-expense="{{ $isExp ? '1' : '0' }}"
                                            data-is-revenue="{{ $isRev ? '1' : '0' }}">
                                            <td class="py-3 px-3 font-mono text-slate-600">{{ $mv->date }}</td>
                                            <td class="py-3 px-3 font-mono font-bold text-slate-900">{{ $mv->ref }}</td>
                                            <td class="py-3 px-3">
                                                <span class="px-2 py-0.5 rounded-lg text-[11px] font-bold {{ $mv->source == 'قيد يومي' ? 'bg-indigo-50 text-indigo-700' : ($mv->source == 'سند قبض' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-800') }}">
                                                    {{ $mv->source }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-3">
                                                <button type="button" onclick="quickFilterByAccount('{{ $mv->account_id }}')" class="font-bold text-slate-900 hover:text-emerald-600 underline decoration-dotted text-right">
                                                    <span class="font-mono text-emerald-700">{{ $mv->account_code }}</span> - {{ $mv->account_name }}
                                                </button>
                                            </td>
                                            <td class="py-3 px-3 text-[11px] text-slate-600">
                                                <div class="font-bold text-slate-800">{{ $mv->party }}</div>
                                                <div class="text-slate-400">{{ $mv->opposite_account }}</div>
                                            </td>
                                            <td class="py-3 px-3 text-slate-700 font-semibold">{{ $mv->notes }}</td>
                                            <td class="py-3 px-3 font-mono font-bold text-emerald-700">{{ $mv->debit > 0 ? number_format($mv->debit, 2) : '-' }}</td>
                                            <td class="py-3 px-3 font-mono font-bold text-amber-700">{{ $mv->credit > 0 ? number_format($mv->credit, 2) : '-' }}</td>
                                            <td class="py-3 px-3 font-mono font-extrabold bg-slate-50 dynamic-running-cell {{ $mv->running_balance >= 0 ? 'text-slate-900' : 'text-rose-600' }}">
                                                {{ number_format($mv->running_balance, 2) }} ر.س
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="9" class="py-12 text-center text-slate-400">لا توجد حركات مالية مسجلة في الأستاذ العام حتى الآن.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- القسم 2: بطاقات الأستاذ العام لكل حساب على حدة (T-Accounts) -->
                    <div id="cards_view_section" class="hidden p-6 space-y-4">
                        <h4 class="font-bold text-sm text-slate-900">🗂️ بطاقات حسابات الأستاذ العام التفصيلية (حسب كل حساب)</h4>
                        <div class="grid grid-cols-1 gap-4">
                            @foreach($leafAccountsList as $acc)
                                @php
                                    $accMovements = $movementsByAccount[$acc->id] ?? collect();
                                    $accDebitSum = $accMovements->sum('debit') ?: ($acc->debit_balance ?? 0);
                                    $accCreditSum = $accMovements->sum('credit') ?: ($acc->credit_balance ?? 0);
                                    $isExp = str_contains($acc->type, 'مصروف') || str_starts_with($acc->code, '5');
                                    $isRev = str_contains($acc->type, 'إيراد') || str_starts_with($acc->code, '4');
                                    $cardSearch = mb_strtolower($acc->code . ' ' . $acc->name . ' ' . $acc->type);
                                @endphp
                                <div class="ledger-account-card rounded-2xl border border-slate-200 overflow-hidden bg-white shadow-sm"
                                     data-search="{{ $cardSearch }}"
                                     data-account-id="{{ $acc->id }}"
                                     data-is-expense="{{ $isExp ? '1' : '0' }}"
                                     data-is-revenue="{{ $isRev ? '1' : '0' }}">
                                    <div class="px-5 py-3.5 bg-slate-900 text-white flex flex-wrap items-center justify-between gap-3">
                                        <div class="flex items-center gap-2.5">
                                            <span class="px-2.5 py-1 rounded-lg bg-emerald-500 text-slate-950 font-mono text-xs font-extrabold">{{ $acc->code }}</span>
                                            <span class="font-bold text-sm">{{ $acc->name }}</span>
                                            <span class="text-[11px] text-slate-400">({{ $acc->type }})</span>
                                        </div>
                                        <div class="flex items-center gap-4 text-xs font-mono">
                                            <span>مدين: <b class="text-emerald-400">{{ number_format($accDebitSum, 2) }}</b></span>
                                            <span>دائن: <b class="text-amber-400">{{ number_format($accCreditSum, 2) }}</b></span>
                                            <span class="px-3 py-1 rounded-xl bg-white/10 text-white font-bold">الرصيد: {{ number_format($acc->balance, 2) }} ر.س</span>
                                        </div>
                                    </div>
                                    @if($accMovements->count() > 0)
                                        <div class="overflow-x-auto">
                                            <table class="w-full text-xs text-right">
                                                <thead class="bg-slate-50 text-slate-600 border-b">
                                                    <tr>
                                                        <th class="py-2 px-4">التاريخ</th>
                                                        <th class="py-2 px-4">المرجع</th>
                                                        <th class="py-2 px-4">الطرف المرتبط</th>
                                                        <th class="py-2 px-4">البيان</th>
                                                        <th class="py-2 px-4">مدين</th>
                                                        <th class="py-2 px-4">دائن</th>
                                                        <th class="py-2 px-4">الرصيد التراكمي</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-slate-100">
                                                    @foreach($accMovements as $am)
                                                        <tr>
                                                            <td class="py-2 px-4 font-mono">{{ $am->date }}</td>
                                                            <td class="py-2 px-4 font-mono font-bold">{{ $am->ref }}</td>
                                                            <td class="py-2 px-4 font-semibold text-slate-700">{{ $am->party }}</td>
                                                            <td class="py-2 px-4">{{ $am->notes }}</td>
                                                            <td class="py-2 px-4 font-mono font-bold text-emerald-700">{{ $am->debit > 0 ? number_format($am->debit, 2) : '-' }}</td>
                                                            <td class="py-2 px-4 font-mono font-bold text-amber-700">{{ $am->credit > 0 ? number_format($am->credit, 2) : '-' }}</td>
                                                            <td class="py-2 px-4 font-mono font-bold">{{ number_format($am->running_balance, 2) }} ر.س</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="p-4 text-xs text-slate-400 text-center">لا توجد حركات مسجلة على هذا الحساب بعد.</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- القسم 3: ميزان المراجعة بالأرصدة والمجاميع (Trial Balance) -->
                    <div id="trial_view_section" class="hidden p-6">
                        <h4 class="font-bold text-sm text-slate-900 mb-3">⚖️ ميزان المراجعة العام (بالمجاميع والأرصدة)</h4>
                        <div class="overflow-x-auto rounded-2xl border border-slate-200">
                            <table class="w-full text-xs text-right">
                                <thead class="bg-slate-900 text-white">
                                    <tr>
                                        <th class="py-3.5 px-4">كود الحساب</th>
                                        <th class="py-3.5 px-4">اسم الحساب في الأستاذ العام</th>
                                        <th class="py-3.5 px-4">القائمة المالية</th>
                                        <th class="py-3.5 px-4">مجموع المدين</th>
                                        <th class="py-3.5 px-4">مجموع الدائن</th>
                                        <th class="py-3.5 px-4 bg-emerald-950">رصيد مدين</th>
                                        <th class="py-3.5 px-4 bg-amber-950">رصيد دائن</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @php
                                        $tbTotalDebit = 0; $tbTotalCredit = 0;
                                        $tbBalDebit = 0; $tbBalCredit = 0;
                                    @endphp
                                    @foreach($leafAccountsList as $acc)
                                        @php
                                            $isExp = str_contains($acc->type, 'مصروف') || str_starts_with($acc->code, '5');
                                            $isRev = str_contains($acc->type, 'إيراد') || str_starts_with($acc->code, '4');
                                            $dVal = (float) ($acc->debit_balance ?? ($acc->balance > 0 ? $acc->balance : 0));
                                            $cVal = (float) ($acc->credit_balance ?? ($acc->balance < 0 ? abs($acc->balance) : 0));
                                            $netB = (float) $acc->balance;
                                            $bDebit = $netB > 0 ? $netB : 0;
                                            $bCredit = $netB < 0 ? abs($netB) : 0;
                                            $tbTotalDebit += $dVal; $tbTotalCredit += $cVal;
                                            $tbBalDebit += $bDebit; $tbBalCredit += $bCredit;
                                        @endphp
                                        <tr class="stmt-summary-row hover:bg-slate-50"
                                            data-search="{{ mb_strtolower($acc->code . ' ' . $acc->name . ' ' . $acc->type) }}"
                                            data-account-id="{{ $acc->id }}"
                                            data-is-expense="{{ $isExp ? '1' : '0' }}"
                                            data-is-revenue="{{ $isRev ? '1' : '0' }}">
                                            <td class="py-3 px-4 font-mono font-bold">{{ $acc->code }}</td>
                                            <td class="py-3 px-4 font-bold text-slate-900">
                                                <button type="button" onclick="quickFilterByAccount('{{ $acc->id }}')" class="hover:text-emerald-600 underline decoration-dotted">
                                                    {{ $acc->name }}
                                                </button>
                                            </td>
                                            <td class="py-3 px-4">
                                                <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $isExp ? 'bg-rose-50 text-rose-700' : ($isRev ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700') }}">
                                                    {{ $acc->type }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 font-mono font-bold text-emerald-700">{{ number_format($dVal, 2) }}</td>
                                            <td class="py-3 px-4 font-mono font-bold text-amber-700">{{ number_format($cVal, 2) }}</td>
                                            <td class="py-3 px-4 font-mono font-extrabold text-emerald-800 bg-emerald-50/40">{{ $bDebit > 0 ? number_format($bDebit, 2) : '0.00' }}</td>
                                            <td class="py-3 px-4 font-mono font-extrabold text-amber-800 bg-amber-50/40">{{ $bCredit > 0 ? number_format($bCredit, 2) : '0.00' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-slate-100 font-extrabold text-slate-900 border-t-2 border-slate-300">
                                    <tr>
                                        <td colspan="3" class="py-3 px-4">الإجمالي الكلي لميزان المراجعة</td>
                                        <td class="py-3 px-4 font-mono text-emerald-700">{{ number_format($tbTotalDebit, 2) }} ر.س</td>
                                        <td class="py-3 px-4 font-mono text-amber-700">{{ number_format($tbTotalCredit, 2) }} ر.س</td>
                                        <td class="py-3 px-4 font-mono text-emerald-800">{{ number_format($tbBalDebit, 2) }} ر.س</td>
                                        <td class="py-3 px-4 font-mono text-amber-800">{{ number_format($tbBalCredit, 2) }} ر.س</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- القسم 4: قائمة الدخل وتفصيل المصروفات (Income Statement) -->
                    <div id="income_view_section" class="hidden p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="rounded-2xl border-2 border-rose-200 overflow-hidden">
                                <div class="bg-rose-600 text-white px-5 py-3.5 flex items-center justify-between">
                                    <span class="font-bold text-sm">🔥 تفصيل المصروفات (اضغط لعرض الحركات)</span>
                                    <span class="font-mono font-extrabold text-sm">{{ number_format($totalExpenses, 2) }} ر.س</span>
                                </div>
                                <table class="w-full text-xs text-right bg-white">
                                    <thead class="bg-rose-50 text-rose-900 border-b">
                                        <tr>
                                            <th class="py-2.5 px-4">رقم الحساب</th>
                                            <th class="py-2.5 px-4">بند المصروف</th>
                                            <th class="py-2.5 px-4">الإجمالي</th>
                                            <th class="py-2.5 px-4 text-center">كشف تفصيلي</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y">
                                        @forelse($expenseAccounts as $exp)
                                            @php $expVal = abs(($exp->debit_balance ?? 0) - ($exp->credit_balance ?? 0)) ?: abs($exp->balance); @endphp
                                            <tr class="hover:bg-rose-50/40">
                                                <td class="py-3 px-4 font-mono font-bold">{{ $exp->code }}</td>
                                                <td class="py-3 px-4 font-bold text-slate-900">{{ $exp->name }}</td>
                                                <td class="py-3 px-4 font-mono font-extrabold text-rose-600">{{ number_format($expVal, 2) }} ر.س</td>
                                                <td class="py-3 px-4 text-center">
                                                    <button type="button" onclick="quickFilterSpecificExpense('{{ $exp->id }}')"
                                                            class="px-3 py-1 rounded-xl bg-rose-100 hover:bg-rose-600 text-rose-800 hover:text-white font-bold text-[11px] transition">
                                                        🔍 عرض الحركات
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="py-6 text-center text-slate-400">لا توجد حسابات مصروفات بعد.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="rounded-2xl border-2 border-emerald-200 overflow-hidden">
                                <div class="bg-emerald-600 text-white px-5 py-3.5 flex items-center justify-between">
                                    <span class="font-bold text-sm">💰 تفصيل الإيرادات التشغيلية</span>
                                    <span class="font-mono font-extrabold text-sm">{{ number_format($totalRevenues, 2) }} ر.س</span>
                                </div>
                                <table class="w-full text-xs text-right bg-white">
                                    <thead class="bg-emerald-50 text-emerald-900 border-b">
                                        <tr>
                                            <th class="py-2.5 px-4">رقم الحساب</th>
                                            <th class="py-2.5 px-4">بند الإيراد</th>
                                            <th class="py-2.5 px-4">الإجمالي</th>
                                            <th class="py-2.5 px-4 text-center">كشف تفصيلي</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y">
                                        @forelse($revenueAccounts as $rev)
                                            @php $revVal = abs(($rev->credit_balance ?? 0) - ($rev->debit_balance ?? 0)) ?: abs($rev->balance); @endphp
                                            <tr class="hover:bg-emerald-50/40">
                                                <td class="py-3 px-4 font-mono font-bold">{{ $rev->code }}</td>
                                                <td class="py-3 px-4 font-bold text-slate-900">{{ $rev->name }}</td>
                                                <td class="py-3 px-4 font-mono font-extrabold text-emerald-600">{{ number_format($revVal, 2) }} ر.س</td>
                                                <td class="py-3 px-4 text-center">
                                                    <button type="button" onclick="quickFilterByAccount('{{ $rev->id }}')"
                                                            class="px-3 py-1 rounded-xl bg-emerald-100 hover:bg-emerald-600 text-emerald-800 hover:text-white font-bold text-[11px] transition">
                                                        🔍 عرض الحركات
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="py-6 text-center text-slate-400">لا توجد حسابات إيرادات بعد.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            @endif

            <!-- 6. صفحة إعدادات الربط واتساب API (للأدمن ولتحديد الرقم الافتراضي للمكاتب) -->
            @if($tab == 'whatsapp')
                <div class="max-w-2xl bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <h3 class="font-bold text-base text-slate-900">💬 إعدادات الربط مع بوابة واتساب (WhatsApp API)</h3>
                            <p class="text-xs text-slate-500 mt-0.5">يُستخدم هذا الربط لإرسال أكواد التحقق (OTP) وسندات القبض والصرف، ويكون مفعلاً افتراضياً لجميع المكاتب المضافة</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ !empty($whatsappSettings['api_token']) ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ !empty($whatsappSettings['api_token']) ? 'الربط مفعل ✔' : 'بانتظار إدخال التوكن' }}
                        </span>
                    </div>

                    <form action="{{ route('erp.whatsapp.save') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">رابط بوابة الواتساب (API Endpoint URL) *</label>
                            <input type="url" name="api_url" value="{{ $whatsappSettings['api_url'] ?? 'https://api.greatwhats.com/v1/messages' }}" required dir="ltr"
                                   placeholder="https://api.example.com/send"
                                   class="w-full text-left font-mono rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">مفتاح الربط السري (API Token / Secret Key) *</label>
                            <input type="text" name="api_token" value="{{ $whatsappSettings['api_token'] ?? '' }}" required dir="ltr"
                                   placeholder="أدخل التوكن الخاص ببوابة الواتساب هنا..."
                                   class="w-full text-left font-mono rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">معرف الجلسة / Instance ID (اختياري)</label>
                                <input type="text" name="instance_id" value="{{ $whatsappSettings['instance_id'] ?? '' }}" dir="ltr"
                                       placeholder="instance_12345"
                                       class="w-full text-left font-mono rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">رقم جوال الإدارة المعتمد لإرسال الأكواد للمكاتب *</label>
                                <input type="tel" name="sender_phone" value="{{ $whatsappSettings['sender_phone'] ?? '0500000000' }}" maxlength="10" required dir="ltr"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                                       class="w-full text-left font-mono rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 text-xs text-emerald-900 space-y-1">
                            <p class="font-bold">📌 ملاحظة حول مكاتب العقار:</p>
                            <p>أي مكتب عقار جديد يتم إضافته سيرتبط تلقائياً برقم الإدارة وإعدادات الـ API هذه كإعداد افتراضي لاستلام الأكواد وإرسال الإشعارات، مع إمكانية قيام المكتب بربط رقمه الخاص من داخل بوابته متى ما رغب بذلك.</p>
                        </div>

                        <button type="submit" class="px-6 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-lg shadow-emerald-600/20">
                            حفظ وتفعيل إعدادات واتساب API
                        </button>
                    </form>
                </div>
            @endif

            <!-- 7. إعدادات الحساب الشخصي + إعدادات نسبة الضريبة المركزية -->
            @if($tab == 'settings')
                @php
                    $myLocalPhone = \App\Http\Controllers\CompanyErpController::toLocalPhone(auth()->user()->phone);
                    $systemVatRate = $companyTaxProfile['default_vat_rate'] ?? 15;
                @endphp
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
                    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
                        <h3 class="font-bold text-base text-slate-900 mb-1">تعديل بيانات الدخول الخاصة بك</h3>
                        <p class="text-xs text-slate-500 mb-5">تحديث رقم الجوال أو اسم المستخدم وكلمة المرور الاحتياطية</p>

                        <form action="{{ route('erp.profile.update') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">الاسم الكامل *</label>
                                <input type="text" name="name" value="{{ auth()->user()->name }}" required class="w-full rounded-2xl border border-slate-300 px-3.5 py-2.5 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">رقم الجوال (10 أرقام يبدأ بـ 05) *</label>
                                <input type="tel" name="phone" value="{{ $myLocalPhone }}" maxlength="10" required dir="ltr"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                                       class="w-full text-left font-mono rounded-2xl border border-slate-300 px-3.5 py-2.5 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">اسم المستخدم *</label>
                                <input type="text" name="username" value="{{ auth()->user()->username }}" required class="w-full rounded-2xl border border-slate-300 px-3.5 py-2.5 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">كلمة المرور الجديدة (اتركها فارغة إذا لا ترغب بتغييرها)</label>
                                <input type="password" name="password" placeholder="••••••••" class="w-full rounded-2xl border border-slate-300 px-3.5 py-2.5 text-sm">
                            </div>
                            <button type="submit" class="px-6 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-sm shadow-lg shadow-emerald-600/20">
                                حفظ وتحديث بيانات الدخول
                            </button>
                        </form>
                    </div>

                    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
                        <h3 class="font-bold text-base text-slate-900 mb-1">⚙️ إعدادات نسبة الضريبة والبيانات الضريبية للنظام</h3>
                        <p class="text-xs text-slate-500 mb-5">تعديل نسبة ضريبة القيمة المضافة يطبق على الفواتير الجديدة لدى الجميع دون أثر رجعي</p>

                        <form action="{{ route('erp.tax_profile.save') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200">
                                <label class="block text-xs font-bold text-emerald-950 mb-1">نسبة ضريبة القيمة المضافة المعتمدة في النظام (%) *</label>
                                <input type="number" step="0.01" min="0" max="100" name="default_vat_rate" value="{{ $systemVatRate }}" required
                                       class="w-full rounded-xl border border-emerald-400 px-3.5 py-2.5 text-sm font-mono font-extrabold text-emerald-900 bg-white">
                                <span class="text-[11px] text-emerald-700 block mt-1">مثال: إذا عدلتها إلى 5 ستصبح 5% عند الشركة وجميع المكاتب للفواتير الجديدة فقط.</span>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">الاسم القانوني للشركة *</label>
                                <input type="text" name="legal_name" value="{{ $companyTaxProfile['legal_name'] ?? '' }}" required class="w-full rounded-2xl border border-slate-300 px-3.5 py-2 text-sm">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">الرقم الضريبي للشركة *</label>
                                    <input type="text" name="tax_number" value="{{ $companyTaxProfile['tax_number'] ?? '' }}" required maxlength="15" class="w-full rounded-2xl border border-slate-300 px-3.5 py-2 text-sm font-mono">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">السجل التجاري</label>
                                    <input type="text" name="cr_number" value="{{ $companyTaxProfile['cr_number'] ?? '' }}" class="w-full rounded-2xl border border-slate-300 px-3.5 py-2 text-sm font-mono">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">العنوان الوطني</label>
                                <input type="text" name="national_address" value="{{ $companyTaxProfile['national_address'] ?? '' }}" class="w-full rounded-2xl border border-slate-300 px-3.5 py-2 text-sm">
                            </div>
                            <button type="submit" class="px-6 py-3 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-sm shadow-lg">
                                💾 حفظ نسبة الضريبة والبيانات الضريبية
                            </button>
                        </form>
                    </div>
                </div>

                <!-- قسم إدارة وتعديل محتوى الواجهة الرئيسية للموقع بالكامل + SEO + ترتيب الأقسام -->
                @php
                    $ls = $landingSettings ?? \App\Http\Controllers\CompanyErpController::getLandingSettings();
                @endphp
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 mt-6">
                    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4 mb-6">
                        <div>
                            <h3 class="font-bold text-lg text-slate-900">🌐 نظام إدارة الواجهة الرئيسية الشامل (Full Landing CMS & Pro SEO)</h3>
                            <p class="text-xs text-slate-500 mt-0.5">تحكم كامل بجميع النصوص، ترتيب الأقسام، الإحصائيات، المميزات، البوابات الذكية، الباقات، الأقسام الإضافية، ومحرك البحث SEO</p>
                        </div>
                        <a href="{{ route('landing') }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold">
                            👁️ معاينة الواجهة الرئيسية الحية
                        </a>
                    </div>

                    <form action="{{ route('erp.landing.save') }}" method="POST" class="space-y-8">
                        @csrf

                        <!-- 1. إعدادات محركات البحث المتقدمة (SEO Power Pack) -->
                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-200 pb-2.5">
                                <div>
                                    <h4 class="font-bold text-sm text-slate-900">🚀 أولاً: إعدادات الظهور القوي في محركات البحث (Google SEO & OpenGraph)</h4>
                                    <p class="text-[11px] text-slate-500">تتحكم هذه الحقول في أرشفة موقعك في جوجل، وكيفية ظهور الرابط عند مشاركته في واتساب وتويتر</p>
                                </div>
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 text-[11px] font-bold">Schema JSON-LD مفعل تلقائياً ✔</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-slate-700 mb-1">عنوان الصفحة في محرك البحث جوجل (SEO Meta Title) *</label>
                                    <input type="text" name="seo_title" value="{{ $ls['seo_title'] ?? '' }}" required class="w-full rounded-xl border px-3.5 py-2 text-xs bg-white font-bold">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-slate-700 mb-1">الوصف التسويقي في نتائج بحث جوجل (SEO Meta Description) *</label>
                                    <textarea name="seo_description" rows="2" required class="w-full rounded-xl border px-3.5 py-2 text-xs bg-white">{{ $ls['seo_description'] ?? '' }}</textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-slate-700 mb-1">الكلمات المفتاحية المستهدفة (SEO Keywords - مفصولة بفاصلة)</label>
                                    <input type="text" name="seo_keywords" value="{{ $ls['seo_keywords'] ?? '' }}" class="w-full rounded-xl border px-3.5 py-2 text-xs bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">رابط الصورة التسويقية عند مشاركة الرابط (OG Image URL - اختياري)</label>
                                    <input type="text" name="seo_og_image" value="{{ $ls['seo_og_image'] ?? '' }}" dir="ltr" placeholder="https://example.com/banner.jpg" class="w-full rounded-xl border px-3 py-2 text-xs font-mono bg-white">
                                </div>
                                <div class="grid grid-cols-2 gap-2.5">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">الرابط الأساسي (Canonical URL)</label>
                                        <input type="text" name="seo_canonical_url" value="{{ $ls['seo_canonical_url'] ?? url('/') }}" dir="ltr" class="w-full rounded-xl border px-3 py-2 text-xs font-mono bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">أوامر عناكب البحث (Robots)</label>
                                        <select name="seo_robots" class="w-full rounded-xl border px-3 py-2 text-xs bg-white font-mono">
                                            <option value="index, follow" {{ ($ls['seo_robots'] ?? '') == 'index, follow' ? 'selected' : '' }}>index, follow (أرشفة كاملة)</option>
                                            <option value="noindex, nofollow" {{ ($ls['seo_robots'] ?? '') == 'noindex, nofollow' ? 'selected' : '' }}>noindex, nofollow (إخفاء من البحث)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. التحكم في ترتيب ظهور الأقسام وإظهارها أو إخفائها -->
                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                            <h4 class="font-bold text-sm text-slate-900">🔢 ثانياً: تحديد ترتيب ظهور الأقسام في الصفحة (من 1 إلى 5) وإمكانية إخفاء أي قسم</h4>
                            <p class="text-[11px] text-slate-500">ضع رقم الترتيب لكل قسم (1 يظهر أولاً، 2 يظهر ثانياً...) أو ألغِ علامة الصح لإخفاء القسم تماماً من الواجهة</p>

                            <div class="grid grid-cols-1 md:grid-cols-5 gap-3 pt-1">
                                <div class="p-3 rounded-xl bg-white border border-slate-200 space-y-2">
                                    <label class="flex items-center gap-2 text-xs font-bold text-slate-800 cursor-pointer">
                                        <input type="checkbox" name="show_stats" value="1" {{ ($ls['show_stats'] ?? 1) ? 'checked' : '' }}>
                                        <span>إظهار شريط الإحصائيات</span>
                                    </label>
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-slate-500">ترتيب الظهور:</span>
                                        <input type="number" name="order_stats" value="{{ $ls['order_stats'] ?? 1 }}" min="1" max="10" class="w-16 text-center rounded-lg border px-2 py-1 font-mono font-bold">
                                    </div>
                                </div>

                                <div class="p-3 rounded-xl bg-white border border-slate-200 space-y-2">
                                    <label class="flex items-center gap-2 text-xs font-bold text-slate-800 cursor-pointer">
                                        <input type="checkbox" name="show_features" value="1" {{ ($ls['show_features'] ?? 1) ? 'checked' : '' }}>
                                        <span>إظهار قسم المميزات</span>
                                    </label>
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-slate-500">ترتيب الظهور:</span>
                                        <input type="number" name="order_features" value="{{ $ls['order_features'] ?? 2 }}" min="1" max="10" class="w-16 text-center rounded-lg border px-2 py-1 font-mono font-bold">
                                    </div>
                                </div>

                                <div class="p-3 rounded-xl bg-white border border-slate-200 space-y-2">
                                    <label class="flex items-center gap-2 text-xs font-bold text-slate-800 cursor-pointer">
                                        <input type="checkbox" name="show_portals" value="1" {{ ($ls['show_portals'] ?? 1) ? 'checked' : '' }}>
                                        <span>إظهار البوابات الذكية</span>
                                    </label>
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-slate-500">ترتيب الظهور:</span>
                                        <input type="number" name="order_portals" value="{{ $ls['order_portals'] ?? 3 }}" min="1" max="10" class="w-16 text-center rounded-lg border px-2 py-1 font-mono font-bold">
                                    </div>
                                </div>

                                <div class="p-3 rounded-xl bg-white border border-slate-200 space-y-2">
                                    <label class="flex items-center gap-2 text-xs font-bold text-slate-800 cursor-pointer">
                                        <input type="checkbox" name="show_plans" value="1" {{ ($ls['show_plans'] ?? 1) ? 'checked' : '' }}>
                                        <span>إظهار قسم الاشتراكات</span>
                                    </label>
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-slate-500">ترتيب الظهور:</span>
                                        <input type="number" name="order_plans" value="{{ $ls['order_plans'] ?? 4 }}" min="1" max="10" class="w-16 text-center rounded-lg border px-2 py-1 font-mono font-bold">
                                    </div>
                                </div>

                                <div class="p-3 rounded-xl bg-white border border-slate-200 space-y-2">
                                    <label class="flex items-center gap-2 text-xs font-bold text-slate-800 cursor-pointer">
                                        <input type="checkbox" name="show_custom" value="1" {{ ($ls['show_custom'] ?? 1) ? 'checked' : '' }}>
                                        <span>إظهار الأقسام الإضافية</span>
                                    </label>
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-slate-500">ترتيب الظهور:</span>
                                        <input type="number" name="order_custom" value="{{ $ls['order_custom'] ?? 5 }}" min="1" max="10" class="w-16 text-center rounded-lg border px-2 py-1 font-mono font-bold">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. الهوية والنصوص الرئيسية (Hero & Contact) -->
                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                            <h4 class="font-bold text-sm text-slate-900 border-b border-slate-200 pb-2">🏛️ ثالثاً: النصوص الترحيبية العليا (Hero Section) وأرقام التواصل</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">اسم المنصة الرئيسي *</label>
                                    <input type="text" name="site_name" value="{{ $ls['site_name'] }}" required class="w-full rounded-xl border px-3 py-2 text-xs bg-white font-bold">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">الوصف المختصر تحت الشعار</label>
                                    <input type="text" name="site_sub" value="{{ $ls['site_sub'] ?? 'ERP & Property Management System' }}" class="w-full rounded-xl border px-3 py-2 text-xs bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">الشريط الترحيبي العلوي (Badge)</label>
                                    <input type="text" name="badge_text" value="{{ $ls['badge_text'] }}" class="w-full rounded-xl border px-3 py-2 text-xs bg-white">
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-xs font-bold text-slate-700 mb-1">العنوان الرئيسي الكبير (Hero Title) *</label>
                                    <input type="text" name="hero_title" value="{{ $ls['hero_title'] }}" required class="w-full rounded-xl border px-3 py-2 text-sm bg-white font-bold">
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-xs font-bold text-slate-700 mb-1">الوصف التفصيلي تحت العنوان الرئيسي *</label>
                                    <textarea name="hero_subtitle" rows="2" required class="w-full rounded-xl border px-3 py-2 text-xs bg-white">{{ $ls['hero_subtitle'] }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">نص زر الدخول الرئيسي</label>
                                    <input type="text" name="cta_primary_text" value="{{ $ls['cta_primary_text'] }}" class="w-full rounded-xl border px-3 py-2 text-xs bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">نص زر التواصل بالواتساب</label>
                                    <input type="text" name="cta_secondary_text" value="{{ $ls['cta_secondary_text'] }}" class="w-full rounded-xl border px-3 py-2 text-xs bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">رقم الجوال المعروض بالواجهة</label>
                                    <input type="text" name="contact_phone" value="{{ $ls['contact_phone'] }}" dir="ltr" class="w-full rounded-xl border px-3 py-2 text-xs font-mono bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">رقم واتساب الاستفسارات (مع 966)</label>
                                    <input type="text" name="contact_whatsapp" value="{{ $ls['contact_whatsapp'] }}" dir="ltr" class="w-full rounded-xl border px-3 py-2 text-xs font-mono bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">البريد الإلكتروني</label>
                                    <input type="text" name="contact_email" value="{{ $ls['contact_email'] }}" dir="ltr" class="w-full rounded-xl border px-3 py-2 text-xs font-mono bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">نص الحقوق في الفوتر (Footer)</label>
                                    <input type="text" name="footer_text" value="{{ $ls['footer_text'] }}" class="w-full rounded-xl border px-3 py-2 text-xs bg-white">
                                </div>
                            </div>
                        </div>

                        <!-- 4. إدارة بطاقات الإحصائيات (Stats Bar) -->
                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-slate-900">📊 رابعاً: بطاقات شريط الإحصائيات والأرقام (تعديل أو إضافة أو حذف):</span>
                                <button type="button" onclick="addLandingStatRow()" class="px-3.5 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-bold">
                                    + إضافة بطاقة إحصائية
                                </button>
                            </div>
                            <div id="landingStatsContainer" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                @foreach(($ls['stats'] ?? []) as $st)
                                    <div class="p-3 rounded-xl bg-white border border-slate-200 space-y-2 landing-stat-item">
                                        <div class="flex items-center justify-between gap-2">
                                            <input type="text" name="stat_numbers[]" value="{{ $st['number'] ?? '' }}" placeholder="الرقم (مثال: +50)" class="w-full rounded-lg border px-2.5 py-1.5 text-xs font-mono font-bold">
                                            <button type="button" onclick="this.closest('.landing-stat-item').remove()" class="px-2 py-1 rounded bg-red-50 text-red-600 text-[10px] font-bold">حذف</button>
                                        </div>
                                        <input type="text" name="stat_labels[]" value="{{ $st['label'] ?? '' }}" placeholder="الوصف (مثال: مكتب عقاري)" class="w-full rounded-lg border px-2.5 py-1.5 text-xs">
                                        <input type="hidden" name="stat_colors[]" value="{{ $st['color'] ?? 'emerald' }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- 5. إدارة قسم مميزات وخدمات النظام -->
                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 pb-2.5">
                                <span class="text-sm font-bold text-slate-900">✨ خامساً: قسم مميزات وخدمات المنظومة (العناوين + البطاقات):</span>
                                <button type="button" onclick="addLandingFeatureRow()" class="px-3.5 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-bold">
                                    + إضافة ميزة جديدة
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">الشريط الصغير فوق العنوان</label>
                                    <input type="text" name="features_badge" value="{{ $ls['features_badge'] ?? '' }}" class="w-full rounded-xl border px-3 py-1.5 text-xs bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">العنوان الرئيسي لقسم المميزات</label>
                                    <input type="text" name="features_heading" value="{{ $ls['features_heading'] ?? '' }}" class="w-full rounded-xl border px-3 py-1.5 text-xs bg-white font-bold">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">الوصف الفرعي لقسم المميزات</label>
                                    <input type="text" name="features_subheading" value="{{ $ls['features_subheading'] ?? '' }}" class="w-full rounded-xl border px-3 py-1.5 text-xs bg-white">
                                </div>
                            </div>

                            <div id="landingFeaturesContainer" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($ls['features'] as $feat)
                                    <div class="p-3.5 rounded-2xl bg-white border border-slate-200 space-y-2 landing-feature-item">
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="flex items-center gap-2 flex-1">
                                                <input type="text" name="feature_icons[]" value="{{ $feat['icon'] ?? '✨' }}" class="w-12 text-center rounded-xl border px-2 py-1.5 text-sm bg-slate-50" title="الأيقونة">
                                                <input type="text" name="feature_titles[]" value="{{ $feat['title'] ?? '' }}" required placeholder="عنوان الميزة..." class="flex-1 rounded-xl border px-3 py-1.5 text-xs font-bold">
                                            </div>
                                            <button type="button" onclick="this.closest('.landing-feature-item').remove()" class="px-2.5 py-1 rounded-lg bg-red-50 text-red-600 text-[11px] font-bold">حذف</button>
                                        </div>
                                        <textarea name="feature_descs[]" rows="2" placeholder="شرح الميزة..." class="w-full rounded-xl border px-3 py-1.5 text-xs">{{ $feat['desc'] ?? '' }}</textarea>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- 6. إدارة قسم (4 بوابات ذكية تعمل بتناغم فوري) مع إمكانية التعديل أو الحذف أو الإضافة -->
                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 pb-2.5">
                                <div>
                                    <span class="text-sm font-bold text-slate-900">🚪 سادساً: قسم البوابات الذكية ("4 بوابات ذكية تعمل بتناغم فوري"):</span>
                                    <p class="text-[11px] text-slate-500">يمكنك تعديل العنوان الرئيسي، تعديل أي بوابة، حذفها، أو إضافة بوابات جديدة</p>
                                </div>
                                <button type="button" onclick="addLandingPortalRow()" class="px-3.5 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-bold">
                                    + إضافة بوابة جديدة
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">الشريط الصغير فوق العنوان</label>
                                    <input type="text" name="portals_badge" value="{{ $ls['portals_badge'] ?? 'هيكلية المنصة' }}" class="w-full rounded-xl border px-3 py-1.5 text-xs bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">العنوان الرئيسي لقسم البوابات</label>
                                    <input type="text" name="portals_heading" value="{{ $ls['portals_heading'] ?? '4 بوابات ذكية تعمل بتناغم فوري' }}" class="w-full rounded-xl border px-3 py-1.5 text-xs bg-white font-bold">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">الوصف الفرعي لقسم البوابات</label>
                                    <input type="text" name="portals_subheading" value="{{ $ls['portals_subheading'] ?? '' }}" class="w-full rounded-xl border px-3 py-1.5 text-xs bg-white">
                                </div>
                            </div>

                            <div id="landingPortalsContainer" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach(($ls['portals'] ?? []) as $prt)
                                    <div class="p-3.5 rounded-2xl bg-white border border-slate-200 space-y-2 landing-portal-item">
                                        <div class="flex items-center justify-between gap-2">
                                            <input type="text" name="portal_badges[]" value="{{ $prt['badge'] ?? '' }}" placeholder="الشريط (مثال: 1. الإدارة المركزية)" class="w-1/3 rounded-xl border px-2.5 py-1.5 text-xs bg-slate-50 font-semibold">
                                            <input type="text" name="portal_titles[]" value="{{ $prt['title'] ?? '' }}" required placeholder="اسم البوابة..." class="flex-1 rounded-xl border px-3 py-1.5 text-xs font-bold">
                                            <button type="button" onclick="this.closest('.landing-portal-item').remove()" class="px-2.5 py-1 rounded-lg bg-red-50 text-red-600 text-[11px] font-bold">حذف</button>
                                        </div>
                                        <textarea name="portal_descs[]" rows="2" placeholder="وصف البوابة وصلاحياتها..." class="w-full rounded-xl border px-3 py-1.5 text-xs">{{ $prt['desc'] ?? '' }}</textarea>
                                        <input type="hidden" name="portal_colors[]" value="{{ $prt['color'] ?? 'emerald' }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- 7. إدارة قسم باقات الاشتراك والاتفاقيات (تعديل، حذف، إضافة باقات جديدة، وتحديد الباقة المميزة) -->
                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 pb-2.5">
                                <div>
                                    <span class="text-sm font-bold text-slate-900">📦 سابعاً: قسم الباقات والاشتراكات (إضافة / تعديل / حذف أي باقة):</span>
                                    <p class="text-[11px] text-slate-500">يمكنك حذف أي باقة أو إضافة باقات جديدة وتحديد السعر أو النص التوضيحي لكل باقة</p>
                                </div>
                                <button type="button" onclick="addLandingPlanRow()" class="px-3.5 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-bold">
                                    + إضافة باقة اشتراك جديدة
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">الشريط الصغير فوق العنوان</label>
                                    <input type="text" name="plans_badge" value="{{ $ls['plans_badge'] ?? 'خيارات اشتراك مرنة' }}" class="w-full rounded-xl border px-3 py-1.5 text-xs bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">العنوان الرئيسي لقسم الاشتراكات</label>
                                    <input type="text" name="plans_heading" value="{{ $ls['plans_heading'] ?? '' }}" class="w-full rounded-xl border px-3 py-1.5 text-xs bg-white font-bold">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">الوصف الفرعي لقسم الاشتراكات</label>
                                    <input type="text" name="plans_subheading" value="{{ $ls['plans_subheading'] ?? '' }}" class="w-full rounded-xl border px-3 py-1.5 text-xs bg-white">
                                </div>
                            </div>

                            <div id="landingPlansContainer" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @foreach(($ls['plans'] ?? []) as $pl)
                                    <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-2.5 landing-plan-item">
                                        <div class="flex items-center justify-between gap-2">
                                            <input type="text" name="plan_badges[]" value="{{ $pl['badge'] ?? '' }}" placeholder="شارة الباقة (مثال: الأكثر طلباً)" class="flex-1 rounded-lg border px-2.5 py-1 text-xs bg-slate-50 font-semibold">
                                            <button type="button" onclick="this.closest('.landing-plan-item').remove()" class="px-2.5 py-1 rounded-lg bg-red-50 text-red-600 text-[11px] font-bold">حذف</button>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] text-slate-500 mb-0.5">اسم الباقة *</label>
                                            <input type="text" name="plan_titles[]" value="{{ $pl['title'] ?? '' }}" required class="w-full rounded-xl border px-3 py-1.5 text-xs font-bold">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] text-slate-500 mb-0.5">السعر / نوع الرسوم (اختياري)</label>
                                            <input type="text" name="plan_prices[]" value="{{ $pl['price'] ?? '' }}" placeholder="مثال: 2,500 ر.س / سنوياً أو نسبة 2.5%" class="w-full rounded-xl border px-3 py-1.5 text-xs font-semibold text-emerald-700">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] text-slate-500 mb-0.5">تفاصيل ومميزات الباقة *</label>
                                            <textarea name="plan_descs[]" rows="3" class="w-full rounded-xl border px-3 py-1.5 text-xs">{{ $pl['desc'] ?? '' }}</textarea>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2 pt-1">
                                            <div>
                                                <label class="block text-[10px] text-slate-500 mb-0.5">نص الزر</label>
                                                <input type="text" name="plan_btns[]" value="{{ $pl['btn_text'] ?? 'ابدأ الآن' }}" class="w-full rounded-lg border px-2.5 py-1 text-xs">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] text-slate-500 mb-0.5">تمييز البطاقة؟</label>
                                                <select name="plan_highlights[]" class="w-full rounded-lg border px-2 py-1 text-xs bg-white">
                                                    <option value="0" {{ empty($pl['highlight']) ? 'selected' : '' }}>عادية</option>
                                                    <option value="1" {{ !empty($pl['highlight']) ? 'selected' : '' }}>⭐ مميزة بإطار أخضر</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- 8. إضافة أقسام حرة جديدة كلياً في الواجهة (Custom Sections) -->
                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 pb-2.5">
                                <div>
                                    <span class="text-sm font-bold text-slate-900">➕ ثامناً: أقسام إضافية مخصصة (أضف أي قسم جديد تريده في الواجهة):</span>
                                    <p class="text-[11px] text-slate-500">مثل: "الأسئلة الشائعة"، "لماذا تختارنا؟"، "شركاء النجاح"، أو أي محتوى إضافي</p>
                                </div>
                                <button type="button" onclick="addLandingCustomSection()" class="px-3.5 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-bold">
                                    + إنشاء قسم جديد في الصفحة
                                </button>
                            </div>

                            <div id="landingCustomSectionsContainer" class="space-y-4">
                                @foreach(($ls['custom_sections'] ?? []) as $cs)
                                    <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-3 landing-custom-sec-item">
                                        <div class="flex items-center justify-between gap-2">
                                            <input type="text" name="custom_sec_badges[]" value="{{ $cs['badge'] ?? '' }}" placeholder="شارة القسم (مثال: لماذا نحن؟)" class="w-48 rounded-xl border px-3 py-1.5 text-xs bg-slate-50 font-semibold">
                                            <input type="text" name="custom_sec_titles[]" value="{{ $cs['title'] ?? '' }}" required placeholder="العنوان الرئيسي للقسم الجديد..." class="flex-1 rounded-xl border px-3 py-1.5 text-xs font-bold">
                                            <button type="button" onclick="this.closest('.landing-custom-sec-item').remove()" class="px-3 py-1.5 rounded-xl bg-red-50 text-red-600 text-xs font-bold">حذف القسم</button>
                                        </div>
                                        <div>
                                            <input type="text" name="custom_sec_descs[]" value="{{ $cs['desc'] ?? '' }}" placeholder="وصف تمهيدي تحت عنوان القسم..." class="w-full rounded-xl border px-3 py-1.5 text-xs">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-600 mb-1">عناصر أو بطاقات هذا القسم (اكتب كل عنصر في سطر جديد بصيغة: العنوان | الوصف)</label>
                                            <textarea name="custom_sec_items[]" rows="3" placeholder="حماية سحابية فائقة | نسخ احتياطي يومي وتشفير كامل للبيانات&#10;دعم فني مباشر | فريق متخصص لخدمة مكاتب العقار على مدار الساعة" class="w-full rounded-xl border px-3 py-2 text-xs">{{ $cs['items_raw'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="pt-3 border-t flex justify-end">
                            <button type="submit" class="px-8 py-3.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-lg">
                                💾 حفظ وتطبيق جميع تعديلات الواجهة والـ SEO الآن
                            </button>
                        </div>
                    </form>
                </div>

                <script>
                    function addLandingStatRow() {
                        const c = document.getElementById('landingStatsContainer');
                        if (!c) return;
                        const div = document.createElement('div');
                        div.className = 'p-3 rounded-xl bg-white border border-slate-200 space-y-2 landing-stat-item';
                        div.innerHTML = `
                            <div class="flex items-center justify-between gap-2">
                                <input type="text" name="stat_numbers[]" placeholder="الرقم (مثال: +100)" class="w-full rounded-lg border px-2.5 py-1.5 text-xs font-mono font-bold">
                                <button type="button" onclick="this.closest('.landing-stat-item').remove()" class="px-2 py-1 rounded bg-red-50 text-red-600 text-[10px] font-bold">حذف</button>
                            </div>
                            <input type="text" name="stat_labels[]" placeholder="الوصف..." class="w-full rounded-lg border px-2.5 py-1.5 text-xs">
                            <input type="hidden" name="stat_colors[]" value="emerald">
                        `;
                        c.appendChild(div);
                    }

                    function addLandingFeatureRow() {
                        const c = document.getElementById('landingFeaturesContainer');
                        if (!c) return;
                        const div = document.createElement('div');
                        div.className = 'p-3.5 rounded-2xl bg-white border border-slate-200 space-y-2 landing-feature-item';
                        div.innerHTML = `
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 flex-1">
                                    <input type="text" name="feature_icons[]" value="🌟" class="w-12 text-center rounded-xl border px-2 py-1.5 text-sm bg-slate-50">
                                    <input type="text" name="feature_titles[]" required placeholder="عنوان الميزة الجديدة..." class="flex-1 rounded-xl border px-3 py-1.5 text-xs font-bold">
                                </div>
                                <button type="button" onclick="this.closest('.landing-feature-item').remove()" class="px-2.5 py-1 rounded-lg bg-red-50 text-red-600 text-[11px] font-bold">حذف</button>
                            </div>
                            <textarea name="feature_descs[]" rows="2" placeholder="وصف وتفاصيل الميزة..." class="w-full rounded-xl border px-3 py-1.5 text-xs"></textarea>
                        `;
                        c.appendChild(div);
                    }

                    function addLandingPortalRow() {
                        const c = document.getElementById('landingPortalsContainer');
                        if (!c) return;
                        const div = document.createElement('div');
                        div.className = 'p-3.5 rounded-2xl bg-white border border-slate-200 space-y-2 landing-portal-item';
                        div.innerHTML = `
                            <div class="flex items-center justify-between gap-2">
                                <input type="text" name="portal_badges[]" placeholder="الشريط (مثال: 5. بوابة إضافية)" class="w-1/3 rounded-xl border px-2.5 py-1.5 text-xs bg-slate-50 font-semibold">
                                <input type="text" name="portal_titles[]" required placeholder="اسم البوابة..." class="flex-1 rounded-xl border px-3 py-1.5 text-xs font-bold">
                                <button type="button" onclick="this.closest('.landing-portal-item').remove()" class="px-2.5 py-1 rounded-lg bg-red-50 text-red-600 text-[11px] font-bold">حذف</button>
                            </div>
                            <textarea name="portal_descs[]" rows="2" placeholder="وصف البوابة وصلاحياتها..." class="w-full rounded-xl border px-3 py-1.5 text-xs"></textarea>
                            <input type="hidden" name="portal_colors[]" value="emerald">
                        `;
                        c.appendChild(div);
                    }

                    function addLandingPlanRow() {
                        const c = document.getElementById('landingPlansContainer');
                        if (!c) return;
                        const div = document.createElement('div');
                        div.className = 'p-4 rounded-2xl bg-white border border-slate-200 space-y-2.5 landing-plan-item';
                        div.innerHTML = `
                            <div class="flex items-center justify-between gap-2">
                                <input type="text" name="plan_badges[]" value="باقة جديدة" class="flex-1 rounded-lg border px-2.5 py-1 text-xs bg-slate-50 font-semibold">
                                <button type="button" onclick="this.closest('.landing-plan-item').remove()" class="px-2.5 py-1 rounded-lg bg-red-50 text-red-600 text-[11px] font-bold">حذف</button>
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500 mb-0.5">اسم الباقة *</label>
                                <input type="text" name="plan_titles[]" required placeholder="عنوان الباقة..." class="w-full rounded-xl border px-3 py-1.5 text-xs font-bold">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500 mb-0.5">السعر / نوع الرسوم</label>
                                <input type="text" name="plan_prices[]" placeholder="مثال: 1,500 ر.س / سنوياً" class="w-full rounded-xl border px-3 py-1.5 text-xs font-semibold text-emerald-700">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500 mb-0.5">تفاصيل ومميزات الباقة *</label>
                                <textarea name="plan_descs[]" rows="3" placeholder="اكتب مميزات الباقة..." class="w-full rounded-xl border px-3 py-1.5 text-xs"></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-2 pt-1">
                                <div>
                                    <label class="block text-[10px] text-slate-500 mb-0.5">نص الزر</label>
                                    <input type="text" name="plan_btns[]" value="ابدأ الآن" class="w-full rounded-lg border px-2.5 py-1 text-xs">
                                </div>
                                <div>
                                    <label class="block text-[10px] text-slate-500 mb-0.5">تمييز البطاقة؟</label>
                                    <select name="plan_highlights[]" class="w-full rounded-lg border px-2 py-1 text-xs bg-white">
                                        <option value="0">عادية</option>
                                        <option value="1">⭐ مميزة بإطار أخضر</option>
                                    </select>
                                </div>
                            </div>
                        `;
                        c.appendChild(div);
                    }

                    function addLandingCustomSection() {
                        const c = document.getElementById('landingCustomSectionsContainer');
                        if (!c) return;
                        const div = document.createElement('div');
                        div.className = 'p-4 rounded-2xl bg-white border border-slate-200 space-y-3 landing-custom-sec-item';
                        div.innerHTML = `
                            <div class="flex items-center justify-between gap-2">
                                <input type="text" name="custom_sec_badges[]" placeholder="شارة القسم (مثال: الأسئلة الشائعة)" class="w-48 rounded-xl border px-3 py-1.5 text-xs bg-slate-50 font-semibold">
                                <input type="text" name="custom_sec_titles[]" required placeholder="العنوان الرئيسي للقسم الجديد..." class="flex-1 rounded-xl border px-3 py-1.5 text-xs font-bold">
                                <button type="button" onclick="this.closest('.landing-custom-sec-item').remove()" class="px-3 py-1.5 rounded-xl bg-red-50 text-red-600 text-xs font-bold">حذف القسم</button>
                            </div>
                            <div>
                                <input type="text" name="custom_sec_descs[]" placeholder="وصف تمهيدي تحت عنوان القسم..." class="w-full rounded-xl border px-3 py-1.5 text-xs">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">عناصر أو بطاقات هذا القسم (كل عنصر في سطر بصيغة: العنوان | الوصف)</label>
                                <textarea name="custom_sec_items[]" rows="3" placeholder="العنوان الأول | تفاصيل العنصر الأول&#10;العنوان الثاني | تفاصيل العنصر الثاني" class="w-full rounded-xl border px-3 py-2 text-xs"></textarea>
                            </div>
                        `;
                        c.appendChild(div);
                    }
                </script>
            @endif

        </main>
    </div>

    <script>
        function openModal(id) {
            const el = document.getElementById(id);
            if (el) {
                el.classList.remove('hidden');
                el.classList.add('flex');
            }
        }

        function closeModal(id) {
            const el = document.getElementById(id);
            if (el) {
                el.classList.add('hidden');
                el.classList.remove('flex');
            }
        }

        function openAccModal(id) { openModal(id); }
        function closeAccModal(id) { closeModal(id); }

        function openOfficeStatementModal(officeId) {
            openModal('officeStatementModal_' + officeId);
        }

        function filterOfficesTable() {
            const input = document.getElementById('officeInstantSearch');
            const q = (input ? input.value : '').trim().toLowerCase();
            document.querySelectorAll('.office-row').forEach(row => {
                const hay = (row.getAttribute('data-search') || row.innerText || '').toLowerCase();
                row.style.display = hay.includes(q) ? '' : 'none';
            });
        }

        function filterPropTypesTable() {
            const input = document.getElementById('propTypeInstantSearch');
            const q = (input ? input.value : '').trim().toLowerCase();
            document.querySelectorAll('.prop-type-row').forEach(row => {
                const hay = (row.getAttribute('data-search') || row.innerText || '').toLowerCase();
                row.style.display = hay.includes(q) ? '' : 'none';
            });
        }

        function filterVouchersTable() {
            const input = document.getElementById('voucherInstantSearch');
            const q = (input ? input.value : '').trim().toLowerCase();
            document.querySelectorAll('.voucher-row').forEach(row => {
                const hay = (row.getAttribute('data-search') || row.innerText || '').toLowerCase();
                row.style.display = hay.includes(q) ? '' : 'none';
            });
        }

        function toggleBillingInputs(prefix) {
            const typeEl = document.getElementById(prefix + '_billing_type');
            if (!typeEl) return;
            const type = typeEl.value;
            const feeBox = document.getElementById(prefix + '_annual_fee_box');
            const rateBox = document.getElementById(prefix + '_commission_rate_box');

            if (type === 'annual_subscription') {
                if (feeBox) feeBox.classList.remove('hidden');
                if (rateBox) rateBox.classList.add('hidden');
            } else if (type === 'per_property_percentage') {
                if (feeBox) feeBox.classList.add('hidden');
                if (rateBox) rateBox.classList.remove('hidden');
            } else {
                if (feeBox) feeBox.classList.remove('hidden');
                if (rateBox) rateBox.classList.remove('hidden');
            }
        }

        function openEditOfficeModal(office) {
            const form = document.getElementById('editOfficeForm');
            if (form) form.action = '/offices/' + office.id + '/update';

            if (document.getElementById('edit_name')) document.getElementById('edit_name').value = office.name || '';
            if (document.getElementById('edit_cr_number')) document.getElementById('edit_cr_number').value = office.cr_number || '';
            if (document.getElementById('edit_fal_license')) document.getElementById('edit_fal_license').value = office.fal_license || '';
            if (document.getElementById('edit_phone')) document.getElementById('edit_phone').value = office.phone || '';
            if (document.getElementById('edit_username')) document.getElementById('edit_username').value = office.username || '';
            if (document.getElementById('edit_billing_type')) document.getElementById('edit_billing_type').value = office.billing_type || 'annual_subscription';
            if (document.getElementById('edit_annual_fee')) document.getElementById('edit_annual_fee').value = office.annual_fee || 0;
            if (document.getElementById('edit_company_commission_rate')) document.getElementById('edit_company_commission_rate').value = office.company_commission_rate || 0;
            if (document.getElementById('edit_subscription_ends_at')) document.getElementById('edit_subscription_ends_at').value = office.subscription_ends_at || '';
            if (document.getElementById('edit_status')) document.getElementById('edit_status').value = office.status || 'active';

            toggleBillingInputs('edit');
            openModal('editOfficeModal');
        }

        function closeEditOfficeModal() {
            closeModal('editOfficeModal');
        }

        let currentVoucherNumber = 'Voucher';
        function openOfficialVoucherPreview(v) {
            currentVoucherNumber = v.number || 'Voucher';
            if (document.getElementById('pdf_issuer')) document.getElementById('pdf_issuer').innerText = v.issuer || '';
            if (document.getElementById('pdf_voucher_title')) document.getElementById('pdf_voucher_title').innerText = v.type === 'receipt' ? 'سند قبض رسمي' : 'سند صرف رسمي';
            if (document.getElementById('pdf_voucher_no')) document.getElementById('pdf_voucher_no').innerText = v.number || '';
            if (document.getElementById('pdf_date')) document.getElementById('pdf_date').innerText = v.date || '';
            if (document.getElementById('pdf_method')) document.getElementById('pdf_method').innerText = v.payment_method || '';
            if (document.getElementById('pdf_amount')) document.getElementById('pdf_amount').innerText = v.amount || '';
            if (document.getElementById('pdf_party')) document.getElementById('pdf_party').innerText = v.party || '';
            if (document.getElementById('pdf_notes')) document.getElementById('pdf_notes').innerText = v.notes || '';
            openModal('officialVoucherModal');
        }

        function downloadVoucherPDF() {
            const element = document.getElementById('printableVoucherArea');
            if (!element) return;
            html2pdf().set({
                margin: 10,
                filename: currentVoucherNumber + '.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            }).from(element).save();
        }

        function openAddBranchesModal(parentId, parentName, parentType) {
            if (document.getElementById('branch_parent_id')) document.getElementById('branch_parent_id').value = parentId || '';
            if (parentType && document.getElementById('branch_acc_type')) document.getElementById('branch_acc_type').value = parentType;
            if (document.getElementById('addBranchesTitle')) {
                document.getElementById('addBranchesTitle').innerText = parentId ? 'إضافة فرع تحت: ' + parentName : 'إضافة حساب أو فروع';
            }
            openModal('addBranchesModal');
        }

        function addAnotherBranchRow() {
            const container = document.getElementById('multiBranchesContainer');
            if (!container) return;
            const firstRow = container.querySelector('.branch-row');
            if (!firstRow) return;
            const clone = firstRow.cloneNode(true);
            const nameInput = clone.querySelector('input[name="names[]"]');
            const codeInput = clone.querySelector('input[name="codes[]"]');
            if (nameInput) nameInput.value = '';
            if (codeInput) codeInput.value = '';
            container.appendChild(clone);
        }

        function openQuickVoucherModal(type, accountId, accountLabel) {
            if (document.getElementById('qv_type')) document.getElementById('qv_type').value = type;
            if (document.getElementById('qv_title')) document.getElementById('qv_title').innerText = type === 'receipt' ? ' سند قبض مباشر' : ' سند صرف مباشر';
            if (document.getElementById('qv_subtitle')) document.getElementById('qv_subtitle').innerText = accountLabel;
            if (type === 'receipt') {
                if (document.getElementById('qv_credit')) document.getElementById('qv_credit').value = accountId;
            } else {
                if (document.getElementById('qv_debit')) document.getElementById('qv_debit').value = accountId;
            }
            openModal('quickVoucherModal');
        }

        function addMoreAttributeRow() {
            const container = document.getElementById('dynamicAttributesContainer');
            if (!container) return;
            const firstRow = container.querySelector('.attribute-row');
            if (!firstRow) return;
            const clone = firstRow.cloneNode(true);
            const input = clone.querySelector('input[name="attribute_names[]"]');
            if (input) input.value = '';
            container.appendChild(clone);
        }

        function syncAutoVoucherAccounts() {
            const vType = document.getElementById('auto_voucher_type')?.value || 'receipt';
            const officeSel = document.getElementById('auto_voucher_office');
            const pmSel = document.getElementById('auto_voucher_pm');
            const debitSel = document.getElementById('auto_voucher_debit');
            const creditSel = document.getElementById('auto_voucher_credit');

            const officeAccId = officeSel?.options[officeSel.selectedIndex]?.getAttribute('data-account') || '';
            const pmAccId = pmSel?.options[pmSel.selectedIndex]?.getAttribute('data-account') || '';

            if (vType === 'receipt') {
                if (pmAccId && debitSel) debitSel.value = pmAccId;
                if (officeAccId && creditSel) creditSel.value = officeAccId;
            } else {
                if (officeAccId && debitSel) debitSel.value = officeAccId;
                if (pmAccId && creditSel) creditSel.value = pmAccId;
            }
        }

        // إضافة خانة عملية جديدة في القيد اليومي مع إظهار زر الحذف
        function addMoreJournalTableBlock() {
            const container = document.getElementById('journalTablesContainer');
            if (!container) return;
            const blocks = container.querySelectorAll('.journal-table-block');
            const firstBlock = blocks[0];
            if (!firstBlock) return;

            const clone = firstBlock.cloneNode(true);
            const nextNum = blocks.length + 1;
            const titleSpan = clone.querySelector('.journal-block-title') || clone.querySelector('span');
            if (titleSpan) titleSpan.innerText = 'جدول العملية (' + nextNum + ') — عملية إضافية';

            const removeBtn = clone.querySelector('.remove-journal-row-btn');
            if (removeBtn) removeBtn.classList.remove('hidden');

            clone.querySelectorAll('input').forEach(inp => {
                if (inp.name === 'debits[]' || inp.name === 'credits[]' || inp.name === 'taxes[]') inp.value = '0';
                else inp.value = '';
            });
            container.appendChild(clone);
        }

        // حذف الخانة الإضافية من القيد اليومي وإعادة ترقيم الجداول
        function removeJournalTableBlock(btn) {
            const container = document.getElementById('journalTablesContainer');
            if (!container) return;
            const blocks = container.querySelectorAll('.journal-table-block');
            if (blocks.length <= 2) {
                alert('يجب أن يحتوي القيد اليومي على طرفين (جدولين) على الأقل.');
                return;
            }
            const block = btn.closest('.journal-table-block');
            if (block) block.remove();

            container.querySelectorAll('.journal-table-block').forEach((b, idx) => {
                const t = b.querySelector('.journal-block-title') || b.querySelector('span');
                if (t) t.innerText = 'جدول العملية (' + (idx + 1) + ')';
            });
        }

        function filterJournalTable() {
            const q = (document.getElementById('journalInstantSearch')?.value || '').trim().toLowerCase();
            document.querySelectorAll('.journal-row').forEach(row => {
                const hay = (row.getAttribute('data-search') || row.innerText || '').toLowerCase();
                row.style.display = hay.includes(q) ? '' : 'none';
            });
        }

        function exportJournalPDF(entryId, entryNumber) {
            const el = document.getElementById('printableJournalArea_' + entryId);
            if (!el) return;
            html2pdf().set({
                margin: 10,
                filename: 'Journal_Entry_' + entryNumber + '.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            }).from(el).save();
        }

        function exportStatementPDF() {
            const el = document.getElementById('financialStatementPrintable');
            if (!el) return;
            html2pdf().set({
                margin: 8,
                filename: 'General_Ledger_Statement.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
            }).from(el).save();
        }

        function exportTableToCSV(tableId, filename) {
            const table = document.getElementById(tableId);
            if (!table) return;
            let csv = [];
            const rows = table.querySelectorAll('tr');
            rows.forEach(row => {
                if (row.style.display === 'none') return;
                const cols = row.querySelectorAll('td, th');
                const rowData = [];
                cols.forEach(col => {
                    let text = (col.innerText || '').replace(/"/g, '""').trim();
                    rowData.push('"' + text + '"');
                });
                csv.push(rowData.join(','));
            });
            const csvContent = '\uFEFF' + csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = filename;
            link.click();
        }

        function switchLedgerView(view) {
            const sections = ['ledger', 'cards', 'trial', 'income'];
            sections.forEach(s => {
                const secEl = document.getElementById(s + '_view_section');
                const btnEl = document.getElementById('btn_view_' + s);
                if (secEl) secEl.classList.toggle('hidden', s !== view);
                if (btnEl) {
                    if (s === view) {
                        btnEl.className = 'px-4 py-2 rounded-2xl bg-emerald-600 text-white text-xs font-bold shadow-sm transition';
                    } else {
                        btnEl.className = 'px-4 py-2 rounded-2xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 text-xs font-bold transition';
                    }
                }
            });
        }

        function quickFilterByAccount(accountId) {
            const modeSel = document.getElementById('stmt_filter_mode');
            const accSel = document.getElementById('stmt_account_select');
            if (modeSel && accSel) {
                modeSel.value = 'specific_account';
                accSel.value = String(accountId);
                switchLedgerView('ledger');
                applyStatementFilters();
            }
        }

        function quickFilterSpecificExpense(expenseId) {
            const modeSel = document.getElementById('stmt_filter_mode');
            const expSel = document.getElementById('stmt_expense_select');
            if (modeSel && expSel) {
                modeSel.value = 'specific_expense';
                expSel.value = String(expenseId);
                switchLedgerView('ledger');
                applyStatementFilters();
            }
        }

        function setLedgerQuickDate(period) {
            const fromEl = document.getElementById('stmt_from_date');
            const toEl = document.getElementById('stmt_to_date');
            if (!fromEl || !toEl) return;

            const now = new Date();
            const yyyy = now.getFullYear();
            const mm = String(now.getMonth() + 1).padStart(2, '0');
            const dd = String(now.getDate()).padStart(2, '0');
            const todayStr = `${yyyy}-${mm}-${dd}`;

            if (period === 'today') {
                fromEl.value = todayStr;
                toEl.value = todayStr;
            } else if (period === 'month') {
                fromEl.value = `${yyyy}-${mm}-01`;
                toEl.value = todayStr;
            } else if (period === 'year') {
                fromEl.value = `${yyyy}-01-01`;
                toEl.value = todayStr;
            } else {
                fromEl.value = '';
                toEl.value = '';
            }
            applyStatementFilters();
        }

        function applyStatementFilters() {
            const searchQ = (document.getElementById('ledgerInstantSearch')?.value || '').trim().toLowerCase();
            const mode = document.getElementById('stmt_filter_mode')?.value || 'all';
            const expBox = document.getElementById('stmt_specific_expense_box');
            const accBox = document.getElementById('stmt_specific_account_box');
            const expId = document.getElementById('stmt_expense_select')?.value || '';
            const accId = document.getElementById('stmt_account_select')?.value || '';
            const officeId = document.getElementById('stmt_office_select')?.value || '';
            const fromDate = document.getElementById('stmt_from_date')?.value || '';
            const toDate = document.getElementById('stmt_to_date')?.value || '';

            if (expBox) expBox.classList.toggle('hidden', mode !== 'specific_expense');
            if (accBox) accBox.classList.toggle('hidden', mode !== 'specific_account');

            let openingSum = 0;
            let debitSum = 0;
            let creditSum = 0;

            document.querySelectorAll('.stmt-summary-row, .ledger-account-card').forEach(row => {
                const rAccId = row.getAttribute('data-account-id');
                const isExp = row.getAttribute('data-is-expense') === '1';
                const isRev = row.getAttribute('data-is-revenue') === '1';
                const hay = (row.getAttribute('data-search') || row.innerText || '').toLowerCase();
                let show = true;

                if (mode === 'all_expenses') show = isExp;
                else if (mode === 'specific_expense') show = isExp && (!expId || rAccId === expId);
                else if (mode === 'all_revenues') show = isRev;
                else if (mode === 'specific_account') show = !accId || rAccId === accId;

                if (show && searchQ && !hay.includes(searchQ)) show = false;
                row.style.display = show ? '' : 'none';
            });

            const movementRows = Array.from(document.querySelectorAll('.stmt-movement-row'));
            // ترتيب زمني تصاعدي لحساب الرصيد الافتتاحي والتراكمي للفترة المفلترة
            const chronologicalRows = movementRows.slice().reverse();
            let runningFiltered = 0;

            chronologicalRows.forEach(row => {
                const rDate = row.getAttribute('data-date') || '';
                const rAccId = row.getAttribute('data-account-id');
                const rOffId = row.getAttribute('data-office-id') || '';
                const isExp = row.getAttribute('data-is-expense') === '1';
                const isRev = row.getAttribute('data-is-revenue') === '1';
                const hay = (row.getAttribute('data-search') || row.innerText || '').toLowerCase();
                const dVal = parseFloat(row.getAttribute('data-debit') || 0);
                const cVal = parseFloat(row.getAttribute('data-credit') || 0);

                let matchEntity = true;
                if (mode === 'all_expenses') matchEntity = isExp;
                else if (mode === 'specific_expense') matchEntity = isExp && (!expId || rAccId === expId);
                else if (mode === 'all_revenues') matchEntity = isRev;
                else if (mode === 'specific_account') matchEntity = !accId || rAccId === accId;

                if (matchEntity && officeId && rOffId !== officeId) matchEntity = false;
                if (matchEntity && searchQ && !hay.includes(searchQ)) matchEntity = false;

                // إذا كانت الحركة مطابقة للحساب ولكن تاريخها قبل تاريخ البداية -> تدخل في الرصيد الافتتاحي
                if (matchEntity && fromDate && rDate < fromDate) {
                    openingSum += (dVal - cVal);
                    runningFiltered += (dVal - cVal);
                    row.style.display = 'none';
                    return;
                }

                let show = matchEntity;
                if (show && toDate && rDate > toDate) show = false;

                row.style.display = show ? '' : 'none';
                if (show) {
                    debitSum += dVal;
                    creditSum += cVal;
                    runningFiltered += (dVal - cVal);
                    const runCell = row.querySelector('.dynamic-running-cell');
                    if (runCell) {
                        runCell.innerText = runningFiltered.toFixed(2) + ' ر.س';
                    }
                }
            });

            const netPeriod = debitSum - creditSum;
            const closingBal = openingSum + netPeriod;

            const opEl = document.getElementById('ledgerOpeningBalance');
            const dEl = document.getElementById('filteredDebitSum');
            const cEl = document.getElementById('filteredCreditSum');
            const nEl = document.getElementById('filteredNetSum');
            const clEl = document.getElementById('ledgerClosingBalance');

            if (opEl) opEl.innerText = openingSum.toFixed(2);
            if (dEl) dEl.innerText = debitSum.toFixed(2);
            if (cEl) cEl.innerText = creditSum.toFixed(2);
            if (nEl) nEl.innerText = netPeriod.toFixed(2);
            if (clEl) clEl.innerText = closingBal.toFixed(2);
        }

        function filterSystemLogsTable() {
            const q = (document.getElementById('systemLogSearchInput')?.value || '').trim().toLowerCase();
            document.querySelectorAll('.sys-log-row').forEach(r => {
                r.style.display = r.innerText.toLowerCase().includes(q) ? '' : 'none';
            });
        }

        window.addEventListener('DOMContentLoaded', () => {
            syncAutoVoucherAccounts();
            applyStatementFilters();
            @if(session('open_diagnostics_modal'))
                openModal('systemDiagnosticsModal');
            @endif
        });
    </script>

    <!-- ======================================================================= -->
    <!-- 1. نافذة سجل الأخطاء الذكي (System Error Logs Modal) -->
    <!-- ======================================================================= -->
    <div id="systemLogsModal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-50 hidden items-center justify-center p-4 no-print">
        <div class="bg-white rounded-3xl max-w-5xl w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] flex flex-col">
            <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-lg text-slate-900 flex items-center gap-2">
                        <span>🚨</span>
                        <span>سجل أخطاء النظام والتنبيهات التقنية (System Error Logs)</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">يعرض أي خطأ برمجي أو تشغيلي في جميع البوابات (الشركة، المكاتب، الملاك، العائلة) مع اقتراح الإصلاح</p>
                </div>
                <div class="flex items-center gap-2">
                    <input type="text" id="systemLogSearchInput" oninput="filterSystemLogsTable()"
                           placeholder="🔍 ابحث في سجل الأخطاء..."
                           class="rounded-xl border border-slate-300 px-3 py-1.5 text-xs">
                    <form action="{{ route('erp.system.clear_logs') }}" method="POST" onsubmit="return confirm('هل تريد مسح وتصفير سجل الأخطاء بالكامل؟');">
                        @csrf
                        <button type="submit" class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border border-rose-200 text-xs font-bold transition">
                            🗑️ تصفير السجل
                        </button>
                    </form>
                    <button type="button" onclick="closeModal('systemLogsModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                </div>
            </div>

            <div class="overflow-y-auto flex-1 my-4 rounded-2xl border border-slate-200">
                <table class="w-full text-xs text-right">
                    <thead class="bg-slate-900 text-white sticky top-0">
                        <tr>
                            <th class="py-3 px-4">#</th>
                            <th class="py-3 px-4">المستوى</th>
                            <th class="py-3 px-4">المصدر / البوابة</th>
                            <th class="py-3 px-4">تفاصيل الخطأ والتشخيص</th>
                            <th class="py-3 px-4">التوجيه المقترح للإصلاح</th>
                            <th class="py-3 px-4">التوقيت</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse(($systemLogs ?? collect()) as $log)
                            <tr class="sys-log-row hover:bg-slate-50">
                                <td class="py-3 px-4 font-mono font-bold">{{ $log->id }}</td>
                                <td class="py-3 px-4">
                                    @if($log->level == 'ERROR' || $log->level == 'CRITICAL')
                                        <span class="px-2.5 py-0.5 rounded-lg bg-rose-100 text-rose-800 font-bold">{{ $log->level }}</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-lg bg-amber-100 text-amber-800 font-bold">{{ $log->level }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 font-bold text-slate-800">
                                    <div>{{ $log->source }}</div>
                                    @if($log->file_line)
                                        <div class="text-[10px] font-mono text-slate-400">{{ $log->file_line }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 font-mono text-rose-700 font-semibold max-w-md break-words">{{ $log->message }}</td>
                                <td class="py-3 px-4 text-emerald-700 font-semibold">{{ $log->suggestion ?: 'راجع المدخلات أو شغل الفحص الأوتوماتيكي.' }}</td>
                                <td class="py-3 px-4 font-mono text-slate-400">{{ $log->created_at }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center">
                                    <div class="text-3xl mb-2">✅</div>
                                    <div class="font-bold text-emerald-700 text-sm">سجل النظام نظيف تماماً! لا توجد أي أخطاء مسجلة.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- 2. نافذة الفحص الأوتوماتيكي الشامل (Auto-Diagnostic Suite Modal) -->
    <!-- ======================================================================= -->
    <div id="systemDiagnosticsModal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-50 hidden items-center justify-center p-4 no-print">
        <div class="bg-white rounded-3xl max-w-5xl w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] flex flex-col">
            <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-lg text-slate-900 flex items-center gap-2">
                        <span>🤖</span>
                        <span>المحرك الأوتوماتيكي الشامل لفحص وتجربة جميع خصائص وبوابات النظام</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">يقوم بمحاكاة وتجربة 15 عملية كاملة تشمل: الشركة الأم، مكاتب العقار وموظفيها، الملاك، عقود إيجار، وأفراد العائلة وصلاحياتهم</p>
                </div>
                <div class="flex items-center gap-2">
                    <form action="{{ route('erp.system.diagnostics') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-indigo-600 to-emerald-600 hover:from-indigo-500 hover:to-emerald-500 text-white text-xs font-bold shadow-lg transition flex items-center gap-2">
                            <span>⚡ تشغيل الفحص الأوتوماتيكي الشامل الآن (15 اختباراً)</span>
                        </button>
                    </form>
                    <button type="button" onclick="closeModal('systemDiagnosticsModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                </div>
            </div>

            <div class="overflow-y-auto flex-1 my-4 rounded-2xl border border-slate-200">
                <table class="w-full text-xs text-right">
                    <thead class="bg-slate-900 text-white sticky top-0">
                        <tr>
                            <th class="py-3 px-4">#</th>
                            <th class="py-3 px-4">البوابة / القسم</th>
                            <th class="py-3 px-4">الميزة التي تمت تجربتها</th>
                            <th class="py-3 px-4 text-center">الحالة</th>
                            <th class="py-3 px-4">نتيجة الاختبار التفصيلية</th>
                            <th class="py-3 px-4 text-center">السرعة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse(($diagnosticRuns ?? collect())->reverse()->values() as $idx => $run)
                            <tr class="hover:bg-slate-50">
                                <td class="py-3 px-4 font-mono font-bold">{{ $idx + 1 }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 font-bold">{{ $run->module }}</span>
                                </td>
                                <td class="py-3 px-4 font-bold text-slate-900">{{ $run->feature_name }}</td>
                                <td class="py-3 px-4 text-center">
                                    @if($run->status == 'passed')
                                        <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold">✅ سليم (Passed)</span>
                                    @elseif($run->status == 'warning')
                                        <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 font-bold">⚠️ تنبيه</span>
                                    @else
                                        <span class="px-3 py-1 rounded-full bg-rose-100 text-rose-800 font-bold">❌ خطأ (Failed)</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-slate-700 font-semibold">{{ $run->details }}</td>
                                <td class="py-3 px-4 text-center font-mono text-slate-500">{{ $run->execution_ms }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    اضغط على زر <b>"⚡ تشغيل الفحص الأوتوماتيكي الشامل الآن"</b> بالزاوية اليسرى لبدء اختبار جميع خصائص النظام.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- 3. نافذة النسخ الاحتياطي والاستعادة الفورية (Backup & Restore Modal) -->
    <!-- ======================================================================= -->
    <div id="systemBackupModal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-50 hidden items-center justify-center p-4 no-print">
        <div class="bg-white rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-slate-200 space-y-5">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-base text-slate-900">💾 مركز النسخ الاحتياطي والاستعادة الشاملة</h3>
                    <p class="text-xs text-slate-500">احفظ نسخة كاملة من بيانات الشركة والمكاتب والملاك والعائلة والحسابات</p>
                </div>
                <button type="button" onclick="closeModal('systemBackupModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
            </div>

            <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 space-y-3">
                <div class="text-xs font-bold text-emerald-950"> أولاً: تحميل نسخة احتياطية فورية إلى جهازك</div>
                <p class="text-xs text-emerald-800">تشمل جميع الجداول (المستخدمين، المكاتب، الملاك، العائلة، العقارات، العقود، شجرة الحسابات، السندات، والقيود).</p>
                <div class="grid grid-cols-2 gap-3 pt-1">
                    <a href="{{ route('erp.system.backup', ['format' => 'json']) }}"
                       class="py-3 px-4 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs text-center shadow-lg transition">
                        📦 تحميل نسخة شاملة (.JSON)
                    </a>
                    <a href="{{ route('erp.system.backup', ['format' => 'sqlite']) }}"
                       class="py-3 px-4 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs text-center shadow-lg transition">
                        🗄️ تحميل قاعدة البيانات (.SQLite)
                    </a>
                </div>
            </div>

            <form action="{{ route('erp.system.restore') }}" method="POST" enctype="multipart/form-data" class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3" onsubmit="return confirm('هل أنت متأكد من استعادة هذه النسخة الاحتياطية؟ سيتم استبدال البيانات الحالية ببيانات النسخة المرفوعة.');">
                @csrf
                <div class="text-xs font-bold text-slate-900"> ثانياً: استعادة النظام من نسخة احتياطية محفوظة (.JSON أو .SQLite)</div>
                <input type="file" name="backup_file" accept=".json,.sqlite,.sqlite3,.db" required
                       class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white">
                <button type="submit" class="w-full py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs shadow transition">
                    ♻️ رفع واستعادة النسخة الاحتياطية الآن (JSON / SQLite)
                </button>
            </form>
        </div>
    </div>
</body>
</html>