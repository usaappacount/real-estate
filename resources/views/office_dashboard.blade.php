<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة مكتب العقار | {{ $office->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Readex+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Readex Pro', sans-serif; background-color: #f8fafc; }
        .office-sidebar { background: linear-gradient(180deg, #0f172a 0%, #022c22 100%); }
        @media print {
            body * { visibility: hidden; }
            #printableVoucherArea, #printableVoucherArea * { visibility: visible; }
            #printableVoucherArea { position: fixed; inset: 0; width: 100%; background: white; padding: 20px; }
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

    <!-- القائمة الجانبية لبوابة المكتب -->
    <aside class="w-72 office-sidebar text-white flex flex-col shrink-0 shadow-2xl sticky top-0 h-screen">
        <div class="p-6 border-b border-white/10 flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-teal-500 to-emerald-400 flex items-center justify-center shadow-lg shadow-emerald-500/20 shrink-0 font-bold text-lg">
                🏢
            </div>
            <div>
                <h1 class="font-bold text-sm tracking-tight text-white">{{ $office->name }}</h1>
            </div>
        </div>

        <nav class="p-4 space-y-1.5 flex-1 overflow-y-auto">
            <div class="px-3 py-2 text-[11px] font-bold text-emerald-300/60 uppercase tracking-wider">إدارة الأملاك والعقارات</div>

            <a href="{{ route('office.dashboard', ['tab' => 'overview']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'overview' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <span> لوحة التحكم </span>
            </a>

            <a href="{{ route('office.dashboard', ['tab' => 'owners']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'owners' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <span> الـملاك ({{ $stats['owners_count'] }})</span>
            </a>

            <a href="{{ route('office.dashboard', ['tab' => 'properties']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'properties' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <span>العقارات والوحدات</span>
            </a>

            <a href="{{ route('office.dashboard', ['tab' => 'contracts']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'contracts' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <span>المستأجرون وعقود (إيجار)</span>
            </a>

            <div class="px-3 pt-4 pb-2 text-[11px] font-bold text-emerald-300/60 uppercase tracking-wider">النظام المالي والمحاسبي للمكتب</div>

            <a href="{{ route('office.dashboard', ['tab' => 'accounting']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'accounting' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <span>شجرة الحسابات</span>
            </a>

            <a href="{{ route('office.dashboard', ['tab' => 'vouchers']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'vouchers' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <span>سندات القبض والصرف</span>
            </a>

           <a href="{{ route('office.dashboard', ['tab' => 'journal']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'journal' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <span>القيود اليومية</span>
            </a>

            <a href="{{ route('office.dashboard', ['tab' => 'invoicing']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'invoicing' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <span> الفواتير الضريبية</span>
            </a>

            <a href="{{ route('office.dashboard', ['tab' => 'account_statement']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'account_statement' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <span>كشف الحساب </span>
            </a>

            <a href="{{ route('office.dashboard', ['tab' => 'statements']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'statements' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <span>الأستاذ العام والقوائم المالية</span>
            </a>

            <div class="px-3 pt-4 pb-2 text-[11px] font-bold text-emerald-300/60 uppercase tracking-wider">الإعدادات والموارد البشرية</div>

            <a href="{{ route('office.dashboard', ['tab' => 'staff']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'staff' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <span> الموظفين والصلاحيات </span>
            </a>

            <a href="{{ route('office.dashboard', ['tab' => 'whatsapp']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'whatsapp' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <span>إعدادات الربط مع واتساب</span>
            </a>
        </nav>

        <div class="p-4 border-t border-white/10 bg-black/20 space-y-2">
            @if(session()->has('impersonate_office_id'))
                <a href="{{ route('erp.return_company') }}" class="block w-full text-center py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition">
                    ↩ العودة للوحة الشركة الأم (الأدمن)
                </a>
            @endif
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-white">{{ auth()->user()->name }}</p>
                    <p class="text-[11px] text-emerald-300 font-mono" dir="ltr">{{ auth()->user()->phone }}</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-red-500/20 hover:bg-red-500/30 text-red-300 text-xs font-semibold transition">
                        خروج
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- المحتوى الرئيسي للمكتب -->
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200/80 px-8 flex items-center justify-between sticky top-0 z-20">
            <div>
                <h2 class="text-base font-bold text-slate-900">{{ $office->name }} - نظام ادارة العقارات </h2>
                <p class="text-xs text-slate-500">
                    رخصة فال: <span class="font-mono font-semibold">{{ $office->fal_license ?: 'غير محدد' }}</span> |
                    السجل التجاري: <span class="font-mono font-semibold">{{ $office->cr_number ?: 'غير محدد' }}</span>
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    @if($office->billing_type == 'annual_subscription')
                        اتفاقية المكتب: اشتراك سنوي ({{ number_format($office->annual_fee, 0) }} ر.س)
                    @elseif($office->billing_type == 'per_property_percentage')
                        اتفاقية المكتب: نسبة للشركة ({{ $office->company_commission_rate }}% لكل عقد)
                    @else
                        اتفاقية مدمجة ({{ number_format($office->annual_fee, 0) }} ر.س + {{ $office->company_commission_rate }}%)
                    @endif
                </span>
            </div>
        </header>

        <main class="p-8 space-y-6">
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold shadow-sm">
                    ✅ {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-700 text-sm font-semibold shadow-sm">
                    ⚠️ {{ $errors->first() }}
                </div>
            @endif

            <!-- 1. تبويب النظرة العامة للمكتب -->
            @if($tab == 'overview')
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                        <div class="text-xs font-semibold text-slate-500 mb-2">إجمالي الملاك المسجلين</div>
                        <div class="text-3xl font-bold text-slate-900">{{ $stats['owners_count'] }}</div>
                        <p class="text-xs text-emerald-600 mt-2">محافظ عقارية مدارة</p>
                    </div>

                    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                        <div class="text-xs font-semibold text-slate-500 mb-2">العقارات والوحدات</div>
                        <div class="text-3xl font-bold text-slate-900">{{ $stats['properties_count'] }} <span class="text-sm font-normal text-slate-500">عقار ({{ $stats['units_count'] }} وحدة)</span></div>
                        <p class="text-xs text-slate-500 mt-2">شاغر: <b class="text-emerald-600">{{ $stats['vacant_units'] }}</b> | مؤجر: <b class="text-blue-600">{{ $stats['rented_units'] }}</b></p>
                    </div>

                    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                        <div class="text-xs font-semibold text-slate-500 mb-2">إجمالي قيمة العقود النشطة</div>
                        <div class="text-2xl font-bold text-emerald-600">{{ number_format($stats['total_rent_value'], 2) }} <span class="text-xs">ر.س</span></div>
                        <p class="text-xs text-slate-400 mt-2">عدد العقود الفعالة: {{ $stats['active_contracts'] }}</p>
                    </div>

                    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                        <div class="text-xs font-semibold text-slate-500 mb-2"> مستحقات نظام العقار </div>
                        <div class="text-2xl font-bold text-amber-600">{{ number_format($stats['company_due_fees'], 2) }} <span class="text-xs">ر.س</span></div>
                        <p class="text-xs text-slate-400 mt-2">بناءً على نسبة الاتفاق ({{ $office->company_commission_rate }}%)</p>
                    </div>
                </div>

                <!-- أحدث العقود المبرمة -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="font-bold text-slate-900">أحدث عقود الإيجار </h3>
                        <a href="{{ route('office.dashboard', ['tab' => 'contracts']) }}" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold">+ إبرام عقد جديد</a>
                    </div>
                    <table class="w-full text-sm text-right">
                        <thead class="bg-slate-50 text-slate-600 text-xs border-b">
                            <tr>
                                <th class="py-3.5 px-5">رقم العقد</th>
                                <th class="py-3.5 px-5">العقار / الوحدة</th>
                                <th class="py-3.5 px-5">المالك</th>
                                <th class="py-3.5 px-5">المستأجر</th>
                                <th class="py-3.5 px-5">قيمة الإيجار</th>
                                <th class="py-3.5 px-5">حالة منصة إيجار</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($contracts->take(5) as $c)
                                <tr>
                                    <td class="py-4 px-5 font-mono font-bold">{{ $c->contract_number }}</td>
                                    <td class="py-4 px-5 font-bold">{{ $c->property_title }} {{ $c->unit_number ? '(وحدة ' . $c->unit_number . ')' : '' }}</td>
                                    <td class="py-4 px-5">{{ $c->owner_name }}</td>
                                    <td class="py-4 px-5">{{ $c->tenant_name }}</td>
                                    <td class="py-4 px-5 font-bold text-emerald-700">{{ number_format($c->total_rent_amount, 2) }} ر.س</td>
                                    <td class="py-4 px-5">
                                        @if($c->ejar_status == 'active')
                                            <span class="px-2.5 py-1 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700">موثق في إيجار ({{ $c->ejar_contract_number }})</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-xl text-xs font-bold bg-amber-50 text-amber-700">بانتظار الإرسال لإيجار</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-8 text-center text-slate-400">لا توجد عقود مضافة بعد. ابدأ بإضافة مالك ثم عقار ثم عقد إيجار.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- 2. تبويب الملاك (Owners) -->
            @if($tab == 'owners')
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="font-bold text-base text-slate-900">سجل ملاك العقارات </h3>
                        </div>
                        <div class="flex items-center gap-3 flex-1 max-w-md">
                            <input type="text" id="ownerSearchInput" oninput="filterTableRows('ownerSearchInput', 'ownersTableBody')"
                                   placeholder="ابحث باسم المالك أو الجوال أو الهوية..."
                                   class="w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-xs focus:outline-none focus:border-emerald-600">
                        </div>
                        <button type="button" onclick="openModal('addOwnerModal')"
                                class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs shadow-lg shadow-emerald-600/20 transition flex items-center gap-2">
                            <span>+ إضافة مالك جديد</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-right">
                            <thead class="bg-slate-50 text-slate-600 text-xs border-b">
                                <tr>
                                    <th class="py-3.5 px-5">اسم المالك والهوية</th>
                                    <th class="py-3.5 px-5">الجوال واسم المستخدم</th>
                                    <th class="py-3.5 px-5">الحساب البنكي</th>
                                    <th class="py-3.5 px-5">نسبة المكتب</th>
                                    <th class="py-3.5 px-5 text-center">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="ownersTableBody" class="divide-y divide-slate-100">
                                @forelse($owners as $owner)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-4 px-5">
                                            <div class="font-bold text-slate-900">{{ $owner->name }}</div>
                                            <div class="text-xs text-slate-400 font-mono">هوية: {{ $owner->national_id }}</div>
                                        </td>
                                        <td class="py-4 px-5">
                                            <div class="font-mono text-xs font-bold text-emerald-700" dir="ltr">{{ $owner->phone }}</div>
                                            <div class="text-xs text-slate-500">المستخدم: <span class="font-mono">{{ $owner->owner_username }}</span></div>
                                        </td>
                                        <td class="py-4 px-5">
                                            <div class="text-xs font-semibold">{{ $owner->bank_name ?: '-' }}</div>
                                            <div class="text-[11px] font-mono text-slate-500" dir="ltr">{{ $owner->iban ?: '-' }}</div>
                                        </td>
                                        <td class="py-4 px-5 font-bold text-amber-700">{{ $owner->office_management_fee_rate }}%</td>
                                        <td class="py-4 px-5 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                @if(auth()->user()->user_type === 'company_admin')
                                                    <a href="{{ route('office.switch_owner', $owner->id) }}"
                                                       class="px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white text-xs font-bold transition">
                                                         دخول لحساب المالك 
                                                    </a>
                                                @endif
                                                <button type="button" onclick="openModal('editOwnerModal_{{ $owner->id }}')"
                                                        class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-white text-xs font-semibold transition">
                                                    تعديل
                                                </button>
                                                <form action="{{ route('office.owners.delete', $owner->id) }}" method="POST" onsubmit="return confirm('حذف هذا المالك؟');">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-red-50 text-red-600 text-xs font-semibold">حذف</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="py-8 text-center text-slate-400">لا يوجد ملاك مضافون بعد.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @foreach($owners as $owner)
                    @php $ownerLocalPhone = \App\Http\Controllers\CompanyErpController::toLocalPhone($owner->phone); @endphp
                    <div id="editOwnerModal_{{ $owner->id }}" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto">
                            <div class="flex items-center justify-between pb-3 border-b mb-4">
                                <h3 class="font-bold text-base text-slate-900">تعديل بيانات المالك: {{ $owner->name }}</h3>
                                <button type="button" onclick="closeModal('editOwnerModal_{{ $owner->id }}')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                            </div>
                            <form action="{{ route('office.owners.update', $owner->id) }}" method="POST" class="space-y-3.5">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">اسم المالك *</label>
                                    <input type="text" name="name" value="{{ $owner->name }}" required class="w-full rounded-xl border px-3.5 py-2 text-sm">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">رقم الهوية / السجل *</label>
                                        <input type="text" name="national_id" value="{{ $owner->national_id }}" required class="w-full rounded-xl border px-3 py-2 text-xs font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">نسبة إدارة المكتب (%)</label>
                                        <input type="number" step="0.1" name="office_management_fee_rate" value="{{ $owner->office_management_fee_rate }}" class="w-full rounded-xl border px-3 py-2 text-xs">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">الرقم الضريبي (اختياري)</label>
                                        <input type="text" name="tax_number" value="{{ $owner->tax_number ?? '' }}" maxlength="15" class="w-full rounded-xl border px-3 py-2 text-xs font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">العنوان الوطني (اختياري)</label>
                                        <input type="text" name="national_address" value="{{ $owner->national_address ?? '' }}" class="w-full rounded-xl border px-3 py-2 text-xs">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">البنك</label>
                                        <input type="text" name="bank_name" value="{{ $owner->bank_name }}" class="w-full rounded-xl border px-3 py-2 text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">الآيبان (IBAN)</label>
                                        <input type="text" name="iban" value="{{ $owner->iban }}" dir="ltr" class="w-full rounded-xl border px-3 py-2 text-xs font-mono">
                                    </div>
                                </div>
                                <div class="p-3 rounded-2xl bg-slate-50 border space-y-2.5">
                                    <div class="text-xs font-bold text-emerald-700">تعديل بيانات الدخول للمالك</div>
                                    <div class="grid grid-cols-2 gap-2.5">
                                        <div>
                                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">رقم الجوال (10 أرقام) *</label>
                                            <input type="tel" name="phone" value="{{ $ownerLocalPhone }}" maxlength="10" required dir="ltr" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);" class="w-full rounded-xl border px-3 py-1.5 text-xs font-mono bg-white">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">اسم المستخدم *</label>
                                            <input type="text" name="username" value="{{ $owner->owner_username }}" required class="w-full rounded-xl border px-3 py-1.5 text-xs bg-white">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">كلمة مرور جديدة (اتركها فارغة لعدم التغيير)</label>
                                        <input type="text" name="password" placeholder="أدخل كلمة مرور جديدة فقط عند الرغبة" class="w-full rounded-xl border px-3 py-1.5 text-xs bg-white">
                                    </div>
                                </div>
                                <div class="flex justify-end gap-2 pt-3 border-t">
                                    <button type="button" onclick="closeModal('editOwnerModal_{{ $owner->id }}')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                    <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">حفظ التعديلات</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach

                <!-- نافذة إضافة مالك جديد -->
                <div id="addOwnerModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                            <div>
                                <h3 class="font-bold text-base text-slate-900">إضافة مالك عقار جديد</h3>
                                <p class="text-xs text-slate-500">ينشئ النظام تلقائياً حساباً للمالك ليتابع عقاراته عبر الجوال</p>
                            </div>
                            <button type="button" onclick="closeModal('addOwnerModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg">✕</button>
                        </div>

                        <form action="{{ route('office.owners.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">اسم المالك رباعياً *</label>
                                <input type="text" name="name" required placeholder="مثال: عبدالله بن محمد القحطاني"
                                       class="w-full rounded-2xl border border-slate-300 px-3.5 py-2.5 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">رقم الهوية الوطنية / السجل *</label>
                                <input type="text" name="national_id" required maxlength="10" placeholder="10XXXXXXXX"
                                       class="w-full rounded-2xl border border-slate-300 px-3.5 py-2.5 text-sm font-mono">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">الرقم الضريبي للمالك (اختياري)</label>
                                    <input type="text" name="tax_number" maxlength="15" placeholder="300XXXXXXXXXXXX"
                                           class="w-full rounded-2xl border border-slate-300 px-3 py-2 text-xs font-mono">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">العنوان الوطني للمالك (اختياري)</label>
                                    <input type="text" name="national_address" placeholder="الرياض - حي..."
                                           class="w-full rounded-2xl border border-slate-300 px-3 py-2 text-xs">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">اسم البنك</label>
                                    <input type="text" name="bank_name" placeholder="مصرف الراجحي"
                                           class="w-full rounded-2xl border border-slate-300 px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">نسبة المكتب للإدارة (%)</label>
                                    <input type="number" step="0.1" name="office_management_fee_rate" value="5"
                                           class="w-full rounded-2xl border border-slate-300 px-3 py-2 text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">رقم الآيبان (IBAN) لتحويل الإيجارات</label>
                                <input type="text" name="iban" placeholder="SA0000000000000000000000" dir="ltr"
                                       class="w-full text-left font-mono rounded-2xl border border-slate-300 px-3.5 py-2 text-sm">
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                                <div class="text-xs font-bold text-emerald-700">بيانات دخول المالك للنظام</div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">رقم الجوال (10 أرقام يبدأ بـ 05) *</label>
                                    <input type="tel" name="phone" maxlength="10" required placeholder="05XXXXXXXX" dir="ltr"
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                                           class="w-full text-left font-mono rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white">
                                </div>
                                <div class="grid grid-cols-2 gap-2.5">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">اسم المستخدم *</label>
                                        <input type="text" name="username" required placeholder="owner_ali"
                                               class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">كلمة المرور *</label>
                                        <input type="text" name="password" value="123456" required
                                               class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white">
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-2 pt-3 border-t">
                                <button type="button" onclick="closeModal('addOwnerModal')" class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold">إلغاء</button>
                                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-600/20">
                                    حفظ المالك وتفعيل حسابه
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- 3. تبويب العقارات والصكوك والوحدات (جدول مرتب + أزرار الإضافة) -->
            @if($tab == 'properties')
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="font-bold text-base text-slate-900">سجل العقارات والصكوك والوحدات التابعة لها</h3>
                            <p class="text-xs text-slate-500 mt-0.5">جميع الصكوك والعقارات المسجلة بالمكتب مع إمكانية استعراض وإضافة الوحدات الداخلية والمرفقات</p>
                        </div>

                        <div class="flex items-center gap-3 flex-1 max-w-md">
                            <input type="text" id="officePropertySearch" oninput="filterTableRows('officePropertySearch', 'officePropertiesTableBody')"
                                   placeholder="🔍 ابحث بمسمى العقار، رقم الصك، المالك، أو الحي..."
                                   class="w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-xs focus:outline-none focus:border-emerald-600">
                        </div>

                        <button type="button" onclick="openModal('addPropertyModal')"
                                class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs shadow-lg shadow-emerald-600/20 flex items-center gap-2 transition">
                            <span>➕</span>
                            <span>إضافة عقار وصك جديد</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-right">
                            <thead class="bg-slate-50 text-slate-600 text-xs border-b border-slate-200/80">
                                <tr>
                                    <th class="py-4 px-4">#</th>
                                    <th class="py-4 px-4">مسمى العقار والنوع</th>
                                    <th class="py-4 px-4">المالك</th>
                                    <th class="py-4 px-4">رقم الصك والمساحة</th>
                                    <th class="py-4 px-4">العنوان الوطني</th>
                                    <th class="py-4 px-4">المواصفات والمرفقات</th>
                                    <th class="py-4 px-4 text-center">الوحدات الداخلية</th>
                                    <th class="py-4 px-4 text-center">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="officePropertiesTableBody" class="divide-y divide-slate-100">
                                @forelse($properties as $idx => $prop)
                                    @php
                                        $propUnits = $units->where('property_id', $prop->id);
                                        $savedAttrs = json_decode($prop->custom_attributes ?? '{}', true) ?: [];
                                    @endphp
                                    <tr class="hover:bg-slate-50/90 transition">
                                        <td class="py-4 px-4 font-mono text-xs text-slate-400 font-bold">{{ $idx + 1 }}</td>
                                        <td class="py-4 px-4">
                                            <div class="font-bold text-slate-900">{{ $prop->title }}</div>
                                            <span class="inline-block mt-1 px-2.5 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 text-[11px] font-bold border border-emerald-200">{{ $prop->type_name }}</span>
                                        </td>
                                        <td class="py-4 px-4 font-bold text-slate-800 text-xs">{{ $prop->owner_name }}</td>
                                        <td class="py-4 px-4">
                                            <div class="font-mono text-xs font-bold text-emerald-700">صك: {{ $prop->deed_number }}</div>
                                            <div class="text-xs text-slate-500 mt-0.5">المساحة: <b>{{ number_format($prop->total_area, 0) }} م²</b></div>
                                        </td>
                                        <td class="py-4 px-4 text-xs text-slate-600">
                                            <div class="font-semibold text-slate-800">{{ $prop->city }} - {{ $prop->district }}</div>
                                            @if($prop->street || $prop->building_number)
                                                <div class="text-[11px] text-slate-400">{{ $prop->street }} {{ $prop->building_number ? 'مبنى ' . $prop->building_number : '' }}</div>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4">
                                            @if(count($savedAttrs) > 0)
                                                <div class="flex flex-wrap gap-1 max-w-xs">
                                                    @foreach($savedAttrs as $k => $v)
                                                        @if(!empty($v))
                                                            @if(is_string($v) && str_starts_with($v, '/uploads/'))
                                                                <a href="{{ $v }}" target="_blank" class="px-2.5 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-bold hover:underline">
                                                                    📎 {{ $k }} (عرض المرفق)
                                                                </a>
                                                            @else
                                                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px]">
                                                                    {{ $k }}: <b>{{ $v }}</b>
                                                                </span>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-xs text-slate-400">-</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            @if($prop->has_units)
                                                <div class="flex items-center justify-center gap-1.5">
                                                    <button type="button" onclick="openModal('viewUnitsModal_{{ $prop->id }}')"
                                                            class="px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-600 text-blue-700 hover:text-white border border-blue-200 text-xs font-bold transition">
                                                        🏢 الوحدات ({{ $propUnits->count() }})
                                                    </button>
                                                    <button type="button" onclick="openAddUnitModal('{{ $prop->id }}', '{{ addslashes($prop->title) }}')"
                                                            class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-sm">
                                                        + وحدة
                                                    </button>
                                                </div>
                                            @else
                                                <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-500 text-xs">عقار مستقل</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <div class="flex items-center justify-center gap-1.5">
                                                <button type="button" onclick="openModal('editPropertyModal_{{ $prop->id }}')"
                                                        class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-white text-xs font-bold transition">تعديل</button>
                                                <form action="{{ route('office.properties.delete', $prop->id) }}" method="POST" onsubmit="return confirm('حذف هذا العقار؟');">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-red-50 hover:bg-red-600 text-red-600 hover:text-white text-xs font-bold transition">حذف</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="py-12 text-center text-slate-400">لا توجد عقارات مسجلة بعد. اضغط على "إضافة عقار وصك جديد" في أعلى اليسار.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @foreach($properties as $prop)
                    <div id="editPropertyModal_{{ $prop->id }}" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto">
                            <div class="flex items-center justify-between pb-3 border-b mb-4">
                                <h3 class="font-bold text-base text-slate-900">تعديل العقار والصك: {{ $prop->title }}</h3>
                                <button type="button" onclick="closeModal('editPropertyModal_{{ $prop->id }}')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                            </div>
                            <form action="{{ route('office.properties.update', $prop->id) }}" method="POST" class="space-y-3.5">
                                @csrf
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">المالك *</label>
                                        <select name="owner_id" required class="w-full rounded-xl border px-3 py-2 text-xs bg-white font-bold">
                                            @foreach($owners as $ow)
                                                <option value="{{ $ow->id }}" {{ $prop->owner_id == $ow->id ? 'selected' : '' }}>{{ $ow->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">نوع العقار *</label>
                                        <select name="property_type_id" required class="w-full rounded-xl border px-3 py-2 text-xs bg-white font-bold">
                                            @foreach($propertyTypes as $pt)
                                                <option value="{{ $pt->id }}" {{ $prop->property_type_id == $pt->id ? 'selected' : '' }}>{{ $pt->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">مسمى العقار *</label>
                                    <input type="text" name="title" value="{{ $prop->title }}" required class="w-full rounded-xl border px-3.5 py-2 text-sm font-bold">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">رقم الصك *</label>
                                        <input type="text" name="deed_number" value="{{ $prop->deed_number }}" required class="w-full rounded-xl border px-3 py-2 text-xs font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">تاريخ الصك</label>
                                        <input type="date" name="deed_date" value="{{ $prop->deed_date }}" class="w-full rounded-xl border px-3 py-2 text-xs">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">المساحة الإجمالية (م²) *</label>
                                        <input type="number" step="0.01" name="total_area" value="{{ $prop->total_area }}" required class="w-full rounded-xl border px-3 py-2 text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">القيمة التقديرية</label>
                                        <input type="number" step="0.01" name="estimated_value" value="{{ $prop->estimated_value }}" class="w-full rounded-xl border px-3 py-2 text-xs">
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-2">
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">المدينة *</label>
                                        <input type="text" name="city" value="{{ $prop->city }}" required class="w-full rounded-xl border px-2.5 py-1.5 text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">الحي *</label>
                                        <input type="text" name="district" value="{{ $prop->district }}" required class="w-full rounded-xl border px-2.5 py-1.5 text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">الشارع</label>
                                        <input type="text" name="street" value="{{ $prop->street }}" class="w-full rounded-xl border px-2.5 py-1.5 text-xs">
                                    </div>
                                </div>
                                <div class="flex justify-end gap-2 pt-3 border-t">
                                    <button type="button" onclick="closeModal('editPropertyModal_{{ $prop->id }}')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                    <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">حفظ التعديلات</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach

                @foreach($properties as $prop)
                    @if($prop->has_units)
                        @php $propUnits = $units->where('property_id', $prop->id); @endphp
                        <div id="viewUnitsModal_{{ $prop->id }}" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                            <div class="bg-white rounded-3xl max-w-3xl w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] flex flex-col">
                                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                                    <div>
                                        <h3 class="font-bold text-base text-slate-900">🏢 الوحدات العقارية التابعة لـ: <span class="text-emerald-700">{{ $prop->title }}</span></h3>
                                        <p class="text-xs text-slate-500">إجمالي الوحدات: {{ $propUnits->count() }}</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="closeModal('viewUnitsModal_{{ $prop->id }}'); openAddUnitModal('{{ $prop->id }}', '{{ addslashes($prop->title) }}');"
                                                class="px-3.5 py-1.5 rounded-xl bg-emerald-600 text-white text-xs font-bold">
                                            + إضافة وحدة جديدة
                                        </button>
                                        <button type="button" onclick="closeModal('viewUnitsModal_{{ $prop->id }}')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                                    </div>
                                </div>
                                <div class="overflow-y-auto flex-1 rounded-2xl border border-slate-200">
                                    <table class="w-full text-xs text-right">
                                        <thead class="bg-slate-50 text-slate-600 border-b">
                                            <tr>
                                                <th class="py-3 px-4">رقم الوحدة</th>
                                                <th class="py-3 px-4">الدور</th>
                                                <th class="py-3 px-4">المساحة</th>
                                                <th class="py-3 px-4">عداد الكهرباء / المياه</th>
                                                <th class="py-3 px-4">الإيجار السنوي</th>
                                                <th class="py-3 px-4">الحالة</th>
                                                <th class="py-3 px-4 text-center">حذف</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            @forelse($propUnits as $u)
                                                <tr>
                                                    <td class="py-3 px-4 font-bold text-slate-900">{{ $u->unit_number }}</td>
                                                    <td class="py-3 px-4">{{ $u->floor_number }}</td>
                                                    <td class="py-3 px-4">{{ $u->area }} م²</td>
                                                    <td class="py-3 px-4 font-mono">كهرباء: {{ $u->electricity_meter ?: '-' }} | ماء: {{ $u->water_meter ?: '-' }}</td>
                                                    <td class="py-3 px-4 font-bold text-emerald-700">{{ number_format($u->default_rent_price, 2) }} ر.س</td>
                                                    <td class="py-3 px-4">
                                                        @if($u->status == 'vacant')
                                                            <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 font-bold">شاغرة</span>
                                                        @else
                                                            <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-bold">مؤجرة</span>
                                                        @endif
                                                    </td>
                                                    <td class="py-3 px-4 text-center">
                                                        <form action="{{ route('office.units.delete', $u->id) }}" method="POST" onsubmit="return confirm('حذف الوحدة؟');">
                                                            @csrf
                                                            <button type="submit" class="text-red-600 font-bold">حذف</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="7" class="py-8 text-center text-slate-400">لا توجد وحدات مضافة داخل هذا العقار بعد.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

                <div id="addPropertyModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-slate-200 max-h-[92vh] overflow-y-auto">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                            <div>
                                <h3 class="font-bold text-base text-slate-900">إضافة عقار وصك ملكية جديد</h3>
                                <p class="text-xs text-slate-500">عند اختيار نوع العقار ستظهر تلقائياً المتطلبات المعتمدة وحقول رفع PDF/الصور</p>
                            </div>
                            <button type="button" onclick="closeModal('addPropertyModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                        </div>

                        <form action="{{ route('office.properties.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">اختر المالك *</label>
                                    <select name="owner_id" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white">
                                        <option value="">-- اختر المالك --</option>
                                        @foreach($owners as $ow)
                                            <option value="{{ $ow->id }}">{{ $ow->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">نوع العقار *</label>
                                    <select name="property_type_id" id="office_property_type_select" required onchange="renderDynamicAttributes(this.value)"
                                            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white font-semibold text-emerald-800">
                                        <option value="">-- اختر النوع --</option>
                                        @foreach($propertyTypes as $pt)
                                            <option value="{{ $pt->id }}">{{ $pt->name }} ({{ $pt->category }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">مسمى العقار *</label>
                                <input type="text" name="title" required placeholder="مثال: عمارة الياسمين السكنية - رقم 4"
                                       class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm">
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                                <div class="text-xs font-bold text-slate-800">بيانات الصك الشرعي والمساحة</div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">رقم الصك الإلكتروني *</label>
                                        <input type="text" name="deed_number" required placeholder="3101XXXXXXXX"
                                               class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-mono bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">تاريخ الصك</label>
                                        <input type="date" name="deed_date" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">المساحة الإجمالية (م²) *</label>
                                        <input type="number" step="0.01" name="total_area" required placeholder="650"
                                               class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">القيمة التقديرية للعقار</label>
                                        <input type="number" step="0.01" name="estimated_value" placeholder="اختياري"
                                               class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white">
                                    </div>
                                </div>
                            </div>

                            <div id="officeDynamicAttrsBox" class="hidden p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-200 space-y-3">
                                <div class="text-xs font-bold text-emerald-800">📋 المتطلبات والمرفقات الخاصة بنوع العقار المختار:</div>
                                <div id="officeDynamicAttrsInputs" class="grid grid-cols-2 gap-3"></div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                                <div class="text-xs font-bold text-slate-800">العنوان الوطني (للتوثيق في منصة إيجار)</div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">المدينة *</label>
                                        <input type="text" name="city" required value="الرياض" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">الحي *</label>
                                        <input type="text" name="district" required placeholder="حي الملقا" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white">
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-2">
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">الشارع</label>
                                        <input type="text" name="street" class="w-full rounded-xl border border-slate-300 px-2.5 py-1.5 text-xs bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">رقم المبنى</label>
                                        <input type="text" name="building_number" placeholder="4125" class="w-full rounded-xl border border-slate-300 px-2.5 py-1.5 text-xs font-mono bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">الرمز البريدي</label>
                                        <input type="text" name="postal_code" placeholder="13521" class="w-full rounded-xl border border-slate-300 px-2.5 py-1.5 text-xs font-mono bg-white">
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-2 pt-3 border-t">
                                <button type="button" onclick="closeModal('addPropertyModal')" class="px-4 py-2.5 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg">
                                    حفظ العقار والصك
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="addUnitModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                        <div class="flex items-center justify-between pb-3 border-b mb-4">
                            <h3 id="addUnitModalTitle" class="font-bold text-base text-slate-900">إضافة وحدة عقارية</h3>
                            <button type="button" onclick="closeModal('addUnitModal')" class="font-bold text-slate-400">✕</button>
                        </div>
                        <form action="{{ route('office.units.store') }}" method="POST" class="space-y-3.5">
                            @csrf
                            <input type="hidden" name="property_id" id="unit_property_id">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">رقم / رمز الوحدة *</label>
                                    <input type="text" name="unit_number" required placeholder="شقة 101 / محل 1" class="w-full rounded-xl border px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">رقم الدور</label>
                                    <input type="number" name="floor_number" value="1" class="w-full rounded-xl border px-3 py-2 text-sm">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">رقم عداد الكهرباء (لإيجار)</label>
                                    <input type="text" name="electricity_meter" class="w-full rounded-xl border px-3 py-2 text-sm font-mono">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">رقم عداد المياه</label>
                                    <input type="text" name="water_meter" class="w-full rounded-xl border px-3 py-2 text-sm font-mono">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">المساحة (م²)</label>
                                    <input type="number" step="0.01" name="area" placeholder="120" class="w-full rounded-xl border px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">قيمة الإيجار السنوي (ر.س) *</label>
                                    <input type="number" step="0.01" name="default_rent_price" required placeholder="25000" class="w-full rounded-xl border px-3 py-2 text-sm font-bold text-emerald-700">
                                </div>
                            </div>
                            <div class="flex justify-end gap-2 pt-3 border-t">
                                <button type="button" onclick="closeModal('addUnitModal')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">حفظ الوحدة</button>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                    const allTypeAttributes = @json($allAttributes);

                    function validateFileSize5MB(input) {
                        if (input.files && input.files[0]) {
                            const maxBytes = 5 * 1024 * 1024;
                            if (input.files[0].size > maxBytes) {
                                alert('⚠️ حجم الملف يتجاوز 5 ميغابايت! يرجى اختيار ملف PDF أو صورة بحجم لا يزيد عن 5MB.');
                                input.value = '';
                            }
                        }
                    }

                    function renderDynamicAttributes(typeId) {
                        const box = document.getElementById('officeDynamicAttrsBox');
                        const container = document.getElementById('officeDynamicAttrsInputs');
                        container.innerHTML = '';

                        const filtered = allTypeAttributes.filter(a => String(a.property_type_id) === String(typeId));
                        if (filtered.length === 0) {
                            box.classList.add('hidden');
                            return;
                        }

                        box.classList.remove('hidden');
                        filtered.forEach(attr => {
                            let inputHtml = '';
                            if (attr.field_type.includes('PDF') || attr.field_type.includes('ملف') || attr.field_type.includes('صور') || attr.field_type === 'file') {
                                inputHtml = `<input type="file" name="custom_files[${attr.attribute_name}]" accept=".pdf,.jpg,.jpeg,.png,.webp,.gif" onchange="validateFileSize5MB(this)" required class="w-full rounded-xl border border-slate-300 px-3 py-1.5 text-xs bg-white">
                                             <span class="text-[10px] text-emerald-700 block mt-0.5">يسمح بـ PDF أو صورة (بحد أقصى 5MB)</span>`;
                            } else if (attr.field_type.includes('رقم') || attr.field_type === 'number') {
                                inputHtml = `<input type="number" step="any" name="custom_attrs[${attr.attribute_name}]" required class="w-full rounded-xl border border-slate-300 px-3 py-1.5 text-xs bg-white">`;
                            } else if (attr.field_type.includes('تاريخ') || attr.field_type === 'date') {
                                inputHtml = `<input type="date" name="custom_attrs[${attr.attribute_name}]" required class="w-full rounded-xl border border-slate-300 px-3 py-1.5 text-xs bg-white">`;
                            } else if (attr.field_type.includes('نعم') || attr.field_type === 'boolean') {
                                inputHtml = `<select name="custom_attrs[${attr.attribute_name}]" class="w-full rounded-xl border border-slate-300 px-3 py-1.5 text-xs bg-white"><option value="نعم">نعم</option><option value="لا">لا</option></select>`;
                            } else {
                                inputHtml = `<input type="text" name="custom_attrs[${attr.attribute_name}]" required class="w-full rounded-xl border border-slate-300 px-3 py-1.5 text-xs bg-white">`;
                            }

                            const div = document.createElement('div');
                            div.innerHTML = `<label class="block text-[11px] font-bold text-slate-700 mb-1">${attr.attribute_name} *</label>${inputHtml}`;
                            container.appendChild(div);
                        });
                    }

                    function openAddUnitModal(propId, propTitle) {
                        document.getElementById('unit_property_id').value = propId;
                        document.getElementById('addUnitModalTitle').innerText = 'إضافة وحدة داخل: ' + propTitle;
                        openModal('addUnitModal');
                    }
                </script>
            @endif

            <!-- 4. تبويب المستأجرين وعقود الإيجار (الجدول أولاً + ربط المستأجرين بالشجرة متسلسلاً + كشف حساب لكل مستأجر) -->
            @if($tab == 'contracts')
                <!-- أولاً: جدول المستأجرين المرتبطين بشجرة الحسابات -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden mb-6">
                    <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="font-bold text-base text-slate-900">بيانات المستأجرين </h3>
                        </div>

                        <div class="flex items-center gap-3 flex-1 max-w-md">
                            <input type="text" id="tenantSearchInput" oninput="filterTableRows('tenantSearchInput', 'tenantsTableBody')"
                                   placeholder="🔍 بحث فوري باسم المستأجر أو رقم الجوال أو الهوية..."
                                   class="w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-xs focus:outline-none focus:border-emerald-600">
                        </div>

                        <div class="flex items-center gap-2.5">
                            <button type="button" onclick="openModal('addTenantModal')"
                                    class="px-5 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-lg transition">
                                 إضافة مستأجر جديد
                            </button>
                            <button type="button" onclick="openModal('addContractModal')"
                                    class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs shadow-lg shadow-emerald-600/20 transition">
                                 إبرام عقد إيجار جديد
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-right">
                            <thead class="bg-slate-50 text-slate-600 text-xs border-b">
                                <tr>
                                    <th class="py-3.5 px-5">اسم المستأجر </th>
                                    <th class="py-3.5 px-5">رقم الهوية / الإقامة</th>
                                    <th class="py-3.5 px-5">رقم الجوال</th>
                                    <th class="py-3.5 px-5">رقم الحساب</th>
                                    <th class="py-3.5 px-5">الرصيد / المسدد</th>
                                    <th class="py-3.5 px-5 text-center">إجراء</th>
                                </tr>
                            </thead>
                            <tbody id="tenantsTableBody" class="divide-y divide-slate-100">
                                @forelse($tenants as $tn)
                                    @php
                                        $tnAcc = $accounts->firstWhere('id', $tn->account_id ?? null);
                                        $tnVouchers = $vouchers->where('tenant_id', $tn->id);
                                        $tnReceipts = $tnVouchers->where('voucher_type', 'receipt')->sum('amount');
                                        $tnPayments = $tnVouchers->where('voucher_type', 'payment')->sum('amount');
                                    @endphp
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-4 px-5">
                                            <button type="button" onclick="openModal('tenantStatementModal_{{ $tn->id }}')"
                                                    class="font-bold text-emerald-700 hover:text-emerald-900 underline flex items-center gap-1.5 text-right">
                                                <span>👤 {{ $tn->name }}</span>
                                                <span class="text-[10px] px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">كشف حساب ({{ $tnVouchers->count() }})</span>
                                            </button>
                                        </td>
                                        <td class="py-4 px-5 font-mono text-xs">{{ $tn->id_number }}</td>
                                        <td class="py-4 px-5 font-mono text-xs font-bold text-slate-700" dir="ltr">{{ $tn->phone }}</td>
                                        <td class="py-4 px-5">
                                            @if($tnAcc)
                                                <span class="px-2.5 py-1 rounded-xl bg-slate-100 font-mono text-xs font-bold text-slate-800">{{ $tnAcc->code }} - {{ $tnAcc->name }}</span>
                                            @else
                                                <span class="text-xs text-slate-400">غير مرتبط</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-5">
                                            <span class="font-bold text-emerald-700">{{ number_format($tnReceipts, 2) }} ر.س</span>
                                        </td>
                                        <td class="py-4 px-5 text-center">
                                            <div class="flex items-center justify-center gap-1.5">
                                                <button type="button" onclick="openModal('tenantStatementModal_{{ $tn->id }}')"
                                                        class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 text-xs font-semibold transition">
                                                    📄 كشف الحساب
                                                </button>
                                                <button type="button" onclick="openModal('editTenantModal_{{ $tn->id }}')"
                                                        class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-white text-xs font-semibold transition">
                                                    تعديل
                                                </button>
                                                <form action="{{ route('office.tenants.delete', $tn->id) }}" method="POST" onsubmit="return confirm('حذف المستأجر؟');">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-red-50 text-red-600 text-xs font-semibold">حذف</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="py-8 text-center text-slate-400">لا يوجد مستأجرون مسجلون بعد. اضغط على "+ إضافة مستأجر جديد" بالزاوية اليسرى.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ثانياً: جدول عقود الإيجار -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="font-bold text-base text-slate-900">سجل عقود الإيجار والربط مع منصة إيجار</h3>
                            <p class="text-xs text-slate-500 mt-0.5">جميع العقود السارية والموثقة مع احتساب عمولة المكتب وحصة الشركة</p>
                        </div>
                        <div class="flex items-center gap-3 flex-1 max-w-md">
                            <input type="text" id="contractSearchInput" oninput="filterTableRows('contractSearchInput', 'contractsTableBody')"
                                   placeholder="🔍 ابحث برقم العقد أو اسم المستأجر أو العقار..."
                                   class="w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-xs focus:outline-none focus:border-emerald-600">
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-right">
                            <thead class="bg-slate-50 text-slate-600 text-xs border-b">
                                <tr>
                                    <th class="py-3.5 px-4">رقم العقد والوحدة</th>
                                    <th class="py-3.5 px-4">المالك والمستأجر</th>
                                    <th class="py-3.5 px-4">قيمة العقد والعمولات</th>
                                    <th class="py-3.5 px-4 text-center">التوثيق في إيجار</th>
                                </tr>
                            </thead>
                            <tbody id="contractsTableBody" class="divide-y divide-slate-100">
                                @forelse($contracts as $cnt)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-4 px-4">
                                            <div class="font-mono font-bold text-slate-900">{{ $cnt->contract_number }}</div>
                                            <div class="text-xs font-semibold text-emerald-700">{{ $cnt->property_title }} {{ $cnt->unit_number ? '- وحدة ' . $cnt->unit_number : '' }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $cnt->start_date }} إلى {{ $cnt->end_date }}</div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="text-xs">المالك: <b>{{ $cnt->owner_name }}</b></div>
                                            <div class="text-xs text-slate-600">المستأجر: <b>{{ $cnt->tenant_name }}</b></div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="font-bold text-slate-900">{{ number_format($cnt->total_rent_amount, 2) }} ر.س</div>
                                            <div class="text-[11px] text-emerald-600">عمولة المكتب: {{ number_format($cnt->office_commission, 2) }} ر.س</div>
                                            @if($cnt->company_fee_amount > 0)
                                                <div class="text-[11px] text-amber-700 font-semibold">حصة الشركة الأم: {{ number_format($cnt->company_fee_amount, 2) }} ر.س</div>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                                @if($cnt->ejar_status == 'active')
                                                    <span class="inline-block px-2.5 py-1 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-bold">
                                                        ✅ موثق ({{ $cnt->ejar_contract_number }})
                                                    </span>
                                                @else
                                                    <form action="{{ route('office.contracts.sync_ejar', $cnt->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-white text-xs font-bold shadow-sm transition">
                                                            ⚡ توثيق بإيجار
                                                        </button>
                                                    </form>
                                                @endif
                                                <button type="button" onclick="openModal('editContractModal_{{ $cnt->id }}')"
                                                        class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-white text-xs font-semibold transition">
                                                    تعديل
                                                </button>
                                                <form action="{{ route('office.contracts.delete', $cnt->id) }}" method="POST" onsubmit="return confirm('حذف هذا العقد؟');">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-red-50 text-red-600 text-xs font-semibold">حذف</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="py-10 text-center text-slate-400">لا توجد عقود إيجار مسجلة بعد.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @foreach($tenants as $tn)
                    @php $tnLocalPhone = \App\Http\Controllers\CompanyErpController::toLocalPhone($tn->phone); @endphp
                    <div id="editTenantModal_{{ $tn->id }}" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                            <div class="flex items-center justify-between pb-3 border-b mb-4">
                                <h3 class="font-bold text-base text-slate-900">تعديل بيانات المستأجر: {{ $tn->name }}</h3>
                                <button type="button" onclick="closeModal('editTenantModal_{{ $tn->id }}')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                            </div>
                            <form action="{{ route('office.tenants.update', $tn->id) }}" method="POST" class="space-y-3.5">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">اسم المستأجر *</label>
                                    <input type="text" name="name" value="{{ $tn->name }}" required class="w-full rounded-xl border px-3.5 py-2 text-sm font-bold">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">نوع الهوية</label>
                                        <select name="id_type" class="w-full rounded-xl border px-3 py-2 text-xs bg-white">
                                            <option value="national_id" {{ $tn->id_type == 'national_id' ? 'selected' : '' }}>هوية وطنية</option>
                                            <option value="iqama" {{ $tn->id_type == 'iqama' ? 'selected' : '' }}>إقامة مقيم</option>
                                            <option value="cr" {{ $tn->id_type == 'cr' ? 'selected' : '' }}>سجل تجاري</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">رقم الهوية / السجل *</label>
                                        <input type="text" name="id_number" value="{{ $tn->id_number }}" required class="w-full rounded-xl border px-3 py-2 text-xs font-mono">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">رقم الجوال (10 أرقام) *</label>
                                    <input type="tel" name="phone" value="{{ $tnLocalPhone }}" maxlength="10" required dir="ltr" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);" class="w-full rounded-xl border px-3.5 py-2 text-sm font-mono">
                                </div>
                                <div class="grid grid-cols-2 gap-2.5">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">الرقم الضريبي (اختياري)</label>
                                        <input type="text" name="tax_number" value="{{ $tn->tax_number ?? '' }}" maxlength="15" class="w-full rounded-xl border px-3 py-2 text-xs font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">العنوان الوطني (اختياري)</label>
                                        <input type="text" name="national_address" value="{{ $tn->national_address ?? '' }}" class="w-full rounded-xl border px-3 py-2 text-xs">
                                    </div>
                                </div>
                                <div class="flex justify-end gap-2 pt-3 border-t">
                                    <button type="button" onclick="closeModal('editTenantModal_{{ $tn->id }}')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                    <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">حفظ التعديلات</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach

                @foreach($contracts as $cnt)
                    <div id="editContractModal_{{ $cnt->id }}" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto">
                            <div class="flex items-center justify-between pb-3 border-b mb-4">
                                <h3 class="font-bold text-base text-slate-900">تعديل عقد الإيجار: {{ $cnt->contract_number }}</h3>
                                <button type="button" onclick="closeModal('editContractModal_{{ $cnt->id }}')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                            </div>
                            <form action="{{ route('office.contracts.update', $cnt->id) }}" method="POST" class="space-y-3.5">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">العقار *</label>
                                    <select name="property_id" required class="w-full rounded-xl border px-3 py-2 text-xs bg-white font-bold">
                                        @foreach($properties as $pr)
                                            <option value="{{ $pr->id }}" {{ $cnt->property_id == $pr->id ? 'selected' : '' }}>{{ $pr->title }} ({{ $pr->owner_name }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">الوحدة العقارية</label>
                                    <select name="property_unit_id" class="w-full rounded-xl border px-3 py-2 text-xs bg-white">
                                        <option value="">-- كامل العقار --</option>
                                        @foreach($units as $un)
                                            <option value="{{ $un->id }}" {{ $cnt->property_unit_id == $un->id ? 'selected' : '' }}>{{ $un->property_title }} - {{ $un->unit_number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">المستأجر *</label>
                                    <select name="tenant_id" required class="w-full rounded-xl border px-3 py-2 text-xs bg-white font-bold">
                                        @foreach($tenants as $tn)
                                            <option value="{{ $tn->id }}" {{ $cnt->tenant_id == $tn->id ? 'selected' : '' }}>{{ $tn->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">بداية العقد *</label>
                                        <input type="date" name="start_date" value="{{ $cnt->start_date }}" required class="w-full rounded-xl border px-3 py-2 text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">نهاية العقد *</label>
                                        <input type="date" name="end_date" value="{{ $cnt->end_date }}" required class="w-full rounded-xl border px-3 py-2 text-xs">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">إجمالي الإيجار (ر.س) *</label>
                                        <input type="number" step="0.01" name="total_rent_amount" value="{{ $cnt->total_rent_amount }}" required class="w-full rounded-xl border px-3 py-2 text-sm font-bold text-emerald-700">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">عمولة المكتب (ر.س)</label>
                                        <input type="number" step="0.01" name="office_commission" value="{{ $cnt->office_commission }}" class="w-full rounded-xl border px-3 py-2 text-sm">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">رقم عقد إيجار الموحد</label>
                                        <input type="text" name="ejar_contract_number" value="{{ $cnt->ejar_contract_number }}" class="w-full rounded-xl border px-3 py-2 text-xs font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">حالة العقد</label>
                                        <select name="status" class="w-full rounded-xl border px-3 py-2 text-xs bg-white">
                                            <option value="active" {{ $cnt->status == 'active' ? 'selected' : '' }}>ساري (نشط)</option>
                                            <option value="expired" {{ $cnt->status == 'expired' ? 'selected' : '' }}>منتهي</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="flex justify-end gap-2 pt-3 border-t">
                                    <button type="button" onclick="closeModal('editContractModal_{{ $cnt->id }}')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                    <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">حفظ التعديلات</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach

                <!-- نوافذ كشف حساب المستأجرين -->
                @foreach($tenants as $tn)
                    @php
                        $tnVouchers = $vouchers->where('tenant_id', $tn->id);
                        $tnContracts = $contracts->where('tenant_id', $tn->id);
                        $totalRequiredRent = $tnContracts->sum('total_rent_amount');
                        $totalPaidRent = $tnVouchers->where('voucher_type', 'receipt')->sum('amount');
                        $remainingRent = $totalRequiredRent - $totalPaidRent;
                    @endphp
                    <div id="tenantStatementModal_{{ $tn->id }}" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-3xl w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto">
                            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                                <div>
                                    <h3 class="font-bold text-base text-slate-900">كشف حساب المستأجر: <span class="text-emerald-700">{{ $tn->name }}</span></h3>
                                    <p class="text-xs text-slate-500 font-mono" dir="ltr">{{ $tn->phone }} | ID: {{ $tn->id_number }}</p>
                                </div>
                                <button type="button" onclick="closeModal('tenantStatementModal_{{ $tn->id }}')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                            </div>

                            <div class="grid grid-cols-3 gap-4 mb-5">
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                                    <div class="text-xs text-slate-500">إجمالي قيمة العقود</div>
                                    <div class="text-lg font-bold text-slate-900 mt-1">{{ number_format($totalRequiredRent, 2) }} ر.س</div>
                                </div>
                                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200">
                                    <div class="text-xs text-emerald-700">إجمالي الدفعات المسددة (قبض)</div>
                                    <div class="text-lg font-bold text-emerald-700 mt-1">{{ number_format($totalPaidRent, 2) }} ر.س</div>
                                </div>
                                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200">
                                    <div class="text-xs text-amber-800">الرصيد المتبقي المستحق</div>
                                    <div class="text-lg font-bold text-amber-800 mt-1">{{ number_format($remainingRent, 2) }} ر.س</div>
                                </div>
                            </div>

                            <table class="w-full text-xs text-right border rounded-2xl overflow-hidden">
                                <thead class="bg-slate-100 text-slate-700">
                                    <tr>
                                        <th class="py-3 px-4">رقم السند</th>
                                        <th class="py-3 px-4">النوع</th>
                                        <th class="py-3 px-4">المبلغ</th>
                                        <th class="py-3 px-4">طريقة الدفع</th>
                                        <th class="py-3 px-4">البيان والتاريخ</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @forelse($tnVouchers as $tv)
                                        <tr>
                                            <td class="py-3 px-4 font-mono font-bold">{{ $tv->voucher_number }}</td>
                                            <td class="py-3 px-4">
                                                @if($tv->voucher_type == 'receipt')
                                                    <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold">دفعة إيجار (قبض)</span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded bg-red-100 text-red-800 font-bold">سند صرف</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 font-bold">{{ number_format($tv->amount, 2) }} ر.س</td>
                                            <td class="py-3 px-4 font-semibold">{{ \App\Http\Controllers\CompanyErpController::translatePaymentMethod($tv->payment_method) }}</td>
                                            <td class="py-3 px-4">{{ $tv->notes }} ({{ $tv->voucher_date }})</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="py-6 text-center text-slate-400">لا توجد حركات مالية مسجلة لهذا المستأجر بعد.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach

                <!-- نافذة إضافة مستأجر جديد مع ربطه بشجرة الحسابات تلقائياً -->
                <div id="addTenantModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                        <div class="flex items-center justify-between pb-3 border-b mb-4">
                            <div>
                                <h3 class="font-bold text-base text-slate-900">تسجيل مستأجر جديد</h3>
                                <p class="text-xs text-slate-500">يتم فتح حساب فرعي متسلسل للمستأجر في شجرة الحسابات تلقائياً</p>
                            </div>
                            <button type="button" onclick="closeModal('addTenantModal')" class="font-bold text-slate-400">✕</button>
                        </div>

                        <form action="{{ route('office.tenants.store') }}" method="POST" class="space-y-3.5">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">اسم المستأجر الكامل *</label>
                                <input type="text" name="name" required placeholder="مثال: فهد بن خالد العتيبي" class="w-full rounded-xl border px-3.5 py-2 text-sm">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">نوع الهوية *</label>
                                    <select name="id_type" class="w-full rounded-xl border px-3 py-2 text-sm bg-white">
                                        <option value="national_id">هوية وطنية</option>
                                        <option value="iqama">إقامة مقيم</option>
                                        <option value="cr">سجل تجاري</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">رقم الهوية / الإقامة *</label>
                                    <input type="text" name="id_number" required maxlength="10" placeholder="10XXXXXXXX" class="w-full rounded-xl border px-3 py-2 text-sm font-mono">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">رقم الجوال المرتبط بأبشر (10 أرقام) *</label>
                                <input type="tel" name="phone" maxlength="10" required placeholder="05XXXXXXXX" dir="ltr"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                                       class="w-full text-left font-mono rounded-xl border px-3.5 py-2 text-sm">
                            </div>
                            <div class="grid grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">الرقم الضريبي للمستأجر (اختياري)</label>
                                    <input type="text" name="tax_number" maxlength="15" placeholder="300XXXXXXXXXXXX"
                                           class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-mono">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">العنوان الوطني (اختياري)</label>
                                    <input type="text" name="national_address" placeholder="الرياض..."
                                           class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs">
                                </div>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-200">
                                <label class="block text-xs font-bold text-emerald-900 mb-1">🌳 الحساب الأب في شجرة المكتب (للتفرع المتسلسل التلقائي)</label>
                                <select name="parent_account_id" class="w-full rounded-xl border border-emerald-300 px-3 py-2 text-xs bg-white font-semibold">
                                    <option value="">-- ربط تلقائي تحت ذمم المستأجرين (1103) --</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}" {{ ($defaultTenantParentAcc->id ?? '') == $acc->id ? 'selected' : '' }}>
                                            {{ $acc->code }} - {{ $acc->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-[10px] text-emerald-700 block mt-1">سيتم توليد رقم حساب متسلسل للمستأجر وربطه آلياً.</span>
                            </div>
                            <div class="flex justify-end gap-2 pt-3 border-t">
                                <button type="button" onclick="closeModal('addTenantModal')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">حفظ المستأجر وربط الحساب</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- نافذة إبرام عقد إيجار جديد -->
                <div id="addContractModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto">
                        <div class="flex items-center justify-between pb-3 border-b mb-4">
                            <div>
                                <h3 class="font-bold text-base text-slate-900">إنشاء عقد إيجار (منصة إيجار)</h3>
                                <p class="text-xs text-slate-500">يتم احتساب نسبة المكتب وحصة الشركة الأم آلياً</p>
                            </div>
                            <button type="button" onclick="closeModal('addContractModal')" class="font-bold text-slate-400">✕</button>
                        </div>

                        <form action="{{ route('office.contracts.store') }}" method="POST" class="space-y-3.5">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">اختر العقار *</label>
                                <select name="property_id" required class="w-full rounded-xl border px-3 py-2 text-sm bg-white">
                                    <option value="">-- اختر العقار --</option>
                                    @foreach($properties as $pr)
                                        <option value="{{ $pr->id }}">{{ $pr->title }} (المالك: {{ $pr->owner_name }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">اختر الوحدة العقارية (في حال كان عمارة)</label>
                                <select name="property_unit_id" class="w-full rounded-xl border px-3 py-2 text-sm bg-white">
                                    <option value="">-- كامل العقار / بدون وحدة فرعية --</option>
                                    @foreach($units->where('status', 'vacant') as $un)
                                        <option value="{{ $un->id }}">{{ $un->property_title }} - وحدة ({{ $un->unit_number }}) - {{ number_format($un->default_rent_price, 0) }} ر.س</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">اختر المستأجر *</label>
                                <select name="tenant_id" required class="w-full rounded-xl border px-3 py-2 text-sm bg-white">
                                    <option value="">-- اختر المستأجر --</option>
                                    @foreach($tenants as $tn)
                                        <option value="{{ $tn->id }}">{{ $tn->name }} ({{ $tn->id_number }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">بداية العقد *</label>
                                    <input type="date" name="start_date" value="{{ date('Y-m-d') }}" required class="w-full rounded-xl border px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">نهاية العقد *</label>
                                    <input type="date" name="end_date" value="{{ date('Y-m-d', strtotime('+1 year')) }}" required class="w-full rounded-xl border px-3 py-2 text-sm">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">دورة السداد *</label>
                                    <select name="payment_cycle" class="w-full rounded-xl border px-3 py-2 text-sm bg-white">
                                        <option value="monthly">شهري</option>
                                        <option value="quarterly">ربع سنوي (كل 3 أشهر)</option>
                                        <option value="semi_annual">نصف سنوي (دفعتين)</option>
                                        <option value="annual">سنوي (دفعة واحدة)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">إجمالي الإيجار السنوي (ر.س) *</label>
                                    <input type="number" step="0.01" name="total_rent_amount" required placeholder="30000" class="w-full rounded-xl border px-3 py-2 text-sm font-bold text-emerald-700">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">مبلغ التأمين (ر.س)</label>
                                    <input type="number" step="0.01" name="security_deposit" value="0" class="w-full rounded-xl border px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">رقم عقد إيجار الموحد (اختياري)</label>
                                    <input type="text" name="ejar_contract_number" placeholder="أو يوثق آلياً بعد الحفظ" class="w-full rounded-xl border px-3 py-2 text-xs font-mono">
                                </div>
                            </div>
                            <div class="flex justify-end gap-2 pt-3 border-t">
                                <button type="button" onclick="closeModal('addContractModal')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold shadow-lg shadow-emerald-600/20">
                                    اعتماد العقد وحساب النسب المالية
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- 5. شجرة الحسابات المحاسبية الخاصة بالمكتب -->
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
                        <h3 class="font-bold text-base text-slate-900">شجرة الحسابات المحاسبية المستقلة — ({{ $office->name }})</h3>
                        <p class="text-xs text-slate-500 mt-0.5">شجرة حسابات خاصة بمكتبك فقط، مع إمكانية إضافة الفروع وإصدار السندات الفورية</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <button type="button" onclick="openModal('manageAccountTypesModal')"
                                class="px-4 py-2 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition">
                            ⚙️ أنواع الحسابات ({{ $accountTypes->count() }})
                        </button>

                        <button type="button" onclick="openAddBranchesModal('', 'إضافة حساب / مجلد رئيسي جديد للمكتب', '')"
                                class="px-4 py-2 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-lg shadow-emerald-600/20 transition">
                            + إضافة حساب رئيسي أو فروع
                        </button>

                        @if($accounts->count() == 0)
                            <form action="{{ route('office.accounts.seed') }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 rounded-2xl bg-amber-500 hover:bg-amber-400 text-white font-bold text-xs shadow-lg transition">
                                    ⚡ توليد الشجرة القياسية للمكتب
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                @if($accounts->count() == 0)
                    <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center space-y-4">
                        <div class="text-4xl">🌳</div>
                        <h4 class="font-bold text-slate-800 text-base">شجرة الحسابات الخاصة بالمكتب فارغة</h4>
                        <p class="text-xs text-slate-500 max-w-md mx-auto">اضغط على زر توليد الشجرة القياسية للمكتب لتأسيس حسابات الصندوق والبنك وأمانات الملاك وذمم المستأجرين فوراً.</p>
                        <form action="{{ route('office.accounts.seed') }}" method="POST">
                            @csrf
                            <button type="submit" class="px-6 py-3 rounded-2xl bg-emerald-600 text-white font-bold text-xs">⚡ تأسيس الشجرة القياسية للمكتب</button>
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
                                $rootBalance = $root->is_group ? $level1Children->sum('balance') + $root->balance : $root->balance;
                            @endphp

                            <div class="border-2 border-slate-200 rounded-3xl p-5 bg-slate-50/50 space-y-3">
                                <div class="flex items-center justify-between flex-wrap gap-2 pb-3 border-b border-slate-200">
                                    <div class="flex items-center gap-3">
                                        <span class="px-3 py-1.5 rounded-xl bg-slate-900 text-white font-mono text-xs font-bold">{{ $root->code }}</span>
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-base">📁 {{ $root->name }}</h4>
                                            <span class="text-[11px] text-emerald-700 font-semibold">{{ $rootTypeAr }} | الإجمالي: {{ number_format($rootBalance, 2) }} ر.س</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <button type="button" onclick="openAddBranchesModal('{{ $root->id }}', '{{ addslashes($root->name) }}', '{{ addslashes($rootTypeAr) }}')"
                                                class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition">
                                            + فرع جديد
                                        </button>
                                        <button type="button" onclick="openModal('editAccModal_{{ $root->id }}')" class="px-2.5 py-1.5 rounded-xl bg-white border text-xs">✏️</button>
                                        @if($level1Children->count() == 0)
                                            <form action="{{ route('office.accounts.delete', $root->id) }}" method="POST" onsubmit="return confirm('حذف هذا المجلد؟');">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-red-50 text-red-600 text-xs">🗑️</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                <div class="pr-6 space-y-2.5 border-r-2 border-emerald-500/40 mr-2">
                                    @forelse($level1Children as $child)
                                        @php
                                            $level2Children = $accounts->where('parent_id', $child->id);
                                        @endphp
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
                                                                class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 text-[11px] font-bold transition">
                                                            📥 قبض
                                                        </button>
                                                        <button type="button" onclick="openQuickVoucherModal('payment', '{{ $child->id }}', '{{ addslashes($child->code . ' - ' . $child->name) }}')"
                                                                class="px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-600 text-amber-700 hover:text-white border border-amber-200 text-[11px] font-bold transition">
                                                            📤 صرف
                                                        </button>
                                                    @endif
                                                    <button type="button" onclick="openAddBranchesModal('{{ $child->id }}', '{{ addslashes($child->name) }}', '{{ addslashes($rootTypeAr) }}')"
                                                            class="px-2 py-1 rounded-lg bg-blue-50 text-blue-700 text-[11px] font-bold">+ فرع</button>
                                                    <button type="button" onclick="openModal('editAccModal_{{ $child->id }}')" class="px-2 py-1 rounded-lg bg-slate-100 text-xs">✏️</button>
                                                    @if($level2Children->count() == 0)
                                                        <form action="{{ route('office.accounts.delete', $child->id) }}" method="POST" onsubmit="return confirm('حذف؟');">
                                                            @csrf
                                                            <button type="submit" class="px-2 py-1 rounded-lg bg-red-50 text-red-600 text-xs">🗑️</button>
                                                        </form>
                                                    @endif
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
                                                                <button type="button" onclick="openModal('editAccModal_{{ $subChild->id }}')" class="px-1.5 py-0.5 rounded bg-white text-[10px]">✏️</button>
                                                                <form action="{{ route('office.accounts.delete', $subChild->id) }}" method="POST" onsubmit="return confirm('حذف؟');">
                                                                    @csrf
                                                                    <button type="submit" class="px-1.5 py-0.5 rounded bg-red-50 text-red-600 text-[10px]">🗑️</button>
                                                                </form>
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

                <!-- نافذة إضافة حسابات وفروع متعددة للمكتب -->
                <div id="addBranchesModal" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-slate-200">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                            <div>
                                <h3 id="addBranchesTitle" class="font-bold text-base text-slate-900">إضافة حسابات وفروع لشجرة المكتب</h3>
                                <p class="text-xs text-slate-500 mt-0.5">أضف فرعاً واحداً أو عدة فروع دفعة واحدة (الكود يولد تلقائياً)</p>
                            </div>
                            <button type="button" onclick="closeModal('addBranchesModal')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 font-bold">✕</button>
                        </div>

                        <form action="{{ route('office.accounts.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">الحساب الأب (اختياري)</label>
                                    <select name="parent_id" id="branch_parent_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white">
                                        <option value="">-- جذع رئيسي جديد --</option>
                                        @foreach($accounts as $accItem)
                                            <option value="{{ $accItem->id }}">{{ $accItem->code }} - {{ $accItem->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">نوع الحساب *</label>
                                    <select name="type" id="branch_acc_type" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white">
                                        @foreach($accountTypes as $at)
                                            <option value="{{ $at->name }}">{{ $at->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div id="multiBranchesContainer" class="space-y-2.5 max-h-64 overflow-y-auto pr-1">
                                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 grid grid-cols-12 gap-2 items-center branch-row">
                                    <div class="col-span-6">
                                        <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">اسم الفرع / الحساب *</label>
                                        <input type="text" name="names[]" required placeholder="مثال: حساب أمانات المالك فيصل..."
                                               class="w-full rounded-xl border border-slate-300 px-3 py-1.5 text-xs bg-white">
                                    </div>
                                    <div class="col-span-3">
                                        <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">الكود (تلقائي)</label>
                                        <input type="text" name="codes[]" placeholder="تلقائي"
                                               class="w-full rounded-xl border border-slate-300 px-2.5 py-1.5 text-xs font-mono bg-white">
                                    </div>
                                    <div class="col-span-3">
                                        <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">النوع</label>
                                        <select name="is_groups[]" class="w-full rounded-xl border border-slate-300 px-2 py-1.5 text-xs bg-white">
                                            <option value="0">حساب فرعي</option>
                                            <option value="1">مجلد رئيسي</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <button type="button" onclick="addAnotherBranchRow()"
                                    class="w-full py-2 rounded-xl border-2 border-dashed border-emerald-500 text-emerald-700 hover:bg-emerald-50 text-xs font-bold transition">
                                + إضافة فرع آخر في القائمة
                            </button>

                            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                                <button type="button" onclick="closeModal('addBranchesModal')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold">حفظ وإضافة للشجرة</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- نافذة إصدار السند الفوري من داخل شجرة المكتب -->
                <div id="quickVoucherModal" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                            <div>
                                <h3 id="qv_title" class="font-bold text-base text-slate-900">إصدار سند مالي للمكتب</h3>
                                <p id="qv_subtitle" class="text-xs text-emerald-600 font-bold mt-0.5"></p>
                            </div>
                            <button type="button" onclick="closeModal('quickVoucherModal')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 font-bold">✕</button>
                        </div>

                        <form action="{{ route('office.vouchers.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="stay_in_accounting" value="1">
                            <input type="hidden" name="voucher_type" id="qv_type" value="receipt">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">من حساب (المدين) *</label>
                                    <select name="debit_account_id" id="qv_debit" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-semibold">
                                        @foreach($leafAccounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">إلى حساب (الدائن) *</label>
                                    <select name="credit_account_id" id="qv_credit" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-semibold">
                                        @foreach($leafAccounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">المالك المرتبط (اختياري)</label>
                                    <select name="owner_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white">
                                        <option value="">-- عام --</option>
                                        @foreach($owners as $ow)
                                            <option value="{{ $ow->id }}">{{ $ow->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">المستأجر المرتبط (اختياري)</label>
                                    <select name="tenant_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white">
                                        <option value="">-- عام --</option>
                                        @foreach($tenants as $tn)
                                            <option value="{{ $tn->id }}">{{ $tn->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">المبلغ (ر.س) *</label>
                                    <input type="number" step="0.01" name="amount" required placeholder="0.00"
                                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm font-bold text-emerald-700">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">طريقة الدفع *</label>
                                    <select name="payment_method" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm bg-white">
                                        <option value="bank_transfer">حوالة بنكية</option>
                                        <option value="sadad">سداد</option>
                                        <option value="mada">مدى</option>
                                        <option value="cash">نقدي</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">البيان (وذلك عن...) *</label>
                                <input type="text" name="notes" required placeholder="أدخل بيان السند..."
                                       class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm">
                            </div>

                            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                                <button type="button" onclick="closeModal('quickVoucherModal')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold">اعتماد السند وتحديث الرصيد</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- نوافذ تعديل الحسابات مع إمكانية تعديل المدين والدائن مباشرة -->
                @foreach($accounts as $acc)
                    @php $accTypeAr = $typeTranslations[$acc->type] ?? $acc->type; @endphp
                    <div id="editAccModal_{{ $acc->id }}" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                                <h3 class="font-bold text-base text-slate-900">تعديل: {{ $acc->name }}</h3>
                                <button type="button" onclick="closeModal('editAccModal_{{ $acc->id }}')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                            </div>
                            <form action="{{ route('office.accounts.update', $acc->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <div class="grid grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">الكود *</label>
                                        <input type="text" name="code" value="{{ $acc->code }}" required class="w-full rounded-xl border px-3 py-2 text-sm font-mono">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">الاسم *</label>
                                        <input type="text" name="name" value="{{ $acc->name }}" required class="w-full rounded-xl border px-3 py-2 text-sm font-bold">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">النوع *</label>
                                    <select name="type" class="w-full rounded-xl border px-3 py-2 text-sm bg-white">
                                        @foreach($accountTypes as $at)
                                            <option value="{{ $at->name }}" {{ $accTypeAr == $at->name ? 'selected' : '' }}>{{ $at->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">الحساب الأب</label>
                                    <select name="parent_id" class="w-full rounded-xl border px-3 py-2 text-sm bg-white">
                                        <option value="">-- جذع رئيسي --</option>
                                        @foreach($accounts->where('id', '!=', $acc->id) as $pAcc)
                                            <option value="{{ $pAcc->id }}" {{ $acc->parent_id == $pAcc->id ? 'selected' : '' }}>{{ $pAcc->code }} - {{ $pAcc->name }}</option>
                                        @endforeach
                                    </select>
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
                                    <span>مجلد رئيسي</span>
                                </label>
                                <div class="flex justify-end gap-2 pt-3 border-t">
                                    <button type="button" onclick="closeModal('editAccModal_{{ $acc->id }}')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">حفظ</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach

                <div id="manageAccountTypesModal" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                            <h3 class="font-bold text-base text-slate-900">إدارة أنواع الحسابات</h3>
                            <button type="button" onclick="closeModal('manageAccountTypesModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                        </div>
                        <form action="{{ route('office.account_types.store') }}" method="POST" class="flex gap-2 mb-5">
                            @csrf
                            <input type="text" name="name" required placeholder="مسمى النوع (مثال: أصول متداولة...)" class="flex-1 rounded-xl border px-3 py-2 text-xs">
                            <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">+ إضافة</button>
                        </form>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @foreach($accountTypes as $at)
                                <div class="flex items-center justify-between gap-2 p-2.5 rounded-xl bg-slate-50 border">
                                    <form action="{{ route('office.account_types.update', $at->id) }}" method="POST" class="flex-1 flex gap-2">
                                        @csrf
                                        <input type="text" name="name" value="{{ $at->name }}" required class="flex-1 rounded-lg border px-3 py-1.5 text-xs font-bold bg-white">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-900 text-white text-xs">تعديل</button>
                                    </form>
                                    <form action="{{ route('office.account_types.delete', $at->id) }}" method="POST" onsubmit="return confirm('حذف؟');">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 text-red-600 text-xs">حذف</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- 6. تبويب سندات القبض والصرف المستقلة للمكتب -->
            @if($tab == 'vouchers')
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                   <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="font-bold text-base text-slate-900">سجل سندات القبض و الصرف — ({{ $office->name }})</h3>
                        </div>

                        <div class="flex items-center gap-3 flex-1 max-w-md">
                            <input type="text" id="officeVoucherSearch" oninput="filterTableRows('officeVoucherSearch', 'officeVouchersTableBody')"
                                   placeholder="🔍 بحث فوري برقم السند أو اسم المستأجر/المالك أو البيان..."
                                   class="w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-xs focus:outline-none focus:border-emerald-600">
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" onclick="openModal('manageOfficePaymentMethodsModal')"
                                    class="px-4 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition">
                                💳 طرق الدفع ({{ $paymentMethods->count() }})
                            </button>

                            <button type="button" onclick="openModal('createOfficeVoucherModal')"
                                    class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs shadow-lg shadow-emerald-600/20 transition flex items-center gap-2">
                                <span>+ إصدار سند قبض / صرف</span>
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-right">
                            <thead class="bg-slate-50 text-slate-600 text-xs border-b border-slate-200/60">
                                <tr>
                                    <th class="py-3.5 px-4">رقم السند</th>
                                    <th class="py-3.5 px-3">النوع</th>
                                    <th class="py-3.5 px-4">المستأجر / المالك</th>
                                    <th class="py-3.5 px-4">القيد المحاسبي (بالمنصة)</th>
                                    <th class="py-3.5 px-4">المبلغ</th>
                                    <th class="py-3.5 px-3 text-center">طريقة الدفع</th>
                                    <th class="py-3.5 px-4">البيان والتاريخ</th>
                                    <th class="py-3.5 px-4 text-center">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="officeVouchersTableBody" class="divide-y divide-slate-100">
                                @forelse($vouchers as $v)
                                    @php
                                        $partyName = $v->tenant_name ?: ($v->owner_name ?: 'عام للمكتب');
                                        $pmArabic = \App\Http\Controllers\CompanyErpController::translatePaymentMethod($v->payment_method);
                                    @endphp
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-4 px-4 font-mono font-bold text-slate-900 text-xs">{{ $v->voucher_number }}</td>
                                        <td class="py-4 px-3">
                                            @if($v->voucher_type == 'receipt')
                                                <span class="px-2.5 py-1 rounded-xl text-xs font-semibold bg-emerald-50 text-emerald-700">📥 قبض</span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-xl text-xs font-semibold bg-amber-50 text-amber-800">📤 صرف</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-xs font-bold text-slate-800">{{ $partyName }}</td>
                                        <td class="py-4 px-4 text-[11px] text-slate-500">
                                            <div>من: {{ $v->debit_code }} - {{ $v->debit_name }}</div>
                                            <div>إلى: {{ $v->credit_code }} - {{ $v->credit_name }}</div>
                                        </td>
                                        <td class="py-4 px-4 font-bold text-emerald-700">{{ number_format($v->amount, 2) }} ر.س</td>
                                        <td class="py-4 px-3 text-center">
                                            <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 text-xs font-bold">{{ $pmArabic }}</span>
                                        </td>
                                        <td class="py-4 px-4 text-xs text-slate-600">
                                            <div class="font-semibold text-slate-800">{{ $v->notes }}</div>
                                            <div class="text-[11px] text-slate-400 font-mono">{{ $v->voucher_date }}</div>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                                <button type="button"
                                                        onclick="previewOfficialVoucher({
                                                            number: '{{ $v->voucher_number }}',
                                                            type: '{{ $v->voucher_type }}',
                                                            amount: '{{ number_format($v->amount, 2) }}',
                                                            date: '{{ $v->voucher_date }}',
                                                            party: '{{ addslashes($partyName) }}',
                                                            method: '{{ addslashes($pmArabic) }}',
                                                            notes: '{{ addslashes($v->notes) }}',
                                                            issuer: '{{ addslashes($office->name) }}'
                                                        })"
                                                        class="px-2.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-white text-[11px] font-bold transition">
                                                    📄 PDF / طباعة
                                                </button>

                                                <form action="{{ route('office.vouchers.whatsapp', $v->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 text-[11px] font-bold transition">
                                                        💬 واتساب
                                                    </button>
                                                </form>

                                                <button type="button" onclick="openModal('editOfficeVoucherModal_{{ $v->id }}')"
                                                        class="px-2 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-600 text-blue-700 hover:text-white text-[11px] font-bold transition">
                                                    تعديل 
                                                </button>

                                                <form action="{{ route('office.vouchers.delete', $v->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا السند وعكس قيده المالي؟');">
                                                    @csrf
                                                    <button type="submit" class="px-2 py-1.5 rounded-xl bg-red-50 hover:bg-red-600 text-red-600 hover:text-white text-[11px] font-bold transition">
                                                        حذف
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="py-10 text-center text-slate-400">لا توجد سندات مصدرة في هذا المكتب بعد.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- نافذة إدارة طرق الدفع -->
                <div id="manageOfficePaymentMethodsModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200">
                        <div class="flex items-center justify-between pb-4 border-b mb-4">
                            <h3 class="font-bold text-base text-slate-900">💳 إدارة وإضافة طرق الدفع</h3>
                            <button type="button" onclick="closeModal('manageOfficePaymentMethodsModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
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
                                    <form action="{{ route('erp.payment_methods.delete', $pm->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 text-red-600 text-xs">حذف</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- نافذة إصدار سند قبض / صرف جديد للمكتب -->
                <div id="createOfficeVoucherModal" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                            <h3 class="font-bold text-base text-slate-900">إصدار سند قبض / صرف جديد للمكتب</h3>
                            <button type="button" onclick="closeModal('createOfficeVoucherModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                        </div>
                        <form action="{{ route('office.vouchers.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">نوع السند *</label>
                                <select name="voucher_type" class="w-full rounded-2xl border border-slate-300 px-3.5 py-2.5 text-sm bg-white font-bold">
                                    <option value="receipt">📥 سند قبض (تحصيل إيجار / دفعة)</option>
                                    <option value="payment">📤 سند صرف (تسلسل SA-{{ date('Y') }}-XXXXXX)</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">المستأجر المرتبط</label>
                                    <select name="tenant_id" class="w-full rounded-xl border border-slate-300 px-2.5 py-2 text-xs bg-white">
                                        <option value="">-- اختياري --</option>
                                        @foreach($tenants as $tn)
                                            <option value="{{ $tn->id }}">{{ $tn->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">المالك المرتبط</label>
                                    <select name="owner_id" class="w-full rounded-xl border border-slate-300 px-2.5 py-2 text-xs bg-white">
                                        <option value="">-- اختياري --</option>
                                        @foreach($owners as $ow)
                                            <option value="{{ $ow->id }}">{{ $ow->name }}</option>
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
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">المبلغ (ر.س) *</label>
                                    <input type="number" step="0.01" name="amount" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm font-bold text-emerald-700">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">طريقة الدفع *</label>
                                    <select name="payment_method" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white font-semibold">
                                        @foreach($paymentMethods as $pm)
                                            <option value="{{ $pm->name }}">{{ $pm->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">تاريخ السند *</label>
                                <input type="date" name="voucher_date" value="{{ date('Y-m-d') }}" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">البيان *</label>
                                <input type="text" name="notes" required placeholder="وذلك عن..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm">
                            </div>
                            <div class="flex justify-end gap-2 pt-3 border-t">
                                <button type="button" onclick="closeModal('createOfficeVoucherModal')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs">
                                    إصدار السند وترحيل القيد
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- نوافذ تعديل السندات -->
                @foreach($vouchers as $v)
                    @php $vMethodAr = \App\Http\Controllers\CompanyErpController::translatePaymentMethod($v->payment_method); @endphp
                    <div id="editOfficeVoucherModal_{{ $v->id }}" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                            <div class="flex items-center justify-between pb-3 border-b mb-4">
                                <h3 class="font-bold text-base text-slate-900">تعديل السند: {{ $v->voucher_number }}</h3>
                                <button type="button" onclick="closeModal('editOfficeVoucherModal_{{ $v->id }}')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                            </div>
                            <form action="{{ route('office.vouchers.update', $v->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">المبلغ (ر.س) *</label>
                                    <input type="number" step="0.01" name="amount" value="{{ $v->amount }}" required class="w-full rounded-xl border px-3.5 py-2 text-sm font-bold text-emerald-700">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">طريقة الدفع *</label>
                                    <select name="payment_method" class="w-full rounded-xl border px-3.5 py-2 text-sm bg-white font-semibold">
                                        @foreach($paymentMethods as $pm)
                                            <option value="{{ $pm->name }}" {{ ($vMethodAr == $pm->name || $v->payment_method == $pm->code) ? 'selected' : '' }}>{{ $pm->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">تاريخ السند *</label>
                                    <input type="date" name="voucher_date" value="{{ $v->voucher_date }}" required class="w-full rounded-xl border px-3.5 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">البيان *</label>
                                    <input type="text" name="notes" value="{{ $v->notes }}" required class="w-full rounded-xl border px-3.5 py-2 text-sm">
                                </div>
                                <div class="flex justify-end gap-2 pt-3 border-t">
                                    <button type="button" onclick="closeModal('editOfficeVoucherModal_{{ $v->id }}')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                    <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">حفظ التعديلات</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            @endif

            
            <!-- 6-ب. صفحة القيود اليومية الخاصة بالمكتب (Office Journal Entries) -->
            @if($tab == 'journal')
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="font-bold text-lg text-slate-900"> سجل القيود اليومية المحاسبية — ({{ $office->name }})</h3>
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
                                                    👁️ عرض ({{ $items->count() }})
                                                </button>

                                                <button type="button" onclick="openModal('printChoiceJournalModal_{{ $je->id }}')"
                                                        class="px-2.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-white text-xs font-bold transition">
                                                    🖨️ طباعة (PDF / Excel)
                                                </button>

                                                <button type="button" onclick="openModal('editJournalModal_{{ $je->id }}')"
                                                        class="px-2.5 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-600 text-blue-700 hover:text-white text-xs font-bold transition">
                                                     تعديل
                                                </button>

                                                <form action="{{ route('office.journal.delete', $je->id) }}" method="POST" onsubmit="return confirm('حذف هذا القيد وعكس أثره من شجرة حسابات المكتب؟');">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-red-50 hover:bg-red-600 text-red-600 hover:text-white text-xs font-bold transition">
                                                        🗑️ حذف
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="py-12 text-center text-slate-400">لا توجد قيود يومية مسجلة في المكتب بعد. اضغط على "إضافة قيد يومي جديد" للبدء.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- نافذة إضافة قيد يومي متعدد الجداول للمكتب (مع زر حذف للخانة الإضافية) -->
                <div id="addJournalEntryModal" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-4xl w-full p-6 shadow-2xl border border-slate-200 max-h-[92vh] overflow-y-auto">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                            <div>
                                <h3 class="font-bold text-base text-slate-900">➕ إضافة قيد يومي محاسبي متعدد العمليات — ({{ $office->name }})</h3>
                                <p class="text-xs text-slate-500">أدخل البيان العام بالأعلى ثم أضف العمليات في الجداول بالأسفل، ويمكنك حذف أي خانة إضافية لا تحتاجها</p>
                            </div>
                            <button type="button" onclick="closeModal('addJournalEntryModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                        </div>

                        <form action="{{ route('office.journal.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200">
                                <div class="md:col-span-9">
                                    <label class="block text-xs font-bold text-emerald-950 mb-1">البيان العام للقيد اليومي *</label>
                                    <input type="text" name="general_notes" required placeholder="مثال: قيد إثبات مصروفات صيانة أو تسوية إيجارات..."
                                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm bg-white font-semibold">
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-xs font-bold text-emerald-950 mb-1">تاريخ القيد *</label>
                                    <input type="date" name="entry_date" value="{{ date('Y-m-d') }}" required
                                           class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white font-mono">
                                </div>
                            </div>

                            <div id="journalTablesContainer" class="space-y-3">
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
                                                <option value="">-- اختر الحساب من شجرة المكتب --</option>
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
                                                <option value="">-- اختر الحساب من شجرة المكتب --</option>
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

                @foreach($journalEntries as $je)
                    @php $items = $journalItemsByEntry[$je->id] ?? collect(); @endphp

                    <div id="viewJournalModal_{{ $je->id }}" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-3xl w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto">
                            <div class="flex items-center justify-between pb-4 border-b mb-4">
                                <div>
                                    <h3 class="font-bold text-base text-slate-900"> تفاصيل القيد اليومي رقم (#{{ $je->entry_number }})</h3>
                                    <p class="text-xs text-slate-500">التاريخ: {{ $je->entry_date }} | البيان العام: {{ $je->general_notes }}</p>
                                </div>
                                <button type="button" onclick="closeModal('viewJournalModal_{{ $je->id }}')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                            </div>

                            <div id="printableJournalArea_{{ $je->id }}" class="p-4 rounded-2xl border border-slate-200 bg-white space-y-4">
                                <div class="flex items-center justify-between border-b pb-3">
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $office->name }} — قيد يومية رقم: #{{ $je->entry_number }}</div>
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

                    <div id="printChoiceJournalModal_{{ $je->id }}" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-200 text-center space-y-4">
                            <h3 class="font-bold text-base text-slate-900">تصدير وطباعة القيد اليومي (#{{ $je->entry_number }})</h3>
                            <p class="text-xs text-slate-500">اختر الصيغة المطلوبة لتصدير القيد اليومي:</p>
                            <div class="grid grid-cols-2 gap-3">
                                <button type="button" onclick="exportJournalPDF('{{ $je->id }}', '{{ $je->entry_number }}')"
                                        class="p-4 rounded-2xl bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border border-rose-200 font-bold text-xs transition flex flex-col items-center gap-2">
                                    <span class="text-2xl">📄</span>
                                    <span>تصدير ملف PDF</span>
                                </button>
                                <button type="button" onclick="exportTableToCSV('journalDetailTable_{{ $je->id }}', 'Office_Journal_Entry_{{ $je->entry_number }}.csv')"
                                        class="p-4 rounded-2xl bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 font-bold text-xs transition flex flex-col items-center gap-2">
                                    <span class="text-2xl">📊</span>
                                    <span>تصدير ملف Excel</span>
                                </button>
                            </div>
                            <button type="button" onclick="closeModal('printChoiceJournalModal_{{ $je->id }}')" class="w-full py-2 rounded-xl bg-slate-100 text-xs font-semibold">إغلاق</button>
                        </div>
                    </div>

                    <div id="editJournalModal_{{ $je->id }}" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-4xl w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto">
                            <div class="flex items-center justify-between pb-4 border-b mb-4">
                                <h3 class="font-bold text-base text-slate-900"> تعديل القيد اليومي رقم (#{{ $je->entry_number }})</h3>
                                <button type="button" onclick="closeModal('editJournalModal_{{ $je->id }}')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                            </div>
                            <form action="{{ route('office.journal.update', $je->id) }}" method="POST" class="space-y-4">
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

            <!-- 6-ج (1). صفحة الفوترة الضريبية للمكتب (جدول الفواتير + بحث فوري + زر إصدار فاتورة ضريبية + تحميل PDF + إرسال واتساب + ربط بالشجرة) -->
            @if($tab == 'invoicing')
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="font-bold text-lg text-slate-900">سجل الفواتير الضريبية والمبسطة — ({{ $office->name }})</h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                الرقم الضريبي للمكتب: <span class="font-mono font-bold text-emerald-700">{{ $office->tax_number ?: 'غير مسجل (يحفظ من الإعدادات)' }}</span> |
                                نسبة الضريبة المعتمدة: <span class="font-mono font-bold text-amber-700">{{ $defaultVatRate ?? 15 }}%</span>
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2.5">
                            <input type="text" id="officeInvoiceSearch" oninput="filterOfficeInvoicesTable()"
                                   placeholder="🔍 ابحث برقم الفاتورة، اسم المستأجر، المالك، أو البيان..."
                                   class="w-64 rounded-2xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-xs focus:bg-white focus:outline-none focus:border-emerald-600">

                            <button type="button" onclick="openModal('createOfficeInvoiceModal')"
                                    class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs shadow-lg flex items-center gap-2 transition">
                                <span>➕</span>
                                <span>إصدار فاتورة ضريبية</span>
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-right" id="officeInvoicesTable">
                            <thead class="bg-slate-50 text-slate-600 text-xs border-b">
                                <tr>
                                    <th class="py-4 px-4">رقم الفاتورة</th>
                                    <th class="py-4 px-4">التاريخ</th>
                                    <th class="py-4 px-4">المُصدِر (البائع)</th>
                                    <th class="py-4 px-4">العميل (المشتري)</th>
                                    <th class="py-4 px-4">نوع الفاتورة</th>
                                    <th class="py-4 px-4">البيان</th>
                                    <th class="py-4 px-4">قبل الضريبة</th>
                                    <th class="py-4 px-4">الضريبة</th>
                                    <th class="py-4 px-4">الإجمالي</th>
                                    <th class="py-4 px-4 text-center">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse(($officeInvoices ?? collect()) as $inv)
                                    <tr class="hover:bg-slate-50 transition office-invoice-row"
                                        data-search="{{ mb_strtolower($inv->invoice_number . ' ' . $inv->seller_name . ' ' . $inv->buyer_name . ' ' . $inv->description . ' ' . $inv->issue_date) }}">
                                        <td class="py-4 px-4 font-mono font-bold text-slate-900 text-xs">{{ $inv->invoice_number }}</td>
                                        <td class="py-4 px-4 font-mono text-xs text-slate-500">{{ $inv->issue_date }}</td>
                                        <td class="py-4 px-4 font-bold text-xs">
                                            {{ $inv->seller_name }}
                                            @if($inv->owner_id)
                                                <span class="block text-[10px] text-amber-700">👑 باسم المالك</span>
                                            @elseif($inv->issuer_level == 'company')
                                                <span class="block text-[10px] text-indigo-600">🏛️ من الإدارة</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 font-semibold text-xs">
                                            <div>{{ $inv->buyer_name }}</div>
                                            @if($inv->buyer_tax_number)
                                                <span class="text-[10px] font-mono text-slate-400">ضريبي: {{ $inv->buyer_tax_number }}</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4">
                                            @if($inv->invoice_type == 'tax')
                                                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[10px]">فاتورة ضريبية</span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 font-bold text-[10px]">ضريبية مبسطة</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-xs text-slate-700">{{ $inv->description }}</td>
                                        <td class="py-4 px-4 font-mono text-xs">{{ number_format($inv->subtotal, 2) }}</td>
                                        <td class="py-4 px-4 font-mono text-xs text-amber-700 font-bold">
                                            {{ number_format($inv->vat_amount, 2) }}
                                            <span class="text-[10px] text-slate-400 block">({{ (float)$inv->vat_rate }}%)</span>
                                        </td>
                                        <td class="py-4 px-4 font-mono font-extrabold text-emerald-700 text-xs">{{ number_format($inv->total_amount, 2) }} ر.س</td>
                                        <td class="py-4 px-4 text-center">
                                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                                <button type="button" onclick="openOfficeTaxInvoiceModal({
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

                                                <form action="{{ route('office.invoices.whatsapp', $inv->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 text-xs font-bold transition">
                                                        💬 واتساب
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="10" class="py-12 text-center text-slate-400">لا توجد فواتير مسجلة حتى الآن. اضغط على "إصدار فاتورة ضريبية" في أعلى اليسار.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- نافذة (Modal) إصدار فاتورة ضريبية / مبسطة للمكتب -->
                <div id="createOfficeInvoiceModal" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-slate-200 max-h-[92vh] overflow-y-auto">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                            <div>
                                <h3 class="font-bold text-base text-slate-900">🧾 إصدار فاتورة ضريبية / مبسطة</h3>
                                <p class="text-xs text-slate-500">تقرأ الأرقام الضريبية المحفوظة وترتبط بشجرة الحسابات (نسبة الضريبة ثابتة: {{ $defaultVatRate ?? 15 }}%)</p>
                            </div>
                            <button type="button" onclick="closeModal('createOfficeInvoiceModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                        </div>

                        <form action="{{ route('office.invoices.store') }}" method="POST" class="space-y-4">
                            @csrf

                            @if(!empty($office->allow_owner_tax_invoice))
                                <div class="p-3.5 rounded-2xl bg-amber-50/70 border border-amber-200 space-y-2.5">
                                    <label class="block text-xs font-bold text-amber-950">جهة إصدار الفاتورة الضريبية *</label>
                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                        <label class="flex items-center gap-2 p-2 rounded-xl bg-white border cursor-pointer font-bold">
                                            <input type="radio" name="issuer_mode" value="office" checked onchange="toggleInvoiceIssuerMode('office')">
                                            <span>🏢 باسم مكتب العقار</span>
                                        </label>
                                        <label class="flex items-center gap-2 p-2 rounded-xl bg-white border cursor-pointer font-bold text-amber-900">
                                            <input type="radio" name="issuer_mode" value="owner" onchange="toggleInvoiceIssuerMode('owner')">
                                            <span>👑 باسم المالك</span>
                                        </label>
                                    </div>

                                    <div id="inv_owner_select_box" class="hidden space-y-2 pt-2 border-t border-amber-200">
                                        <label class="block text-[11px] font-bold text-slate-700">اختر المالك *</label>
                                        <select name="owner_id" id="off_inv_owner" onchange="fillOwnerTaxInfo()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-bold">
                                            <option value="" data-tax="" data-addr="" data-acc="">-- اختر المالك --</option>
                                            @foreach($owners as $ow)
                                                <option value="{{ $ow->id }}" data-tax="{{ $ow->tax_number ?? '' }}" data-addr="{{ $ow->national_address ?? '' }}" data-acc="{{ $ow->account_id ?? '' }}">
                                                    {{ $ow->name }} {{ !empty($ow->tax_number) ? '(ضريبي: ' . $ow->tax_number . ')' : '(بدون رقم ضريبي)' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @else
                                <input type="hidden" name="issuer_mode" value="office">
                            @endif

                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="text-xs font-bold text-slate-700">اختر المستأجر (العميل) *</label>
                                    <span id="auto_inv_type_badge" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                        نوع الفاتورة يحدد تلقائياً حسب الرقم الضريبي للمستأجر
                                    </span>
                                </div>
                                <select name="tenant_id" id="off_inv_tenant" required onchange="checkTenantTaxStatus()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-bold">
                                    <option value="" data-tax="" data-addr="" data-acc="">-- اختر المستأجر --</option>
                                    @foreach($tenants as $tn)
                                        <option value="{{ $tn->id }}" data-tax="{{ $tn->tax_number ?? '' }}" data-addr="{{ $tn->national_address ?? '' }}" data-acc="{{ $tn->account_id ?? '' }}">
                                            {{ $tn->name }} {{ !empty($tn->tax_number) ? '(ضريبي: ' . $tn->tax_number . ')' : '(فرد / فاتورة مبسطة)' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- الربط بشجرة حسابات المكتب -->
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">🌳 حساب المدين (المستأجر) *</label>
                                    <select name="debit_account_id" id="off_inv_debit_acc" class="w-full rounded-xl border border-slate-300 px-2.5 py-2 text-xs bg-white font-semibold">
                                        @foreach($accounts->where('is_group', 0) as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">🌳 حساب الدائن (الإيراد / المالك) *</label>
                                    <select name="credit_account_id" id="off_inv_credit_acc" class="w-full rounded-xl border border-slate-300 px-2.5 py-2 text-xs bg-white font-semibold">
                                        @foreach($accounts->where('is_group', 0) as $acc)
                                            <option value="{{ $acc->id }}" {{ $acc->code == '4101' ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">البيان / تفاصيل الفاتورة *</label>
                                <input type="text" name="description" required placeholder="مثال: دفعة إيجار / عمولة سعي وإدارة أملاك..." class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs">
                            </div>

                            <div class="grid grid-cols-3 gap-2.5 p-3.5 rounded-2xl bg-emerald-50/60 border border-emerald-200">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">المبلغ قبل الضريبة *</label>
                                    <input type="number" step="0.01" name="subtotal" id="off_inv_sub" required oninput="calcOfficeInvTotal()" class="w-full rounded-xl border border-slate-300 px-2.5 py-1.5 text-xs bg-white font-bold">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-500 mb-1">الضريبة % (ثابتة)</label>
                                    <input type="number" id="off_inv_rate" value="{{ $defaultVatRate ?? 15 }}" readonly disabled class="w-full rounded-xl border border-slate-200 px-2.5 py-1.5 text-xs bg-slate-100 font-mono text-slate-600 cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-emerald-800 mb-1">الإجمالي بالضريبة</label>
                                    <input type="text" id="off_inv_total_preview" readonly value="0.00" class="w-full rounded-xl border border-emerald-300 px-2.5 py-1.5 text-xs bg-emerald-100 font-mono font-extrabold text-emerald-900">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">تاريخ الإصدار</label>
                                <input type="date" name="issue_date" value="{{ date('Y-m-d') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-mono">
                            </div>

                            <div class="flex justify-end gap-2 pt-3 border-t">
                                <button type="button" onclick="closeModal('createOfficeInvoiceModal')" class="px-4 py-2.5 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg">
                                    🧾 اعتماد الفاتورة وترحيلها للشجرة
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- نافذة معاينة وتحميل الفاتورة الضريبية PDF للمكتب -->
                <div id="officeTaxInvoiceModal" class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200">
                        <div class="flex items-center justify-between pb-4 border-b mb-4">
                            <h3 class="font-bold text-base text-slate-900">معاينة الفاتورة الضريبية الإلكترونية</h3>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="downloadOfficeTaxInvoicePDF()" class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">📥 تحميل PDF</button>
                                <button type="button" onclick="window.print()" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold">🖨️ طباعة</button>
                                <button type="button" onclick="closeModal('officeTaxInvoiceModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                            </div>
                        </div>
                        <div id="printableVoucherArea" class="p-6 rounded-3xl border-2 border-slate-900 bg-white space-y-5 text-slate-900">
                            <div class="flex items-start justify-between border-b-2 border-slate-900 pb-4">
                                <div>
                                    <div id="oti_type" class="text-lg font-extrabold text-emerald-800">فاتورة ضريبية</div>
                                    <div class="text-xs text-slate-500">Tax Invoice — متوافقة مع هيئة الزكاة والضريبة والجمارك</div>
                                    <div class="mt-2 text-xs font-mono font-bold">رقم الفاتورة: <span id="oti_number"></span></div>
                                    <div class="text-xs font-mono">التاريخ: <span id="oti_date"></span></div>
                                </div>
                                <div class="text-center">
                                    <img id="oti_qr_img" src="" alt="QR" class="w-24 h-24 border rounded-xl p-1 mx-auto">
                                    <span class="text-[9px] font-mono text-slate-400 block mt-1">ZATCA QR</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 text-xs">
                                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                                    <div class="font-bold text-slate-500">المُصدِر (المورّد):</div>
                                    <div id="oti_seller_name" class="font-extrabold text-slate-900 text-sm"></div>
                                    <div>الرقم الضريبي: <b id="oti_seller_tax" class="font-mono text-emerald-700"></b></div>
                                    <div>العنوان: <span id="oti_seller_addr"></span></div>
                                </div>
                                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                                    <div class="font-bold text-slate-500">العميل (المستأجر / المستفيد):</div>
                                    <div id="oti_buyer_name" class="font-extrabold text-slate-900 text-sm"></div>
                                    <div>الرقم الضريبي: <b id="oti_buyer_tax" class="font-mono text-emerald-700"></b></div>
                                    <div>العنوان: <span id="oti_buyer_addr"></span></div>
                                </div>
                            </div>
                            <table class="w-full text-xs text-right border border-slate-300 rounded-xl overflow-hidden">
                                <thead class="bg-slate-900 text-white">
                                    <tr>
                                        <th class="py-2.5 px-3">البيان</th>
                                        <th class="py-2.5 px-3">المبلغ قبل الضريبة</th>
                                        <th class="py-2.5 px-3">ضريبة القيمة المضافة (<span id="oti_vat_rate_label">15</span>%)</th>
                                        <th class="py-2.5 px-3">الإجمالي شامل الضريبة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b">
                                        <td id="oti_desc" class="py-3 px-3 font-bold"></td>
                                        <td id="oti_subtotal" class="py-3 px-3 font-mono"></td>
                                        <td id="oti_vat" class="py-3 px-3 font-mono text-amber-700 font-bold"></td>
                                        <td id="oti_total" class="py-3 px-3 font-mono font-extrabold text-emerald-700 text-sm"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <script>
                    let currentOfficeInvoiceNo = 'Office_Tax_Invoice';
                    function filterOfficeInvoicesTable() {
                        const q = (document.getElementById('officeInvoiceSearch')?.value || '').trim().toLowerCase();
                        document.querySelectorAll('.office-invoice-row').forEach(r => {
                            const hay = (r.getAttribute('data-search') || r.innerText || '').toLowerCase();
                            r.style.display = hay.includes(q) ? '' : 'none';
                        });
                    }
                    function toggleInvoiceIssuerMode(mode) {
                        const box = document.getElementById('inv_owner_select_box');
                        if (box) box.classList.toggle('hidden', mode !== 'owner');
                    }
                    function fillOwnerTaxInfo() {
                        const sel = document.getElementById('off_inv_owner');
                        if (!sel) return;
                        const opt = sel.options[sel.selectedIndex];
                        const ownerAcc = opt.getAttribute('data-acc') || '';
                        if (ownerAcc && document.getElementById('off_inv_credit_acc')) {
                            document.getElementById('off_inv_credit_acc').value = ownerAcc;
                        }
                    }
                    function checkTenantTaxStatus() {
                        const sel = document.getElementById('off_inv_tenant');
                        if (!sel) return;
                        const opt = sel.options[sel.selectedIndex];
                        const taxVal = (opt.getAttribute('data-tax') || '').trim();
                        const tenantAcc = opt.getAttribute('data-acc') || '';
                        if (tenantAcc && document.getElementById('off_inv_debit_acc')) {
                            document.getElementById('off_inv_debit_acc').value = tenantAcc;
                        }
                        const badge = document.getElementById('auto_inv_type_badge');
                        if (badge) {
                            if (taxVal.length > 0) {
                                badge.innerText = 'تلقائي: فاتورة ضريبية (الرقم الضريبي: ' + taxVal + ')';
                                badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800';
                            } else {
                                badge.innerText = 'تلقائي: فاتورة ضريبية مبسطة (بدون رقم ضريبي)';
                                badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800';
                            }
                        }
                    }
                    function calcOfficeInvTotal() {
                        const sub = parseFloat(document.getElementById('off_inv_sub')?.value || 0);
                        const rate = parseFloat(document.getElementById('off_inv_rate')?.value || 15);
                        const tot = sub + ((sub * rate) / 100);
                        const prev = document.getElementById('off_inv_total_preview');
                        if (prev) prev.value = tot.toFixed(2) + ' ر.س';
                    }
                    function openOfficeTaxInvoiceModal(d) {
                        currentOfficeInvoiceNo = d.number || 'Office_Tax_Invoice';
                        document.getElementById('oti_type').innerText = d.type;
                        document.getElementById('oti_number').innerText = d.number;
                        document.getElementById('oti_date').innerText = d.date;
                        document.getElementById('oti_seller_name').innerText = d.seller_name;
                        document.getElementById('oti_seller_tax').innerText = d.seller_tax || 'غير مسجل';
                        document.getElementById('oti_seller_addr').innerText = d.seller_addr || '-';
                        document.getElementById('oti_buyer_name').innerText = d.buyer_name;
                        document.getElementById('oti_buyer_tax').innerText = d.buyer_tax || 'لا يوجد (فاتورة ضريبية مبسطة)';
                        document.getElementById('oti_buyer_addr').innerText = d.buyer_addr || '-';
                        document.getElementById('oti_desc').innerText = d.desc;
                        document.getElementById('oti_subtotal').innerText = d.subtotal + ' ر.س';
                        if (document.getElementById('oti_vat_rate_label')) {
                            document.getElementById('oti_vat_rate_label').innerText = d.vat_rate || '15';
                        }
                        document.getElementById('oti_vat').innerText = d.vat + ' ر.س';
                        document.getElementById('oti_total').innerText = d.total + ' ر.س';
                        document.getElementById('oti_qr_img').src = 'https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=' + encodeURIComponent(d.qr);
                        openModal('officeTaxInvoiceModal');
                    }
                    function downloadOfficeTaxInvoicePDF() {
                        const el = document.getElementById('printableVoucherArea');
                        if (!el) return;
                        html2pdf().set({
                            margin: 10,
                            filename: currentOfficeInvoiceNo + '.pdf',
                            image: { type: 'jpeg', quality: 0.98 },
                            html2canvas: { scale: 2, useCORS: true },
                            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                        }).from(el).save();
                    }
                </script>
            @endif

            <!-- 6-ج (2). صفحة كشف الحساب التفصيلي المستقلة (مفصولة عن الأستاذ العام) -->
            @if($tab == 'account_statement')
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-5">
                    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4 no-print">
                        <div>
                            <h3 class="font-bold text-lg text-slate-900"> كشف الحساب  (Account Statement)</h3>
                            <p class="text-xs text-slate-500">صفحة مستقلة لاستخراج كشف حساب تفصيلي لأي حساب فرعي في الشجرة أو مالك أو مستأجر مع الرصيد التراكمي</p>
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
                            <label class="block text-xs font-bold text-slate-700 mb-1">أو اختر المالك / المستأجر</label>
                            <select id="sa_party_filter" onchange="filterStandaloneStatement()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-bold">
                                <option value="">-- جميع الأطراف --</option>
                                @foreach($owners as $ow)
                                    <option value="owner_{{ $ow->id }}">مالك: {{ $ow->name }}</option>
                                @endforeach
                                @foreach($tenants as $tn)
                                    <option value="tenant_{{ $tn->id }}">مستأجر: {{ $tn->name }}</option>
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
                                    <th class="py-3.5 px-4">الطرف المرتبط</th>
                                    <th class="py-3.5 px-4">البيان</th>
                                    <th class="py-3.5 px-4 text-emerald-300">مدين (+)</th>
                                    <th class="py-3.5 px-4 text-amber-300">دائن (-)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($vouchers as $v)
                                    <tr class="sa-stmt-row hover:bg-slate-50"
                                        data-date="{{ $v->voucher_date }}"
                                        data-owner="{{ $v->owner_id ?? '' }}"
                                        data-tenant="{{ $v->tenant_id ?? '' }}"
                                        data-debit-acc="{{ $v->debit_account_id }}"
                                        data-credit-acc="{{ $v->credit_account_id }}">
                                        <td class="py-3 px-4 font-mono">{{ $v->voucher_date }}</td>
                                        <td class="py-3 px-4 font-mono font-bold">{{ $v->voucher_number }}</td>
                                        <td class="py-3 px-4 font-bold">{{ $v->debit_code }} - {{ $v->debit_name }} / {{ $v->credit_code }} - {{ $v->credit_name }}</td>
                                        <td class="py-3 px-4 font-semibold">{{ $v->tenant_name ?: ($v->owner_name ?: 'عام للمكتب') }}</td>
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
                        const party = document.getElementById('sa_party_filter')?.value || '';
                        const from = document.getElementById('sa_from_date')?.value || '';
                        const to = document.getElementById('sa_to_date')?.value || '';
                        document.querySelectorAll('.sa-stmt-row').forEach(r => {
                            const d = r.getAttribute('data-date') || '';
                            const rOw = r.getAttribute('data-owner') || '';
                            const rTn = r.getAttribute('data-tenant') || '';
                            const dAcc = r.getAttribute('data-debit-acc') || '';
                            const cAcc = r.getAttribute('data-credit-acc') || '';
                            let show = true;
                            if (acc && dAcc !== acc && cAcc !== acc) show = false;
                            if (party) {
                                if (party.startsWith('owner_') && rOw !== party.replace('owner_', '')) show = false;
                                if (party.startsWith('tenant_') && rTn !== party.replace('tenant_', '')) show = false;
                            }
                            if (from && d < from) show = false;
                            if (to && d > to) show = false;
                            r.style.display = show ? '' : 'none';
                        });
                    }
                </script>
            @endif

            <!-- 6-ج. دفتر الأستاذ العام الاحترافي والقوائم المالية للمكتب (Office General Ledger Pro) -->
            @if($tab == 'statements')
                @php
                    $rawMovements = collect();

                    foreach ($vouchers as $v) {
                        $partyLabel = $v->tenant_name ? ('مستأجر: ' . $v->tenant_name) : ($v->owner_name ? ('مالك: ' . $v->owner_name) : 'حركة عامة للمكتب');
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
                            'owner_id' => $v->owner_id ?? null,
                            'tenant_id' => $v->tenant_id ?? null,
                            'party' => $partyLabel,
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
                            'owner_id' => $v->owner_id ?? null,
                            'tenant_id' => $v->tenant_id ?? null,
                            'party' => $partyLabel,
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
                            'owner_id' => null,
                            'tenant_id' => null,
                            'party' => 'قيد يومية للمكتب',
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
                                <p class="text-xs text-slate-500 mt-0.5">يدعم حساب الرصيد الافتتاحي للفترة، الحساب المقابل، الفلترة بالمالك أو المستأجر، وميزان المراجعة بالمجاميع والأرصدة</p>
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
                                <label class="block text-[11px] font-bold text-slate-700 mb-1"> فلترة حسب المالك / المستأجر</label>
                                <select id="stmt_party_select" onchange="applyStatementFilters()" class="w-full rounded-xl border border-slate-300 px-2.5 py-2 text-xs bg-white font-semibold">
                                    <option value="">-- الكل (جميع الملاك والمستأجرين) --</option>
                                    <optgroup label="ملاك العقارات">
                                        @foreach($owners as $ow)
                                            <option value="owner_{{ $ow->id }}">مالك: {{ $ow->name }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="المستأجرون">
                                        @foreach($tenants as $tn)
                                            <option value="tenant_{{ $tn->id }}">مستأجر: {{ $tn->name }}</option>
                                        @endforeach
                                    </optgroup>
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
                                            data-owner-id="{{ $mv->owner_id ?? '' }}"
                                            data-tenant-id="{{ $mv->tenant_id ?? '' }}"
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

            <!-- 7. تبويب موظفي المكتب وصلاحياتهم -->
            @if($tab == 'staff')
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
                        <h3 class="font-bold text-base text-slate-900 mb-1">إضافة موظف جديد بالمكتب</h3>
                        <p class="text-xs text-slate-500 mb-4">حدد صلاحيات الموظف ورقم جواله للدخول</p>

                        <form action="{{ route('office.staff.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">اسم الموظف *</label>
                                <input type="text" name="name" required placeholder="مثال: سعد الشمري" class="w-full rounded-xl border px-3.5 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">رقم الجوال (10 أرقام يبدأ بـ 05) *</label>
                                <input type="tel" name="phone" maxlength="10" required placeholder="05XXXXXXXX" dir="ltr"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                                       class="w-full text-left font-mono rounded-xl border px-3.5 py-2 text-sm">
                            </div>
                            <div class="grid grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">اسم المستخدم *</label>
                                    <input type="text" name="username" required placeholder="saad_staff" class="w-full rounded-xl border px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">كلمة المرور *</label>
                                    <input type="text" name="password" value="123456" required class="w-full rounded-xl border px-3 py-2 text-sm">
                                </div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                                <div class="text-xs font-bold text-slate-800 mb-1">الصلاحيات الممنوحة للموظف:</div>
                                <label class="flex items-center gap-2 text-xs"><input type="checkbox" name="permissions[]" value="إدارة الملاك والعقارات" checked> إدارة الملاك والعقارات والصكوك</label>
                                <label class="flex items-center gap-2 text-xs"><input type="checkbox" name="permissions[]" value="إبرام وتوثيق عقود إيجار" checked> إبرام وتوثيق عقود إيجار</label>
                                <label class="flex items-center gap-2 text-xs"><input type="checkbox" name="permissions[]" value="إصدار سندات القبض والصرف"> إصدار سندات القبض والصرف</label>
                                <label class="flex items-center gap-2 text-xs"><input type="checkbox" name="permissions[]" value="الاطلاع على شجرة الحسابات"> الاطلاع على شجرة الحسابات</label>
                            </div>

                            <button type="submit" class="w-full py-3 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm">
                                إضافة الموظف وتفعيل صلاحياته
                            </button>
                        </form>
                    </div>

                    <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-100">
                            <h3 class="font-bold text-slate-900">قائمة مدير وموظفي المكتب وصلاحياتهم</h3>
                        </div>
                        <table class="w-full text-sm text-right">
                            <thead class="bg-slate-50 text-slate-600 text-xs border-b">
                                <tr>
                                    <th class="py-3.5 px-5">الاسم</th>
                                    <th class="py-3.5 px-5">الجوال / المستخدم</th>
                                    <th class="py-3.5 px-5">الدور الوظيفي والصلاحيات</th>
                                    <th class="py-3.5 px-5 text-center">إجراء</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($staff as $st)
                                    @php
                                        $perms = json_decode($st->custom_permissions ?? '[]', true) ?: [];
                                    @endphp
                                    <tr>
                                        <td class="py-4 px-5 font-bold">{{ $st->name }}</td>
                                        <td class="py-4 px-5">
                                            <div class="font-mono text-xs font-bold text-emerald-700" dir="ltr">{{ $st->phone }}</div>
                                            <div class="text-xs text-slate-400">{{ $st->username }}</div>
                                        </td>
                                        <td class="py-4 px-5">
                                            @if($st->user_type == 'office_admin')
                                                <span class="px-3 py-1 rounded-xl bg-emerald-100 text-emerald-800 text-xs font-bold">مدير المكتب (كامل الصلاحيات)</span>
                                            @else
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($perms as $p)
                                                        <span class="px-2 py-0.5 rounded-lg bg-blue-50 text-blue-700 text-[11px] font-semibold">{{ $p }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-4 px-5 text-center">
                                            <div class="flex items-center justify-center gap-1.5">
                                                <button type="button" onclick="openModal('editStaffModal_{{ $st->id }}')"
                                                        class="px-3 py-1 rounded-xl bg-slate-900 text-white text-xs font-bold">تعديل</button>
                                                @if($st->user_type != 'office_admin')
                                                    <form action="{{ route('office.staff.delete', $st->id) }}" method="POST" onsubmit="return confirm('حذف حساب الموظف؟');">
                                                        @csrf
                                                        <button type="submit" class="px-3 py-1 rounded-xl bg-red-50 text-red-600 text-xs font-bold">حذف</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @foreach($staff as $st)
                    @php
                        $stPerms = json_decode($st->custom_permissions ?? '[]', true) ?: [];
                        $stLocalPhone = \App\Http\Controllers\CompanyErpController::toLocalPhone($st->phone);
                    @endphp
                    <div id="editStaffModal_{{ $st->id }}" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                            <div class="flex items-center justify-between pb-3 border-b mb-4">
                                <h3 class="font-bold text-base text-slate-900">تعديل بيانات الموظف: {{ $st->name }}</h3>
                                <button type="button" onclick="closeModal('editStaffModal_{{ $st->id }}')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                            </div>
                            <form action="{{ route('office.staff.update', $st->id) }}" method="POST" class="space-y-3.5">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">الاسم *</label>
                                    <input type="text" name="name" value="{{ $st->name }}" required class="w-full rounded-xl border px-3.5 py-2 text-sm font-bold">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">رقم الجوال *</label>
                                        <input type="tel" name="phone" value="{{ $stLocalPhone }}" maxlength="10" required dir="ltr" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);" class="w-full rounded-xl border px-3 py-2 text-xs font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">اسم المستخدم *</label>
                                        <input type="text" name="username" value="{{ $st->username }}" required class="w-full rounded-xl border px-3 py-2 text-xs">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">كلمة مرور جديدة (اتركها فارغة لعدم التغيير)</label>
                                    <input type="text" name="password" placeholder="أدخل كلمة مرور جديدة فقط عند الرغبة" class="w-full rounded-xl border px-3.5 py-2 text-xs">
                                </div>
                                @if($st->user_type != 'office_admin')
                                    <div class="p-3.5 rounded-2xl bg-slate-50 border space-y-2">
                                        <div class="text-xs font-bold text-slate-800">تعديل الصلاحيات:</div>
                                        <label class="flex items-center gap-2 text-xs"><input type="checkbox" name="permissions[]" value="إدارة الملاك والعقارات" {{ in_array('إدارة الملاك والعقارات', $stPerms) ? 'checked' : '' }}> إدارة الملاك والعقارات والصكوك</label>
                                        <label class="flex items-center gap-2 text-xs"><input type="checkbox" name="permissions[]" value="إبرام وتوثيق عقود إيجار" {{ in_array('إبرام وتوثيق عقود إيجار', $stPerms) ? 'checked' : '' }}> إبرام وتوثيق عقود إيجار</label>
                                        <label class="flex items-center gap-2 text-xs"><input type="checkbox" name="permissions[]" value="إصدار سندات القبض والصرف" {{ in_array('إصدار سندات القبض والصرف', $stPerms) ? 'checked' : '' }}> إصدار سندات القبض والصرف</label>
                                        <label class="flex items-center gap-2 text-xs"><input type="checkbox" name="permissions[]" value="الاطلاع على شجرة الحسابات" {{ in_array('الاطلاع على شجرة الحسابات', $stPerms) ? 'checked' : '' }}> الاطلاع على شجرة الحسابات</label>
                                    </div>
                                @endif
                                <div class="flex justify-end gap-2 pt-3 border-t">
                                    <button type="button" onclick="closeModal('editStaffModal_{{ $st->id }}')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                    <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">حفظ التعديلات</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            @endif

            <!-- 8. تبويب إعدادات الربط مع واتساب API والبيانات الضريبية للمكتب -->
            @if($tab == 'whatsapp')
                @php
                    $useCustomWa = ($office->use_custom_whatsapp ?? 0) == 1;
                @endphp
                <div class="max-w-2xl bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <h3 class="font-bold text-base text-slate-900">💬 إعدادات المكتب والربط مع واتساب API — ({{ $office->name }})</h3>
                            <p class="text-xs text-slate-500 mt-0.5">حفظ الرقم الضريبي للمكتب وإدارة إرسال أكواد التحقق (OTP) وإشعارات السندات</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $useCustomWa ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                            {{ $useCustomWa ? 'مربوط برقم خاص بالمكتب' : 'مربوط ببوابة الإدارة المركزية (افتراضي)' }}
                        </span>
                    </div>

                    <form action="{{ route('office.whatsapp.update') }}" method="POST" class="space-y-4">
                        @csrf
                        <!-- حفظ الرقم الضريبي والعنوان الوطني للمكتب من الإعدادات -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                            <div class="text-xs font-bold text-slate-900">🏛️ البيانات الضريبية للمكتب (تُستخدم تلقائياً في الفواتير الضريبية)</div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">الرقم الضريبي للمكتب (15 رقماً - اختياري)</label>
                                    <input type="text" name="tax_number" value="{{ $office->tax_number ?? '' }}" maxlength="15" placeholder="300XXXXXXXXXXXX"
                                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs font-mono bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">العنوان الوطني للمكتب</label>
                                    <input type="text" name="national_address" value="{{ $office->national_address ?? $office->address }}" placeholder="الرياض - حي..."
                                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs bg-white">
                                </div>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 space-y-2">
                            <label class="flex items-center gap-2.5 text-sm font-bold text-emerald-950 cursor-pointer">
                                <input type="radio" name="use_custom_whatsapp" value="0" {{ !$useCustomWa ? 'checked' : '' }} onchange="toggleOfficeWaMode(false)" class="text-emerald-600">
                                <span>استخدام رقم نظام ادارة العقارات لإرسال الرسائل </span>
                            </label>
                            <p class="text-xs text-emerald-800 pr-6">
                                يتم إرسال جميع أكواد التحقق والإشعارات تلقائياً عبر رقم الواتساب المعتمد لدى الشركة.
                            </p>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                            <label class="flex items-center gap-2.5 text-sm font-bold text-slate-900 cursor-pointer">
                                <input type="radio" name="use_custom_whatsapp" value="1" {{ $useCustomWa ? 'checked' : '' }} onchange="toggleOfficeWaMode(true)" class="text-emerald-600">
                                <span>ربط رقم واتساب خاص بالمكتب (API مخصص)</span>
                            </label>

                            <div id="customOfficeWaFields" class="{{ $useCustomWa ? '' : 'hidden' }} space-y-3 pt-3 border-t border-slate-200">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">رقم جوال الواتساب الخاص بالمكتب (مع مفتاح الدولة)</label>
                                    <input type="text" name="whatsapp_sender_phone" value="{{ $office->whatsapp_sender_phone ?? '' }}" placeholder="+9665XXXXXXXX" dir="ltr"
                                           class="w-full text-left font-mono rounded-xl border border-slate-300 px-3.5 py-2 text-sm bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">رابط بوابة الإرسال (WhatsApp API URL)</label>
                                    <input type="url" name="whatsapp_api_url" value="{{ $office->whatsapp_api_url ?? '' }}" placeholder="https://api.greatwhats.com/send" dir="ltr"
                                           class="w-full text-left font-mono rounded-xl border border-slate-300 px-3.5 py-2 text-sm bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">مفتاح الربط السري (API Token)</label>
                                    <input type="text" name="whatsapp_api_token" value="{{ $office->whatsapp_api_token ?? '' }}" placeholder="أدخل الـ Token الخاص بحساب المكتب..." dir="ltr"
                                           class="w-full text-left font-mono rounded-xl border border-slate-300 px-3.5 py-2 text-sm bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">معرّف الجلسة / الجهاز (Instance ID - اختياري)</label>
                                    <input type="text" name="whatsapp_instance_id" value="{{ $office->whatsapp_instance_id ?? '' }}" placeholder="instance_xxxx" dir="ltr"
                                           class="w-full text-left font-mono rounded-xl border border-slate-300 px-3.5 py-2 text-sm bg-white">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="px-6 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-lg shadow-emerald-600/20">
                            حفظ الاعدادات
                        </button>
                    </form>
                </div>
            @endif

        </main>
    </div>

    <!-- نافذة معاينة وطباعة وتصدير السند الرسمي PDF -->
    <div id="officialVoucherModal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
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

            <div id="printableVoucherArea" class="border-2 border-slate-800 rounded-3xl p-6 bg-white space-y-5 text-slate-900">
                <div class="flex items-center justify-between border-b-2 border-slate-800 pb-4">
                    <div>
                        <h2 id="pv_issuer" class="text-lg font-bold text-slate-900">{{ $office->name }}</h2>
                        <p class="text-xs text-slate-500">نظام الإدارة المالية والمحاسبية المعتمد</p>
                    </div>
                    <div class="text-center px-5 py-2 rounded-2xl bg-slate-900 text-white">
                        <div id="pv_title" class="text-base font-bold">سند مالي</div>
                        <div id="pv_number" class="text-xs font-mono text-emerald-300 mt-0.5"></div>
                    </div>
                    <div class="text-left text-xs">
                        <div>التاريخ: <b id="pv_date" class="font-mono"></b></div>
                        <div class="mt-1">طريقة الدفع: <b id="pv_method" class="text-emerald-700"></b></div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-200 text-xs">
                    <div>
                        <span class="text-slate-500 block">المبلغ رقماً:</span>
                        <span id="pv_amount" class="text-lg font-bold text-emerald-700 font-mono"></span> <span class="font-bold">ريال سعودي</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block">صاحب العلاقة (المستفيد / الدافع):</span>
                        <span id="pv_party" class="text-sm font-bold text-slate-900"></span>
                    </div>
                    <div class="col-span-2 pt-2 border-t border-slate-200">
                        <span class="text-slate-500 block">وذلك عن (البيان والتفاصيل):</span>
                        <span id="pv_notes" class="font-bold text-slate-900"></span>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 pt-6 text-center text-xs text-slate-600 border-t border-dashed border-slate-300">
                    <div>
                        <p class="font-bold mb-6">المُحاسب / المُصدِر</p>
                        <p class="text-slate-400">............................</p>
                    </div>
                    <div>
                        <p class="font-bold mb-6">المستلم</p>
                        <p class="text-slate-400">............................</p>
                    </div>
                    <div>
                        <p class="font-bold mb-6">الختم والاعتماد</p>
                        <p class="text-emerald-700 font-bold">معتمد إلكترونياً ✔</p>
                    </div>
                </div>
            </div>
        </div>
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

        function filterTableRows(inputId, tbodyId) {
            const q = (document.getElementById(inputId)?.value || '').toLowerCase();
            const rows = document.querySelectorAll('#' + tbodyId + ' tr');
            rows.forEach(r => {
                const text = r.innerText.toLowerCase();
                r.style.display = text.includes(q) ? '' : 'none';
            });
        }

        function openAddUnitModal(propId, propTitle) {
            const propInput = document.getElementById('unit_property_id');
            const titleEl = document.getElementById('addUnitModalTitle');
            if (propInput) propInput.value = propId;
            if (titleEl) titleEl.innerText = 'إضافة وحدة داخل: ' + propTitle;
            openModal('addUnitModal');
        }

        function openAddBranchesModal(parentId, parentName, parentType) {
            if (document.getElementById('branch_parent_id')) document.getElementById('branch_parent_id').value = parentId || '';
            if (parentType && document.getElementById('branch_acc_type')) document.getElementById('branch_acc_type').value = parentType;
            if (document.getElementById('addBranchesTitle')) {
                document.getElementById('addBranchesTitle').innerText = parentId ? 'إضافة فرع تحت: ' + parentName : 'إضافة حساب أو فروع لشجرة المكتب';
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
            const titleEl = document.getElementById('qv_title');
            const subEl = document.getElementById('qv_subtitle');
            const debitSelect = document.getElementById('qv_debit');
            const creditSelect = document.getElementById('qv_credit');
            if (type === 'receipt') {
                if (titleEl) titleEl.innerText = '📥 سند قبض مباشر للمكتب';
                if (subEl) subEl.innerText = 'الحساب الدائن: ' + accountLabel;
                if (creditSelect) creditSelect.value = accountId;
            } else {
                if (titleEl) titleEl.innerText = '📤 سند صرف مباشر للمكتب';
                if (subEl) subEl.innerText = 'الحساب المدين: ' + accountLabel;
                if (debitSelect) debitSelect.value = accountId;
            }
            openModal('quickVoucherModal');
        }

        function toggleOfficeWaMode(isCustom) {
            const box = document.getElementById('customOfficeWaFields');
            if (box) {
                if (isCustom) box.classList.remove('hidden');
                else box.classList.add('hidden');
            }
        }

        let currentVoucherNumber = 'Voucher';
        function previewOfficialVoucher(v) {
            currentVoucherNumber = v.number || 'Voucher';
            if (document.getElementById('pv_issuer')) document.getElementById('pv_issuer').innerText = v.issuer || '';
            if (document.getElementById('pv_title')) document.getElementById('pv_title').innerText = v.type === 'receipt' ? 'سند قبض رسمي (Receipt Voucher)' : 'سند صرف رسمي (Payment Voucher)';
            if (document.getElementById('pv_number')) document.getElementById('pv_number').innerText = v.number || '';
            if (document.getElementById('pv_date')) document.getElementById('pv_date').innerText = v.date || '';
            if (document.getElementById('pv_method')) document.getElementById('pv_method').innerText = v.method || '';
            if (document.getElementById('pv_amount')) document.getElementById('pv_amount').innerText = v.amount || '';
            if (document.getElementById('pv_party')) document.getElementById('pv_party').innerText = v.party || '';
            if (document.getElementById('pv_notes')) document.getElementById('pv_notes').innerText = v.notes || '';
            openModal('officialVoucherModal');
        }

        function downloadVoucherPDF() {
            const element = document.getElementById('printableVoucherArea');
            if (!element) return;
            const opt = {
                margin: 10,
                filename: currentVoucherNumber + '.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        }

        function syncOfficeVoucherAccounts() {
            const vType = document.getElementById('off_auto_vtype')?.value || 'receipt';
            const tenantSel = document.getElementById('off_auto_tenant');
            const ownerSel = document.getElementById('off_auto_owner');
            const pmSel = document.getElementById('off_auto_pm');
            const debitSel = document.getElementById('off_auto_debit');
            const creditSel = document.getElementById('off_auto_credit');

            const tenantAcc = tenantSel?.options[tenantSel.selectedIndex]?.getAttribute('data-account') || '';
            const ownerAcc = ownerSel?.options[ownerSel.selectedIndex]?.getAttribute('data-account') || '';
            const partyAcc = tenantAcc || ownerAcc;
            const pmAcc = pmSel?.options[pmSel.selectedIndex]?.getAttribute('data-account') || '';

            if (vType === 'receipt') {
                if (pmAcc && debitSel) debitSel.value = pmAcc;
                if (partyAcc && creditSel) creditSel.value = partyAcc;
            } else {
                if (partyAcc && debitSel) debitSel.value = partyAcc;
                if (pmAcc && creditSel) creditSel.value = pmAcc;
            }
        }

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
                filename: 'Office_Journal_Entry_' + entryNumber + '.pdf',
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
                filename: 'Office_General_Ledger.pdf',
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
            const partyFilter = document.getElementById('stmt_party_select')?.value || '';
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
            const chronologicalRows = movementRows.slice().reverse();
            let runningFiltered = 0;

            chronologicalRows.forEach(row => {
                const rDate = row.getAttribute('data-date') || '';
                const rAccId = row.getAttribute('data-account-id');
                const rOwnerId = row.getAttribute('data-owner-id') || '';
                const rTenantId = row.getAttribute('data-tenant-id') || '';
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

                if (matchEntity && partyFilter) {
                    if (partyFilter.startsWith('owner_')) {
                        const targetOwner = partyFilter.replace('owner_', '');
                        if (rOwnerId !== targetOwner) matchEntity = false;
                    } else if (partyFilter.startsWith('tenant_')) {
                        const targetTenant = partyFilter.replace('tenant_', '');
                        if (rTenantId !== targetTenant) matchEntity = false;
                    }
                }

                if (matchEntity && searchQ && !hay.includes(searchQ)) matchEntity = false;

                // احتساب الرصيد الافتتاحي للحركات التي تسبق تاريخ البداية المحدد
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

        window.addEventListener('DOMContentLoaded', () => {
            syncOfficeVoucherAccounts();
            applyStatementFilters();
        });
    </script>
</body>
</html>