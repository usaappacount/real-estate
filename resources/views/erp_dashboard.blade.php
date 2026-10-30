<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>منظومة العقارات الذكية | لوحة الإدارة المركزية</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Readex+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Readex Pro', sans-serif; background-color: #f8fafc; }
        .glass-sidebar { background: linear-gradient(180deg, #0f172a 0%, #090d16 100%); }
    </style>
</head>
<body class="text-slate-800 flex min-h-screen">

    <!-- القائمة الجانبية العصرية -->
    <aside class="w-72 glass-sidebar text-white flex flex-col shrink-0 shadow-2xl sticky top-0 h-screen">
        <!-- الهوية -->
        <div class="p-6 border-b border-slate-800/80 flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center shadow-lg shadow-emerald-500/20 shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <div>
                <h1 class="font-bold text-base tracking-tight text-white">نظام ادارة العقارات</h1>
            </div>
        </div>

        <!-- روابط التنقل -->
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

            <div class="px-3 pt-5 pb-2 text-[11px] font-bold text-slate-500 uppercase tracking-wider">الأمان والحساب</div>

            <a href="{{ route('erp.dashboard', ['tab' => 'settings']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'settings' ? 'bg-gradient-to-l from-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-600/25' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                <span>⚙️</span>
                <span>بيانات الدخول والواتساب</span>
            </a>
        </nav>

        <!-- معلومات المستخدم وتسجيل الخروج -->
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
        <!-- الشريط العلوي -->
        <header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200/80 px-8 flex items-center justify-between sticky top-0 z-20">
            <div class="flex items-center gap-3">
                <span class="px-3.5 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    متصل كـ: المدير العام ({{ auth()->user()->username }})
                </span>
            </div>
        </header>

        <!-- مساحة العمل -->
        <main class="p-8 space-y-6">

            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center justify-between shadow-sm">
                    <span>✅ {{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-700 text-sm font-semibold shadow-sm">
                    ⚠️ {{ $errors->first() }}
                </div>
            @endif

            <!-- 1. النظرة العامة -->
            @if($tab == 'overview')
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-slate-500">مكاتب العقار </span>
                            <span class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">🏢</span>
                        </div>
                        <div class="text-3xl font-bold text-slate-900">{{ $stats['offices_count'] }}</div>
                        <p class="text-xs text-emerald-600 mt-2 font-medium"> مكتب نشط حالياً</p>
                    </div>

                    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-slate-500">تصنيفات العقارات المعرّفة</span>
                            <span class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">🏗️</span>
                        </div>
                        <div class="text-3xl font-bold text-slate-900">{{ $stats['property_types_count'] }}</div>
                        <p class="text-xs text-slate-400 mt-2">سكني، تجاري، زراعي، صناعي</p>
                    </div>

                    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-slate-500">إجمالي سندات القبض</span>
                            <span class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold">📥</span>
                        </div>
                        <div class="text-2xl font-bold text-emerald-600">{{ number_format($stats['total_receipts'], 2) }} <span class="text-xs">ر.س</span></div>
                        <p class="text-xs text-slate-400 mt-2">المقبوضات المرحلة للشجرة</p>
                    </div>

                    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-slate-500">إجمالي سندات الصرف</span>
                            <span class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">📤</span>
                        </div>
                        <div class="text-2xl font-bold text-slate-900">{{ number_format($stats['total_payments'], 2) }} <span class="text-xs">ر.س</span></div>
                        <p class="text-xs text-slate-400 mt-2">المصروفات والتحويلات</p>
                    </div>
                </div>

                <!-- جدول سريع للمكاتب في الرئيسية -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900">أحدث مكاتب العقار </h3>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-right">
                            <thead class="bg-slate-50 text-slate-600 text-xs uppercase border-b border-slate-200/60">
                                <tr>
                                    <th class="py-3.5 px-6">اسم المكتب</th>
                                    <th class="py-3.5 px-6">رقم الجوال</th>
                                    <th class="py-3.5 px-6">اسم المستخدم</th>
                                    <th class="py-3.5 px-6">نوع الاتفاق</th>
                                    <th class="py-3.5 px-6">الاشتراك السنوي</th>
                                    <th class="py-3.5 px-6">نسبة الشركة</th>
                                    <th class="py-3.5 px-6">الحالة</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($offices as $office)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-4 px-6 font-bold text-slate-900">{{ $office->name }}</td>
                                        <td class="py-4 px-6 font-mono text-xs text-emerald-700 font-semibold" dir="ltr">{{ $office->phone }}</td>
                                        <td class="py-4 px-6 font-mono text-xs">{{ $office->manager_username ?? '-' }}</td>
                                        <td class="py-4 px-6">
                                            @if($office->billing_type == 'annual_subscription')
                                                <span class="px-2.5 py-1 rounded-xl text-xs font-semibold bg-blue-50 text-blue-700">اشتراك سنوي</span>
                                            @elseif($office->billing_type == 'per_property_percentage')
                                                <span class="px-2.5 py-1 rounded-xl text-xs font-semibold bg-amber-50 text-amber-700">نسبة لكل عقار</span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-xl text-xs font-semibold bg-emerald-50 text-emerald-700">مدمج (اشتراك + نسبة)</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 font-semibold">{{ number_format($office->annual_fee, 2) }} ر.س</td>
                                        <td class="py-4 px-6 font-semibold text-amber-700">{{ $office->company_commission_rate }}%</td>
                                        <td class="py-4 px-6">
                                            @if($office->status == 'active')
                                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">نشط</span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">موقوف</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="py-8 text-center text-slate-400">لا توجد مكاتب مضافة بعد.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- 2. إدارة وتعديل مكاتب العقار والاتفاقيات المالية -->
            @if($tab == 'offices')
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    
                    <!-- نموذج إضافة مكتب جديد (بدون إيميل + جوال 10 أرقام) -->
                    <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
                        <h3 class="font-bold text-base text-slate-900 mb-1">تسجيل مكتب عقار جديد</h3>
                        <p class="text-xs text-slate-500 mb-5">يتم الدخول برقم الجوال (10 أرقام) مع اسم مستخدم احتياطي</p>

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
                                           class="w-full rounded-2xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:border-emerald-600">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">رخصة فال</label>
                                    <input type="text" name="fal_license" placeholder="12000XXXXX"
                                           class="w-full rounded-2xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:border-emerald-600">
                                </div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                                <div class="text-xs font-bold text-emerald-700">بيانات الدخول للمكتب (بدون إيميل)</div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">رقم الجوال (10 أرقام يبدأ بـ 05) *</label>
                                    <input type="tel" name="phone" maxlength="10" required placeholder="05XXXXXXXX" dir="ltr"
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                                           class="w-full text-left font-mono rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:outline-none focus:border-emerald-600">
                                    <span class="text-[11px] text-slate-400">سيُحذف الـ 0 ويتحول تلقائياً إلى 966+ لإرسال كود الواتساب</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2.5">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">اسم المستخدم الاحتياطي *</label>
                                        <input type="text" name="username" required placeholder="rawafed_office"
                                               class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:border-emerald-600">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">كلمة المرور الاحتياطية *</label>
                                        <input type="text" name="password" value="123456" required
                                               class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:border-emerald-600">
                                    </div>
                                </div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-emerald-50/50 border border-emerald-200/70 space-y-3">
                                <div class="text-xs font-bold text-slate-800">الاتفاقية المالية مع صاحب المكتب</div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">نوع الاتفاق المالي *</label>
                                    <select name="billing_type" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white">
                                        <option value="annual_subscription">اشتراك سنوي مقطوع</option>
                                        <option value="per_property_percentage">نسبة من قيمة كل عقار / عقد</option>
                                        <option value="hybrid">مدمج (اشتراك سنوي + نسبة)</option>
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-2.5">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">الاشتراك السنوي (ر.س)</label>
                                        <input type="number" step="0.01" name="annual_fee" value="0"
                                               class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">نسبة الشركة (%)</label>
                                        <input type="number" step="0.01" name="company_commission_rate" value="2.5"
                                               class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="w-full py-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-semibold text-sm shadow-lg shadow-emerald-600/20 transition">
                                اعتماد المكتب وتفعيل الاتفاقية
                            </button>
                        </form>
                    </div>

                    <!-- قائمة المكاتب مع زر تعديل الحساب والاتفاقية -->
                    <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-100">
                            <h3 class="font-bold text-slate-900">سجل مكاتب العقار وإدارة اتفاقياتهم</h3>
                            <p class="text-xs text-slate-500 mt-0.5">اضغط على "تعديل الاتفاقية والحساب" لتغيير النسبة أو الاشتراك أو رقم الجوال أو كلمة المرور</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-right">
                                <thead class="bg-slate-50 text-slate-600 text-xs border-b border-slate-200/60">
                                    <tr>
                                        <th class="py-3.5 px-4">المكتب والترخيص</th>
                                        <th class="py-3.5 px-4">الجوال واسم المستخدم</th>
                                        <th class="py-3.5 px-4">الاتفاق المالي</th>
                                        <th class="py-3.5 px-4">الاشتراك / النسبة</th>
                                        <th class="py-3.5 px-4">الحالة</th>
                                        <th class="py-3.5 px-4 text-center">إجراء التعديل</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($offices as $office)
                                        @php
                                            $localPhone = \App\Http\Controllers\CompanyErpController::toLocalPhone($office->phone);
                                        @endphp
                                        <tr class="hover:bg-slate-50/80 transition">
                                            <td class="py-4 px-4">
                                                <div class="font-bold text-slate-900">{{ $office->name }}</div>
                                                <div class="text-xs text-slate-400">فال: {{ $office->fal_license ?: 'غير محدد' }}</div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <div class="font-mono text-xs font-bold text-emerald-700" dir="ltr">{{ $office->phone }}</div>
                                                <div class="text-xs text-slate-500">المستخدم: <span class="font-mono">{{ $office->manager_username }}</span></div>
                                            </td>
                                            <td class="py-4 px-4">
                                                @if($office->billing_type == 'annual_subscription')
                                                    <span class="px-2.5 py-1 rounded-xl text-xs font-semibold bg-blue-50 text-blue-700">اشتراك سنوي</span>
                                                @elseif($office->billing_type == 'per_property_percentage')
                                                    <span class="px-2.5 py-1 rounded-xl text-xs font-semibold bg-amber-50 text-amber-700">نسبة لكل عقار</span>
                                                @else
                                                    <span class="px-2.5 py-1 rounded-xl text-xs font-semibold bg-emerald-50 text-emerald-700">مدمج</span>
                                                @endif
                                            </td>
                                            <td class="py-4 px-4">
                                                <div class="text-xs font-bold">{{ number_format($office->annual_fee, 2) }} ر.س</div>
                                                <div class="text-xs text-amber-700 font-semibold">النسبة: {{ $office->company_commission_rate }}%</div>
                                            </td>
                                            <td class="py-4 px-4">
                                                @if($office->status == 'active')
                                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">نشط</span>
                                                @else
                                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">موقوف</span>
                                                @endif
                                            </td>
                                            <td class="py-4 px-4 text-center">
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
                                                            status: '{{ $office->status }}'
                                                        })"
                                                        class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white text-xs font-semibold transition shadow-sm">
                                                    ✏️ تعديل الاتفاقية والحساب
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="py-8 text-center text-slate-400">لا توجد مكاتب مسجلة بعد.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <!-- نافذة (Modal) تعديل حساب المكتب والاتفاقية المالية -->
                <div id="editOfficeModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200">
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
                                <div class="text-xs font-bold text-emerald-700">تعديل بيانات الدخول (الجوال 10 أرقام + اسم المستخدم)</div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">رقم الجوال (10 أرقام) *</label>
                                        <input type="tel" name="phone" id="edit_phone" maxlength="10" required dir="ltr"
                                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                                               class="w-full text-left font-mono rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">اسم المستخدم *</label>
                                        <input type="text" name="username" id="edit_username" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">كلمة مرور جديدة (اتركها فارغة إذا لا تريد تغييرها)</label>
                                    <input type="text" name="password" placeholder="أدخل كلمة مرور جديدة فقط عند الرغبة بالتغيير" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                </div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-amber-50/60 border border-amber-200 space-y-3">
                                <div class="text-xs font-bold text-amber-900">تعديل الاتفاق المالي والاشتراك</div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">نوع الاتفاق المالي *</label>
                                    <select name="billing_type" id="edit_billing_type" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white">
                                        <option value="annual_subscription">اشتراك سنوي مقطوع</option>
                                        <option value="per_property_percentage">نسبة من قيمة كل عقار / عقد</option>
                                        <option value="hybrid">مدمج (اشتراك سنوي + نسبة)</option>
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">قيمة الاشتراك السنوي (ر.س)</label>
                                        <input type="number" step="0.01" name="annual_fee" id="edit_annual_fee" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white">
                                    </div>
                                    <div>
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

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    <div class="lg:col-span-5 space-y-6">
                        
                        <!-- 1. إضافة نوع عقار أو أرض + زر إدارة التصنيفات الرئيسية -->
                        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-bold text-base text-slate-900">1. إضافة نوع عقار أو أرض</h3>
                                <button type="button" onclick="openModal('manageCategoriesModal')"
                                        class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200 transition">
                                    ⚙️ إضافة / تعديل تصنيف رئيسي
                                </button>
                            </div>

                            <form action="{{ route('erp.property_types.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">التصنيف الرئيسي *</label>
                                    <select name="category" required class="w-full rounded-2xl border border-slate-300 px-3.5 py-2.5 text-sm bg-white">
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">مسمى نوع العقار *</label>
                                    <input type="text" name="name" required placeholder="مثال: عمارة سكنية، أرض زراعية، شقة، معرض"
                                           class="w-full rounded-2xl border border-slate-300 px-3.5 py-2.5 text-sm">
                                </div>
                                <label class="flex items-center gap-2 text-xs font-medium text-slate-700 cursor-pointer">
                                    <input type="checkbox" name="has_units" value="1" class="rounded text-emerald-600">
                                    <span>يتفرع منه وحدات داخلية (مثل العمارة تتفرع لشقق ومحلات)</span>
                                </label>
                                <button type="submit" class="w-full py-3 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold transition">
                                    حفظ نوع العقار
                                </button>
                            </form>
                        </div>

                        <!-- 2. إضافة أكثر من متطلب لكل عقار + زر إدارة أنواع الحقول -->
                        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-bold text-base text-slate-900">2. إضافة المتطلبات والحقول لكل عقار</h3>
                                <button type="button" onclick="openModal('manageFieldTypesModal')"
                                        class="px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold border border-blue-200 transition">
                                    ⚙️ أنواع الحقول
                                </button>
                            </div>

                            <form action="{{ route('erp.property_attributes.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">اختر نوع العقار *</label>
                                    <select name="property_type_id" required class="w-full rounded-2xl border border-slate-300 px-3.5 py-2.5 text-sm bg-white">
                                        @foreach($propertyTypes as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }} ({{ $catMap[$type->category] ?? $type->category }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- حاوية إضافة أكثر من متطلب في نفس الوقت -->
                                <div id="dynamicAttributesContainer" class="space-y-3">
                                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2.5 attribute-row">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-emerald-700">بيانات المتطلب</span>
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">اسم المتطلب / الحقل المطلوب من المكتب *</label>
                                            <input type="text" name="attribute_names[]" required placeholder="مثال: المساحة، عدد الأدوار، عرض الشارع، رقم الصك"
                                                   class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">نوع الحقل *</label>
                                            <select name="field_types[]" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white">
                                                @foreach($fieldTypes as $ft)
                                                    <option value="{{ $ft->name }}">{{ $ft->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" onclick="addMoreAttributeRow()"
                                        class="w-full py-2.5 rounded-2xl border-2 border-dashed border-emerald-500 text-emerald-700 hover:bg-emerald-50 text-xs font-bold transition">
                                    + إضافة متطلب آخر لنفس العقار
                                </button>

                                <button type="submit" class="w-full py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold transition shadow-lg shadow-emerald-600/20">
                                    ربط وحفظ المتطلبات بالعقار
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- جدول المتطلبات والحقول الديناميكية المعتمدة (يعرض كافة العقارات بترتيب أنيق) -->
                    <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/40">
                            <div>
                                <h3 class="font-bold text-slate-900 text-base">سجل العقارات والمتطلبات الديناميكية المعتمدة</h3>
                                <p class="text-xs text-slate-500 mt-0.5">جميع أنواع العقارات المسجلة في النظام مع إمكانية استعراض وتعديل متطلبات كل عقار</p>
                            </div>
                            <span class="px-3.5 py-1.5 rounded-2xl text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                إجمالي العقارات: {{ $propertyTypes->count() }}
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-right">
                                <thead class="bg-slate-50 text-slate-600 text-xs border-b border-slate-200/80">
                                    <tr>
                                        <th class="py-3.5 px-4 w-12 text-center">#</th>
                                        <th class="py-3.5 px-5">مسمى العقار</th>
                                        <th class="py-3.5 px-4 text-center">التصنيف الرئيسي</th>
                                        <th class="py-3.5 px-4 text-center">المتطلبات والحقول</th>
                                        <th class="py-3.5 px-5 text-center">إجراءات العقار</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($propertyTypes as $index => $type)
                                        @php
                                            $typeAttrs = $attributes->where('property_type_id', $type->id);
                                            $arabicCat = $catMap[$type->category] ?? $type->category;
                                        @endphp
                                        <tr class="hover:bg-slate-50/90 transition">
                                            <td class="py-4 px-4 text-center font-mono text-xs text-slate-400 font-bold">
                                                {{ $index + 1 }}
                                            </td>
                                            <td class="py-4 px-5">
                                                <div class="font-bold text-slate-900 text-sm">{{ $type->name }}</div>
                                                <div class="mt-1">
                                                    @if($type->has_units)
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[11px] bg-emerald-50 text-emerald-700 font-medium border border-emerald-200/60">
                                                            🏢 يتفرع لوحدات داخلية
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[11px] bg-slate-100 text-slate-500 font-medium">
                                                            📍 عقار مستقل (بدون وحدات)
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/60">
                                                    {{ $arabicCat }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                <button type="button" onclick="openModal('attrsModal_{{ $type->id }}')"
                                                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl {{ $typeAttrs->count() > 0 ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-600 hover:text-white' : 'bg-slate-100 text-slate-600 border-slate-200 hover:bg-slate-800 hover:text-white' }} border text-xs font-bold transition shadow-sm">
                                                    <span>📋 المتطلبات</span>
                                                    <span class="px-2 py-0.5 rounded-lg bg-white/80 text-slate-900 font-mono text-[11px]">{{ $typeAttrs->count() }}</span>
                                                </button>
                                            </td>
                                            <td class="py-4 px-5 text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    <button type="button" onclick="openModal('editPropertyTypeModal_{{ $type->id }}')"
                                                            class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-white text-xs font-semibold transition shadow-sm">
                                                        ✏️ تعديل
                                                    </button>
                                                    <form action="{{ route('erp.property_types.delete', $type->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف العقار ({{ $type->name }}) وكافة متطلباته؟');">
                                                        @csrf
                                                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-red-50 hover:bg-red-600 text-red-600 hover:text-white border border-red-200 text-xs font-semibold transition">
                                                            🗑️ حذف
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-12 text-center text-slate-400">
                                                لا توجد عقارات مضافة بعد. قم بإضافة نوع عقار جديد من النموذج الجانبي.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- نوافذ (Modals) المتطلبات وتعديل العقارات (مفصولة خارج الجدول لضمان عرض كافة الصفوف بانتظام) -->
                @foreach($propertyTypes as $type)
                    @php
                        $typeAttrs = $attributes->where('property_type_id', $type->id);
                        $arabicCat = $catMap[$type->category] ?? $type->category;
                    @endphp

                    <!-- نافذة عرض وتعديل وحذف المتطلبات الخاصة بهذا العقار -->
                    <div id="attrsModal_{{ $type->id }}" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200">
                            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                                <div>
                                    <h3 class="font-bold text-base text-slate-900">جدول متطلبات وحقول: <span class="text-emerald-600">{{ $type->name }}</span></h3>
                                    <p class="text-xs text-slate-500 mt-0.5">التصنيف الرئيسي: {{ $arabicCat }} | إجمالي المتطلبات: {{ $typeAttrs->count() }}</p>
                                </div>
                                <button type="button" onclick="closeModal('attrsModal_{{ $type->id }}')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold flex items-center justify-center">✕</button>
                            </div>

                            <div class="max-h-96 overflow-y-auto rounded-2xl border border-slate-200/80">
                                <table class="w-full text-sm text-right">
                                    <thead class="bg-slate-50 text-slate-600 text-xs border-b border-slate-200">
                                        <tr>
                                            <th class="py-3 px-4 w-10 text-center">#</th>
                                            <th class="py-3 px-4">اسم المتطلب</th>
                                            <th class="py-3 px-4">نوع الإدخال</th>
                                            <th class="py-3 px-4 text-center">إجراء</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($typeAttrs->values() as $attrIdx => $attr)
                                            @php
                                                $currentFieldType = $fieldTypeMap[$attr->field_type] ?? $attr->field_type;
                                            @endphp
                                            <tr class="hover:bg-slate-50/60">
                                                <td class="py-3 px-4 text-center font-mono text-xs text-slate-400">{{ $attrIdx + 1 }}</td>
                                                <td class="py-3 px-4">
                                                    <input type="text" name="attribute_name" form="updateAttrForm_{{ $attr->id }}" value="{{ $attr->attribute_name }}" required
                                                           class="w-full rounded-xl border border-slate-300 px-3 py-1.5 text-xs font-bold text-slate-900 focus:border-emerald-600 focus:outline-none">
                                                </td>
                                                <td class="py-3 px-4">
                                                    <select name="field_type" form="updateAttrForm_{{ $attr->id }}" class="w-full rounded-xl border border-slate-300 px-3 py-1.5 text-xs bg-white focus:border-emerald-600 focus:outline-none">
                                                        @foreach($fieldTypes as $ft)
                                                            <option value="{{ $ft->name }}" {{ $currentFieldType == $ft->name ? 'selected' : '' }}>{{ $ft->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="py-3 px-4 text-center">
                                                    <div class="flex items-center justify-center gap-1.5">
                                                        <form id="updateAttrForm_{{ $attr->id }}" action="{{ route('erp.property_attributes.update', $attr->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold transition">
                                                                حفظ
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('erp.property_attributes.delete', $attr->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا المتطلب؟');">
                                                            @csrf
                                                            <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-red-50 hover:bg-red-600 text-red-600 hover:text-white border border-red-200 text-xs font-semibold transition">
                                                                حذف
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="py-8 text-center text-slate-400 text-xs">
                                                    لا توجد متطلبات مضافة لهذا العقار حالياً. يمكنك إضافتها من نموذج (2. إضافة المتطلبات والحقول).
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="flex justify-end pt-4 mt-4 border-t border-slate-100">
                                <button type="button" onclick="closeModal('attrsModal_{{ $type->id }}')" class="px-5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">إغلاق النافذة</button>
                            </div>
                        </div>
                    </div>

                    <!-- نافذة تعديل نوع العقار كامل -->
                    <div id="editPropertyTypeModal_{{ $type->id }}" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 text-right">
                            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                                <h3 class="font-bold text-base text-slate-900">تعديل بيانات العقار: {{ $type->name }}</h3>
                                <button type="button" onclick="closeModal('editPropertyTypeModal_{{ $type->id }}')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold flex items-center justify-center">✕</button>
                            </div>
                            <form action="{{ route('erp.property_types.update', $type->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">التصنيف الرئيسي *</label>
                                    <select name="category" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm bg-white">
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->name }}" {{ $arabicCat == $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">مسمى نوع العقار *</label>
                                    <input type="text" name="name" value="{{ $type->name }}" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm">
                                </div>
                                <label class="flex items-center gap-2 text-xs font-medium text-slate-700 cursor-pointer">
                                    <input type="checkbox" name="has_units" value="1" {{ $type->has_units ? 'checked' : '' }} class="rounded text-emerald-600">
                                    <span>يتفرع منه وحدات داخلية (مثل العمارة تتفرع لشقق ومحلات)</span>
                                </label>
                                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                                    <button type="button" onclick="closeModal('editPropertyTypeModal_{{ $type->id }}')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold">إلغاء</button>
                                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-lg shadow-emerald-600/20">حفظ التعديلات</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach

                <!-- نافذة (Modal) إدارة التصنيفات الرئيسية (إضافة / تعديل / حذف) -->
                <div id="manageCategoriesModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                            <h3 class="font-bold text-base text-slate-900">إدارة التصنيفات الرئيسية للعقارات</h3>
                            <button type="button" onclick="closeModal('manageCategoriesModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg">✕</button>
                        </div>

                        <form action="{{ route('erp.property_categories.store') }}" method="POST" class="flex gap-2 mb-5">
                            @csrf
                            <input type="text" name="name" required placeholder="أدخل اسم تصنيف جديد (مثال: استثماري، فندقي...)"
                                   class="flex-1 rounded-xl border border-slate-300 px-3.5 py-2 text-sm">
                            <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shrink-0">
                                + إضافة تصنيف
                            </button>
                        </form>

                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @foreach($categories as $cat)
                                <div class="flex items-center justify-between gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                                    <form action="{{ route('erp.property_categories.update', $cat->id) }}" method="POST" class="flex-1 flex gap-2">
                                        @csrf
                                        <input type="text" name="name" value="{{ $cat->name }}" required
                                               class="flex-1 rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-bold bg-white">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-700 text-white text-xs font-semibold">
                                            تعديل
                                        </button>
                                    </form>
                                    <form action="{{ route('erp.property_categories.delete', $cat->id) }}" method="POST" onsubmit="return confirm('حذف هذا التصنيف؟');">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-600 text-red-600 hover:text-white text-xs font-semibold">
                                            حذف
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- نافذة (Modal) إدارة أنواع الحقول (إضافة / تعديل / حذف) -->
                <div id="manageFieldTypesModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                            <h3 class="font-bold text-base text-slate-900">إدارة أنواع الحقول والمدخلات</h3>
                            <button type="button" onclick="closeModal('manageFieldTypesModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg">✕</button>
                        </div>

                        <form action="{{ route('erp.field_types.store') }}" method="POST" class="grid grid-cols-12 gap-2 mb-5">
                            @csrf
                            <input type="text" name="name" required placeholder="مسمى نوع الحقل (مثال: مرفق ملف PDF)"
                                   class="col-span-6 rounded-xl border border-slate-300 px-3 py-2 text-xs">
                            <select name="input_type" class="col-span-3 rounded-xl border border-slate-300 px-2 py-2 text-xs bg-white">
                                <option value="text">نص</option>
                                <option value="number">رقم</option>
                                <option value="date">تاريخ</option>
                                <option value="boolean">نعم/لا</option>
                                <option value="select">قائمة</option>
                            </select>
                            <button type="submit" class="col-span-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold">
                                + إضافة
                            </button>
                        </form>

                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @foreach($fieldTypes as $ft)
                                <div class="flex items-center justify-between gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                                    <form action="{{ route('erp.field_types.update', $ft->id) }}" method="POST" class="flex-1 flex gap-2">
                                        @csrf
                                        <input type="text" name="name" value="{{ $ft->name }}" required
                                               class="flex-1 rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-bold bg-white">
                                        <input type="hidden" name="input_type" value="{{ $ft->input_type }}">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-700 text-white text-xs font-semibold">
                                            تعديل
                                        </button>
                                    </form>
                                    <form action="{{ route('erp.field_types.delete', $ft->id) }}" method="POST" onsubmit="return confirm('حذف نوع الحقل؟');">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-600 text-red-600 hover:text-white text-xs font-semibold">
                                            حذف
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <script>
                    function openModal(id) {
                        const el = document.getElementById(id);
                        if (el) { el.classList.remove('hidden'); el.classList.add('flex'); }
                    }
                    function closeModal(id) {
                        const el = document.getElementById(id);
                        if (el) { el.classList.add('hidden'); el.classList.remove('flex'); }
                    }
                    function addMoreAttributeRow() {
                        const container = document.getElementById('dynamicAttributesContainer');
                        const firstRow = container.querySelector('.attribute-row');
                        const clone = firstRow.cloneNode(true);
                        clone.querySelector('input[name="attribute_names[]"]').value = '';
                        
                        const header = clone.querySelector('.flex.items-center.justify-between');
                        if (!header.querySelector('.remove-row-btn')) {
                            const removeBtn = document.createElement('button');
                            removeBtn.type = 'button';
                            removeBtn.className = 'text-xs text-red-500 hover:text-red-700 font-bold remove-row-btn';
                            removeBtn.innerText = '✕ حذف الحقل';
                            removeBtn.onclick = function() { clone.remove(); };
                            header.appendChild(removeBtn);
                        } else {
                            header.querySelector('.remove-row-btn').onclick = function() { clone.remove(); };
                        }
                        container.appendChild(clone);
                    }
                </script>
            @endif

           <!-- 4. شجرة الحسابات المحاسبية (شكل شجري مبسط ومتسلسل) -->
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

                <!-- شريط الأدوات العلوي -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-5 flex flex-wrap items-center justify-between gap-4 mb-6">
                    <div>
                        <h3 class="font-bold text-base text-slate-900">شجرة الحسابات المحاسبية (الهيكل الشجري)</h3>
                        <p class="text-xs text-slate-500 mt-0.5">عرض هيكلي مبسط ومرتب، مع إمكانية التفرع والإدارة وربط السندات فوراً</p>
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

                <!-- الهيكل الشجري الحقيقي -->
                @if($accounts->count() == 0)
                    <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center space-y-4">
                        <div class="text-4xl">🌳</div>
                        <h4 class="font-bold text-slate-800 text-base">شجرة الحسابات فارغة</h4>
                        <p class="text-xs text-slate-500 max-w-md mx-auto">اضغط على زر توليد الشجرة القياسية أدناه لبدء العمل فوراً.</p>
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
                                $rootBalance = $root->is_group ? $level1Children->sum('balance') + $root->balance : $root->balance;
                            @endphp

                            <!-- الجذع الرئيسي للشجرة -->
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
                                        <button type="button" onclick="openAccModal('editAccModal_{{ $root->id }}')" class="px-2.5 py-1.5 rounded-xl bg-white border text-xs">✏️</button>
                                        @if($level1Children->count() == 0)
                                            <form action="{{ route('erp.accounts.delete', $root->id) }}" method="POST" onsubmit="return confirm('حذف هذا المجلد؟');">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-red-50 text-red-600 text-xs">🗑️</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                <!-- الأغصان والفروع المتسلسلة -->
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
                                                    <button type="button" onclick="openAccModal('editAccModal_{{ $child->id }}')" class="px-2 py-1 rounded-lg bg-slate-100 text-xs">✏️</button>
                                                    @if($level2Children->count() == 0)
                                                        <form action="{{ route('erp.accounts.delete', $child->id) }}" method="POST" onsubmit="return confirm('حذف؟');">
                                                            @csrf
                                                            <button type="submit" class="px-2 py-1 rounded-lg bg-red-50 text-red-600 text-xs">🗑️</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- المستوى الثالث (الأوراق الفرعية) -->
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
                                                                <form action="{{ route('erp.accounts.delete', $subChild->id) }}" method="POST" onsubmit="return confirm('حذف؟');">
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

                <!-- النوافذ المنبثقة (Modals) للإضافة والتعديل والسندات -->
                <div id="addBranchesModal" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-slate-200">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                            <div>
                                <h3 id="addBranchesTitle" class="font-bold text-base text-slate-900">إضافة حسابات وفروع</h3>
                                <p class="text-xs text-slate-500 mt-0.5">أضف فرعاً واحداً أو عدة فروع دفعة واحدة (الكود يولد تلقائياً)</p>
                            </div>
                            <button type="button" onclick="closeAccModal('addBranchesModal')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 font-bold">✕</button>
                        </div>

                        <form action="{{ route('erp.accounts.store') }}" method="POST" class="space-y-4">
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
                                        <input type="text" name="names[]" required placeholder="مثال: البنك الأهلي..."
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
                                <button type="button" onclick="closeAccModal('addBranchesModal')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold">حفظ وإضافة للشجرة</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- نافذة إصدار السند الفوري -->
                <div id="quickVoucherModal" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                            <div>
                                <h3 id="qv_title" class="font-bold text-base text-slate-900">إصدار سند مالي</h3>
                                <p id="qv_subtitle" class="text-xs text-emerald-600 font-bold mt-0.5"></p>
                            </div>
                            <button type="button" onclick="closeAccModal('quickVoucherModal')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 font-bold">✕</button>
                        </div>

                        <form action="{{ route('erp.vouchers.store') }}" method="POST" class="space-y-4">
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
                                }
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">البيان (وذلك عن...) *</label>
                                <input type="text" name="notes" required placeholder="أدخل بيان السند..."
                                       class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm">
                            </div>

                            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                                <button type="button" onclick="closeAccModal('quickVoucherModal')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold">اعتماد السند وتحديث الرصيد</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- نوافذ تعديل الحسابات -->
                @foreach($accounts as $acc)
                    @php $accTypeAr = $typeTranslations[$acc->type] ?? $acc->type; @endphp
                    <div id="editAccModal_{{ $acc->id }}" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                                <h3 class="font-bold text-base text-slate-900">تعديل: {{ $acc->name }}</h3>
                                <button type="button" onclick="closeAccModal('editAccModal_{{ $acc->id }}')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                            </div>
                            <form action="{{ route('erp.accounts.update', $acc->id) }}" method="POST" class="space-y-4">
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
                                <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                                    <input type="checkbox" name="is_group" value="1" {{ $acc->is_group ? 'checked' : '' }} class="rounded text-emerald-600">
                                    <span>مجلد رئيسي</span>
                                </label>
                                <div class="flex justify-end gap-2 pt-3 border-t">
                                    <button type="button" onclick="closeAccModal('editAccModal_{{ $acc->id }}')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">حفظ</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach

                <!-- نافذة إدارة أنواع الحسابات -->
                <div id="manageAccountTypesModal" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                            <h3 class="font-bold text-base text-slate-900">إدارة أنواع الحسابات</h3>
                            <button type="button" onclick="closeAccModal('manageAccountTypesModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                        </div>
                        <form action="{{ route('erp.account_types.store') }}" method="POST" class="flex gap-2 mb-5">
                            @csrf
                            <input type="text" name="name" required placeholder="مسمى النوع (مثال: أصول متداولة...)" class="flex-1 rounded-xl border px-3 py-2 text-xs">
                            <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">+ إضافة</button>
                        </form>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @foreach($accountTypes as $at)
                                <div class="flex items-center justify-between gap-2 p-2.5 rounded-xl bg-slate-50 border">
                                    <form action="{{ route('erp.account_types.update', $at->id) }}" method="POST" class="flex-1 flex gap-2">
                                        @csrf
                                        <input type="text" name="name" value="{{ $at->name }}" required class="flex-1 rounded-lg border px-3 py-1.5 text-xs font-bold bg-white">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-900 text-white text-xs">تعديل</button>
                                    </form>
                                    <form action="{{ route('erp.account_types.delete', $at->id) }}" method="POST" onsubmit="return confirm('حذف؟');">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 text-red-600 text-xs">حذف</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <script>
                    function openAccModal(id) { const el = document.getElementById(id); if (el) { el.classList.remove('hidden'); el.classList.add('flex'); } }
                    function closeAccModal(id) { const el = document.getElementById(id); if (el) { el.classList.add('hidden'); el.classList.remove('flex'); } }
                    function openAddBranchesModal(parentId, parentName, parentType) {
                        document.getElementById('branch_parent_id').value = parentId || '';
                        if (parentType) document.getElementById('branch_acc_type').value = parentType;
                        document.getElementById('addBranchesTitle').innerText = parentId ? 'إضافة فرع تحت: ' + parentName : 'إضافة حساب أو فروع';
                        openAccModal('addBranchesModal');
                    }
                    function addAnotherBranchRow() {
                        const container = document.getElementById('multiBranchesContainer');
                        const firstRow = container.querySelector('.branch-row');
                        const clone = firstRow.cloneNode(true);
                        clone.querySelector('input[name="names[]"]').value = '';
                        clone.querySelector('input[name="codes[]"]').value = '';
                        container.appendChild(clone);
                    }
                    function openQuickVoucherModal(type, accountId, accountLabel) {
                        document.getElementById('qv_type').value = type;
                        const titleEl = document.getElementById('qv_title');
                        const subEl = document.getElementById('qv_subtitle');
                        const debitSelect = document.getElementById('qv_debit');
                        const creditSelect = document.getElementById('qv_credit');
                        if (type === 'receipt') {
                            titleEl.innerText = '📥 سند قبض مباشر';
                            subEl.innerText = 'الحساب الدائن: ' + accountLabel;
                            creditSelect.value = accountId;
                        } else {
                            titleEl.innerText = '📤 سند صرف مباشر';
                            subEl.innerText = 'الحساب المدين: ' + accountLabel;
                            debitSelect.value = accountId;
                        }
                        openAccModal('quickVoucherModal');
                    }
                </script>
            @endif

            <!-- 5. سندات القبض والصرف -->
            @if($tab == 'vouchers')
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
                        <h3 class="font-bold text-base text-slate-900 mb-4">إصدار سند قبض / صرف</h3>
                        <form action="{{ route('erp.vouchers.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">نوع السند *</label>
                                <select name="voucher_type" class="w-full rounded-2xl border border-slate-300 px-3.5 py-2.5 text-sm bg-white">
                                    <option value="receipt">سند قبض (استلام دفعة / اشتراك / نسبة)</option>
                                    <option value="payment">سند صرف (دفع مصروفات / تحويل)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">المكتب المرتبط (اختياري)</label>
                                <select name="office_id" class="w-full rounded-2xl border border-slate-300 px-3.5 py-2.5 text-sm bg-white">
                                    <option value="">-- عام للشركة --</option>
                                    @foreach($offices as $off)
                                        <option value="{{ $off->id }}">{{ $off->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">من حساب (المدين) *</label>
                                <select name="debit_account_id" required class="w-full rounded-2xl border border-slate-300 px-3.5 py-2.5 text-sm bg-white">
                                    @foreach($accounts->where('is_group', 0) as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">إلى حساب (الدائن) *</label>
                                <select name="credit_account_id" required class="w-full rounded-2xl border border-slate-300 px-3.5 py-2.5 text-sm bg-white">
                                    @foreach($accounts->where('is_group', 0) as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">المبلغ (ر.س) *</label>
                                <input type="number" step="0.01" name="amount" required class="w-full rounded-2xl border border-slate-300 px-3.5 py-2.5 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">طريقة الدفع *</label>
                                <select name="payment_method" class="w-full rounded-2xl border border-slate-300 px-3.5 py-2.5 text-sm bg-white">
                                    <option value="bank_transfer">حوالة بنكية</option>
                                    <option value="sadad">سداد</option>
                                    <option value="mada">مدى</option>
                                    <option value="cash">نقدي</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">تاريخ السند *</label>
                                <input type="date" name="voucher_date" value="{{ date('Y-m-d') }}" required class="w-full rounded-2xl border border-slate-300 px-3.5 py-2.5 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">البيان *</label>
                                <input type="text" name="notes" required placeholder="وذلك عن..." class="w-full rounded-2xl border border-slate-300 px-3.5 py-2.5 text-sm">
                            </div>
                            <button type="submit" class="w-full py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-sm">إصدار السند وترحيل القيد</button>
                        </form>
                    </div>

                    <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-100">
                            <h3 class="font-bold text-slate-900">سجل السندات المالية المرحّلة</h3>
                        </div>
                        <table class="w-full text-sm text-right">
                            <thead class="bg-slate-50 text-slate-600 text-xs border-b border-slate-200/60">
                                <tr>
                                    <th class="py-3.5 px-6">رقم السند</th>
                                    <th class="py-3.5 px-6">النوع</th>
                                    <th class="py-3.5 px-6">المكتب</th>
                                    <th class="py-3.5 px-6">المبلغ</th>
                                    <th class="py-3.5 px-6">البيان والتاريخ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($vouchers as $v)
                                    <tr>
                                        <td class="py-4 px-6 font-mono font-bold">{{ $v->voucher_number }}</td>
                                        <td class="py-4 px-6">
                                            @if($v->voucher_type == 'receipt')
                                                <span class="px-2.5 py-1 rounded-xl text-xs font-semibold bg-emerald-50 text-emerald-700">سند قبض</span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-xl text-xs font-semibold bg-red-50 text-red-700">سند صرف</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6">{{ $v->office_name ?? 'الشركة الأم' }}</td>
                                        <td class="py-4 px-6 font-bold">{{ number_format($v->amount, 2) }} ر.س</td>
                                        <td class="py-4 px-6 text-xs text-slate-500">{{ $v->notes }} ({{ $v->voucher_date }})</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="py-8 text-center text-slate-400">لا توجد سندات مصدرة بعد.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- 6. إعدادات الحساب الشخصي (تعديل رقم الجوال 10 أرقام + اسم المستخدم وكلمة المرور) -->
            @if($tab == 'settings')
                @php
                    $myLocalPhone = \App\Http\Controllers\CompanyErpController::toLocalPhone(auth()->user()->phone);
                @endphp
                <div class="max-w-xl bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
                    <h3 class="font-bold text-base text-slate-900 mb-1">تعديل بيانات الدخول الخاصة بك</h3>
                    <p class="text-xs text-slate-500 mb-5">يمكنك تغيير رقم الجوال المعتمد لكود الواتساب أو تعديل اسم المستخدم وكلمة المرور الاحتياطية</p>

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
                            <span class="text-[11px] text-emerald-600">محفوظ دولياً للواتساب بصيغة: {{ auth()->user()->phone }}</span>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">اسم المستخدم (للدخول في حال تعطل الواتساب) *</label>
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
        function openEditOfficeModal(office) {
            const modal = document.getElementById('editOfficeModal');
            const form = document.getElementById('editOfficeForm');
            form.action = '/offices/' + office.id + '/update';

            document.getElementById('edit_name').value = office.name;
            document.getElementById('edit_cr_number').value = office.cr_number;
            document.getElementById('edit_fal_license').value = office.fal_license;
            document.getElementById('edit_phone').value = office.phone;
            document.getElementById('edit_username').value = office.username;
            document.getElementById('edit_billing_type').value = office.billing_type;
            document.getElementById('edit_annual_fee').value = office.annual_fee;
            document.getElementById('edit_company_commission_rate').value = office.company_commission_rate;
            document.getElementById('edit_subscription_ends_at').value = office.subscription_ends_at;
            document.getElementById('edit_status').value = office.status;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeEditOfficeModal() {
            const modal = document.getElementById('editOfficeModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
</body>
</html>