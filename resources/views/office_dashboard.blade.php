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
                <p class="text-[11px] text-emerald-300 font-medium">بوابة إدارة الأملاك والعقود</p>
            </div>
        </div>

        <nav class="p-4 space-y-1.5 flex-1 overflow-y-auto">
            <div class="px-3 py-2 text-[11px] font-bold text-emerald-300/60 uppercase tracking-wider">إدارة الأملاك والعقارات</div>

            <a href="{{ route('office.dashboard', ['tab' => 'overview']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'overview' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <span>📊</span>
                <span>ملخص المكتب والإحصائيات</span>
            </a>

            <a href="{{ route('office.dashboard', ['tab' => 'owners']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'owners' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <span>👔</span>
                <span>الملاك والوكلاء ({{ $stats['owners_count'] }})</span>
            </a>

            <a href="{{ route('office.dashboard', ['tab' => 'properties']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'properties' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <span>🏗️</span>
                <span>العقارات والصكوك والوحدات</span>
            </a>

            <a href="{{ route('office.dashboard', ['tab' => 'contracts']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'contracts' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <span>📝</span>
                <span>المستأجرون وعقود (إيجار)</span>
            </a>

            <div class="px-3 pt-4 pb-2 text-[11px] font-bold text-emerald-300/60 uppercase tracking-wider">النظام المالي والمحاسبي للمكتب</div>

            <a href="{{ route('office.dashboard', ['tab' => 'accounting']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'accounting' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <span>🌳</span>
                <span>شجرة الحسابات المحاسبية</span>
            </a>

            <a href="{{ route('office.dashboard', ['tab' => 'vouchers']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'vouchers' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <span>🧾</span>
                <span>سندات القبض والصرف</span>
            </a>

            <div class="px-3 pt-4 pb-2 text-[11px] font-bold text-emerald-300/60 uppercase tracking-wider">الإعدادات والموارد البشرية</div>

            <a href="{{ route('office.dashboard', ['tab' => 'staff']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'staff' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <span>👥</span>
                <span>موظفو المكتب والصلاحيات</span>
            </a>

            <a href="{{ route('office.dashboard', ['tab' => 'whatsapp']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'whatsapp' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <span>💬</span>
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
                <h2 class="text-base font-bold text-slate-900">{{ $office->name }} — نظام إدارة الأملاك والربط مع إيجار</h2>
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
                        <div class="text-xs font-semibold text-slate-500 mb-2">مستحقات الشركة الأم المحتسبة</div>
                        <div class="text-2xl font-bold text-amber-600">{{ number_format($stats['company_due_fees'], 2) }} <span class="text-xs">ر.س</span></div>
                        <p class="text-xs text-slate-400 mt-2">بناءً على نسبة الاتفاق ({{ $office->company_commission_rate }}%)</p>
                    </div>
                </div>

                <!-- أحدث العقود المبرمة -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="font-bold text-slate-900">أحدث عقود الإيجار المبرمة في المكتب</h3>
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
                            <h3 class="font-bold text-base text-slate-900">سجل ملاك العقارات والمحافظ المدارة</h3>
                            <p class="text-xs text-slate-500 mt-0.5">يمكنك البحث المباشر أو الدخول لبوابة المالك لمعاينتها</p>
                        </div>
                        <div class="flex items-center gap-3 flex-1 max-w-md">
                            <input type="text" id="ownerSearchInput" oninput="filterTableRows('ownerSearchInput', 'ownersTableBody')"
                                   placeholder="🔍 ابحث فوراً باسم المالك أو الجوال أو الهوية..."
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
                                    <th class="py-3.5 px-5">البنك والآيبان</th>
                                    <th class="py-3.5 px-5">نسبة الإدارة</th>
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
                            <p class="text-xs text-slate-500 mt-0.5">جميع الصكوك والعقارات المسجلة بالمكتب مع إمكانية استعراض وإضافة الوحدات الداخلية لكل عقار</p>
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
                                    <th class="py-4 px-4">المواصفات والمتطلبات</th>
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
                                                            <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px]">
                                                                {{ $k }}: <b>{{ $v }}</b>
                                                            </span>
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
                                                            class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-sm"
                                                            title="إضافة وحدة جديدة">
                                                        + وحدة
                                                    </button>
                                                </div>
                                            @else
                                                <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-500 text-xs">عقار مستقل</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <form action="{{ route('office.properties.delete', $prop->id) }}" method="POST" onsubmit="return confirm('حذف هذا العقار؟');">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-red-50 hover:bg-red-600 text-red-600 hover:text-white text-xs font-bold transition">🗑️ حذف</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="py-12 text-center text-slate-400">لا توجد عقارات مسجلة بعد. اضغط على "إضافة عقار وصك جديد" في أعلى اليسار.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- نوافذ عرض الوحدات الداخلية لكل عقار -->
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
                                                            <button type="submit" class="text-red-600 font-bold">🗑️</button>
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

                <!-- نافذة إضافة عقار وصك ملكية جديد -->
                <div id="addPropertyModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-slate-200 max-h-[92vh] overflow-y-auto">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                            <div>
                                <h3 class="font-bold text-base text-slate-900">إضافة عقار وصك ملكية جديد</h3>
                                <p class="text-xs text-slate-500">عند اختيار نوع العقار ستظهر تلقائياً المتطلبات المعتمدة من الإدارة</p>
                            </div>
                            <button type="button" onclick="closeModal('addPropertyModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                        </div>

                        <form action="{{ route('office.properties.store') }}" method="POST" class="space-y-4">
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
                                <div class="text-xs font-bold text-emerald-800">📋 المتطلبات الخاصة بنوع العقار المختار:</div>
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

                <!-- نافذة إضافة وحدة عقارية داخل عقار -->
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
                            if (attr.field_type.includes('رقم') || attr.field_type === 'number') {
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
                            <h3 class="font-bold text-base text-slate-900">دليل المستأجرين وحساباتهم في الشجرة المحاسبية</h3>
                            <p class="text-xs text-slate-500 mt-0.5">اضغط على <b class="text-emerald-700">اسم المستأجر</b> لفتح كشف الحساب التفصيلي الخاص به</p>
                        </div>

                        <div class="flex items-center gap-3 flex-1 max-w-md">
                            <input type="text" id="tenantSearchInput" oninput="filterTableRows('tenantSearchInput', 'tenantsTableBody')"
                                   placeholder="🔍 بحث فوري باسم المستأجر أو رقم الجوال أو الهوية..."
                                   class="w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-xs focus:outline-none focus:border-emerald-600">
                        </div>

                        <div class="flex items-center gap-2.5">
                            <button type="button" onclick="openModal('addTenantModal')"
                                    class="px-5 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-lg transition">
                                + إضافة مستأجر جديد (وربطه بالشجرة)
                            </button>
                            <button type="button" onclick="openModal('addContractModal')"
                                    class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs shadow-lg shadow-emerald-600/20 transition">
                                📝 إبرام عقد إيجار جديد
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-right">
                            <thead class="bg-slate-50 text-slate-600 text-xs border-b">
                                <tr>
                                    <th class="py-3.5 px-5">اسم المستأجر (اضغط لكشف الحساب)</th>
                                    <th class="py-3.5 px-5">رقم الهوية / الإقامة</th>
                                    <th class="py-3.5 px-5">رقم الجوال</th>
                                    <th class="py-3.5 px-5">رقم الحساب في الشجرة</th>
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
                                                <span class="text-xs text-slate-400">غير مربوط</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-5">
                                            <span class="font-bold text-emerald-700">{{ number_format($tnReceipts, 2) }} ر.س</span>
                                        </td>
                                        <td class="py-4 px-5 text-center">
                                            <button type="button" onclick="openModal('tenantStatementModal_{{ $tn->id }}')"
                                                    class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white text-xs font-semibold transition">
                                                📄 كشف الحساب
                                            </button>
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
                                            @if($cnt->ejar_status == 'active')
                                                <span class="inline-block px-3 py-1 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold">
                                                    ✅ موثق في إيجار<br>
                                                    <span class="font-mono text-[10px]">{{ $cnt->ejar_contract_number }}</span>
                                                </span>
                                            @else
                                                <form action="{{ route('office.contracts.sync_ejar', $cnt->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-white text-xs font-bold shadow-sm transition">
                                                        ⚡ إرسال وتوثيق في إيجار
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="py-10 text-center text-slate-400">لا توجد عقود إيجار مسجلة بعد.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

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
                            <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-200">
                                <label class="block text-xs font-bold text-emerald-900 mb-1">🌳 الحساب الأب في شجرة المكتب (للتفرع المتسلسل التلقائي)</label>
                                <select name="parent_account_id" class="w-full rounded-xl border border-emerald-300 px-3 py-2 text-xs bg-white font-semibold">
                                    <option value="">-- ربط تلقائي تحت ذمم المستأجرين (1103) --</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}" {{ $acc->code == '1103' ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
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
                            <h3 class="font-bold text-base text-slate-900">سجل سندات القبض والصرف — ({{ $office->name }})</h3>
                            <p class="text-xs text-slate-500 mt-0.5">تسلسل منفصل للسندات، مع إمكانية التعديل والحذف والتصدير PDF والإرسال عبر واتساب</p>
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
                                                    ✏️
                                                </button>

                                                <form action="{{ route('office.vouchers.delete', $v->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا السند وعكس قيده المالي؟');">
                                                    @csrf
                                                    <button type="submit" class="px-2 py-1.5 rounded-xl bg-red-50 hover:bg-red-600 text-red-600 hover:text-white text-[11px] font-bold transition">
                                                        🗑️
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
                                            @if($st->user_type != 'office_admin')
                                                <form action="{{ route('office.staff.delete', $st->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1 rounded-xl bg-red-50 text-red-600 text-xs font-bold">حذف</button>
                                                </form>
                                            @else
                                                <span class="text-xs text-slate-400">حساب رئيسي</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- 8. تبويب إعدادات الربط مع واتساب API للمكتب -->
            @if($tab == 'whatsapp')
                @php
                    $useCustomWa = ($office->use_custom_whatsapp ?? 0) == 1;
                @endphp
                <div class="max-w-2xl bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <h3 class="font-bold text-base text-slate-900">💬 إعدادات الربط مع واتساب API — ({{ $office->name }})</h3>
                            <p class="text-xs text-slate-500 mt-0.5">إدارة إرسال أكواد التحقق (OTP) وإشعارات السندات المالية للمستأجرين والملاك</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $useCustomWa ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                            {{ $useCustomWa ? 'مربوط برقم خاص بالمكتب' : 'مربوط ببوابة الإدارة المركزية (افتراضي)' }}
                        </span>
                    </div>

                    <form action="{{ route('office.whatsapp.update') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 space-y-2">
                            <label class="flex items-center gap-2.5 text-sm font-bold text-emerald-950 cursor-pointer">
                                <input type="radio" name="use_custom_whatsapp" value="0" {{ !$useCustomWa ? 'checked' : '' }} onchange="toggleOfficeWaMode(false)" class="text-emerald-600">
                                <span>استخدام رقم وبوابة الإدارة المركزية (مفعل افتراضياً)</span>
                            </label>
                            <p class="text-xs text-emerald-800 pr-6">
                                يتم إرسال جميع أكواد التحقق والإشعارات تلقائياً عبر رقم الواتساب المعتمد لدى الشركة الأم دون الحاجة لأي إعدادات إضافية.
                            </p>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                            <label class="flex items-center gap-2.5 text-sm font-bold text-slate-900 cursor-pointer">
                                <input type="radio" name="use_custom_whatsapp" value="1" {{ $useCustomWa ? 'checked' : '' }} onchange="toggleOfficeWaMode(true)" class="text-emerald-600">
                                <span>ربط رقم وجهاز واتساب خاص بمكتبنا (API مخصص)</span>
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
                            حفظ واعتماد إعدادات الواتساب للمكتب
                        </button>
                    </form>
                </div>
            @endif

        </main>
    </div>

    <!-- نافذة معاينة وطباعة وتصدير السند الرسمي PDF (بدون حساب مدين ودائن) -->
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

            <!-- قالب السند الرسمي القابل للطباعة والـ PDF -->
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
            if (document.getElementById('pv_debit')) document.getElementById('pv_debit').innerText = v.debit || '';
            if (document.getElementById('pv_credit')) document.getElementById('pv_credit').innerText = v.credit || '';
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
    </script>
</body>
</html>