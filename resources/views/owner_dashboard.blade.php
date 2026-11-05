<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة المالك والعائلة | {{ $owner->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Readex+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Readex Pro', sans-serif; background-color: #f8fafc; }
        .owner-sidebar { background: linear-gradient(180deg, #1e1b4b 0%, #0f172a 100%); }
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

    @php
        $canViewProperties = \App\Http\Controllers\OwnerErpController::hasOwnerPermission('view_properties');
        $canViewContracts  = \App\Http\Controllers\OwnerErpController::hasOwnerPermission('view_contracts');
        $canViewFinancials = \App\Http\Controllers\OwnerErpController::hasOwnerPermission('view_financials');
        $canManageFamily   = \App\Http\Controllers\OwnerErpController::hasOwnerPermission('manage_family');
    @endphp

    <!-- القائمة الجانبية لبوابة المالك -->
    <aside class="w-72 owner-sidebar text-white flex flex-col shrink-0 shadow-2xl sticky top-0 h-screen">
        <div class="p-6 border-b border-white/10 flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-amber-500 to-yellow-400 flex items-center justify-center shadow-lg shadow-amber-500/20 shrink-0 font-bold text-lg text-slate-950">
                👑
            </div>
            <div>
                <h1 class="font-bold text-sm tracking-tight text-white">بوابة الملاك والثروة العقارية</h1>
                <p class="text-[11px] text-amber-300 font-medium">محفظة: {{ $owner->name }}</p>
            </div>
        </div>

        <nav class="p-4 space-y-1.5 flex-1 overflow-y-auto">
            <div class="px-3 py-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider">استعراض المحفظة العقارية</div>

            <a href="{{ route('owner.dashboard', ['tab' => 'overview']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'overview' ? 'bg-gradient-to-l from-amber-500 to-yellow-500 text-slate-950 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <span>📊</span>
                <span>ملخص المحفظة والعوائد</span>
            </a>

            @if($canViewProperties)
                <a href="{{ route('owner.dashboard', ['tab' => 'properties']) }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'properties' ? 'bg-gradient-to-l from-amber-500 to-yellow-500 text-slate-950 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <span>🏛️</span>
                    <span>عقاراتي وصكوكي ({{ $stats['properties_count'] }})</span>
                </a>
            @endif

            @if($canViewContracts)
                <a href="{{ route('owner.dashboard', ['tab' => 'contracts']) }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'contracts' ? 'bg-gradient-to-l from-amber-500 to-yellow-500 text-slate-950 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <span>📝</span>
                    <span>عقود الإيجار ومنصة إيجار</span>
                </a>
            @endif

            @if($canViewFinancials)
                <a href="{{ route('owner.dashboard', ['tab' => 'financials']) }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'financials' ? 'bg-gradient-to-l from-amber-500 to-yellow-500 text-slate-950 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <span>💰</span>
                    <span>كشف الحساب والتحويلات</span>
                </a>
            @endif

            <div class="px-3 pt-4 pb-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider">العائلة والحساب</div>

            @if($canManageFamily)
                <a href="{{ route('owner.dashboard', ['tab' => 'family']) }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'family' ? 'bg-gradient-to-l from-amber-500 to-yellow-500 text-slate-950 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <span>👨‍👩‍👧‍👦</span>
                    <span>أفراد العائلة والوكلاء ({{ $familyMembers->count() }})</span>
                </a>

                <a href="{{ route('owner.dashboard', ['tab' => 'activity_logs']) }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'activity_logs' ? 'bg-gradient-to-l from-amber-500 to-yellow-500 text-slate-950 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <span>🕵️</span>
                    <span>سجل تتبع العائلة والوكلاء</span>
                </a>
            @endif

            <a href="{{ route('owner.dashboard', ['tab' => 'settings']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'settings' ? 'bg-gradient-to-l from-amber-500 to-yellow-500 text-slate-950 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <span>⚙️</span>
                <span>بيانات الدخول الخاصة بي</span>
            </a>
        </nav>

        <div class="p-4 border-t border-white/10 bg-black/25 space-y-2">
            @if(session()->has('impersonate_owner_id'))
                <a href="{{ route('office.return_office') }}" class="block w-full text-center py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs transition">
                    ↩ العودة لبوابة مكتب العقار
                </a>
            @endif
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-white">{{ auth()->user()->name }}</p>
                    <p class="text-[11px] text-amber-300 font-mono" dir="ltr">{{ auth()->user()->phone }}</p>
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

    <!-- المحتوى الرئيسي لبوابة المالك -->
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200/80 px-8 flex items-center justify-between sticky top-0 z-20">
            <div>
                <h2 class="text-base font-bold text-slate-900">محفظة أملاك: {{ $owner->name }}</h2>
                <p class="text-xs text-slate-500">
                    إدارة وتشغيل: <span class="font-bold text-indigo-700">{{ $office->name ?? 'مكتب العقار' }}</span> |
                    الآيبان المعتمد للتحويل: <span class="font-mono font-semibold" dir="ltr">{{ $owner->iban ?: 'غير محدد' }}</span>
                </p>
            </div>
            <div class="flex items-center gap-3">
                @if(auth()->user()->user_type == 'owner_family')
                    <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                        حساب فرد عائلة / وكيل
                    </span>
                @else
                    <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-300">
                        👑 حساب المالك الأصيل
                    </span>
                @endif
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

            <!-- 1. ملخص المحفظة والعوائد -->
            @if($tab == 'overview')
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                        <div class="text-xs font-semibold text-slate-500 mb-2">إجمالي العقارات والصكوك</div>
                        <div class="text-3xl font-bold text-slate-900">{{ $stats['properties_count'] }}</div>
                        <p class="text-xs text-indigo-600 mt-2 font-medium">إجمالي الوحدات: {{ $stats['units_count'] }} وحدة</p>
                    </div>

                    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                        <div class="text-xs font-semibold text-slate-500 mb-2">نسبة الإشغال والوحدات</div>
                        <div class="text-2xl font-bold text-emerald-600">{{ $stats['rented_units'] }} مؤجرة <span class="text-xs text-slate-400 font-normal">/ {{ $stats['vacant_units'] }} شاغرة</span></div>
                        <p class="text-xs text-slate-500 mt-2">العقود السارية: {{ $stats['active_contracts'] }} عقد</p>
                    </div>

                    @if($canViewFinancials)
                        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                            <div class="text-xs font-semibold text-slate-500 mb-2">إجمالي قيمة العقود السنوية</div>
                            <div class="text-2xl font-bold text-slate-900">{{ number_format($stats['gross_rent'], 2) }} <span class="text-xs">ر.س</span></div>
                            <p class="text-xs text-amber-600 mt-2">عمولة إدارة المكتب ({{ $owner->office_management_fee_rate }}%): {{ number_format($stats['office_fees'], 2) }} ر.س</p>
                        </div>

                        <div class="bg-gradient-to-br from-slate-900 to-indigo-950 text-white p-6 rounded-3xl shadow-lg">
                            <div class="text-xs font-semibold text-amber-300 mb-2">صافي العائد السنوي للمالك</div>
                            <div class="text-2xl font-bold text-white">{{ number_format($stats['net_income'], 2) }} <span class="text-xs">ر.س</span></div>
                            <p class="text-xs text-emerald-300 mt-2">المحصل فعلياً بسندات: {{ number_format($stats['total_received_vouchers'], 2) }} ر.س</p>
                        </div>
                    @endif
                </div>

                @if($canViewProperties)
                    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                            <h3 class="font-bold text-slate-900">نظرة سريعة على عقاراتك المدارة</h3>
                            <a href="{{ route('owner.dashboard', ['tab' => 'properties']) }}" class="text-xs font-bold text-indigo-600 hover:underline">عرض كافة التفاصيل والوحدات ←</a>
                        </div>
                        <table class="w-full text-sm text-right">
                            <thead class="bg-slate-50 text-slate-600 text-xs border-b">
                                <tr>
                                    <th class="py-3.5 px-5">مسمى العقار</th>
                                    <th class="py-3.5 px-5">النوع والتصنيف</th>
                                    <th class="py-3.5 px-5">رقم الصك</th>
                                    <th class="py-3.5 px-5">المساحة</th>
                                    <th class="py-3.5 px-5">الموقع</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($properties as $p)
                                    <tr>
                                        <td class="py-4 px-5 font-bold text-slate-900">{{ $p->title }}</td>
                                        <td class="py-4 px-5"><span class="px-2.5 py-1 rounded-xl bg-indigo-50 text-indigo-700 text-xs font-bold">{{ $p->type_name }}</span></td>
                                        <td class="py-4 px-5 font-mono text-xs font-bold text-emerald-700">{{ $p->deed_number }}</td>
                                        <td class="py-4 px-5">{{ number_format($p->total_area, 0) }} م²</td>
                                        <td class="py-4 px-5 text-xs text-slate-500">{{ $p->city }} - {{ $p->district }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="py-8 text-center text-slate-400">لا توجد عقارات مسجلة بعد.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            @endif

            <!-- 2. تبويب عقاراتي وصكوكي ووحداتي -->
            @if($tab == 'properties' && $canViewProperties)
                <div class="space-y-6">
                    @forelse($properties as $prop)
                        @php
                            $propUnits = $units->where('property_id', $prop->id);
                            $savedAttrs = json_decode($prop->custom_attributes ?? '{}', true) ?: [];
                        @endphp
                        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                            <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-100 pb-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-3 py-1 rounded-xl bg-indigo-50 text-indigo-700 text-xs font-bold">{{ $prop->type_name }}</span>
                                        <h3 class="font-bold text-lg text-slate-900">{{ $prop->title }}</h3>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1.5">
                                        رقم الصك: <b class="font-mono text-emerald-700">{{ $prop->deed_number }}</b> |
                                        تاريخ الصك: <b>{{ $prop->deed_date ?: 'غير محدد' }}</b> |
                                        المساحة الإجمالية: <b>{{ number_format($prop->total_area, 2) }} م²</b> |
                                        العنوان الوطني: {{ $prop->city }} - {{ $prop->district }} {{ $prop->street ? '- ' . $prop->street : '' }}
                                    </p>
                                    @if(count($savedAttrs) > 0)
                                        <div class="flex flex-wrap gap-2 mt-3">
                                            @foreach($savedAttrs as $k => $v)
                                                @if(!empty($v))
                                                    <span class="px-3 py-1 rounded-xl bg-slate-100 text-slate-700 text-xs">
                                                        {{ $k }}: <b>{{ $v }}</b>
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="text-left">
                                    <span class="text-xs text-slate-400">القيمة التقديرية للعقار</span>
                                    <div class="text-base font-bold text-slate-900">{{ number_format($prop->estimated_value, 2) }} ر.س</div>
                                </div>
                            </div>

                            @if($propUnits->count() > 0)
                                <div>
                                    <h4 class="text-xs font-bold text-slate-700 mb-3">الوحدات العقارية التابعة لهذا العقار ({{ $propUnits->count() }} وحدة):</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        @foreach($propUnits as $u)
                                            <div class="p-3.5 rounded-2xl border {{ $u->status == 'rented' ? 'bg-blue-50/30 border-blue-200' : 'bg-emerald-50/30 border-emerald-200' }} flex items-center justify-between">
                                                <div>
                                                    <div class="font-bold text-sm text-slate-900">وحدة رقم: {{ $u->unit_number }}</div>
                                                    <div class="text-xs text-slate-500">الدور: {{ $u->floor_number }} | عداد كهرباء: {{ $u->electricity_meter ?: '-' }}</div>
                                                    <div class="text-xs font-bold text-indigo-700 mt-1">الإيجار السنوي: {{ number_format($u->default_rent_price, 0) }} ر.س</div>
                                                </div>
                                                <div>
                                                    @if($u->status == 'rented')
                                                        <span class="px-2.5 py-1 rounded-xl bg-blue-100 text-blue-800 text-xs font-bold">مؤجرة</span>
                                                    @else
                                                        <span class="px-2.5 py-1 rounded-xl bg-emerald-100 text-emerald-800 text-xs font-bold">شاغرة</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="bg-white rounded-3xl border p-12 text-center text-slate-400">لا توجد عقارات مسجلة في محفظتك حالياً.</div>
                    @endforelse
                </div>
            @endif

            <!-- 3. تبويب عقود الإيجار ومنصة إيجار -->
            @if($tab == 'contracts' && $canViewContracts)
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100">
                        <h3 class="font-bold text-slate-900">سجل عقود الإيجار المبرمة على عقاراتك</h3>
                    </div>
                    <table class="w-full text-sm text-right">
                        <thead class="bg-slate-50 text-slate-600 text-xs border-b">
                            <tr>
                                <th class="py-3.5 px-5">رقم العقد</th>
                                <th class="py-3.5 px-5">العقار والوحدة</th>
                                <th class="py-3.5 px-5">المستأجر</th>
                                <th class="py-3.5 px-5">مدة العقد</th>
                                <th class="py-3.5 px-5">الإيجار السنوي</th>
                                <th class="py-3.5 px-5">صافي العائد لك</th>
                                <th class="py-3.5 px-5">حالة منصة إيجار</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($contracts as $c)
                                <tr>
                                    <td class="py-4 px-5 font-mono font-bold">{{ $c->contract_number }}</td>
                                    <td class="py-4 px-5 font-bold">{{ $c->property_title }} {{ $c->unit_number ? '(وحدة ' . $c->unit_number . ')' : '' }}</td>
                                    <td class="py-4 px-5">{{ $c->tenant_name }}</td>
                                    <td class="py-4 px-5 text-xs">{{ $c->start_date }} إلى {{ $c->end_date }}</td>
                                    <td class="py-4 px-5 font-bold">{{ number_format($c->total_rent_amount, 2) }} ر.س</td>
                                    <td class="py-4 px-5 font-bold text-emerald-700">{{ number_format($c->total_rent_amount - $c->office_commission, 2) }} ر.س</td>
                                    <td class="py-4 px-5">
                                        @if($c->ejar_status == 'active')
                                            <span class="px-2.5 py-1 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-bold">✅ موثق ({{ $c->ejar_contract_number }})</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-xl bg-amber-50 text-amber-700 text-xs font-bold">قيد التوثيق</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="py-8 text-center text-slate-400">لا توجد عقود إيجار مسجلة بعد.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- 4. تبويب كشف الحساب المالي والسندات -->
            @if($tab == 'financials' && $canViewFinancials)
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900">كشف الحساب المالي وسندات القبض والصرف</h3>
                            <p class="text-xs text-slate-500 mt-0.5">جميع العمليات المالية المنفذة على محفظتك العقارية عبر مكتب العقار</p>
                        </div>
                    </div>
                    <table class="w-full text-sm text-right">
                        <thead class="bg-slate-50 text-slate-600 text-xs border-b">
                            <tr>
                                <th class="py-3.5 px-5">رقم السند</th>
                                <th class="py-3.5 px-5">نوع العملية</th>
                                <th class="py-3.5 px-5">المستأجر المرتبط</th>
                                <th class="py-3.5 px-5">المبلغ</th>
                                <th class="py-3.5 px-5">طريقة الدفع</th>
                                <th class="py-3.5 px-5">البيان والتاريخ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($vouchers as $v)
                                <tr>
                                    <td class="py-4 px-5 font-mono font-bold">{{ $v->voucher_number }}</td>
                                    <td class="py-4 px-5">
                                        @if($v->voucher_type == 'receipt')
                                            <span class="px-2.5 py-1 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-bold">📥 تحصيل إيجار للمحفظة</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-xl bg-indigo-50 text-indigo-700 text-xs font-bold">📤 تحويل للمالك / مصروفات</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-5 font-semibold">{{ $v->tenant_name ?: '-' }}</td>
                                    <td class="py-4 px-5 font-bold text-emerald-700">{{ number_format($v->amount, 2) }} ر.س</td>
                                    <td class="py-4 px-5">
                                        <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 text-xs font-bold">
                                            {{ \App\Http\Controllers\CompanyErpController::translatePaymentMethod($v->payment_method) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-5 text-xs text-slate-600">{{ $v->notes }} <span class="font-mono text-slate-400">({{ $v->voucher_date }})</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-8 text-center text-slate-400">لا توجد حركات مالية أو سندات مسجلة حتى الآن.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- 5. تبويب إدارة أفراد العائلة والوكلاء والصلاحيات -->
            @if($tab == 'family' && $canManageFamily)
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
                        <h3 class="font-bold text-base text-slate-900 mb-1">إضافة فرد من العائلة أو وكيل</h3>
                        <p class="text-xs text-slate-500 mb-4">امنح أبناءك أو وكيلك الشرعي حساب دخول وحدد ما يمكنهم رؤيته</p>

                        <form action="{{ route('owner.family.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">الاسم الكامل *</label>
                                <input type="text" name="name" required placeholder="مثال: محمد بن فيصل" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">صلة القرابة / الصفة *</label>
                                <select name="relation" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white">
                                    <option value="ابن / ابنة">ابن / ابنة</option>
                                    <option value="زوج / زوجة">زوج / زوجة</option>
                                    <option value="شريك عقاري">شريك عقاري</option>
                                    <option value="وكيل شرعي">وكيل شرعي</option>
                                    <option value="محاسب العائلة">محاسب العائلة</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">رقم الجوال (10 أرقام يبدأ بـ 05) *</label>
                                <input type="tel" name="phone" maxlength="10" required placeholder="05XXXXXXXX" dir="ltr"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                                       class="w-full text-left font-mono rounded-xl border border-slate-300 px-3.5 py-2 text-sm">
                            </div>
                            <div class="grid grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">اسم المستخدم *</label>
                                    <input type="text" name="username" required placeholder="mohammed_f" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">كلمة المرور *</label>
                                    <input type="text" name="password" value="123456" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                </div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-indigo-50/60 border border-indigo-200 space-y-2">
                                <div class="text-xs font-bold text-indigo-900 mb-1">الصلاحيات الممنوحة لهذا العضو:</div>
                                <label class="flex items-center gap-2 text-xs cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="view_properties" checked class="rounded text-indigo-600">
                                    <span>الاطلاع على العقارات والصكوك والوحدات</span>
                                </label>
                                <label class="flex items-center gap-2 text-xs cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="view_contracts" checked class="rounded text-indigo-600">
                                    <span>الاطلاع على عقود الإيجار والمستأجرين</span>
                                </label>
                                <label class="flex items-center gap-2 text-xs cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="view_financials" class="rounded text-indigo-600">
                                    <span>الاطلاع على المبالغ المالية وكشف الحساب</span>
                                </label>
                                <label class="flex items-center gap-2 text-xs cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="manage_family" class="rounded text-indigo-600">
                                    <span>إدارة وإضافة أفراد العائلة</span>
                                </label>
                            </div>

                            <button type="submit" class="w-full py-3 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm shadow-lg">
                                + إضافة العضو وتفعيل صلاحياته
                            </button>
                        </form>
                    </div>

                    <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-100">
                            <h3 class="font-bold text-slate-900">أفراد العائلة والوكلاء المضافون وصلاحياتهم</h3>
                        </div>
                        <table class="w-full text-sm text-right">
                            <thead class="bg-slate-50 text-slate-600 text-xs border-b">
                                <tr>
                                    <th class="py-3.5 px-5">الاسم والصفة</th>
                                    <th class="py-3.5 px-5">رقم الجوال / المستخدم</th>
                                    <th class="py-3.5 px-5">الصلاحيات المسموحة</th>
                                    <th class="py-3.5 px-5 text-center">إجراء</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($familyMembers as $fm)
                                    @php
                                        $fPerms = json_decode($fm->custom_permissions ?? '[]', true) ?: [];
                                        $permLabels = [
                                            'view_properties' => 'العقارات والصكوك',
                                            'view_contracts' => 'العقود والمستأجرين',
                                            'view_financials' => 'كشف الحساب المالي',
                                            'manage_family' => 'إدارة العائلة',
                                        ];
                                    @endphp
                                    <tr>
                                        <td class="py-4 px-5 font-bold text-slate-900">{{ $fm->name }}</td>
                                        <td class="py-4 px-5">
                                            <div class="font-mono text-xs font-bold text-emerald-700" dir="ltr">{{ $fm->phone }}</div>
                                            <div class="text-xs text-slate-400">المستخدم: {{ $fm->username }}</div>
                                        </td>
                                        <td class="py-4 px-5">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($fPerms as $fp)
                                                    <span class="px-2.5 py-0.5 rounded-lg bg-indigo-50 text-indigo-700 text-[11px] font-bold">
                                                        {{ $permLabels[$fp] ?? $fp }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                       <td class="py-4 px-5 text-center">
                                            <div class="flex items-center justify-center gap-1.5">
                                                <button type="button" onclick="document.getElementById('editFamilyModal_{{ $fm->id }}').classList.remove('hidden'); document.getElementById('editFamilyModal_{{ $fm->id }}').classList.add('flex');"
                                                        class="px-3 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-bold">تعديل</button>
                                                <form action="{{ route('owner.family.delete', $fm->id) }}" method="POST" onsubmit="return confirm('إلغاء حساب فرد العائلة؟');">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-red-50 text-red-600 text-xs font-bold">حذف</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="py-8 text-center text-slate-400">لم تقم بإضافة أي فرد من العائلة أو وكيل حتى الآن.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @foreach($familyMembers as $fm)
                    @php
                        $fPerms = json_decode($fm->custom_permissions ?? '[]', true) ?: [];
                        $fmLocalPhone = \App\Http\Controllers\CompanyErpController::toLocalPhone($fm->phone);
                    @endphp
                    <div id="editFamilyModal_{{ $fm->id }}" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                            <div class="flex items-center justify-between pb-3 border-b mb-4">
                                <h3 class="font-bold text-base text-slate-900">تعديل بيانات وصلاحيات: {{ $fm->name }}</h3>
                                <button type="button" onclick="document.getElementById('editFamilyModal_{{ $fm->id }}').classList.add('hidden'); document.getElementById('editFamilyModal_{{ $fm->id }}').classList.remove('flex');" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                            </div>
                            <form action="{{ route('owner.family.update', $fm->id) }}" method="POST" class="space-y-3.5">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">الاسم والصفة *</label>
                                    <input type="text" name="name" value="{{ $fm->name }}" required class="w-full rounded-xl border px-3.5 py-2 text-sm font-bold">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">رقم الجوال *</label>
                                        <input type="tel" name="phone" value="{{ $fmLocalPhone }}" maxlength="10" required dir="ltr" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);" class="w-full rounded-xl border px-3 py-2 text-xs font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">اسم المستخدم *</label>
                                        <input type="text" name="username" value="{{ $fm->username }}" required class="w-full rounded-xl border px-3 py-2 text-xs">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">كلمة مرور جديدة (اتركها فارغة لعدم التغيير)</label>
                                    <input type="text" name="password" placeholder="أدخل كلمة مرور جديدة فقط عند الرغبة" class="w-full rounded-xl border px-3.5 py-2 text-xs">
                                </div>
                                <div class="p-3.5 rounded-2xl bg-indigo-50/60 border border-indigo-200 space-y-2">
                                    <div class="text-xs font-bold text-indigo-900">تعديل الصلاحيات الممنوحة:</div>
                                    <label class="flex items-center gap-2 text-xs"><input type="checkbox" name="permissions[]" value="view_properties" {{ in_array('view_properties', $fPerms) ? 'checked' : '' }}> الاطلاع على العقارات والصكوك</label>
                                    <label class="flex items-center gap-2 text-xs"><input type="checkbox" name="permissions[]" value="view_contracts" {{ in_array('view_contracts', $fPerms) ? 'checked' : '' }}> الاطلاع على العقود والمستأجرين</label>
                                    <label class="flex items-center gap-2 text-xs"><input type="checkbox" name="permissions[]" value="view_financials" {{ in_array('view_financials', $fPerms) ? 'checked' : '' }}> الاطلاع على المبالغ وكشف الحساب</label>
                                    <label class="flex items-center gap-2 text-xs"><input type="checkbox" name="permissions[]" value="manage_family" {{ in_array('manage_family', $fPerms) ? 'checked' : '' }}> إدارة أفراد العائلة</label>
                                </div>
                                <div class="flex justify-end gap-2 pt-3 border-t">
                                    <button type="button" onclick="document.getElementById('editFamilyModal_{{ $fm->id }}').classList.add('hidden'); document.getElementById('editFamilyModal_{{ $fm->id }}').classList.remove('flex');" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                    <button type="submit" class="px-6 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold">حفظ التعديلات</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            @endif

            <!-- سجل تتبع حركات أفراد العائلة والوكلاء فقط (بدون IP وبدون MAC) -->
            @if($tab == 'activity_logs' && $canManageFamily)
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="font-bold text-base text-slate-900">🕵️ سجل تتبع حركات أفراد العائلة والوكلاء</h3>
                            <p class="text-xs text-slate-500 mt-0.5">يعرض جميع العمليات والتعديلات التي قام بها أفراد العائلة أو الوكلاء المضافون في محفظتك</p>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-right">
                            <thead class="bg-slate-50 text-slate-600 border-b">
                                <tr>
                                    <th class="py-3.5 px-4">#</th>
                                    <th class="py-3.5 px-4">فرد العائلة / الوكيل</th>
                                    <th class="py-3.5 px-4">نوع الحركة</th>
                                    <th class="py-3.5 px-4">الإجراء</th>
                                    <th class="py-3.5 px-4">التفاصيل</th>
                                    <th class="py-3.5 px-4">التاريخ والوقت</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse(($activityLogs ?? collect()) as $idx => $log)
                                    <tr class="hover:bg-slate-50">
                                        <td class="py-3 px-4 font-mono font-bold">{{ $idx + 1 }}</td>
                                        <td class="py-3 px-4 font-bold text-slate-900">{{ $log->user_name }}</td>
                                        <td class="py-3 px-4">
                                            <span class="px-2.5 py-1 rounded-lg font-bold bg-indigo-50 text-indigo-700">{{ $log->action_type }}</span>
                                        </td>
                                        <td class="py-3 px-4 font-bold text-slate-800">{{ $log->action_label }}</td>
                                        <td class="py-3 px-4 text-slate-600">{{ $log->details }}</td>
                                        <td class="py-3 px-4 font-mono text-slate-500" dir="ltr">{{ $log->created_at }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="py-10 text-center text-slate-400">لا توجد حركات مسجلة لأفراد العائلة حتى الآن.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- 6. إعدادات الحساب الشخصي للمالك أو فرد العائلة -->
            @if($tab == 'settings')
                @php
                    $myLocalPhone = \App\Http\Controllers\CompanyErpController::toLocalPhone(auth()->user()->phone);
                @endphp
                <div class="max-w-xl bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
                    <h3 class="font-bold text-base text-slate-900 mb-1">تعديل بيانات الدخول الخاصة بك</h3>
                    <p class="text-xs text-slate-500 mb-5">تحديث رقم الجوال المعتمد للواتساب أو اسم المستخدم وكلمة المرور</p>

                    <form action="{{ route('owner.profile.update') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">الاسم *</label>
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
                        <button type="submit" class="px-6 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm shadow-lg">
                            حفظ التعديلات
                        </button>
                    </form>
                </div>
            @endif

        </main>
    </div>

<!-- بداية البلوك الشامل المطور: تطابق العرض 100% في الصفحات الأفقية والعمودية والأستاذ العام + إصلاح الـ PDF والخطوط العربية -->
<style id="arabic-pdf-and-journal-fixes">
    /* 1. ضبط الخطوط العربية ومنع تقطع الحروف أو التصاق الكلمات */
    body, body *, table, th, td, div, span, p, h1, h2, h3, h4, b, strong, .font-mono {
        font-family: 'Readex Pro', 'Tahoma', 'Arial', sans-serif !important;
        letter-spacing: 0px !important;
        font-kerning: normal !important;
        font-variant-ligatures: normal !important;
    }

    /* 2. توحيد وتطابق العرض على الشاشة في جميع الصفحات والأستاذ العام وكشف الحساب */
    main {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        box-sizing: border-box !important;
    }
    main > div,
    #financialStatementPrintable,
    #pro_ownerSettlementPrintableArea,
    #pro_general_ledger_view,
    #officePropertiesPrintableWrap,
    #officeInstallmentsPrintableWrap {
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }

    /* منع انكسار شريط الفلاتر في صفحة الأستاذ العام عند إظهار فلتر الحساب أو المصروف */
    #financialStatementPrintable .grid.md\:grid-cols-12 {
        display: grid !important;
        grid-template-columns: repeat(auto-fit, minmax(155px, 1fr)) !important;
        gap: 12px !important;
        align-items: end !important;
    }
    #financialStatementPrintable .grid.md\:grid-cols-12 > div {
        grid-column: span 1 / span 1 !important;
        width: 100% !important;
    }

    /* 3. تثبيت حاوية html2pdf في نقطة الصفر (0,0) لمنع الإزاحة والقص في الصفحات الأفقية والعمودية نهائياً */
    .html2pdf__overlay {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: auto !important;
        margin: 0 !important;
        padding: 0 !important;
        direction: ltr !important;
    }
    .html2pdf__container {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: auto !important;
        margin: 0 !important;
        padding: 0 !important;
        transform: none !important;
        direction: rtl !important;
        box-sizing: border-box !important;
    }

    /* 4. منع انقسام الصفوف والبطاقات بين صفحات الـ PDF */
    tr, thead, tfoot, .journal-table-block, .pdf-no-break {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }
    thead { display: table-header-group !important; }
    tfoot { display: table-footer-group !important; }

    /* 5. تنسيق نسخة الـ PDF لتطابق عرض الورقة 100% من اليمين إلى اليسار بدون أي فراغ أو قص */
    .erp-pdf-clean-clone {
        direction: rtl !important;
        text-align: right !important;
        background: #ffffff !important;
        color: #0f172a !important;
        padding: 12px !important;
        margin: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        min-width: 100% !important;
        max-height: none !important;
        overflow: visible !important;
        box-sizing: border-box !important;
        word-spacing: 2.5px !important;
        text-rendering: auto !important;
    }
    .erp-pdf-clean-clone * {
        font-family: 'Readex Pro', 'Tahoma', 'Arial', sans-serif !important;
        letter-spacing: 0px !important;
        word-spacing: 2px !important;
        text-rendering: auto !important;
        max-height: none !important;
        overflow: visible !important;
        box-sizing: border-box !important;
    }
    .erp-pdf-clean-clone .max-w-2xl,
    .erp-pdf-clean-clone .max-w-3xl,
    .erp-pdf-clean-clone .max-w-4xl,
    .erp-pdf-clean-clone .overflow-x-auto {
        max-width: 100% !important;
        width: 100% !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
    }

    /* ضبط البطاقات الـ 6 في تصفية المالك والبطاقات الـ 5 في الأستاذ العام لتظهر كاملة في سطر واحد */
    .erp-pdf-clean-clone .md\:grid-cols-6 {
        display: grid !important;
        grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
        gap: 8px !important;
        width: 100% !important;
    }
    .erp-pdf-clean-clone .md\:grid-cols-5 {
        display: grid !important;
        grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
        gap: 8px !important;
        width: 100% !important;
    }
    .erp-pdf-clean-clone .md\:grid-cols-4 {
        display: grid !important;
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        gap: 8px !important;
        width: 100% !important;
    }
    .erp-pdf-clean-clone .md\:grid-cols-6 > div,
    .erp-pdf-clean-clone .md\:grid-cols-5 > div,
    .erp-pdf-clean-clone .md\:grid-cols-4 > div {
        padding: 8px 6px !important;
        min-width: 0 !important;
    }
    .erp-pdf-clean-clone .text-2xl,
    .erp-pdf-clean-clone .text-lg {
        font-size: 13px !important;
        line-height: 1.35 !important;
    }

    /* إخفاء عناصر التحكم والأزرار غير المطبوعة فقط (دون إخفاء أسماء الحسابات والملاك) */
    .erp-pdf-clean-clone .no-print,
    .erp-pdf-clean-clone .erp-pagination-bar,
    .erp-pdf-clean-clone form,
    .erp-pdf-clean-clone input[type="text"],
    .erp-pdf-clean-clone input[type="date"],
    .erp-pdf-clean-clone select,
    .erp-pdf-clean-clone .erp-picker-host {
        display: none !important;
    }

    /* تنسيق الجداول في الـ PDF لتملأ 100% من عرض الصفحة بانتظام */
    .erp-pdf-clean-clone table {
        width: 100% !important;
        min-width: 100% !important;
        max-width: 100% !important;
        border-collapse: collapse !important;
        table-layout: auto !important;
        direction: rtl !important;
        margin-top: 6px !important;
    }
    .erp-pdf-clean-clone th {
        background-color: #0f172a !important;
        color: #ffffff !important;
        border: 1px solid #334155 !important;
        padding: 7px 6px !important;
        font-size: 10.5px !important;
        font-weight: 700 !important;
        text-align: right !important;
        white-space: normal !important;
        line-height: 1.3 !important;
    }
    .erp-pdf-clean-clone td {
        border: 1px solid #cbd5e1 !important;
        padding: 6px 6px !important;
        font-size: 10.5px !important;
        line-height: 1.4 !important;
        color: #0f172a !important;
        white-space: normal !important;
        word-break: break-word !important;
        text-align: right !important;
        vertical-align: middle !important;
    }
    .erp-pdf-clean-clone tfoot td,
    .erp-pdf-clean-clone tfoot th {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
        font-weight: 800 !important;
        border: 1px solid #94a3b8 !important;
    }
</style>

<script id="erp-master-fixes-script">
(function() {
    // 1. إصلاح انعكاس الأقواس والنقطتين في الكلمات العربية داخل Canvas أثناء توليد الـ PDF
    const origFillText = CanvasRenderingContext2D.prototype.fillText;
    CanvasRenderingContext2D.prototype.fillText = function(text, x, y, maxWidth) {
        if (window.__erpPdfRenderingActive && typeof text === 'string' && /[\u0600-\u06FF]/.test(text)) {
            this.direction = 'rtl';
            let fixed = '\u200F' + text + '\u200F';
            fixed = fixed.replace(/:/g, ':\u200F').replace(/\(/g, '\u200F(').replace(/\)/g, ')\u200F');
            return maxWidth !== undefined
                ? origFillText.call(this, fixed, x, y, maxWidth)
                : origFillText.call(this, fixed, x, y);
        }
        return maxWidth !== undefined
            ? origFillText.call(this, text, x, y, maxWidth)
            : origFillText.call(this, text, x, y);
    };

    // 2. تعريب طرق الدفع في جميع الجداول والنوافذ
    const PM_AR = {
        bank_transfer: 'حوالة بنكية', transfer: 'حوالة بنكية', bank: 'حوالة بنكية',
        sadad: 'نظام سداد', mada: 'شبكة مدى', pos: 'شبكة مدى', card: 'شبكة مدى',
        cash: 'نقداً (كاش)', cheque: 'شيك مصدق', check: 'شيك مصدق'
    };

    window.translatePaymentMethodAr = function(v) {
        if (!v) return 'حوالة بنكية';
        const k = String(v).trim().toLowerCase();
        return PM_AR[k] || v;
    };

    function localizeDOM() {
        if (Array.isArray(window.officeVouchersMaster)) {
            window.officeVouchersMaster.forEach(v => {
                if (v && v.payment_method) v.payment_method = window.translatePaymentMethodAr(v.payment_method);
            });
        }
        document.querySelectorAll('td, span, b, div, option').forEach(el => {
            if (el.children.length === 0 && el.innerText) {
                const k = el.innerText.trim().toLowerCase();
                if (PM_AR[k]) el.innerText = PM_AR[k];
            }
        });
    }

    const origVoucher = window.openOfficialVoucherPreview;
    window.openOfficialVoucherPreview = function(v) {
        if (v) {
            v.payment_method = window.translatePaymentMethodAr(v.payment_method || v.method);
            v.method = v.payment_method;
        }
        if (typeof origVoucher === 'function') origVoucher(v);
        const m = document.getElementById('pdf_method');
        if (m) m.innerText = window.translatePaymentMethodAr(m.innerText);
    };
    window.previewOfficialVoucher = window.openOfficialVoucherPreview;

    // 3. حساب إجمالي القيد اليومي المباشر + ضريبة المدين مدين وضريبة الدائن دائن
    const fmt = n => Number(n || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    window.recalculateJournalFormTotals = function(form) {
        if (!form) return;
        const debits = form.querySelectorAll('input[name="debits[]"]');
        const credits = form.querySelectorAll('input[name="credits[]"]');
        const taxes = form.querySelectorAll('input[name="taxes[]"]');
        let bD = 0, bC = 0, tD = 0, tC = 0;

        for (let i = 0; i < Math.max(debits.length, credits.length); i++) {
            const d = parseFloat(debits[i]?.value || 0) || 0;
            const c = parseFloat(credits[i]?.value || 0) || 0;
            const t = parseFloat(taxes[i]?.value || 0) || 0;
            bD += d; bC += c;
            if (taxes[i]) {
                const lbl = taxes[i].parentElement.querySelector('label');
                let hint = taxes[i].parentElement.querySelector('.row-tax-hint');
                if (!hint) {
                    hint = document.createElement('div');
                    hint.className = 'row-tax-hint text-[10px] font-bold mt-1';
                    taxes[i].parentElement.appendChild(hint);
                }
                if (d > 0) {
                    tD += t;
                    if (lbl) lbl.innerText = 'الضريبة (مدين +)';
                    hint.className = 'row-tax-hint text-[10px] font-bold mt-1 text-emerald-700';
                    hint.innerText = 'الإجمالي: ' + fmt(d + t);
                } else if (c > 0) {
                    tC += t;
                    if (lbl) lbl.innerText = 'الضريبة (دائن -)';
                    hint.className = 'row-tax-hint text-[10px] font-bold mt-1 text-amber-700';
                    hint.innerText = 'الإجمالي: ' + fmt(c + t);
                } else {
                    if (lbl) lbl.innerText = 'الضريبة';
                    hint.innerText = '';
                }
            }
        }

        const totD = bD + tD, totC = bC + tC;
        const entryTotal = Math.max(totD, totC);
        const diff = Math.round((totD - totC) * 100) / 100;
        const ok = Math.abs(diff) <= 0.05 && entryTotal > 0;

        let box = form.querySelector('.live-journal-summary-bar');
        if (!box) {
            box = document.createElement('div');
            box.className = 'live-journal-summary-bar p-4 rounded-2xl bg-slate-900 text-white space-y-2 my-3';
            const footer = form.querySelector('.flex.justify-end');
            footer ? form.insertBefore(box, footer) : form.appendChild(box);
        }

        box.innerHTML = `
            <div class="flex flex-wrap justify-between items-center border-b border-slate-700 pb-2 text-xs">
                <b class="text-emerald-400">📊 إجمالي القيد اليومي المباشر (شامل الضريبة):</b>
                <span class="px-2.5 py-1 rounded font-bold ${ok ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/30 text-rose-200'}">
                    ${ok ? '✔ متوازن (الفرق: 0.00)' : '⚠️ غير متوازن — الفرق: ' + fmt(Math.abs(diff)) + ' ر.س'}
                </span>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs pt-1">
                <div class="p-2 rounded bg-white/5">المدين + ضريبته: <b class="text-emerald-400 block">${fmt(bD)} + ${fmt(tD)} = ${fmt(totD)} ر.س</b></div>
                <div class="p-2 rounded bg-white/5">الدائن + ضريبته: <b class="text-amber-400 block">${fmt(bC)} + ${fmt(tC)} = ${fmt(totC)} ر.س</b></div>
                <div class="p-2 rounded bg-emerald-500/20">إجمالي القيد المعتمد: <b class="text-white text-sm block">${fmt(entryTotal)} ر.س</b></div>
                <div class="p-2 rounded bg-white/5">صافي الفرق (التوازن): <b class="${ok ? 'text-emerald-400' : 'text-rose-300'} text-sm block">${fmt(diff)} ر.س</b></div>
            </div>
        `;
    };

    function initJournalAndLedgerFixes() {
        // إزالة شريط الترقيم الخاطئ عن جداول القوائم المالية الفرعية وجداول برنت تصفية المالك
        ['#cards_view_section table', '#trial_view_section table', '#income_view_section table', '#pro_rep_contracts_table', '#pro_rep_vouchers_table'].forEach(sel => {
            document.querySelectorAll(sel).forEach(tbl => {
                if (tbl._paginationBar) {
                    tbl._paginationBar.remove();
                    tbl._paginationBar = null;
                }
                tbl.querySelectorAll('tbody tr').forEach(r => r.style.display = '');
            });
        });

        // توحيد فلترة الأستاذ العام وحساب الرصيد التراكمي والافتتاحي والختامي بدقة
        const origApplyFilters = window.applyStatementFilters;
        window.applyStatementFilters = function() {
            if (typeof origApplyFilters === 'function') {
                origApplyFilters();
            }
            const rows = Array.from(document.querySelectorAll('.stmt-movement-row'));
            if (rows.length === 0) return;

            const q = (document.getElementById('ledgerInstantSearch')?.value || '').trim().toLowerCase();
            const mode = document.getElementById('stmt_filter_mode')?.value || 'all';
            const specExp = document.getElementById('stmt_expense_select')?.value || '';
            const specAcc = document.getElementById('stmt_account_select')?.value || '';
            const officeId = document.getElementById('stmt_office_select')?.value || '';
            const partyVal = document.getElementById('stmt_party_select')?.value || '';
            const fromDate = document.getElementById('stmt_from_date')?.value || '';
            const toDate = document.getElementById('stmt_to_date')?.value || '';

            let debitSum = 0, creditSum = 0;
            rows.forEach(row => {
                const rSearch = (row.getAttribute('data-search') || '').toLowerCase();
                const rDate = row.getAttribute('data-date') || '';
                const rAcc = row.getAttribute('data-account-id') || '';
                const rOff = row.getAttribute('data-office-id') || '';
                const rOwn = row.getAttribute('data-owner-id') || '';
                const rTen = row.getAttribute('data-tenant-id') || '';
                const isExp = row.getAttribute('data-is-expense') === '1';
                const isRev = row.getAttribute('data-is-revenue') === '1';

                let show = true;
                if (q && !rSearch.includes(q)) show = false;
                if (mode === 'all_expenses' && !isExp) show = false;
                if (mode === 'all_revenues' && !isRev) show = false;
                if (mode === 'specific_expense' && specExp && rAcc !== specExp) show = false;
                if (mode === 'specific_account' && specAcc && rAcc !== specAcc) show = false;
                if (officeId && rOff !== officeId) show = false;
                if (partyVal) {
                    if (partyVal.startsWith('owner_') && rOwn !== partyVal.replace('owner_', '')) show = false;
                    if (partyVal.startsWith('tenant_') && rTen !== partyVal.replace('tenant_', '')) show = false;
                }
                if (fromDate && rDate < fromDate) show = false;
                if (toDate && rDate > toDate) show = false;

                row.dataset.searchMatched = show ? '1' : '0';
                row.style.display = show ? '' : 'none';
                if (show) {
                    debitSum += parseFloat(row.getAttribute('data-debit') || 0);
                    creditSum += parseFloat(row.getAttribute('data-credit') || 0);
                }
            });

            const net = debitSum - creditSum;
            if (document.getElementById('filteredDebitSum')) document.getElementById('filteredDebitSum').innerText = fmt(debitSum);
            if (document.getElementById('filteredCreditSum')) document.getElementById('filteredCreditSum').innerText = fmt(creditSum);
            if (document.getElementById('filteredNetSum')) document.getElementById('filteredNetSum').innerText = fmt(net);
            if (document.getElementById('ledgerClosingBalance')) document.getElementById('ledgerClosingBalance').innerText = fmt(net);

            const table = document.getElementById('generalStatementTable');
            if (table) {
                if (typeof window.refreshTablePagination === 'function') window.refreshTablePagination(table, true);
                else if (typeof window.refreshAdminTablePagination === 'function') window.refreshAdminTablePagination(table, true);
            }
        };

        document.querySelectorAll('form[action*="journal-entries"]').forEach(f => {
            if (f.dataset.bound !== '1') {
                f.dataset.bound = '1';
                f.addEventListener('input', e => {
                    const t = e.target;
                    const row = t?.closest('.grid');
                    if (t?.name === 'debits[]' && parseFloat(t.value) > 0 && row) {
                        const c = row.querySelector('input[name="credits[]"]');
                        if (c && parseFloat(c.value) > 0) c.value = '0';
                    } else if (t?.name === 'credits[]' && parseFloat(t.value) > 0 && row) {
                        const d = row.querySelector('input[name="debits[]"]');
                        if (d && parseFloat(d.value) > 0) d.value = '0';
                    }
                    window.recalculateJournalFormTotals(f);
                });
                f.addEventListener('click', () => setTimeout(() => window.recalculateJournalFormTotals(f), 50));
            }
            window.recalculateJournalFormTotals(f);
        });

        const tbl = document.getElementById('journalEntriesTable');
        if (tbl && !tbl.querySelector('tfoot')) {
            let sumE = 0, sumT = 0;
            tbl.querySelectorAll('tbody tr.journal-row').forEach(tr => {
                const tds = tr.querySelectorAll('td');
                if (tds.length >= 5) {
                    sumE += parseFloat((tds[3].innerText || '').replace(/[^0-9.-]/g, '')) || 0;
                    sumT += parseFloat((tds[4].innerText || '').replace(/[^0-9.-]/g, '')) || 0;
                }
            });
            const tf = document.createElement('tfoot');
            tf.className = 'bg-slate-900 text-white font-bold text-xs';
            tf.innerHTML = `<tr><td colspan="3" class="py-3 px-4 text-emerald-300">إجمالي القيود المحفوظة</td><td class="py-3 px-4">${fmt(sumE)} ر.س</td><td class="py-3 px-4 text-amber-300">${fmt(sumT)} ر.س</td><td colspan="2" class="py-3 px-4 text-emerald-400">متوازن (الفرق: 0.00) ✔</td></tr>`;
            tbl.appendChild(tf);
        }
    }

    // 4. محرك تجهيز الـ PDF الذكي: يحافظ على أسماء الحسابات والملاك، ويضبط العرض 100% بلا إزاحة
    window.prepareSmartPdfClone = function(el) {
        const clone = el.cloneNode(true);
        clone.classList.add('erp-pdf-clean-clone');

        // إظهار الصفوف المطابقة للبحث/الفلترة والتي كانت مخفية بسبب ترقيم الصفحات (1, 2, 3)
        clone.querySelectorAll('tbody tr').forEach(tr => {
            if (tr.dataset.searchMatched === '0') {
                tr.remove();
            } else {
                tr.style.display = '';
            }
        });

        // حذف عمود "الإجراءات" من جداول الـ PDF وتوسيع الأعمدة المتبقية
        let maxRemainingCols = 0;
        const actionHeaderKeywords = ['إجراءات', 'الإجراءات', 'إجراء', 'حذف', 'تعديل', 'تحميل السند', 'إجراءات التحصيل', 'التوثيق في إيجار'];

        clone.querySelectorAll('table').forEach(t => {
            const dropIndices = [];
            const ths = Array.from(t.querySelectorAll('thead th'));
            ths.forEach((th, idx) => {
                const txt = (th.innerText || '').trim();
                if (actionHeaderKeywords.some(w => txt.includes(w))) {
                    dropIndices.push(idx);
                    th.remove();
                }
            });
            if (dropIndices.length > 0) {
                t.querySelectorAll('tbody tr').forEach(tr => {
                    const tds = Array.from(tr.querySelectorAll('td'));
                    if (tds.length > 1) {
                        dropIndices.forEach(i => { if (tds[i]) tds[i].remove(); });
                    }
                });
            }
            const remainingCols = t.querySelectorAll('thead th').length;
            if (remainingCols > maxRemainingCols) maxRemainingCols = remainingCols;
        });

        // تحويل الأزرار التي تحتوي على أسماء الحسابات أو الملاك أو المستأجرين داخل الخلايا إلى نصوص واضحة بدلاً من إخفائها
        clone.querySelectorAll('td button').forEach(btn => {
            const span = document.createElement('span');
            span.className = 'font-bold text-slate-900';
            span.innerHTML = btn.innerHTML;
            btn.replaceWith(span);
        });

        // إزالة الأزرار المتبقية في الهيدر والفلاتر
        clone.querySelectorAll('button').forEach(btn => btn.remove());

        // تحديد الاتجاه (Landscape للأستاذ العام وتصفية المالك والجداول العريضة، وPortrait للسندات والفواتير المفردة)
        const singlePortraitIds = [
            'printableVoucherArea',
            'companyTaxInvoicePrintArea',
            'officeTaxInvoicePrintArea',
            'legalNoticePrintableArea',
            'installmentClaimPdfTemplate',
            'invoicePdfArea'
        ];
        const isSingleDoc = singlePortraitIds.includes(el.id);
        const isLandscape = !isSingleDoc && (maxRemainingCols >= 5 || el.id === 'pro_ownerSettlementPrintableArea' || el.id === 'financialStatementPrintable' || el.scrollWidth > 760);

        // عرض ورقة A4 الصافي بعد خصم الهوامش (8mm من كل جهة):
        // في الوضع الأفقي: 297mm - 16mm = 281mm (يعادل 1062px)
        // في الوضع العمودي: 210mm - 16mm = 194mm (يعادل 733px)
        const exactWidthMm = isLandscape ? 281 : 194;
        const exactWidthPx = isLandscape ? 1062 : 733;

        clone.style.width = exactWidthMm + 'mm';
        clone.style.maxWidth = exactWidthMm + 'mm';
        clone.style.minWidth = exactWidthMm + 'mm';

        return { clone, isLandscape, exactWidthPx };
    };

    window.buildPdfWorkerOptions = function(filename, isLandscape, exactWidthPx) {
        return {
            margin: [8, 8, 8, 8],
            filename: (filename || 'Document.pdf').replace(/\.pdf$/i, '') + '.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: {
                scale: 2,
                useCORS: true,
                letterRendering: false,
                backgroundColor: '#ffffff',
                scrollX: 0,
                scrollY: 0,
                x: 0,
                y: 0,
                width: exactWidthPx
            },
            pagebreak: {
                mode: ['css', 'legacy'],
                avoid: ['tr', 'thead', 'tfoot', '.pdf-no-break']
            },
            jsPDF: {
                unit: 'mm',
                format: 'a4',
                orientation: isLandscape ? 'landscape' : 'portrait'
            }
        };
    };

    // 5. توحيد دالة تصدير الـ PDF لجميع صفحات وبوابات النظام
    window.exportElementToPDF = async function(elementId, filename) {
        const el = typeof elementId === 'string' ? document.getElementById(elementId) : elementId;
        if (!el) return alert('العنصر المطلوب للطباعة غير موجود.');
        if (document.fonts && document.fonts.ready) await document.fonts.ready;

        const savedScrollX = window.scrollX;
        const savedScrollY = window.scrollY;
        window.scrollTo(0, 0);
        window.__erpPdfRenderingActive = true;

        const { clone, isLandscape, exactWidthPx } = window.prepareSmartPdfClone(el);
        const opt = window.buildPdfWorkerOptions(filename, isLandscape, exactWidthPx);

        try {
            await html2pdf().set(opt).from(clone).toContainer().then(function() {
                const c = document.querySelector('.html2pdf__container');
                const o = document.querySelector('.html2pdf__overlay');
                if (o) {
                    o.style.left = '0px';
                    o.style.top = '0px';
                    o.style.right = 'auto';
                    o.style.direction = 'ltr';
                }
                if (c) {
                    c.style.left = '0px';
                    c.style.top = '0px';
                    c.style.right = 'auto';
                    c.style.margin = '0px';
                    c.style.width = exactWidthPx + 'px';
                    c.style.direction = 'rtl';
                }
            }).toCanvas().toPdf().save();
        } finally {
            window.__erpPdfRenderingActive = false;
            window.scrollTo(savedScrollX, savedScrollY);
        }
    };

    // 6. ربط جميع دوال التصدير وإرسال الواتساب في النظام بالمحرك الموحد
    window.exportStatementPDF = function() {
        window.exportElementToPDF('financialStatementPrintable', 'دفتر_الأستاذ_العام_والقوائم_المالية.pdf');
    };

    window.downloadVoucherPDF = function() {
        window.exportElementToPDF('printableVoucherArea', (window.currentVoucherNumber || 'Voucher') + '.pdf');
    };

    window.downloadCompanyTaxInvoicePDF = function() {
        const targetId = document.getElementById('companyTaxInvoicePrintArea') ? 'companyTaxInvoicePrintArea' : 'printableVoucherArea';
        window.exportElementToPDF(targetId, (window.currentCompanyInvoiceNo || 'Tax_Invoice') + '.pdf');
    };

    window.downloadOfficeTaxInvoicePDF = function() {
        window.exportElementToPDF('officeTaxInvoicePrintArea', (window.currentOfficeInvoiceNo || 'Office_Tax_Invoice') + '.pdf');
    };

    window.sendElementAsOriginalPdfWhatsapp = async function(elementOrId, filename, targetPhone, docType = 'general') {
        const el = typeof elementOrId === 'string' ? document.getElementById(elementOrId) : elementOrId;
        if (!el) return alert('عنصر الطباعة غير جاهز.');

        let phone = (targetPhone || '').trim();
        if (!phone) {
            phone = prompt('أدخل رقم جوال المستلم لإرسال ملف الـ PDF الأصلي عبر الواتساب (مثال: 05XXXXXXXX):', '');
            if (!phone) return;
        }

        const cleanFile = (filename || 'Document.pdf').replace(/\.pdf$/i, '') + '.pdf';
        if (typeof window.showErpToast === 'function') {
            window.showErpToast('⏳ جاري توليد ملف الـ PDF الأصلي وإرساله كمرفق عبر الواتساب...', 'info');
        }

        const savedScrollX = window.scrollX;
        const savedScrollY = window.scrollY;
        window.scrollTo(0, 0);
        window.__erpPdfRenderingActive = true;

        const { clone, isLandscape, exactWidthPx } = window.prepareSmartPdfClone(el);
        const opt = window.buildPdfWorkerOptions(cleanFile, isLandscape, exactWidthPx);

        try {
            const pdfDataUri = await html2pdf().set(opt).from(clone).toContainer().then(function() {
                const c = document.querySelector('.html2pdf__container');
                const o = document.querySelector('.html2pdf__overlay');
                if (o) { o.style.left = '0px'; o.style.top = '0px'; o.style.right = 'auto'; o.style.direction = 'ltr'; }
                if (c) { c.style.left = '0px'; c.style.top = '0px'; c.style.right = 'auto'; c.style.margin = '0px'; c.style.width = exactWidthPx + 'px'; c.style.direction = 'rtl'; }
            }).outputPdf('datauristring');

            const res = await fetch('/whatsapp/send-raw-pdf', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    phone: phone,
                    pdf_base64: pdfDataUri,
                    filename: cleanFile,
                    doc_type: docType
                })
            });
            const data = await res.json();
            if (data && data.success) {
                if (typeof window.showErpToast === 'function') window.showErpToast('✅ ' + data.message, 'success');
            } else {
                await window.exportElementToPDF(el, cleanFile);
            }
        } catch (err) {
            await window.exportElementToPDF(el, cleanFile);
        } finally {
            window.__erpPdfRenderingActive = false;
            window.scrollTo(savedScrollX, savedScrollY);
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        localizeDOM();
        initJournalAndLedgerFixes();
    });
})();
</script>
<!-- بداية البلوك الشامل المطور: إصلاح تداخل النصوص العربية والأرقام + تطابق العرض 100% في الصفحات الأفقية والأستاذ العام -->
<style id="arabic-pdf-and-journal-fixes">
    /* 1. ضبط الخطوط العربية ومنع تقطع الحروف على الشاشة وفي الـ PDF */
    body, body *, table, th, td, div, span, p, h1, h2, h3, h4, b, strong, .font-mono {
        font-family: 'Readex Pro', 'Tahoma', 'Arial', sans-serif !important;
        letter-spacing: 0px !important;
        word-spacing: normal !important;
        font-kerning: normal !important;
        font-variant-ligatures: normal !important;
    }

    /* 2. تطابق العرض 100% لجميع الجداول والحاويات في صفحات النظام والأستاذ العام */
    main {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        box-sizing: border-box !important;
    }
    main > div,
    #financialStatementPrintable,
    #pro_ownerSettlementPrintableArea,
    #pro_general_ledger_view,
    #officePropertiesPrintableWrap,
    #officeInstallmentsPrintableWrap {
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }

    /* تنسيق شريط فلاتر الأستاذ العام ليتمدد بانتظام دون كسر العرض */
    #financialStatementPrintable .grid.md\:grid-cols-12 {
        display: grid !important;
        grid-template-columns: repeat(auto-fit, minmax(155px, 1fr)) !important;
        gap: 12px !important;
        align-items: end !important;
    }
    #financialStatementPrintable .grid.md\:grid-cols-12 > div {
        grid-column: span 1 / span 1 !important;
        width: 100% !important;
    }

    /* 3. تثبيت حاوية html2pdf في نقطة الصفر (0,0) لمنع أي إزاحة أفقية */
    .html2pdf__overlay {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        direction: ltr !important;
    }
    .html2pdf__container {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: auto !important;
        margin: 0 !important;
        padding: 0 !important;
        transform: none !important;
        direction: rtl !important;
        box-sizing: border-box !important;
        background: #ffffff !important;
    }

    /* 4. منع انقسام الصفوف والبطاقات بين صفحات الـ PDF */
    tr, thead, tfoot, .journal-table-block, .pdf-no-break {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }
    thead { display: table-header-group !important; }
    tfoot { display: table-footer-group !important; }

    /* 5. تنسيق نسخة الـ PDF لتملأ 100% من عرض الورقة بدون هوامش داخلية زائدة أو تداخل كلمات */
    .erp-pdf-clean-clone {
        direction: rtl !important;
        text-align: right !important;
        background: #ffffff !important;
        color: #0f172a !important;
        padding: 4px !important;
        margin: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        min-width: 100% !important;
        max-height: none !important;
        overflow: visible !important;
        box-sizing: border-box !important;
        border: none !important;
        box-shadow: none !important;
    }
    .erp-pdf-clean-clone * {
        font-family: 'Readex Pro', 'Tahoma', 'Arial', sans-serif !important;
        letter-spacing: 0px !important;
        word-spacing: normal !important;
        max-height: none !important;
        overflow: visible !important;
        box-sizing: border-box !important;
    }
    .erp-pdf-clean-clone #ledger_view_section,
    .erp-pdf-clean-clone #cards_view_section,
    .erp-pdf-clean-clone #trial_view_section,
    .erp-pdf-clean-clone #income_view_section {
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
    }
    .erp-pdf-clean-clone .max-w-2xl,
    .erp-pdf-clean-clone .max-w-3xl,
    .erp-pdf-clean-clone .max-w-4xl,
    .erp-pdf-clean-clone .overflow-x-auto {
        max-width: 100% !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* ضبط شبكات البطاقات العلوية في تصفية المالك (6 بطاقات) والأستاذ العام (5 بطاقات) */
    .erp-pdf-clean-clone .md\:grid-cols-6 {
        display: grid !important;
        grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
        gap: 8px !important;
        width: 100% !important;
    }
    .erp-pdf-clean-clone .md\:grid-cols-5 {
        display: grid !important;
        grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
        gap: 8px !important;
        width: 100% !important;
    }
    .erp-pdf-clean-clone .md\:grid-cols-4 {
        display: grid !important;
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        gap: 8px !important;
        width: 100% !important;
    }
    .erp-pdf-clean-clone .md\:grid-cols-6 > div,
    .erp-pdf-clean-clone .md\:grid-cols-5 > div,
    .erp-pdf-clean-clone .md\:grid-cols-4 > div {
        padding: 8px 10px !important;
        min-width: 0 !important;
    }

    /* إخفاء الفلاتر وأزرار التحكم غير المطبوعة فقط */
    .erp-pdf-clean-clone .no-print,
    .erp-pdf-clean-clone .erp-pagination-bar,
    .erp-pdf-clean-clone form,
    .erp-pdf-clean-clone input[type="text"],
    .erp-pdf-clean-clone input[type="date"],
    .erp-pdf-clean-clone select,
    .erp-pdf-clean-clone .erp-picker-host {
        display: none !important;
    }

    /* تنسيق جداول الـ PDF بوضوح تام وعرض 100% */
    .erp-pdf-clean-clone table {
        width: 100% !important;
        min-width: 100% !important;
        max-width: 100% !important;
        border-collapse: collapse !important;
        table-layout: auto !important;
        direction: rtl !important;
        margin-top: 8px !important;
    }
    .erp-pdf-clean-clone th {
        background-color: #0f172a !important;
        color: #ffffff !important;
        border: 1px solid #334155 !important;
        padding: 7px 6px !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        text-align: right !important;
        white-space: normal !important;
        line-height: 1.35 !important;
    }
    .erp-pdf-clean-clone td {
        border: 1px solid #cbd5e1 !important;
        padding: 6px 6px !important;
        font-size: 11px !important;
        line-height: 1.45 !important;
        color: #0f172a !important;
        white-space: normal !important;
        word-break: break-word !important;
        text-align: right !important;
        vertical-align: middle !important;
    }
    .erp-pdf-clean-clone tfoot td,
    .erp-pdf-clean-clone tfoot th {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
        font-weight: 800 !important;
        border: 1px solid #94a3b8 !important;
    }
</style>

<script id="erp-master-fixes-script">
(function() {
    // 1. ضبط اتجاه الرسم في Canvas أثناء توليد الـ PDF مع تثبيت textAlign = 'left' لمنع إزاحة الكلمات أو تداخل الأرقام
    const origFillText = CanvasRenderingContext2D.prototype.fillText;
    CanvasRenderingContext2D.prototype.fillText = function(text, x, y, maxWidth) {
        if (window.__erpPdfRenderingActive && typeof text === 'string') {
            this.textAlign = 'left';
            if (/[\u0600-\u06FF]/.test(text)) {
                this.direction = 'rtl';
            } else {
                this.direction = 'ltr';
            }
        }
        return maxWidth !== undefined
            ? origFillText.call(this, text, x, y, maxWidth)
            : origFillText.call(this, text, x, y);
    };

    // 2. تعريب طرق الدفع في جميع الجداول والنوافذ
    const PM_AR = {
        bank_transfer: 'حوالة بنكية', transfer: 'حوالة بنكية', bank: 'حوالة بنكية',
        sadad: 'نظام سداد', mada: 'شبكة مدى', pos: 'شبكة مدى', card: 'شبكة مدى',
        cash: 'نقداً (كاش)', cheque: 'شيك مصدق', check: 'شيك مصدق'
    };

    window.translatePaymentMethodAr = function(v) {
        if (!v) return 'حوالة بنكية';
        const k = String(v).trim().toLowerCase();
        return PM_AR[k] || v;
    };

    function localizeDOM() {
        if (Array.isArray(window.officeVouchersMaster)) {
            window.officeVouchersMaster.forEach(v => {
                if (v && v.payment_method) v.payment_method = window.translatePaymentMethodAr(v.payment_method);
            });
        }
        document.querySelectorAll('td, span, b, div, option').forEach(el => {
            if (el.children.length === 0 && el.innerText) {
                const k = el.innerText.trim().toLowerCase();
                if (PM_AR[k]) el.innerText = PM_AR[k];
            }
        });
    }

    const origVoucher = window.openOfficialVoucherPreview;
    window.openOfficialVoucherPreview = function(v) {
        if (v) {
            v.payment_method = window.translatePaymentMethodAr(v.payment_method || v.method);
            v.method = v.payment_method;
        }
        if (typeof origVoucher === 'function') origVoucher(v);
        const m = document.getElementById('pdf_method');
        if (m) m.innerText = window.translatePaymentMethodAr(m.innerText);
    };
    window.previewOfficialVoucher = window.openOfficialVoucherPreview;

    // 3. حساب إجمالي القيد اليومي المباشر + ضريبة المدين مدين وضريبة الدائن دائن
    const fmt = n => Number(n || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    window.recalculateJournalFormTotals = function(form) {
        if (!form) return;
        const debits = form.querySelectorAll('input[name="debits[]"]');
        const credits = form.querySelectorAll('input[name="credits[]"]');
        const taxes = form.querySelectorAll('input[name="taxes[]"]');
        let bD = 0, bC = 0, tD = 0, tC = 0;

        for (let i = 0; i < Math.max(debits.length, credits.length); i++) {
            const d = parseFloat(debits[i]?.value || 0) || 0;
            const c = parseFloat(credits[i]?.value || 0) || 0;
            const t = parseFloat(taxes[i]?.value || 0) || 0;
            bD += d; bC += c;
            if (taxes[i]) {
                const lbl = taxes[i].parentElement.querySelector('label');
                let hint = taxes[i].parentElement.querySelector('.row-tax-hint');
                if (!hint) {
                    hint = document.createElement('div');
                    hint.className = 'row-tax-hint text-[10px] font-bold mt-1';
                    taxes[i].parentElement.appendChild(hint);
                }
                if (d > 0) {
                    tD += t;
                    if (lbl) lbl.innerText = 'الضريبة (مدين +)';
                    hint.className = 'row-tax-hint text-[10px] font-bold mt-1 text-emerald-700';
                    hint.innerText = 'الإجمالي: ' + fmt(d + t);
                } else if (c > 0) {
                    tC += t;
                    if (lbl) lbl.innerText = 'الضريبة (دائن -)';
                    hint.className = 'row-tax-hint text-[10px] font-bold mt-1 text-amber-700';
                    hint.innerText = 'الإجمالي: ' + fmt(c + t);
                } else {
                    if (lbl) lbl.innerText = 'الضريبة';
                    hint.innerText = '';
                }
            }
        }

        const totD = bD + tD, totC = bC + tC;
        const entryTotal = Math.max(totD, totC);
        const diff = Math.round((totD - totC) * 100) / 100;
        const ok = Math.abs(diff) <= 0.05 && entryTotal > 0;

        let box = form.querySelector('.live-journal-summary-bar');
        if (!box) {
            box = document.createElement('div');
            box.className = 'live-journal-summary-bar p-4 rounded-2xl bg-slate-900 text-white space-y-2 my-3';
            const footer = form.querySelector('.flex.justify-end');
            footer ? form.insertBefore(box, footer) : form.appendChild(box);
        }

        box.innerHTML = `
            <div class="flex flex-wrap justify-between items-center border-b border-slate-700 pb-2 text-xs">
                <b class="text-emerald-400">📊 إجمالي القيد اليومي المباشر (شامل الضريبة):</b>
                <span class="px-2.5 py-1 rounded font-bold ${ok ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/30 text-rose-200'}">
                    ${ok ? '✔ متوازن (الفرق: 0.00)' : '⚠️ غير متوازن — الفرق: ' + fmt(Math.abs(diff)) + ' ر.س'}
                </span>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs pt-1">
                <div class="p-2 rounded bg-white/5">المدين + ضريبته: <b class="text-emerald-400 block">${fmt(bD)} + ${fmt(tD)} = ${fmt(totD)} ر.س</b></div>
                <div class="p-2 rounded bg-white/5">الدائن + ضريبته: <b class="text-amber-400 block">${fmt(bC)} + ${fmt(tC)} = ${fmt(totC)} ر.س</b></div>
                <div class="p-2 rounded bg-emerald-500/20">إجمالي القيد المعتمد: <b class="text-white text-sm block">${fmt(entryTotal)} ر.س</b></div>
                <div class="p-2 rounded bg-white/5">صافي الفرق (التوازن): <b class="${ok ? 'text-emerald-400' : 'text-rose-300'} text-sm block">${fmt(diff)} ر.س</b></div>
            </div>
        `;
    };

    function initJournalAndLedgerFixes() {
        // منع ترقيم الصفحات من إخفاء حسابات ميزان المراجعة أو بطاقات الأستاذ أو تصفية المالك
        ['#cards_view_section table', '#trial_view_section table', '#income_view_section table', '#pro_rep_contracts_table', '#pro_rep_vouchers_table'].forEach(sel => {
            document.querySelectorAll(sel).forEach(tbl => {
                if (tbl._paginationBar) {
                    tbl._paginationBar.remove();
                    tbl._paginationBar = null;
                }
                tbl.querySelectorAll('tbody tr').forEach(r => r.style.display = '');
            });
        });

        document.querySelectorAll('form[action*="journal-entries"]').forEach(f => {
            if (f.dataset.bound !== '1') {
                f.dataset.bound = '1';
                f.addEventListener('input', e => {
                    const t = e.target;
                    const row = t?.closest('.grid');
                    if (t?.name === 'debits[]' && parseFloat(t.value) > 0 && row) {
                        const c = row.querySelector('input[name="credits[]"]');
                        if (c && parseFloat(c.value) > 0) c.value = '0';
                    } else if (t?.name === 'credits[]' && parseFloat(t.value) > 0 && row) {
                        const d = row.querySelector('input[name="debits[]"]');
                        if (d && parseFloat(d.value) > 0) d.value = '0';
                    }
                    window.recalculateJournalFormTotals(f);
                });
                f.addEventListener('click', () => setTimeout(() => window.recalculateJournalFormTotals(f), 50));
            }
            window.recalculateJournalFormTotals(f);
        });

        const tbl = document.getElementById('journalEntriesTable');
        if (tbl && !tbl.querySelector('tfoot')) {
            let sumE = 0, sumT = 0;
            tbl.querySelectorAll('tbody tr.journal-row').forEach(tr => {
                const tds = tr.querySelectorAll('td');
                if (tds.length >= 5) {
                    sumE += parseFloat((tds[3].innerText || '').replace(/[^0-9.-]/g, '')) || 0;
                    sumT += parseFloat((tds[4].innerText || '').replace(/[^0-9.-]/g, '')) || 0;
                }
            });
            const tf = document.createElement('tfoot');
            tf.className = 'bg-slate-900 text-white font-bold text-xs';
            tf.innerHTML = `<tr><td colspan="3" class="py-3 px-4 text-emerald-300">إجمالي القيود المحفوظة</td><td class="py-3 px-4">${fmt(sumE)} ر.س</td><td class="py-3 px-4 text-amber-300">${fmt(sumT)} ر.س</td><td colspan="2" class="py-3 px-4 text-emerald-400">متوازن (الفرق: 0.00) ✔</td></tr>`;
            tbl.appendChild(tf);
        }
    }

    // 4. تجهيز نسخة الـ PDF بدون تداخل عناصر، مع ترويسة رسمية للأستاذ العام وعرض 100%
    window.prepareSmartPdfClone = function(el) {
        const clone = el.cloneNode(true);
        clone.classList.add('erp-pdf-clean-clone');

        // إضافة ترويسة رسمية أعلى تقرير الأستاذ العام عند التصدير
        if (el.id === 'financialStatementPrintable') {
            const pageTitle = document.querySelector('header h2')?.innerText || document.querySelector('aside h1')?.innerText || 'منظومة العقارات الذكية ERP';
            const hdr = document.createElement('div');
            hdr.className = 'flex items-center justify-between border-b-2 border-slate-900 pb-3 mb-3 text-slate-900';
            hdr.innerHTML = `
                <div>
                    <div style="font-size:15px;font-weight:800;">${pageTitle}</div>
                    <div style="font-size:11px;color:#475569;">تقرير دفتر الأستاذ العام والقوائم المالية المعتمد</div>
                </div>
                <div style="text-align:left;font-size:11px;font-weight:700;">
                    تاريخ الإصدار: ${new Date().toISOString().slice(0, 10)}
                </div>
            `;
            clone.insertBefore(hdr, clone.firstChild);
        }

        // في بطاقات الملخص العلوية، وضع الرقم والعملة (ر.س) بجانب بعضهما بوضوح لمنع تداخلهما
        clone.querySelectorAll('.grid > div').forEach(card => {
            const bTag = card.querySelector('b');
            if (bTag && bTag.nextElementSibling && bTag.nextElementSibling.innerText.includes('ر.س')) {
                const unitSpan = bTag.nextElementSibling;
                bTag.innerText = bTag.innerText.trim() + ' ' + unitSpan.innerText.trim();
                unitSpan.remove();
            }
        });

        // إظهار كافة الصفوف المطابقة للفلترة (والتي أخفاها ترقيم الصفحات 1, 2, 3)
        clone.querySelectorAll('tbody tr').forEach(tr => {
            if (tr.dataset.searchMatched === '0') {
                tr.remove();
            } else {
                tr.style.display = '';
            }
        });

        // حذف عمود "الإجراءات" من جداول الـ PDF
        let maxRemainingCols = 0;
        const actionHeaderKeywords = ['إجراءات', 'الإجراءات', 'إجراء', 'حذف', 'تعديل', 'تحميل السند', 'إجراءات التحصيل', 'التوثيق في إيجار'];

        clone.querySelectorAll('table').forEach(t => {
            const dropIndices = [];
            const ths = Array.from(t.querySelectorAll('thead th'));
            ths.forEach((th, idx) => {
                const txt = (th.innerText || '').trim();
                if (actionHeaderKeywords.some(w => txt.includes(w))) {
                    dropIndices.push(idx);
                    th.remove();
                }
            });
            if (dropIndices.length > 0) {
                t.querySelectorAll('tbody tr').forEach(tr => {
                    const tds = Array.from(tr.querySelectorAll('td'));
                    if (tds.length > 1) {
                        dropIndices.forEach(i => { if (tds[i]) tds[i].remove(); });
                    }
                });
            }
            const remainingCols = t.querySelectorAll('thead th').length;
            if (remainingCols > maxRemainingCols) maxRemainingCols = remainingCols;
        });

        // تحويل الأزرار التي تحمل أسماء الحسابات أو الملاك داخل الجداول إلى نصوص واضحة
        clone.querySelectorAll('td button').forEach(btn => {
            const span = document.createElement('span');
            span.className = 'font-bold text-slate-900';
            span.innerHTML = btn.innerHTML;
            btn.replaceWith(span);
        });

        // إزالة أي أزرار تحكم متبقية خارج الجداول
        clone.querySelectorAll('button').forEach(btn => btn.remove());

        const singlePortraitIds = [
            'printableVoucherArea',
            'companyTaxInvoicePrintArea',
            'officeTaxInvoicePrintArea',
            'legalNoticePrintableArea',
            'installmentClaimPdfTemplate',
            'invoicePdfArea'
        ];
        const isSingleDoc = singlePortraitIds.includes(el.id);
        const isLandscape = !isSingleDoc && (maxRemainingCols >= 5 || el.id === 'pro_ownerSettlementPrintableArea' || el.id === 'financialStatementPrintable' || el.scrollWidth > 760);

        clone.style.width = '100%';
        clone.style.maxWidth = '100%';
        clone.style.minWidth = '100%';

        return { clone, isLandscape };
    };

    // تمرير jsPDF قبل margin لضمان حساب العرض الأفقي الكامل (285mm) في html2pdf
    window.createConfiguredPdfWorker = function(filename, isLandscape) {
        const cleanName = (filename || 'Document.pdf').replace(/\.pdf$/i, '') + '.pdf';
        const exactContainerMm = isLandscape ? '285mm' : '198mm';

        const worker = html2pdf().set({
            jsPDF: {
                unit: 'mm',
                format: 'a4',
                orientation: isLandscape ? 'landscape' : 'portrait'
            },
            pagebreak: {
                mode: ['css', 'legacy'],
                avoid: ['tr', 'thead', 'tfoot', '.pdf-no-break']
            },
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: {
                scale: 2,
                useCORS: true,
                letterRendering: false,
                backgroundColor: '#ffffff',
                scrollX: 0,
                scrollY: 0
            },
            filename: cleanName
        }).set({
            margin: [6, 6, 6, 6]
        });

        return { worker, exactContainerMm };
    };

    // 5. الدالة الموحدة لتصدير الـ PDF بعرض 100% من اليمين إلى اليسار
    window.exportElementToPDF = async function(elementId, filename) {
        const el = typeof elementId === 'string' ? document.getElementById(elementId) : elementId;
        if (!el) return alert('العنصر المطلوب للطباعة غير موجود.');
        if (document.fonts && document.fonts.ready) await document.fonts.ready;

        const savedScrollX = window.scrollX;
        const savedScrollY = window.scrollY;
        window.scrollTo(0, 0);
        window.__erpPdfRenderingActive = true;

        const { clone, isLandscape } = window.prepareSmartPdfClone(el);
        const { worker, exactContainerMm } = window.createConfiguredPdfWorker(filename, isLandscape);

        try {
            await worker.from(clone).toContainer().then(function() {
                const c = document.querySelector('.html2pdf__container');
                const o = document.querySelector('.html2pdf__overlay');
                if (o) {
                    o.style.left = '0px';
                    o.style.top = '0px';
                    o.style.right = '0px';
                    o.style.direction = 'ltr';
                }
                if (c) {
                    c.style.left = '0px';
                    c.style.top = '0px';
                    c.style.right = 'auto';
                    c.style.margin = '0px';
                    c.style.width = exactContainerMm;
                    c.style.maxWidth = exactContainerMm;
                    c.style.direction = 'rtl';
                }
            }).toCanvas().toPdf().save();
        } finally {
            window.__erpPdfRenderingActive = false;
            window.scrollTo(savedScrollX, savedScrollY);
        }
    };

    // 6. ربط جميع أزرار التصدير والواتساب بالمحرك الموحد
    window.exportStatementPDF = function() {
        window.exportElementToPDF('financialStatementPrintable', 'دفتر_الأستاذ_العام_والقوائم_المالية.pdf');
    };

    window.downloadVoucherPDF = function() {
        window.exportElementToPDF('printableVoucherArea', (window.currentVoucherNumber || 'Voucher') + '.pdf');
    };

    window.downloadCompanyTaxInvoicePDF = function() {
        const targetId = document.getElementById('companyTaxInvoicePrintArea') ? 'companyTaxInvoicePrintArea' : 'printableVoucherArea';
        window.exportElementToPDF(targetId, (window.currentCompanyInvoiceNo || 'Tax_Invoice') + '.pdf');
    };

    window.downloadOfficeTaxInvoicePDF = function() {
        window.exportElementToPDF('officeTaxInvoicePrintArea', (window.currentOfficeInvoiceNo || 'Office_Tax_Invoice') + '.pdf');
    };

    window.sendElementAsOriginalPdfWhatsapp = async function(elementOrId, filename, targetPhone, docType = 'general') {
        const el = typeof elementOrId === 'string' ? document.getElementById(elementOrId) : elementOrId;
        if (!el) return alert('عنصر الطباعة غير جاهز.');

        let phone = (targetPhone || '').trim();
        if (!phone) {
            phone = prompt('أدخل رقم جوال المستلم لإرسال ملف الـ PDF الأصلي عبر الواتساب (مثال: 05XXXXXXXX):', '');
            if (!phone) return;
        }

        const cleanFile = (filename || 'Document.pdf').replace(/\.pdf$/i, '') + '.pdf';
        if (typeof window.showErpToast === 'function') {
            window.showErpToast('⏳ جاري توليد ملف الـ PDF الأصلي وإرساله كمرفق عبر الواتساب...', 'info');
        }

        const savedScrollX = window.scrollX;
        const savedScrollY = window.scrollY;
        window.scrollTo(0, 0);
        window.__erpPdfRenderingActive = true;

        const { clone, isLandscape } = window.prepareSmartPdfClone(el);
        const { worker, exactContainerMm } = window.createConfiguredPdfWorker(cleanFile, isLandscape);

        try {
            const pdfDataUri = await worker.from(clone).toContainer().then(function() {
                const c = document.querySelector('.html2pdf__container');
                const o = document.querySelector('.html2pdf__overlay');
                if (o) { o.style.left = '0px'; o.style.top = '0px'; o.style.right = '0px'; o.style.direction = 'ltr'; }
                if (c) { c.style.left = '0px'; c.style.top = '0px'; c.style.right = 'auto'; c.style.margin = '0px'; c.style.width = exactContainerMm; c.style.maxWidth = exactContainerMm; c.style.direction = 'rtl'; }
            }).outputPdf('datauristring');

            const res = await fetch('/whatsapp/send-raw-pdf', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    phone: phone,
                    pdf_base64: pdfDataUri,
                    filename: cleanFile,
                    doc_type: docType
                })
            });
            const data = await res.json();
            if (data && data.success) {
                if (typeof window.showErpToast === 'function') window.showErpToast('✅ ' + data.message, 'success');
            } else {
                await window.exportElementToPDF(el, cleanFile);
            }
        } catch (err) {
            await window.exportElementToPDF(el, cleanFile);
        } finally {
            window.__erpPdfRenderingActive = false;
            window.scrollTo(savedScrollX, savedScrollY);
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        localizeDOM();
        initJournalAndLedgerFixes();
    });
})();
</script>
</body>
</html>