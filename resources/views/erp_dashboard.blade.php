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
                <p class="text-[11px] text-emerald-400">الإدارة المركزية للشركة الأم</p>
            </div>
        </div>

        <nav class="p-4 space-y-1.5 flex-1 overflow-y-auto">
            <div class="px-3 py-2 text-[11px] font-bold text-slate-500 uppercase tracking-wider">الإدارة المركزية</div>
            
            <a href="{{ route('erp.dashboard', ['tab' => 'overview']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'overview' ? 'bg-gradient-to-l from-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-600/25' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                <span>📊</span>
                <span>لوحة التحكم</span>
            </a>

            <a href="{{ route('erp.dashboard', ['tab' => 'offices']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'offices' ? 'bg-gradient-to-l from-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-600/25' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                <span>🏢</span>
                <span>مـكـاتب العقـار</span>
            </a>

            <a href="{{ route('erp.dashboard', ['tab' => 'properties']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'properties' ? 'bg-gradient-to-l from-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-600/25' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                <span>🏗️</span>
                <span>أنواع العقارات والمتطلبات</span>
            </a>

            <div class="px-3 pt-5 pb-2 text-[11px] font-bold text-slate-500 uppercase tracking-wider">النظام المالي والمحاسبي</div>

            <a href="{{ route('erp.dashboard', ['tab' => 'accounting']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'accounting' ? 'bg-gradient-to-l from-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-600/25' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                <span>🌳</span>
                <span>شجرة الحسابات المحاسبية</span>
            </a>

            <a href="{{ route('erp.dashboard', ['tab' => 'vouchers']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'vouchers' ? 'bg-gradient-to-l from-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-600/25' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                <span>🧾</span>
                <span>سندات القبض والصرف</span>
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
        <header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200/80 px-8 flex items-center justify-between sticky top-0 z-20 no-print">
            <div class="flex items-center gap-3">
                <span class="px-3.5 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    متصل كـ: المدير العام ({{ auth()->user()->username }})
                </span>
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
                            <span class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">🏢</span>
                        </div>
                        <div class="text-3xl font-bold text-slate-900">{{ $stats['offices_count'] }}</div>
                        <p class="text-xs text-emerald-600 mt-2 font-medium">{{ $stats['active_offices'] }} مكتب نشط حالياً</p>
                    </div>

                    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-slate-500">تصنيفات العقارات المعرّفة</span>
                            <span class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">🏗️</span>
                        </div>
                        <div class="text-3xl font-bold text-slate-900">{{ $stats['property_types_count'] }}</div>
                        <p class="text-xs text-slate-400 mt-2">سكني، تجاري، زراعي، صناعي</p>
                    </div>

                    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-slate-500">إجمالي سندات القبض (RV)</span>
                            <span class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold">📥</span>
                        </div>
                        <div class="text-2xl font-bold text-emerald-600">{{ number_format($stats['total_receipts'], 2) }} <span class="text-xs">ر.س</span></div>
                        <p class="text-xs text-slate-400 mt-2">المقبوضات المرحلة للشجرة</p>
                    </div>

                    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-slate-500">إجمالي سندات الصرف (SA)</span>
                            <span class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">📤</span>
                        </div>
                        <div class="text-2xl font-bold text-slate-900">{{ number_format($stats['total_payments'], 2) }} <span class="text-xs">ر.س</span></div>
                        <p class="text-xs text-slate-400 mt-2">المصروفات والعمولات المصروفة</p>
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="font-bold text-slate-900">أحدث مكاتب العقار المسجلة</h3>
                        <a href="{{ route('erp.dashboard', ['tab' => 'offices']) }}" class="text-xs font-bold text-emerald-600 hover:underline">إدارة المكاتب وكشوف الحسابات ←</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-right">
                            <thead class="bg-slate-50 text-slate-600 text-xs uppercase border-b border-slate-200/60">
                                <tr>
                                    <th class="py-3.5 px-6">اسم المكتب</th>
                                    <th class="py-3.5 px-6">رقم الجوال</th>
                                    <th class="py-3.5 px-6">حساب الشجرة</th>
                                    <th class="py-3.5 px-6">نوع الاتفاق</th>
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
                                        <td class="py-4 px-6 font-mono text-xs">{{ $office->account_code ? $office->account_code . ' - ' . $office->account_name : 'غير مربوط' }}</td>
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
                                    <th class="py-4 px-4">حساب الشجرة المحاسبية</th>
                                    <th class="py-4 px-4">الاتفاق المالي</th>
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
                                                <span class="text-xs text-slate-400">غير مربوط</span>
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
                                            <div class="flex items-center justify-center gap-1.5">
                                                <a href="{{ route('erp.switch_office', $office->id) }}"
                                                   class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 text-xs font-bold transition"
                                                   title="دخول لبوابة المكتب">
                                                    👁️ دخول
                                                </a>
                                                <button type="button"
                                                        onclick="openEditOfficeModal({
                                                            id: '{{ $office->id }}',
                                                            name: '{{ addslashes($office->name) }}',
                                                            cr_number: '{{ addslashes($office->cr_number) }}',
                                                            fal_license: '{{ addslashes($office->fal_license) }}',
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
                                                    ✏️ تعديل
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
                                <div class="text-xs font-bold text-blue-900">🌳 الربط الآلي بشجرة الحسابات المحاسبية</div>
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
                                        <option value="annual_subscription">اشتراك سنوي مقطوع</option>
                                        <option value="per_property_percentage">نسبة من قيمة كل عقار / عقد</option>
                                        <option value="hybrid">مدمج (اشتراك سنوي + نسبة معاً)</option>
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
                                        <option value="receipt">📥 قبض دفعة من المكتب</option>
                                        <option value="payment">📤 صرف عمولة / مصروف للمكتب</option>
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
                    ];
                @endphp

                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <!-- شريط الأدوات العلوي المرتب -->
                    <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="font-bold text-lg text-slate-900">سجل أنواع العقارات والمتطلبات الديناميكية المعتمدة</h3>
                            <p class="text-xs text-slate-500 mt-0.5">إدارة شاملة لأنواع العقارات وتصنيفاتها والحقول المطلوبة من المكاتب عند تسجيل كل عقار</p>
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
                                <span>إضافة نوع عقار مع متطلباته</span>
                            </button>
                        </div>
                    </div>

                    <!-- جدول أنواع العقارات المرتب -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-right" id="propTypesTable">
                            <thead class="bg-slate-50 text-slate-600 text-xs border-b border-slate-200/80">
                                <tr>
                                    <th class="py-4 px-5 w-14 text-center">#</th>
                                    <th class="py-4 px-5">مسمى نوع العقار</th>
                                    <th class="py-4 px-5 text-center">التصنيف الرئيسي</th>
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
                                        data-search="{{ mb_strtolower($type->name . ' ' . $arabicCat) }}">
                                        <td class="py-4 px-5 text-center font-mono text-xs text-slate-400 font-bold">{{ $index + 1 }}</td>
                                        <td class="py-4 px-5 font-bold text-slate-900 text-sm">{{ $type->name }}</td>
                                        <td class="py-4 px-5 text-center">
                                            <span class="px-3 py-1 rounded-xl text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/60">{{ $arabicCat }}</span>
                                        </td>
                                        <td class="py-4 px-5 text-center">
                                            @if($type->has_units)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/60">🏢 يتفرع لوحدات داخلية</span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs bg-slate-100 text-slate-600 font-medium">📍 عقار مستقل</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-5 text-center">
                                            <button type="button" onclick="openModal('attrsModal_{{ $type->id }}')"
                                                    class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl {{ $typeAttrs->count() > 0 ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-600 hover:text-white' : 'bg-slate-100 text-slate-600 border-slate-200' }} border text-xs font-bold transition shadow-sm">
                                                <span>📋 استعراض / إضافة متطلبات</span>
                                                <span class="px-2 py-0.5 rounded-lg bg-white/90 text-slate-900 font-mono text-[11px]">{{ $typeAttrs->count() }}</span>
                                            </button>
                                        </td>
                                        <td class="py-4 px-5 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <button type="button" onclick="openModal('editPropertyTypeModal_{{ $type->id }}')"
                                                        class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-white text-xs font-semibold transition">✏️ تعديل</button>
                                                <form action="{{ route('erp.property_types.delete', $type->id) }}" method="POST" onsubmit="return confirm('حذف هذا العقار وجميع متطلباته؟');">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-red-50 hover:bg-red-600 text-red-600 hover:text-white border border-red-200 text-xs font-semibold transition">🗑️ حذف</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="py-12 text-center text-slate-400">لا توجد عقارات مضافة بعد. اضغط على "إضافة نوع عقار مع متطلباته" للبدء.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- نافذة موحدة: إضافة نوع العقار مع متطلباته دفعة واحدة -->
                <div id="addPropertyTypeWithAttrsModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200 max-h-[92vh] overflow-y-auto">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                            <div>
                                <h3 class="font-bold text-base text-slate-900">➕ إضافة نوع عقار جديد مع متطلباته الديناميكية</h3>
                                <p class="text-xs text-slate-500 mt-0.5">حدد بيانات العقار الأساسية وأضف الحقول المطلوبة من المكاتب في نفس النافذة</p>
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
                                <div class="md:col-span-2 pt-1">
                                    <label class="flex items-center gap-2 text-xs font-bold text-emerald-800 cursor-pointer">
                                        <input type="checkbox" name="has_units" value="1" class="rounded text-emerald-600">
                                        <span>🏢 هذا العقار يتفرع منه وحدات داخلية (مثل العمارة تتفرع لشقق ومحلات)</span>
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
                                            <input type="text" name="attribute_names[]" placeholder="مثال: عدد الأدوار، عرض الشارع، رقم المخطط..."
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

                <!-- نوافذ عرض وإضافة وتعديل متطلبات كل عقار -->
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

                            <!-- نموذج إضافة متطلب جديد لهذا العقار مباشرة -->
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

            <!-- 4. شجرة الحسابات المحاسبية -->
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
                        <h3 class="font-bold text-base text-slate-900">شجرة الحسابات المحاسبية (للشركة الأم)</h3>
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
                                        <button type="button" onclick="openAccModal('editAccModal_{{ $root->id }}')" class="px-2.5 py-1.5 rounded-xl bg-white border text-xs">✏️</button>
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
                                                                class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 text-[11px] font-bold transition">📥 قبض</button>
                                                        <button type="button" onclick="openQuickVoucherModal('payment', '{{ $child->id }}', '{{ addslashes($child->code . ' - ' . $child->name) }}')"
                                                                class="px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-600 text-amber-700 hover:text-white border border-amber-200 text-[11px] font-bold transition">📤 صرف</button>
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
                                                                        class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[10px] font-bold">📥 قبض</button>
                                                                <button type="button" onclick="openQuickVoucherModal('payment', '{{ $subChild->id }}', '{{ addslashes($subChild->code . ' - ' . $subChild->name) }}')"
                                                                        class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 text-[10px] font-bold">📤 صرف</button>
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
                                                <span class="px-2.5 py-1 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">📥 قبض</span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-xl text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">📤 صرف</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 font-bold text-slate-800 text-xs">{{ $v->office_name ?? 'عام - الشركة الأم' }}</td>
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
                                                    ✏️
                                                </button>

                                                <form action="{{ route('erp.vouchers.delete', $v->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف السند وعكس قيده المحاسبي؟');">
                                                    @csrf
                                                    <button type="submit" class="px-2 py-1.5 rounded-xl bg-red-50 hover:bg-red-600 text-red-600 hover:text-white text-xs font-bold transition">
                                                        🗑️
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

                <!-- نافذة إدارة طرق الدفع -->
                <div id="managePaymentMethodsModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4 no-print">
                    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200">
                        <div class="flex items-center justify-between pb-4 border-b mb-4">
                            <h3 class="font-bold text-base text-slate-900">💳 إدارة طرق الدفع المعتمدة في السندات</h3>
                            <button type="button" onclick="closeModal('managePaymentMethodsModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                        </div>
                        <form action="{{ route('erp.payment_methods.store') }}" method="POST" class="flex gap-2 mb-5">
                            @csrf
                            <input type="text" name="name" required placeholder="أضف طريقة دفع جديدة (مثال: Apple Pay، شيك، STC Pay...)" class="flex-1 rounded-xl border px-3.5 py-2 text-xs">
                            <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">+ إضافة</button>
                        </form>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @foreach($paymentMethods as $pm)
                                <div class="flex items-center justify-between gap-2 p-2.5 rounded-xl bg-slate-50 border">
                                    <form action="{{ route('erp.payment_methods.update', $pm->id) }}" method="POST" class="flex-1 flex gap-2">
                                        @csrf
                                        <input type="text" name="name" value="{{ $pm->name }}" required class="flex-1 rounded-lg border px-3 py-1.5 text-xs font-bold bg-white">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-900 text-white text-xs">تعديل</button>
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

                <!-- نافذة إضافة سند قبض / صرف جديد -->
                <div id="addVoucherModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4 no-print">
                    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200">
                        <div class="flex items-center justify-between pb-4 border-b mb-4">
                            <h3 class="font-bold text-base text-slate-900">إصدار سند قبض / صرف رسمي جديد</h3>
                            <button type="button" onclick="closeModal('addVoucherModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                        </div>
                        <form action="{{ route('erp.vouchers.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">نوع السند *</label>
                                    <select name="voucher_type" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white font-bold">
                                        <option value="receipt">📥 سند قبض (RV)</option>
                                        <option value="payment">📤 سند صرف (SA)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">المكتب المرتبط (اختياري)</label>
                                    <select name="office_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white">
                                        <option value="">-- عام للشركة --</option>
                                        @foreach($offices as $off)
                                            <option value="{{ $off->id }}">{{ $off->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">من حساب (المدين) *</label>
                                    <select name="debit_account_id" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white">
                                        @foreach($accounts->where('is_group', 0) as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">إلى حساب (الدائن) *</label>
                                    <select name="credit_account_id" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white">
                                        @foreach($accounts->where('is_group', 0) as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
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
                                    <select name="payment_method" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-semibold">
                                        @foreach($paymentMethods as $pm)
                                            <option value="{{ $pm->name }}">{{ $pm->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">التاريخ *</label>
                                    <input type="date" name="voucher_date" value="{{ date('Y-m-d') }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs">
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
                                    📥 تحميل PDF
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

            <!-- 7. إعدادات الحساب الشخصي -->
            @if($tab == 'settings')
                @php
                    $myLocalPhone = \App\Http\Controllers\CompanyErpController::toLocalPhone(auth()->user()->phone);
                @endphp
                <div class="max-w-xl bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
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

        // فلترة جدول المكاتب
        function filterOfficesTable() {
            const input = document.getElementById('officeInstantSearch');
            const q = (input ? input.value : '').trim().toLowerCase();
            document.querySelectorAll('.office-row').forEach(row => {
                const hay = (row.getAttribute('data-search') || row.innerText || '').toLowerCase();
                row.style.display = hay.includes(q) ? '' : 'none';
            });
        }

        // فلترة جدول أنواع العقارات
        function filterPropTypesTable() {
            const input = document.getElementById('propTypeInstantSearch');
            const q = (input ? input.value : '').trim().toLowerCase();
            document.querySelectorAll('.prop-type-row').forEach(row => {
                const hay = (row.getAttribute('data-search') || row.innerText || '').toLowerCase();
                row.style.display = hay.includes(q) ? '' : 'none';
            });
        }

        // فلترة جدول السندات
        function filterVouchersTable() {
            const input = document.getElementById('voucherInstantSearch');
            const q = (input ? input.value : '').trim().toLowerCase();
            document.querySelectorAll('.voucher-row').forEach(row => {
                const hay = (row.getAttribute('data-search') || row.innerText || '').toLowerCase();
                row.style.display = hay.includes(q) ? '' : 'none';
            });
        }

        // إظهار وإخفاء حقول الاشتراك السنوي والنسبة حسب نوع الاتفاقية
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

        // معاينة السند الرسمي وتصديره PDF
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
            if (document.getElementById('pdf_debit')) document.getElementById('pdf_debit').innerText = v.debit || '';
            if (document.getElementById('pdf_credit')) document.getElementById('pdf_credit').innerText = v.credit || '';
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
            if (document.getElementById('qv_title')) document.getElementById('qv_title').innerText = type === 'receipt' ? '📥 سند قبض مباشر' : '📤 سند صرف مباشر';
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
    </script>
</body>
</html>