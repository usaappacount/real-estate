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
                                            <form action="{{ route('owner.family.delete', $fm->id) }}" method="POST" onsubmit="return confirm('إلغاء حساب فرد العائلة؟');">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-red-50 text-red-600 text-xs font-bold">حذف</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="py-8 text-center text-slate-400">لم تقم بإضافة أي فرد من العائلة أو وكيل حتى الآن.</td></tr>
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

</body>
</html>