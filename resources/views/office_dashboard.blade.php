<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة مكتب العقار | {{ $office->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
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
        /* تنسيق جدول البحث والاختيار الموحد العصري بدون أي قص للأعمدة */
        .erp-picker-host { position: relative !important; width: 100% !important; }
        .erp-picker-trigger {
            display: flex !important; align-items: center !important; justify-content: space-between !important; gap: 6px !important;
            width: 100% !important; padding: 8px 10px !important; border-radius: 9px !important;
            border: 1.5px solid #cbd5e1 !important; background: #ffffff !important; cursor: pointer !important;
            font-size: 0.76rem !important; font-weight: 700 !important; color: #0f172a !important;
        }
        .erp-picker-trigger:hover, .erp-picker-trigger.active {
            border-color: #0f172a !important; background-color: #f8fafc !important;
        }
        .erp-picker-panel {
            position: absolute !important; top: calc(100% + 4px) !important; right: 0 !important; left: 0 !important;
            width: 100% !important; min-width: 100% !important; max-width: 100% !important; z-index: 99999 !important;
            background: #ffffff !important; border: 2px solid #0f172a !important; border-radius: 12px !important;
            box-shadow: 0 18px 38px rgba(15, 23, 42, 0.24) !important; overflow: hidden !important;
        }
        .erp-picker-table-wrap { max-height: 230px !important; overflow-y: auto !important; overflow-x: hidden !important; width: 100% !important; }
        .erp-picker-table { width: 100% !important; table-layout: auto !important; border-collapse: collapse !important; text-align: right !important; }
        .erp-picker-table thead th {
            position: sticky !important; top: 0 !important; z-index: 3 !important;
            background: #0f172a !important; color: #ffffff !important;
            padding: 7px 6px !important; font-size: 0.68rem !important; font-weight: 700 !important;
            white-space: normal !important; line-height: 1.25 !important; border-bottom: none !important;
        }
        .erp-picker-table tbody td {
            padding: 7px 6px !important; font-size: 0.71rem !important; line-height: 1.35 !important;
            white-space: normal !important; word-break: break-word !important;
            border-bottom: 1px solid #e2e8f0 !important; color: #0f172a !important;
        }
        .erp-picker-table tbody tr:hover { background-color: #ecfdf5 !important; }
        .erp-picker-table tbody tr.selected-row { background-color: #d1fae5 !important; font-weight: 700 !important; }

        /* شريط ترقيم الصفحات العصري (Pagination 1, 2, 3...) */
        .erp-pagination-bar {
            display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px;
            padding: 12px 18px; background: #f8fafc; border-top: 1px solid #e2e8f0; font-size: 0.75rem;
        }
        .erp-page-btn {
            min-width: 32px; height: 32px; padding: 0 10px; border-radius: 8px;
            border: 1px solid #cbd5e1; background: #ffffff; color: #0f172a;
            font-weight: 700; font-size: 0.75rem; cursor: pointer; transition: all 0.15s ease;
        }
        .erp-page-btn:hover:not(:disabled) { background: #e2e8f0; border-color: #94a3b8; }
        .erp-page-btn.active { background: #0f172a !important; color: #ffffff !important; border-color: #0f172a !important; }
        .erp-page-btn:disabled { opacity: 0.45; cursor: not-allowed; }

        /* 1. توحيد الخلفية والخطوط مع منع تقطع الحروف العربية نهائياً */
        body, body *, table, th, td, div, span, p, h1, h2, h3, h4 {
            letter-spacing: 0px !important;
            word-spacing: normal !important;
            font-kerning: normal !important;
            font-feature-settings: "liga" 1, "calt" 1 !important;
        }
        body {
            background-color: #f8fafc !important;
            color: #0f172a !important;
        }

        /* 2. توحيد القوائم الجانبية بلون كحلي داكن رسمي */
        .glass-sidebar, .office-sidebar, .owner-sidebar {
            background: #0f172a !important;
            border-left: 1px solid #1e293b !important;
            box-shadow: none !important;
        }
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
        aside nav a { border-radius: 8px !important; }

        /* 3. البطاقات والحاويات */
        .rounded-3xl, .rounded-2xl { border-radius: 10px !important; }
        .shadow-2xl, .shadow-lg, .shadow-md, .shadow-sm {
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
        }

        /* 4. الجداول */
        table {
            border-collapse: separate !important;
            border-spacing: 0 !important;
            width: 100% !important;
        }
        table thead tr { background-color: #f1f5f9 !important; color: #475569 !important; }
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
        table tbody tr:hover { background-color: #f8fafc !important; }

        /* 5. الأزرار */
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
        td button, td a { border-radius: 6px !important; box-shadow: none !important; }
        span.rounded-full, span.rounded-xl, span.rounded-lg {
            border-radius: 6px !important; font-weight: 500 !important; box-shadow: none !important;
        }
        input:not([type="checkbox"]):not([type="radio"]), select, textarea {
            border-radius: 8px !important; border-color: #cbd5e1 !important; box-shadow: none !important;
        }
        input:focus, select:focus, textarea:focus {
            border-color: #0f172a !important; outline: none !important; ring: 0 !important;
        }
    </style>
</head>
<body class="text-slate-800 flex min-h-screen">

    <!-- القائمة الجانبية لبوابة المكتب -->
    <aside class="w-72 office-sidebar text-white flex flex-col shrink-0 shadow-2xl sticky top-0 h-screen no-print">
        <div class="p-6 border-b border-white/10 flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-teal-500 to-emerald-400 flex items-center justify-center shadow-lg shadow-emerald-500/20 shrink-0 font-bold text-lg">
                🏢
            </div>
            <div>
                <h1 class="font-bold text-sm tracking-tight text-white">{{ $office->name }}</h1>
            </div>
        </div>

        @php
            $allowedTabsList = $allowedOfficeTabs ?? array_keys(\App\Http\Controllers\CompanyErpController::getAvailableOfficeTabs());
            $hasAnyAccountingTab = count(array_intersect(['accounting', 'vouchers', 'journal', 'invoicing', 'account_statement', 'statements'], $allowedTabsList)) > 0;
            $hasAnySettingsTab = count(array_intersect(['staff', 'activity_logs', 'whatsapp'], $allowedTabsList)) > 0;
        @endphp
        <nav class="p-4 space-y-1.5 flex-1 overflow-y-auto">
            <div class="px-3 py-2 text-[11px] font-bold text-emerald-300/60 uppercase tracking-wider">إدارة الأملاك والعقارات</div>

            <a href="{{ route('office.dashboard', ['tab' => 'overview']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'overview' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <span> لوحة التحكم </span>
            </a>

            @if(in_array('owners', $allowedTabsList))
                <a href="{{ route('office.dashboard', ['tab' => 'owners']) }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'owners' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <span> الـملاك ({{ $stats['owners_count'] }})</span>
                </a>
            @endif

            @if(in_array('properties', $allowedTabsList))
                <a href="{{ route('office.dashboard', ['tab' => 'properties']) }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'properties' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <span>العقارات والوحدات</span>
                </a>
            @endif

            @if(in_array('contracts', $allowedTabsList))
                <a href="{{ route('office.dashboard', ['tab' => 'contracts']) }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'contracts' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <span>المستأجرون وعقود (إيجار)</span>
                </a>
            @endif

            @if(in_array('installments', $allowedTabsList))
                <a href="{{ route('office.dashboard', ['tab' => 'installments']) }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'installments' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <span>📅 جدولة الدفعات وأعمار الديون</span>
                </a>
            @endif

            @if(in_array('legal_notices', $allowedTabsList))
                <a href="{{ route('office.dashboard', ['tab' => 'legal_notices']) }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'legal_notices' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <span>⚖️ مولد الإنذارات القانونية الذكي</span>
                </a>
            @endif

            @if($hasAnyAccountingTab)
                <div class="px-3 pt-4 pb-2 text-[11px] font-bold text-emerald-300/60 uppercase tracking-wider">النظام المالي والمحاسبي للمكتب</div>

                @if(in_array('accounting', $allowedTabsList))
                    <a href="{{ route('office.dashboard', ['tab' => 'accounting']) }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'accounting' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <span>شجرة الحسابات</span>
                    </a>
                @endif

                @if(in_array('vouchers', $allowedTabsList))
                    <a href="{{ route('office.dashboard', ['tab' => 'vouchers']) }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'vouchers' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <span>سندات القبض والصرف</span>
                    </a>
                @endif

                @if(in_array('journal', $allowedTabsList))
                    <a href="{{ route('office.dashboard', ['tab' => 'journal']) }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'journal' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <span>القيود اليومية</span>
                    </a>
                @endif

                @if(in_array('invoicing', $allowedTabsList))
                    <a href="{{ route('office.dashboard', ['tab' => 'invoicing']) }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'invoicing' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <span> الفواتير الضريبية</span>
                    </a>
                @endif

                @if(in_array('account_statement', $allowedTabsList))
                    <a href="{{ route('office.dashboard', ['tab' => 'account_statement']) }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'account_statement' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <span>كشف الحساب </span>
                    </a>
                @endif

                @if(in_array('statements', $allowedTabsList))
                    <a href="{{ route('office.dashboard', ['tab' => 'statements']) }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'statements' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <span>الأستاذ العام والقوائم المالية</span>
                    </a>
                @endif
            @endif

            @if($hasAnySettingsTab)
                <div class="px-3 pt-4 pb-2 text-[11px] font-bold text-emerald-300/60 uppercase tracking-wider">الإعدادات والموارد البشرية</div>

                @if(in_array('staff', $allowedTabsList))
                    <a href="{{ route('office.dashboard', ['tab' => 'staff']) }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'staff' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <span> الموظفين والصلاحيات </span>
                    </a>
                @endif

                @if(in_array('activity_logs', $allowedTabsList))
                    <a href="{{ route('office.dashboard', ['tab' => 'activity_logs']) }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'activity_logs' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <span>🕵️ سجل تتبع حركات الموظفين</span>
                    </a>
                @endif

                @if(in_array('whatsapp', $allowedTabsList))
                    <a href="{{ route('office.dashboard', ['tab' => 'whatsapp']) }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ $tab == 'whatsapp' ? 'bg-white text-slate-900 font-bold shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <span>إعدادات الربط مع واتساب</span>
                    </a>
                @endif
            @endif
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
        <header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200/80 px-8 flex items-center justify-between sticky top-0 z-20 no-print">
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
            <?php if (session('success')) { ?>
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold shadow-sm">
                    ✅ {{ session('success') }}
                </div>
            <?php } ?>
            <?php if ($errors->any()) { ?>
                <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-700 text-sm font-semibold shadow-sm">
                    ⚠️ {{ $errors->first() }}
                </div>
            <?php } ?>

            <!-- نافذة معاينة وطباعة السند الرسمي لبوابة المكتب + إرسال PDF أصلي للواتساب -->
            <div id="officialVoucherModal" class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200">
                    <div class="flex items-center justify-between pb-4 border-b mb-4 no-print">
                        <h3 class="font-bold text-base text-slate-900">معاينة السند المالي الرسمي</h3>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="downloadVoucherPDF()" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-700 text-white text-xs font-bold shadow">📥 تحميل PDF</button>
                            <button type="button" onclick="sendElementAsOriginalPdfWhatsapp('printableVoucherArea', (window.currentVoucherNumber || 'Voucher') + '.pdf', '', 'voucher')" class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold shadow">💬 إرسال PDF للواتساب</button>
                            <button type="button" onclick="window.print()" class="px-3.5 py-2 rounded-xl bg-slate-100 text-slate-800 text-xs font-bold">🖨️ طباعة</button>
                            <button type="button" onclick="closeModal('officialVoucherModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                        </div>
                    </div>
                    <div id="printableVoucherArea" class="p-8 rounded-3xl border-2 border-slate-800 bg-white space-y-6 text-slate-900">
                        <div class="flex items-center justify-between border-b-2 border-slate-800 pb-5">
                            <div>
                                <h2 id="pdf_issuer" class="text-xl font-bold text-slate-900">{{ $office->name }}</h2>
                                <p class="text-xs text-slate-500 mt-1">سند مالي رسمي معتمد إلكترونياً</p>
                            </div>
                            <div class="text-center px-6 py-2.5 rounded-2xl border-2 border-slate-900 bg-slate-50">
                                <div id="pdf_voucher_title" class="text-lg font-extrabold text-emerald-800">سند قبض</div>
                                <div id="pdf_voucher_no" class="font-mono text-xs font-bold text-slate-700 mt-0.5">RV-2026-00001</div>
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
                                <span class="text-slate-500 font-semibold">الطرف المرتبط:</span>
                                <span id="pdf_party" class="font-bold text-slate-900"></span>
                            </div>
                            <div class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200">
                                <span class="text-slate-500 font-semibold">وذلك عن (البيان والتفاصيل):</span>
                                <span id="pdf_notes" class="font-bold text-slate-900"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- نافذة إصدار سندات قبض مجمّعة (مع اختيار المالك أو العقار لتعبئة المستأجرين ومبالغهم تلقائياً) -->
            <div id="batchReceiptsModal" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                <div class="bg-white rounded-3xl max-w-4xl w-full p-6 shadow-2xl border border-slate-200 max-h-[92vh] overflow-y-auto">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                        <div>
                            <h3 class="font-bold text-base text-slate-900">⚡ إصدار سندات قبض مجمّعة (بالبحث عن المالك أو العقار أو المستأجر)</h3>
                            <p class="text-xs text-slate-500">اختر المالك من الأعلى ليقوم النظام بإدراج جميع مستأجريه ووضع مبالغهم المستحقة تلقائياً، أو أضفهم يدوياً</p>
                        </div>
                        <button type="button" onclick="closeModal('batchReceiptsModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                    </div>

                    <form action="{{ route('office.vouchers.batch_store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                            <div class="md:col-span-5">
                                <label class="block text-xs font-bold text-emerald-950 mb-1">👑 اختر المالك (لتعبئة جميع مستأجريه ومبالغهم تلقائياً)</label>
                                <select id="batch_owner_selector" onchange="populateBatchRowsByOwner(this.value)" class="w-full rounded-xl border border-emerald-300 px-3 py-2 text-xs bg-white font-bold">
                                    <option value="">-- اختر المالك لتعبئة مستأجريه ومبالغهم --</option>
                                    <?php foreach (($owners ?? collect()) as $ow) { ?>
                                        <option value="{{ $ow->id }}">{{ $ow->name }} ({{ $ow->phone }})</option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="md:col-span-4">
                                <label class="block text-xs font-bold text-emerald-950 mb-1">🏢 أو اختر العقار</label>
                                <select id="batch_property_selector" onchange="populateBatchRowsByProperty(this.value)" class="w-full rounded-xl border border-emerald-300 px-3 py-2 text-xs bg-white font-bold">
                                    <option value="">-- جميع العقارات --</option>
                                    <?php foreach (($properties ?? collect()) as $pr) { ?>
                                        <option value="{{ $pr->id }}" data-owner="{{ $pr->owner_id }}">{{ $pr->title }} ({{ $pr->owner_name }})</option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-xs font-bold text-slate-700 mb-1">📅 تاريخ السندات *</label>
                                <input type="date" name="voucher_date" value="{{ date('Y-m-d') }}" required class="w-full rounded-xl border px-3 py-2 text-xs bg-white font-mono">
                            </div>
                        </div>

                        <div id="batchReceiptsRowsContainer" class="space-y-2.5">
                            <div class="grid grid-cols-12 gap-2 items-end p-3 rounded-2xl bg-slate-50 border batch-receipt-row">
                                <div class="col-span-5">
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">المستأجر (جدول بحث موحد) *</label>
                                    <select name="tenant_ids[]" required class="w-full rounded-xl border px-2.5 py-2 text-xs bg-white font-bold">
                                        <option value="">-- اختر المستأجر من الجدول --</option>
                                        <?php foreach (($tenants ?? collect()) as $tn) { ?>
                                            <option value="{{ $tn->id }}">{{ $tn->name }}</option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-[11px] font-bold text-emerald-700 mb-1">المبلغ (ر.س) *</label>
                                    <input type="number" step="0.01" name="amounts[]" required placeholder="0.00" class="w-full rounded-xl border px-2.5 py-2 text-xs bg-white font-bold text-emerald-700">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">طريقة الدفع</label>
                                    <select name="payment_methods[]" class="w-full rounded-xl border px-2 py-2 text-xs bg-white">
                                        <?php foreach (($paymentMethods ?? collect()) as $pm) { ?>
                                            <option value="{{ $pm->name }}">{{ $pm->name }}</option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-span-3 flex items-end gap-1">
                                    <div class="flex-1">
                                        <label class="block text-[11px] font-bold text-slate-600 mb-1">البيان</label>
                                        <input type="text" name="batch_notes[]" placeholder="دفعة إيجار..." class="w-full rounded-xl border px-2.5 py-2 text-xs bg-white">
                                    </div>
                                    <button type="button" onclick="if(document.querySelectorAll('.batch-receipt-row').length > 1) this.closest('.batch-receipt-row').remove();" class="px-2.5 py-2 rounded-xl bg-red-50 text-red-600 text-xs font-bold" title="حذف السطر">🗑️</button>
                                </div>
                            </div>
                        </div>

                        <button type="button" onclick="addBatchReceiptRow()" class="w-full py-2 rounded-xl border-2 border-dashed border-emerald-500 text-emerald-700 text-xs font-bold">
                            + إضافة مستأجر آخر في القائمة
                        </button>

                        <div class="flex justify-end gap-2 pt-3 border-t">
                            <button type="button" onclick="closeModal('batchReceiptsModal')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                            <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">اعتماد وإصدار جميع السندات</button>
                        </div>
                    </form>
                </div>
            </div>

            <script id="office-portal-master-engine">
                window.currentVoucherNumber = 'Office_Voucher';
                window.currentOfficeInvoiceNo = 'Office_Tax_Invoice';
                window.showVacantPropertiesOnly = false;
                window.officeAllowedTabsMaster = @json($allowedOfficeTabs ?? []);
                window.officeContractsMaster = @json($contracts ?? []);
                window.officeAccountsMaster = @json($accounts ?? []);
                window.officeTenantsMaster = @json($tenants ?? []);
                window.officeOwnersMaster = @json($owners ?? []);
                window.officePropertiesMaster = @json($properties ?? []);
                window.officeUnitsMaster = @json($units ?? []);
                window.officePaymentMethodsMaster = @json($paymentMethods ?? []);
                window.officeInstallmentsMaster = @json($installments ?? []);
                window.officeVouchersMaster = @json($vouchers ?? []);
                window.officeInvoicesMaster = @json($officeInvoices ?? []);

                window.showErpToast = function(msg, type = 'info') {
                    let toast = document.getElementById('erpGlobalToast');
                    if (!toast) {
                        toast = document.createElement('div');
                        toast.id = 'erpGlobalToast';
                        toast.style.cssText = 'position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:99999;padding:12px 22px;border-radius:12px;font-size:13px;font-weight:700;box-shadow:0 10px 25px rgba(0,0,0,0.2);transition:all 0.25s ease;';
                        document.body.appendChild(toast);
                    }
                    toast.style.background = type === 'success' ? '#065f46' : (type === 'error' ? '#991b1b' : '#0f172a');
                    toast.style.color = '#ffffff';
                    toast.innerText = msg;
                    toast.style.display = 'block';
                    clearTimeout(window._erpToastTimer);
                    window._erpToastTimer = setTimeout(() => { toast.style.display = 'none'; }, 4500);
                };

                window.openModal = function(id) {
                    const el = document.getElementById(id);
                    if (el) {
                        el.classList.remove('hidden');
                        el.classList.add('flex');
                        setTimeout(() => {
                            if (typeof window.initUnifiedTablePickers === 'function') {
                                window.initUnifiedTablePickers(el);
                            }
                        }, 40);
                    }
                };

                window.closeModal = function(id) {
                    const el = document.getElementById(id);
                    if (el) {
                        el.classList.add('hidden');
                        el.classList.remove('flex');
                    }
                };

                window.openAccModal = function(id) { window.openModal(id); };
                window.closeAccModal = function(id) { window.closeModal(id); };

                // 1. محرك ترقيم الصفحات المتسلسلة (30 عميل/سجل في كل صفحة 1, 2, 3...) مع البحث الشامل
                window.ERP_PAGE_SIZE = 30;

                window.refreshTablePagination = function(tableOrId, resetToFirstPage = false) {
                    const table = typeof tableOrId === 'string' ? document.getElementById(tableOrId) : tableOrId;
                    if (!table) return;
                    const tbody = table.querySelector('tbody');
                    if (!tbody) return;

                    const allRows = Array.from(tbody.querySelectorAll('tr')).filter(r => !r.querySelector('td[colspan]'));
                    if (allRows.length === 0) return;

                    if (resetToFirstPage || !table._currentPage) {
                        table._currentPage = 1;
                    }

                    const matchedRows = allRows.filter(r => r.dataset.searchMatched !== '0');
                    const totalMatched = matchedRows.length;
                    const totalPages = Math.max(1, Math.ceil(totalMatched / window.ERP_PAGE_SIZE));
                    if (table._currentPage > totalPages) table._currentPage = totalPages;

                    const startIdx = (table._currentPage - 1) * window.ERP_PAGE_SIZE;
                    const endIdx = startIdx + window.ERP_PAGE_SIZE;

                    allRows.forEach(r => { r.style.display = 'none'; });
                    matchedRows.slice(startIdx, endIdx).forEach(r => { r.style.display = ''; });

                    let bar = table._paginationBar;
                    if (!bar) {
                        bar = document.createElement('div');
                        bar.className = 'erp-pagination-bar no-print';
                        table.parentElement.after(bar);
                        table._paginationBar = bar;
                    }

                    if (allRows.length <= window.ERP_PAGE_SIZE && totalMatched === allRows.length) {
                        bar.innerHTML = `<div class="text-slate-500 font-semibold">إجمالي السجلات المعروضة: <b class="text-slate-900 font-mono">${totalMatched}</b> سجل</div><div><span class="erp-page-btn active">1</span></div>`;
                        return;
                    }

                    const fromNum = totalMatched === 0 ? 0 : startIdx + 1;
                    const toNum = Math.min(endIdx, totalMatched);

                    let btnsHtml = `<button type="button" class="erp-page-btn" ${table._currentPage <= 1 ? 'disabled' : ''} data-page="${table._currentPage - 1}">السابق</button>`;
                    for (let p = 1; p <= totalPages; p++) {
                        if (totalPages <= 10 || p === 1 || p === totalPages || Math.abs(p - table._currentPage) <= 2) {
                            btnsHtml += `<button type="button" class="erp-page-btn ${p === table._currentPage ? 'active' : ''}" data-page="${p}">${p}</button>`;
                        } else if (Math.abs(p - table._currentPage) === 3) {
                            btnsHtml += `<span class="px-1 text-slate-400 font-bold">...</span>`;
                        }
                    }
                    btnsHtml += `<button type="button" class="erp-page-btn" ${table._currentPage >= totalPages ? 'disabled' : ''} data-page="${table._currentPage + 1}">التالي</button>`;

                    bar.innerHTML = `
                        <div class="text-slate-600 font-semibold">
                            عرض <b class="text-slate-900 font-mono">${fromNum}</b> إلى <b class="text-slate-900 font-mono">${toNum}</b> من أصل <b class="text-emerald-700 font-mono">${totalMatched}</b> سجل (صفحة ${table._currentPage} من ${totalPages})
                        </div>
                        <div class="flex items-center gap-1.5 flex-wrap">${btnsHtml}</div>
                    `;

                    bar.querySelectorAll('button[data-page]').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const targetPage = parseInt(this.getAttribute('data-page'), 10);
                            if (targetPage >= 1 && targetPage <= totalPages) {
                                table._currentPage = targetPage;
                                window.refreshTablePagination(table, false);
                            }
                        });
                    });
                };

                window.initAllTablesPagination = function() {
                    document.querySelectorAll('main table').forEach(tbl => {
                        if (tbl.classList.contains('erp-picker-table')) return;
                        if (tbl.closest('[id*="Modal"]')) return;
                        const tbody = tbl.querySelector('tbody');
                        if (!tbody) return;
                        const rows = Array.from(tbody.querySelectorAll('tr')).filter(r => !r.querySelector('td[colspan]'));
                        if (rows.length === 0) return;
                        rows.forEach(r => {
                            if (r.dataset.searchMatched === undefined) r.dataset.searchMatched = '1';
                        });
                        window.refreshTablePagination(tbl, false);
                    });
                };

                window.filterTableRows = function(inputId, tbodyId) {
                    const input = document.getElementById(inputId);
                    const tbody = document.getElementById(tbodyId);
                    if (!input || !tbody) return;
                    const q = input.value.trim().toLowerCase();
                    const table = tbody.closest('table');
                    tbody.querySelectorAll('tr').forEach(row => {
                        if (row.querySelector('td[colspan]')) return;
                        const hay = (row.getAttribute('data-search') || row.innerText || '').toLowerCase();
                        const matched = !q || hay.includes(q);
                        row.dataset.searchMatched = matched ? '1' : '0';
                        row.style.display = matched ? '' : 'none';
                    });
                    if (table) window.refreshTablePagination(table, true);
                };

                window.getTenantDueAmount = function(tenantId) {
                    const tId = String(tenantId || '');
                    if (!tId) return 0;
                    const nextInst = (window.officeInstallmentsMaster || []).find(i => String(i.tenant_id) === tId && i.status !== 'paid');
                    if (nextInst) {
                        const rem = Math.max(0, parseFloat(nextInst.amount || 0) - parseFloat(nextInst.paid_amount || 0));
                        if (rem > 0) return rem;
                    }
                    const c = (window.officeContractsMaster || []).find(x => String(x.tenant_id) === tId && x.status === 'active')
                           || (window.officeContractsMaster || []).find(x => String(x.tenant_id) === tId);
                    if (c) {
                        const divisor = c.payment_cycle === 'monthly' ? 12 : (c.payment_cycle === 'quarterly' ? 4 : (c.payment_cycle === 'semi_annual' ? 2 : 1));
                        return Math.round((parseFloat(c.total_rent_amount || 0) / divisor) * 100) / 100;
                    }
                    return 0;
                };

                window.getOwnerSuggestedAmount = function(ownerId, voucherType = 'receipt') {
                    const oId = String(ownerId || '');
                    if (!oId) return 0;
                    const owner = (window.officeOwnersMaster || []).find(o => String(o.id) === oId);
                    const feeRate = parseFloat(owner?.office_management_fee_rate || 5);

                    if (voucherType === 'payment') {
                        const ownerVouchers = (window.officeVouchersMaster || []).filter(v => String(v.owner_id || '') === oId);
                        const rec = ownerVouchers.filter(v => v.voucher_type === 'receipt').reduce((s, v) => s + parseFloat(v.amount || 0), 0);
                        const pay = ownerVouchers.filter(v => v.voucher_type === 'payment').reduce((s, v) => s + parseFloat(v.amount || 0), 0);
                        const net = rec - ((rec * feeRate) / 100) - pay;
                        if (net > 0) return Math.round(net * 100) / 100;
                    }

                    const ownerContracts = (window.officeContractsMaster || []).filter(c => String(c.owner_id) === oId);
                    if (ownerContracts.length > 0) {
                        const firstTenantDue = window.getTenantDueAmount(ownerContracts[0].tenant_id);
                        if (firstTenantDue > 0) return firstTenantDue;
                        return parseFloat(ownerContracts[0].total_rent_amount || 0);
                    }
                    return 0;
                };

                window.populateBatchRowsByOwner = function(ownerId) {
                    const oId = String(ownerId || '');
                    const propSel = document.getElementById('batch_property_selector');
                    if (propSel) {
                        Array.from(propSel.options).forEach(opt => {
                            if (!opt.value) { opt.hidden = false; return; }
                            opt.hidden = (oId && String(opt.getAttribute('data-owner')) !== oId);
                        });
                        propSel.value = '';
                        if (propSel._syncTriggerUI) propSel._syncTriggerUI();
                    }
                    if (!oId) return;
                    const ownerContracts = (window.officeContractsMaster || []).filter(c => String(c.owner_id) === oId);
                    window.buildBatchReceiptRowsForContracts(ownerContracts);
                };

                window.populateBatchRowsByProperty = function(propId) {
                    const pId = String(propId || '');
                    if (!pId) return;
                    const propContracts = (window.officeContractsMaster || []).filter(c => String(c.property_id) === pId);
                    window.buildBatchReceiptRowsForContracts(propContracts);
                };

                window.buildBatchReceiptRowsForContracts = function(contractsList) {
                    const container = document.getElementById('batchReceiptsRowsContainer');
                    if (!container) return;
                    if (!contractsList || contractsList.length === 0) {
                        window.showErpToast('⚠️ لا توجد عقود نشطة مرتبطة بهذا الاختيار حالياً.', 'error');
                        return;
                    }
                    const firstTemplate = container.querySelector('.batch-receipt-row').cloneNode(true);
                    container.innerHTML = '';

                    contractsList.forEach(c => {
                        const row = firstTemplate.cloneNode(true);
                        row.querySelectorAll('.erp-picker-host').forEach(h => h.remove());
                        row.querySelectorAll('select').forEach(s => {
                            delete s.dataset.tablePickerInit;
                            s.style.display = '';
                        });

                        const tSel = row.querySelector('select[name="tenant_ids[]"]');
                        const amtInp = row.querySelector('input[name="amounts[]"]');
                        const noteInp = row.querySelector('input[name="batch_notes[]"]');

                        if (tSel) tSel.value = c.tenant_id;
                        const dueAmt = window.getTenantDueAmount(c.tenant_id);
                        if (amtInp) amtInp.value = dueAmt > 0 ? dueAmt.toFixed(2) : Number(c.total_rent_amount || 0).toFixed(2);
                        if (noteInp) noteInp.value = `تحصيل دفعة إيجار - ${c.property_title} ${c.unit_number ? '(وحدة ' + c.unit_number + ')' : ''} - ${c.tenant_name}`;

                        container.appendChild(row);
                    });

                    window.initUnifiedTablePickers(container);
                    window.showErpToast(`⚡ تم إدراج (${contractsList.length}) مستأجرين مع مبالغهم المستحقة تلقائياً!`, 'success');
                };

                // 2. تصدير ملفات Excel حقيقية بصيغة (.xlsx) بحيث يكون كل عمود في عمود مستقل (A, B, C...)
                window.exportAOAToRealXLSX = function(aoaData, filename, sheetTitle = 'التقرير') {
                    if (!aoaData || aoaData.length === 0) {
                        alert('لا توجد بيانات لتصديرها.');
                        return;
                    }
                    const cleanFilename = (filename || 'Report.xlsx').replace(/\.(csv|xls|xlsx)$/i, '') + '.xlsx';
                    if (typeof XLSX === 'undefined') {
                        alert('جاري تحميل مكتبة الإكسل، يرجى المحاولة بعد ثانية.');
                        return;
                    }
                    const ws = XLSX.utils.aoa_to_sheet(aoaData);
                    ws['!views'] = [{ rightToLeft: true }];

                    const colWidths = [];
                    aoaData.forEach(row => {
                        (row || []).forEach((cell, colIdx) => {
                            const len = String(cell ?? '').length;
                            colWidths[colIdx] = Math.max(colWidths[colIdx] || 14, Math.min(len + 6, 42));
                        });
                    });
                    ws['!cols'] = colWidths.map(w => ({ wch: w }));

                    const wb = XLSX.utils.book_new();
                    wb.Workbook = { Views: [{ RTL: true }] };
                    XLSX.utils.book_append_sheet(wb, ws, sheetTitle.slice(0, 30));
                    XLSX.writeFile(wb, cleanFilename);
                    window.showErpToast('📊 تم تحميل ملف الإكسل (.xlsx) بأعمدة منظمة بنجاح', 'success');
                };

                window.exportTableToCSV = function(tableId, filename) {
                    if (tableId === 'standaloneAccountStatementTable' && typeof window.exportActiveAccountStatementXLSX === 'function') {
                        window.exportActiveAccountStatementXLSX(filename);
                        return;
                    }
                    const table = document.getElementById(tableId);
                    if (!table) {
                        alert('الجدول المطلوب غير موجود في الصفحة الحالية.');
                        return;
                    }
                    const skipHeaders = ['إجراءات', 'إجراء', 'الإجراءات', 'حذف', 'تعديل', 'تحميل السند', 'تحميل السند (pdf / excel)', 'إجراءات التحصيل والإنذار', 'التوثيق في إيجار والإجراءات'];
                    const skipColIndices = new Set();
                    const aoa = [];

                    const headerRow = table.querySelector('thead tr');
                    if (headerRow) {
                        const headers = [];
                        headerRow.querySelectorAll('th').forEach((th, idx) => {
                            const text = (th.innerText || '').replace(/\s+/g, ' ').trim();
                            if (skipHeaders.some(sh => text.toLowerCase().includes(sh.toLowerCase()))) {
                                skipColIndices.add(idx);
                            } else {
                                headers.push(text);
                            }
                        });
                        if (headers.length > 0) aoa.push(headers);
                    }

                    table.querySelectorAll('tbody tr, tfoot tr').forEach(tr => {
                        if (tr.dataset.searchMatched === '0') return;
                        const cells = tr.querySelectorAll('td, th');
                        if (cells.length <= 1) return;
                        const rowData = [];
                        cells.forEach((td, idx) => {
                            if (skipColIndices.has(idx)) return;
                            let text = (td.innerText || '').replace(/\s+/g, ' ').trim();
                            rowData.push(text);
                        });
                        if (rowData.length > 0) aoa.push(rowData);
                    });

                    window.exportAOAToRealXLSX(aoa, filename, 'كشف البيانات');
                };

                window.directDownloadSingleVoucherExcel = function(v) {
                    const aoa = [
                        ['رقم السند', 'نوع السند', 'التاريخ', 'المبلغ (ر.س)', 'الطرف المرتبط (المستأجر / المالك)', 'طريقة الدفع', 'البيان والتفاصيل', 'المكتب المُصدِر'],
                        [v.number || '', v.type || '', v.date || '', v.amount || '', v.party || '', v.payment_method || v.method || '', v.notes || '', v.issuer || '{{ addslashes($office->name) }}']
                    ];
                    window.exportAOAToRealXLSX(aoa, (v.number || 'Voucher') + '.xlsx', 'سند مالي');
                };

                window.exportCurrentTaxInvoiceExcel = function(prefix) {
                    const p = prefix || 'oti';
                    const getVal = id => document.getElementById(p + '_' + id)?.innerText?.trim() || '';
                    const aoa = [
                        ['نوع الفاتورة', 'رقم الفاتورة', 'تاريخ الإصدار', 'اسم المورّد (البائع)', 'الرقم الضريبي للمورّد', 'اسم العميل (المشتري)', 'الرقم الضريبي للعميل', 'البيان', 'المبلغ قبل الضريبة', 'ضريبة القيمة المضافة', 'الإجمالي شامل الضريبة'],
                        [getVal('type'), getVal('number'), getVal('date'), getVal('seller_name'), getVal('seller_tax'), getVal('buyer_name'), getVal('buyer_tax'), getVal('desc'), getVal('subtotal'), getVal('vat'), getVal('total')]
                    ];
                    window.exportAOAToRealXLSX(aoa, (getVal('number') || 'Tax_Invoice') + '.xlsx', 'فاتورة ضريبية');
                };

                // 3. إرسال ملفات الـ PDF الأصلية عبر الواتساب (بدون كلام مكتوب أو روابط)
                window.sendElementAsOriginalPdfWhatsapp = async function(elementOrId, filename, targetPhone, docType = 'general') {
                    const el = typeof elementOrId === 'string' ? document.getElementById(elementOrId) : elementOrId;
                    if (!el) {
                        alert('عنصر الطباعة غير جاهز.');
                        return;
                    }
                    let phone = (targetPhone || '').trim();
                    if (!phone) {
                        phone = prompt('أدخل رقم جوال المستلم لإرسال ملف الـ PDF الأصلي عبر الواتساب (مثال: 05XXXXXXXX):', '');
                        if (!phone) return;
                    }

                    const cleanFile = (filename || 'Document.pdf').replace(/\.pdf$/i, '') + '.pdf';
                    window.showErpToast('⏳ جاري توليد ملف الـ PDF الأصلي وإرساله كمرفق عبر الواتساب...', 'info');

                    try {
                        const pdfDataUri = await html2pdf().set({
                            margin: 8,
                            filename: cleanFile,
                            image: { type: 'jpeg', quality: 0.98 },
                            html2canvas: { scale: 2, useCORS: true },
                            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                        }).from(el).outputPdf('datauristring');

                        const res = await fetch("{{ route('erp.whatsapp.send_raw_pdf') }}", {
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
                                doc_type: docType,
                                office_id: '{{ $office->id }}'
                            })
                        });
                        const data = await res.json();
                        if (data && data.success) {
                            window.showErpToast('✅ ' + (data.message || 'تم إرسال ملف الـ PDF الأصلي عبر الواتساب بنجاح!'), 'success');
                        } else {
                            await html2pdf().set({
                                margin: 8,
                                filename: cleanFile,
                                image: { type: 'jpeg', quality: 0.98 },
                                html2canvas: { scale: 2, useCORS: true },
                                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                            }).from(el).save();
                            window.showErpToast('📥 تم تجهيز وتحميل ملف الـ PDF الأصلي (' + cleanFile + '). لتفعيله تلقائياً عبر الواتساب تأكد من إدخال التوكن في الإعدادات.', 'info');
                        }
                    } catch (err) {
                        window.showErpToast('⚠️ تعذر الاتصال بالخادم أثناء إرسال ملف الـ PDF.', 'error');
                    }
                };

                document.addEventListener('submit', function(e) {
                    const form = e.target;
                    if (!form || !form.action) return;

                    if (form.action.includes('/vouchers/') && form.action.includes('whatsapp')) {
                        e.preventDefault();
                        const match = form.action.match(/\/vouchers\/(\d+)\//);
                        const vId = match ? match[1] : null;
                        const vObj = (window.officeVouchersMaster || []).find(v => String(v.id) === String(vId));
                        if (vObj) {
                            const partyName = vObj.tenant_name || vObj.owner_name || 'عام للمكتب';
                            const phone = vObj.tenant_phone || vObj.owner_phone || '';
                            window.previewOfficialVoucher({
                                number: vObj.voucher_number,
                                type: vObj.voucher_type,
                                amount: Number(vObj.amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                                date: vObj.voucher_date,
                                party: partyName,
                                method: vObj.payment_method || 'حوالة بنكية',
                                notes: vObj.notes || '',
                                issuer: '{{ addslashes($office->name) }}'
                            });
                            setTimeout(() => {
                                window.sendElementAsOriginalPdfWhatsapp('printableVoucherArea', vObj.voucher_number + '.pdf', phone, 'voucher');
                            }, 300);
                        }
                    } else if (form.action.includes('/invoices/') && form.action.includes('whatsapp')) {
                        e.preventDefault();
                        const match = form.action.match(/\/invoices\/(\d+)\//);
                        const invId = match ? match[1] : null;
                        const invObj = (window.officeInvoicesMaster || []).find(i => String(i.id) === String(invId));
                        if (invObj && typeof window.openOfficeTaxInvoiceModal === 'function') {
                            const tenant = (window.officeTenantsMaster || []).find(t => String(t.id) === String(invObj.tenant_id));
                            const owner = (window.officeOwnersMaster || []).find(o => String(o.id) === String(invObj.owner_id));
                            const phone = (tenant && tenant.phone) ? tenant.phone : (owner && owner.phone ? owner.phone : '');
                            window.openOfficeTaxInvoiceModal({
                                number: invObj.invoice_number,
                                type: invObj.invoice_type === 'tax' ? 'فاتورة ضريبية' : 'فاتورة ضريبية مبسطة',
                                date: invObj.issue_date,
                                seller_name: invObj.seller_name || '',
                                seller_tax: invObj.seller_tax_number || '',
                                seller_addr: invObj.seller_address || '',
                                buyer_name: invObj.buyer_name || '',
                                buyer_tax: invObj.buyer_tax_number || '',
                                buyer_addr: invObj.buyer_address || '',
                                desc: invObj.description || '',
                                subtotal: Number(invObj.subtotal || 0).toFixed(2),
                                vat_rate: Number(invObj.vat_rate || 15),
                                vat: Number(invObj.vat_amount || 0).toFixed(2),
                                total: Number(invObj.total_amount || 0).toFixed(2),
                                qr: invObj.zatca_qr || ''
                            });
                            setTimeout(() => {
                                window.sendElementAsOriginalPdfWhatsapp('officeTaxInvoicePrintArea', invObj.invoice_number + '.pdf', phone, 'invoice');
                            }, 350);
                        }
                    }
                });

                // 4. التعرف التلقائي الذكي عند اختيار المستأجر أو المالك
                window.instantFillVoucherByTenant = function(tenantId, formEl) {
                    const container = formEl || document.getElementById('createOfficeVoucherModal') || document;
                    const tSelect = container.querySelector('select[name="tenant_id"]') || document.getElementById('ov_tenant_id');
                    const tId = String(tenantId !== undefined ? tenantId : (tSelect ? tSelect.value : ''));
                    const vTypeSelect = container.querySelector('select[name="voucher_type"]') || document.getElementById('ov_voucher_type');
                    const vType = vTypeSelect ? vTypeSelect.value : 'receipt';

                    if (!tId) return;

                    const tenant = (window.officeTenantsMaster || []).find(t => String(t.id) === tId);
                    const contract = (window.officeContractsMaster || []).find(c => String(c.tenant_id) === tId && c.status === 'active')
                                  || (window.officeContractsMaster || []).find(c => String(c.tenant_id) === tId);
                    const owner = contract ? (window.officeOwnersMaster || []).find(o => String(o.id) === String(contract.owner_id)) : null;

                    if (contract) {
                        const ownerSel = container.querySelector('select[name="owner_id"]');
                        if (ownerSel && contract.owner_id) {
                            ownerSel.value = contract.owner_id;
                            if (ownerSel._syncTriggerUI) ownerSel._syncTriggerUI();
                        }
                        const propSel = container.querySelector('select[name="property_id"]');
                        if (propSel && contract.property_id) {
                            propSel.value = contract.property_id;
                            if (propSel._syncTriggerUI) propSel._syncTriggerUI();
                        }
                        const unitSel = container.querySelector('select[name="property_unit_id"]');
                        if (unitSel && contract.property_unit_id) {
                            unitSel.value = contract.property_unit_id;
                            if (unitSel._syncTriggerUI) unitSel._syncTriggerUI();
                        }
                    }

                    const debitSel = container.querySelector('select[name="debit_account_id"]');
                    const creditSel = container.querySelector('select[name="credit_account_id"]');
                    const pmSel = container.querySelector('select[name="payment_method"]');

                    let cashBankAccId = '';
                    if (pmSel && pmSel.value) {
                        const pmObj = (window.officePaymentMethodsMaster || []).find(p => p.name === pmSel.value || p.code === pmSel.value);
                        if (pmObj && pmObj.account_id && (window.officeAccountsMaster || []).some(a => String(a.id) === String(pmObj.account_id))) {
                            cashBankAccId = pmObj.account_id;
                        }
                    }
                    if (!cashBankAccId) {
                        const defaultBank = (window.officeAccountsMaster || []).find(a => String(a.code) === '1102')
                                         || (window.officeAccountsMaster || []).find(a => String(a.code) === '1101');
                        if (defaultBank) cashBankAccId = defaultBank.id;
                    }

                    if (vType === 'receipt') {
                        if (debitSel && cashBankAccId) {
                            debitSel.value = cashBankAccId;
                            if (debitSel._syncTriggerUI) debitSel._syncTriggerUI();
                        }
                        if (creditSel) {
                            if (tenant && tenant.account_id) creditSel.value = tenant.account_id;
                            else if (owner && owner.account_id) creditSel.value = owner.account_id;
                            if (creditSel._syncTriggerUI) creditSel._syncTriggerUI();
                        }
                    } else {
                        if (debitSel) {
                            if (owner && owner.account_id) debitSel.value = owner.account_id;
                            else if (tenant && tenant.account_id) debitSel.value = tenant.account_id;
                            if (debitSel._syncTriggerUI) debitSel._syncTriggerUI();
                        }
                        if (creditSel && cashBankAccId) {
                            creditSel.value = cashBankAccId;
                            if (creditSel._syncTriggerUI) creditSel._syncTriggerUI();
                        }
                    }

                    const notesInp = container.querySelector('input[name="notes"]') || container.querySelector('input[name="description"]');
                    const amountInp = container.querySelector('input[name="amount"]') || container.querySelector('input[name="subtotal"]');
                    const dueAmt = window.getTenantDueAmount(tId);

                    if (amountInp && dueAmt > 0) {
                        amountInp.value = dueAmt.toFixed(2);
                        amountInp.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                    if (notesInp && tenant) {
                        notesInp.value = (vType === 'receipt' ? 'دفعة إيجار - المستأجر: ' : 'سند صرف - ') + tenant.name + (contract ? (' - ' + contract.property_title) : '');
                    }

                    window.renderAutoRecognitionBadge(container, tenant, owner, contract);
                };

                window.instantFillVoucherByOwner = function(ownerId, formEl) {
                    const container = formEl || document.getElementById('createOfficeVoucherModal') || document;
                    const oSelect = container.querySelector('select[name="owner_id"]') || document.getElementById('ov_owner_id');
                    const oId = String(ownerId !== undefined ? ownerId : (oSelect ? oSelect.value : ''));
                    if (!oId) return;

                    const owner = (window.officeOwnersMaster || []).find(o => String(o.id) === oId);
                    const vTypeSelect = container.querySelector('select[name="voucher_type"]') || document.getElementById('ov_voucher_type');
                    const vType = vTypeSelect ? vTypeSelect.value : 'receipt';
                    const tSelect = container.querySelector('select[name="tenant_id"]');
                    const hasTenant = tSelect && tSelect.value;

                    const debitSel = container.querySelector('select[name="debit_account_id"]');
                    const creditSel = container.querySelector('select[name="credit_account_id"]');

                    if (owner && owner.account_id) {
                        if (vType === 'payment' && debitSel) {
                            debitSel.value = owner.account_id;
                            if (debitSel._syncTriggerUI) debitSel._syncTriggerUI();
                        } else if (vType === 'receipt' && !hasTenant && creditSel) {
                            creditSel.value = owner.account_id;
                            if (creditSel._syncTriggerUI) creditSel._syncTriggerUI();
                        }
                    }

                    const ownerProps = (window.officePropertiesMaster || []).filter(p => String(p.owner_id) === oId);
                    const propSel = container.querySelector('select[name="property_id"]');
                    if (propSel && ownerProps.length === 1 && !propSel.value) {
                        propSel.value = ownerProps[0].id;
                        if (propSel._syncTriggerUI) propSel._syncTriggerUI();
                    }

                    if (!hasTenant && owner) {
                        const amountInp = container.querySelector('input[name="amount"]') || container.querySelector('input[name="subtotal"]');
                        const notesInp = container.querySelector('input[name="notes"]') || container.querySelector('input[name="description"]');
                        const suggestedAmt = window.getOwnerSuggestedAmount(oId, vType);
                        if (amountInp && suggestedAmt > 0) {
                            amountInp.value = suggestedAmt.toFixed(2);
                            amountInp.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                        if (notesInp && !notesInp.value) {
                            notesInp.value = (vType === 'payment' ? 'تصفية وصرف مستحقات المالك: ' : 'سند قبض لحساب المالك: ') + owner.name;
                        }
                    }

                    const tenant = hasTenant ? (window.officeTenantsMaster || []).find(t => String(t.id) === String(tSelect.value)) : null;
                    const contract = hasTenant ? (window.officeContractsMaster || []).find(c => String(c.tenant_id) === String(tSelect.value)) : null;
                    window.renderAutoRecognitionBadge(container, tenant, owner, contract);
                };

                window.renderAutoRecognitionBadge = function(container, tenant, owner, contract) {
                    if (!container || container === document) return;
                    let badge = container.querySelector('.smart-auto-link-badge');
                    if (!badge) {
                        badge = document.createElement('div');
                        badge.className = 'smart-auto-link-badge p-3.5 rounded-2xl bg-slate-900 text-white text-xs space-y-1.5 my-2 shadow-sm';
                        const form = container.tagName === 'FORM' ? container : container.querySelector('form');
                        if (form) form.insertBefore(badge, form.firstChild);
                    }
                    const items = [];
                    if (tenant) items.push(`<span>👤 المستأجر: <b class="text-emerald-300">${tenant.name}</b> (حساب الشجرة: <span class="font-mono text-sky-300">${tenant.account_code || 'مرتبط'}</span>)</span>`);
                    if (owner) items.push(`<span>👑 المالك المرتبط: <b class="text-amber-300">${owner.name}</b> (حساب الأمانات: <span class="font-mono text-amber-200">${owner.account_code || 'مرتبط'}</span>)</span>`);
                    if (contract) items.push(`<span>🏢 العقد والعقار: <b>${contract.property_title}</b> ${contract.unit_number ? '(وحدة ' + contract.unit_number + ')' : ''} — عقد <span class="font-mono text-emerald-300">${contract.contract_number}</span></span>`);

                    if (items.length > 0) {
                        badge.innerHTML = `<div class="text-[11px] font-bold text-emerald-400 border-b border-slate-700 pb-1">🤖 الربط الذكي التلقائي بالشجرة والعقد:</div><div class="flex flex-wrap gap-x-4 gap-y-1">${items.join('')}</div>`;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                };

                // 5. بناء جدول البحث والاختيار الموحد العصري (واضح 100% داخل النوافذ بدون قص للأعمدة)
                window.getPickerTableConfig = function(sel) {
                    const name = sel.getAttribute('name') || '';
                    const id = sel.getAttribute('id') || '';

                    if (name === 'tenant_id' || name === 'tenant_ids[]') {
                        return {
                            placeholder: '👤 اضغط للبحث واختيار المستأجر من الجدول الموحد...',
                            headers: ['اسم المستأجر والجوال', 'حساب الشجرة', 'العقار والوحدة', 'المالك والإيجار'],
                            getRowCells: (opt) => {
                                const t = (window.officeTenantsMaster || []).find(x => String(x.id) === String(opt.value));
                                const c = (window.officeContractsMaster || []).find(x => String(x.tenant_id) === String(opt.value));
                                if (!t) return [opt.text, '-', '-', '-'];
                                const dueAmt = window.getTenantDueAmount(t.id);
                                return [
                                    `<div class="font-bold text-slate-900">${t.name}</div><div class="font-mono text-[10px] text-emerald-700" dir="ltr">${t.phone || '-'}</div>`,
                                    `<span class="px-1.5 py-0.5 rounded bg-slate-100 font-mono font-bold text-slate-800">${t.account_code || 'تلقائي'}</span>`,
                                    c ? `<div class="font-semibold">${c.property_title}</div><div class="text-[10px] text-slate-500">${c.unit_number ? 'وحدة ' + c.unit_number : 'كامل العقار'}</div>` : '-',
                                    c ? `<div class="font-bold text-amber-800">${c.owner_name}</div><div class="font-mono text-[10px] text-emerald-700">${Number(dueAmt || c.total_rent_amount || 0).toLocaleString()} ر.س</div>` : '-'
                                ];
                            },
                            getTriggerLabel: (opt) => {
                                const t = (window.officeTenantsMaster || []).find(x => String(x.id) === String(opt.value));
                                const c = (window.officeContractsMaster || []).find(x => String(x.tenant_id) === String(opt.value));
                                if (!t) return opt.text;
                                return `👤 ${t.name} [${t.account_code || 'شجرة'}] ${c ? '— المالك: ' + c.owner_name : ''}`;
                            }
                        };
                    }

                    if (name === 'owner_id' || id === 'pro_settle_owner_id' || id === 'settle_owner_id' || id === 'batch_owner_selector') {
                        return {
                            placeholder: '👑 اضغط للبحث واختيار المالك من الجدول الموحد...',
                            headers: ['اسم المالك والجوال', 'رقم الهوية', 'حساب الأمانات بالشجرة', 'نسبة الإدارة'],
                            getRowCells: (opt) => {
                                const o = (window.officeOwnersMaster || []).find(x => String(x.id) === String(opt.value));
                                if (!o) return [opt.text, '-', '-', '-'];
                                return [
                                    `<div class="font-bold text-slate-900">${o.name}</div><div class="font-mono text-[10px] text-emerald-700" dir="ltr">${o.phone || '-'}</div>`,
                                    `<span class="font-mono text-slate-600">${o.national_id || '-'}</span>`,
                                    `<span class="px-1.5 py-0.5 rounded bg-amber-50 text-amber-900 font-mono font-bold">${o.account_code || 'تلقائي'}</span>`,
                                    `<b class="text-emerald-700">${o.office_management_fee_rate || 5}%</b>`
                                ];
                            },
                            getTriggerLabel: (opt) => {
                                const o = (window.officeOwnersMaster || []).find(x => String(x.id) === String(opt.value));
                                if (!o) return opt.text;
                                return `👑 ${o.name} [${o.account_code || 'شجرة'}] (${o.phone || ''})`;
                            }
                        };
                    }

                    if (name === 'property_id' || id === 'pro_settle_property_id' || id === 'settle_property_id' || id === 'batch_property_selector') {
                        return {
                            placeholder: '🏢 اضغط للبحث واختيار العقار من الجدول الموحد...',
                            headers: ['مسمى العقار والنوع', 'اسم المالك', 'رقم الصك', 'العنوان'],
                            getRowCells: (opt) => {
                                const p = (window.officePropertiesMaster || []).find(x => String(x.id) === String(opt.value));
                                if (!p) return [opt.text, '-', '-', '-'];
                                return [
                                    `<div class="font-bold text-slate-900">${p.title}</div><span class="text-[10px] text-emerald-700 font-bold">${p.type_name || '-'}</span>`,
                                    `<b>${p.owner_name || '-'}</b>`,
                                    `<span class="font-mono">${p.deed_number || '-'}</span>`,
                                    `${p.city || ''} - ${p.district || ''}`
                                ];
                            },
                            getTriggerLabel: (opt) => {
                                const p = (window.officePropertiesMaster || []).find(x => String(x.id) === String(opt.value));
                                return p ? `🏢 ${p.title} (المالك: ${p.owner_name})` : opt.text;
                            }
                        };
                    }

                    if (name === 'property_unit_id') {
                        return {
                            placeholder: '🚪 اضغط لاختيار الوحدة العقارية من الجدول الموحد...',
                            headers: ['رقم الوحدة', 'العقار التابع له', 'الإيجار السنوي', 'الحالة'],
                            getRowCells: (opt) => {
                                const u = (window.officeUnitsMaster || []).find(x => String(x.id) === String(opt.value));
                                if (!u) return [opt.text, '-', '-', '-'];
                                return [
                                    `<b class="text-slate-900">${u.unit_number}</b> (دور ${u.floor_number ?? '-'})`,
                                    `${u.property_title || '-'}`,
                                    `<span class="font-mono font-bold text-emerald-700">${Number(u.default_rent_price || 0).toLocaleString()} ر.س</span>`,
                                    u.status === 'vacant' ? '<span class="text-emerald-700 font-bold">شاغرة</span>' : '<span class="text-blue-700 font-bold">مؤجرة</span>'
                                ];
                            },
                            getTriggerLabel: (opt) => opt.text
                        };
                    }

                    if (name.includes('account_id') || name === 'account_ids[]' || id.includes('acc_filter') || id.includes('account_select') || id.includes('expense_select')) {
                        return {
                            placeholder: '🌳 اضغط للبحث واختيار الحساب من الشجرة...',
                            headers: ['كود الحساب', 'اسم الحساب في الشجرة', 'النوع', 'الرصيد'],
                            getRowCells: (opt) => {
                                const a = (window.officeAccountsMaster || []).find(x => String(x.id) === String(opt.value));
                                if (!a) return ['-', opt.text, '-', '-'];
                                return [
                                    `<span class="px-1.5 py-0.5 rounded bg-slate-900 text-white font-mono font-bold">${a.code}</span>`,
                                    `<b class="text-slate-900">${a.name}</b>`,
                                    `<span class="text-slate-500">${a.type || '-'}</span>`,
                                    `<span class="font-mono font-bold text-emerald-700">${Number(a.balance || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}</span>`
                                ];
                            },
                            getTriggerLabel: (opt) => {
                                const a = (window.officeAccountsMaster || []).find(x => String(x.id) === String(opt.value));
                                return a ? `🌳 [${a.code}] ${a.name}` : opt.text;
                            }
                        };
                    }

                    if (id === 'legal_contract_select' || name === 'lease_contract_id') {
                        return {
                            placeholder: '⚖️ اضغط للبحث واختيار العقد / المستأجر من الجدول الموحد...',
                            headers: ['المستأجر والجوال', 'العقار والوحدة', 'المالك', 'رقم العقد والإيجار'],
                            getRowCells: (opt) => {
                                const c = (window.officeContractsMaster || []).find(x => String(x.id) === String(opt.value));
                                if (!c) return [opt.text, '-', '-', '-'];
                                return [
                                    `<div class="font-bold text-slate-900">${c.tenant_name}</div><div class="font-mono text-[10px] text-emerald-700" dir="ltr">${c.tenant_phone || '-'}</div>`,
                                    `${c.property_title} ${c.unit_number ? '(' + c.unit_number + ')' : ''}`,
                                    `<b>${c.owner_name}</b>`,
                                    `<div class="font-mono font-bold">${c.contract_number}</div><div class="font-mono text-[10px] text-emerald-700">${Number(c.total_rent_amount || 0).toLocaleString()} ر.س</div>`
                                ];
                            },
                            getTriggerLabel: (opt) => opt.text
                        };
                    }

                    if (id === 'pro_sa_party_filter' || id === 'sa_party_filter' || id === 'stmt_party_select') {
                        return {
                            placeholder: '👥 اضغط للبحث واختيار المالك أو المستأجر من الجدول الموحد...',
                            headers: ['الصفة', 'الاسم الكامل', 'رقم الجوال', 'حساب الشجرة'],
                            getRowCells: (opt) => {
                                const val = String(opt.value || '');
                                if (val.startsWith('owner_')) {
                                    const o = (window.officeOwnersMaster || []).find(x => String(x.id) === val.replace('owner_', ''));
                                    return ['<span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-900 font-bold">👑 مالك</span>', `<b>${o ? o.name : opt.text}</b>`, `<span class="font-mono" dir="ltr">${o?.phone || '-'}</span>`, `<span class="font-mono font-bold">${o?.account_code || '-'}</span>`];
                                }
                                if (val.startsWith('tenant_')) {
                                    const t = (window.officeTenantsMaster || []).find(x => String(x.id) === val.replace('tenant_', ''));
                                    return ['<span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-900 font-bold">👤 مستأجر</span>', `<b>${t ? t.name : opt.text}</b>`, `<span class="font-mono" dir="ltr">${t?.phone || '-'}</span>`, `<span class="font-mono font-bold">${t?.account_code || '-'}</span>`];
                                }
                                return ['الكل', opt.text, '-', '-'];
                            },
                            getTriggerLabel: (opt) => opt.text
                        };
                    }

                    return null;
                };

                window.initUnifiedTablePickers = function(rootEl) {
                    const scope = rootEl || document;
                    scope.querySelectorAll('select').forEach(sel => {
                        if (sel.dataset.tablePickerInit === '1') return;
                        const cfg = window.getPickerTableConfig(sel);
                        if (!cfg) return;

                        sel.dataset.tablePickerInit = '1';
                        sel.style.display = 'none';

                        const host = document.createElement('div');
                        host.className = 'erp-picker-host relative w-full';
                        sel.parentNode.insertBefore(host, sel);

                        const trigger = document.createElement('div');
                        trigger.className = 'erp-picker-trigger';

                        const panel = document.createElement('div');
                        panel.className = 'erp-picker-panel hidden';
                        panel.innerHTML = `
                            <div class="p-2 bg-slate-100 border-b border-slate-200 flex items-center gap-2">
                                <input type="text" class="erp-picker-search w-full rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold focus:outline-none focus:border-slate-900" placeholder="🔍 اكتب للبحث الفوري في جميع الصفحات (الاسم، الجوال، الحساب، العقار)...">
                                <span class="erp-picker-count px-2 py-1 rounded bg-slate-900 text-white font-mono text-[10px] font-bold shrink-0">0</span>
                            </div>
                            <div class="erp-picker-table-wrap">
                                <table class="erp-picker-table">
                                    <thead><tr>${cfg.headers.map(h => `<th>${h}</th>`).join('')}</tr></thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        `;

                        host.appendChild(trigger);
                        host.appendChild(panel);

                        const searchInp = panel.querySelector('.erp-picker-search');
                        const countBadge = panel.querySelector('.erp-picker-count');
                        const tbody = panel.querySelector('tbody');

                        const updateTriggerText = () => {
                            const opt = sel.options[sel.selectedIndex];
                            if (opt && opt.value !== '') {
                                trigger.innerHTML = `<span class="truncate text-slate-900 font-bold">${cfg.getTriggerLabel(opt)}</span><span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] shrink-0">تغيير ▾</span>`;
                            } else {
                                trigger.innerHTML = `<span class="truncate text-slate-400 font-normal">${cfg.placeholder}</span><span class="px-2 py-0.5 rounded bg-slate-900 text-white text-[10px] shrink-0">جدول البحث ▾</span>`;
                            }
                        };

                        const renderRows = (qText) => {
                            const q = (qText || '').trim().toLowerCase();
                            tbody.innerHTML = '';
                            let count = 0;

                            Array.from(sel.options).forEach(opt => {
                                if (opt.hidden) return;
                                if (opt.value === '') {
                                    const trEmpty = document.createElement('tr');
                                    trEmpty.innerHTML = `<td colspan="${cfg.headers.length}" class="text-slate-400 italic">${opt.text}</td>`;
                                    trEmpty.addEventListener('mousedown', (e) => {
                                        e.preventDefault();
                                        sel.value = '';
                                        updateTriggerText();
                                        panel.classList.add('hidden');
                                        trigger.classList.remove('active');
                                        sel.dispatchEvent(new Event('change', { bubbles: true }));
                                    });
                                    tbody.appendChild(trEmpty);
                                    return;
                                }

                                const cells = cfg.getRowCells(opt);
                                const plainHay = (cells.join(' ') + ' ' + opt.text).replace(/<[^>]*>/g, ' ').toLowerCase();
                                if (!q || plainHay.includes(q)) {
                                    count++;
                                    const tr = document.createElement('tr');
                                    if (String(sel.value) === String(opt.value)) tr.classList.add('selected-row');
                                    tr.innerHTML = cells.map(c => `<td>${c}</td>`).join('');
                                    tr.addEventListener('mousedown', (e) => {
                                        e.preventDefault();
                                        sel.value = opt.value;
                                        updateTriggerText();
                                        panel.classList.add('hidden');
                                        trigger.classList.remove('active');
                                        sel.dispatchEvent(new Event('change', { bubbles: true }));
                                    });
                                    tbody.appendChild(tr);
                                }
                            });

                            countBadge.innerText = count;
                            if (count === 0) {
                                tbody.innerHTML = `<tr><td colspan="${cfg.headers.length}" class="text-center py-4 text-slate-400">لا توجد نتائج مطابقة لـ "${qText}"</td></tr>`;
                            }
                        };

                        trigger.addEventListener('click', (e) => {
                            e.stopPropagation();
                            document.querySelectorAll('.erp-picker-panel').forEach(p => {
                                if (p !== panel) p.classList.add('hidden');
                            });
                            const isHidden = panel.classList.contains('hidden');
                            if (isHidden) {
                                panel.classList.remove('hidden');
                                trigger.classList.add('active');
                                searchInp.value = '';
                                renderRows('');
                                setTimeout(() => searchInp.focus(), 30);
                            } else {
                                panel.classList.add('hidden');
                                trigger.classList.remove('active');
                            }
                        });

                        searchInp.addEventListener('input', () => renderRows(searchInp.value));
                        searchInp.addEventListener('click', (e) => e.stopPropagation());

                        sel._syncTriggerUI = updateTriggerText;
                        sel._syncSearchInput = updateTriggerText;

                        sel.addEventListener('change', () => {
                            updateTriggerText();
                            const nameAttr = sel.getAttribute('name') || '';
                            const idAttr = sel.getAttribute('id') || '';
                            const formContainer = sel.closest('form') || sel.closest('[id*="Modal"]') || document;

                            if (nameAttr === 'tenant_id') {
                                window.instantFillVoucherByTenant(sel.value, formContainer);
                                if (typeof window.checkTenantTaxStatus === 'function' && idAttr === 'off_inv_tenant') {
                                    window.checkTenantTaxStatus();
                                }
                            } else if (nameAttr === 'owner_id') {
                                window.instantFillVoucherByOwner(sel.value, formContainer);
                                if (typeof window.fillOwnerTaxInfo === 'function' && idAttr === 'off_inv_owner') {
                                    window.fillOwnerTaxInfo();
                                }
                            } else if (nameAttr === 'property_id') {
                                const propObj = (window.officePropertiesMaster || []).find(p => String(p.id) === String(sel.value));
                                if (propObj && propObj.owner_id) {
                                    const owSel = formContainer.querySelector('select[name="owner_id"]');
                                    if (owSel && String(owSel.value) !== String(propObj.owner_id)) {
                                        owSel.value = propObj.owner_id;
                                        if (owSel._syncTriggerUI) owSel._syncTriggerUI();
                                        window.instantFillVoucherByOwner(propObj.owner_id, formContainer);
                                    }
                                }
                            } else if (nameAttr === 'tenant_ids[]') {
                                const batchRow = sel.closest('.batch-receipt-row');
                                if (batchRow && sel.value) {
                                    const dueAmt = window.getTenantDueAmount(sel.value);
                                    const c = (window.officeContractsMaster || []).find(x => String(x.tenant_id) === String(sel.value));
                                    const amtInp = batchRow.querySelector('input[name="amounts[]"]');
                                    const noteInp = batchRow.querySelector('input[name="batch_notes[]"]');
                                    if (amtInp) {
                                        amtInp.value = dueAmt > 0 ? dueAmt.toFixed(2) : (c ? Number(c.total_rent_amount || 0).toFixed(2) : '');
                                    }
                                    if (noteInp && c) {
                                        noteInp.value = `تحصيل دفعة إيجار - ${c.property_title} - ${c.tenant_name}`;
                                    }
                                }
                            }
                        });

                        updateTriggerText();
                    });
                };

                // 6. السماح بإضافة وحذف المزيد من الصفوف عند تعديل قيد يومي (Edit Journal Entry)
                window.enhanceEditJournalModals = function() {
                    document.querySelectorAll('[id^="editJournalModal_"]').forEach(modal => {
                        if (modal.dataset.rowsEnhanced === '1') return;
                        modal.dataset.rowsEnhanced = '1';
                        const form = modal.querySelector('form');
                        if (!form) return;
                        const rowsContainer = form.querySelector('.space-y-2\\.5');
                        if (!rowsContainer) return;

                        rowsContainer.querySelectorAll('.grid.grid-cols-12').forEach(row => {
                            const lastCol = row.querySelector('.col-span-3');
                            if (lastCol && !row.querySelector('.del-edit-je-row')) {
                                lastCol.className = 'col-span-3 flex items-end gap-1';
                                const inp = lastCol.querySelector('input');
                                const label = lastCol.querySelector('label');
                                const wrap = document.createElement('div');
                                wrap.className = 'flex-1';
                                if (label) wrap.appendChild(label);
                                if (inp) wrap.appendChild(inp);
                                const delBtn = document.createElement('button');
                                delBtn.type = 'button';
                                delBtn.className = 'del-edit-je-row px-2.5 py-1.5 rounded-xl bg-red-50 hover:bg-red-600 text-red-600 hover:text-white text-xs font-bold transition';
                                delBtn.innerText = '🗑️';
                                delBtn.onclick = function() {
                                    if (rowsContainer.querySelectorAll('.grid.grid-cols-12').length <= 2) {
                                        alert('يجب أن يحتوي القيد اليومي على صفين (مدين ودائن) على الأقل.');
                                        return;
                                    }
                                    row.remove();
                                };
                                lastCol.innerHTML = '';
                                lastCol.appendChild(wrap);
                                lastCol.appendChild(delBtn);
                            }
                        });

                        const addBtn = document.createElement('button');
                        addBtn.type = 'button';
                        addBtn.className = 'w-full py-2.5 rounded-2xl border-2 border-dashed border-emerald-500 text-emerald-700 hover:bg-emerald-50 text-xs font-bold transition mt-2';
                        addBtn.innerText = '+ إضافة جدول / صف عملية جديدة في هذا القيد اليومي';
                        addBtn.onclick = function() {
                            const firstRow = rowsContainer.querySelector('.grid.grid-cols-12');
                            if (!firstRow) return;
                            const clone = firstRow.cloneNode(true);
                            clone.querySelectorAll('.erp-picker-host').forEach(h => h.remove());
                            clone.querySelectorAll('select').forEach(s => {
                                delete s.dataset.tablePickerInit;
                                s.style.display = '';
                                s.value = '';
                            });
                            clone.querySelectorAll('input').forEach(inp => {
                                inp.value = inp.type === 'number' ? '0' : '';
                            });
                            const delBtn = clone.querySelector('.del-edit-je-row');
                            if (delBtn) {
                                delBtn.onclick = function() {
                                    if (rowsContainer.querySelectorAll('.grid.grid-cols-12').length <= 2) {
                                        alert('يجب أن يحتوي القيد اليومي على صفين على الأقل.');
                                        return;
                                    }
                                    clone.remove();
                                };
                            }
                            rowsContainer.appendChild(clone);
                            window.initUnifiedTablePickers(clone);
                        };
                        rowsContainer.after(addBtn);
                    });
                };

                // 7. إخفاء التبويبات غير المصرح بها لصاحب المكتب في القائمة الجانبية
                window.enforceAllowedSidebarTabs = function() {
                    const allowed = Array.isArray(window.officeAllowedTabsMaster) ? window.officeAllowedTabsMaster : [];
                    if (allowed.length === 0) return;
                    document.querySelectorAll('aside nav a[href*="tab="]').forEach(a => {
                        try {
                            const url = new URL(a.href, window.location.origin);
                            const tabName = url.searchParams.get('tab');
                            if (tabName && tabName !== 'overview' && !allowed.includes(tabName)) {
                                a.style.display = 'none';
                            }
                        } catch (e) {}
                    });
                };

                // 8. دوال الفلترة المتوافقة مع نظام الصفحات (30 سجل لكل صفحة)
                window.filterOfficePropertiesAdvanced = function() {
                    const q = (document.getElementById('officePropertySearch')?.value || '').trim().toLowerCase();
                    const table = document.getElementById('officePropertiesMainTable');
                    document.querySelectorAll('.office-prop-row').forEach(row => {
                        const text = (row.innerText || '').toLowerCase();
                        const vacantCount = parseInt(row.getAttribute('data-vacant-count') || '0', 10);
                        let show = text.includes(q);
                        if (window.showVacantPropertiesOnly && vacantCount <= 0) show = false;
                        row.dataset.searchMatched = show ? '1' : '0';
                        row.style.display = show ? '' : 'none';
                    });
                    if (table) window.refreshTablePagination(table, true);
                };

                window.toggleVacantPropertiesOnly = function() {
                    window.showVacantPropertiesOnly = !window.showVacantPropertiesOnly;
                    const btn = document.getElementById('btnToggleVacantFilter');
                    const txt = document.getElementById('vacantFilterText');
                    if (btn && txt) {
                        if (window.showVacantPropertiesOnly) {
                            btn.className = 'px-4 py-2.5 rounded-2xl bg-emerald-600 text-white text-xs font-bold transition flex items-center gap-1.5 border border-emerald-600';
                            txt.innerText = 'عرض جميع العقارات (إلغاء فلتر الشواغر)';
                        } else {
                            btn.className = 'px-4 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition flex items-center gap-1.5 border border-slate-300';
                            txt.innerText = 'عرض العقارات ذات الوحدات الشاغرة فقط';
                        }
                    }
                    window.filterOfficePropertiesAdvanced();
                };

                window.exportGeneralPropertiesPDF = function() {
                    window.closeModal('propertiesPdfExportChoiceModal');
                    window.exportElementToPDF('officePropertiesPrintableWrap', 'تقرير_العقارات_والوحدات.pdf');
                };

                window.exportDetailedBuildingsZipPDF = async function() {
                    const rows = Array.from(document.querySelectorAll('.office-prop-row')).filter(r => r.dataset.searchMatched !== '0');
                    if (rows.length === 0) {
                        alert('لا توجد عقارات معروضة حالياً لتصديرها.');
                        return;
                    }
                    if (typeof JSZip === 'undefined') {
                        window.exportGeneralPropertiesPDF();
                        return;
                    }
                    const progressBox = document.getElementById('zipExportProgressBox');
                    const progressText = document.getElementById('zipExportProgressText');
                    if (progressBox) progressBox.classList.remove('hidden');

                    const zip = new JSZip();
                    for (let i = 0; i < rows.length; i++) {
                        const r = rows[i];
                        const title = r.getAttribute('data-prop-title') || ('Building_' + (i + 1));
                        const owner = r.getAttribute('data-prop-owner') || '-';
                        const deed = r.getAttribute('data-prop-deed') || '-';
                        const address = r.getAttribute('data-prop-address') || '-';
                        let units = [];
                        try { units = JSON.parse(r.getAttribute('data-vacant-units') || '[]'); } catch (e) {}

                        if (progressText) progressText.innerText = `⏳ جاري تجهيز ملف (${i + 1} من ${rows.length}): ${title}...`;

                        const tempDiv = document.createElement('div');
                        tempDiv.dir = 'rtl';
                        tempDiv.style.padding = '24px';
                        tempDiv.style.fontFamily = "'Readex Pro', sans-serif";
                        tempDiv.style.background = '#ffffff';
                        let unitsRowsHtml = units.length > 0
                            ? units.map(u => `<tr><td style="padding:8px;border:1px solid #cbd5e1;">${u.unit_number}</td><td style="padding:8px;border:1px solid #cbd5e1;">${u.floor_number}</td><td style="padding:8px;border:1px solid #cbd5e1;">${u.area} م²</td><td style="padding:8px;border:1px solid #cbd5e1;">${u.default_rent_price} ر.س</td></tr>`).join('')
                            : `<tr><td colspan="4" style="padding:12px;text-align:center;border:1px solid #cbd5e1;">لا توجد وحدات شاغرة حالياً</td></tr>`;

                        tempDiv.innerHTML = `
                            <h2 style="font-size:18px;font-weight:bold;margin-bottom:8px;">بيان العقار والوحدات الشاغرة: ${title}</h2>
                            <p style="font-size:12px;margin-bottom:14px;">المالك: ${owner} | الصك: ${deed} | العنوان: ${address}</p>
                            <table style="width:100%;border-collapse:collapse;font-size:12px;text-align:right;">
                                <thead><tr style="background:#f1f5f9;"><th style="padding:8px;border:1px solid #cbd5e1;">رقم الوحدة</th><th style="padding:8px;border:1px solid #cbd5e1;">الدور</th><th style="padding:8px;border:1px solid #cbd5e1;">المساحة</th><th style="padding:8px;border:1px solid #cbd5e1;">الإيجار السنوي</th></tr></thead>
                                <tbody>${unitsRowsHtml}</tbody>
                            </table>
                        `;
                        const pdfBlob = await html2pdf().set({
                            margin: 10,
                            image: { type: 'jpeg', quality: 0.98 },
                            html2canvas: { scale: 2, useCORS: true },
                            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                        }).from(tempDiv).outputPdf('blob');

                        zip.file(title.replace(/[\\/:*?"<>|]/g, '_') + '.pdf', pdfBlob);
                    }

                    const zipContent = await zip.generateAsync({ type: 'blob' });
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(zipContent);
                    link.download = 'تقارير_العقارات_التفصيلية.zip';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    if (progressBox) progressBox.classList.add('hidden');
                    window.closeModal('propertiesPdfExportChoiceModal');
                };

                window.openAddBranchesModal = function(parentId, parentName, parentType) {
                    const parentSel = document.getElementById('branch_parent_id');
                    if (parentSel) {
                        parentSel.value = parentId || '';
                        if (parentSel._syncTriggerUI) parentSel._syncTriggerUI();
                    }
                    const typeSel = document.getElementById('branch_acc_type');
                    if (typeSel && parentType) typeSel.value = parentType;
                    const titleEl = document.getElementById('addBranchesTitle');
                    if (titleEl) titleEl.innerText = parentName ? ('إضافة فرع تحت: ' + parentName) : 'إضافة حسابات وفروع لشجرة المكتب';
                    window.openModal('addBranchesModal');
                };

                window.addAnotherBranchRow = function() {
                    const container = document.getElementById('multiBranchesContainer');
                    if (!container) return;
                    const firstRow = container.querySelector('.branch-row');
                    if (!firstRow) return;
                    const clone = firstRow.cloneNode(true);
                    clone.querySelectorAll('input').forEach(inp => inp.value = '');
                    container.appendChild(clone);
                };

                window.openQuickVoucherModal = function(type, accountId, accountLabel) {
                    const typeInput = document.getElementById('qv_type');
                    if (typeInput) typeInput.value = type;
                    const titleEl = document.getElementById('qv_title');
                    if (titleEl) titleEl.innerText = type === 'receipt' ? '📥 إصدار سند قبض للمكتب' : '📤 إصدار سند صرف للمكتب';
                    const subEl = document.getElementById('qv_subtitle');
                    if (subEl) subEl.innerText = 'الحساب المحدد: ' + (accountLabel || '');
                    const debitSel = document.getElementById('qv_debit');
                    const creditSel = document.getElementById('qv_credit');
                    if (type === 'receipt' && creditSel && accountId) {
                        creditSel.value = accountId;
                        if (creditSel._syncTriggerUI) creditSel._syncTriggerUI();
                    } else if (type === 'payment' && debitSel && accountId) {
                        debitSel.value = accountId;
                        if (debitSel._syncTriggerUI) debitSel._syncTriggerUI();
                    }
                    window.openModal('quickVoucherModal');
                };

                window.autoClassifyExpenseByNotes = function(val) {
                    const modal = document.getElementById('createOfficeVoucherModal');
                    const badge = document.getElementById('autoExpenseClassifierBadge');
                    if (!modal) return;
                    const text = (val || '').trim();
                    const debitSel = modal.querySelector('select[name="debit_account_id"]');
                    if (!debitSel) return;

                    const maintKeywords = ['صيانة', 'سباكة', 'كهرباء', 'مضخة', 'تكييف', 'دهان', 'ترميم', 'نظافة', 'إصلاح', 'اصلاح'];
                    const salaryKeywords = ['راتب', 'رواتب', 'مكافأة', 'عمولة موظف', 'أجور'];

                    let targetCode = '';
                    if (maintKeywords.some(k => text.includes(k))) targetCode = '5101';
                    else if (salaryKeywords.some(k => text.includes(k))) targetCode = '5102';

                    if (targetCode) {
                        const acc = (window.officeAccountsMaster || []).find(a => String(a.code) === targetCode);
                        if (acc) {
                            debitSel.value = acc.id;
                            if (debitSel._syncTriggerUI) debitSel._syncTriggerUI();
                            if (badge) badge.classList.remove('hidden');
                        }
                    } else if (badge) {
                        badge.classList.add('hidden');
                    }
                };

                window.previewOfficialVoucher = function(v) {
                    window.currentVoucherNumber = v.number || 'Office_Voucher';
                    const methodVal = v.payment_method || v.method || 'حوالة بنكية';
                    if (document.getElementById('pdf_issuer')) document.getElementById('pdf_issuer').innerText = v.issuer || '{{ addslashes($office->name) }}';
                    if (document.getElementById('pdf_voucher_title')) document.getElementById('pdf_voucher_title').innerText = (v.type === 'receipt' || v.type === 'سند قبض') ? 'سند قبض رسمي' : 'سند صرف رسمي';
                    if (document.getElementById('pdf_voucher_no')) document.getElementById('pdf_voucher_no').innerText = v.number || '';
                    if (document.getElementById('pdf_date')) document.getElementById('pdf_date').innerText = v.date || '';
                    if (document.getElementById('pdf_method')) document.getElementById('pdf_method').innerText = methodVal;
                    if (document.getElementById('pdf_amount')) document.getElementById('pdf_amount').innerText = v.amount || '0.00';
                    if (document.getElementById('pdf_party')) document.getElementById('pdf_party').innerText = v.party || '';
                    if (document.getElementById('pdf_notes')) document.getElementById('pdf_notes').innerText = v.notes || '';
                    window.openModal('officialVoucherModal');
                };

                window.openOfficialVoucherPreview = window.previewOfficialVoucher;

                window.downloadVoucherPDF = function() {
                    const el = document.getElementById('printableVoucherArea');
                    if (!el) return;
                    html2pdf().set({
                        margin: 10,
                        filename: (window.currentVoucherNumber || 'Voucher') + '.pdf',
                        image: { type: 'jpeg', quality: 0.98 },
                        html2canvas: { scale: 2, useCORS: true },
                        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                    }).from(el).save();
                };

                window.directDownloadSingleVoucherPDF = function(v) {
                    window.previewOfficialVoucher(v);
                    setTimeout(() => { window.downloadVoucherPDF(); }, 250);
                };

                window.addBatchReceiptRow = function() {
                    const container = document.getElementById('batchReceiptsRowsContainer');
                    if (!container) return;
                    const firstRow = container.querySelector('.batch-receipt-row');
                    if (!firstRow) return;
                    const clone = firstRow.cloneNode(true);
                    clone.querySelectorAll('.erp-picker-host').forEach(h => h.remove());
                    clone.querySelectorAll('select').forEach(s => {
                        delete s.dataset.tablePickerInit;
                        s.style.display = '';
                        s.value = '';
                    });
                    clone.querySelectorAll('input').forEach(inp => inp.value = '');
                    container.appendChild(clone);
                    if (typeof window.initUnifiedTablePickers === 'function') {
                        window.initUnifiedTablePickers(clone);
                    }
                };

                window.filterJournalTable = function() {
                    const q = (document.getElementById('journalInstantSearch')?.value || '').trim().toLowerCase();
                    const table = document.getElementById('journalEntriesTable');
                    document.querySelectorAll('.journal-row').forEach(row => {
                        const hay = (row.getAttribute('data-search') || row.innerText || '').toLowerCase();
                        const show = hay.includes(q);
                        row.dataset.searchMatched = show ? '1' : '0';
                        row.style.display = show ? '' : 'none';
                    });
                    if (table) window.refreshTablePagination(table, true);
                };

                window.filterOfficeInvoicesTable = function() {
                    const q = (document.getElementById('officeInvoiceSearch')?.value || '').trim().toLowerCase();
                    const table = document.getElementById('officeInvoicesTable');
                    document.querySelectorAll('.office-invoice-row').forEach(r => {
                        const hay = (r.getAttribute('data-search') || r.innerText || '').toLowerCase();
                        const show = hay.includes(q);
                        r.dataset.searchMatched = show ? '1' : '0';
                        r.style.display = show ? '' : 'none';
                    });
                    if (table) window.refreshTablePagination(table, true);
                };

                window.addMoreJournalTableBlock = function() {
                    const container = document.getElementById('journalTablesContainer');
                    if (!container) return;
                    const blocks = container.querySelectorAll('.journal-table-block');
                    if (blocks.length === 0) return;
                    const clone = blocks[0].cloneNode(true);
                    clone.querySelectorAll('.erp-picker-host').forEach(h => h.remove());
                    clone.querySelectorAll('select').forEach(s => {
                        delete s.dataset.tablePickerInit;
                        s.style.display = '';
                        s.value = '';
                    });
                    const nextIdx = blocks.length + 1;
                    const titleEl = clone.querySelector('.journal-block-title');
                    if (titleEl) titleEl.innerText = 'جدول العملية (' + nextIdx + ') — طرف إضافي';
                    clone.querySelectorAll('input').forEach(inp => {
                        if (inp.type === 'number') inp.value = '0';
                        else inp.value = '';
                    });
                    const removeBtn = clone.querySelector('.remove-journal-row-btn');
                    if (removeBtn) removeBtn.classList.remove('hidden');
                    container.appendChild(clone);
                    if (typeof window.initUnifiedTablePickers === 'function') {
                        window.initUnifiedTablePickers(clone);
                    }
                };

                window.removeJournalTableBlock = function(btn) {
                    const container = document.getElementById('journalTablesContainer');
                    if (!container) return;
                    if (container.querySelectorAll('.journal-table-block').length <= 2) {
                        alert('يجب أن يحتوي القيد اليومي على طرفين على الأقل.');
                        return;
                    }
                    const block = btn.closest('.journal-table-block');
                    if (block) block.remove();
                };

                window.exportJournalPDF = function(entryId, entryNumber) {
                    window.closeModal('printChoiceJournalModal_' + entryId);
                    window.openModal('viewJournalModal_' + entryId);
                    setTimeout(() => {
                        window.exportElementToPDF('printableJournalArea_' + entryId, 'Office_Journal_Entry_' + entryNumber + '.pdf');
                    }, 250);
                };

                window.exportElementToPDF = function(elementId, filename) {
                    const el = document.getElementById(elementId);
                    if (!el) {
                        alert('العنصر المطلوب للطباعة غير موجود.');
                        return;
                    }
                    html2pdf().set({
                        margin: 8,
                        filename: filename || 'Document.pdf',
                        image: { type: 'jpeg', quality: 0.98 },
                        html2canvas: { scale: 2, useCORS: true },
                        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                    }).from(el).save();
                };

                window.exportStatementPDF = function() {
                    window.exportElementToPDF('financialStatementPrintable', 'Office_General_Ledger.pdf');
                };

                window.switchLedgerView = function(view) {
                    ['ledger', 'cards', 'trial', 'income'].forEach(v => {
                        const sec = document.getElementById(v + '_view_section');
                        const btn = document.getElementById('btn_view_' + v);
                        if (sec) sec.classList.toggle('hidden', v !== view);
                        if (btn) {
                            btn.className = (v === view)
                                ? 'px-4 py-2 rounded-2xl bg-emerald-600 text-white text-xs font-bold shadow-sm transition'
                                : 'px-4 py-2 rounded-2xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 text-xs font-bold transition';
                        }
                    });
                };

                window.setLedgerQuickDate = function(period) {
                    const fromEl = document.getElementById('stmt_from_date');
                    const toEl = document.getElementById('stmt_to_date');
                    if (!fromEl || !toEl) return;
                    const now = new Date();
                    const yyyy = now.getFullYear();
                    const mm = String(now.getMonth() + 1).padStart(2, '0');
                    const dd = String(now.getDate()).padStart(2, '0');
                    const today = `${yyyy}-${mm}-${dd}`;

                    if (period === 'today') {
                        fromEl.value = today;
                        toEl.value = today;
                    } else if (period === 'month') {
                        fromEl.value = `${yyyy}-${mm}-01`;
                        toEl.value = today;
                    } else if (period === 'year') {
                        fromEl.value = `${yyyy}-01-01`;
                        toEl.value = `${yyyy}-12-31`;
                    } else {
                        fromEl.value = '';
                        toEl.value = '';
                    }
                    window.applyStatementFilters();
                };

                window.applyStatementFilters = function() {
                    const q = (document.getElementById('ledgerInstantSearch')?.value || '').trim().toLowerCase();
                    const mode = document.getElementById('stmt_filter_mode')?.value || 'all';
                    const expBox = document.getElementById('stmt_specific_expense_box');
                    const accBox = document.getElementById('stmt_specific_account_box');

                    if (expBox) expBox.classList.toggle('hidden', mode !== 'specific_expense');
                    if (accBox) accBox.classList.toggle('hidden', mode !== 'specific_account');

                    const specExp = document.getElementById('stmt_expense_select')?.value || '';
                    const specAcc = document.getElementById('stmt_account_select')?.value || '';
                    const partyVal = document.getElementById('stmt_party_select')?.value || '';
                    const fromDate = document.getElementById('stmt_from_date')?.value || '';
                    const toDate = document.getElementById('stmt_to_date')?.value || '';

                    let debitSum = 0, creditSum = 0;
                    const table = document.getElementById('generalStatementTable');

                    document.querySelectorAll('.stmt-movement-row').forEach(row => {
                        const rSearch = (row.getAttribute('data-search') || '').toLowerCase();
                        const rDate = row.getAttribute('data-date') || '';
                        const rAcc = row.getAttribute('data-account-id') || '';
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

                    if (table) window.refreshTablePagination(table, true);

                    const net = debitSum - creditSum;
                    const fmt = n => Number(n || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    if (document.getElementById('filteredDebitSum')) document.getElementById('filteredDebitSum').innerText = fmt(debitSum);
                    if (document.getElementById('filteredCreditSum')) document.getElementById('filteredCreditSum').innerText = fmt(creditSum);
                    if (document.getElementById('filteredNetSum')) document.getElementById('filteredNetSum').innerText = fmt(net);
                    if (document.getElementById('ledgerClosingBalance')) document.getElementById('ledgerClosingBalance').innerText = fmt(net);
                };

                window.quickFilterByAccount = function(accId) {
                    window.switchLedgerView('ledger');
                    const modeEl = document.getElementById('stmt_filter_mode');
                    const accEl = document.getElementById('stmt_account_select');
                    if (modeEl) modeEl.value = 'specific_account';
                    if (accEl) {
                        accEl.value = accId;
                        if (accEl._syncTriggerUI) accEl._syncTriggerUI();
                    }
                    window.applyStatementFilters();
                };

                window.quickFilterSpecificExpense = function(accId) {
                    window.switchLedgerView('ledger');
                    const modeEl = document.getElementById('stmt_filter_mode');
                    const expEl = document.getElementById('stmt_expense_select');
                    if (modeEl) modeEl.value = 'specific_expense';
                    if (expEl) {
                        expEl.value = accId;
                        if (expEl._syncTriggerUI) expEl._syncTriggerUI();
                    }
                    window.applyStatementFilters();
                };

                document.addEventListener('click', () => {
                    document.querySelectorAll('.erp-picker-panel').forEach(p => p.classList.add('hidden'));
                    document.querySelectorAll('.erp-picker-trigger').forEach(t => t.classList.remove('active'));
                });

                document.addEventListener('DOMContentLoaded', function() {
                    window.enforceAllowedSidebarTabs();
                    window.enhanceEditJournalModals();
                    window.initUnifiedTablePickers(document);
                    window.initAllTablesPagination();
                    if (document.querySelectorAll('.stmt-movement-row').length > 0) {
                        window.applyStatementFilters();
                    }
                    ['officialVoucherModal', 'batchReceiptsModal'].forEach(dupId => {
                        const nodes = document.querySelectorAll('[id="' + dupId + '"]');
                        if (nodes.length > 1) {
                            for (let i = 1; i < nodes.length; i++) nodes[i].remove();
                        }
                    });
                });
            </script>

            <!-- ========================================================================= -->
            <!-- 1. صفحة كشف الحساب وتصفية مستحقات الملاك الشاملة -->
            <!-- ========================================================================= -->
            <?php if (($tab ?? '') === 'account_statement') { ?>
                <div id="masterTabSectionActive" class="space-y-6">
                    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-5 no-print">
                        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4">
                            <div>
                                <h3 class="font-bold text-lg text-slate-900">📑 مركز كشف الحساب وتصفية مستحقات الملاك والمستأجرين</h3>
                                <p class="text-xs text-slate-500 mt-0.5">اختر المالك أو المستأجر أو الحساب من الجدول الموحد لعرض الكشف الفوري، أو تحميله كملف Excel (.xlsx) أو PDF، أو إرساله كملف PDF أصلي عبر الواتساب</p>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <button type="button" onclick="proSwitchStatementMode('owner_settlement')" id="pro_btn_mode_owner"
                                        class="px-4 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold transition">
                                    👑 برنت تصفية حساب مالك (شامل)
                                </button>
                                <button type="button" onclick="proSwitchStatementMode('general_ledger')" id="pro_btn_mode_ledger"
                                        class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-bold transition">
                                    📋 كشف حركات حساب / مستأجر / مالك
                                </button>
                                <button type="button" onclick="proDownloadActiveStatementPDF()"
                                        class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-white text-xs font-bold shadow">
                                    📥 تصدير PDF
                                </button>
                                <button type="button" onclick="exportActiveAccountStatementXLSX('كشف_الحساب_والتصفية.xlsx')"
                                        class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow">
                                    📊 تصدير Excel (.xlsx)
                                </button>
                                <button type="button" onclick="proSendActiveStatementWhatsappPDF()"
                                        class="px-4 py-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-800 hover:text-white border border-emerald-300 text-xs font-bold transition">
                                    💬 إرسال PDF أصلي للواتساب
                                </button>
                            </div>
                        </div>

                        <div id="pro_owner_settlement_filters" class="grid grid-cols-1 md:grid-cols-12 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-200 items-end">
                            <div class="md:col-span-4">
                                <label class="block text-xs font-bold text-slate-800 mb-1">👤 1. اختر المالك من الجدول الموحد *</label>
                                <select id="pro_settle_owner_id" onchange="proOnOwnerChange()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-bold">
                                    <option value="">-- اختر المالك لإصدار التصفية --</option>
                                    <?php foreach (($owners ?? collect()) as $ow) { ?>
                                        <option value="{{ $ow->id }}">{{ $ow->name }} ({{ $ow->phone }})</option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-xs font-bold text-slate-800 mb-1">🏢 2. العقار (اختياري)</label>
                                <select id="pro_settle_property_id" onchange="proRenderOwnerSettlement()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-semibold">
                                    <option value="">-- جميع عقارات المالك --</option>
                                    <?php foreach (($properties ?? collect()) as $pr) { ?>
                                        <option value="{{ $pr->id }}" data-owner="{{ $pr->owner_id }}">{{ $pr->title }} (صك: {{ $pr->deed_number }})</option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-slate-800 mb-1">📅 من تاريخ</label>
                                <input type="date" id="pro_settle_from" value="{{ date('Y-01-01') }}" onchange="proRenderOwnerSettlement()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-mono">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-slate-800 mb-1">📅 إلى تاريخ</label>
                                <input type="date" id="pro_settle_to" value="{{ date('Y-12-31') }}" onchange="proRenderOwnerSettlement()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-mono">
                            </div>
                            <div class="md:col-span-1">
                                <button type="button" onclick="proSetFullYear()" class="w-full py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 text-[11px] font-bold">
                                    الكل
                                </button>
                            </div>
                        </div>

                        <div id="pro_general_statement_filters" class="hidden grid grid-cols-1 md:grid-cols-4 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">🌳 اختر الحساب من الشجرة</label>
                                <select id="pro_sa_acc_filter" onchange="proFilterGeneralStatement('acc')" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-bold">
                                    <option value="">-- جميع الحسابات --</option>
                                    <?php foreach (($accounts ?? collect())->where('is_group', 0) as $acc) { ?>
                                        <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">👥 أو اختر المالك / المستأجر</label>
                                <select id="pro_sa_party_filter" onchange="proFilterGeneralStatement('party')" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-bold">
                                    <option value="">-- جميع الأطراف --</option>
                                    <?php foreach (($owners ?? collect()) as $ow) { ?>
                                        <option value="owner_{{ $ow->id }}">مالك: {{ $ow->name }}</option>
                                    <?php } ?>
                                    <?php foreach (($tenants ?? collect()) as $tn) { ?>
                                        <option value="tenant_{{ $tn->id }}">مستأجر: {{ $tn->name }}</option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">📅 من تاريخ</label>
                                <input type="date" id="pro_sa_from" onchange="proFilterGeneralStatement()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">📅 إلى تاريخ</label>
                                <input type="date" id="pro_sa_to" onchange="proFilterGeneralStatement()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-mono">
                            </div>
                        </div>
                    </div>

                    <div id="pro_owner_settlement_view">
                        <div id="pro_settlement_empty" class="bg-white rounded-3xl border border-slate-200 p-12 text-center space-y-3">
                            <div class="text-4xl">👑</div>
                            <h4 class="font-bold text-base text-slate-900">اختر اسم المالك من جدول البحث بالأعلى لعرض برنت التصفية الشامل</h4>
                            <p class="text-xs text-slate-500 max-w-md mx-auto">يقوم النظام فوراً بجمع عقارات المالك، عقوده، الإيجارات المحصلة، المتأخرات، وخصم عمولة المكتب والمصروفات لإعطائك الصافي الجاهز للتحويل.</p>
                        </div>

                        <div id="pro_ownerSettlementPrintableArea" class="hidden bg-white rounded-3xl border-2 border-slate-800 p-8 space-y-6 shadow-sm text-slate-900">
                            <div class="flex flex-wrap items-start justify-between gap-4 border-b-2 border-slate-900 pb-5">
                                <div>
                                    <h2 class="text-xl font-extrabold text-slate-900">{{ $office->name }}</h2>
                                    <p class="text-xs text-slate-500 mt-1">
                                        رخصة فال: <span class="font-mono font-bold">{{ $office->fal_license ?: '-' }}</span> |
                                        سجل تجاري: <span class="font-mono font-bold">{{ $office->cr_number ?: '-' }}</span> |
                                        جوال: <span class="font-mono font-bold" dir="ltr">{{ $office->phone }}</span>
                                    </p>
                                </div>
                                <div class="text-center px-6 py-2.5 rounded-2xl bg-slate-900 text-white">
                                    <div class="text-base font-extrabold">بيان تصفية حساب مالك عقار</div>
                                    <div id="pro_rep_period" class="text-[11px] font-mono text-emerald-300 mt-0.5">الفترة: الكل</div>
                                </div>
                                <div class="text-left text-xs space-y-1">
                                    <div>تاريخ الإصدار: <b class="font-mono">{{ date('Y-m-d') }}</b></div>
                                    <div>نسبة إدارة الأملاك: <b id="pro_rep_fee_rate" class="font-mono text-amber-700">5%</b></div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
                                <div><span class="text-slate-400 block">اسم المالك:</span><b id="pro_rep_owner_name" class="text-sm text-slate-900">-</b></div>
                                <div><span class="text-slate-400 block">رقم الهوية / الجوال:</span><b id="pro_rep_owner_phone" class="font-mono text-slate-800">-</b></div>
                                <div><span class="text-slate-400 block">البنك والآيبان للتحويل:</span><b id="pro_rep_owner_iban" class="font-mono text-emerald-700" dir="ltr">-</b></div>
                                <div><span class="text-slate-400 block">نطاق العقارات بالتقرير:</span><b id="pro_rep_prop_scope" class="text-slate-900">جميع عقارات المالك</b></div>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-6 gap-3 text-center">
                                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                                    <div class="text-[11px] font-bold text-slate-500">إجمالي عقود الإيجار</div>
                                    <div id="pro_rep_total_rent" class="text-base font-extrabold font-mono text-slate-900 mt-1">0.00</div>
                                </div>
                                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200">
                                    <div class="text-[11px] font-bold text-emerald-800">الإيجارات المستلمة (+)</div>
                                    <div id="pro_rep_collected" class="text-base font-extrabold font-mono text-emerald-700 mt-1">0.00</div>
                                </div>
                                <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200">
                                    <div class="text-[11px] font-bold text-rose-800">الإيجارات المتأخرة (!)</div>
                                    <div id="pro_rep_overdue" class="text-base font-extrabold font-mono text-rose-600 mt-1">0.00</div>
                                </div>
                                <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200">
                                    <div class="text-[11px] font-bold text-amber-900">عمولة المكتب (-)</div>
                                    <div id="pro_rep_commission" class="text-base font-extrabold font-mono text-amber-800 mt-1">0.00</div>
                                </div>
                                <div class="p-3.5 rounded-2xl bg-slate-100 border border-slate-300">
                                    <div class="text-[11px] font-bold text-slate-700">المصروفات والتحويلات (-)</div>
                                    <div id="pro_rep_expenses" class="text-base font-extrabold font-mono text-slate-800 mt-1">0.00</div>
                                </div>
                                <div class="p-3.5 rounded-2xl bg-slate-900 text-white shadow-md">
                                    <div class="text-[11px] font-bold text-emerald-300">💎 الصافي المستحق للمالك</div>
                                    <div id="pro_rep_net" class="text-lg font-extrabold font-mono text-white mt-1">0.00</div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-bold text-sm text-slate-900">🏢 أولاً: بيان العقود والوحدات (المحصّل والمتبقي لكل مستأجر)</h4>
                                    <span id="pro_rep_vacant_badge" class="px-3 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold"></span>
                                </div>
                                <div class="overflow-x-auto rounded-2xl border border-slate-300">
                                    <table class="w-full text-xs text-right" id="pro_rep_contracts_table">
                                        <thead class="bg-slate-900 text-white">
                                            <tr>
                                                <th class="py-3 px-3">رقم العقد</th>
                                                <th class="py-3 px-3">العقار / الوحدة</th>
                                                <th class="py-3 px-3">اسم المستأجر وجواله</th>
                                                <th class="py-3 px-3">فترة العقد</th>
                                                <th class="py-3 px-3">قيمة العقد</th>
                                                <th class="py-3 px-3">المسدد (+)</th>
                                                <th class="py-3 px-3">المتبقي (!)</th>
                                                <th class="py-3 px-3 text-center">حالة السداد</th>
                                            </tr>
                                        </thead>
                                        <tbody id="pro_rep_contracts_tbody" class="divide-y divide-slate-200"></tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <h4 class="font-bold text-sm text-slate-900">🧾 ثانياً: سجل سندات القبض والصرف الفعلية للمالك</h4>
                                <div class="overflow-x-auto rounded-2xl border border-slate-300">
                                    <table class="w-full text-xs text-right" id="pro_rep_vouchers_table">
                                        <thead class="bg-slate-100 text-slate-800 border-b border-slate-300">
                                            <tr>
                                                <th class="py-2.5 px-3">التاريخ</th>
                                                <th class="py-2.5 px-3">رقم السند</th>
                                                <th class="py-2.5 px-3">النوع</th>
                                                <th class="py-2.5 px-3">المستأجر / الطرف</th>
                                                <th class="py-2.5 px-3">طريقة الدفع</th>
                                                <th class="py-2.5 px-3">البيان</th>
                                                <th class="py-2.5 px-3">مقبوضات (+)</th>
                                                <th class="py-2.5 px-3">مصروفات / تحويلات (-)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="pro_rep_vouchers_tbody" class="divide-y divide-slate-200"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="pro_general_ledger_view" class="hidden bg-white rounded-3xl border border-slate-200 p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 p-4 rounded-2xl bg-slate-900 text-white text-xs">
                            <div><span class="text-slate-400 block">الجهة / الحساب المحدد:</span><b id="pro_sa_selected_title" class="text-sm text-white">جميع الحركات المالية</b></div>
                            <div><span class="text-slate-400 block">إجمالي المقبوضات / المدين (+):</span><b id="pro_sa_total_debit" class="text-base font-mono text-emerald-400">0.00 ر.س</b></div>
                            <div><span class="text-slate-400 block">إجمالي المصروفات / الدائن (-):</span><b id="pro_sa_total_credit" class="text-base font-mono text-amber-400">0.00 ر.س</b></div>
                            <div><span class="text-slate-400 block">الرصيد الصافي التراكمي:</span><b id="pro_sa_net_balance" class="text-base font-mono text-sky-300">0.00 ر.س</b></div>
                        </div>

                        <div class="overflow-x-auto rounded-2xl border border-slate-200">
                            <table class="w-full text-xs text-right" id="pro_standaloneAccountStatementTable">
                                <thead class="bg-slate-900 text-white">
                                    <tr>
                                        <th class="py-3.5 px-4">التاريخ</th>
                                        <th class="py-3.5 px-4">رقم السند</th>
                                        <th class="py-3.5 px-4">النوع</th>
                                        <th class="py-3.5 px-4">الحساب المحاسبي</th>
                                        <th class="py-3.5 px-4">المستأجر / المالك المرتبط</th>
                                        <th class="py-3.5 px-4">البيان والتفاصيل</th>
                                        <th class="py-3.5 px-4">مدين (+)</th>
                                        <th class="py-3.5 px-4">دائن (-)</th>
                                        <th class="py-3.5 px-4">الرصيد التراكمي</th>
                                    </tr>
                                </thead>
                                <tbody id="pro_sa_tbody" class="divide-y divide-slate-100">
                                    <?php foreach (($vouchers ?? collect()) as $v) { ?>
                                        <tr class="pro-sa-row hover:bg-slate-50"
                                            data-date="{{ $v->voucher_date }}"
                                            data-owner="{{ $v->owner_id ?? '' }}"
                                            data-tenant="{{ $v->tenant_id ?? '' }}"
                                            data-debit-acc="{{ $v->debit_account_id }}"
                                            data-credit-acc="{{ $v->credit_account_id }}"
                                            data-debit="{{ $v->voucher_type == 'receipt' ? (float)$v->amount : 0 }}"
                                            data-credit="{{ $v->voucher_type == 'payment' ? (float)$v->amount : 0 }}">
                                            <td class="py-3 px-4 font-mono">{{ $v->voucher_date }}</td>
                                            <td class="py-3 px-4 font-mono font-bold">{{ $v->voucher_number }}</td>
                                            <td class="py-3 px-4">
                                                <span class="px-2 py-0.5 rounded font-bold text-[10px] {{ $v->voucher_type == 'receipt' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                                    {{ $v->voucher_type == 'receipt' ? 'سند قبض' : 'سند صرف' }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 font-bold">{{ $v->debit_code }} - {{ $v->debit_name }} / {{ $v->credit_code }} - {{ $v->credit_name }}</td>
                                            <td class="py-3 px-4 font-semibold">{{ $v->tenant_name ?: ($v->owner_name ?: 'عام للمكتب') }}</td>
                                            <td class="py-3 px-4">{{ $v->notes }}</td>
                                            <td class="py-3 px-4 font-mono font-bold text-emerald-700">{{ $v->voucher_type == 'receipt' ? number_format($v->amount, 2) : '0.00' }}</td>
                                            <td class="py-3 px-4 font-mono font-bold text-amber-700">{{ $v->voucher_type == 'payment' ? number_format($v->amount, 2) : '0.00' }}</td>
                                            <td class="py-3 px-4 font-mono font-extrabold pro-sa-running-cell">0.00</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <script>
                        window.currentProStatementMode = 'owner_settlement';
                        window.proFormatSAR = function(n) {
                            return Number(n || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        };

                        window.proSwitchStatementMode = function(mode) {
                            window.currentProStatementMode = mode;
                            const isOwner = (mode === 'owner_settlement');
                            document.getElementById('pro_owner_settlement_view').classList.toggle('hidden', !isOwner);
                            document.getElementById('pro_owner_settlement_filters').classList.toggle('hidden', !isOwner);
                            document.getElementById('pro_general_ledger_view').classList.toggle('hidden', isOwner);
                            document.getElementById('pro_general_statement_filters').classList.toggle('hidden', isOwner);

                            document.getElementById('pro_btn_mode_owner').className = isOwner
                                ? 'px-4 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold transition'
                                : 'px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-bold transition';
                            document.getElementById('pro_btn_mode_ledger').className = !isOwner
                                ? 'px-4 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold transition'
                                : 'px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-bold transition';

                            if (!isOwner) window.proFilterGeneralStatement();
                        };

                        window.proSetFullYear = function() {
                            document.getElementById('pro_settle_from').value = '';
                            document.getElementById('pro_settle_to').value = '';
                            window.proRenderOwnerSettlement();
                        };

                        window.proOnOwnerChange = function() {
                            const ownerId = String(document.getElementById('pro_settle_owner_id')?.value || '');
                            const propSel = document.getElementById('pro_settle_property_id');
                            if (propSel) {
                                Array.from(propSel.options).forEach(opt => {
                                    if (!opt.value) { opt.hidden = false; return; }
                                    opt.hidden = (ownerId && String(opt.getAttribute('data-owner')) !== ownerId);
                                });
                                propSel.value = '';
                                if (propSel._syncTriggerUI) propSel._syncTriggerUI();
                            }
                            window.proRenderOwnerSettlement();
                        };

                        window.proRenderOwnerSettlement = function() {
                            const ownerId = String(document.getElementById('pro_settle_owner_id')?.value || '');
                            const propId = String(document.getElementById('pro_settle_property_id')?.value || '');
                            const fromDate = document.getElementById('pro_settle_from')?.value || '';
                            const toDate = document.getElementById('pro_settle_to')?.value || '';

                            const emptyBox = document.getElementById('pro_settlement_empty');
                            const printArea = document.getElementById('pro_ownerSettlementPrintableArea');

                            if (!ownerId) {
                                emptyBox.classList.remove('hidden');
                                printArea.classList.add('hidden');
                                return;
                            }

                            const owner = (window.officeOwnersMaster || []).find(o => String(o.id) === ownerId);
                            if (!owner) return;

                            emptyBox.classList.add('hidden');
                            printArea.classList.remove('hidden');

                            const feeRate = parseFloat(owner.office_management_fee_rate || 5);
                            document.getElementById('pro_rep_owner_name').innerText = owner.name;
                            document.getElementById('pro_rep_owner_phone').innerText = (owner.national_id || '-') + ' | ' + (owner.phone || '-');
                            document.getElementById('pro_rep_owner_iban').innerText = (owner.bank_name ? owner.bank_name + ' - ' : '') + (owner.iban || 'غير مسجل');
                            document.getElementById('pro_rep_fee_rate').innerText = feeRate + '%';
                            document.getElementById('pro_rep_period').innerText = 'الفترة: ' + (fromDate || 'البداية') + ' إلى ' + (toDate || 'اليوم');

                            const selectedProp = propId ? (window.officePropertiesMaster || []).find(p => String(p.id) === propId) : null;
                            document.getElementById('pro_rep_prop_scope').innerText = selectedProp ? selectedProp.title : 'جميع عقارات المالك';

                            const ownerProps = (window.officePropertiesMaster || []).filter(p => String(p.owner_id) === ownerId && (!propId || String(p.id) === propId));
                            const ownerPropIds = ownerProps.map(p => String(p.id));
                            const ownerUnits = (window.officeUnitsMaster || []).filter(u => ownerPropIds.includes(String(u.property_id)));
                            const vacantCount = ownerUnits.filter(u => u.status === 'vacant').length;
                            document.getElementById('pro_rep_vacant_badge').innerText = `إجمالي العقارات: ${ownerProps.length} | الوحدات الشاغرة: ${vacantCount}`;

                            const ownerContracts = (window.officeContractsMaster || []).filter(c => {
                                if (String(c.owner_id) !== ownerId) return false;
                                if (propId && String(c.property_id) !== propId) return false;
                                return true;
                            });
                            const ownerTenantIds = ownerContracts.map(c => String(c.tenant_id));

                            const periodVouchers = (window.officeVouchersMaster || []).filter(v => {
                                const vDate = v.voucher_date || '';
                                if (fromDate && vDate < fromDate) return false;
                                if (toDate && vDate > toDate) return false;
                                const matchOwner = String(v.owner_id || '') === ownerId;
                                const matchTenant = v.tenant_id && ownerTenantIds.includes(String(v.tenant_id));
                                return matchOwner || matchTenant;
                            });

                            let totalContractsRent = 0;
                            let totalOverdueRent = 0;
                            const cTbody = document.getElementById('pro_rep_contracts_tbody');
                            cTbody.innerHTML = '';

                            if (ownerContracts.length === 0) {
                                cTbody.innerHTML = `<tr><td colspan="8" class="py-6 text-center text-slate-400">لا توجد عقود مسجلة لهذا المالك ضمن النطاق المحدد.</td></tr>`;
                            } else {
                                ownerContracts.forEach(c => {
                                    const rentVal = parseFloat(c.total_rent_amount || 0);
                                    totalContractsRent += rentVal;
                                    const paidByTenant = periodVouchers
                                        .filter(v => v.voucher_type === 'receipt' && String(v.tenant_id || '') === String(c.tenant_id))
                                        .reduce((s, v) => s + parseFloat(v.amount || 0), 0);
                                    const rem = Math.max(0, rentVal - paidByTenant);
                                    totalOverdueRent += rem;

                                    const statusBadge = rem <= 0.01
                                        ? `<span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold">✅ مسدد بالكامل</span>`
                                        : (paidByTenant > 0 ? `<span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-bold">⚠️ سداد جزئي</span>` : `<span class="px-2 py-0.5 rounded bg-rose-100 text-rose-800 font-bold">🚨 لم يسدد</span>`);

                                    const tr = document.createElement('tr');
                                    tr.innerHTML = `
                                        <td class="py-2.5 px-3 font-mono font-bold">${c.contract_number}</td>
                                        <td class="py-2.5 px-3 font-bold">${c.property_title} ${c.unit_number ? '(وحدة ' + c.unit_number + ')' : ''}</td>
                                        <td class="py-2.5 px-3"><b>${c.tenant_name}</b> <span class="font-mono text-[10px] text-slate-500" dir="ltr">${c.tenant_phone || ''}</span></td>
                                        <td class="py-2.5 px-3 font-mono text-[11px]">${c.start_date} ← ${c.end_date}</td>
                                        <td class="py-2.5 px-3 font-mono font-bold">${window.proFormatSAR(rentVal)}</td>
                                        <td class="py-2.5 px-3 font-mono font-bold text-emerald-700">${window.proFormatSAR(paidByTenant)}</td>
                                        <td class="py-2.5 px-3 font-mono font-bold text-rose-600">${window.proFormatSAR(rem)}</td>
                                        <td class="py-2.5 px-3 text-center">${statusBadge}</td>
                                    `;
                                    cTbody.appendChild(tr);
                                });
                            }

                            const totalRec = periodVouchers.filter(v => v.voucher_type === 'receipt').reduce((s, v) => s + parseFloat(v.amount || 0), 0);
                            const totalPay = periodVouchers.filter(v => v.voucher_type === 'payment').reduce((s, v) => s + parseFloat(v.amount || 0), 0);
                            const officeComm = (totalRec * feeRate) / 100;
                            const netOwner = totalRec - officeComm - totalPay;

                            document.getElementById('pro_rep_total_rent').innerText = window.proFormatSAR(totalContractsRent);
                            document.getElementById('pro_rep_collected').innerText = window.proFormatSAR(totalRec);
                            document.getElementById('pro_rep_overdue').innerText = window.proFormatSAR(totalOverdueRent);
                            document.getElementById('pro_rep_commission').innerText = window.proFormatSAR(officeComm);
                            document.getElementById('pro_rep_expenses').innerText = window.proFormatSAR(totalPay);
                            document.getElementById('pro_rep_net').innerText = window.proFormatSAR(netOwner) + ' ر.س';

                            const vTbody = document.getElementById('pro_rep_vouchers_tbody');
                            vTbody.innerHTML = '';
                            if (periodVouchers.length === 0) {
                                vTbody.innerHTML = `<tr><td colspan="8" class="py-6 text-center text-slate-400">لا توجد سندات مسجلة خلال هذه الفترة.</td></tr>`;
                            } else {
                                periodVouchers.forEach(v => {
                                    const isRec = v.voucher_type === 'receipt';
                                    const tr = document.createElement('tr');
                                    tr.innerHTML = `
                                        <td class="py-2.5 px-3 font-mono">${v.voucher_date}</td>
                                        <td class="py-2.5 px-3 font-mono font-bold">${v.voucher_number}</td>
                                        <td class="py-2.5 px-3"><span class="px-2 py-0.5 rounded font-bold text-[10px] ${isRec ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'}">${isRec ? 'سند قبض' : 'سند صرف'}</span></td>
                                        <td class="py-2.5 px-3 font-bold">${v.tenant_name || v.owner_name || '-'}</td>
                                        <td class="py-2.5 px-3">${v.payment_method || '-'}</td>
                                        <td class="py-2.5 px-3">${v.notes || '-'}</td>
                                        <td class="py-2.5 px-3 font-mono font-bold text-emerald-700">${isRec ? window.proFormatSAR(v.amount) : '0.00'}</td>
                                        <td class="py-2.5 px-3 font-mono font-bold text-rose-600">${!isRec ? window.proFormatSAR(v.amount) : '0.00'}</td>
                                    `;
                                    vTbody.appendChild(tr);
                                });
                            }
                        };

                        window.proFilterGeneralStatement = function(changedSource) {
                            const accSel = document.getElementById('pro_sa_acc_filter');
                            const partySel = document.getElementById('pro_sa_party_filter');
                            if (changedSource === 'acc' && accSel && accSel.value && partySel) {
                                partySel.value = '';
                                if (partySel._syncTriggerUI) partySel._syncTriggerUI();
                            } else if (changedSource === 'party' && partySel && partySel.value && accSel) {
                                accSel.value = '';
                                if (accSel._syncTriggerUI) accSel._syncTriggerUI();
                            }

                            const acc = accSel?.value || '';
                            const party = partySel?.value || '';
                            const from = document.getElementById('pro_sa_from')?.value || '';
                            const to = document.getElementById('pro_sa_to')?.value || '';

                            let sumDebit = 0, sumCredit = 0, running = 0;
                            const rows = Array.from(document.querySelectorAll('.pro-sa-row'));
                            const table = document.getElementById('pro_standaloneAccountStatementTable');

                            rows.forEach(r => {
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

                                r.dataset.searchMatched = show ? '1' : '0';
                                r.style.display = show ? '' : 'none';
                                if (show) {
                                    const deb = parseFloat(r.getAttribute('data-debit') || 0);
                                    const cred = parseFloat(r.getAttribute('data-credit') || 0);
                                    sumDebit += deb;
                                    sumCredit += cred;
                                    running += (deb - cred);
                                    const runCell = r.querySelector('.pro-sa-running-cell');
                                    if (runCell) runCell.innerText = window.proFormatSAR(running) + ' ر.س';
                                }
                            });

                            if (table) window.refreshTablePagination(table, true);

                            const titleEl = document.getElementById('pro_sa_selected_title');
                            if (titleEl) {
                                if (party && partySel) titleEl.innerText = partySel.options[partySel.selectedIndex]?.text || 'طرف محدد';
                                else if (acc && accSel) titleEl.innerText = accSel.options[accSel.selectedIndex]?.text || 'حساب محدد';
                                else titleEl.innerText = 'جميع الحركات المالية بالمكتب';
                            }
                            document.getElementById('pro_sa_total_debit').innerText = window.proFormatSAR(sumDebit) + ' ر.س';
                            document.getElementById('pro_sa_total_credit').innerText = window.proFormatSAR(sumCredit) + ' ر.س';
                            document.getElementById('pro_sa_net_balance').innerText = window.proFormatSAR(sumDebit - sumCredit) + ' ر.س';
                        };

                        window.exportActiveAccountStatementXLSX = function(filename) {
                            if (window.currentProStatementMode === 'owner_settlement') {
                                const ownerId = document.getElementById('pro_settle_owner_id')?.value || '';
                                if (!ownerId) {
                                    alert('يرجى اختيار المالك أولاً من جدول البحث لتصدير تصفية حسابه إلى Excel (.xlsx).');
                                    return;
                                }
                                const ownerName = document.getElementById('pro_rep_owner_name')?.innerText || 'المالك';
                                const aoa = [
                                    ['بيان تصفية حساب مالك عقار — ' + '{{ addslashes($office->name) }}'],
                                    ['اسم المالك', ownerName, 'الهوية والجوال', document.getElementById('pro_rep_owner_phone')?.innerText || '', 'البنك والآيبان', document.getElementById('pro_rep_owner_iban')?.innerText || ''],
                                    [],
                                    ['إجمالي عقود الإيجار', 'الإيجارات المستلمة (+)', 'الإيجارات المتأخرة (!)', 'عمولة المكتب (-)', 'المصروفات والتحويلات (-)', 'الصافي المستحق للمالك'],
                                    [
                                        document.getElementById('pro_rep_total_rent')?.innerText || '0.00',
                                        document.getElementById('pro_rep_collected')?.innerText || '0.00',
                                        document.getElementById('pro_rep_overdue')?.innerText || '0.00',
                                        document.getElementById('pro_rep_commission')?.innerText || '0.00',
                                        document.getElementById('pro_rep_expenses')?.innerText || '0.00',
                                        document.getElementById('pro_rep_net')?.innerText || '0.00'
                                    ],
                                    [],
                                    ['أولاً: بيان العقود والوحدات'],
                                    ['رقم العقد', 'العقار / الوحدة', 'اسم المستأجر وجواله', 'فترة العقد', 'قيمة العقد', 'المسدد (+)', 'المتبقي (!)', 'حالة السداد']
                                ];
                                document.querySelectorAll('#pro_rep_contracts_tbody tr').forEach(tr => {
                                    const cells = Array.from(tr.querySelectorAll('td')).map(td => (td.innerText || '').replace(/\s+/g, ' ').trim());
                                    if (cells.length > 1) aoa.push(cells);
                                });
                                aoa.push([]);
                                aoa.push(['ثانياً: سجل سندات القبض والصرف الفعلية']);
                                aoa.push(['التاريخ', 'رقم السند', 'النوع', 'المستأجر / الطرف', 'طريقة الدفع', 'البيان', 'مقبوضات (+)', 'مصروفات / تحويلات (-)']);
                                document.querySelectorAll('#pro_rep_vouchers_tbody tr').forEach(tr => {
                                    const cells = Array.from(tr.querySelectorAll('td')).map(td => (td.innerText || '').replace(/\s+/g, ' ').trim());
                                    if (cells.length > 1) aoa.push(cells);
                                });
                                window.exportAOAToRealXLSX(aoa, 'تصفية_حساب_' + ownerName + '.xlsx', 'تصفية حساب المالك');
                            } else {
                                window.exportTableToCSV('pro_standaloneAccountStatementTable', filename || 'كشف_الحساب_التفصيلي.xlsx');
                            }
                        };

                        window.proDownloadActiveStatementPDF = function() {
                            if (window.currentProStatementMode === 'owner_settlement') {
                                const printArea = document.getElementById('pro_ownerSettlementPrintableArea');
                                if (!printArea || printArea.classList.contains('hidden')) {
                                    alert('يرجى اختيار المالك أولاً من جدول البحث لعرض برنت التصفية ثم تصديره.');
                                    return;
                                }
                                const ownerName = document.getElementById('pro_rep_owner_name')?.innerText || 'Owner';
                                window.exportElementToPDF('pro_ownerSettlementPrintableArea', 'تصفية_حساب_' + ownerName + '.pdf');
                            } else {
                                window.exportElementToPDF('pro_general_ledger_view', 'كشف_حساب_تفصيلي.pdf');
                            }
                        };

                        window.proSendActiveStatementWhatsappPDF = function() {
                            if (window.currentProStatementMode === 'owner_settlement') {
                                const ownerId = String(document.getElementById('pro_settle_owner_id')?.value || '');
                                if (!ownerId) {
                                    alert('يرجى اختيار المالك أولاً لإرسال برنت التصفية كملف PDF أصلي عبر الواتساب.');
                                    return;
                                }
                                const owner = (window.officeOwnersMaster || []).find(o => String(o.id) === ownerId);
                                window.sendElementAsOriginalPdfWhatsapp('pro_ownerSettlementPrintableArea', 'تصفية_حساب_' + (owner?.name || 'المالك') + '.pdf', owner?.phone || '', 'general');
                            } else {
                                const partyVal = document.getElementById('pro_sa_party_filter')?.value || '';
                                let phone = '';
                                if (partyVal.startsWith('owner_')) {
                                    const o = (window.officeOwnersMaster || []).find(x => String(x.id) === partyVal.replace('owner_', ''));
                                    phone = o?.phone || '';
                                } else if (partyVal.startsWith('tenant_')) {
                                    const t = (window.officeTenantsMaster || []).find(x => String(x.id) === partyVal.replace('tenant_', ''));
                                    phone = t?.phone || '';
                                }
                                window.sendElementAsOriginalPdfWhatsapp('pro_general_ledger_view', 'كشف_حساب_مالي.pdf', phone, 'general');
                            }
                        };

                        document.addEventListener('DOMContentLoaded', function() {
                            const masterSec = document.getElementById('masterTabSectionActive');
                            if (masterSec && masterSec.parentElement) {
                                let next = masterSec.nextElementSibling;
                                while (next) {
                                    const toRemove = next;
                                    next = next.nextElementSibling;
                                    toRemove.remove();
                                }
                            }
                        });
                    </script>
                </div>
            <?php } ?>

            <!-- ========================================================================= -->
            <!-- 2. تبويب جدولة الدفعات وأعمار الديون العقارية المطور (8 فلاتر ذكية + مؤشرات التحصيل) -->
            <!-- ========================================================================= -->
            <?php if (($tab ?? '') === 'installments') {
                $allInstCollection = collect($installments ?? []);
                $totalScheduledSum = $allInstCollection->sum('amount');
                $totalPaidSum = $allInstCollection->sum('paid_amount');
                $totalRemainingSum = max(0, $totalScheduledSum - $totalPaidSum);
                $collectionRatePct = $totalScheduledSum > 0 ? round(($totalPaidSum / $totalScheduledSum) * 100, 1) : 0;

                $todayStr = date('Y-m-d');
                $next30Str = date('Y-m-d', strtotime('+30 days'));
                $upcoming30Sum = $allInstCollection->filter(function($i) use ($todayStr, $next30Str) {
                    $rem = round($i->amount - $i->paid_amount, 2);
                    return $rem > 0.01 && $i->due_date >= $todayStr && $i->due_date <= $next30Str;
                })->sum(function($i) { return max(0, $i->amount - $i->paid_amount); });

                $totalOverdueAll = ($agingBuckets['b_30'] ?? 0) + ($agingBuckets['b_60'] ?? 0) + ($agingBuckets['b_90'] ?? 0) + ($agingBuckets['b_plus'] ?? 0);

                $cycleMapAr = [
                    'monthly' => 'شهري',
                    'quarterly' => 'ربع سنوي',
                    'semi_annual' => 'نصف سنوي',
                    'annual' => 'سنوي',
                ];
            ?>
                <div id="masterTabSectionActive" class="space-y-6">
                    <!-- 1. مؤشرات الأداء المالي والتحصيل (KPIs) -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-500">إجمالي الدفعات المجدولة</span>
                                <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 font-mono text-[11px] font-bold">{{ $allInstCollection->count() }} دفعة</span>
                            </div>
                            <div class="text-2xl font-extrabold text-slate-900 font-mono mt-2">{{ number_format($totalScheduledSum, 2) }} <span class="text-xs font-normal">ر.س</span></div>
                        </div>

                        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-emerald-700">إجمالي المحصّل الفعلي</span>
                                <span class="px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 font-mono text-[11px] font-bold">نسبة التحصيل {{ $collectionRatePct }}%</span>
                            </div>
                            <div class="text-2xl font-extrabold text-emerald-600 font-mono mt-2">{{ number_format($totalPaidSum, 2) }} <span class="text-xs font-normal">ر.س</span></div>
                            <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden mt-2">
                                <div class="h-full bg-emerald-500 rounded-full" style="width: {{ min(100, $collectionRatePct) }}%"></div>
                            </div>
                        </div>

                        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-amber-800">الرصيد المتبقي (غير مسدد)</span>
                                <span class="px-2 py-0.5 rounded-lg bg-amber-50 text-amber-800 text-[11px] font-bold">مجدول + متأخر</span>
                            </div>
                            <div class="text-2xl font-extrabold text-amber-700 font-mono mt-2">{{ number_format($totalRemainingSum, 2) }} <span class="text-xs font-normal">ر.س</span></div>
                        </div>

                        <div class="bg-slate-900 text-white p-5 rounded-3xl shadow-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-rose-300">إجمالي المتأخرات الحالة 🚨</span>
                                <span class="px-2 py-0.5 rounded-lg bg-rose-500/20 text-rose-200 text-[11px] font-bold">واجبة التحصيل</span>
                            </div>
                            <div class="text-2xl font-extrabold text-white font-mono mt-2">{{ number_format($totalOverdueAll, 2) }} <span class="text-xs font-normal text-rose-300">ر.س</span></div>
                        </div>
                    </div>

                    <!-- 2. بطاقات أعمار الديون التفاعلية (اضغط على أي فئة لفلترة الجدول فوراً) -->
                    <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
                        <div onclick="applyQuickAgingBucket('all')" id="aging_card_all" class="aging-bucket-card cursor-pointer bg-slate-900 text-white p-4 rounded-2xl border border-slate-900 transition">
                            <div class="text-[11px] font-bold opacity-80">📋 جميع الدفعات</div>
                            <div class="text-base font-extrabold font-mono mt-1">{{ number_format($totalRemainingSum, 2) }} <span class="text-[10px]">ر.س</span></div>
                            <div class="text-[10px] text-emerald-300 mt-0.5">عرض الكل ({{ $allInstCollection->count() }})</div>
                        </div>

                        <div onclick="applyQuickAgingBucket('upcoming_30')" id="aging_card_upcoming_30" class="aging-bucket-card cursor-pointer bg-white hover:bg-sky-50/50 p-4 rounded-2xl border border-slate-200 transition">
                            <div class="text-[11px] font-bold text-sky-700">⏳ تستحق خلال 30 يوم</div>
                            <div class="text-base font-extrabold font-mono text-sky-800 mt-1">{{ number_format($upcoming30Sum, 2) }} <span class="text-[10px]">ر.س</span></div>
                            <div class="text-[10px] text-slate-400 mt-0.5">دفعات قريبة الحلول</div>
                        </div>

                        <div onclick="applyQuickAgingBucket('1_30')" id="aging_card_1_30" class="aging-bucket-card cursor-pointer bg-white hover:bg-amber-50/50 p-4 rounded-2xl border border-slate-200 transition">
                            <div class="text-[11px] font-bold text-amber-700">⚠️ تأخير 1 - 30 يوماً</div>
                            <div class="text-base font-extrabold font-mono text-amber-600 mt-1">{{ number_format($agingBuckets['b_30'] ?? 0, 2) }} <span class="text-[10px]">ر.س</span></div>
                            <div class="text-[10px] text-slate-400 mt-0.5">تأخير حديث</div>
                        </div>

                        <div onclick="applyQuickAgingBucket('31_60')" id="aging_card_31_60" class="aging-bucket-card cursor-pointer bg-white hover:bg-orange-50/50 p-4 rounded-2xl border border-slate-200 transition">
                            <div class="text-[11px] font-bold text-orange-700">🔔 تأخير 31 - 60 يوماً</div>
                            <div class="text-base font-extrabold font-mono text-orange-600 mt-1">{{ number_format($agingBuckets['b_60'] ?? 0, 2) }} <span class="text-[10px]">ر.س</span></div>
                            <div class="text-[10px] text-slate-400 mt-0.5">يتطلب إشعار سداد</div>
                        </div>

                        <div onclick="applyQuickAgingBucket('61_90')" id="aging_card_61_90" class="aging-bucket-card cursor-pointer bg-white hover:bg-rose-50/50 p-4 rounded-2xl border border-slate-200 transition">
                            <div class="text-[11px] font-bold text-rose-700">🚨 تأخير 61 - 90 يوماً</div>
                            <div class="text-base font-extrabold font-mono text-rose-600 mt-1">{{ number_format($agingBuckets['b_90'] ?? 0, 2) }} <span class="text-[10px]">ر.س</span></div>
                            <div class="text-[10px] text-slate-400 mt-0.5">يتطلب إنذار نهائي</div>
                        </div>

                        <div onclick="applyQuickAgingBucket('90_plus')" id="aging_card_90_plus" class="aging-bucket-card cursor-pointer bg-white hover:bg-rose-100/50 p-4 rounded-2xl border border-rose-200 transition">
                            <div class="text-[11px] font-bold text-rose-900">⚖️ متعثرة (+90 يوماً)</div>
                            <div class="text-base font-extrabold font-mono text-rose-700 mt-1">{{ number_format($agingBuckets['b_plus'] ?? 0, 2) }} <span class="text-[10px]">ر.س</span></div>
                            <div class="text-[10px] text-rose-600 font-semibold mt-0.5">جاهزة للتنفيذ القضائي</div>
                        </div>
                    </div>

                    <!-- 3. لوحة الفلترة الذكية المتقدمة (8 فلاتر مترابطة) -->
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-slate-100 space-y-4">
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <div>
                                    <h3 class="font-bold text-base text-slate-900">📅 مركز جدولة الدفعات والتحصيل وأعمار الديون</h3>
                                    <p class="text-xs text-slate-500 mt-0.5">فلترة متقدمة بالمالك، العقار، المستأجر، حالة القسط، عمر الدين، دورة الإيجار، والتاريخ — مع بحث شامل في جميع الصفحات</p>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <button type="button" onclick="setInstallmentDateQuickFilter('this_month')" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold">استحقاق هذا الشهر</button>
                                    <button type="button" onclick="setInstallmentDateQuickFilter('this_year')" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold">هذه السنة</button>
                                    <button type="button" onclick="resetAllInstallmentFilters()" class="px-3.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border border-rose-200 text-xs font-bold transition">
                                        🔄 إعادة ضبط الفلاتر
                                    </button>
                                    <button type="button" onclick="exportTableToCSV('officeInstallmentsMainTable', 'جدول_الدفعات_وأعمار_الديون.xlsx')"
                                            class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold shadow-sm">
                                        📊 تصدير Excel (.xlsx)
                                    </button>
                                    <button type="button" onclick="exportElementToPDF('officeInstallmentsPrintableWrap', 'جدول_الدفعات_وأعمار_الديون.pdf')"
                                            class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold shadow-sm">
                                        📥 تصدير PDF
                                    </button>
                                </div>
                            </div>

                            <!-- شبكة الفلاتر الـ 8 -->
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                                <div class="md:col-span-3">
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">🔍 1. بحث نصي فوري شامل</label>
                                    <input type="text" id="instSearchInput" oninput="filterInstallmentsTable()"
                                           placeholder="ابحث برقم العقد، المستأجر، الجوال، الوحدة..."
                                           class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-semibold">
                                </div>

                                <div class="md:col-span-3">
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">👑 2. فلترة حسب المالك</label>
                                    <select id="instOwnerFilter" onchange="onInstOwnerFilterChange()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-bold">
                                        <option value="">-- جميع الملاك --</option>
                                        <?php foreach (($owners ?? collect()) as $ow) { ?>
                                            <option value="{{ $ow->id }}">{{ $ow->name }}</option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="md:col-span-3">
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">🏢 3. فلترة حسب العقار</label>
                                    <select id="instPropertyFilter" onchange="filterInstallmentsTable()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-bold">
                                        <option value="">-- جميع العقارات --</option>
                                        <?php foreach (($properties ?? collect()) as $pr) { ?>
                                            <option value="{{ $pr->id }}" data-owner="{{ $pr->owner_id }}">{{ $pr->title }} ({{ $pr->owner_name }})</option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="md:col-span-3">
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">👤 4. فلترة حسب المستأجر</label>
                                    <select id="instTenantFilter" onchange="filterInstallmentsTable()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-bold">
                                        <option value="">-- جميع المستأجرين --</option>
                                        <?php foreach (($tenants ?? collect()) as $tn) { ?>
                                            <option value="{{ $tn->id }}">{{ $tn->name }}</option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="md:col-span-3">
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">📌 5. حالة الدفعة</label>
                                    <select id="instStatusFilter" onchange="filterInstallmentsTable()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-bold">
                                        <option value="all">جميع الحالات</option>
                                        <option value="unpaid">غير المسددة (المتأخرة + المجدولة + الجزئية)</option>
                                        <option value="overdue">المتأخرة فقط 🚨</option>
                                        <option value="partial">المسددة جزئياً ⚠️</option>
                                        <option value="upcoming">المجدولة القادمة (لم يحل موعدها) ⏳</option>
                                        <option value="paid">المسددة بالكامل ✅</option>
                                    </select>
                                </div>

                                <div class="md:col-span-3">
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">⏱️ 6. فئة عمر الدين والاستحقاق</label>
                                    <select id="instAgingFilter" onchange="filterInstallmentsTable()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-bold">
                                        <option value="all">جميع أعمار الديون والفترات</option>
                                        <option value="1_30">متأخرة من 1 إلى 30 يوماً</option>
                                        <option value="31_60">متأخرة من 31 إلى 60 يوماً</option>
                                        <option value="61_90">متأخرة من 61 إلى 90 يوماً</option>
                                        <option value="90_plus">متعثرة أكثر من 90 يوماً (+90)</option>
                                        <option value="upcoming_7">تستحق خلال 7 أيام قادمة</option>
                                        <option value="upcoming_30">تستحق خلال 30 يوماً قادمة</option>
                                    </select>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">🔄 7. دورة السداد</label>
                                    <select id="instCycleFilter" onchange="filterInstallmentsTable()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-bold">
                                        <option value="all">جميع الدورات</option>
                                        <option value="monthly">شهري</option>
                                        <option value="quarterly">ربع سنوي</option>
                                        <option value="semi_annual">نصف سنوي</option>
                                        <option value="annual">سنوي</option>
                                    </select>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">📅 8. من تاريخ استحقاق</label>
                                    <input type="date" id="instFromDate" onchange="filterInstallmentsTable()" class="w-full rounded-xl border border-slate-300 px-2.5 py-1.5 text-xs bg-white font-mono">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">📅 إلى تاريخ استحقاق</label>
                                    <input type="date" id="instToDate" onchange="filterInstallmentsTable()" class="w-full rounded-xl border border-slate-300 px-2.5 py-1.5 text-xs bg-white font-mono">
                                </div>
                            </div>

                            <!-- شريط ملخص نتائج الفلترة المباشر -->
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 p-3.5 rounded-2xl bg-slate-900 text-white text-xs">
                                <div>
                                    <span class="text-slate-400 block text-[11px]">عدد الدفعات المطابقة للفلترة:</span>
                                    <b id="inst_filtered_count" class="font-mono text-sm text-white">0</b> <span class="text-[10px] text-slate-300">دفعة</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">إجمالي قيمة الدفعات المفلترة:</span>
                                    <b id="inst_filtered_total" class="font-mono text-sm text-sky-300">0.00</b> <span class="text-[10px]">ر.س</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">المحصّل من الدفعات المفلترة (+):</span>
                                    <b id="inst_filtered_paid" class="font-mono text-sm text-emerald-400">0.00</b> <span class="text-[10px]">ر.س</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">المتبقي المطلوب تحصيله (!):</span>
                                    <b id="inst_filtered_rem" class="font-mono text-sm text-amber-300">0.00</b> <span class="text-[10px]">ر.س</span>
                                </div>
                            </div>
                        </div>

                        <!-- جدول الدفعات الرئيسي -->
                        <div id="officeInstallmentsPrintableWrap" class="overflow-x-auto">
                            <table class="w-full text-xs text-right" id="officeInstallmentsMainTable">
                                <thead class="bg-slate-50 text-slate-600 border-b">
                                    <tr>
                                        <th class="py-3.5 px-3">رقم العقد والدورة</th>
                                        <th class="py-3.5 px-3">العقار / الوحدة</th>
                                        <th class="py-3.5 px-3">المالك</th>
                                        <th class="py-3.5 px-3">المستأجر والجوال</th>
                                        <th class="py-3.5 px-2 text-center">القسط</th>
                                        <th class="py-3.5 px-3">تاريخ الاستحقاق</th>
                                        <th class="py-3.5 px-3 text-center">عمر الدين / المدة</th>
                                        <th class="py-3.5 px-3">قيمة الدفعة</th>
                                        <th class="py-3.5 px-3">المسدد</th>
                                        <th class="py-3.5 px-3">المتبقي</th>
                                        <th class="py-3.5 px-3 text-center">الحالة</th>
                                        <th class="py-3.5 px-3 text-center">إجراءات التحصيل والمطالبة</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php if ($allInstCollection->count() > 0) { ?>
                                        <?php foreach ($allInstCollection as $inst) {
                                            $remAmount = max(0, round($inst->amount - $inst->paid_amount, 2));
                                            $isPaid = ($inst->status === 'paid' || $remAmount <= 0.01);
                                            $isOverdue = (!$isPaid && $inst->due_date < $todayStr);
                                            $daysDiff = (int) \Carbon\Carbon::parse($todayStr)->diffInDays(\Carbon\Carbon::parse($inst->due_date), false);
                                            $daysLate = $daysDiff < 0 ? abs($daysDiff) : 0;
                                            $daysRemaining = $daysDiff >= 0 ? $daysDiff : 0;
                                            $paidPct = $inst->amount > 0 ? min(100, round(($inst->paid_amount / $inst->amount) * 100)) : 0;
                                            $cycleAr = $cycleMapAr[$inst->payment_cycle ?? 'annual'] ?? ($inst->payment_cycle ?? 'سنوي');
                                        ?>
                                            <tr class="inst-row hover:bg-slate-50 transition"
                                                data-owner-id="{{ $inst->owner_id }}"
                                                data-property-id="{{ $inst->property_id }}"
                                                data-tenant-id="{{ $inst->tenant_id }}"
                                                data-cycle="{{ $inst->payment_cycle ?? 'annual' }}"
                                                data-due-date="{{ $inst->due_date }}"
                                                data-amount="{{ (float)$inst->amount }}"
                                                data-paid="{{ (float)$inst->paid_amount }}"
                                                data-rem="{{ $remAmount }}"
                                                data-is-paid="{{ $isPaid ? '1' : '0' }}"
                                                data-is-overdue="{{ $isOverdue ? '1' : '0' }}"
                                                data-is-partial="{{ (!$isPaid && $inst->paid_amount > 0) ? '1' : '0' }}"
                                                data-days-late="{{ $daysLate }}"
                                                data-days-remaining="{{ $daysRemaining }}"
                                                data-search="{{ mb_strtolower($inst->contract_number . ' ' . $inst->property_title . ' ' . ($inst->unit_number ?? '') . ' ' . $inst->owner_name . ' ' . $inst->tenant_name . ' ' . $inst->tenant_phone . ' ' . $inst->due_date . ' ' . $cycleAr) }}">
                                                <td class="py-3.5 px-3">
                                                    <div class="font-mono font-bold text-slate-900">{{ $inst->contract_number }}</div>
                                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 font-semibold">{{ $cycleAr }}</span>
                                                </td>
                                                <td class="py-3.5 px-3">
                                                    <div class="font-bold text-slate-900">{{ $inst->property_title }}</div>
                                                    <div class="text-[11px] text-emerald-700 font-semibold">{{ $inst->unit_number ? 'وحدة ' . $inst->unit_number : 'كامل العقار' }}</div>
                                                </td>
                                                <td class="py-3.5 px-3 font-semibold text-slate-800">{{ $inst->owner_name }}</td>
                                                <td class="py-3.5 px-3">
                                                    <div class="font-bold text-slate-900">{{ $inst->tenant_name }}</div>
                                                    <div class="font-mono text-[11px] text-emerald-700" dir="ltr">{{ $inst->tenant_phone }}</div>
                                                </td>
                                                <td class="py-3.5 px-2 text-center font-mono font-bold">#{{ $inst->installment_number }}</td>
                                                <td class="py-3.5 px-3 font-mono font-bold {{ $isOverdue ? 'text-rose-600' : 'text-slate-700' }}">{{ $inst->due_date }}</td>
                                                <td class="py-3.5 px-3 text-center">
                                                    <?php if ($isPaid) { ?>
                                                        <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 font-bold text-[10px]">مكتملة ✔</span>
                                                    <?php } elseif ($isOverdue) { ?>
                                                        <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-800 font-mono font-bold text-[11px]">متأخر {{ $daysLate }} يوم</span>
                                                    <?php } elseif ($daysRemaining === 0) { ?>
                                                        <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-900 font-bold text-[11px]">تستحق اليوم 🔔</span>
                                                    <?php } else { ?>
                                                        <span class="px-2 py-0.5 rounded bg-sky-50 text-sky-800 font-mono font-semibold text-[11px]">متبقي {{ $daysRemaining }} يوم</span>
                                                    <?php } ?>
                                                </td>
                                                <td class="py-3.5 px-3 font-mono font-bold">{{ number_format($inst->amount, 2) }}</td>
                                                <td class="py-3.5 px-3">
                                                    <div class="font-mono font-bold text-emerald-700">{{ number_format($inst->paid_amount, 2) }}</div>
                                                    <?php if ($paidPct > 0 && $paidPct < 100) { ?>
                                                        <div class="w-16 h-1 bg-slate-200 rounded-full overflow-hidden mt-1">
                                                            <div class="h-full bg-amber-500" style="width: {{ $paidPct }}%"></div>
                                                        </div>
                                                    <?php } ?>
                                                </td>
                                                <td class="py-3.5 px-3 font-mono font-extrabold {{ $remAmount > 0 ? 'text-rose-600' : 'text-slate-400' }}">{{ number_format($remAmount, 2) }} ر.س</td>
                                                <td class="py-3.5 px-3 text-center">
                                                    <?php if ($isPaid) { ?>
                                                        <span class="px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 font-bold text-[11px]">✅ مسددة</span>
                                                    <?php } elseif ($inst->paid_amount > 0) { ?>
                                                        <span class="px-2.5 py-1 rounded-lg bg-amber-100 text-amber-800 font-bold text-[11px]">⚠️ جزئي ({{ $paidPct }}%)</span>
                                                    <?php } elseif ($isOverdue) { ?>
                                                        <span class="px-2.5 py-1 rounded-lg bg-rose-100 text-rose-800 font-bold text-[11px]">🚨 متأخرة</span>
                                                    <?php } else { ?>
                                                        <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 font-bold text-[11px]">⏳ مجدولة</span>
                                                    <?php } ?>
                                                </td>
                                                <td class="py-3.5 px-3 text-center">
                                                    <div class="flex items-center justify-center gap-1 flex-wrap">
                                                        <?php if (!$isPaid) { ?>
                                                            <button type="button"
                                                                    onclick="openPayInstallmentModal({
                                                                        id: '{{ $inst->id }}',
                                                                        contract_no: '{{ $inst->contract_number }}',
                                                                        inst_no: '{{ $inst->installment_number }}',
                                                                        tenant_name: '{{ addslashes($inst->tenant_name) }}',
                                                                        owner_name: '{{ addslashes($inst->owner_name) }}',
                                                                        property_title: '{{ addslashes($inst->property_title) }}',
                                                                        remaining: '{{ $remAmount }}',
                                                                        due_date: '{{ $inst->due_date }}'
                                                                    })"
                                                                    class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-bold transition">
                                                                💳 سداد
                                                            </button>
                                                            <button type="button"
                                                                    onclick="sendInstallmentClaimOriginalPDF({
                                                                        contract_no: '{{ $inst->contract_number }}',
                                                                        inst_no: '{{ $inst->installment_number }}',
                                                                        tenant_name: '{{ addslashes($inst->tenant_name) }}',
                                                                        tenant_phone: '{{ $inst->tenant_phone }}',
                                                                        owner_name: '{{ addslashes($inst->owner_name) }}',
                                                                        property_title: '{{ addslashes($inst->property_title) }}',
                                                                        unit_number: '{{ addslashes($inst->unit_number ?: 'كامل العقار') }}',
                                                                        amount: '{{ number_format($inst->amount, 2) }}',
                                                                        paid: '{{ number_format($inst->paid_amount, 2) }}',
                                                                        remaining: '{{ number_format($remAmount, 2) }}',
                                                                        due_date: '{{ $inst->due_date }}',
                                                                        days_late: '{{ $daysLate }}'
                                                                    })"
                                                                    class="px-2.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-white text-[11px] font-bold transition"
                                                                    title="إرسال إشعار مطالبة مالية كملف PDF أصلي عبر الواتساب">
                                                                📄 مطالبة PDF واتساب
                                                            </button>
                                                            <a href="{{ route('office.dashboard', ['tab' => 'legal_notices', 'contract_id' => $inst->lease_contract_id]) }}"
                                                               class="px-2 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border border-rose-200 text-[11px] font-bold transition">
                                                                ⚖️ إنذار
                                                            </a>
                                                        <?php } else { ?>
                                                            <span class="text-[11px] text-emerald-700 font-bold">تم التحصيل ✔</span>
                                                        <?php } ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <tr><td colspan="12" class="py-10 text-center text-slate-400">لا توجد دفعات مجدولة حتى الآن.</td></tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- نافذة تسديد دفعة إيجار -->
                    <div id="payInstallmentModal" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
                            <div class="flex items-center justify-between pb-3 border-b">
                                <div>
                                    <h3 class="font-bold text-base text-slate-900">💳 تحصيل وتسديد دفعة إيجار</h3>
                                    <p id="pay_inst_subtitle" class="text-xs text-emerald-700 font-bold mt-0.5"></p>
                                </div>
                                <button type="button" onclick="closeModal('payInstallmentModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                            </div>
                            <form action="{{ route('office.installments.pay') }}" method="POST" class="space-y-3.5">
                                @csrf
                                <input type="hidden" name="installment_id" id="pay_inst_id">
                                <div class="p-3 rounded-2xl bg-slate-50 border text-xs space-y-1">
                                    <div>المستأجر: <b id="pay_inst_tenant" class="text-slate-900"></b></div>
                                    <div>المالك المرتبط: <b id="pay_inst_owner" class="text-emerald-700"></b></div>
                                    <div>العقار: <b id="pay_inst_prop" class="text-slate-700"></b></div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">المبلغ المحصّل (ر.س) *</label>
                                        <input type="number" step="0.01" name="amount" id="pay_inst_amount" required class="w-full rounded-xl border px-3 py-2 text-sm font-bold text-emerald-700">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">طريقة الدفع *</label>
                                        <select name="payment_method" class="w-full rounded-xl border px-3 py-2 text-xs bg-white font-bold">
                                            <?php foreach (($paymentMethods ?? collect()) as $pm) { ?>
                                                <option value="{{ $pm->name }}">{{ $pm->name }}</option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">حساب الإيداع (البنك/الصندوق)</label>
                                        <select name="debit_account_id" class="w-full rounded-xl border px-2.5 py-2 text-xs bg-white font-semibold">
                                            <?php foreach (($accounts ?? collect())->where('is_group', 0) as $acc) { ?>
                                                <option value="{{ $acc->id }}" {{ $acc->code == '1102' ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">تاريخ السداد *</label>
                                        <input type="date" name="voucher_date" value="{{ date('Y-m-d') }}" required class="w-full rounded-xl border px-3 py-2 text-xs font-mono">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">البيان *</label>
                                    <input type="text" name="notes" id="pay_inst_notes" required class="w-full rounded-xl border px-3 py-2 text-xs">
                                </div>
                                <div class="flex justify-end gap-2 pt-3 border-t">
                                    <button type="button" onclick="closeModal('payInstallmentModal')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                    <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">اعتماد السداد وإصدار السند</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- قالب خفي لتوليد إشعار مطالبة مالية PDF أصلي وإرساله للواتساب -->
                    <div class="hidden">
                        <div id="installmentClaimPdfTemplate" class="p-8 bg-white text-slate-900 space-y-5 border-2 border-slate-900 rounded-2xl" style="width: 720px; font-family: 'Readex Pro', sans-serif;" dir="rtl">
                            <div class="flex items-start justify-between border-b-2 border-slate-900 pb-4">
                                <div>
                                    <h2 class="text-lg font-extrabold">{{ $office->name }}</h2>
                                    <p class="text-xs text-slate-500">رخصة فال: {{ $office->fal_license ?: '-' }} | سجل تجاري: {{ $office->cr_number ?: '-' }} | جوال: {{ $office->phone }}</p>
                                </div>
                                <div class="px-4 py-2 rounded-xl bg-slate-900 text-white text-center">
                                    <div class="text-xs font-bold">إشعار مطالبة بسداد دفعة إيجار</div>
                                    <div class="text-[10px] font-mono text-emerald-300">{{ date('Y-m-d') }}</div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3 p-4 rounded-xl bg-slate-50 border text-xs">
                                <div>المستأجر: <b id="ic_pdf_tenant"></b></div>
                                <div>رقم العقد: <b id="ic_pdf_contract" class="font-mono"></b> (الدفعة #<span id="ic_pdf_inst_no"></span>)</div>
                                <div>العقار والوحدة: <b id="ic_pdf_prop"></b></div>
                                <div>المالك المؤجر: <b id="ic_pdf_owner"></b></div>
                                <div>تاريخ الاستحقاق: <b id="ic_pdf_due" class="font-mono text-rose-600"></b></div>
                                <div>الرصيد المتبقي المستحق: <b id="ic_pdf_rem" class="font-mono text-rose-600 text-sm"></b></div>
                            </div>
                            <div class="text-xs leading-relaxed space-y-2">
                                <p>المكرم المستأجر الموضح اسمه أعلاه، نشعركم بحلول موعد سداد الدفعة الإيجارية المستحقة بموجب عقد الإيجار المسجل لدينا، ونأمل المبادرة بسداد المبلغ المتبقي وتزويد إدارة الأملاك بإيصال التحويل لإصدار سند القبض الرسمي.</p>
                            </div>
                        </div>
                    </div>

                    <script>
                        // تفعيل جدول البحث الموحد على فلاتر المالك والعقار والمستأجر في صفحة الدفعات
                        document.addEventListener('DOMContentLoaded', function() {
                            ['instOwnerFilter', 'instPropertyFilter', 'instTenantFilter'].forEach(id => {
                                const el = document.getElementById(id);
                                if (el) {
                                    if (id === 'instOwnerFilter') el.setAttribute('name', 'owner_id');
                                    if (id === 'instPropertyFilter') el.setAttribute('name', 'property_id');
                                    if (id === 'instTenantFilter') el.setAttribute('name', 'tenant_id');
                                }
                            });
                            if (typeof window.initUnifiedTablePickers === 'function') {
                                window.initUnifiedTablePickers(document.getElementById('masterTabSectionActive'));
                            }
                            filterInstallmentsTable();

                            const masterSec = document.getElementById('masterTabSectionActive');
                            if (masterSec && masterSec.parentElement) {
                                let next = masterSec.nextElementSibling;
                                while (next) {
                                    const toRemove = next;
                                    next = next.nextElementSibling;
                                    toRemove.remove();
                                }
                            }
                        });

                        function openPayInstallmentModal(d) {
                            document.getElementById('pay_inst_id').value = d.id;
                            document.getElementById('pay_inst_subtitle').innerText = 'عقد رقم: ' + d.contract_no + ' — الدفعة #' + d.inst_no;
                            document.getElementById('pay_inst_tenant').innerText = d.tenant_name;
                            document.getElementById('pay_inst_owner').innerText = d.owner_name;
                            document.getElementById('pay_inst_prop').innerText = d.property_title;
                            document.getElementById('pay_inst_amount').value = d.remaining;
                            document.getElementById('pay_inst_notes').value = 'سداد الدفعة #' + d.inst_no + ' للعقد ' + d.contract_no + ' - ' + d.tenant_name;
                            openModal('payInstallmentModal');
                        }

                        function sendInstallmentClaimOriginalPDF(d) {
                            document.getElementById('ic_pdf_tenant').innerText = d.tenant_name;
                            document.getElementById('ic_pdf_contract').innerText = d.contract_no;
                            document.getElementById('ic_pdf_inst_no').innerText = d.inst_no;
                            document.getElementById('ic_pdf_prop').innerText = d.property_title + ' (' + d.unit_number + ')';
                            document.getElementById('ic_pdf_owner').innerText = d.owner_name;
                            document.getElementById('ic_pdf_due').innerText = d.due_date + (parseInt(d.days_late, 10) > 0 ? ' (متأخرة ' + d.days_late + ' يوم)' : '');
                            document.getElementById('ic_pdf_rem').innerText = d.remaining + ' ر.س';

                            const fileName = 'إشعار_مطالبة_دفعة_' + d.contract_no + '_' + d.tenant_name.replace(/\s+/g, '_') + '.pdf';
                            window.sendElementAsOriginalPdfWhatsapp('installmentClaimPdfTemplate', fileName, d.tenant_phone, 'legal');
                        }

                        function onInstOwnerFilterChange() {
                            const ownerId = String(document.getElementById('instOwnerFilter')?.value || '');
                            const propSel = document.getElementById('instPropertyFilter');
                            if (propSel) {
                                Array.from(propSel.options).forEach(opt => {
                                    if (!opt.value) { opt.hidden = false; return; }
                                    opt.hidden = (ownerId && String(opt.getAttribute('data-owner')) !== ownerId);
                                });
                                propSel.value = '';
                                if (propSel._syncTriggerUI) propSel._syncTriggerUI();
                            }
                            filterInstallmentsTable();
                        }

                        function applyQuickAgingBucket(bucket) {
                            const agingSel = document.getElementById('instAgingFilter');
                            const statusSel = document.getElementById('instStatusFilter');
                            if (agingSel) agingSel.value = bucket;
                            if (statusSel) {
                                if (bucket === 'all') statusSel.value = 'all';
                                else if (bucket.startsWith('upcoming')) statusSel.value = 'unpaid';
                                else statusSel.value = 'overdue';
                            }

                            document.querySelectorAll('.aging-bucket-card').forEach(c => {
                                c.classList.remove('ring-2', 'ring-slate-900');
                            });
                            const activeCard = document.getElementById('aging_card_' + bucket);
                            if (activeCard) activeCard.classList.add('ring-2', 'ring-slate-900');

                            filterInstallmentsTable();
                        }

                        function setInstallmentDateQuickFilter(mode) {
                            const now = new Date();
                            const yyyy = now.getFullYear();
                            const mm = String(now.getMonth() + 1).padStart(2, '0');
                            const lastDay = new Date(yyyy, now.getMonth() + 1, 0).getDate();
                            const fromEl = document.getElementById('instFromDate');
                            const toEl = document.getElementById('instToDate');

                            if (mode === 'this_month') {
                                fromEl.value = `${yyyy}-${mm}-01`;
                                toEl.value = `${yyyy}-${mm}-${String(lastDay).padStart(2, '0')}`;
                            } else if (mode === 'this_year') {
                                fromEl.value = `${yyyy}-01-01`;
                                toEl.value = `${yyyy}-12-31`;
                            }
                            filterInstallmentsTable();
                        }

                        function resetAllInstallmentFilters() {
                            if (document.getElementById('instSearchInput')) document.getElementById('instSearchInput').value = '';
                            ['instOwnerFilter', 'instPropertyFilter', 'instTenantFilter'].forEach(id => {
                                const el = document.getElementById(id);
                                if (el) {
                                    Array.from(el.options).forEach(o => o.hidden = false);
                                    el.value = '';
                                    if (el._syncTriggerUI) el._syncTriggerUI();
                                }
                            });
                            if (document.getElementById('instStatusFilter')) document.getElementById('instStatusFilter').value = 'all';
                            if (document.getElementById('instAgingFilter')) document.getElementById('instAgingFilter').value = 'all';
                            if (document.getElementById('instCycleFilter')) document.getElementById('instCycleFilter').value = 'all';
                            if (document.getElementById('instFromDate')) document.getElementById('instFromDate').value = '';
                            if (document.getElementById('instToDate')) document.getElementById('instToDate').value = '';
                            filterInstallmentsTable();
                        }

                        function filterInstallmentsByStatus(st) {
                            const sel = document.getElementById('instStatusFilter');
                            if (sel) {
                                sel.value = st;
                                filterInstallmentsTable();
                            }
                        }

                        function filterInstallmentsTable() {
                            const q = (document.getElementById('instSearchInput')?.value || '').trim().toLowerCase();
                            const ownerId = String(document.getElementById('instOwnerFilter')?.value || '');
                            const propId = String(document.getElementById('instPropertyFilter')?.value || '');
                            const tenantId = String(document.getElementById('instTenantFilter')?.value || '');
                            const status = document.getElementById('instStatusFilter')?.value || 'all';
                            const aging = document.getElementById('instAgingFilter')?.value || 'all';
                            const cycle = document.getElementById('instCycleFilter')?.value || 'all';
                            const fromDate = document.getElementById('instFromDate')?.value || '';
                            const toDate = document.getElementById('instToDate')?.value || '';

                            let matchedCount = 0;
                            let sumTotal = 0;
                            let sumPaid = 0;
                            let sumRem = 0;

                            const table = document.getElementById('officeInstallmentsMainTable');

                            document.querySelectorAll('.inst-row').forEach(r => {
                                const hay = (r.getAttribute('data-search') || '').toLowerCase();
                                const rOwner = String(r.getAttribute('data-owner-id') || '');
                                const rProp = String(r.getAttribute('data-property-id') || '');
                                const rTenant = String(r.getAttribute('data-tenant-id') || '');
                                const rCycle = r.getAttribute('data-cycle') || '';
                                const rDue = r.getAttribute('data-due-date') || '';
                                const isPaid = r.getAttribute('data-is-paid') === '1';
                                const isOverdue = r.getAttribute('data-is-overdue') === '1';
                                const isPartial = r.getAttribute('data-is-partial') === '1';
                                const daysLate = parseInt(r.getAttribute('data-days-late') || '0', 10);
                                const daysRem = parseInt(r.getAttribute('data-days-remaining') || '0', 10);

                                let show = true;
                                if (q && !hay.includes(q)) show = false;
                                if (ownerId && rOwner !== ownerId) show = false;
                                if (propId && rProp !== propId) show = false;
                                if (tenantId && rTenant !== tenantId) show = false;
                                if (cycle !== 'all' && rCycle !== cycle) show = false;
                                if (fromDate && rDue < fromDate) show = false;
                                if (toDate && rDue > toDate) show = false;

                                // فلتر حالة الدفعة
                                if (status === 'unpaid' && isPaid) show = false;
                                if (status === 'overdue' && !isOverdue) show = false;
                                if (status === 'partial' && !isPartial) show = false;
                                if (status === 'upcoming' && (isPaid || isOverdue)) show = false;
                                if (status === 'paid' && !isPaid) show = false;

                                // فلتر عمر الدين
                                if (aging === '1_30' && (!isOverdue || daysLate < 1 || daysLate > 30)) show = false;
                                if (aging === '31_60' && (!isOverdue || daysLate < 31 || daysLate > 60)) show = false;
                                if (aging === '61_90' && (!isOverdue || daysLate < 61 || daysLate > 90)) show = false;
                                if (aging === '90_plus' && (!isOverdue || daysLate <= 90)) show = false;
                                if (aging === 'upcoming_7' && (isPaid || isOverdue || daysRem > 7)) show = false;
                                if (aging === 'upcoming_30' && (isPaid || isOverdue || daysRem > 30)) show = false;

                                r.dataset.searchMatched = show ? '1' : '0';
                                r.style.display = show ? '' : 'none';

                                if (show) {
                                    matchedCount++;
                                    sumTotal += parseFloat(r.getAttribute('data-amount') || 0);
                                    sumPaid += parseFloat(r.getAttribute('data-paid') || 0);
                                    sumRem += parseFloat(r.getAttribute('data-rem') || 0);
                                }
                            });

                            if (table && typeof window.refreshTablePagination === 'function') {
                                window.refreshTablePagination(table, true);
                            }

                            const fmt = n => Number(n || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            if (document.getElementById('inst_filtered_count')) document.getElementById('inst_filtered_count').innerText = matchedCount;
                            if (document.getElementById('inst_filtered_total')) document.getElementById('inst_filtered_total').innerText = fmt(sumTotal);
                            if (document.getElementById('inst_filtered_paid')) document.getElementById('inst_filtered_paid').innerText = fmt(sumPaid);
                            if (document.getElementById('inst_filtered_rem')) document.getElementById('inst_filtered_rem').innerText = fmt(sumRem);
                        }
                    </script>
                </div>
            <?php } ?>

            <!-- ========================================================================= -->
            <!-- 3. تبويب مولد الإنذارات والمحاضر القانونية الذكي (8 خيارات قانونية شاملة) -->
            <!-- ========================================================================= -->
            <?php if (($tab ?? '') === 'legal_notices') { ?>
                <div id="masterTabSectionActive" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200 p-6 shadow-sm space-y-4">
                        <div class="border-b pb-3">
                            <h3 class="font-bold text-base text-slate-900">⚖️ مولد الإنذارات والمحاضر القانونية الذكي</h3>
                            <p class="text-xs text-slate-500 mt-0.5">اختر المستأجر من الجدول الموحد وحدد نوع الإجراء القانوني (8 نماذج قانونية معتمدة) مع إمكانية التعديل المباشر وإرسال ملف PDF أصلي عبر الواتساب</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-800 mb-1">1. اختر العقد / المستأجر من الجدول الموحد *</label>
                            <select id="legal_contract_select" onchange="generateSmartLegalNotice()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-bold">
                                <option value="">-- اختر المستأجر أو العقد من الجدول --</option>
                                <?php foreach (($contracts ?? collect()) as $c) {
                                    $cPaid = ($vouchers ?? collect())->where('tenant_id', $c->tenant_id)->where('voucher_type', 'receipt')->sum('amount');
                                    $cRem = max(0, $c->total_rent_amount - $cPaid);
                                ?>
                                    <option value="{{ $c->id }}"
                                            data-contract-no="{{ $c->contract_number }}"
                                            data-ejar-no="{{ $c->ejar_contract_number ?: 'غير موثق' }}"
                                            data-tenant="{{ $c->tenant_name }}"
                                            data-phone="{{ $c->tenant_phone }}"
                                            data-owner="{{ $c->owner_name }}"
                                            data-prop="{{ $c->property_title }}"
                                            data-unit="{{ $c->unit_number ?: 'كامل العقار' }}"
                                            data-deed="{{ $c->deed_number }}"
                                            data-rent="{{ number_format($c->total_rent_amount, 2) }}"
                                            data-overdue="{{ number_format($cRem, 2) }}"
                                            data-start="{{ $c->start_date }}"
                                            data-end="{{ $c->end_date }}"
                                            {{ request('contract_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->tenant_name }} — {{ $c->property_title }} (متبقي: {{ number_format($cRem, 2) }} ر.س)
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-800 mb-1">2. اختر نوع النموذج / الإجراء القانوني (8 خيارات) *</label>
                            <select id="legal_notice_type" onchange="generateSmartLegalNotice()" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-xs bg-white font-bold text-slate-900">
                                <option value="final_payment">1. إنذار نهائي بالسداد قبل محكمة التنفيذ (سند تنفيذي - إيجار)</option>
                                <option value="first_reminder">2. إشعار أول بمطالبة مالية وسداد قسط إيجار مستحق</option>
                                <option value="eviction_expiry">3. إشعار رسمي بإخلاء عين مؤجرة لانتهاء العقد وعدم التجديد</option>
                                <option value="eviction_breach">4. إنذار بفسخ عقد الإيجار وإخلاء فوري لمخالفة شروط العقد</option>
                                <option value="utilities_damages">5. مطالبة بتصفية فواتير الخدمات (كهرباء/مياه) وتكاليف الصيانة</option>
                                <option value="payment_undertaking">6. محضر جدولة مديونية متأخرة وتعهد خطي بالسداد</option>
                                <option value="handover_clearance">7. محضر استلام وتسليم وحدة عقارية ومخالصة نهائية (إبراء ذمة)</option>
                                <option value="lawyer_referral">8. مذكرة إحالة ملف مستأجر متعثر للشؤون القانونية / المحاماة</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">المهلة الممنوحة (بالأيام)</label>
                                <input type="number" id="legal_grace_days" value="5" min="1" max="90" oninput="generateSmartLegalNotice()" class="w-full rounded-xl border px-3 py-2 text-xs font-mono font-bold">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">المبلغ المطالب به (ر.س)</label>
                                <input type="text" id="legal_custom_amount" oninput="generateSmartLegalNotice()" placeholder="يتحدد تلقائياً" class="w-full rounded-xl border px-3 py-2 text-xs font-mono font-bold text-rose-600">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">شروط أو ملاحظات إضافية تُدرج في الوثيقة (اختياري)</label>
                            <textarea id="legal_extra_notes" rows="2" oninput="generateSmartLegalNotice()" placeholder="مثال: سداد فاتورة الكهرباء رقم (...) أو تسليم مفاتيح الوحدة لإدارة المكتب..." class="w-full rounded-xl border px-3 py-2 text-xs"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-2.5 pt-2">
                            <button type="button" onclick="exportElementToPDF('legalNoticePrintableArea', (window.currentLegalDocFilename || 'وثيقة_قانونية') + '.pdf')"
                                    class="py-2.5 px-4 rounded-xl bg-slate-900 text-white text-xs font-bold">
                                📥 تحميل PDF
                            </button>
                            <button type="button" onclick="sendLegalNoticeOriginalPdfWhatsapp()"
                                    class="py-2.5 px-4 rounded-xl bg-emerald-600 text-white text-xs font-bold">
                                💬 إرسال PDF أصلي للواتساب
                            </button>
                        </div>
                    </div>

                    <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200 p-6 shadow-sm space-y-3">
                        <div class="flex items-center justify-between text-xs text-slate-500 px-1 no-print">
                            <span>💡 يمكنك النقر مباشرة داخل نص الوثيقة بالأسفل لتعديل أي كلمة قبل التحميل أو الإرسال</span>
                            <button type="button" onclick="window.print()" class="px-3 py-1 rounded-lg bg-slate-100 text-slate-800 font-bold">🖨️ طباعة</button>
                        </div>
                        <div id="legalNoticePrintableArea" class="p-8 rounded-2xl border-2 border-slate-900 bg-white space-y-6 text-slate-900">
                            <div class="flex items-start justify-between border-b-2 border-slate-900 pb-4">
                                <div>
                                    <h2 class="text-lg font-extrabold">{{ $office->name }}</h2>
                                    <p class="text-xs text-slate-500 mt-0.5">رخصة فال: {{ $office->fal_license ?: '-' }} | سجل تجاري: {{ $office->cr_number ?: '-' }} | جوال: {{ $office->phone }}</p>
                                </div>
                                <div class="text-center px-5 py-2 rounded-xl bg-slate-900 text-white">
                                    <div id="ln_doc_title" class="text-sm font-extrabold">إنذار عدلي / مطالبة مالية رسمية</div>
                                    <div class="text-[10px] text-emerald-300 font-mono mt-0.5">التاريخ: {{ date('Y-m-d') }}</div>
                                </div>
                            </div>

                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 grid grid-cols-2 gap-3 text-xs">
                                <div>الطرف المعني (المستأجر): <b id="ln_tenant_name" class="text-slate-900">-- اختر المستأجر --</b></div>
                                <div>رقم الجوال: <b id="ln_tenant_phone" class="font-mono" dir="ltr">-</b></div>
                                <div>العقار والوحدة: <b id="ln_property_unit">-</b></div>
                                <div>المالك المؤجر: <b id="ln_owner_name">-</b></div>
                                <div>رقم العقد / إيجار الموحد: <b id="ln_contract_no" class="font-mono">-</b></div>
                                <div>المبلغ محل المطالبة / التسوية: <b id="ln_overdue_amount" class="font-mono text-rose-600 text-sm">0.00 ر.س</b></div>
                            </div>

                            <div id="ln_body_content" contenteditable="true" class="text-sm leading-relaxed space-y-3 text-justify font-medium text-slate-800 py-3 px-2 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400">
                                يرجى اختيار العقد أو المستأجر من جدول البحث الجانبي لتوليد الوثيقة القانونية الرسمية تلقائياً.
                            </div>

                            <div class="grid grid-cols-3 gap-4 pt-8 border-t border-dashed border-slate-300 text-xs text-center">
                                <div>
                                    <p class="font-bold text-slate-800 mb-6">إدارة الأملاك والشؤون القانونية</p>
                                    <p>{{ $office->name }}</p>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800 mb-6">توقيع الطرف المعني / المستلم</p>
                                    <p>....................................</p>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800 mb-6">الختم والاعتماد الرسمي</p>
                                    <p class="text-emerald-700 font-bold">معتمد رسمياً ✔</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        window.currentLegalDocFilename = 'إنذار_قانوني';

                        function generateSmartLegalNotice() {
                            const sel = document.getElementById('legal_contract_select');
                            if (!sel || !sel.value) return;
                            const opt = sel.options[sel.selectedIndex];

                            const tenant = opt.getAttribute('data-tenant') || '';
                            const phone = opt.getAttribute('data-phone') || '';
                            const owner = opt.getAttribute('data-owner') || '';
                            const prop = opt.getAttribute('data-prop') || '';
                            const unit = opt.getAttribute('data-unit') || '';
                            const deed = opt.getAttribute('data-deed') || '-';
                            const contractNo = opt.getAttribute('data-contract-no') || '';
                            const ejarNo = opt.getAttribute('data-ejar-no') || '';
                            const endDate = opt.getAttribute('data-end') || '';
                            const defaultOverdue = opt.getAttribute('data-overdue') || '0.00';

                            const customAmtInp = document.getElementById('legal_custom_amount');
                            const finalAmount = (customAmtInp && customAmtInp.value.trim() !== '') ? customAmtInp.value.trim() : defaultOverdue;
                            const graceDays = document.getElementById('legal_grace_days')?.value || '5';
                            const noticeType = document.getElementById('legal_notice_type')?.value || 'final_payment';
                            const extraNotes = document.getElementById('legal_extra_notes')?.value || '';

                            document.getElementById('ln_tenant_name').innerText = tenant;
                            document.getElementById('ln_tenant_phone').innerText = phone;
                            document.getElementById('ln_property_unit').innerText = prop + ' (' + unit + ')';
                            document.getElementById('ln_owner_name').innerText = owner;
                            document.getElementById('ln_contract_no').innerText = contractNo + ' / ' + ejarNo;
                            document.getElementById('ln_overdue_amount').innerText = finalAmount + ' ر.س';

                            let titleText = '';
                            let bodyHtml = '';

                            switch (noticeType) {
                                case 'final_payment':
                                    titleText = 'إنذار نهائي بالسداد قبل محكمة التنفيذ';
                                    bodyHtml = `
                                        <p>المكرم المستأجر / <b>${tenant}</b>، نشعركم بصفتنا وكلاء ومديري أملاك العقار (<b>${prop} - ${unit}</b>) بصك رقم (<b>${deed}</b>) المملوك للسيد/ <b>${owner}</b> والمؤجر لكم بموجب عقد الإيجار رقم (<b>${contractNo}</b>) الموثق في شبكة إيجار برقم موحد (<b>${ejarNo}</b>).</p>
                                        <p>حيث تبين تخلفكم عن سداد المستحقات الإيجارية الحالة في ذمتكم والبالغ إجماليها (<b>${finalAmount} ريال سعودي</b>)، فإننا نوجّه إليكم هذا <b>الإنذار النهائي</b> ونمهلكم مدة أقصاها (<b>${graceDays} أيام</b>) من تاريخه لسداد كامل المبلغ المستحق.</p>
                                        <p>وفي حال انقضاء المهلة دون السداد، سيتم فوراً تقديم عقد الإيجار الموحد باعتباره <b>سنداً تنفيذياً</b> مباشرة إلى <b>محكمة التنفيذ</b> لإصدار أوامر التنفيذ القضائي (إيقاف الخدمات، الحجز على الحسابات البنكية، والمنع من السفر) والمطالبة بإخلاء العين المؤجرة وفق الأنظمة المرعية في المملكة العربية السعودية.</p>
                                    `;
                                    break;

                                case 'first_reminder':
                                    titleText = 'إشعار أول بمطالبة مالية وسداد قسط إيجار';
                                    bodyHtml = `
                                        <p>المكرم المستأجر / <b>${tenant}</b> المحترم، تحية طيبة وبعد:</p>
                                        <p>نود تذكيركم بحلول واستحقاق الدفعة الإيجارية الخاصة بالعقار (<b>${prop} - ${unit}</b>) بموجب العقد رقم (<b>${contractNo}</b>)، حيث يبلغ الرصيد المستحق حالياً (<b>${finalAmount} ريال سعودي</b>).</p>
                                        <p>نأمل منكم المبادرة بتحويل المبلغ المستحق خلال (<b>${graceDays} أيام</b>) من تاريخ هذا الإشعار وتزويدنا بإيصال السداد لإصدار سند القبض الرسمي وتحديث سجل العقد في منصة إيجار.</p>
                                    `;
                                    break;

                                case 'eviction_expiry':
                                    titleText = 'إشعار رسمي بإخلاء عين مؤجرة لانتهاء العقد';
                                    bodyHtml = `
                                        <p>المكرم المستأجر / <b>${tenant}</b>، إشارة إلى عقد الإيجار رقم (<b>${contractNo}</b>) الموثق برقم (<b>${ejarNo}</b>) والخاص بالوحدة العقارية (<b>${unit}</b>) في عقار (<b>${prop}</b>) والذي ينتهي بتاريخ (<b>${endDate}</b>).</p>
                                        <p>نفيدكم رسمياً بعدم رغبة المالك في تجديد العقد لفترة أخرى، ونطلب منكم اتخاذ كافة الترتيبات اللازمة لإخلاء وتسليم العين المؤجرة خالية من الشواغل وبنفس الحالة التي تم استلامها عليها، مع تصفية الرصيد المالي المتبقي وقدره (<b>${finalAmount} ريال سعودي</b>) خلال مهلة لا تتجاوز (<b>${graceDays} أيام</b>).</p>
                                    `;
                                    break;

                                case 'eviction_breach':
                                    titleText = 'إنذار بفسخ عقد الإيجار وإخلاء فوري للمخالفة';
                                    bodyHtml = `
                                        <p>المكرم المستأجر / <b>${tenant}</b>، استناداً إلى بنود وأحكام عقد الإيجار الموحد رقم (<b>${ejarNo}</b>) الخاص بالوحدة (<b>${unit} - ${prop}</b>).</p>
                                        <p>نظراً لإخلالكم بالتزاماتكم التعاقدية وتراكم المستحقات المالية البالغة (<b>${finalAmount} ريال سعودي</b>)، فإننا ننذركم بفسخ العقد وإلزامكم بإخلاء الوحدة العقارية وتسليمها للمكتب خلال (<b>${graceDays} أيام</b>) من تاريخه، مع احتفاظ المالك والمكتب بكامل الحق النظامي في المطالبة بالأجرة المتأخرة والتعويضات أمام الجهات القضائية المختصة.</p>
                                    `;
                                    break;

                                case 'utilities_damages':
                                    titleText = 'مطالبة بتصفية فواتير الخدمات وتكاليف الصيانة';
                                    bodyHtml = `
                                        <p>المكرم المستأجر / <b>${tenant}</b>، بالإشارة إلى الوحدة المؤجرة لكم (<b>${unit}</b>) في عقار (<b>${prop}</b>) بموجب العقد رقم (<b>${contractNo}</b>).</p>
                                        <p>نطالبكم بسرعة سداد وتصفية المبالغ المترتبة على الوحدة من فواتير خدمات (كهرباء / مياه) وتكاليف إصلاح التلفيات والصيانة غير الاستهلاكية ومستحقات الإيجار والبالغ إجماليها (<b>${finalAmount} ريال سعودي</b>) خلال مهلة أقصاها (<b>${graceDays} أيام</b>) من تاريخ هذا الخطاب.</p>
                                    `;
                                    break;

                                case 'payment_undertaking':
                                    titleText = 'محضر جدولة مديونية متأخرة وتعهد خطي بالسداد';
                                    bodyHtml = `
                                        <p>إنه في يوم <b>{{ date('Y-m-d') }}</b>، حضر الطرف الثاني (المستأجر): <b>${tenant}</b>، وأقرّ إقراراً شرعياً ونظامياً بصحة وانشغال ذمته بمبلغ قدره (<b>${finalAmount} ريال سعودي</b>) يمثل مستحقات إيجارية متأخرة عن العقار (<b>${prop} - ${unit}</b>) بموجب العقد (<b>${contractNo}</b>).</p>
                                        <p>وقد تعهّد المستأجر بسداد هذا المبلغ في موعد أقصاه (<b>${graceDays} أيام</b>) من تاريخه دون أي مماطلة أو تأخير، وفي حال الإخلال بهذا التعهد تسقط كافة المهل الممنوحة ويحق للمكتب فوراً مباشرة إجراءات التنفيذ القضائي وإخلاء العقار.</p>
                                    `;
                                    break;

                                case 'handover_clearance':
                                    titleText = 'محضر استلام وتسليم وحدة عقارية ومخالصة نهائية';
                                    bodyHtml = `
                                        <p>بعونه تعالى بتاريخ <b>{{ date('Y-m-d') }}</b>، تم الشخوص إلى الوحدة العقارية (<b>${unit}</b>) في عقار (<b>${prop}</b>) المؤجرة سابقاً للسيد/ <b>${tenant}</b> بموجب العقد رقم (<b>${contractNo}</b>).</p>
                                        <p>وقد تم معاينة الوحدة واستلام مفاتيحها بعد التحقق من سلامة مرافقها وتصفية الحساب المالي المتبقي وقدره (<b>${finalAmount} ريال سعودي</b>)، ويُعد هذا المحضر بمثابة إثبات استلام للعين المؤجرة ومخالصة وفق ما هو مدوّن أعلاه.</p>
                                    `;
                                    break;

                                case 'lawyer_referral':
                                    titleText = 'مذكرة إحالة ملف مديونية متعثرة للشؤون القانونية';
                                    bodyHtml = `
                                        <p><b>إلى: الإدارة القانونية / مكتب المحاماة والتنفيذ القضائي</b></p>
                                        <p>نحيل إليكم ملف العقد المتعثر رقم (<b>${contractNo}</b>) الموثق في منصة إيجار برقم (<b>${ejarNo}</b>) ضد المستأجر (<b>${tenant}</b> - جوال: <span dir="ltr">${phone}</span>) عن العقار (<b>${prop} - ${unit}</b>) المملوك للسيد/ <b>${owner}</b>.</p>
                                        <p>إجمالي المديونية المستحقة واجبة التنفيذ: (<b>${finalAmount} ريال سعودي</b>). نأمل قيد طلب التنفيذ لدى محكمة التنفيذ فوراً واتخاذ إجراءات الحجز والإخلاء النظامية.</p>
                                    `;
                                    break;
                            }

                            if (extraNotes.trim() !== '') {
                                bodyHtml += `<p class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs"><b>بنود وملاحظات إضافية:</b> ${extraNotes}</p>`;
                            }

                            document.getElementById('ln_doc_title').innerText = titleText;
                            document.getElementById('ln_body_content').innerHTML = bodyHtml;
                            window.currentLegalDocFilename = titleText.replace(/\s+/g, '_') + '_' + tenant.replace(/\s+/g, '_');
                        }

                        function sendLegalNoticeOriginalPdfWhatsapp() {
                            const sel = document.getElementById('legal_contract_select');
                            if (!sel || !sel.value) {
                                alert('يرجى اختيار المستأجر أو العقد أولاً من جدول البحث.');
                                return;
                            }
                            const opt = sel.options[sel.selectedIndex];
                            const phone = opt.getAttribute('data-phone') || '';
                            window.sendElementAsOriginalPdfWhatsapp('legalNoticePrintableArea', (window.currentLegalDocFilename || 'إنذار_قانوني') + '.pdf', phone, 'legal');
                        }

                        document.addEventListener('DOMContentLoaded', function() {
                            generateSmartLegalNotice();
                            const masterSec = document.getElementById('masterTabSectionActive');
                            if (masterSec && masterSec.parentElement) {
                                let next = masterSec.nextElementSibling;
                                while (next) {
                                    const toRemove = next;
                                    next = next.nextElementSibling;
                                    toRemove.remove();
                                }
                            }
                        });
                    </script>
                </div>
            <?php } ?>

            <!-- ========================================================================= -->
            <!-- 4. تبويب إعدادات الربط مع واتساب + التنبيهات الآلية المطورة + الشذوذ والاعتماد -->
            <!-- ========================================================================= -->
            <?php if (($tab ?? '') === 'whatsapp') {
                $remCfg = json_decode($office->reminder_settings_json ?? '{}', true) ?: [];
                $sendToTenant       = $remCfg['send_to_tenant'] ?? 1;
                $sendToOwner        = $remCfg['send_to_owner'] ?? 0;
                $sendToOfficeAdmin  = $remCfg['send_to_office_admin'] ?? 1;
                $selectedStaffIds   = $remCfg['selected_staff_ids'] ?? [];
                $customRecipients   = $remCfg['custom_recipients'] ?? [];

                $notifyInstBefore   = $remCfg['notify_installments_before'] ?? 1;
                $notifySecondChance = $remCfg['notify_second_chance'] ?? 1;
                $secondDaysBefore   = $remCfg['second_days_before'] ?? 3;
                $notifyOnDueDay     = $remCfg['notify_on_due_day'] ?? 1;
                $notifyOverdue      = $remCfg['notify_overdue_after'] ?? 1;
                $overdueDaysAfter   = $remCfg['overdue_days_after'] ?? 7;
                $notifyExpiry       = $remCfg['notify_contract_expiry'] ?? 1;
                $contractExpiryDays = $remCfg['contract_expiry_days'] ?? 30;
                $preferredSendTime  = $remCfg['preferred_send_time'] ?? '09:00';
                $skipFullyPaid      = $remCfg['skip_fully_paid'] ?? 1;

                $overdueTemplate = $remCfg['overdue_template'] ?? '⚠️ إشعار مطالبة سداد: المكرم {اسم_المستأجر}، نفيدكم بتأخر سداد الدفعة المستحقة للعقد {رقم_العقد} ({اسم_العقار} - {رقم_الوحدة}) بمبلغ متبقي {المبلغ_المتبقي} ر.س منذ تاريخ {تاريخ_الاستحقاق}. نأمل سرعة السداد. {اسم_المكتب}';
                $ownerTemplate   = $remCfg['owner_template'] ?? '👑 إشعار للمالك {اسم_المالك}: نحيطكم علماً بأن دفعة المستأجر ({اسم_المستأجر}) للعقار ({اسم_العقار} - {رقم_الوحدة}) بمبلغ {المبلغ_المتبقي} ر.س تاريخ استحقاقها {تاريخ_الاستحقاق}. إدارة {اسم_المكتب}.';
                $isCustomWa = (!empty($office->use_custom_whatsapp) || ($office->whatsapp_mode ?? '') === 'custom');
                $localWaSender = \App\Http\Controllers\CompanyErpController::toLocalPhone($office->whatsapp_sender_phone ?? '');
            ?>
                <div id="masterTabSectionActive" class="space-y-6">

                    <!-- شريط علوي لفحص وتشغيل التنبيهات الآلية فوراً -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="font-bold text-lg text-slate-900">💬 مركز إعدادات واتساب والتنبيهات الآلية الذكية</h3>
                            <p class="text-xs text-slate-500 mt-0.5">إدارة الربط البرمجي (API)، تخصيص مستلمي التنبيهات الآلية، وشروط اعتماد السندات وكاشف الشذوذ المحاسبي</p>
                        </div>
                        <form action="{{ route('office.whatsapp.run_reminders') }}" method="POST">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-lg flex items-center gap-2 transition">
                                <span>🚀 فحص الدفعات والعقود وإرسال التنبيهات المستحقة الآن</span>
                            </button>
                        </form>
                    </div>

                    <!-- النموذج الأول: إعدادات الربط مع واتساب + إعدادات التنبيهات الآلية التلقائية المطورة -->
                    <form action="{{ route('office.whatsapp_settings.update') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- 1. إعدادات بوابة الربط مع واتساب (افتراضي الشركة أو خاص بالمكتب) -->
                        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm space-y-4">
                            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
                                <div>
                                    <h4 class="font-bold text-base text-slate-900">🔗 أولاً: إعدادات الربط التقني مع بوابة واتساب (WhatsApp API)</h4>
                                    <p class="text-xs text-slate-500">يمكنك استخدام ربط المنصة المركزي مجاناً أو ربط رقم مكتبك الخاص عبر GreatWhats API</p>
                                </div>
                                <select name="whatsapp_mode" onchange="document.getElementById('customOfficeWaBox').classList.toggle('hidden', this.value !== 'custom');"
                                        class="rounded-xl border border-slate-300 px-3.5 py-2 text-xs bg-slate-50 font-bold text-slate-900">
                                    <option value="company" {{ !$isCustomWa ? 'selected' : '' }}>استخدام بوابة المنصة الافتراضية ({{ $companyAdminPhone ?? '' }})</option>
                                    <option value="custom" {{ $isCustomWa ? 'selected' : '' }}>تفعيل رقم واتساب خاص بالمكتب (Custom API)</option>
                                </select>
                            </div>

                            <div id="customOfficeWaBox" class="{{ $isCustomWa ? '' : 'hidden' }} grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">رقم جوال الواتساب المرسل للمكتب (10 أرقام)</label>
                                    <input type="tel" name="whatsapp_sender_phone" value="{{ $localWaSender }}" maxlength="10" dir="ltr"
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                                           placeholder="05XXXXXXXX" class="w-full text-left font-mono rounded-xl border px-3 py-2 text-xs bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">معرف الجلسة (Instance ID)</label>
                                    <input type="text" name="whatsapp_instance_id" value="{{ $office->whatsapp_instance_id ?? '' }}" dir="ltr"
                                           placeholder="instance_xxx" class="w-full text-left font-mono rounded-xl border px-3 py-2 text-xs bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">رابط الـ API (Endpoint URL)</label>
                                    <input type="url" name="whatsapp_api_url" value="{{ $office->whatsapp_api_url ?: 'https://api.greatwhats.com/v1/messages' }}" dir="ltr"
                                           class="w-full text-left font-mono rounded-xl border px-3 py-2 text-xs bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">مفتاح الربط السري (API Token)</label>
                                    <input type="text" name="whatsapp_api_token" value="{{ $office->whatsapp_api_token ?? '' }}" dir="ltr"
                                           placeholder="أدخل التوكن السري..." class="w-full text-left font-mono rounded-xl border px-3 py-2 text-xs bg-white">
                                </div>
                            </div>
                        </div>

                        <!-- 2. بطاقة إعدادات التنبيهات الآلية التلقائية المطورة -->
                        <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-6">
                            <!-- الترويسة وزر التفعيل الرئيسي -->
                            <div class="flex flex-wrap items-center justify-between gap-4 p-4 rounded-2xl bg-slate-900 text-white">
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-xl bg-emerald-500/20 border border-emerald-400/40 flex items-center justify-center text-xl">
                                        🔔
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-base text-white">إعدادات التنبيهات الآلية التلقائية الذكية</h4>
                                        <p class="text-xs text-slate-300 mt-0.5">عند تفعيل هذا الخيار يرسل النظام التنبيهات آلياً في موعدها لمن تحددهم أدناه، وعند إيقافه يتوقف الإرسال</p>
                                    </div>
                                </div>

                                <label class="inline-flex items-center gap-3 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/15 border border-white/20 cursor-pointer transition">
                                    <input type="checkbox" name="enable_auto_reminders" value="1"
                                           {{ ($office->enable_auto_reminders ?? 1) ? 'checked' : '' }}
                                           onchange="document.getElementById('autoRemindersMasterBody').classList.toggle('opacity-50', !this.checked);"
                                           class="w-5 h-5 rounded text-emerald-500 focus:ring-0">
                                    <span class="text-xs font-extrabold text-emerald-300">تفعيل الإرسال الآلي للتنبيهات</span>
                                </label>
                            </div>

                            <div id="autoRemindersMasterBody" class="space-y-6 {{ ($office->enable_auto_reminders ?? 1) ? '' : 'opacity-50' }}">

                                <!-- القسم الأول: تحديد المستلمين بحرية كاملة (لمن تُرسل التنبيهات؟) -->
                                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 pb-3">
                                        <div>
                                            <h5 class="font-bold text-sm text-slate-900">👥 أولاً: حدد لمن تُرسل التنبيهات (تحكم كامل بالمستلمين)</h5>
                                            <p class="text-[11px] text-slate-500">يمكنك تفعيل طرف واحد أو عدة أطراف معاً، واختيار موظفين محددين بالاسم أو إضافة أرقام جوال خارجية يدوياً</p>
                                        </div>
                                        <span class="px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 text-[11px] font-bold">توزيع متعدد ذكي ✔</span>
                                    </div>

                                    <!-- الأطراف الأساسية بالنظام -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        <label class="flex items-start gap-3 p-3.5 rounded-xl bg-white border border-slate-200 cursor-pointer hover:border-slate-900 transition">
                                            <input type="checkbox" name="send_to_tenant" value="1" {{ $sendToTenant ? 'checked' : '' }} class="mt-1 w-4 h-4 rounded text-slate-900">
                                            <div>
                                                <div class="font-bold text-xs text-slate-900">👤 المستأجر (صاحب العقد/الدفعة)</div>
                                                <div class="text-[11px] text-slate-500 mt-0.5">يُرسل تلقائياً لرقم جوال المستأجر المرتبط بالعقد</div>
                                            </div>
                                        </label>

                                        <label class="flex items-start gap-3 p-3.5 rounded-xl bg-white border border-slate-200 cursor-pointer hover:border-slate-900 transition">
                                            <input type="checkbox" name="send_to_owner" value="1" {{ $sendToOwner ? 'checked' : '' }} class="mt-1 w-4 h-4 rounded text-slate-900">
                                            <div>
                                                <div class="font-bold text-xs text-slate-900">👑 مالك العقار (المؤجر)</div>
                                                <div class="text-[11px] text-slate-500 mt-0.5">إشعار المالك بحلول دفعة إيجار أو قرب انتهاء عقد في عقاره</div>
                                            </div>
                                        </label>

                                        <label class="flex items-start gap-3 p-3.5 rounded-xl bg-white border border-slate-200 cursor-pointer hover:border-slate-900 transition">
                                            <input type="checkbox" name="send_to_office_admin" value="1" {{ $sendToOfficeAdmin ? 'checked' : '' }} class="mt-1 w-4 h-4 rounded text-slate-900">
                                            <div>
                                                <div class="font-bold text-xs text-slate-900">🏢 جوال إدارة المكتب الرسمي</div>
                                                <div class="text-[11px] text-slate-500 mt-0.5">نسخة إدارية لجوال المكتب (<span class="font-mono" dir="ltr">{{ $office->phone }}</span>)</div>
                                            </div>
                                        </label>
                                    </div>

                                    <!-- اختيار موظفين محددين من طاقم المكتب -->
                                    <div class="p-4 rounded-xl bg-white border border-slate-200 space-y-2.5">
                                        <div class="flex items-center justify-between">
                                            <label class="text-xs font-bold text-slate-800">👨‍💼 تحديد موظفين معينين من المكتب لاستلام نسخة من التنبيهات:</label>
                                            <span class="text-[11px] text-slate-400">اختر من يتابع التحصيل من موظفيك</span>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                            @forelse(($staff ?? collect()) as $stMember)
                                                <label class="flex items-center gap-2 p-2.5 rounded-lg bg-slate-50 border border-slate-200 text-xs cursor-pointer hover:bg-slate-100">
                                                    <input type="checkbox" name="selected_staff_ids[]" value="{{ $stMember->id }}"
                                                           {{ in_array($stMember->id, $selectedStaffIds) ? 'checked' : '' }}
                                                           class="rounded text-slate-900">
                                                    <div class="truncate">
                                                        <span class="font-bold text-slate-900">{{ $stMember->name }}</span>
                                                        <span class="block font-mono text-[10px] text-emerald-700" dir="ltr">{{ $stMember->phone }}</span>
                                                    </div>
                                                </label>
                                            @empty
                                                <div class="col-span-3 text-xs text-slate-400">لا يوجد موظفون مضافون حالياً.</div>
                                            @endforelse
                                        </div>
                                    </div>

                                    <!-- إضافة أرقام جوال مخصصة يدوياً -->
                                    <div class="p-4 rounded-xl bg-white border border-slate-200 space-y-3">
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <div>
                                                <label class="text-xs font-bold text-slate-900">📲 أرقام جوال إضافية يحددها صاحب المكتب يدوياً:</label>
                                                <p class="text-[11px] text-slate-500">أضف أي رقم جوال تريد أن تصله التنبيهات (مثل: رقم شريك، محاسب، أو مسؤول تحصيل)</p>
                                            </div>
                                            <button type="button" onclick="addCustomReminderRecipientRow()"
                                                    class="px-3.5 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-bold">
                                                + إضافة رقم مستلم جديد
                                            </button>
                                        </div>

                                        <div id="customReminderRecipientsContainer" class="space-y-2">
                                            @forelse($customRecipients as $cr)
                                                <div class="grid grid-cols-12 gap-2 items-center custom-recipient-row">
                                                    <div class="col-span-5">
                                                        <input type="text" name="custom_recipient_names[]" value="{{ $cr['name'] ?? '' }}"
                                                               placeholder="اسم المستلم أو صفته (مثال: مسؤول التحصيل)"
                                                               class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-slate-50 font-semibold">
                                                    </div>
                                                    <div class="col-span-6">
                                                        <input type="tel" name="custom_recipient_phones[]" value="{{ $cr['phone'] ?? '' }}" maxlength="10" dir="ltr"
                                                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                                                               placeholder="05XXXXXXXX"
                                                               class="w-full text-left font-mono rounded-xl border border-slate-300 px-3 py-2 text-xs bg-slate-50 font-bold">
                                                    </div>
                                                    <div class="col-span-1 text-center">
                                                        <button type="button" onclick="this.closest('.custom-recipient-row').remove()"
                                                                class="px-2.5 py-2 rounded-xl bg-rose-50 text-rose-600 text-xs font-bold">🗑️</button>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="grid grid-cols-12 gap-2 items-center custom-recipient-row">
                                                    <div class="col-span-5">
                                                        <input type="text" name="custom_recipient_names[]"
                                                               placeholder="اسم المستلم أو صفته (اختياري - مثال: المحاسب)"
                                                               class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-slate-50 font-semibold">
                                                    </div>
                                                    <div class="col-span-6">
                                                        <input type="tel" name="custom_recipient_phones[]" maxlength="10" dir="ltr"
                                                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                                                               placeholder="05XXXXXXXX (اتركه فارغاً إن لم ترغب)"
                                                               class="w-full text-left font-mono rounded-xl border border-slate-300 px-3 py-2 text-xs bg-slate-50 font-bold">
                                                    </div>
                                                    <div class="col-span-1 text-center">
                                                        <button type="button" onclick="if(document.querySelectorAll('.custom-recipient-row').length > 1) this.closest('.custom-recipient-row').remove();"
                                                                class="px-2.5 py-2 rounded-xl bg-rose-50 text-rose-600 text-xs font-bold">🗑️</button>
                                                    </div>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                                <!-- القسم الثاني: جدولة مراحل ومواعيد التنبيهات المتعددة -->
                                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                                    <h5 class="font-bold text-sm text-slate-900 border-b border-slate-200 pb-2.5">⏱️ ثانياً: مراحل ومواعيد إرسال التنبيهات التلقائية</h5>

                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5">
                                        <div class="p-4 rounded-xl bg-white border border-slate-200 space-y-2">
                                            <label class="flex items-center gap-2 text-xs font-bold text-slate-900 cursor-pointer">
                                                <input type="checkbox" name="notify_installments_before" value="1" {{ $notifyInstBefore ? 'checked' : '' }} class="rounded text-slate-900">
                                                <span>1. تنبيه مبكر قبل حلول الدفعة</span>
                                            </label>
                                            <div class="flex items-center gap-2 pt-1">
                                                <span class="text-[11px] text-slate-500">قبل الاستحقاق بـ:</span>
                                                <input type="number" name="reminder_days_before" value="{{ $office->reminder_days_before ?? 15 }}" min="1" max="120"
                                                       class="w-20 rounded-lg border px-2.5 py-1 text-xs font-mono font-bold text-center">
                                                <span class="text-[11px] text-slate-600 font-bold">يوماً</span>
                                            </div>
                                        </div>

                                        <div class="p-4 rounded-xl bg-white border border-slate-200 space-y-2">
                                            <label class="flex items-center gap-2 text-xs font-bold text-slate-900 cursor-pointer">
                                                <input type="checkbox" name="notify_second_chance" value="1" {{ $notifySecondChance ? 'checked' : '' }} class="rounded text-slate-900">
                                                <span>2. تذكير تأكيدي ثانٍ قبل الموعد</span>
                                            </label>
                                            <div class="flex items-center gap-2 pt-1">
                                                <span class="text-[11px] text-slate-500">قبل الاستحقاق بـ:</span>
                                                <input type="number" name="second_days_before" value="{{ $secondDaysBefore }}" min="1" max="30"
                                                       class="w-20 rounded-lg border px-2.5 py-1 text-xs font-mono font-bold text-center">
                                                <span class="text-[11px] text-slate-600 font-bold">أيام</span>
                                            </div>
                                        </div>

                                        <div class="p-4 rounded-xl bg-white border border-slate-200 space-y-2">
                                            <label class="flex items-center gap-2 text-xs font-bold text-slate-900 cursor-pointer">
                                                <input type="checkbox" name="notify_on_due_day" value="1" {{ $notifyOnDueDay ? 'checked' : '' }} class="rounded text-slate-900">
                                                <span>3. إشعار في نفس يوم الاستحقاق</span>
                                            </label>
                                            <p class="text-[11px] text-slate-500 pt-1">يرسل تذكيراً مباشراً صباح يوم حلول القسط الإيجاري</p>
                                        </div>

                                        <div class="p-4 rounded-xl bg-white border border-rose-200 space-y-2">
                                            <label class="flex items-center gap-2 text-xs font-bold text-rose-800 cursor-pointer">
                                                <input type="checkbox" name="notify_overdue_after" value="1" {{ $notifyOverdue ? 'checked' : '' }} class="rounded text-rose-600">
                                                <span>4. تنبيه المتأخرات (بعد فوات الموعد) 🚨</span>
                                            </label>
                                            <div class="flex items-center gap-2 pt-1">
                                                <span class="text-[11px] text-slate-500">متابعة المتأخرات حتى:</span>
                                                <input type="number" name="overdue_days_after" value="{{ $overdueDaysAfter }}" min="1" max="365"
                                                       class="w-20 rounded-lg border px-2.5 py-1 text-xs font-mono font-bold text-center text-rose-700">
                                                <span class="text-[11px] text-slate-600 font-bold">يوماً</span>
                                            </div>
                                        </div>

                                        <div class="p-4 rounded-xl bg-white border border-slate-200 space-y-2">
                                            <label class="flex items-center gap-2 text-xs font-bold text-slate-900 cursor-pointer">
                                                <input type="checkbox" name="notify_contract_expiry" value="1" {{ $notifyExpiry ? 'checked' : '' }} class="rounded text-slate-900">
                                                <span>5. تنبيه تجديد عقد الإيجار (إيجار)</span>
                                            </label>
                                            <div class="flex items-center gap-2 pt-1">
                                                <span class="text-[11px] text-slate-500">قبل انتهاء العقد بـ:</span>
                                                <input type="number" name="contract_expiry_days" value="{{ $contractExpiryDays }}" min="5" max="180"
                                                       class="w-20 rounded-lg border px-2.5 py-1 text-xs font-mono font-bold text-center">
                                                <span class="text-[11px] text-slate-600 font-bold">يوماً</span>
                                            </div>
                                        </div>

                                        <div class="p-4 rounded-xl bg-white border border-slate-200 space-y-2">
                                            <label class="block text-xs font-bold text-slate-900">6. وقت الإرسال اليومي المفضل 🕘</label>
                                            <div class="flex items-center gap-2 pt-1">
                                                <input type="time" name="preferred_send_time" value="{{ $preferredSendTime }}"
                                                       class="w-full rounded-lg border px-3 py-1 text-xs font-mono font-bold">
                                            </div>
                                            <label class="flex items-center gap-1.5 text-[11px] text-emerald-700 font-semibold cursor-pointer">
                                                <input type="checkbox" name="skip_fully_paid" value="1" {{ $skipFullyPaid ? 'checked' : '' }}>
                                                <span>استثناء الدفعات المسددة تلقائياً</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- القسم الثالث: قوالب الرسائل المخصصة + أزرار إدراج المتغيرات -->
                                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <div>
                                            <h5 class="font-bold text-sm text-slate-900">📝 ثالثاً: تخصيص صيغة رسائل التنبيهات الآلية</h5>
                                            <p class="text-[11px] text-slate-500">اضغط على أي متغير بالأسفل لإدراجه تلقائياً داخل نص الرسالة</p>
                                        </div>
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach(['{اسم_المستأجر}', '{اسم_المالك}', '{اسم_العقار}', '{رقم_الوحدة}', '{رقم_العقد}', '{المبلغ_المتبقي}', '{تاريخ_الاستحقاق}', '{الأيام_المتبقية}', '{أيام_التأخير}', '{اسم_المكتب}'] as $tagVar)
                                                <button type="button" onclick="insertTagIntoReminderTpl('{{ $tagVar }}')"
                                                        class="px-2.5 py-1 rounded-lg bg-white hover:bg-slate-900 text-slate-700 hover:text-white border border-slate-300 text-[11px] font-mono font-bold transition">
                                                    + {{ $tagVar }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-800 mb-1">1. قالب تذكير استحقاق الدفعة / العقد (للمستأجر والمكتب)</label>
                                            <textarea name="reminder_template" id="main_reminder_template_box" rows="4"
                                                      onfocus="window.activeReminderTextarea = this"
                                                      class="w-full rounded-xl border border-slate-300 p-3 text-xs bg-white leading-relaxed">{{ $office->reminder_template ?: 'عزيزي {اسم_المستأجر}، نود تذكيركم بقرب استحقاق دفعة الإيجار للعقد رقم {رقم_العقد} للعقار ({اسم_العقار} - {رقم_الوحدة}) بمبلغ {المبلغ_المتبقي} ر.س بتاريخ {تاريخ_الاستحقاق} (متبقي {الأيام_المتبقية} يوم). مع تحيات {اسم_المكتب}.' }}</textarea>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-rose-800 mb-1">2. قالب مطالبة المتأخرات (عند تجاوز تاريخ الاستحقاق)</label>
                                            <textarea name="overdue_template" rows="4"
                                                      onfocus="window.activeReminderTextarea = this"
                                                      class="w-full rounded-xl border border-rose-200 p-3 text-xs bg-white leading-relaxed">{{ $overdueTemplate }}</textarea>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-amber-900 mb-1">3. قالب إشعار المالك (عند تفعيل خيار إرسال للمالك)</label>
                                            <textarea name="owner_template" rows="4"
                                                      onfocus="window.activeReminderTextarea = this"
                                                      class="w-full rounded-xl border border-amber-200 p-3 text-xs bg-white leading-relaxed">{{ $ownerTemplate }}</textarea>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="flex justify-end pt-2 border-t border-slate-100">
                                <button type="submit" class="px-8 py-3 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-lg">
                                    💾 حفظ إعدادات الواتساب والتنبيهات الآلية والمستلمين
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- النموذج الثاني: إعدادات اعتماد سندات الصرف عبر الواتساب + كاشف الشذوذ المحاسبي + نصوص رسائل الـ PDF -->
                    <form action="{{ route('office.smart_settings.save') }}" method="POST" class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm space-y-6">
                        @csrf
                        <div class="border-b border-slate-100 pb-3">
                            <h4 class="font-bold text-base text-slate-900">🛡️ رابعاً: إعدادات اعتماد سندات الصرف عبر الواتساب وكاشف الشذوذ المحاسبي</h4>
                            <p class="text-xs text-slate-500">تحكم بسقف اعتماد السندات بالكلمات المفتاحية عبر الواتساب، وتنبيهات العمليات الليلية أو المبالغ الكبيرة</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                                <label class="flex items-center gap-2 text-xs font-bold text-slate-900 cursor-pointer">
                                    <input type="checkbox" name="enable_wa_voucher_approval" value="1" {{ !empty($office->enable_wa_voucher_approval) ? 'checked' : '' }} class="rounded text-slate-900">
                                    <span>تفعيل اشتراط اعتماد مدير المكتب لسندات الصرف عبر الواتساب</span>
                                </label>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">المبلغ الذي يتطلب اعتماد واتساب (ر.س)</label>
                                    <input type="number" step="0.01" name="wa_approval_threshold" value="{{ $office->wa_approval_threshold ?? 10000 }}" class="w-full rounded-xl border px-3 py-2 text-xs bg-white font-mono font-bold">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">كلمات الاعتماد عبر الواتساب (مفصولة بفاصلة)</label>
                                    <input type="text" name="wa_approval_keywords" value="{{ $office->wa_approval_keywords ?: 'اعتمد,تم,اوك,موافق,نعم' }}" class="w-full rounded-xl border px-3 py-2 text-xs bg-white font-bold">
                                </div>
                            </div>

                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                                <label class="flex items-center gap-2 text-xs font-bold text-slate-900 cursor-pointer">
                                    <input type="checkbox" name="enable_anomaly_detector" value="1" {{ ($office->enable_anomaly_detector ?? 1) ? 'checked' : '' }} class="rounded text-slate-900">
                                    <span>تفعيل رادار كاشف الشذوذ المحاسبي والعمليات الحساسة</span>
                                </label>
                                <div class="grid grid-cols-3 gap-2">
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-700 mb-1">سقف المبلغ (ر.س)</label>
                                        <input type="number" step="0.01" name="anomaly_max_amount" value="{{ $office->anomaly_max_amount ?? 20000 }}" class="w-full rounded-xl border px-2.5 py-1.5 text-xs bg-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-700 mb-1">بداية الليل (ساعة)</label>
                                        <input type="number" min="0" max="23" name="anomaly_night_start" value="{{ $office->anomaly_night_start ?? 23 }}" class="w-full rounded-xl border px-2.5 py-1.5 text-xs bg-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-700 mb-1">نهاية الليل (ساعة)</label>
                                        <input type="number" min="0" max="23" name="anomaly_night_end" value="{{ $office->anomaly_night_end ?? 5 }}" class="w-full rounded-xl border px-2.5 py-1.5 text-xs bg-white font-mono">
                                    </div>
                                </div>
                                <label class="flex items-center gap-2 text-xs font-semibold text-rose-700 cursor-pointer pt-1">
                                    <input type="checkbox" name="anomaly_alert_on_delete" value="1" {{ ($office->anomaly_alert_on_delete ?? 1) ? 'checked' : '' }} class="rounded text-rose-600">
                                    <span>التنبيه عند قيام أي موظف بحذف سند أو عقد أو سجل</span>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">نص مرافق لملف PDF السندات (اتركه فارغاً لإرسال الملف فقط)</label>
                                <input type="text" name="wa_voucher_caption" value="{{ $office->wa_voucher_caption ?? '' }}" placeholder="اختياري..." class="w-full rounded-xl border px-3 py-2 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">نص مرافق لملف PDF الفواتير الضريبية</label>
                                <input type="text" name="wa_invoice_caption" value="{{ $office->wa_invoice_caption ?? '' }}" placeholder="اختياري..." class="w-full rounded-xl border px-3 py-2 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">نص مرافق لملف PDF الإنذارات والمطالبات</label>
                                <input type="text" name="wa_legal_caption" value="{{ $office->wa_legal_caption ?? '' }}" placeholder="اختياري..." class="w-full rounded-xl border px-3 py-2 text-xs">
                            </div>
                        </div>

                        <div class="flex justify-end pt-2 border-t border-slate-100">
                            <button type="submit" class="px-8 py-3 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-lg">
                                💾 حفظ إعدادات الاعتماد وكاشف الشذوذ
                            </button>
                        </div>
                    </form>

                    <script>
                        window.activeReminderTextarea = document.getElementById('main_reminder_template_box');

                        window.insertTagIntoReminderTpl = function(tag) {
                            const box = window.activeReminderTextarea || document.getElementById('main_reminder_template_box');
                            if (!box) return;
                            const start = box.selectionStart || box.value.length;
                            const end = box.selectionEnd || box.value.length;
                            box.value = box.value.substring(0, start) + tag + box.value.substring(end);
                            box.focus();
                        };

                        window.addCustomReminderRecipientRow = function() {
                            const container = document.getElementById('customReminderRecipientsContainer');
                            if (!container) return;
                            const row = document.createElement('div');
                            row.className = 'grid grid-cols-12 gap-2 items-center custom-recipient-row';
                            row.innerHTML = `
                                <div class="col-span-5">
                                    <input type="text" name="custom_recipient_names[]" placeholder="اسم المستلم أو صفته (مثال: شريك / مشرف تحصيل)"
                                           class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-slate-50 font-semibold">
                                </div>
                                <div class="col-span-6">
                                    <input type="tel" name="custom_recipient_phones[]" maxlength="10" dir="ltr"
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                                           placeholder="05XXXXXXXX"
                                           class="w-full text-left font-mono rounded-xl border border-slate-300 px-3 py-2 text-xs bg-slate-50 font-bold">
                                </div>
                                <div class="col-span-1 text-center">
                                    <button type="button" onclick="this.closest('.custom-recipient-row').remove()"
                                            class="px-2.5 py-2 rounded-xl bg-rose-50 text-rose-600 text-xs font-bold">🗑️</button>
                                </div>
                            `;
                            container.appendChild(row);
                        };

                        document.addEventListener('DOMContentLoaded', function() {
                            const masterSec = document.getElementById('masterTabSectionActive');
                            if (masterSec && masterSec.parentElement) {
                                let next = masterSec.nextElementSibling;
                                while (next) {
                                    const toRemove = next;
                                    next = next.nextElementSibling;
                                    toRemove.remove();
                                }
                            }
                        });
                    </script>
                </div>
            <?php } ?>

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

                <!-- جدول عقود الإيجار وكشف التنبيهات المستحقة في لوحة التحكم -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h3 class="font-bold text-slate-900">أحدث عقود الإيجار وكشف التنبيهات المستحقة (للمكتب والمالك والمستأجر)</h3>
                            <p class="text-xs text-slate-500 mt-0.5">يعرض تفاصيل العقود، جوال المستأجر، تاريخ الاستحقاق/الانتهاء، الأيام المتبقية، وحالة التنبيه الآلي</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" onclick="exportTableToCSV('officeOverviewContractsTable', 'كشف_العقود_والتنبيهات.xlsx')"
                                    class="px-3.5 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">
                                📊 تصدير Excel
                            </button>
                            <button type="button" onclick="exportElementToPDF('officeOverviewContractsWrap', 'كشف_العقود_والتنبيهات.pdf')"
                                    class="px-3.5 py-2 rounded-xl bg-slate-800 text-white text-xs font-bold">
                                📥 تصدير PDF
                            </button>
                            <a href="{{ route('office.dashboard', ['tab' => 'contracts']) }}" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold">+ إبرام عقد جديد</a>
                        </div>
                    </div>
                    <div id="officeOverviewContractsWrap" class="overflow-x-auto">
                        <table class="w-full text-sm text-right" id="officeOverviewContractsTable">
                            <thead class="bg-slate-50 text-slate-600 text-xs border-b">
                                <tr>
                                    <th class="py-3.5 px-4">رقم العقد</th>
                                    <th class="py-3.5 px-4">العقار / الوحدة</th>
                                    <th class="py-3.5 px-4">المالك</th>
                                    <th class="py-3.5 px-4">المستأجر</th>
                                    <th class="py-3.5 px-4">جوال المستأجر</th>
                                    <th class="py-3.5 px-4">قيمة الإيجار</th>
                                    <th class="py-3.5 px-4">تاريخ الاستحقاق / الانتهاء</th>
                                    <th class="py-3.5 px-4">الأيام المتبقية</th>
                                    <th class="py-3.5 px-4">حالة التنبيه</th>
                                    <th class="py-3.5 px-4">حالة منصة إيجار</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($contracts as $c)
                                    @php
                                        $remDays = (int) now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($c->end_date)->startOfDay(), false);
                                        $limitDays = (int) ($office->reminder_days_before ?? 15);
                                    @endphp
                                    <tr class="hover:bg-slate-50">
                                        <td class="py-3.5 px-4 font-mono font-bold text-xs">{{ $c->contract_number }}</td>
                                        <td class="py-3.5 px-4 font-bold text-xs">{{ $c->property_title }} {{ $c->unit_number ? '(وحدة ' . $c->unit_number . ')' : '' }}</td>
                                        <td class="py-3.5 px-4 text-xs">{{ $c->owner_name }}</td>
                                        <td class="py-3.5 px-4 font-bold text-emerald-700 text-xs">{{ $c->tenant_name }}</td>
                                        <td class="py-3.5 px-4 font-mono text-xs" dir="ltr">{{ $c->tenant_phone }}</td>
                                        <td class="py-3.5 px-4 font-bold text-emerald-700 text-xs font-mono">{{ number_format($c->total_rent_amount, 2) }} ر.س</td>
                                        <td class="py-3.5 px-4 font-mono text-xs">{{ $c->end_date }}</td>
                                        <td class="py-3.5 px-4 font-mono text-xs font-bold {{ $remDays <= $limitDays ? 'text-rose-600' : 'text-slate-700' }}">{{ $remDays }} يوم</td>
                                        <td class="py-3.5 px-4">
                                            @if($remDays < 0)
                                                <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-800 text-xs font-bold">منتهي / متأخر</span>
                                            @elseif($remDays <= $limitDays)
                                                <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 text-xs font-bold">مستحق للتنبيه 🔔</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 text-xs font-bold">ساري</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4">
                                            @if($c->ejar_status == 'active')
                                                <span class="px-2.5 py-1 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700">موثق ({{ $c->ejar_contract_number }})</span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-xl text-xs font-bold bg-amber-50 text-amber-700">بانتظار التوثيق</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="10" class="py-8 text-center text-slate-400">لا توجد عقود مضافة بعد. ابدأ بإضافة مالك ثم عقار ثم عقد إيجار.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- 2. تبويب الملاك (Owners) -->
            @if($tab == 'owners')
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-visible">
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
                                    @php $ownerVouchersCount = $vouchers->where('owner_id', $owner->id)->count(); @endphp
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-4 px-5">
                                            <button type="button" onclick="openModal('ownerStatementModal_{{ $owner->id }}')" class="text-right group focus:outline-none">
                                                <div class="font-bold text-emerald-700 group-hover:text-emerald-900 underline flex items-center gap-1.5">
                                                    <span>👑 {{ $owner->name }}</span>
                                                    <span class="text-[10px] px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">كشف الحساب ({{ $ownerVouchersCount }})</span>
                                                </div>
                                                <div class="text-xs text-slate-400 font-mono mt-0.5">هوية: {{ $owner->national_id }} | اضغط لعرض وتحميل كشف الحساب</div>
                                            </button>
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

                    <!-- نافذة كشف حساب المالك عند الضغط على اسمه -->
                    @php
                        $owVouchers = $vouchers->where('owner_id', $owner->id);
                        $owRec = $owVouchers->where('voucher_type', 'receipt')->sum('amount');
                        $owPay = $owVouchers->where('voucher_type', 'payment')->sum('amount');
                        $owComm = ($owRec * ($owner->office_management_fee_rate ?? 5)) / 100;
                        $owNet = $owRec - $owComm - $owPay;
                    @endphp
                    <div id="ownerStatementModal_{{ $owner->id }}" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-4xl w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] flex flex-col">
                            <div class="flex flex-wrap items-center justify-between gap-2 pb-4 border-b border-slate-100">
                                <div>
                                    <h3 class="font-bold text-base text-slate-900">👑 كشف حساب المالك: <span class="text-emerald-700">{{ $owner->name }}</span></h3>
                                    <p class="text-xs text-slate-500 font-mono mt-0.5">هوية: {{ $owner->national_id }} | جوال: {{ $owner->phone }} | آيبان: {{ $owner->iban ?: '-' }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="exportElementToPDF('ownerStmtPrintArea_{{ $owner->id }}', 'كشف_حساب_المالك_{{ $owner->name }}.pdf')"
                                            class="px-3.5 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold">
                                        📥 تحميل الكشف PDF
                                    </button>
                                    <button type="button" onclick="exportTableToCSV('ownerStmtTable_{{ $owner->id }}', 'كشف_حساب_المالك_{{ $owner->name }}.xlsx')"
                                            class="px-3.5 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">
                                        📊 تصدير الكشف Excel
                                    </button>
                                    <button type="button" onclick="closeModal('ownerStatementModal_{{ $owner->id }}')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                                </div>
                            </div>

                            <div id="ownerStmtPrintArea_{{ $owner->id }}" class="overflow-y-auto flex-1 space-y-4 p-3 bg-white">
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-center">
                                    <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200">
                                        <div class="text-[11px] text-emerald-800 font-bold">إجمالي الإيجارات المحصلة (+)</div>
                                        <div class="text-base font-extrabold text-emerald-700 font-mono mt-1">{{ number_format($owRec, 2) }} ر.س</div>
                                    </div>
                                    <div class="p-3 rounded-xl bg-amber-50 border border-amber-200">
                                        <div class="text-[11px] text-amber-900 font-bold">عمولة المكتب ({{ $owner->office_management_fee_rate }}%)</div>
                                        <div class="text-base font-extrabold text-amber-800 font-mono mt-1">{{ number_format($owComm, 2) }} ر.س</div>
                                    </div>
                                    <div class="p-3 rounded-xl bg-rose-50 border border-rose-200">
                                        <div class="text-[11px] text-rose-800 font-bold">سندات الصرف والتحويلات (-)</div>
                                        <div class="text-base font-extrabold text-rose-700 font-mono mt-1">{{ number_format($owPay, 2) }} ر.س</div>
                                    </div>
                                    <div class="p-3 rounded-xl bg-slate-900 text-white">
                                        <div class="text-[11px] text-emerald-300 font-bold">الصافي المستحق للمالك</div>
                                        <div class="text-base font-extrabold text-white font-mono mt-1">{{ number_format($owNet, 2) }} ر.س</div>
                                    </div>
                                </div>

                                <table class="w-full text-xs text-right border rounded-xl overflow-hidden" id="ownerStmtTable_{{ $owner->id }}">
                                    <thead class="bg-slate-100 text-slate-700 border-b">
                                        <tr>
                                            <th class="py-3 px-3">رقم السند</th>
                                            <th class="py-3 px-3">التاريخ</th>
                                            <th class="py-3 px-3">النوع</th>
                                            <th class="py-3 px-3">المستأجر المرتبط</th>
                                            <th class="py-3 px-3">طريقة الدفع</th>
                                            <th class="py-3 px-3">البيان</th>
                                            <th class="py-3 px-3">المبلغ</th>
                                            <th class="py-3 px-3 text-center">تحميل السند</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($owVouchers as $ov)
                                            @php $ovPmAr = \App\Http\Controllers\CompanyErpController::translatePaymentMethod($ov->payment_method); @endphp
                                            <tr class="hover:bg-slate-50">
                                                <td class="py-2.5 px-3 font-mono font-bold">{{ $ov->voucher_number }}</td>
                                                <td class="py-2.5 px-3 font-mono">{{ $ov->voucher_date }}</td>
                                                <td class="py-2.5 px-3">
                                                    @if($ov->voucher_type == 'receipt')
                                                        <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold">قبض إيجار</span>
                                                    @else
                                                        <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-bold">صرف / تحويل</span>
                                                    @endif
                                                </td>
                                                <td class="py-2.5 px-3 font-bold">{{ $ov->tenant_name ?: '-' }}</td>
                                                <td class="py-2.5 px-3">{{ $ovPmAr }}</td>
                                                <td class="py-2.5 px-3">{{ $ov->notes }}</td>
                                                <td class="py-2.5 px-3 font-mono font-bold {{ $ov->voucher_type == 'receipt' ? 'text-emerald-700' : 'text-rose-600' }}">{{ number_format($ov->amount, 2) }} ر.س</td>
                                                <td class="py-2.5 px-3 text-center">
                                                    <div class="flex items-center justify-center gap-1">
                                                        <button type="button"
                                                                onclick="directDownloadSingleVoucherPDF({
                                                                    number: '{{ $ov->voucher_number }}',
                                                                    type: '{{ $ov->voucher_type }}',
                                                                    amount: '{{ number_format($ov->amount, 2) }}',
                                                                    date: '{{ $ov->voucher_date }}',
                                                                    party: '{{ addslashes($ov->tenant_name ?: $owner->name) }}',
                                                                    payment_method: '{{ addslashes($ovPmAr) }}',
                                                                    notes: '{{ addslashes($ov->notes) }}',
                                                                    issuer: '{{ addslashes($office->name) }}'
                                                                })"
                                                                class="px-2 py-1 rounded bg-slate-900 text-white text-[10px] font-bold">PDF</button>
                                                        <button type="button"
                                                                onclick="directDownloadSingleVoucherExcel({
                                                                    number: '{{ $ov->voucher_number }}',
                                                                    type: '{{ $ov->voucher_type == 'receipt' ? 'سند قبض' : 'سند صرف' }}',
                                                                    amount: '{{ number_format($ov->amount, 2) }}',
                                                                    date: '{{ $ov->voucher_date }}',
                                                                    party: '{{ addslashes($ov->tenant_name ?: $owner->name) }}',
                                                                    payment_method: '{{ addslashes($ovPmAr) }}',
                                                                    notes: '{{ addslashes($ov->notes) }}',
                                                                    issuer: '{{ addslashes($office->name) }}'
                                                                })"
                                                                class="px-2 py-1 rounded bg-emerald-600 text-white text-[10px] font-bold">Excel</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="8" class="py-8 text-center text-slate-400">لا توجد سندات مسجلة لهذا المالك حتى الآن.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
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

           <!-- 3. تبويب العقارات والصكوك والوحدات (مع فلتر الشواغر وتصدير PDF عام وتفصيلي ZIP) -->
            @if($tab == 'properties')
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="font-bold text-base text-slate-900">سجل العقارات والصكوك والوحدات التابعة لها</h3>
                            <p class="text-xs text-slate-500 mt-0.5">يمكنك فلترة العقارات التي بها وحدات شاغرة فقط وتصديرها كجدول PDF عام أو ملف مضغوط (ZIP) يضم ملف PDF مستقل لكل عمارة</p>
                        </div>

                        <div class="flex items-center gap-2 flex-1 max-w-sm">
                            <input type="text" id="officePropertySearch" oninput="filterOfficePropertiesAdvanced()"
                                   placeholder="🔍 ابحث بمسمى العقار، الصك، المالك، أو الحي..."
                                   class="w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-xs focus:outline-none focus:border-emerald-600">
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <!-- زر فلترة الوحدات الشاغرة فقط -->
                            <button type="button" id="btnToggleVacantFilter" onclick="toggleVacantPropertiesOnly()"
                                    class="px-4 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition flex items-center gap-1.5 border border-slate-300">
                                <span id="vacantFilterIcon">🏢</span>
                                <span id="vacantFilterText">عرض العقارات ذات الوحدات الشاغرة فقط</span>
                            </button>

                            <!-- زر خيارات تصدير PDF (عام / تفصيلي ZIP) -->
                            <button type="button" onclick="openModal('propertiesPdfExportChoiceModal')"
                                    class="px-4 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-700 text-white text-xs font-bold transition flex items-center gap-1.5">
                                <span>📥 تصدير PDF (عام / تفصيلي ZIP)</span>
                            </button>

                            <!-- زر تصدير إكسل -->
                            <button type="button" onclick="exportTableToCSV('officePropertiesMainTable', 'سجل_العقارات_والوحدات.xlsx')"
                                    class="px-3.5 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition">
                                📊 Excel
                            </button>

                            <button type="button" onclick="openModal('addPropertyModal')"
                                    class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs shadow-lg flex items-center gap-2 transition">
                                <span>➕ إضافة عقار وصك</span>
                            </button>
                        </div>
                    </div>

                    <!-- نافذة اختيار نوع تصدير PDF للعقارات (PDF عام أو ZIP تفصيلي لكل عمارة) -->
                    <div id="propertiesPdfExportChoiceModal" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4">
                            <div class="flex items-center justify-between border-b pb-3">
                                <h3 class="font-bold text-base text-slate-900">📥 اختر نوع تصدير تقرير العقارات والوحدات المتاحة</h3>
                                <button type="button" onclick="closeModal('propertiesPdfExportChoiceModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                            </div>
                            <p class="text-xs text-slate-500">ملاحظة: إذا قمت بتفعيل زر (عرض العقارات ذات الوحدات الشاغرة فقط) فسيتم تصدير العقارات والوحدات المتاحة فقط.</p>

                            <div id="zipExportProgressBox" class="hidden p-4 rounded-2xl bg-amber-50 border border-amber-200 text-center space-y-2">
                                <div class="text-xs font-bold text-amber-900" id="zipExportProgressText">⏳ جاري توليد ملفات الـ PDF المنفصلة لكل عمارة وضغطها في ملف ZIP...</div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <button type="button" onclick="exportGeneralPropertiesPDF()"
                                        class="p-4 rounded-2xl bg-slate-50 hover:bg-slate-900 text-slate-900 hover:text-white border border-slate-200 text-right transition space-y-1">
                                    <div class="text-xl">📄</div>
                                    <div class="font-bold text-xs">1. تحميل PDF عام (جدول موحد)</div>
                                    <div class="text-[11px] opacity-75">يصدّر جدول العقارات المعروضة حالياً في ملف PDF واحد شامل</div>
                                </button>

                                <button type="button" onclick="exportDetailedBuildingsZipPDF()"
                                        class="p-4 rounded-2xl bg-emerald-50 hover:bg-emerald-600 text-emerald-950 hover:text-white border border-emerald-200 text-right transition space-y-1">
                                    <div class="text-xl">🗂️</div>
                                    <div class="font-bold text-xs">2. تحميل PDF تفصيلي (ملف مضغوط ZIP)</div>
                                    <div class="text-[11px] opacity-75">يولّد ملف PDF مستقل لكل عمارة مع جدول وحداتها الشاغرة ويجمعها في ملف ZIP واحد</div>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto" id="officePropertiesPrintableWrap">
                        <table class="w-full text-sm text-right" id="officePropertiesMainTable">
                            <thead class="bg-slate-50 text-slate-600 text-xs border-b border-slate-200/80">
                                <tr>
                                    <th class="py-4 px-4">#</th>
                                    <th class="py-4 px-4">مسمى العقار والنوع</th>
                                    <th class="py-4 px-4">المالك</th>
                                    <th class="py-4 px-4">رقم الصك والمساحة</th>
                                    <th class="py-4 px-4">العنوان الوطني</th>
                                    <th class="py-4 px-4">المواصفات والمرفقات</th>
                                    <th class="py-4 px-4 text-center">الوحدات الداخلية (والشاغر)</th>
                                    <th class="py-4 px-4 text-center">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="officePropertiesTableBody" class="divide-y divide-slate-100">
                                @forelse($properties as $idx => $prop)
                                    @php
                                        $propUnits = $units->where('property_id', $prop->id);
                                        $vacantUnitsList = $propUnits->where('status', 'vacant')->values();
                                        $vacantCount = $vacantUnitsList->count();
                                        $savedAttrs = json_decode($prop->custom_attributes ?? '{}', true) ?: [];
                                    @endphp
                                    <tr class="hover:bg-slate-50/90 transition office-prop-row"
                                        data-prop-id="{{ $prop->id }}"
                                        data-prop-title="{{ $prop->title }}"
                                        data-prop-type="{{ $prop->type_name }}"
                                        data-prop-owner="{{ $prop->owner_name }}"
                                        data-prop-deed="{{ $prop->deed_number }}"
                                        data-prop-area="{{ number_format($prop->total_area, 0) }}"
                                        data-prop-address="{{ $prop->city }} - {{ $prop->district }} {{ $prop->street }}"
                                        data-vacant-count="{{ $vacantCount }}"
                                        data-vacant-units='@json($vacantUnitsList)'>
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

            <!-- 4. تبويب عقود الإيجار (الرئيسي) + زر صفحة المستأجرين في الأعلى -->
            @if($tab == 'contracts')
                <!-- أولاً (الواجهة الرئيسية للتبويب): سجل عقود الإيجار -->
                <div id="mainContractsSectionView" class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="font-bold text-base text-slate-900">📝 سجل عقود الإيجار والربط مع منصة إيجار</h3>
                            <p class="text-xs text-slate-500 mt-0.5">جميع العقود السارية والموثقة مع احتساب عمولة المكتب وحصة الشركة</p>
                        </div>

                        <div class="flex items-center gap-3 flex-1 max-w-md">
                            <input type="text" id="contractSearchInput" oninput="filterTableRows('contractSearchInput', 'contractsTableBody')"
                                   placeholder="🔍 ابحث برقم العقد أو اسم المستأجر أو العقار..."
                                   class="w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-xs focus:outline-none focus:border-emerald-600">
                        </div>

                        <div class="flex flex-wrap items-center gap-2.5">
                            <!-- زر فتح صفحة جدول المستأجرين وباقي بياناتهم -->
                            <button type="button" onclick="toggleContractsTenantsPage('tenants')"
                                    class="px-5 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-900 font-bold text-xs border border-slate-300 transition flex items-center gap-2">
                                <span>👥 المستأجرون ({{ $tenants->count() }})</span>
                            </button>

                            <button type="button" onclick="openModal('addTenantModal')"
                                    class="px-4 py-2.5 rounded-2xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition">
                                + إضافة مستأجر
                            </button>

                            <button type="button" onclick="openModal('addContractModal')"
                                    class="px-5 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-700 text-white font-bold text-xs shadow-lg transition">
                                + إبرام عقد إيجار جديد
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-right">
                            <thead class="bg-slate-50 text-slate-600 text-xs border-b">
                                <tr>
                                    <th class="py-3.5 px-4">رقم العقد والوحدة</th>
                                    <th class="py-3.5 px-4">المالك والمستأجر</th>
                                    <th class="py-3.5 px-4">قيمة العقد والعمولات</th>
                                    <th class="py-3.5 px-4 text-center">التوثيق في إيجار والإجراءات</th>
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

                <!-- ثانياً (صفحة المستأجرون التي تفتح عند الضغط على زر المستأجرون بالأعلى) -->
                <div id="subTenantsSectionView" class="hidden bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="font-bold text-base text-slate-900">👥 سجل وبيانات المستأجرين وكشوف حساباتهم</h3>
                            <p class="text-xs text-slate-500 mt-0.5">اضغط على اسم المستأجر لعرض وتحميل كشف حسابه المالي (PDF / Excel)</p>
                        </div>

                        <div class="flex items-center gap-3 flex-1 max-w-md">
                            <input type="text" id="tenantSearchInput" oninput="filterTableRows('tenantSearchInput', 'tenantsTableBody')"
                                   placeholder="🔍 بحث فوري باسم المستأجر أو رقم الجوال أو الهوية..."
                                   class="w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-xs focus:outline-none focus:border-emerald-600">
                        </div>

                        <div class="flex items-center gap-2.5">
                            <button type="button" onclick="toggleContractsTenantsPage('contracts')"
                                    class="px-4 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs border border-slate-300 transition">
                                ↩ العودة لجدول عقود الإيجار
                            </button>
                            <button type="button" onclick="openModal('addTenantModal')"
                                    class="px-5 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-lg transition">
                                + إضافة مستأجر جديد
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-right">
                            <thead class="bg-slate-50 text-slate-600 text-xs border-b">
                                <tr>
                                    <th class="py-3.5 px-5">اسم المستأجر (اضغط لكشف الحساب)</th>
                                    <th class="py-3.5 px-5">رقم الحساب في الشجرة</th>
                                    <th class="py-3.5 px-5">رقم الهوية / الإقامة</th>
                                    <th class="py-3.5 px-5">رقم الجوال</th>
                                    <th class="py-3.5 px-5">إجمالي المسدد</th>
                                    <th class="py-3.5 px-5 text-center">إجراء</th>
                                </tr>
                            </thead>
                            <tbody id="tenantsTableBody" class="divide-y divide-slate-100">
                                @forelse($tenants as $tn)
                                    @php
                                        $tnVouchers = $vouchers->where('tenant_id', $tn->id);
                                        $tnReceipts = $tnVouchers->where('voucher_type', 'receipt')->sum('amount');
                                    @endphp
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-4 px-5">
                                            <button type="button" onclick="openModal('tenantStatementModal_{{ $tn->id }}')"
                                                    class="font-bold text-emerald-700 hover:text-emerald-900 underline flex items-center gap-1.5 text-right">
                                                <span>👤 {{ $tn->name }}</span>
                                                <span class="text-[10px] px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">كشف حساب ({{ $tnVouchers->count() }})</span>
                                            </button>
                                        </td>
                                        <td class="py-4 px-5 font-mono text-xs font-bold text-slate-600">{{ $tn->account_code ?: '-' }}</td>
                                        <td class="py-4 px-5 font-mono text-xs text-slate-700">{{ $tn->id_number }}</td>
                                        <td class="py-4 px-5 font-mono text-xs font-bold text-emerald-700" dir="ltr">{{ $tn->phone }}</td>
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
                                    <tr><td colspan="6" class="py-8 text-center text-slate-400">لا يوجد مستأجرون مسجلون بعد.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <script>
                    function toggleContractsTenantsPage(view) {
                        document.getElementById('mainContractsSectionView').classList.toggle('hidden', view === 'tenants');
                        document.getElementById('subTenantsSectionView').classList.toggle('hidden', view !== 'tenants');
                    }
                </script>

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
                            <div class="flex flex-wrap items-center justify-between gap-2 pb-4 border-b border-slate-100 mb-4">
                                <div>
                                    <h3 class="font-bold text-base text-slate-900">كشف حساب المستأجر: <span class="text-emerald-700">{{ $tn->name }}</span></h3>
                                    <p class="text-xs text-slate-500 font-mono" dir="ltr">{{ $tn->phone }} | ID: {{ $tn->id_number }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="exportElementToPDF('tenantStmtPrintArea_{{ $tn->id }}', 'كشف_حساب_المستأجر_{{ $tn->name }}.pdf')"
                                            class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-700 text-white text-xs font-bold">
                                        📥 تحميل PDF
                                    </button>
                                    <button type="button" onclick="exportTableToCSV('tenantStmtTable_{{ $tn->id }}', 'كشف_حساب_المستأجر_{{ $tn->name }}.xlsx')"
                                            class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold">
                                        📊 تصدير Excel
                                    </button>
                                    <button type="button" onclick="closeModal('tenantStatementModal_{{ $tn->id }}')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                                </div>
                            </div>

                            <div id="tenantStmtPrintArea_{{ $tn->id }}" class="space-y-4 bg-white p-2">
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

                                <table class="w-full text-xs text-right border rounded-2xl overflow-hidden" id="tenantStmtTable_{{ $tn->id }}">
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
                            <button type="button" onclick="exportTableToCSV('officeVouchersMainTable', 'سجل_سندات_المكتب.xlsx')"
                                    class="px-4 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition flex items-center gap-1.5">
                                <span>📊 تصدير السندات Excel</span>
                            </button>

                            <button type="button" onclick="openModal('manageOfficePaymentMethodsModal')"
                                    class="px-4 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition">
                                💳 طرق الدفع ({{ $paymentMethods->count() }})
                            </button>

                            <button type="button" onclick="openModal('batchReceiptsModal')"
                                    class="px-4 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition flex items-center gap-1.5">
                                <span>⚡ سندات قبض مجمّعة</span>
                            </button>

                            <button type="button" onclick="openModal('createOfficeVoucherModal')"
                                    class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs shadow-lg shadow-emerald-600/20 transition flex items-center gap-2">
                                <span>+ إصدار سند قبض / صرف</span>
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-right" id="officeVouchersMainTable">
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
                                <select name="voucher_type" id="ov_voucher_type" onchange="window.instantFillVoucherByTenant()" class="w-full rounded-2xl border border-slate-300 px-3.5 py-2.5 text-sm bg-white font-bold">
                                    <option value="receipt">📥 سند قبض (تحصيل إيجار / دفعة)</option>
                                    <option value="payment">📤 سند صرف (تسلسل SA-{{ date('Y') }}-XXXXXX)</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-emerald-800 mb-1">المستأجر المرتبط (اختره لملء كافة الحقول تلقائياً)</label>
                                    <select name="tenant_id" id="ov_tenant_id" onchange="window.instantFillVoucherByTenant(this.value)" class="w-full rounded-xl border-2 border-emerald-500 px-2.5 py-2 text-xs bg-white font-bold">
                                        <option value="">-- اختر المستأجر --</option>
                                        @foreach($tenants as $tn)
                                            <option value="{{ $tn->id }}">{{ $tn->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">المالك المرتبط (يتحدد تلقائياً)</label>
                                    <select name="owner_id" id="ov_owner_id" onchange="window.instantFillVoucherByOwner(this.value)" class="w-full rounded-xl border border-slate-300 px-2.5 py-2 text-xs bg-white font-bold">
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
                            <!-- مركز التكلفة على مستوى العقار والوحدة -->
                            <div class="grid grid-cols-2 gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-200">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">🏢 مركز التكلفة (العقار)</label>
                                    <select name="property_id" class="w-full rounded-xl border border-slate-300 px-2.5 py-1.5 text-xs bg-white">
                                        <option value="">-- عام لجميع العقارات --</option>
                                        @foreach($properties as $pr)
                                            <option value="{{ $pr->id }}">{{ $pr->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">🚪 مركز التكلفة (الوحدة)</label>
                                    <select name="property_unit_id" class="w-full rounded-xl border border-slate-300 px-2.5 py-1.5 text-xs bg-white">
                                        <option value="">-- عام للعقار --</option>
                                        @foreach($units as $un)
                                            <option value="{{ $un->id }}">{{ $un->property_title }} - {{ $un->unit_number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="text-xs font-semibold text-slate-700">البيان (يدعم التصنيف الذكي التلقائي للمصروفات) *</label>
                                    <span id="autoExpenseClassifierBadge" class="hidden px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-bold">🤖 تم تحديد حساب المصروف تلقائياً</span>
                                </div>
                                <input type="text" name="notes" id="office_voucher_notes_input" oninput="autoClassifyExpenseByNotes(this.value)" required placeholder="مثال: إصلاح مضخة مياه / فاتورة كهرباء / راتب..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm">
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

            
            <!-- تبويب القيود اليومية للمكتب العقاري -->
            @if($tab == 'journal')
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden no-print">
                    <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="font-bold text-lg text-slate-900">سجل القيود اليومية للمكتب</h3>
                            <p class="text-xs text-slate-500 mt-0.5">عرض القيود اليومية المتوازنة مع تفاصيل الإجمالي الشامل وصافي الفرق</p>
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
                                    <th class="py-4 px-4 text-center w-16">رقم القيد</th>
                                    <th class="py-4 px-4">رقم الحساب (المدين)</th>
                                    <th class="py-4 px-4">حساب الدائن</th>
                                    <th class="py-4 px-4">البيان العام والتاريخ</th>
                                    <th class="py-4 px-4 text-center">إجمالي القيد والتوازن (شامل الضريبة)</th>
                                    <th class="py-4 px-4 text-center">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($journalEntries as $je)
                                    @php
                                        $items = $journalItemsByEntry[$je->id] ?? collect();
                                        $debitAccountsList = $items->where('debit', '>', 0)->map(fn($i) => $i->account_code . ' - ' . $i->account_name)->implode(' ، ');
                                        $creditAccountsList = $items->where('credit', '>', 0)->map(fn($i) => $i->account_code . ' - ' . $i->account_name)->implode(' ، ');

                                        $totalDebit = (float) $je->total_debit;
                                        $totalCredit = (float) $je->total_credit;
                                        $totalTax = (float) $je->total_tax;
                                        $entryAmount = max($totalDebit, $totalCredit);
                                        $netDiff = round(abs($totalDebit - $totalCredit), 2);
                                        $isBalanced = $netDiff <= 0.05;

                                        $rawDebitSum = (float) $items->where('debit', '>', 0)->sum('debit');
                                        $rawDebitTax = (float) $items->where('debit', '>', 0)->sum('tax');
                                        if (abs($rawDebitSum - $totalDebit) <= 0.05 && $rawDebitTax > 0) {
                                            $baseDebit = max(0, $totalDebit - $rawDebitTax);
                                            $debitTax = $rawDebitTax;
                                        } else {
                                            $baseDebit = $rawDebitSum;
                                            $debitTax = $rawDebitTax;
                                        }

                                        $rawCreditSum = (float) $items->where('credit', '>', 0)->sum('credit');
                                        $rawCreditTax = (float) $items->where('credit', '>', 0)->sum('tax');
                                        if (abs($rawCreditSum - $totalCredit) <= 0.05 && $rawCreditTax > 0) {
                                            $baseCredit = max(0, $totalCredit - $rawCreditTax);
                                            $creditTax = $rawCreditTax;
                                        } else {
                                            $baseCredit = $rawCreditSum;
                                            $creditTax = $rawCreditTax;
                                        }
                                    @endphp
                                    <tr class="hover:bg-slate-50/90 transition journal-row"
                                        data-search="{{ mb_strtolower($je->entry_number . ' ' . $debitAccountsList . ' ' . $creditAccountsList . ' ' . $je->general_notes) }}">
                                        <td class="py-4 px-4 text-center font-mono font-extrabold text-slate-900">{{ $je->entry_number }}</td>
                                        <td class="py-4 px-4 text-xs font-bold text-emerald-700">{{ $debitAccountsList ?: '-' }}</td>
                                        <td class="py-4 px-4 text-xs font-bold text-amber-700">{{ $creditAccountsList ?: '-' }}</td>
                                        <td class="py-4 px-4">
                                            <div class="text-xs font-bold text-slate-800">{{ $je->general_notes }}</div>
                                            <div class="text-[11px] font-mono text-slate-400 mt-0.5">{{ $je->entry_date }}</div>
                                        </td>
                                        <td class="py-3 px-4 min-w-[340px]">
                                            <div class="p-3 rounded-2xl bg-slate-900 text-white space-y-2 shadow-sm text-right">
                                                <div class="flex items-center justify-between gap-2 border-b border-slate-700 pb-1.5 text-[11px]">
                                                    <span class="font-bold text-emerald-400">📊 إجمالي القيد (شامل الضريبة):</span>
                                                    <span class="px-2 py-0.5 rounded font-bold {{ $isBalanced ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/30 text-rose-200' }}">
                                                        {{ $isBalanced ? '✔ متوازن (الفرق: 0.00 ر.س)' : '⚠️ غير متوازن — الفرق: ' . number_format($netDiff, 2) . ' ر.س' }}
                                                    </span>
                                                </div>
                                                <div class="grid grid-cols-4 gap-1.5 text-[11px] text-center">
                                                    <div class="p-1.5 rounded-lg bg-white/5">
                                                        <span class="text-slate-300 block text-[10px]">المدين + ضريبته</span>
                                                        <b class="font-mono text-emerald-400 block mt-0.5">{{ number_format($totalDebit, 2) }} ر.س</b>
                                                        @if($debitTax > 0)
                                                            <span class="text-[9px] text-slate-400 font-mono block">({{ number_format($baseDebit, 0) }}+{{ number_format($debitTax, 0) }})</span>
                                                        @endif
                                                    </div>
                                                    <div class="p-1.5 rounded-lg bg-white/5">
                                                        <span class="text-slate-300 block text-[10px]">الدائن + ضريبته</span>
                                                        <b class="font-mono text-amber-400 block mt-0.5">{{ number_format($totalCredit, 2) }} ر.س</b>
                                                        @if($creditTax > 0)
                                                            <span class="text-[9px] text-slate-400 font-mono block">({{ number_format($baseCredit, 0) }}+{{ number_format($creditTax, 0) }})</span>
                                                        @endif
                                                    </div>
                                                    <div class="p-1.5 rounded-lg bg-emerald-500/20 border border-emerald-500/30">
                                                        <span class="text-emerald-200 block text-[10px]">إجمالي القيد المعتمد</span>
                                                        <b class="font-mono text-white block mt-0.5">{{ number_format($entryAmount, 2) }} ر.س</b>
                                                        @if($totalTax > 0)
                                                            <span class="text-[9px] text-emerald-300 block">ضريبة: {{ number_format($totalTax, 2) }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="p-1.5 rounded-lg bg-white/5">
                                                        <span class="text-slate-300 block text-[10px]">صافي الفرق</span>
                                                        <b class="font-mono {{ $isBalanced ? 'text-emerald-400' : 'text-rose-400' }} block mt-0.5">{{ number_format($netDiff, 2) }} ر.س</b>
                                                    </div>
                                                </div>
                                            </div>
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

                                                <form action="{{ route('office.journal.delete', $je->id) }}" method="POST" class="inline" onsubmit="return confirm('حذف هذا القيد وعكس أثره من شجرة حسابات المكتب؟');">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-red-50 hover:bg-red-600 text-red-600 hover:text-white text-xs font-bold transition">
                                                        🗑️ حذف
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="py-12 text-center text-slate-400">لا توجد قيود يومية مسجلة بعد. اضغط على "إضافة قيد يومي جديد" للبدء.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- نافذة إضافة قيد يومي متعدد الجداول للمكتب -->
                <div id="addJournalEntryModal" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4 no-print">
                    <div class="bg-white rounded-3xl max-w-4xl w-full p-6 shadow-2xl border border-slate-200 max-h-[92vh] overflow-y-auto">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                            <div>
                                <h3 class="font-bold text-base text-slate-900">➕ إضافة قيد يومي محاسبي للمكتب</h3>
                                <p class="text-xs text-slate-500">أدخل البيان العام بالأعلى ثم أضف العمليات في الجداول بالأسفل</p>
                            </div>
                            <button type="button" onclick="closeModal('addJournalEntryModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                        </div>

                        <form action="{{ route('office.journal.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200">
                                <div class="md:col-span-9">
                                    <label class="block text-xs font-bold text-emerald-950 mb-1">البيان العام للقيد اليومي *</label>
                                    <input type="text" name="general_notes" required placeholder="مثال: قيد إثبات مصروفات صيانة أو تسوية حسابات..."
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

                <!-- نوافذ العرض والطباعة والتعديل لكل قيد يومي بالمكتب -->
                @foreach($journalEntries as $je)
                    @php
                        $items = $journalItemsByEntry[$je->id] ?? collect();
                        $totalDebit = (float) $je->total_debit;
                        $totalCredit = (float) $je->total_credit;
                        $totalTax = (float) $je->total_tax;
                        $entryAmount = max($totalDebit, $totalCredit);
                        $netDiff = round(abs($totalDebit - $totalCredit), 2);
                        $isBalanced = $netDiff <= 0.05;
                    @endphp

                    <div id="viewJournalModal_{{ $je->id }}" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-3xl w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto">
                            <div class="flex items-center justify-between pb-4 border-b mb-4 no-print">
                                <div>
                                    <h3 class="font-bold text-base text-slate-900">📒 تفاصيل القيد اليومي رقم (#{{ $je->entry_number }})</h3>
                                    <p class="text-xs text-slate-500">التاريخ: {{ $je->entry_date }} | البيان العام: {{ $je->general_notes }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="exportJournalPDF('{{ $je->id }}', '{{ $je->entry_number }}')"
                                            class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-700 text-white text-xs font-bold">
                                        📥 تحميل PDF
                                    </button>
                                    <button type="button" onclick="closeModal('viewJournalModal_{{ $je->id }}')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                                </div>
                            </div>

                            <div id="printableJournalArea_{{ $je->id }}" class="p-6 rounded-2xl border-2 border-slate-800 bg-white space-y-5 text-slate-900">
                                <div class="flex items-center justify-between border-b-2 border-slate-800 pb-4">
                                    <div>
                                        <div class="text-base font-extrabold text-slate-900">{{ $office->name }} — سند قيد يومية رقم: #{{ $je->entry_number }}</div>
                                        <div class="text-xs text-slate-600 mt-1">البيان العام: <b>{{ $je->general_notes }}</b></div>
                                    </div>
                                    <div class="text-left text-xs space-y-1">
                                        <div class="font-mono font-bold">التاريخ: {{ $je->entry_date }}</div>
                                        <div class="px-2.5 py-0.5 rounded bg-emerald-50 text-emerald-800 font-bold inline-block">
                                            {{ $isBalanced ? '✔ قيد متوازن' : '⚠️ غير متوازن' }}
                                        </div>
                                    </div>
                                </div>

                                <table class="w-full text-xs text-right border border-slate-300" id="journalDetailTable_{{ $je->id }}">
                                    <thead class="bg-slate-900 text-white">
                                        <tr>
                                            <th class="py-2.5 px-3">#</th>
                                            <th class="py-2.5 px-3">رقم واسم الحساب</th>
                                            <th class="py-2.5 px-3">مدين (Debit)</th>
                                            <th class="py-2.5 px-3">دائن (Credit)</th>
                                            <th class="py-2.5 px-3">الضريبة</th>
                                            <th class="py-2.5 px-3">البيان التفصيلي</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200">
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
                                    <tfoot class="bg-slate-100 font-bold border-t-2 border-slate-300">
                                        <tr>
                                            <td colspan="2" class="py-2.5 px-3">الإجمالي الشامل للقيد</td>
                                            <td class="py-2.5 px-3 font-mono text-emerald-700">{{ number_format($totalDebit, 2) }} ر.س</td>
                                            <td class="py-2.5 px-3 font-mono text-amber-700">{{ number_format($totalCredit, 2) }} ر.س</td>
                                            <td class="py-2.5 px-3 font-mono">{{ number_format($totalTax, 2) }} ر.س</td>
                                            <td class="py-2.5 px-3 font-mono text-emerald-800">صافي الفرق: {{ number_format($netDiff, 2) }} ر.س</td>
                                        </tr>
                                    </tfoot>
                                </table>

                                <div class="grid grid-cols-4 gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-300 text-xs text-center pdf-no-break">
                                    <div>
                                        <span class="text-slate-500 block">إجمالي المدين (شامل الضريبة)</span>
                                        <b class="font-mono text-emerald-700 text-sm">{{ number_format($totalDebit, 2) }} ر.س</b>
                                    </div>
                                    <div>
                                        <span class="text-slate-500 block">إجمالي الدائن (شامل الضريبة)</span>
                                        <b class="font-mono text-amber-700 text-sm">{{ number_format($totalCredit, 2) }} ر.س</b>
                                    </div>
                                    <div>
                                        <span class="text-slate-500 block">إجمالي القيد المعتمد</span>
                                        <b class="font-mono text-slate-900 text-sm">{{ number_format($entryAmount, 2) }} ر.س</b>
                                    </div>
                                    <div>
                                        <span class="text-slate-500 block">صافي الفرق (التوازن)</span>
                                        <b class="font-mono {{ $isBalanced ? 'text-emerald-700' : 'text-rose-600' }} text-sm">{{ number_format($netDiff, 2) }} ر.س ({{ $isBalanced ? 'متوازن ✔' : 'غير متوازن' }})</b>
                                    </div>
                                </div>
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
                                <button type="button" onclick="exportTableToCSV('journalDetailTable_{{ $je->id }}', 'Office_Journal_Entry_{{ $je->entry_number }}.xlsx')"
                                        class="p-4 rounded-2xl bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 font-bold text-xs transition flex flex-col items-center gap-2">
                                    <span class="text-2xl">📊</span>
                                    <span>تصدير ملف Excel</span>
                                </button>
                            </div>
                            <button type="button" onclick="closeModal('printChoiceJournalModal_{{ $je->id }}')" class="w-full py-2 rounded-xl bg-slate-100 text-xs font-semibold">إلغلاق</button>
                        </div>
                    </div>

                    <div id="editJournalModal_{{ $je->id }}" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4 no-print">
                        <div class="bg-white rounded-3xl max-w-4xl w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto">
                            <div class="flex items-center justify-between pb-4 border-b mb-4">
                                <h3 class="font-bold text-base text-slate-900">تعديل القيد اليومي رقم (#{{ $je->entry_number }})</h3>
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
                                <button type="button" onclick="exportElementToPDF('officeTaxInvoicePrintArea', (window.currentOfficeInvoiceNo || 'Office_Tax_Invoice') + '.pdf')" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold">📥 تحميل PDF</button>
                                <button type="button" onclick="exportCurrentTaxInvoiceExcel('oti')" class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">📊 تصدير Excel</button>
                                <button type="button" onclick="closeModal('officeTaxInvoiceModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                            </div>
                        </div>
                        <div id="officeTaxInvoicePrintArea" class="p-6 rounded-3xl border-2 border-slate-900 bg-white space-y-5 text-slate-900">
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

            <!-- 6-ج (2). صفحة كشف الحساب وتصفية حسابات الملاك الشاملة (Owner Settlement & Account Statement) -->
            @if($tab == 'account_statement')
                <div class="space-y-6">
                    <!-- شريط التحكم العلوي والفلاتر الذكية -->
                    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-5 no-print">
                        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4">
                            <div>
                                <h3 class="font-bold text-lg text-slate-900">📑 مركز كشف الحساب وتصفية مستحقات الملاك</h3>
                                <p class="text-xs text-slate-500 mt-0.5">اختر المالك والفترة الزمنية لإصدار برنت تصفية شامل يوضح الإيجارات المستلمة، المتأخرة، عمولة المكتب، والصافي للتحويل</p>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <button type="button" onclick="switchStatementMode('owner_settlement')" id="btn_mode_owner_settlement"
                                        class="px-4 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold transition">
                                    👑 برنت تصفية حساب مالك (شامل)
                                </button>
                                <button type="button" onclick="switchStatementMode('general_ledger')" id="btn_mode_general_ledger"
                                        class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-bold transition">
                                    📋 كشف حركات حساب / مستأجر
                                </button>
                                <button type="button" onclick="downloadOwnerSettlementPDF()"
                                        class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-white text-xs font-bold shadow">
                                    📥 تصدير PDF
                                </button>
                                <button type="button" onclick="exportTableToCSV('standaloneAccountStatementTable', 'كشف_الحساب_والتصفية.xlsx')"
                                        class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow">
                                    📊 تصدير Excel
                                </button>
                            </div>
                        </div>

                        <!-- فلاتر تصفية حساب المالك -->
                        <div id="owner_settlement_filters" class="grid grid-cols-1 md:grid-cols-12 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-200 items-end">
                            <div class="md:col-span-4">
                                <label class="block text-xs font-bold text-slate-800 mb-1">👤 1. اختر المالك (ابحث بالاسم أو الهوية) *</label>
                                <select id="settle_owner_id" onchange="onSettlementOwnerChange()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-bold">
                                    <option value="">-- اختر المالك لإصدار التصفية --</option>
                                    @foreach($owners as $ow)
                                        <option value="{{ $ow->id }}">{{ $ow->name }} ({{ $ow->phone }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-xs font-bold text-slate-800 mb-1">🏢 2. العقار (اختياري)</label>
                                <select id="settle_property_id" onchange="renderOwnerSettlementReport()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-semibold">
                                    <option value="">-- جميع عقارات المالك --</option>
                                    @foreach($properties as $pr)
                                        <option value="{{ $pr->id }}" data-owner="{{ $pr->owner_id }}">{{ $pr->title }} (صك: {{ $pr->deed_number }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-slate-800 mb-1">📅 من تاريخ</label>
                                <input type="date" id="settle_from_date" value="{{ date('Y-01-01') }}" onchange="renderOwnerSettlementReport()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-mono">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-slate-800 mb-1">📅 إلى تاريخ</label>
                                <input type="date" id="settle_to_date" value="{{ date('Y-m-d') }}" onchange="renderOwnerSettlementReport()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-mono">
                            </div>
                            <div class="md:col-span-1">
                                <button type="button" onclick="setSettlementFullYear()" title="عرض كامل السنة" class="w-full py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 text-[11px] font-bold">
                                    سنة كاملة
                                </button>
                            </div>
                        </div>

                        <!-- فلاتر كشف الحساب العام (التقليدي) -->
                        <div id="general_statement_filters" class="hidden grid grid-cols-1 md:grid-cols-4 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-200">
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
                    </div>

                    <!-- ========================================================= -->
                    <!-- الوضع الأول: ورقة برنت تصفية حساب المالك الرسمية (Printable) -->
                    <!-- ========================================================= -->
                    <div id="owner_settlement_view">
                        <!-- رسالة تنبيهية قبل اختيار المالك -->
                        <div id="settlement_empty_prompt" class="bg-white rounded-3xl border border-slate-200 p-12 text-center space-y-3">
                            <div class="text-4xl">👑</div>
                            <h4 class="font-bold text-base text-slate-900">اختر اسم المالك من القائمة بالأعلى لعرض برنت التصفية الشامل</h4>
                            <p class="text-xs text-slate-500 max-w-md mx-auto">سيقوم النظام فوراً بجمع عقارات المالك، عقوده، الإيجارات المستلمة، الإيجارات المتأخرة، وخصم عمولة المكتب والمصروفات لإعطائك الصافي الجاهز للتحويل.</p>
                        </div>

                        <!-- منطقة البرنت الفعلي التي تظهر عند اختيار المالك -->
                        <div id="ownerSettlementPrintableArea" class="hidden bg-white rounded-3xl border-2 border-slate-800 p-8 space-y-6 shadow-sm text-slate-900">
                            <!-- ترويسة التقرير الرسمية -->
                            <div class="flex flex-wrap items-start justify-between gap-4 border-b-2 border-slate-900 pb-5">
                                <div>
                                    <h2 class="text-xl font-extrabold text-slate-900">{{ $office->name }}</h2>
                                    <p class="text-xs text-slate-500 mt-1">
                                        رخصة فال: <span class="font-mono font-bold">{{ $office->fal_license ?: '-' }}</span> |
                                        سجل تجاري: <span class="font-mono font-bold">{{ $office->cr_number ?: '-' }}</span> |
                                        جوال: <span class="font-mono font-bold" dir="ltr">{{ $office->phone }}</span>
                                    </p>
                                </div>
                                <div class="text-center px-6 py-2.5 rounded-2xl bg-slate-900 text-white">
                                    <div class="text-base font-extrabold">بيان تصفية حساب مالك عقار</div>
                                    <div id="rep_period_label" class="text-[11px] font-mono text-emerald-300 mt-0.5">الفترة: الكل</div>
                                </div>
                                <div class="text-left text-xs space-y-1">
                                    <div>تاريخ الإصدار: <b class="font-mono">{{ date('Y-m-d') }}</b></div>
                                    <div>نسبة إدارة الأملاك: <b id="rep_fee_rate" class="font-mono text-amber-700">5%</b></div>
                                </div>
                            </div>

                            <!-- بيانات المالك والآيبان -->
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
                                <div>
                                    <span class="text-slate-400 block">اسم المالك:</span>
                                    <b id="rep_owner_name" class="text-sm text-slate-900">-</b>
                                </div>
                                <div>
                                    <span class="text-slate-400 block">رقم الهوية / الجوال:</span>
                                    <b id="rep_owner_id_phone" class="font-mono text-slate-800">-</b>
                                </div>
                                <div>
                                    <span class="text-slate-400 block">البنك والآيبان المعتمد للتحويل:</span>
                                    <b id="rep_owner_iban" class="font-mono text-emerald-700" dir="ltr">-</b>
                                </div>
                                <div>
                                    <span class="text-slate-400 block">نطاق العقارات بالتقرير:</span>
                                    <b id="rep_property_scope" class="text-slate-900">جميع عقارات المالك</b>
                                </div>
                            </div>

                            <!-- 1. بطاقات الملخص المالي الصافي (6 مؤشرات حاسمة) -->
                            <div class="grid grid-cols-2 md:grid-cols-6 gap-3 text-center">
                                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                                    <div class="text-[11px] font-bold text-slate-500">إجمالي عقود الإيجار</div>
                                    <div id="rep_total_contract_rent" class="text-base font-extrabold font-mono text-slate-900 mt-1">0.00</div>
                                    <span class="text-[10px] text-slate-400">ريال سعودي</span>
                                </div>
                                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200">
                                    <div class="text-[11px] font-bold text-emerald-800">الإيجارات المستلمة (+)</div>
                                    <div id="rep_collected_rent" class="text-base font-extrabold font-mono text-emerald-700 mt-1">0.00</div>
                                    <span class="text-[10px] text-emerald-600">محصل فعلياً بالفترة</span>
                                </div>
                                <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200">
                                    <div class="text-[11px] font-bold text-rose-800">الإيجارات المتأخرة (!)</div>
                                    <div id="rep_overdue_rent" class="text-base font-extrabold font-mono text-rose-600 mt-1">0.00</div>
                                    <span class="text-[10px] text-rose-500">متبقي بذمة المستأجرين</span>
                                </div>
                                <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200">
                                    <div class="text-[11px] font-bold text-amber-900">عمولة المكتب (-)</div>
                                    <div id="rep_office_commission" class="text-base font-extrabold font-mono text-amber-800 mt-1">0.00</div>
                                    <span class="text-[10px] text-amber-700">تخصم من المحصل</span>
                                </div>
                                <div class="p-3.5 rounded-2xl bg-slate-100 border border-slate-300">
                                    <div class="text-[11px] font-bold text-slate-700">المصروفات والمسحوبات (-)</div>
                                    <div id="rep_expenses_paid" class="text-base font-extrabold font-mono text-slate-800 mt-1">0.00</div>
                                    <span class="text-[10px] text-slate-500">سندات صرف على المالك</span>
                                </div>
                                <div class="p-3.5 rounded-2xl bg-slate-900 text-white shadow-md">
                                    <div class="text-[11px] font-bold text-emerald-300">💎 الصافي المستحق للمالك</div>
                                    <div id="rep_net_due_owner" class="text-lg font-extrabold font-mono text-white mt-1">0.00</div>
                                    <span class="text-[10px] text-emerald-300">جاهز للتحويل البنكي</span>
                                </div>
                            </div>

                            <!-- 2. جدول العقود وحالة التحصيل والمتأخرات لكل مستأجر / وحدة -->
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-bold text-sm text-slate-900">🏢 أولاً: بيان العقود والوحدات (الإيجارات المستلمة والمتأخرة لكل مستأجر)</h4>
                                    <span id="rep_vacant_units_badge" class="px-3 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold"></span>
                                </div>
                                <div class="overflow-x-auto rounded-2xl border border-slate-300">
                                    <table class="w-full text-xs text-right">
                                        <thead class="bg-slate-900 text-white">
                                            <tr>
                                                <th class="py-3 px-3">رقم العقد</th>
                                                <th class="py-3 px-3">العقار / الوحدة</th>
                                                <th class="py-3 px-3">اسم المستأجر وجواله</th>
                                                <th class="py-3 px-3">فترة العقد والدورة</th>
                                                <th class="py-3 px-3">قيمة العقد</th>
                                                <th class="py-3 px-3 text-emerald-300">المسدد بالفترة (+)</th>
                                                <th class="py-3 px-3 text-rose-300">المتبقي / المتأخر (!)</th>
                                                <th class="py-3 px-3 text-center">حالة السداد</th>
                                            </tr>
                                        </thead>
                                        <tbody id="rep_contracts_tbody" class="divide-y divide-slate-200"></tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- 3. جدول سندات القبض والصرف الفعلية خلال الفترة -->
                            <div class="space-y-2">
                                <h4 class="font-bold text-sm text-slate-900">🧾 ثانياً: سجل سندات القبض والصرف الفعلية خلال الفترة المحددة</h4>
                                <div class="overflow-x-auto rounded-2xl border border-slate-300">
                                    <table class="w-full text-xs text-right">
                                        <thead class="bg-slate-100 text-slate-800 border-b border-slate-300">
                                            <tr>
                                                <th class="py-2.5 px-3">التاريخ</th>
                                                <th class="py-2.5 px-3">رقم السند</th>
                                                <th class="py-2.5 px-3">النوع</th>
                                                <th class="py-2.5 px-3">المستأجر / الطرف</th>
                                                <th class="py-2.5 px-3">طريقة الدفع</th>
                                                <th class="py-2.5 px-3">البيان والتفاصيل</th>
                                                <th class="py-2.5 px-3 text-emerald-700">مقبوضات للمالك (+)</th>
                                                <th class="py-2.5 px-3 text-rose-700">مصروفات / تحويلات (-)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="rep_vouchers_tbody" class="divide-y divide-slate-200"></tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- توقيع واعتماد التصفية -->
                            <div class="grid grid-cols-3 gap-6 pt-6 border-t-2 border-dashed border-slate-300 text-center text-xs text-slate-600">
                                <div>
                                    <p class="font-bold text-slate-800 mb-8">إعداد محاسب الأملاك</p>
                                    <p>........................................</p>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800 mb-8">مصادقة المالك / الوكيل</p>
                                    <p>........................................</p>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800 mb-8">ختم واعتماد المكتب العقاري</p>
                                    <p class="text-emerald-700 font-bold">معتمد إلكترونياً — {{ $office->name }} ✔</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================================= -->
                    <!-- الوضع الثاني: جدول كشف الحركات العام (لأي حساب أو مستأجر) -->
                    <!-- ========================================================= -->
                    <div id="general_ledger_statement_view" class="hidden bg-white rounded-3xl border border-slate-200 p-6">
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
                </div>

                <script>
                    const settlementOwnersData = @json($owners ?? []);
                    const settlementPropsData = @json($properties ?? []);
                    const settlementUnitsData = @json($units ?? []);
                    const settlementContractsData = @json($contracts ?? []);
                    const settlementVouchersData = @json($vouchers ?? []);

                    const cycleLabelsAr = {
                        'monthly': 'شهري',
                        'quarterly': 'ربع سنوي',
                        'semi_annual': 'نصف سنوي',
                        'annual': 'سنوي'
                    };

                    function formatMoneySAR(num) {
                        return Number(num || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    }

                    function switchStatementMode(mode) {
                        const isOwnerMode = (mode === 'owner_settlement');
                        document.getElementById('owner_settlement_view').classList.toggle('hidden', !isOwnerMode);
                        document.getElementById('owner_settlement_filters').classList.toggle('hidden', !isOwnerMode);
                        document.getElementById('general_ledger_statement_view').classList.toggle('hidden', isOwnerMode);
                        document.getElementById('general_statement_filters').classList.toggle('hidden', isOwnerMode);

                        document.getElementById('btn_mode_owner_settlement').className = isOwnerMode
                            ? 'px-4 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold transition'
                            : 'px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-bold transition';
                        document.getElementById('btn_mode_general_ledger').className = !isOwnerMode
                            ? 'px-4 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold transition'
                            : 'px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-bold transition';
                    }

                    function setSettlementFullYear() {
                        const y = new Date().getFullYear();
                        document.getElementById('settle_from_date').value = y + '-01-01';
                        document.getElementById('settle_to_date').value = y + '-12-31';
                        renderOwnerSettlementReport();
                    }

                    function onSettlementOwnerChange() {
                        const ownerId = String(document.getElementById('settle_owner_id')?.value || '');
                        const propSel = document.getElementById('settle_property_id');
                        if (propSel) {
                            Array.from(propSel.options).forEach(opt => {
                                if (!opt.value) { opt.hidden = false; return; }
                                opt.hidden = (ownerId && String(opt.getAttribute('data-owner')) !== ownerId);
                            });
                            propSel.value = '';
                            propSel.dispatchEvent(new Event('change'));
                        }
                        renderOwnerSettlementReport();
                    }

                    function renderOwnerSettlementReport() {
                        const ownerId = String(document.getElementById('settle_owner_id')?.value || '');
                        const propId = String(document.getElementById('settle_property_id')?.value || '');
                        const fromDate = document.getElementById('settle_from_date')?.value || '';
                        const toDate = document.getElementById('settle_to_date')?.value || '';

                        const emptyPrompt = document.getElementById('settlement_empty_prompt');
                        const printArea = document.getElementById('ownerSettlementPrintableArea');

                        if (!ownerId) {
                            emptyPrompt.classList.remove('hidden');
                            printArea.classList.add('hidden');
                            return;
                        }

                        const owner = settlementOwnersData.find(o => String(o.id) === ownerId);
                        if (!owner) return;

                        emptyPrompt.classList.add('hidden');
                        printArea.classList.remove('hidden');

                        const feeRate = parseFloat(owner.office_management_fee_rate || 5);
                        document.getElementById('rep_owner_name').innerText = owner.name;
                        document.getElementById('rep_owner_id_phone').innerText = (owner.national_id || '-') + ' | ' + (owner.phone || '-');
                        document.getElementById('rep_owner_iban').innerText = (owner.bank_name ? owner.bank_name + ' - ' : '') + (owner.iban || 'غير مسجل');
                        document.getElementById('rep_fee_rate').innerText = feeRate + '%';
                        document.getElementById('rep_period_label').innerText = 'الفترة: ' + (fromDate || 'البداية') + ' إلى ' + (toDate || 'اليوم');

                        const selectedPropObj = propId ? settlementPropsData.find(p => String(p.id) === propId) : null;
                        document.getElementById('rep_property_scope').innerText = selectedPropObj ? selectedPropObj.title : 'جميع عقارات المالك';

                        // 1. فلترة عقارات ووحدات وعقود المالك
                        const ownerProps = settlementPropsData.filter(p => String(p.owner_id) === ownerId && (!propId || String(p.id) === propId));
                        const ownerPropIds = ownerProps.map(p => String(p.id));
                        const ownerUnits = settlementUnitsData.filter(u => ownerPropIds.includes(String(u.property_id)));
                        const vacantCount = ownerUnits.filter(u => u.status === 'vacant').length;
                        document.getElementById('rep_vacant_units_badge').innerText =
                            `إجمالي العقارات: ${ownerProps.length} | الوحدات الشاغرة حالياً: ${vacantCount}`;

                        const ownerContracts = settlementContractsData.filter(c => {
                            if (String(c.owner_id) !== ownerId) return false;
                            if (propId && String(c.property_id) !== propId) return false;
                            return true;
                        });

                        const ownerTenantIds = ownerContracts.map(c => String(c.tenant_id));

                        // 2. فلترة سندات المالك ضمن الفترة الزمنية (سواء رُبطت بالمالك مباشرة أو بمستأجر لدى هذا المالك)
                        const periodVouchers = settlementVouchersData.filter(v => {
                            const vDate = v.voucher_date || '';
                            if (fromDate && vDate < fromDate) return false;
                            if (toDate && vDate > toDate) return false;

                            const matchOwner = String(v.owner_id || '') === ownerId;
                            const matchTenantOfOwner = !v.owner_id && v.tenant_id && ownerTenantIds.includes(String(v.tenant_id));

                            if (!matchOwner && !matchTenantOfOwner) return false;

                            if (propId && v.tenant_id) {
                                const hasContractInProp = ownerContracts.some(c => String(c.tenant_id) === String(v.tenant_id) && String(c.property_id) === propId);
                                if (!hasContractInProp) return false;
                            }
                            return true;
                        });

                        // 3. بناء جدول العقود وحساب المسدد والمتأخر لكل مستأجر
                        let totalContractsRent = 0;
                        let totalCollectedFromContracts = 0;
                        let totalOverdueRent = 0;

                        const contractsTbody = document.getElementById('rep_contracts_tbody');
                        contractsTbody.innerHTML = '';

                        if (ownerContracts.length === 0) {
                            contractsTbody.innerHTML = `<tr><td colspan="8" class="py-6 text-center text-slate-400">لا توجد عقود إيجار مسجلة لهذا المالك ضمن النطاق المحدد.</td></tr>`;
                        } else {
                            ownerContracts.forEach(c => {
                                const rentVal = parseFloat(c.total_rent_amount || 0);
                                totalContractsRent += rentVal;

                                // مجموع سندات القبض لهذا المستأجر خلال الفترة
                                const paidByTenant = periodVouchers
                                    .filter(v => v.voucher_type === 'receipt' && String(v.tenant_id || '') === String(c.tenant_id))
                                    .reduce((sum, v) => sum + parseFloat(v.amount || 0), 0);

                                const remaining = Math.max(0, rentVal - paidByTenant);
                                totalOverdueRent += remaining;

                                let statusBadge = '';
                                if (remaining <= 0.01 && rentVal > 0) {
                                    statusBadge = `<span class="px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 font-bold text-[11px]">✅ مسدد بالكامل</span>`;
                                } else if (paidByTenant > 0) {
                                    statusBadge = `<span class="px-2.5 py-1 rounded-lg bg-amber-100 text-amber-800 font-bold text-[11px]">⚠️ متأخر جزئياً</span>`;
                                } else {
                                    statusBadge = `<span class="px-2.5 py-1 rounded-lg bg-rose-100 text-rose-800 font-bold text-[11px]">🚨 لم يسدد (متأخر)</span>`;
                                }

                                const tr = document.createElement('tr');
                                tr.className = 'hover:bg-slate-50';
                                tr.innerHTML = `
                                    <td class="py-3 px-3 font-mono font-bold">${c.contract_number}</td>
                                    <td class="py-3 px-3 font-bold">${c.property_title} ${c.unit_number ? '<span class="text-emerald-700">(وحدة ' + c.unit_number + ')</span>' : ''}</td>
                                    <td class="py-3 px-3">
                                        <div class="font-bold text-slate-900">${c.tenant_name}</div>
                                        <div class="font-mono text-[10px] text-slate-500" dir="ltr">${c.tenant_phone || ''}</div>
                                    </td>
                                    <td class="py-3 px-3 text-[11px]">
                                        <div class="font-mono">${c.start_date} ← ${c.end_date}</div>
                                        <span class="text-slate-500">الدورة: ${cycleLabelsAr[c.payment_cycle] || c.payment_cycle}</span>
                                    </td>
                                    <td class="py-3 px-3 font-mono font-bold">${formatMoneySAR(rentVal)}</td>
                                    <td class="py-3 px-3 font-mono font-extrabold text-emerald-700 bg-emerald-50/50">${formatMoneySAR(paidByTenant)}</td>
                                    <td class="py-3 px-3 font-mono font-extrabold ${remaining > 0 ? 'text-rose-600 bg-rose-50/50' : 'text-slate-400'}">${formatMoneySAR(remaining)}</td>
                                    <td class="py-3 px-3 text-center">${statusBadge}</td>
                                `;
                                contractsTbody.appendChild(tr);
                            });
                        }

                        // 4. بناء جدول السندات وحساب إجمالي المقبوضات والمصروفات الفعلية
                        const totalReceiptsPeriod = periodVouchers
                            .filter(v => v.voucher_type === 'receipt')
                            .reduce((sum, v) => sum + parseFloat(v.amount || 0), 0);

                        const totalPaymentsPeriod = periodVouchers
                            .filter(v => v.voucher_type === 'payment')
                            .reduce((sum, v) => sum + parseFloat(v.amount || 0), 0);

                        const officeCommissionAmount = (totalReceiptsPeriod * feeRate) / 100;
                        const netDueToOwner = totalReceiptsPeriod - officeCommissionAmount - totalPaymentsPeriod;

                        document.getElementById('rep_total_contract_rent').innerText = formatMoneySAR(totalContractsRent);
                        document.getElementById('rep_collected_rent').innerText = formatMoneySAR(totalReceiptsPeriod);
                        document.getElementById('rep_overdue_rent').innerText = formatMoneySAR(totalOverdueRent);
                        document.getElementById('rep_office_commission').innerText = formatMoneySAR(officeCommissionAmount);
                        document.getElementById('rep_expenses_paid').innerText = formatMoneySAR(totalPaymentsPeriod);
                        document.getElementById('rep_net_due_owner').innerText = formatMoneySAR(netDueToOwner) + ' ر.س';

                        const vouchersTbody = document.getElementById('rep_vouchers_tbody');
                        vouchersTbody.innerHTML = '';

                        if (periodVouchers.length === 0) {
                            vouchersTbody.innerHTML = `<tr><td colspan="8" class="py-6 text-center text-slate-400">لا توجد سندات قبض أو صرف مسجلة خلال هذه الفترة.</td></tr>`;
                        } else {
                            periodVouchers.forEach(v => {
                                const isRec = v.voucher_type === 'receipt';
                                const tr = document.createElement('tr');
                                tr.className = 'hover:bg-slate-50';
                                tr.innerHTML = `
                                    <td class="py-2.5 px-3 font-mono">${v.voucher_date}</td>
                                    <td class="py-2.5 px-3 font-mono font-bold">${v.voucher_number}</td>
                                    <td class="py-2.5 px-3">
                                        <span class="px-2 py-0.5 rounded font-bold text-[10px] ${isRec ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'}">
                                            ${isRec ? 'سند قبض إيجار' : 'سند صرف / تحويل'}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3 font-bold">${v.tenant_name || v.owner_name || '-'}</td>
                                    <td class="py-2.5 px-3">${v.payment_method || '-'}</td>
                                    <td class="py-2.5 px-3">${v.notes || '-'}</td>
                                    <td class="py-2.5 px-3 font-mono font-bold text-emerald-700">${isRec ? formatMoneySAR(v.amount) : '-'}</td>
                                    <td class="py-2.5 px-3 font-mono font-bold text-rose-600">${!isRec ? formatMoneySAR(v.amount) : '-'}</td>
                                `;
                                vouchersTbody.appendChild(tr);
                            });
                        }
                    }

                    function downloadOwnerSettlementPDF() {
                        const printArea = document.getElementById('ownerSettlementPrintableArea');
                        if (!printArea || printArea.classList.contains('hidden')) {
                            alert('يرجى اختيار المالك أولاً لعرض برنت التصفية ثم تصديره.');
                            return;
                        }
                        const ownerName = document.getElementById('rep_owner_name')?.innerText || 'Owner';
                        html2pdf().set({
                            margin: 8,
                            filename: 'تصفية_حساب_' + ownerName + '.pdf',
                            image: { type: 'jpeg', quality: 0.98 },
                            html2canvas: { scale: 2, useCORS: true },
                            jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
                        }).from(printArea).save();
                    }

                    function printOwnerSettlementArea() {
                        const printArea = document.getElementById('ownerSettlementPrintableArea');
                        if (!printArea || printArea.classList.contains('hidden')) {
                            window.print();
                            return;
                        }
                        const origId = printArea.id;
                        const prevPrintable = document.getElementById('printableVoucherArea');
                        if (prevPrintable) prevPrintable.id = 'temp_old_printable';
                        printArea.id = 'printableVoucherArea';
                        window.print();
                        printArea.id = origId;
                        if (prevPrintable) prevPrintable.id = 'printableVoucherArea';
                    }

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
                                <button type="button" onclick="exportStatementPDF()" class="px-4 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow flex items-center gap-1.5">
                                    <span>📥 تصدير PDF</span>
                                </button>
                                <button type="button" onclick="exportTableToCSV('generalStatementTable', 'General_Ledger_Pro.csv')" class="px-4 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow flex items-center gap-1.5">
                                    <span>📊 تصدير Excel</span>
                                </button>
                                <form action="{{ route('office.accounting.close_year') }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إقفال السنة المالية وترحيل صافي الأرباح والمصروفات إلى حساب الأرباح المبقاة؟');">
                                    @csrf
                                    <button type="submit" class="px-4 py-2.5 rounded-2xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold shadow flex items-center gap-1.5">
                                        <span>🔒 إقفال السنة المالية</span>
                                    </button>
                                </form>
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

            <!-- سجل تتبع حركات موظفي المكتب فقط (لا يعرض الملاك ولا يعرض MAC/IP) -->
            @if($tab == 'activity_logs')
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="font-bold text-base text-slate-900">🕵️ سجل تتبع عمليات وحركات موظفي المكتب</h3>
                            <p class="text-xs text-slate-500 mt-0.5">يعرض جميع العمليات والتعديلات التي قام بها موظفو المكتب وتوقيتها بالتفصيل</p>
                        </div>
                        <div class="w-72">
                            <input type="text" oninput="const q=this.value.toLowerCase(); document.querySelectorAll('.off-act-row').forEach(r=>r.style.display=r.innerText.toLowerCase().includes(q)?'':'none');"
                                   placeholder="🔍 ابحث باسم الموظف أو العملية..."
                                   class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-2 text-xs focus:bg-white">
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-right">
                            <thead class="bg-slate-50 text-slate-600 border-b">
                                <tr>
                                    <th class="py-3.5 px-4">#</th>
                                    <th class="py-3.5 px-4">اسم الموظف</th>
                                    <th class="py-3.5 px-4">نوع العملية</th>
                                    <th class="py-3.5 px-4">القسم / الإجراء</th>
                                    <th class="py-3.5 px-4">تفاصيل التعديل / الإضافة</th>
                                    <th class="py-3.5 px-4">التاريخ والوقت</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse(($activityLogs ?? collect()) as $idx => $log)
                                    <tr class="off-act-row hover:bg-slate-50">
                                        <td class="py-3 px-4 font-mono font-bold">{{ $idx + 1 }}</td>
                                        <td class="py-3 px-4 font-bold text-slate-900">{{ $log->user_name }}</td>
                                        <td class="py-3 px-4">
                                            <span class="px-2.5 py-1 rounded-lg font-bold {{ $log->action_type == 'حذف' ? 'bg-rose-50 text-rose-700' : ($log->action_type == 'إضافة' ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700') }}">
                                                {{ $log->action_type }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 font-bold text-slate-800">{{ $log->action_label }}</td>
                                        <td class="py-3 px-4 max-w-md break-words text-slate-600">{{ $log->details }}</td>
                                        <td class="py-3 px-4 font-mono text-slate-500" dir="ltr">{{ $log->created_at }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="py-10 text-center text-slate-400">لا توجد حركات مسجلة لموظفي المكتب حتى الآن.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
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

                        <!-- قسم تفعيل أو إيقاف التنبيهات الآلية (تعمل تلقائياً عند التفعيل بدون زر يدوي) -->
                        <div class="p-5 rounded-2xl bg-amber-50/70 border border-amber-200 space-y-4">
                            <div class="flex items-center justify-between border-b border-amber-200 pb-2.5">
                                <div>
                                    <h4 class="font-bold text-sm text-slate-900">🔔 إعدادات التنبيهات الآلية التلقائية</h4>
                                    <p class="text-[11px] text-slate-600">عند تفعيل هذا الخيار يرسل النظام التنبيهات آلياً في موعدها، وعند إيقافه يتوقف الإرسال</p>
                                </div>
                                <label class="flex items-center gap-2 text-xs font-bold text-emerald-800 cursor-pointer">
                                    <input type="checkbox" name="enable_auto_reminders" value="1" {{ ($office->enable_auto_reminders ?? 1) ? 'checked' : '' }} class="rounded text-emerald-600">
                                    <span>تفعيل الإرسال الآلي للتنبيهات</span>
                                </label>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">التنبيه قبل تاريخ الاستحقاق بـ (عدد الأيام) *</label>
                                    <input type="number" name="reminder_days_before" value="{{ $office->reminder_days_before ?? 15 }}" min="1" max="120"
                                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs font-mono font-bold bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">إرسال التنبيه الآلي إلى *</label>
                                    <select name="reminder_target" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-bold">
                                        <option value="both" {{ ($office->reminder_target ?? 'both') == 'both' ? 'selected' : '' }}>المكتب + المستأجر + المالك معاً</option>
                                        <option value="tenant" {{ ($office->reminder_target ?? '') == 'tenant' ? 'selected' : '' }}>المستأجر فقط</option>
                                        <option value="office" {{ ($office->reminder_target ?? '') == 'office' ? 'selected' : '' }}>المكتب والمالك فقط</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">نص رسالة التنبيه الآلي</label>
                                <textarea name="reminder_template" rows="2" placeholder="عزيزي {اسم_المستأجر}، نود تذكيركم بقرب استحقاق دفعة الإيجار للعقار ({اسم_العقار} - {رقم_الوحدة}) بمبلغ {المبلغ} ر.س بتاريخ {تاريخ_الاستحقاق}."
                                          class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs bg-white">{{ $office->reminder_template ?? '' }}</textarea>
                            </div>
                        </div>

                        <button type="submit" class="px-6 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg">
                            💾 حفظ إعدادات الواتساب والتنبيهات الآلية
                        </button>
                    </form>
                </div>

                <!-- فورم إعدادات نصوص رسائل ملفات الـ PDF + اعتماد الصرف + شروط الشذوذ -->
                <div class="max-w-2xl bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-5 mt-6">
                    <h3 class="font-bold text-base text-slate-900 border-b pb-3">📄 نصوص رسائل ملفات الـ PDF بالواتساب + شروط الاعتماد والشذوذ</h3>
                    <form action="{{ route('office.smart_settings.save') }}" method="POST" class="space-y-4">
                        @csrf

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                            <div class="text-xs font-bold text-slate-900">💬 نص الرسالة المرافق لملفات الـ PDF المرسلة بالواتساب (اترك الحقل فارغاً لإرسال ملف الـ PDF الأصلي بدون أي رسالة نصية)</div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">1. نص الرسالة مع ملف الفاتورة الضريبية (PDF)</label>
                                <input type="text" name="wa_invoice_caption" value="{{ $office->wa_invoice_caption ?? '' }}" placeholder="اتركه فارغاً لإرسال ملف PDF فقط بدون نص، أو اكتب مثلاً: مرفق لكم الفاتورة الضريبية..." class="w-full rounded-xl border px-3 py-2 text-xs bg-white">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">2. نص الرسالة مع ملف سند القبض أو الصرف (PDF)</label>
                                <input type="text" name="wa_voucher_caption" value="{{ $office->wa_voucher_caption ?? '' }}" placeholder="اتركه فارغاً لإرسال ملف PDF فقط بدون نص..." class="w-full rounded-xl border px-3 py-2 text-xs bg-white">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">3. نص الرسالة مع ملف الإنذار القانوني (PDF)</label>
                                <input type="text" name="wa_legal_caption" value="{{ $office->wa_legal_caption ?? '' }}" placeholder="اتركه فارغاً لإرسال ملف PDF فقط بدون نص..." class="w-full rounded-xl border px-3 py-2 text-xs bg-white">
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 space-y-3">
                            <label class="flex items-center gap-2 text-xs font-bold text-emerald-950 cursor-pointer">
                                <input type="checkbox" name="enable_wa_voucher_approval" value="1" {{ !empty($office->enable_wa_voucher_approval) ? 'checked' : '' }}>
                                <span>📲 تفعيل اعتماد سندات الصرف عبر الواتساب</span>
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">المبلغ الذي يبدأ عنده طلب الاعتماد (ر.س)</label>
                                    <input type="number" step="0.01" name="wa_approval_threshold" value="{{ $office->wa_approval_threshold ?? 10000 }}" class="w-full rounded-xl border px-3 py-2 text-xs font-mono font-bold bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">الكلمات المقبولة للاعتماد (افصل بفاصلة)</label>
                                    <input type="text" name="wa_approval_keywords" value="{{ $office->wa_approval_keywords ?? 'اعتمد,تم,اوك,موافق,نعم' }}" class="w-full rounded-xl border px-3 py-2 text-xs font-bold bg-white">
                                </div>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-rose-50/60 border border-rose-200 space-y-3">
                            <label class="flex items-center gap-2 text-xs font-bold text-rose-950 cursor-pointer">
                                <input type="checkbox" name="enable_anomaly_detector" value="1" {{ ($office->enable_anomaly_detector ?? 1) ? 'checked' : '' }}>
                                <span>🚨 تفعيل كاشف الشذوذ المحاسبي وتحديد شروط التنبيه</span>
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">تنبيه إذا زاد السند عن (ر.س)</label>
                                    <input type="number" step="0.01" name="anomaly_max_amount" value="{{ $office->anomaly_max_amount ?? 20000 }}" class="w-full rounded-xl border px-3 py-2 text-xs font-mono font-bold bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">بداية وقت الليل المحظور (0-23)</label>
                                    <input type="number" min="0" max="23" name="anomaly_night_start" value="{{ $office->anomaly_night_start ?? 23 }}" class="w-full rounded-xl border px-3 py-2 text-xs font-mono bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">نهاية وقت الليل المحظور (0-23)</label>
                                    <input type="number" min="0" max="23" name="anomaly_night_end" value="{{ $office->anomaly_night_end ?? 5 }}" class="w-full rounded-xl border px-3 py-2 text-xs font-mono bg-white">
                                </div>
                            </div>
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                                <input type="checkbox" name="anomaly_alert_on_delete" value="1" {{ ($office->anomaly_alert_on_delete ?? 1) ? 'checked' : '' }}>
                                <span>التنبيه عند قيام أي موظف بحذف سند أو قيد أو عقد</span>
                            </label>
                        </div>

                        <button type="submit" class="px-6 py-3 rounded-2xl bg-slate-900 text-white text-xs font-bold">
                            💾 حفظ نصوص الرسائل وشروط الشذوذ والاعتماد
                        </button>
                    </form>
                </div>
            @endif

        <!-- ========================================================================= -->
            <!-- تبويب جدولة الدفعات المستحقة + صفحة أعمار الديون العقارية الكاملة -->
            <!-- ========================================================================= -->
            @if($tab == 'installments')
                @php
                    $instAll = $installments ?? collect();
                    $totalInstAmount = $instAll->sum('amount');
                    $totalInstPaid = $instAll->sum('paid_amount');
                    $totalInstRem = max(0, $totalInstAmount - $totalInstPaid);
                    $overdueInstCount = $instAll->whereIn('status', ['overdue', 'partial_overdue'])->count();
                    $cycleArMap = ['monthly' => 'شهري', 'quarterly' => 'ربع سنوي', 'semi_annual' => 'نصف سنوي', 'annual' => 'سنوي'];
                @endphp

                <!-- بطاقات الملخص العلوية + زر التبديل لصفحة أعمار الديون -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-5">
                    <div class="bg-white p-5 rounded-2xl border border-slate-200">
                        <span class="text-xs font-bold text-slate-500">إجمالي قيمة الدفعات المجدولة</span>
                        <div class="text-xl font-extrabold text-slate-900 font-mono mt-1">{{ number_format($totalInstAmount, 2) }} ر.س</div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-emerald-200">
                        <span class="text-xs font-bold text-emerald-700">إجمالي الدفعات المحصلة (+)</span>
                        <div class="text-xl font-extrabold text-emerald-700 font-mono mt-1">{{ number_format($totalInstPaid, 2) }} ر.س</div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-rose-200">
                        <span class="text-xs font-bold text-rose-700">إجمالي المتبقي غير المسدد (!)</span>
                        <div class="text-xl font-extrabold text-rose-600 font-mono mt-1">{{ number_format($totalInstRem, 2) }} ر.س</div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-amber-200 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-amber-800">دفعات متأخرة حالياً</span>
                            <div class="text-xl font-extrabold text-amber-700 font-mono mt-1">{{ $overdueInstCount }} دفعة</div>
                        </div>
                        <button type="button" id="btnToggleAgingPage" onclick="toggleInstallmentsAgingPage('aging')"
                                class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-white text-xs font-bold transition shadow">
                            ⏳ صفحة أعمار الديون
                        </button>
                    </div>
                </div>

                <!-- 1. الصفحة الرئيسية: جدول الأقساط والدفعات الإيجارية المستحقة (مفرود ومرتب بدون انكسار النص) -->
                <div id="mainInstallmentsPageSection" class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-visible">
                    <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="font-bold text-base text-slate-900">📅 جدول الأقساط والدفعات الإيجارية المستحقة</h3>
                            <p class="text-xs text-slate-500 mt-0.5">اضغط على اسم المستأجر أو زر (السندات) لعرض السند المرتبط بالدفعة، وتختفي الدفعة تلقائياً عند اكتمال سدادها</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" onclick="toggleInstallmentsAgingPage('aging')"
                                    class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold">
                                ⏳ فتح صفحة أعمار الديون والمتأخرات
                            </button>
                            <button type="button" onclick="exportElementToPDF('installmentsPrintableWrap', 'جدولة_الدفعات_المستحقة.pdf')"
                                    class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold">
                                📥 تصدير PDF
                            </button>
                            <button type="button" onclick="exportTableToCSV('installmentsMainTable', 'جدولة_الدفعات_المستحقة.xlsx')"
                                    class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">
                                📊 تصدير Excel
                            </button>
                        </div>
                    </div>

                    <!-- شريط الفلترة المرتب -->
                    <div class="p-4 bg-slate-50 border-b border-slate-200 grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                        <div class="md:col-span-4">
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">🔍 بحث باسم المستأجر أو المالك أو العقار</label>
                            <input type="text" id="inst_search_name" oninput="filterInstallmentsTable()" placeholder="اكتب اسم المستأجر أو المالك أو العقار..."
                                   class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">فلتر حالة الدفعة</label>
                            <select id="inst_status_filter" onchange="filterInstallmentsTable()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-bold">
                                <option value="unpaid">المستحقة والمتأخرة فقط (إخفاء المسددة)</option>
                                <option value="all">عرض كافة الدفعات (شامل المسددة بالكامل)</option>
                                <option value="overdue">المتأخرة فقط (Overdue)</option>
                                <option value="partial_overdue">متأخرة جزئياً (سُدد جزء منها)</option>
                                <option value="partial">مسددة جزئياً (قبل موعدها)</option>
                                <option value="due">مستحقة قادمة</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">من تاريخ</label>
                            <input type="date" id="inst_from_date" onchange="filterInstallmentsTable()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-mono">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">إلى تاريخ</label>
                            <input type="date" id="inst_to_date" onchange="filterInstallmentsTable()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-mono">
                        </div>
                        <div class="md:col-span-1">
                            <button type="button" onclick="document.getElementById('inst_search_name').value=''; document.getElementById('inst_from_date').value=''; document.getElementById('inst_to_date').value=''; document.getElementById('inst_status_filter').value='all'; document.getElementById('inst_status_filter').dispatchEvent(new Event('change')); filterInstallmentsTable();"
                                    class="w-full py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-xs font-bold">
                                الكل
                            </button>
                        </div>
                    </div>

                    <div id="installmentsPrintableWrap" class="overflow-x-auto">
                        <table class="w-full text-xs text-right border-collapse" id="installmentsMainTable">
                            <thead class="bg-slate-100 text-slate-700 border-b border-slate-200 whitespace-nowrap">
                                <tr>
                                    <th class="py-3.5 px-4 text-center">رقم الدفعة</th>
                                    <th class="py-3.5 px-4">المستأجر وجواله</th>
                                    <th class="py-3.5 px-4">العقار والوحدة</th>
                                    <th class="py-3.5 px-4">المالك</th>
                                    <th class="py-3.5 px-4 text-center">تاريخ الاستحقاق</th>
                                    <th class="py-3.5 px-4 text-center">مبلغ الدفعة</th>
                                    <th class="py-3.5 px-4 text-center text-emerald-700">المسدد</th>
                                    <th class="py-3.5 px-4 text-center text-rose-600">المتبقي</th>
                                    <th class="py-3.5 px-4 text-center">الحالة</th>
                                    <th class="py-3.5 px-4 text-center no-export-pdf">السداد / السندات</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @forelse($instAll as $inst)
                                    @php
                                        $remAmt = round($inst->amount - $inst->paid_amount, 2);
                                        $linkedVoucherIds = json_decode($inst->voucher_ids_json ?? '[]', true) ?: [];
                                        if ($inst->last_voucher_id && !in_array($inst->last_voucher_id, $linkedVoucherIds)) {
                                            $linkedVoucherIds[] = $inst->last_voucher_id;
                                        }
                                        $linkedVouchersCount = $vouchers->whereIn('id', $linkedVoucherIds)->count();
                                    @endphp
                                    <tr class="inst-row hover:bg-slate-50 transition {{ $inst->status === 'paid' ? 'hidden' : '' }}"
                                        data-search="{{ mb_strtolower($inst->tenant_name . ' ' . $inst->owner_name . ' ' . $inst->property_title . ' ' . $inst->contract_number) }}"
                                        data-date="{{ $inst->due_date }}"
                                        data-status="{{ $inst->status }}">
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            <span class="px-2.5 py-1 rounded-lg bg-slate-100 font-bold text-slate-900 inline-block">دفعة {{ $inst->installment_number }}</span>
                                            <span class="text-[10px] text-slate-400 block mt-0.5">{{ $cycleArMap[$inst->payment_cycle] ?? $inst->payment_cycle }}</span>
                                        </td>
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <button type="button" onclick="openModal('instVouchersModal_{{ $inst->id }}')" class="text-right group">
                                                <div class="font-bold text-slate-900 group-hover:text-emerald-700 underline">{{ $inst->tenant_name }}</div>
                                                <div class="font-mono text-[11px] text-slate-500" dir="ltr">{{ $inst->tenant_phone }}</div>
                                            </button>
                                        </td>
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <div class="font-bold text-slate-800">{{ $inst->property_title }}</div>
                                            @if($inst->unit_number)
                                                <span class="text-emerald-700 text-[11px] font-semibold">وحدة: {{ $inst->unit_number }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 whitespace-nowrap text-slate-700 font-semibold">{{ $inst->owner_name }}</td>
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap font-mono font-bold text-slate-800" dir="ltr">{{ $inst->due_date }}</td>
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap font-mono font-bold">{{ number_format($inst->amount, 2) }} <span class="text-[10px] font-normal">ر.س</span></td>
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap font-mono font-bold text-emerald-700">{{ number_format($inst->paid_amount, 2) }} <span class="text-[10px] font-normal">ر.س</span></td>
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap font-mono font-bold text-rose-600">{{ number_format($remAmt, 2) }} <span class="text-[10px] font-normal">ر.س</span></td>
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            @if($inst->status === 'paid')
                                                <span class="px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 font-bold">مكتملة ✔</span>
                                            @elseif($inst->status === 'partial_overdue')
                                                <span class="px-2.5 py-1 rounded-lg bg-amber-100 text-amber-900 font-bold">متأخر جزئي</span>
                                            @elseif($inst->status === 'overdue')
                                                <span class="px-2.5 py-1 rounded-lg bg-rose-100 text-rose-800 font-bold">متأخر</span>
                                            @elseif($inst->status === 'partial')
                                                <span class="px-2.5 py-1 rounded-lg bg-sky-100 text-sky-800 font-bold">سداد جزئي</span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-bold">مستحق</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap no-export-pdf">
                                            <div class="flex items-center justify-center gap-1.5">
                                                @if($remAmt > 0)
                                                    <button type="button" onclick="openPayInstallmentModal('{{ $inst->id }}', '{{ addslashes($inst->tenant_name) }}', '{{ $remAmt }}', 'دفعة رقم {{ $inst->installment_number }} للعقد {{ $inst->contract_number }}')"
                                                            class="px-3 py-1.5 rounded-xl bg-slate-900 text-white text-[11px] font-bold">
                                                        سداد
                                                    </button>
                                                @endif
                                                <button type="button" onclick="openModal('instVouchersModal_{{ $inst->id }}')"
                                                        class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-[11px] font-bold border">
                                                    🧾 السندات ({{ $linkedVouchersCount }})
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="10" class="py-10 text-center text-slate-400">لا توجد دفعات مجدولة حالياً.</td></tr>
                                @endforelse
                                <tr id="instNoMatchRow" class="hidden">
                                    <td colspan="10" class="py-10 text-center text-slate-500 font-bold">
                                        لا توجد دفعات مطابقة لهذا الفلتر حالياً —
                                        <button type="button" onclick="document.getElementById('inst_status_filter').value='all'; document.getElementById('inst_status_filter').dispatchEvent(new Event('change')); filterInstallmentsTable();" class="text-emerald-700 underline font-extrabold">اضغط هنا لعرض كافة الدفعات</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 2. صفحة تقرير أعمار الديون والمتأخرات العقارية الكاملة (Aging Receivables Page) -->
                <div id="agingReceivablesPageSection" class="hidden bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-visible space-y-5 p-6">
                    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4">
                        <div>
                            <h3 class="font-bold text-lg text-slate-900">⏳ تقرير أعمار الديون والمتأخرات العقارية التفصيلي</h3>
                            <p class="text-xs text-slate-500 mt-0.5">فلترة ذكية حسب فئة التأخير (1-30، 31-60، 61-90، +90 يوم) أو تحديد نطاق أيام حر (مثلاً من 1 إلى 70 يوم) مع البحث بالاسم</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" onclick="toggleInstallmentsAgingPage('installments')"
                                    class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-900 text-xs font-bold border border-slate-300">
                                ↩ العودة لجدول الدفعات والأقساط
                            </button>
                            <button type="button" onclick="exportTableToCSV('agingReceivablesTable', 'تقرير_أعمار_الديون_العقارية.xlsx')"
                                    class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">
                                📊 تصدير Excel
                            </button>
                            <button type="button" onclick="exportElementToPDF('agingPrintableArea', 'تقرير_أعمار_الديون_العقارية.pdf')"
                                    class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold">
                                📥 تصدير PDF
                            </button>
                        </div>
                    </div>

                    <!-- بطاقات فئات أعمار الديون (قابلة للضغط للفلترة السريعة) -->
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 text-center">
                        <button type="button" onclick="setAgingQuickBucket('all', '', '')" class="p-3.5 rounded-2xl bg-slate-50 hover:bg-slate-100 border border-slate-300 transition">
                            <div class="text-xs font-bold text-slate-700">جميع المتأخرات</div>
                            <div class="text-base font-extrabold font-mono text-slate-900 mt-1">
                                {{ number_format(($agingBuckets['b_30'] ?? 0) + ($agingBuckets['b_60'] ?? 0) + ($agingBuckets['b_90'] ?? 0) + ($agingBuckets['b_plus'] ?? 0), 2) }} ر.س
                            </div>
                        </button>
                        <button type="button" onclick="setAgingQuickBucket('1_30', 1, 30)" class="p-3.5 rounded-2xl bg-amber-50 hover:bg-amber-100 border border-amber-200 transition">
                            <div class="text-xs font-bold text-amber-900">تأخير 1 - 30 يوم</div>
                            <div class="text-base font-extrabold font-mono text-amber-700 mt-1">{{ number_format($agingBuckets['b_30'] ?? 0, 2) }} ر.س</div>
                        </button>
                        <button type="button" onclick="setAgingQuickBucket('31_60', 31, 60)" class="p-3.5 rounded-2xl bg-orange-50 hover:bg-orange-100 border border-orange-200 transition">
                            <div class="text-xs font-bold text-orange-900">تأخير 31 - 60 يوم</div>
                            <div class="text-base font-extrabold font-mono text-orange-700 mt-1">{{ number_format($agingBuckets['b_60'] ?? 0, 2) }} ر.س</div>
                        </button>
                        <button type="button" onclick="setAgingQuickBucket('61_90', 61, 90)" class="p-3.5 rounded-2xl bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">
                            <div class="text-xs font-bold text-rose-900">تأخير 61 - 90 يوم</div>
                            <div class="text-base font-extrabold font-mono text-rose-700 mt-1">{{ number_format($agingBuckets['b_90'] ?? 0, 2) }} ر.س</div>
                        </button>
                        <button type="button" onclick="setAgingQuickBucket('90_plus', 91, 99999)" class="p-3.5 rounded-2xl bg-slate-900 text-white transition">
                            <div class="text-xs font-bold text-rose-300">أكثر من 90 يوم</div>
                            <div class="text-base font-extrabold font-mono text-white mt-1">{{ number_format($agingBuckets['b_plus'] ?? 0, 2) }} ر.س</div>
                        </button>
                    </div>

                    <!-- شريط فلاتر أعمار الديون المتقدم: (بحث بالاسم + اختيار الفئة + تحديد مخصص بالأيام من 1 إلى 70 أو أي رقم) -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                        <div class="md:col-span-4">
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">🔍 بحث بالاسم (المستأجر / المالك / العقار / الجوال)</label>
                            <input type="text" id="aging_search_name" oninput="filterAgingPageTable()" placeholder="اكتب اسم المستأجر أو المالك أو العقار..."
                                   class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">فئة عمر الدين السريعة</label>
                            <select id="aging_bucket_select" onchange="onAgingBucketSelectChange(this.value)" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-bold">
                                <option value="all">جميع الفترات</option>
                                <option value="1_30">من 1 إلى 30 يوم</option>
                                <option value="31_60">من 31 إلى 60 يوم</option>
                                <option value="61_90">من 61 إلى 90 يوم</option>
                                <option value="90_plus">أكثر من 90 يوم</option>
                                <option value="custom">تحديد يدوي مخصص بالأيام (من - إلى)</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-emerald-800 mb-1">من (عدد أيام التأخير)</label>
                            <input type="number" min="0" id="aging_min_days" oninput="document.getElementById('aging_bucket_select').value='custom'; filterAgingPageTable();" placeholder="مثال: 1"
                                   class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-mono font-bold">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-emerald-800 mb-1">إلى (عدد أيام التأخير)</label>
                            <input type="number" min="0" id="aging_max_days" oninput="document.getElementById('aging_bucket_select').value='custom'; filterAgingPageTable();" placeholder="مثال: 70"
                                   class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs bg-white font-mono font-bold">
                        </div>
                        <div class="md:col-span-1">
                            <button type="button" onclick="resetAgingFilters()" class="w-full py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-xs font-bold">
                                تصفير
                            </button>
                        </div>
                    </div>

                    <div id="agingPrintableArea" class="overflow-x-auto rounded-2xl border border-slate-200">
                        <table class="w-full text-xs text-right border-collapse" id="agingReceivablesTable">
                            <thead class="bg-slate-900 text-white whitespace-nowrap">
                                <tr>
                                    <th class="py-3.5 px-4">المستأجر</th>
                                    <th class="py-3.5 px-4">رقم الجوال</th>
                                    <th class="py-3.5 px-4">العقار والوحدة</th>
                                    <th class="py-3.5 px-4">المالك</th>
                                    <th class="py-3.5 px-4 text-center">تاريخ الاستحقاق</th>
                                    <th class="py-3.5 px-4 text-center">مدة التأخير (بالأيام)</th>
                                    <th class="py-3.5 px-4 text-center">المبلغ المتأخر</th>
                                    <th class="py-3.5 px-4 text-center no-export-pdf">إجراء</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @forelse(($agingBuckets['rows'] ?? []) as $ar)
                                    <tr class="aging-row hover:bg-slate-50 whitespace-nowrap"
                                        data-search="{{ mb_strtolower($ar->tenant_name . ' ' . $ar->owner_name . ' ' . $ar->property_title . ' ' . $ar->tenant_phone) }}"
                                        data-days="{{ $ar->days_late }}"
                                        data-amount="{{ $ar->remaining_amount }}">
                                        <td class="py-3 px-4 font-bold text-slate-900">{{ $ar->tenant_name }}</td>
                                        <td class="py-3 px-4 font-mono" dir="ltr">{{ $ar->tenant_phone }}</td>
                                        <td class="py-3 px-4 font-bold">{{ $ar->property_title }} {{ $ar->unit_number ? '- وحدة ' . $ar->unit_number : '' }}</td>
                                        <td class="py-3 px-4 text-slate-700">{{ $ar->owner_name }}</td>
                                        <td class="py-3 px-4 text-center font-mono" dir="ltr">{{ $ar->due_date }}</td>
                                        <td class="py-3 px-4 text-center font-mono font-extrabold text-rose-600">{{ $ar->days_late }} يوم</td>
                                        <td class="py-3 px-4 text-center font-mono font-extrabold text-rose-700">{{ number_format($ar->remaining_amount, 2) }} ر.س</td>
                                        <td class="py-3 px-4 text-center no-export-pdf">
                                            <button type="button" onclick="openPayInstallmentModal('{{ $ar->id }}', '{{ addslashes($ar->tenant_name) }}', '{{ $ar->remaining_amount }}', 'سداد متأخرات دفعة رقم {{ $ar->installment_number }}')"
                                                    class="px-3 py-1.5 rounded-xl bg-slate-900 text-white text-[11px] font-bold">
                                                💰 تحصيل الآن
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="py-8 text-center text-slate-400">لا توجد ديون متأخرة حالياً.</td></tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-slate-100 font-extrabold border-t-2 border-slate-300">
                                <tr>
                                    <td colspan="6" class="py-3 px-4 text-left">إجمالي المتأخرات المعروضة حسب الفلتر:</td>
                                    <td id="agingFilteredTotalCell" class="py-3 px-4 text-center font-mono text-rose-700 text-sm">
                                        {{ number_format(($agingBuckets['b_30'] ?? 0) + ($agingBuckets['b_60'] ?? 0) + ($agingBuckets['b_90'] ?? 0) + ($agingBuckets['b_plus'] ?? 0), 2) }} ر.س
                                    </td>
                                    <td class="no-export-pdf"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- نوافذ السندات المرتبطة بالدفعة -->
                @foreach($instAll as $inst)
                    @php
                        $linkedVoucherIds = json_decode($inst->voucher_ids_json ?? '[]', true) ?: [];
                        if ($inst->last_voucher_id && !in_array($inst->last_voucher_id, $linkedVoucherIds)) {
                            $linkedVoucherIds[] = $inst->last_voucher_id;
                        }
                        $linkedVouchers = $vouchers->whereIn('id', $linkedVoucherIds)->values();
                    @endphp
                    <div id="instVouchersModal_{{ $inst->id }}" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200">
                            <div class="flex items-center justify-between pb-3 border-b mb-4">
                                <h3 class="font-bold text-base text-slate-900">🧾 السندات المرتبطة بالدفعة #{{ $inst->installment_number }} — {{ $inst->tenant_name }}</h3>
                                <button type="button" onclick="closeModal('instVouchersModal_{{ $inst->id }}')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                            </div>
                            <table class="w-full text-xs text-right border">
                                <thead class="bg-slate-100">
                                    <tr>
                                        <th class="py-2.5 px-3">رقم السند</th>
                                        <th class="py-2.5 px-3">التاريخ</th>
                                        <th class="py-2.5 px-3">المبلغ المسدد</th>
                                        <th class="py-2.5 px-3">طريقة الدفع</th>
                                        <th class="py-2.5 px-3">البيان</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @forelse($linkedVouchers as $lv)
                                        <tr>
                                            <td class="py-2.5 px-3 font-mono font-bold">{{ $lv->voucher_number }}</td>
                                            <td class="py-2.5 px-3 font-mono">{{ $lv->voucher_date }}</td>
                                            <td class="py-2.5 px-3 font-mono font-bold text-emerald-700">{{ number_format($lv->amount, 2) }} ر.س</td>
                                            <td class="py-2.5 px-3">{{ \App\Http\Controllers\CompanyErpController::translatePaymentMethod($lv->payment_method) }}</td>
                                            <td class="py-2.5 px-3">{{ $lv->notes }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="py-6 text-center text-slate-400">لا توجد سندات مسجلة لهذه الدفعة بعد.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach

                <!-- نافذة تسجيل سداد كلي أو جزئي للدفعة -->
                <div id="payInstallmentModal" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                        <div class="flex items-center justify-between pb-3 border-b mb-4">
                            <h3 class="font-bold text-base text-slate-900">💰 تسجيل سداد دفعة (كلي أو جزئي)</h3>
                            <button type="button" onclick="closeModal('payInstallmentModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                        </div>
                        <form action="{{ route('office.installments.pay') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="installment_id" id="pay_inst_id">
                            <div class="p-3 rounded-xl bg-slate-50 border text-xs font-bold text-slate-800" id="pay_inst_tenant_label"></div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">المبلغ المسدد الآن (ر.س) *</label>
                                <input type="number" step="0.01" name="amount" id="pay_inst_amount" required class="w-full rounded-xl border px-3.5 py-2 text-sm font-mono font-bold text-emerald-700">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">طريقة الدفع (تربط حساب الصندوق/البنك تلقائياً) *</label>
                                <select name="payment_method" onchange="const acc=this.options[this.selectedIndex].getAttribute('data-acc'); if(acc) document.getElementById('pay_inst_debit_acc').value=acc;" class="w-full rounded-xl border px-3 py-2 text-xs bg-white font-bold">
                                    @foreach($paymentMethods as $pm)
                                        <option value="{{ $pm->name }}" data-acc="{{ $pm->account_id ?? '' }}">{{ $pm->name }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="debit_account_id" id="pay_inst_debit_acc" value="{{ $paymentMethods->first()->account_id ?? '' }}">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">البيان *</label>
                                <input type="text" name="notes" id="pay_inst_notes" required class="w-full rounded-xl border px-3.5 py-2 text-xs">
                            </div>
                            <div class="flex justify-end gap-2 pt-3 border-t">
                                <button type="button" onclick="closeModal('payInstallmentModal')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">إصدار السند وتحديث الدفعة</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

                <!-- نوافذ السندات المرتبطة بالدفعة (مفصولة خارج الجدول تماماً لمنع انكسار الأعمدة) -->
                @foreach(($installments ?? collect()) as $inst)
                    @php
                        $linkedVoucherIds = json_decode($inst->voucher_ids_json ?? '[]', true) ?: [];
                        if ($inst->last_voucher_id && !in_array($inst->last_voucher_id, $linkedVoucherIds)) {
                            $linkedVoucherIds[] = $inst->last_voucher_id;
                        }
                        $linkedVouchers = $vouchers->whereIn('id', $linkedVoucherIds)->values();
                    @endphp
                    <div id="instVouchersModal_{{ $inst->id }}" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200">
                            <div class="flex items-center justify-between pb-3 border-b mb-4">
                                <h3 class="font-bold text-base text-slate-900">🧾 السندات المرتبطة بالدفعة #{{ $inst->installment_number }} — {{ $inst->tenant_name }}</h3>
                                <button type="button" onclick="closeModal('instVouchersModal_{{ $inst->id }}')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                            </div>
                            <table class="w-full text-xs text-right border">
                                <thead class="bg-slate-100">
                                    <tr>
                                        <th class="py-2.5 px-3">رقم السند</th>
                                        <th class="py-2.5 px-3">التاريخ</th>
                                        <th class="py-2.5 px-3">المبلغ المسدد</th>
                                        <th class="py-2.5 px-3">طريقة الدفع</th>
                                        <th class="py-2.5 px-3">البيان</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @forelse($linkedVouchers as $lv)
                                        <tr>
                                            <td class="py-2.5 px-3 font-mono font-bold">{{ $lv->voucher_number }}</td>
                                            <td class="py-2.5 px-3 font-mono">{{ $lv->voucher_date }}</td>
                                            <td class="py-2.5 px-3 font-mono font-bold text-emerald-700">{{ number_format($lv->amount, 2) }} ر.س</td>
                                            <td class="py-2.5 px-3">{{ \App\Http\Controllers\CompanyErpController::translatePaymentMethod($lv->payment_method) }}</td>
                                            <td class="py-2.5 px-3">{{ $lv->notes }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="py-6 text-center text-slate-400">لا توجد سندات مسجلة لهذه الدفعة بعد.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach

                <!-- نافذة تسجيل سداد كلي أو جزئي للدفعة -->
                <div id="payInstallmentModal" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                        <div class="flex items-center justify-between pb-3 border-b mb-4">
                            <h3 class="font-bold text-base text-slate-900">💰 تسجيل سداد دفعة (كلي أو جزئي)</h3>
                            <button type="button" onclick="closeModal('payInstallmentModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                        </div>
                        <form action="{{ route('office.installments.pay') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="installment_id" id="pay_inst_id">
                            <div class="p-3 rounded-xl bg-slate-50 border text-xs font-bold text-slate-800" id="pay_inst_tenant_label"></div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">المبلغ المسدد الآن (ر.س) *</label>
                                <input type="number" step="0.01" name="amount" id="pay_inst_amount" required class="w-full rounded-xl border px-3.5 py-2 text-sm font-mono font-bold text-emerald-700">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">طريقة الدفع (تربط حساب الصندوق/البنك تلقائياً) *</label>
                                <select name="payment_method" onchange="const acc=this.options[this.selectedIndex].getAttribute('data-acc'); if(acc) document.getElementById('pay_inst_debit_acc').value=acc;" class="w-full rounded-xl border px-3 py-2 text-xs bg-white font-bold">
                                    @foreach($paymentMethods as $pm)
                                        <option value="{{ $pm->name }}" data-acc="{{ $pm->account_id ?? '' }}">{{ $pm->name }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="debit_account_id" id="pay_inst_debit_acc" value="{{ $paymentMethods->first()->account_id ?? '' }}">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">البيان *</label>
                                <input type="text" name="notes" id="pay_inst_notes" required class="w-full rounded-xl border px-3.5 py-2 text-xs">
                            </div>
                            <div class="flex justify-end gap-2 pt-3 border-t">
                                <button type="button" onclick="closeModal('payInstallmentModal')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                                <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">إصدار السند وتحديث الدفعة</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- نافذة تقرير أعمار الديون العقارية -->
                <div id="agingReceivablesModal" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-4xl w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto space-y-5">
                        <div class="flex items-center justify-between border-b pb-3">
                            <div>
                                <h3 class="font-bold text-base text-slate-900">⏳ تقرير أعمار الديون والمتأخرات العقارية</h3>
                                <p class="text-xs text-slate-500">تصنيف الدفعات المتأخرة حسب عدد أيام التأخير</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="exportTableToCSV('agingReceivablesTable', 'أعمار_الديون_العقارية.xlsx')" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 text-white text-xs font-bold">📊 Excel</button>
                                <button type="button" onclick="exportElementToPDF('agingPrintableArea', 'أعمار_الديون_العقارية.pdf')" class="px-3.5 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-bold">📥 PDF</button>
                                <button type="button" onclick="closeModal('agingReceivablesModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                            </div>
                        </div>
                        <div id="agingPrintableArea" class="space-y-4">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-center">
                                <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200">
                                    <div class="text-xs font-bold text-amber-900">1 - 30 يوم</div>
                                    <div class="text-lg font-extrabold font-mono text-amber-700 mt-1">{{ number_format($agingBuckets['b_30'] ?? 0, 2) }} ر.س</div>
                                </div>
                                <div class="p-3.5 rounded-2xl bg-orange-50 border border-orange-200">
                                    <div class="text-xs font-bold text-orange-900">31 - 60 يوم</div>
                                    <div class="text-lg font-extrabold font-mono text-orange-700 mt-1">{{ number_format($agingBuckets['b_60'] ?? 0, 2) }} ر.س</div>
                                </div>
                                <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200">
                                    <div class="text-xs font-bold text-rose-900">61 - 90 يوم</div>
                                    <div class="text-lg font-extrabold font-mono text-rose-700 mt-1">{{ number_format($agingBuckets['b_90'] ?? 0, 2) }} ر.س</div>
                                </div>
                                <div class="p-3.5 rounded-2xl bg-slate-900 text-white">
                                    <div class="text-xs font-bold text-rose-300">أكثر من 90 يوم</div>
                                    <div class="text-lg font-extrabold font-mono text-white mt-1">{{ number_format($agingBuckets['b_plus'] ?? 0, 2) }} ر.س</div>
                                </div>
                            </div>
                            <table class="w-full text-xs text-right border" id="agingReceivablesTable">
                                <thead class="bg-slate-100">
                                    <tr>
                                        <th class="py-2.5 px-3">المستأجر</th>
                                        <th class="py-2.5 px-3">الجوال</th>
                                        <th class="py-2.5 px-3">العقار / الوحدة</th>
                                        <th class="py-2.5 px-3">تاريخ الاستحقاق</th>
                                        <th class="py-2.5 px-3">أيام التأخير</th>
                                        <th class="py-2.5 px-3">المبلغ المتأخر</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @forelse(($agingBuckets['rows'] ?? []) as $ar)
                                        <tr>
                                            <td class="py-2.5 px-3 font-bold">{{ $ar->tenant_name }}</td>
                                            <td class="py-2.5 px-3 font-mono" dir="ltr">{{ $ar->tenant_phone }}</td>
                                            <td class="py-2.5 px-3">{{ $ar->property_title }} {{ $ar->unit_number ? '- ' . $ar->unit_number : '' }}</td>
                                            <td class="py-2.5 px-3 font-mono">{{ $ar->due_date }}</td>
                                            <td class="py-2.5 px-3 font-mono font-bold text-rose-600">{{ $ar->days_late }} يوم</td>
                                            <td class="py-2.5 px-3 font-mono font-extrabold text-rose-700">{{ number_format($ar->remaining_amount, 2) }} ر.س</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="py-6 text-center text-slate-400">لا توجد ديون متأخرة حالياً.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            <!-- ========================================================================= -->
            <!-- تبويب مولد الإنذارات والخطابات القانونية الذكي (متعدد النماذج وقابل للتعديل الكامل) -->
            <!-- ========================================================================= -->
            @if($tab == 'legal_notices')
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-sm">
                        <h3 class="font-bold text-base text-slate-900">⚖️ مولد الإنذارات والإشعارات القانونية الذكي</h3>
                        <p class="text-xs text-slate-500">اختر العقد ونوع الخطاب، وحدد المهلة بالأيام ونوع المخالفة، كما يمكنك التعديل مباشرة على نص الخطاب قبل تحميله PDF أو إرساله واتساب</p>

                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">1. اختر العقد / المستأجر *</label>
                                <select id="legal_contract_select" onchange="generateSmartLegalNotice()" class="w-full rounded-xl border px-3 py-2.5 text-xs bg-white font-bold">
                                    <option value="">-- اختر العقد --</option>
                                    @foreach($contracts as $cnt)
                                        <option value="{{ $cnt->id }}"
                                                data-tenant="{{ $cnt->tenant_name }}"
                                                data-phone="{{ $cnt->tenant_phone }}"
                                                data-prop="{{ $cnt->property_title }} {{ $cnt->unit_number ? '- وحدة ' . $cnt->unit_number : '' }}"
                                                data-contract="{{ $cnt->contract_number }}"
                                                data-ejar="{{ $cnt->ejar_contract_number ?: $cnt->contract_number }}"
                                                data-rent="{{ number_format($cnt->total_rent_amount, 2) }}"
                                                data-end="{{ $cnt->end_date }}">
                                            {{ $cnt->tenant_name }} — {{ $cnt->property_title }} ({{ $cnt->contract_number }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">2. اختر نوع الإنذار / الإشعار القانوني *</label>
                                <select id="legal_notice_type" onchange="generateSmartLegalNotice()" class="w-full rounded-xl border px-3 py-2.5 text-xs bg-white font-bold">
                                    <option value="payment_warning">1. إنذار نهائي بالسداد قبل الرفع لمحكمة التنفيذ (ناجز)</option>
                                    <option value="contract_violation">2. إنذار بمخالفة شروط عقد الإيجار الموحد</option>
                                    <option value="eviction_notice">3. إشعار بعدم الرغبة في تجديد العقد وطلب إخلاء الوحدة</option>
                                    <option value="utility_warning">4. إشعار بضرورة سداد وتصفية فواتير الكهرباء والمياه</option>
                                    <option value="damage_notice">5. إشعار بإصلاح تلفيات أو أضرار في العين المؤجرة</option>
                                    <option value="custom_notice">6. خطاب / إشعار قانوني حر ومخصص</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-emerald-800 mb-1">⏳ مدة المهلة الممنوحة (بالأيام)</label>
                                    <input type="number" min="1" max="365" id="legal_grace_days" value="5" oninput="generateSmartLegalNotice()"
                                           placeholder="مثال: 3 أو 10 أيام" class="w-full rounded-xl border px-3 py-2 text-xs font-mono font-bold bg-emerald-50/40">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">💰 المبلغ المطالب به (إن وجد)</label>
                                    <input type="text" id="legal_custom_amount" oninput="generateSmartLegalNotice()" placeholder="اختياري..."
                                           class="w-full rounded-xl border px-3 py-2 text-xs font-mono font-bold">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-rose-800 mb-1">📝 نوع المخالفة / تفاصيل السبب الإضافية (تُدرج داخل النص)</label>
                                <input type="text" id="legal_violation_details" oninput="generateSmartLegalNotice()"
                                       placeholder="مثال: التأجير من الباطن / إحداث تعديلات بدون إذن / إزعاج الجيران..."
                                       class="w-full rounded-xl border border-rose-300 px-3 py-2 text-xs bg-rose-50/30 font-semibold">
                            </div>

                            <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-[11px] text-amber-900 font-semibold">
                                💡 يمكنك التعديل بحرية تامة على العنوان أو نص الخطاب مباشرة داخل الورقة المقابلة قبل التحميل أو الإرسال.
                            </div>

                            <div class="flex flex-wrap gap-2 pt-2">
                                <button type="button" onclick="exportLegalPaperToPDF()" class="flex-1 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold">
                                    📥 تحميل الخطاب PDF
                                </button>
                                <button type="button" onclick="sendLegalPaperWhatsappPDF()" class="flex-1 py-2.5 rounded-xl bg-emerald-600 text-white text-xs font-bold">
                                    💬 إرسال واتساب (ملف PDF أصلي)
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-7">
                        <div id="legalNoticePaperArea" class="bg-white rounded-3xl border-2 border-slate-900 p-8 space-y-5 text-slate-900 shadow-sm">
                            <div class="flex items-start justify-between border-b-2 border-slate-900 pb-4">
                                <div>
                                    <h2 class="text-lg font-extrabold">{{ $office->name }}</h2>
                                    <p class="text-xs text-slate-500">الإدارة القانونية وإدارة الأملاك | رخصة فال: {{ $office->fal_license ?: '-' }}</p>
                                </div>
                                <div class="text-left text-xs font-mono">
                                    <div>التاريخ: {{ date('Y-m-d') }}</div>
                                    <div>المرجع: LGL-{{ date('Ymd') }}</div>
                                </div>
                            </div>

                            <div class="text-center">
                                <input type="text" id="lgl_title_input" value="إشعار وإنذار قانوني رسمي"
                                       class="w-full text-center text-base font-extrabold underline bg-transparent border-b border-dashed border-slate-300 focus:border-slate-900 py-1">
                            </div>

                            <div>
                                <textarea id="lgl_body_textarea" rows="11"
                                          class="w-full text-sm leading-relaxed font-medium text-slate-800 bg-slate-50/50 rounded-xl border border-slate-200 p-4 focus:bg-white">يرجى اختيار العقد من القائمة الجانبية لتوليد النص القانوني المعتمد تلقائياً، ويمكنك التعديل على أي كلمة هنا بحرية تامة...</textarea>
                            </div>

                            <div class="pt-6 border-t border-dashed border-slate-300 flex justify-between text-xs font-bold">
                                <div>الإدارة القانونية — {{ $office->name }}</div>
                                <div class="text-emerald-700">ختم المكتب المعتمد ✔</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- عرض تنبيهات كاشف الشذوذ المحاسبي في صفحة سجل التتبع -->
            @if($tab == 'activity_logs' && isset($anomalyAlerts) && $anomalyAlerts->count() > 0)
                <div class="bg-rose-50 border-2 border-rose-200 rounded-3xl p-5 space-y-3 mt-4">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-sm text-rose-900">🚨 كاشف الشذوذ والمخاطر المحاسبية ({{ $anomalyAlerts->count() }} تنبيه مرصود)</h4>
                        <a href="{{ route('office.dashboard', ['tab' => 'whatsapp']) }}" class="text-xs font-bold text-rose-700 underline">⚙️ تعديل شروط التنبيه</a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5">
                        @foreach($anomalyAlerts->take(10) as $al)
                            <div class="p-3 rounded-xl bg-white border border-rose-200 flex items-start justify-between gap-2 text-xs">
                                <div>
                                    <div class="font-bold text-rose-800">⚠️ {{ $al->type }}</div>
                                    <div class="text-slate-700 mt-0.5">{{ $al->details }}</div>
                                </div>
                                <span class="font-mono text-[10px] text-slate-400 shrink-0" dir="ltr">{{ $al->time }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- نافذة إصدار سندات القبض المجمعة (5 خانات فقط + زر إضافة خانة جديدة + تجاهل الفارغ) -->
            <div id="batchReceiptsModal" class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                <div class="bg-white rounded-3xl max-w-4xl w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between pb-3 border-b mb-4">
                        <div>
                            <h3 class="font-bold text-base text-slate-900">⚡ إصدار سندات قبض مجمّعة (Batch Receipts)</h3>
                            <p class="text-xs text-slate-500">اختر المستأجر وأدخل المبلغ في الخانات التي تحتاجها فقط (أي خانة تُترك فارغة يتم تجاهلها تلقائياً دون أي ضرر)</p>
                        </div>
                        <button type="button" onclick="closeModal('batchReceiptsModal')" class="w-8 h-8 rounded-full bg-slate-100 font-bold">✕</button>
                    </div>

                    <form action="{{ route('office.vouchers.batch_store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border">
                            <span class="text-xs font-bold text-slate-700">📅 تاريخ إصدار السندات المجمعة:</span>
                            <input type="date" name="voucher_date" value="{{ date('Y-m-d') }}" required class="rounded-xl border px-3 py-1.5 text-xs bg-white font-mono">
                        </div>

                        <div class="overflow-x-auto border rounded-2xl">
                            <table class="w-full text-xs text-right">
                                <thead class="bg-slate-100 border-b">
                                    <tr>
                                        <th class="py-2.5 px-3 w-10 text-center">#</th>
                                        <th class="py-2.5 px-3">اختر المستأجر (اختياري للخانات الفارغة)</th>
                                        <th class="py-2.5 px-3 w-36">المبلغ المحصل (ر.س)</th>
                                        <th class="py-2.5 px-3 w-44">طريقة الدفع</th>
                                        <th class="py-2.5 px-3">البيان (اختياري)</th>
                                        <th class="py-2.5 px-2 w-12 text-center">حذف</th>
                                    </tr>
                                </thead>
                                <tbody id="batchReceiptsRowsContainer" class="divide-y">
                                    @for($i = 1; $i <= 5; $i++)
                                        <tr class="batch-receipt-row hover:bg-slate-50">
                                            <td class="py-2.5 px-3 text-center font-mono font-bold text-slate-400 batch-row-index">{{ $i }}</td>
                                            <td class="py-2.5 px-3">
                                                <select name="tenant_ids[]" class="w-full rounded-lg border px-2.5 py-1.5 text-xs bg-white font-bold">
                                                    <option value="">-- اختر المستأجر (أو اتركه فارغاً) --</option>
                                                    @foreach($tenants as $tn)
                                                        <option value="{{ $tn->id }}">{{ $tn->name }} ({{ $tn->account_code ?: 'حساب تلقائي' }})</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="py-2.5 px-3">
                                                <input type="number" step="0.01" name="amounts[]" placeholder="0.00" class="w-full rounded-lg border px-2.5 py-1.5 font-mono font-bold text-emerald-700 bg-white">
                                            </td>
                                            <td class="py-2.5 px-3">
                                                <select name="payment_methods[]" class="w-full rounded-lg border px-2 py-1.5 text-xs bg-white font-semibold">
                                                    @foreach($paymentMethods as $pm)
                                                        <option value="{{ $pm->name }}">{{ $pm->name }} {{ $pm->account_code ? '(' . $pm->account_code . ')' : '' }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="py-2.5 px-3">
                                                <input type="text" name="batch_notes[]" placeholder="دفعة إيجار..." class="w-full rounded-lg border px-2.5 py-1.5 bg-white">
                                            </td>
                                            <td class="py-2.5 px-2 text-center">
                                                <button type="button" onclick="removeBatchReceiptRow(this)" class="px-2 py-1 rounded bg-red-50 hover:bg-red-600 text-red-600 hover:text-white text-[11px] font-bold transition" title="حذف الخانة">✕</button>
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>

                        <button type="button" onclick="addMoreBatchReceiptRow()"
                                class="w-full py-2.5 rounded-2xl border-2 border-dashed border-slate-300 hover:border-slate-900 text-slate-700 hover:text-slate-900 text-xs font-bold transition">
                            + إضافة خانة جديدة أخرى
                        </button>

                        <div class="flex justify-end gap-2 pt-3 border-t">
                            <button type="button" onclick="closeModal('batchReceiptsModal')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold">إلغاء</button>
                            <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">⚡ اعتماد وإصدار السندات المعبأة فقط</button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                <script>
                // 1. التبديل بين صفحة جدولة الدفعات وصفحة أعمار الديون الكاملة
                function toggleInstallmentsAgingPage(viewMode) {
                    const instSec = document.getElementById('mainInstallmentsPageSection');
                    const agingSec = document.getElementById('agingReceivablesPageSection');
                    if (!instSec || !agingSec) return;
                    if (viewMode === 'aging') {
                        instSec.classList.add('hidden');
                        agingSec.classList.remove('hidden');
                        filterAgingPageTable();
                    } else {
                        agingSec.classList.add('hidden');
                        instSec.classList.remove('hidden');
                    }
                }

                // 2. فلترة جدول الدفعات المستحقة
                function filterInstallmentsTable() {
                    const q = (document.getElementById('inst_search_name')?.value || '').trim().toLowerCase();
                    const st = document.getElementById('inst_status_filter')?.value || 'unpaid';
                    const from = document.getElementById('inst_from_date')?.value || '';
                    const to = document.getElementById('inst_to_date')?.value || '';

                    let visibleCount = 0;
                    document.querySelectorAll('.inst-row').forEach(r => {
                        const hay = (r.getAttribute('data-search') || '').toLowerCase();
                        const rDate = r.getAttribute('data-date') || '';
                        const rStatus = r.getAttribute('data-status') || '';
                        let show = true;
                        if (q && !hay.includes(q)) show = false;
                        if (st === 'unpaid' && rStatus === 'paid') show = false;
                        else if (st === 'partial' && rStatus !== 'partial' && rStatus !== 'partial_overdue') show = false;
                        else if (st !== 'all' && st !== 'unpaid' && st !== 'partial' && rStatus !== st) show = false;
                        if (from && rDate < from) show = false;
                        if (to && rDate > to) show = false;

                        r.classList.toggle('hidden', !show);
                        if (show) visibleCount++;
                    });

                    const noMatchRow = document.getElementById('instNoMatchRow');
                    if (noMatchRow) {
                        noMatchRow.classList.toggle('hidden', visibleCount > 0);
                    }
                }

                // 3. فلترة صفحة أعمار الديون (بالفئات السريعة أو من يوم كذا إلى يوم كذا مثل 1-70 + بالاسم)
                function setAgingQuickBucket(bucketVal, minD, maxD) {
                    const sel = document.getElementById('aging_bucket_select');
                    const minInp = document.getElementById('aging_min_days');
                    const maxInp = document.getElementById('aging_max_days');
                    if (sel) {
                        sel.value = bucketVal;
                        sel.dispatchEvent(new Event('change'));
                    }
                    if (minInp) minInp.value = minD !== '' ? minD : '';
                    if (maxInp) maxInp.value = maxD !== '' ? maxD : '';
                    filterAgingPageTable();
                }

                function onAgingBucketSelectChange(val) {
                    const minInp = document.getElementById('aging_min_days');
                    const maxInp = document.getElementById('aging_max_days');
                    if (val === 'all') { minInp.value = ''; maxInp.value = ''; }
                    else if (val === '1_30') { minInp.value = 1; maxInp.value = 30; }
                    else if (val === '31_60') { minInp.value = 31; maxInp.value = 60; }
                    else if (val === '61_90') { minInp.value = 61; maxInp.value = 90; }
                    else if (val === '90_plus') { minInp.value = 91; maxInp.value = 99999; }
                    filterAgingPageTable();
                }

                function resetAgingFilters() {
                    if (document.getElementById('aging_search_name')) document.getElementById('aging_search_name').value = '';
                    setAgingQuickBucket('all', '', '');
                }

                function filterAgingPageTable() {
                    const q = (document.getElementById('aging_search_name')?.value || '').trim().toLowerCase();
                    const minVal = document.getElementById('aging_min_days')?.value;
                    const maxVal = document.getElementById('aging_max_days')?.value;
                    const minDays = (minVal !== '' && minVal !== undefined) ? parseInt(minVal, 10) : null;
                    const maxDays = (maxVal !== '' && maxVal !== undefined) ? parseInt(maxVal, 10) : null;

                    let totalSum = 0;
                    document.querySelectorAll('.aging-row').forEach(r => {
                        const hay = (r.getAttribute('data-search') || '').toLowerCase();
                        const days = parseInt(r.getAttribute('data-days') || '0', 10);
                        const amt = parseFloat(r.getAttribute('data-amount') || '0');
                        let show = true;
                        if (q && !hay.includes(q)) show = false;
                        if (minDays !== null && !isNaN(minDays) && days < minDays) show = false;
                        if (maxDays !== null && !isNaN(maxDays) && days > maxDays) show = false;

                        r.classList.toggle('hidden', !show);
                        if (show) totalSum += amt;
                    });

                    const totCell = document.getElementById('agingFilteredTotalCell');
                    if (totCell) {
                        totCell.innerText = totalSum.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ر.س';
                    }
                }

                function openPayInstallmentModal(id, tenantName, remAmt, notesText) {
                    document.getElementById('pay_inst_id').value = id;
                    document.getElementById('pay_inst_tenant_label').innerText = 'المستأجر: ' + tenantName + ' | المتبقي من الدفعة: ' + remAmt + ' ر.س';
                    document.getElementById('pay_inst_amount').value = remAmt;
                    document.getElementById('pay_inst_notes').value = 'سداد ' + notesText;
                    openModal('payInstallmentModal');
                }

                // 4. دوال سندات القبض المجمعة (5 خانات + إضافة خانة)
                function addMoreBatchReceiptRow() {
                    const container = document.getElementById('batchReceiptsRowsContainer');
                    if (!container) return;
                    const firstRow = container.querySelector('.batch-receipt-row');
                    if (!firstRow) return;
                    const clone = firstRow.cloneNode(true);
                    clone.querySelectorAll('input').forEach(inp => inp.value = '');
                    const tenantSel = clone.querySelector('select[name="tenant_ids[]"]');
                    if (tenantSel) tenantSel.value = '';
                    container.appendChild(clone);
                    renumberBatchReceiptRows();
                }

                function removeBatchReceiptRow(btn) {
                    const container = document.getElementById('batchReceiptsRowsContainer');
                    const rows = container.querySelectorAll('.batch-receipt-row');
                    if (rows.length <= 1) {
                        rows[0].querySelectorAll('input').forEach(inp => inp.value = '');
                        const sel = rows[0].querySelector('select[name="tenant_ids[]"]');
                        if (sel) sel.value = '';
                        return;
                    }
                    btn.closest('.batch-receipt-row').remove();
                    renumberBatchReceiptRows();
                }

                function renumberBatchReceiptRows() {
                    document.querySelectorAll('#batchReceiptsRowsContainer .batch-row-index').forEach((td, idx) => {
                        td.innerText = idx + 1;
                    });
                }

                // 5. التصنيف التلقائي للمصروفات بمجرد كتابة البيان
                function autoClassifyExpenseByNotes(text) {
                    const t = (text || '').trim();
                    const debitSel = document.querySelector('#createOfficeVoucherModal select[name="debit_account_id"]');
                    const badge = document.getElementById('autoExpenseClassifierBadge');
                    if (!debitSel || t.length < 3) return;

                    let targetKeyword = '';
                    if (/صيان|سباك|كهرب|مكيف|دهان|مضخ|عطل|ترميم|نظاف|مصعد/.test(t)) targetKeyword = 'صيانة';
                    else if (/راتب|رواتب|مكافأ|موظف|بدل/.test(t)) targetKeyword = 'رواتب';

                    if (targetKeyword) {
                        Array.from(debitSel.options).forEach(opt => {
                            if (opt.text.includes(targetKeyword)) {
                                setSelectValueAndRefreshUI(debitSel, opt.value);
                                if (badge) badge.classList.remove('hidden');
                            }
                        });
                    }
                }

                // 6. مولد الإنذارات القانونية الذكي (6 قوالب + تعديل أيام المهلة ونوع المخالفة والنص الحر)
                let currentLegalTenantPhone = '';
                function generateSmartLegalNotice() {
                    const sel = document.getElementById('legal_contract_select');
                    const type = document.getElementById('legal_notice_type')?.value || 'payment_warning';
                    const graceDays = document.getElementById('legal_grace_days')?.value || '5';
                    const violationText = (document.getElementById('legal_violation_details')?.value || '').trim();
                    const customAmt = (document.getElementById('legal_custom_amount')?.value || '').trim();

                    let tenant = '....................';
                    let prop = '....................';
                    let ejar = '............';
                    let rent = customAmt || '............';
                    let endDate = '............';

                    if (sel && sel.value) {
                        const opt = sel.options[sel.selectedIndex];
                        tenant = opt.getAttribute('data-tenant') || tenant;
                        currentLegalTenantPhone = opt.getAttribute('data-phone') || '';
                        prop = opt.getAttribute('data-prop') || prop;
                        ejar = opt.getAttribute('data-ejar') || ejar;
                        if (!customAmt) rent = opt.getAttribute('data-rent') || rent;
                        endDate = opt.getAttribute('data-end') || endDate;
                    }

                    const titleInp = document.getElementById('lgl_title_input');
                    const bodyArea = document.getElementById('lgl_body_textarea');
                    if (!titleInp || !bodyArea) return;

                    if (type === 'payment_warning') {
                        titleInp.value = 'إنذار نهائي بسداد مستحقات إيجارية متأخرة';
                        bodyArea.value = `المكرم المستأجر / ${tenant} حفظه الله\nالسلام عليكم ورحمة الله وبركاته،،،\n\nإشارة إلى عقد الإيجار الموحد رقم (${ejar}) والخاص بالوحدة العقارية (${prop})، نفيدكم بأنه قد حلت عليكم مستحقات إيجارية متأخرة واجبة السداد بمبلغ وقدره (${rent} ريال سعودي).${violationText ? '\nملاحظات إضافية: ' + violationText : ''}\n\nوعليه نمهلكم مدة أقصاها (${graceDays} أيام) من تاريخ هذا الإنذار لسداد كامل المبلغ المستحق، وفي حال عدم السداد سيتم رفع العقد فوراً كسند تنفيذي لدى محكمة التنفيذ عبر بوابة (ناجز) واتخاذ الإجراءات النظامية لإخلاء العين المؤجرة والمطالبة القضائية.\n\nشاكرين تعاونكم وسرعة تجاوبكم.`;
                    } else if (type === 'contract_violation') {
                        titleInp.value = 'إنذار رسمي بمخالفة بنود عقد الإيجار';
                        const vDesc = violationText || 'مخالفة شروط والتزامات العقد المبرم';
                        bodyArea.value = `المكرم المستأجر / ${tenant} حفظه الله\nالسلام عليكم ورحمة الله وبركاته،،،\n\nإشارة إلى عقد الإيجار رقم (${ejar}) للوحدة العقارية (${prop})، فقد تبين لإدارة الأملاك وجود المخالفة التالية:\n(${vDesc}).\n\nوعليه ننذركم بضرورة إزالة المخالفة وتصحيح الوضع خلال مهلة أقصاها (${graceDays} أيام) من تاريخه، وفي حال عدم الالتزام سيتم اتخاذ الإجراءات النظامية لفسخ العقد وإخلاء الوحدة والمطالبة بالتعويض النظامي.`;
                    } else if (type === 'eviction_notice') {
                        titleInp.value = 'إشعار رسمي بعدم الرغبة في تجديد العقد وطلب إخلاء الوحدة';
                        bodyArea.value = `المكرم المستأجر / ${tenant} حفظه الله\nالسلام عليكم ورحمة الله وبركاته،،،\n\nإشارة إلى عقد الإيجار رقم (${ejar}) والخاص بالوحدة العقارية (${prop}) والذي ينتهي بتاريخ (${endDate}).\n\nنود إشعاركم رسمياً بعدم رغبة المالك في تجديد العقد لفترة أخرى، ونأمل منكم التكرم بتجهيز الوحدة للإخلاء وتسليم المفاتيح وتصفية فواتير الخدمات خلال (${graceDays} أيام) / في موعد أقصاه تاريخ انتهاء العقد المذكور أعلاه.${violationText ? '\nملاحظات: ' + violationText : ''}\n\nوتقبلوا خالص التحية والتقدير.`;
                    } else if (type === 'utility_warning') {
                        titleInp.value = 'إشعار بضرورة سداد وتصفية فواتير الكهرباء والمياه';
                        bodyArea.value = `المكرم المستأجر / ${tenant} حفظه الله\nالسلام عليكم ورحمة الله وبركاته،،،\n\nإشارة إلى الوحدة العقارية المؤجرة لكم (${prop}) بموجب العقد رقم (${ejar})، نود تنبيهكم بوجود مبالغ مستحقة على فواتير الخدمات (الكهرباء / المياه) الخاصة بالوحدة${customAmt ? ' بمبلغ (' + customAmt + ' ر.س)' : ''}.${violationText ? '\nتفاصيل الفواتير: ' + violationText : ''}\n\nنأمل منكم المبادرة بالسداد وتزويد المكتب بإيصال السداد خلال (${graceDays} أيام) من تاريخه تجنباً لفصل الخدمة أو خصمها من التأمين.`;
                    } else if (type === 'damage_notice') {
                        titleInp.value = 'إشعار بإصلاح تلفيات وأضرار في العين المؤجرة';
                        const dmg = violationText || 'وجود تلفيات ناتجة عن سوء الاستخدام';
                        bodyArea.value = `المكرم المستأجر / ${tenant} حفظه الله\nالسلام عليكم ورحمة الله وبركاته،،،\n\nبعد معاينة الوحدة العقارية (${prop}) المؤجرة لكم بموجب العقد (${ejar})، تبين وجود الملاحظات والتلفيات التالية:\n(${dmg}).\n\nنأمل منكم إصلاح التلفيات وإعادة الحال إلى ما كان عليه أو سداد تكلفة الإصلاح البالغة (${rent} ر.س) خلال مهلة (${graceDays} أيام) من تاريخه.`;
                    } else {
                        titleInp.value = 'إشعار وخطاب رسمي للمستأجر';
                        bodyArea.value = `المكرم المستأجر / ${tenant} حفظه الله\nالسلام عليكم ورحمة الله وبركاته،،،\n\nإشارة إلى الوحدة العقارية (${prop}) والعقد رقم (${ejar}):\n${violationText || 'اكتب نص الخطاب المخصص هنا...'}\n\nنأمل منكم التجاوب خلال (${graceDays} أيام) من تاريخه.\nوتقبلوا خالص التحية.`;
                    }
                }

                // بناء نسخة نظيفة للطباعة من الخطاب القانوني المعدل
                function buildCleanLegalElementForPdf() {
                    const orig = document.getElementById('legalNoticePaperArea');
                    const clone = orig.cloneNode(true);
                    const titleVal = document.getElementById('lgl_title_input')?.value || '';
                    const bodyVal = document.getElementById('lgl_body_textarea')?.value || '';
                    const cInput = clone.querySelector('#lgl_title_input');
                    if (cInput) {
                        const h3 = document.createElement('h3');
                        h3.style.cssText = 'font-size:16px; font-weight:bold; text-decoration:underline; text-align:center;';
                        h3.innerText = titleVal;
                        cInput.replaceWith(h3);
                    }
                    const cArea = clone.querySelector('#lgl_body_textarea');
                    if (cArea) {
                        const div = document.createElement('div');
                        div.style.cssText = 'font-size:13px; line-height:1.9; white-space:pre-line; padding:12px 0; min-height:220px;';
                        div.innerText = bodyVal;
                        cArea.replaceWith(div);
                    }
                    return clone;
                }

                function exportLegalPaperToPDF() {
                    const cleanNode = buildCleanLegalElementForPdf();
                    const prepared = prepareArabicPdfClone(cleanNode, '');
                    html2pdf().set({
                        margin: 10,
                        filename: 'خطاب_إنذار_قانوني.pdf',
                        image: { type: 'jpeg', quality: 1.0 },
                        html2canvas: { scale: 2, useCORS: true, scrollY: 0, letterRendering: false },
                        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                    }).from(prepared).save();
                }

                function sendLegalPaperWhatsappPDF() {
                    let phone = currentLegalTenantPhone || prompt('أدخل رقم جوال المستأجر لإرسال ملف الـ PDF الأصلي على الواتساب:');
                    if (!phone) return;
                    const cleanNode = buildCleanLegalElementForPdf();
                    const prepared = prepareArabicPdfClone(cleanNode, '');
                    html2pdf().set({
                        margin: 10,
                        filename: 'إنذار_قانوني_رسمي.pdf',
                        image: { type: 'jpeg', quality: 1.0 },
                        html2canvas: { scale: 2, useCORS: true, scrollY: 0, letterRendering: false },
                        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                    }).from(prepared).outputPdf('datauristring').then(function (pdfBase64) {
                        fetch('{{ route('erp.whatsapp.send_raw_pdf') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ phone: phone, pdf_base64: pdfBase64, filename: 'إنذار_قانوني_رسمي.pdf', doc_type: 'legal' })
                        }).then(r => r.json()).then(d => alert(d.message || 'تم الإرسال'));
                    });
                }
            </script>
        </main>
    </div>

<style>
    tr, td, th, .rounded-2xl, .rounded-3xl { page-break-inside: avoid !important; break-inside: avoid !important; }
    thead { display: table-header-group; }
    tfoot { display: table-footer-group; }
</style>

<script>
    function prepareArabicPdfClone(sourceElement, docTitle = '') {
        const clone = sourceElement.cloneNode(true);
        clone.querySelectorAll('button, .no-print, .no-export-pdf, input[type="text"], input[type="date"], select').forEach(el => {
            if (el.tagName === 'BUTTON' || el.classList.contains('no-print') || el.classList.contains('no-export-pdf')) {
                el.remove();
            }
        });

        const container = document.createElement('div');
        container.dir = 'rtl';
        container.lang = 'ar';
        container.style.cssText = 'background:#ffffff; color:#0f172a; padding:15px; width:1000px; font-family: Tahoma, Arial, "Segoe UI", sans-serif !important; letter-spacing: 0px !important; word-spacing: normal !important; direction: rtl !important; text-align: right !important;';

        const styleTag = document.createElement('style');
        styleTag.innerHTML = `
            * {
                font-family: Tahoma, Arial, "Segoe UI", sans-serif !important;
                letter-spacing: 0px !important;
                word-spacing: normal !important;
                font-variant-ligatures: normal !important;
                box-sizing: border-box !important;
            }
            table { width: 100% !important; border-collapse: collapse !important; border: 1px solid #cbd5e1 !important; }
            th { background-color: #0f172a !important; color: #ffffff !important; padding: 8px 10px !important; font-size: 11px !important; border: 1px solid #334155 !important; text-align: right !important; }
            td { padding: 8px 10px !important; font-size: 11px !important; color: #0f172a !important; border: 1px solid #e2e8f0 !important; vertical-align: middle !important; text-align: right !important; }
            tr { page-break-inside: avoid !important; break-inside: avoid !important; }
        `;
        container.appendChild(styleTag);

        if (docTitle) {
            const h2 = document.createElement('h2');
            h2.style.cssText = 'font-size:16px; font-weight:bold; text-align:center; margin-bottom:14px; color:#0f172a;';
            h2.innerText = docTitle;
            container.appendChild(h2);
        }

        container.appendChild(clone);
        return container;
    }

    function exportElementToPDF(elementId, fileName) {
        const el = document.getElementById(elementId);
        if (!el) return;
        const cleanTitle = (fileName || '').replace(/\.pdf$/i, '').replace(/_/g, ' ');
        const preparedNode = prepareArabicPdfClone(el, cleanTitle);

        html2pdf().set({
            margin: [8, 8, 8, 8],
            filename: fileName || 'Document.pdf',
            image: { type: 'jpeg', quality: 1.0 },
            html2canvas: { scale: 2, useCORS: true, scrollY: 0, letterRendering: false },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' },
            pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
        }).from(preparedNode).save();
    }

    function exportGeneralPropertiesPDF() {
        const rows = Array.from(document.querySelectorAll('#officePropertiesMainTable tbody tr.office-prop-row'))
            .filter(r => r.style.display !== 'none');

        const wrapper = document.createElement('div');
        wrapper.dir = 'rtl';
        wrapper.lang = 'ar';
        wrapper.style.cssText = 'padding:16px; background:#ffffff; width:1020px; font-family: Tahoma, Arial, sans-serif !important; letter-spacing:0px !important; direction:rtl !important;';

        let tbodyHtml = '';
        rows.forEach(r => {
            const propTitle = (r.getAttribute('data-prop-title') || '') + ' (' + (r.getAttribute('data-prop-type') || '') + ')';
            const ownerName = r.getAttribute('data-prop-owner') || '';
            const deedArea  = 'صك: ' + (r.getAttribute('data-prop-deed') || '-') + ' | المساحة: ' + (r.getAttribute('data-prop-area') || '0') + ' م²';
            const address   = r.getAttribute('data-prop-address') || '-';
            const cells     = r.querySelectorAll('td');
            const specs     = cells[5] ? cells[5].innerText.replace(/\(عرض المرفق\)/g, '').replace(/\s+/g, ' ').trim() : '-';
            const vacantCnt = r.getAttribute('data-vacant-count') || '0';
            const unitsText = (cells[6] && cells[6].innerText.includes('عقار مستقل'))
                ? 'عقار مستقل'
                : ('إجمالي الوحدات (الشاغر: ' + vacantCnt + ')');

            tbodyHtml += `
                <tr style="page-break-inside: avoid; border-bottom: 1px solid #cbd5e1;">
                    <td style="padding:10px; font-weight:bold; border:1px solid #cbd5e1; font-family:Tahoma, Arial, sans-serif;">${propTitle}</td>
                    <td style="padding:10px; border:1px solid #cbd5e1; font-family:Tahoma, Arial, sans-serif;">${ownerName}</td>
                    <td style="padding:10px; border:1px solid #cbd5e1; font-family:Tahoma, Arial, sans-serif;">${deedArea}</td>
                    <td style="padding:10px; border:1px solid #cbd5e1; font-family:Tahoma, Arial, sans-serif;">${address}</td>
                    <td style="padding:10px; border:1px solid #cbd5e1; font-family:Tahoma, Arial, sans-serif;">${specs}</td>
                    <td style="padding:10px; font-weight:bold; text-align:center; border:1px solid #cbd5e1; font-family:Tahoma, Arial, sans-serif;">${unitsText}</td>
                </tr>
            `;
        });

        wrapper.innerHTML = `
            <div style="text-align:center; margin-bottom:14px; font-family:Tahoma, Arial, sans-serif;">
                <h2 style="font-size:16px; font-weight:bold; color:#0f172a; margin:0;">بيان العقارات والصكوك والوحدات التابعة لها</h2>
            </div>
            <table style="width:100%; border-collapse:collapse; font-size:11px; text-align:right; border:1px solid #0f172a; font-family:Tahoma, Arial, sans-serif;">
                <thead>
                    <tr style="background:#0f172a; color:#ffffff;">
                        <th style="padding:10px; border:1px solid #334155;">مسمى العقار والنوع</th>
                        <th style="padding:10px; border:1px solid #334155;">المالك</th>
                        <th style="padding:10px; border:1px solid #334155;">رقم الصك والمساحة</th>
                        <th style="padding:10px; border:1px solid #334155;">العنوان الوطني</th>
                        <th style="padding:10px; border:1px solid #334155;">المواصفات والمرفقات</th>
                        <th style="padding:10px; text-align:center; border:1px solid #334155;">الوحدات الداخلية</th>
                    </tr>
                </thead>
                <tbody>${tbodyHtml}</tbody>
            </table>
        `;

        html2pdf().set({
            margin: 8,
            filename: 'بيان_العقارات_والوحدات_العام.pdf',
            image: { type: 'jpeg', quality: 1.0 },
            html2canvas: { scale: 2, useCORS: true, scrollY: 0, letterRendering: false },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' },
            pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
        }).from(wrapper).save();

        if (typeof closeModal === 'function') closeModal('propertiesPdfExportChoiceModal');
    }

    function sendElementAsOriginalPdfWhatsapp(elementId, targetPhone, fileName, docType = 'general') {
        const el = document.getElementById(elementId);
        if (!el) return;
        let phone = targetPhone || prompt('أدخل رقم جوال المستلم لإرسال ملف الـ PDF الأصلي على الواتساب (مثال: 05XXXXXXXX):');
        if (!phone) return;

        const preparedNode = prepareArabicPdfClone(el, '');
        html2pdf().set({
            margin: 8,
            filename: fileName || 'Document.pdf',
            image: { type: 'jpeg', quality: 1.0 },
            html2canvas: { scale: 2, useCORS: true, scrollY: 0, letterRendering: false },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
            pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
        }).from(preparedNode).outputPdf('datauristring').then(function (pdfBase64) {
            fetch('{{ route('erp.whatsapp.send_raw_pdf') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ phone: phone, pdf_base64: pdfBase64, filename: fileName || 'Document.pdf', doc_type: docType })
            })
            .then(r => r.json())
            .then(d => alert(d.message || 'تم إرسال ملف الـ PDF بنجاح'))
            .catch(() => alert('حدث خطأ أثناء الاتصال ببوابة الواتساب.'));
        });
    }

    // =========================================================================
    // 4. المحرك الفوري لتحديث قوائم السندات والفواتير تلقائياً (مع دعم قوائم البحث المخصصة وبدون ملء المبلغ تلقائياً)
    // =========================================================================
    const officeSmartVoucherData = {
        tenantAccounts: {
            @if(isset($tenants))
                @foreach($tenants as $t) "{{ $t->id }}": "{{ $t->account_id ?? '' }}", @endforeach
            @endif
        },
        tenantNames: {
            @if(isset($tenants))
                @foreach($tenants as $t) "{{ $t->id }}": "{{ addslashes($t->name) }}", @endforeach
            @endif
        },
        ownerAccounts: {
            @if(isset($owners))
                @foreach($owners as $o) "{{ $o->id }}": "{{ $o->account_id ?? '' }}", @endforeach
            @endif
        },
        ownerNames: {
            @if(isset($owners))
                @foreach($owners as $o) "{{ $o->id }}": "{{ addslashes($o->name) }}", @endforeach
            @endif
        },
        tenantContracts: {
            @if(isset($contracts))
                @foreach($contracts as $c)
                    @php
                        $autoNote = 'دفعة إيجار - ' . $c->tenant_name . ' - ' . $c->property_title . ($c->unit_number ? ' (' . $c->unit_number . ')' : '');
                    @endphp
                    "{{ $c->tenant_id }}": {
                        owner_id: "{{ $c->owner_id ?? '' }}",
                        property_id: "{{ $c->property_id ?? '' }}",
                        unit_id: "{{ $c->property_unit_id ?? '' }}",
                        auto_note: "{{ addslashes($autoNote) }}"
                    },
                @endforeach
            @endif
        },
        propertyOwners: {
            @if(isset($properties))
                @foreach($properties as $p) "{{ $p->id }}": "{{ $p->owner_id ?? '' }}", @endforeach
            @endif
        },
        pmAccounts: {
            @if(isset($paymentMethods))
                @foreach($paymentMethods as $pm) "{{ $pm->name }}": "{{ $pm->account_id ?? '' }}", @endforeach
            @endif
        },
        defaultBankAccId: "{{ ($accounts->where('is_group', 0)->firstWhere('code', '1102')->id ?? ($accounts->where('is_group', 0)->first()->id ?? '')) }}",
        defaultCashAccId: "{{ ($accounts->where('is_group', 0)->firstWhere('code', '1101')->id ?? ($accounts->where('is_group', 0)->first()->id ?? '')) }}"
    };

    // دالة تحديث القيمة وإجبار أي قائمة مخصصة (بها أيقونة بحث 🔍) على التحديث البصري فوراً
    let isInternalAutoSyncing = false;
    function setSelectValueAndRefreshUI(selectEl, val) {
        if (!selectEl || val === undefined || val === null || val === '') return;
        const strVal = String(val);
        if (String(selectEl.value) === strVal) return;

        selectEl.value = strVal;
        let matchedText = '';
        Array.from(selectEl.options || []).forEach(opt => {
            if (String(opt.value) === strVal) {
                opt.selected = true;
                matchedText = opt.text;
            } else {
                opt.selected = false;
            }
        });

        // إذا كان هناك حقل بحث أو عنصر عرض مخصص بجانب القائمة نقوم بتحديث نصه فوراً
        const wrapper = selectEl.parentElement;
        if (wrapper && matchedText) {
            wrapper.querySelectorAll('input[type="text"]:not([name]), .custom-select-label, .selected-text').forEach(ui => {
                if (ui.tagName === 'INPUT') ui.value = matchedText;
                else ui.innerText = matchedText;
            });
        }

        // إطلاق حدث التغيير ليتعرف عليه أي سكربت قوائم مخصص
        selectEl.dispatchEvent(new Event('change', { bubbles: true }));
    }

    // البحث الذكي عن رقم الحساب في القائمة إذا لم يكن مخزناً مسبقاً
    function findAccountOptionByKeyword(selectEl, keyword, fallbackCode) {
        if (!selectEl) return '';
        if (keyword) {
            const foundByName = Array.from(selectEl.options).find(o => o.text.includes(keyword));
            if (foundByName) return foundByName.value;
        }
        if (fallbackCode) {
            const foundByCode = Array.from(selectEl.options).find(o => o.text.includes(fallbackCode));
            if (foundByCode) return foundByCode.value;
        }
        return '';
    }

    // التعبئة الفورية عند اختيار المستأجر (بدون تعبئة المبلغ تلقائياً)
    window.instantFillVoucherByTenant = function (passedTenantId) {
        if (isInternalAutoSyncing) return;
        const modal = document.getElementById('createOfficeVoucherModal');
        if (!modal) return;

        isInternalAutoSyncing = true;
        try {
            const vTypeSel  = modal.querySelector('select[name="voucher_type"]');
            const tenantSel = modal.querySelector('select[name="tenant_id"]');
            const ownerSel  = modal.querySelector('select[name="owner_id"]');
            const propSel   = modal.querySelector('select[name="property_id"]');
            const unitSel   = modal.querySelector('select[name="property_unit_id"]');
            const pmSel     = modal.querySelector('select[name="payment_method"]');
            const debitSel  = modal.querySelector('select[name="debit_account_id"]');
            const creditSel = modal.querySelector('select[name="credit_account_id"]');
            const notesInp  = modal.querySelector('input[name="notes"]');

            const vType = vTypeSel ? vTypeSel.value : 'receipt';
            const tId   = (passedTenantId !== undefined && passedTenantId !== '') ? String(passedTenantId) : (tenantSel ? String(tenantSel.value) : '');

            // 1. إذا اختار المستأجر -> نحدد فوراً (المالك + العقار + الوحدة + البيان) بدون لمس حقل المبلغ!
            if (tId && officeSmartVoucherData.tenantContracts[tId]) {
                const info = officeSmartVoucherData.tenantContracts[tId];
                if (ownerSel && info.owner_id) setSelectValueAndRefreshUI(ownerSel, info.owner_id);
                if (propSel && info.property_id) setSelectValueAndRefreshUI(propSel, info.property_id);
                if (unitSel && info.unit_id) setSelectValueAndRefreshUI(unitSel, info.unit_id);
                if (notesInp && info.auto_note) notesInp.value = info.auto_note;
            }

            // 2. تحديد حساب الصندوق أو البنك بناءً على طريقة الدفع
            const pmName = pmSel ? pmSel.value : '';
            let cashAccId = officeSmartVoucherData.pmAccounts[pmName] || '';
            if (!cashAccId) {
                if (pmName.includes('نقد') || pmName.includes('كاش')) {
                    cashAccId = officeSmartVoucherData.defaultCashAccId || findAccountOptionByKeyword(debitSel, 'صندوق', '1101');
                } else {
                    cashAccId = officeSmartVoucherData.defaultBankAccId || findAccountOptionByKeyword(debitSel, 'بنك', '1102');
                }
            }

            // 3. تحديد حساب المستأجر أو المالك في الشجرة
            const oId = ownerSel ? String(ownerSel.value) : '';
            let partyAccId = '';
            if (tId) {
                partyAccId = officeSmartVoucherData.tenantAccounts[tId]
                          || findAccountOptionByKeyword(creditSel, officeSmartVoucherData.tenantNames[tId], '1103');
            }
            if (!partyAccId && oId) {
                partyAccId = officeSmartVoucherData.ownerAccounts[oId]
                          || findAccountOptionByKeyword(creditSel, officeSmartVoucherData.ownerNames[oId], '2101');
            }

            if (vType === 'receipt') {
                if (cashAccId && debitSel) setSelectValueAndRefreshUI(debitSel, cashAccId);
                if (partyAccId && creditSel) setSelectValueAndRefreshUI(creditSel, partyAccId);
            } else {
                if (partyAccId && debitSel) setSelectValueAndRefreshUI(debitSel, partyAccId);
                if (cashAccId && creditSel) setSelectValueAndRefreshUI(creditSel, cashAccId);
            }
        } finally {
            isInternalAutoSyncing = false;
        }
    };

    window.instantFillVoucherByOwner = function (passedOwnerId) {
        if (isInternalAutoSyncing) return;
        const modal = document.getElementById('createOfficeVoucherModal');
        if (!modal) return;

        isInternalAutoSyncing = true;
        try {
            const vTypeSel  = modal.querySelector('select[name="voucher_type"]');
            const ownerSel  = modal.querySelector('select[name="owner_id"]');
            const pmSel     = modal.querySelector('select[name="payment_method"]');
            const debitSel  = modal.querySelector('select[name="debit_account_id"]');
            const creditSel = modal.querySelector('select[name="credit_account_id"]');

            const vType = vTypeSel ? vTypeSel.value : 'receipt';
            const oId = (passedOwnerId !== undefined && passedOwnerId !== '') ? String(passedOwnerId) : (ownerSel ? String(ownerSel.value) : '');
            const pmName = pmSel ? pmSel.value : '';

            let cashAccId = officeSmartVoucherData.pmAccounts[pmName] || officeSmartVoucherData.defaultBankAccId;
            let ownerAccId = (oId && officeSmartVoucherData.ownerAccounts[oId])
                ? officeSmartVoucherData.ownerAccounts[oId]
                : findAccountOptionByKeyword(debitSel, officeSmartVoucherData.ownerNames[oId], '2101');

            if (vType === 'payment') {
                if (ownerAccId && debitSel) setSelectValueAndRefreshUI(debitSel, ownerAccId);
                if (cashAccId && creditSel) setSelectValueAndRefreshUI(creditSel, cashAccId);
            } else {
                if (cashAccId && debitSel) setSelectValueAndRefreshUI(debitSel, cashAccId);
                if (ownerAccId && creditSel) setSelectValueAndRefreshUI(creditSel, ownerAccId);
            }
        } finally {
            isInternalAutoSyncing = false;
        }
    };

    // مراقبة أي تغيير في القوائم المنسدلة داخل نافذة السندات أو الفاتورة الضريبية (حتى مع وجود سكربت البحث 🔍)
    document.addEventListener('change', function (e) {
        if (isInternalAutoSyncing || !e.target) return;
        const el = e.target;

        if (el.closest('#createOfficeVoucherModal')) {
            if (el.name === 'tenant_id') window.instantFillVoucherByTenant(el.value);
            else if (el.name === 'owner_id') window.instantFillVoucherByOwner(el.value);
            else if (el.name === 'property_id') {
                const ownerId = officeSmartVoucherData.propertyOwners[el.value] || '';
                const ownerSel = document.querySelector('#createOfficeVoucherModal select[name="owner_id"]');
                if (ownerId && ownerSel) {
                    setSelectValueAndRefreshUI(ownerSel, ownerId);
                    window.instantFillVoucherByOwner(ownerId);
                }
            } else if (el.name === 'payment_method' || el.name === 'voucher_type') {
                window.instantFillVoucherByTenant();
            }
        }

        // في نافذة الفاتورة الضريبية للمكتب: تحديث حساب المدين والدائن تلقائياً بدون وضع مبلغ
        if (el.id === 'off_inv_tenant') {
            const opt = el.options[el.selectedIndex];
            const acc = opt ? opt.getAttribute('data-acc') : '';
            const debitSel = document.getElementById('off_inv_debit_acc');
            if (acc && debitSel) setSelectValueAndRefreshUI(debitSel, acc);
        }
        if (el.id === 'off_inv_owner') {
            const opt = el.options[el.selectedIndex];
            const acc = opt ? opt.getAttribute('data-acc') : '';
            const creditSel = document.getElementById('off_inv_credit_acc');
            if (acc && creditSel) setSelectValueAndRefreshUI(creditSel, acc);
        }
    });
<!-- المحرك الشامل: تعريب طرق الدفع + إجمالي القيد اليومي والضريبة + إصلاح تقطع الحروف والصفحات في الـ PDF -->
<style id="arabic-pdf-and-journal-fixes">
    /* منع تقطع الحروف العربية نهائياً حتى داخل عناصر الأرقام font-mono */
    body, body *, table, th, td, div, span, p, h1, h2, h3, h4, b, strong, .font-mono {
        font-family: 'Readex Pro', 'Tahoma', 'Arial', sans-serif !important;
        letter-spacing: normal !important;
        word-spacing: normal !important;
        font-kerning: normal !important;
        font-variant-ligatures: normal !important;
        font-feature-settings: "liga" 1, "calt" 1, "kern" 1 !important;
        text-rendering: optimizeLegibility !important;
    }

    /* منع انقسام الأسطر والبطاقات والجداول بين صفحات الـ PDF */
    tr, thead, tfoot, .journal-table-block, .pdf-no-break, .grid > div {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }
    thead { display: table-header-group !important; }
    tfoot { display: table-footer-group !important; }

    /* حاوية الطباعة والـ PDF المنظمة */
    .erp-pdf-clean-clone {
        direction: rtl !important;
        text-align: right !important;
        background: #ffffff !important;
        color: #0f172a !important;
        padding: 16px !important;
        width: 100% !important;
        max-width: none !important;
        overflow: visible !important;
    }
    .erp-pdf-clean-clone * {
        font-family: 'Readex Pro', 'Tahoma', 'Arial', sans-serif !important;
        letter-spacing: normal !important;
        max-height: none !important;
        overflow: visible !important;
    }
    .erp-pdf-clean-clone .no-print,
    .erp-pdf-clean-clone .erp-pagination-bar,
    .erp-pdf-clean-clone button,
    .erp-pdf-clean-clone form {
        display: none !important;
    }
    .erp-pdf-clean-clone table {
        width: 100% !important;
        border-collapse: collapse !important;
        table-layout: auto !important;
        direction: rtl !important;
    }
    .erp-pdf-clean-clone th,
    .erp-pdf-clean-clone td {
        border: 1px solid #cbd5e1 !important;
        padding: 8px 10px !important;
        font-size: 11px !important;
        line-height: 1.45 !important;
        white-space: normal !important;
        word-break: break-word !important;
        text-align: right !important;
    }
</style>

<script id="erp-master-fixes-script">
(function() {
    // 1. قاموس تعريب طرق الدفع الفوري في العرض والطباعة والنوافذ
    const PM_AR_MAP = {
        'bank_transfer': 'حوالة بنكية',
        'transfer': 'حوالة بنكية',
        'bank': 'حوالة بنكية',
        'sadad': 'نظام سداد',
        'mada': 'شبكة مدى',
        'pos': 'شبكة مدى',
        'card': 'شبكة مدى',
        'cash': 'نقداً (كاش)',
        'cheque': 'شيك مصدق',
        'check': 'شيك مصدق'
    };

    window.translatePaymentMethodAr = function(val) {
        if (!val) return 'حوالة بنكية';
        const key = String(val).trim().toLowerCase();
        return PM_AR_MAP[key] || val;
    };

    function localizeAllPaymentMethodsInDOM() {
        if (Array.isArray(window.officeVouchersMaster)) {
            window.officeVouchersMaster.forEach(v => {
                if (v && v.payment_method) v.payment_method = window.translatePaymentMethodAr(v.payment_method);
            });
        }
        document.querySelectorAll('td, span, b, div, option').forEach(el => {
            if (el.children.length === 0 && el.innerText) {
                const raw = el.innerText.trim().toLowerCase();
                if (PM_AR_MAP[raw]) {
                    el.innerText = PM_AR_MAP[raw];
                }
            }
        });
    }

    // ضمان تعريب طريقة الدفع في نافذة معاينة وطباعة السند الرسمي
    const origOpenVoucher = window.openOfficialVoucherPreview;
    window.openOfficialVoucherPreview = function(v) {
        if (v) {
            v.payment_method = window.translatePaymentMethodAr(v.payment_method || v.method);
            v.method = v.payment_method;
        }
        if (typeof origOpenVoucher === 'function') origOpenVoucher(v);
        const mEl = document.getElementById('pdf_method');
        if (mEl) mEl.innerText = window.translatePaymentMethodAr(mEl.innerText);
    };
    window.previewOfficialVoucher = window.openOfficialVoucherPreview;

    // 2. محرك القيد اليومي الذكي: إظهار الإجمالي المباشر + تبعية الضريبة للمدين أو الدائن + إظهار الإجمالي بعد الحفظ
    function fmtMoney(n) {
        return Number(n || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    window.recalculateJournalFormTotals = function(form) {
        if (!form) return;
        const debits  = Array.from(form.querySelectorAll('input[name="debits[]"]'));
        const credits = Array.from(form.querySelectorAll('input[name="credits[]"]'));
        const taxes   = Array.from(form.querySelectorAll('input[name="taxes[]"]'));

        let baseDebit = 0, baseCredit = 0;
        let debitTax = 0, creditTax = 0;

        const rowCount = Math.max(debits.length, credits.length);
        for (let i = 0; i < rowCount; i++) {
            const dInp = debits[i];
            const cInp = credits[i];
            const tInp = taxes[i];
            const d = parseFloat(dInp?.value || 0) || 0;
            const c = parseFloat(cInp?.value || 0) || 0;
            const t = parseFloat(tInp?.value || 0) || 0;

            baseDebit  += d;
            baseCredit += c;

            // تحديث عنوان الضريبة في نفس السطر ليعرف المستخدم هل هي ضريبة مدين أم ضريبة دائن
            if (tInp) {
                const rowWrap = tInp.closest('.grid') || tInp.parentElement;
                const taxLabel = rowWrap ? tInp.parentElement.querySelector('label') : null;
                let rowSumBadge = tInp.parentElement.querySelector('.row-line-total-hint');
                if (!rowSumBadge) {
                    rowSumBadge = document.createElement('div');
                    rowSumBadge.className = 'row-line-total-hint text-[10px] font-bold mt-1';
                    tInp.parentElement.appendChild(rowSumBadge);
                }

                if (d > 0) {
                    debitTax += t;
                    if (taxLabel) {
                        taxLabel.innerText = 'الضريبة (مدين +)';
                        taxLabel.className = 'block text-[10px] font-bold text-emerald-700 mb-1';
                    }
                    rowSumBadge.className = 'row-line-total-hint text-[10px] font-bold mt-1 text-emerald-700';
                    rowSumBadge.innerText = 'إجمالي السطر المدين: ' + fmtMoney(d + t);
                } else if (c > 0) {
                    creditTax += t;
                    if (taxLabel) {
                        taxLabel.innerText = 'الضريبة (دائن -)';
                        taxLabel.className = 'block text-[10px] font-bold text-amber-700 mb-1';
                    }
                    rowSumBadge.className = 'row-line-total-hint text-[10px] font-bold mt-1 text-amber-700';
                    rowSumBadge.innerText = 'إجمالي السطر الدائن: ' + fmtMoney(c + t);
                } else {
                    if (taxLabel) {
                        taxLabel.innerText = 'الضريبة';
                        taxLabel.className = 'block text-[10px] font-bold text-slate-500 mb-1';
                    }
                    rowSumBadge.innerText = '';
                }
            }
        }

        const totalDebitWithTax  = baseDebit + debitTax;
        const totalCreditWithTax = baseCredit + creditTax;
        const entryTotal         = Math.max(totalDebitWithTax, totalCreditWithTax);
        const diff               = Math.round((totalDebitWithTax - totalCreditWithTax) * 100) / 100;
        const isBalanced         = Math.abs(diff) <= 0.05 && entryTotal > 0;

        let summaryBox = form.querySelector('.live-journal-summary-bar');
        if (!summaryBox) {
            summaryBox = document.createElement('div');
            summaryBox.className = 'live-journal-summary-bar p-4 rounded-2xl bg-slate-900 text-white space-y-3 my-3 shadow-md';
            const submitFooter = form.querySelector('.flex.justify-end');
            if (submitFooter) {
                form.insertBefore(summaryBox, submitFooter);
            } else {
                form.appendChild(summaryBox);
            }
        }

        summaryBox.innerHTML = `
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-700 pb-2">
                <span class="text-xs font-bold text-emerald-400">📊 ملخص وإجمالي القيد اليومي المباشر (شامل الضريبة التابعة لكل طرف):</span>
                <span class="px-3 py-1 rounded-lg text-xs font-extrabold ${isBalanced ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-400/40' : 'bg-rose-500/30 text-rose-200 border border-rose-400/40'}">
                    ${isBalanced ? '✔ القيد متوازن وجاهز للحفظ (الفرق: 0.00)' : ('⚠️ الفرق غير المتوازن: ' + fmtMoney(Math.abs(diff)) + ' ر.س')}
                </span>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-2.5 text-xs">
                <div class="p-2.5 rounded-xl bg-white/5 border border-white/10">
                    <span class="text-slate-400 block text-[10px]">المبلغ المدين + ضريبة المدين:</span>
                    <b class="text-emerald-400 text-sm">${fmtMoney(baseDebit)} + ${fmtMoney(debitTax)}</b>
                    <div class="text-[11px] font-bold text-white mt-0.5">الإجمالي: ${fmtMoney(totalDebitWithTax)} ر.س</div>
                </div>
                <div class="p-2.5 rounded-xl bg-white/5 border border-white/10">
                    <span class="text-slate-400 block text-[10px]">المبلغ الدائن + ضريبة الدائن:</span>
                    <b class="text-amber-400 text-sm">${fmtMoney(baseCredit)} + ${fmtMoney(creditTax)}</b>
                    <div class="text-[11px] font-bold text-white mt-0.5">الإجمالي: ${fmtMoney(totalCreditWithTax)} ر.س</div>
                </div>
                <div class="p-2.5 rounded-xl bg-white/5 border border-white/10">
                    <span class="text-slate-400 block text-[10px]">صافي الضريبة (مدين - دائن):</span>
                    <b class="text-sky-300 text-sm">${fmtMoney(debitTax - creditTax)} ر.س</b>
                    <div class="text-[10px] text-slate-400 mt-0.5">مدين: ${fmtMoney(debitTax)} | دائن: ${fmtMoney(creditTax)}</div>
                </div>
                <div class="p-2.5 rounded-xl bg-emerald-500/20 border border-emerald-400/40">
                    <span class="text-emerald-300 block text-[10px] font-bold">💎 إجمالي القيد المعتمد:</span>
                    <b class="text-white text-base font-extrabold">${fmtMoney(entryTotal)} ر.س</b>
                </div>
                <div class="p-2.5 rounded-xl ${Math.abs(diff) <= 0.05 ? 'bg-white/5 border-white/10' : 'bg-rose-500/25 border-rose-400'} border">
                    <span class="text-slate-300 block text-[10px] font-bold">⚖️ صافي الفرق (التوازن):</span>
                    <b class="text-base font-extrabold ${Math.abs(diff) <= 0.05 ? 'text-emerald-400' : 'text-rose-300'}">${fmtMoney(diff)} ر.س</b>
                </div>
            </div>
        `;
    };

    function bindJournalFormsLiveCalc() {
        document.querySelectorAll('form[action*="journal-entries"]').forEach(form => {
            if (form.dataset.liveCalcBound !== '1') {
                form.dataset.liveCalcBound = '1';
                form.addEventListener('input', function(e) {
                    const t = e.target;
                    if (t && t.name === 'debits[]' && parseFloat(t.value || 0) > 0) {
                        const row = t.closest('.grid');
                        const cInp = row ? row.querySelector('input[name="credits[]"]') : null;
                        if (cInp && parseFloat(cInp.value || 0) > 0) cInp.value = '0';
                    } else if (t && t.name === 'credits[]' && parseFloat(t.value || 0) > 0) {
                        const row = t.closest('.grid');
                        const dInp = row ? row.querySelector('input[name="debits[]"]') : null;
                        if (dInp && parseFloat(dInp.value || 0) > 0) dInp.value = '0';
                    }
                    window.recalculateJournalFormTotals(form);
                });
                form.addEventListener('click', function() {
                    setTimeout(() => window.recalculateJournalFormTotals(form), 50);
                });
            }
            window.recalculateJournalFormTotals(form);
        });

        // إظهار إجمالي جميع القيود المحفوظة أسفل جدول القيود اليومية الرئيسي وحتى بعد الحفظ
        const journalTable = document.getElementById('journalEntriesTable');
        if (journalTable && !journalTable.querySelector('tfoot')) {
            let sumEntries = 0, sumTaxes = 0;
            journalTable.querySelectorAll('tbody tr.journal-row').forEach(tr => {
                const cells = tr.querySelectorAll('td');
                if (cells.length >= 5) {
                    const amtTxt = (cells[3].innerText || '').replace(/[^0-9.-]/g, '');
                    const taxTxt = (cells[4].innerText || '').replace(/[^0-9.-]/g, '');
                    sumEntries += parseFloat(amtTxt || 0);
                    sumTaxes   += parseFloat(taxTxt || 0);
                    // إضافة شارة "متوازن (الفرق: 0.00)" بجانب مبلغ كل قيد محفوظ
                    if (!cells[3].querySelector('.saved-je-badge')) {
                        const b = document.createElement('div');
                        b.className = 'saved-je-badge text-[10px] font-bold text-emerald-700 mt-0.5';
                        b.innerText = 'إجمالي القيد شامل الضريبة ✔ (الفرق: 0.00)';
                        cells[3].appendChild(b);
                    }
                }
            });
            const tfoot = document.createElement('tfoot');
            tfoot.className = 'bg-slate-900 text-white font-bold text-xs';
            tfoot.innerHTML = `
                <tr>
                    <td colspan="3" class="py-3.5 px-4 text-emerald-300">📊 إجمالي القيود اليومية المحفوظة في السجل</td>
                    <td class="py-3.5 px-4 font-extrabold text-white">${fmtMoney(sumEntries)} ر.س</td>
                    <td class="py-3.5 px-4 text-amber-300">${fmtMoney(sumTaxes)} ر.س</td>
                    <td colspan="2" class="py-3.5 px-4 text-emerald-400">جميع القيود متوازنة (صافي الفرق: 0.00 ر.س) ✔</td>
                </tr>
            `;
            journalTable.appendChild(tfoot);
        }

        // تحديث جداول عرض تفاصيل القيد المحفوظ (viewJournalModal) لتوضيح ضريبة المدين وضريبة الدائن وإجمالي القيد
        document.querySelectorAll('table[id^="journalDetailTable_"]').forEach(detailTbl => {
            if (detailTbl.dataset.enhancedDetail === '1') return;
            detailTbl.dataset.enhancedDetail = '1';
            let dBase = 0, cBase = 0, dTax = 0, cTax = 0;
            detailTbl.querySelectorAll('tbody tr').forEach(tr => {
                const tds = tr.querySelectorAll('td');
                if (tds.length >= 5) {
                    const d = parseFloat((tds[2].innerText || '').replace(/[^0-9.-]/g, '')) || 0;
                    const c = parseFloat((tds[3].innerText || '').replace(/[^0-9.-]/g, '')) || 0;
                    const t = parseFloat((tds[4].innerText || '').replace(/[^0-9.-]/g, '')) || 0;
                    dBase += d; cBase += c;
                    if (d > 0 && t > 0) {
                        dTax += t;
                        tds[4].innerHTML = `<span class="text-emerald-700 font-bold">${fmtMoney(t)} (ضريبة مدين)</span>`;
                    } else if (c > 0 && t > 0) {
                        cTax += t;
                        tds[4].innerHTML = `<span class="text-amber-700 font-bold">${fmtMoney(t)} (ضريبة دائن)</span>`;
                    }
                }
            });
            const tf = detailTbl.querySelector('tfoot');
            if (tf) {
                const totD = dBase + dTax;
                const totC = cBase + cTax;
                const diff = totD - totC;
                tf.innerHTML = `
                    <tr class="bg-slate-100 font-bold border-t">
                        <td colspan="2" class="py-2.5 px-3">إجمالي المبالغ الأساسية</td>
                        <td class="py-2.5 px-3 text-emerald-700">${fmtMoney(dBase)} ر.س</td>
                        <td class="py-2.5 px-3 text-amber-700">${fmtMoney(cBase)} ر.س</td>
                        <td class="py-2.5 px-3">مدين: ${fmtMoney(dTax)} | دائن: ${fmtMoney(cTax)}</td>
                        <td class="py-2.5 px-3">صافي الضريبة: ${fmtMoney(dTax - cTax)} ر.س</td>
                    </tr>
                    <tr class="bg-slate-900 text-white font-extrabold">
                        <td colspan="2" class="py-3 px-3 text-emerald-300">💎 إجمالي القيد النهائي (شامل الضريبة)</td>
                        <td class="py-3 px-3 text-emerald-300">${fmtMoney(totD)} ر.س</td>
                        <td class="py-3 px-3 text-amber-300">${fmtMoney(totC)} ر.س</td>
                        <td colspan="2" class="py-3 px-3 text-white">الفرق (رصيد التوازن): ${fmtMoney(diff)} ر.س ✔</td>
                    </tr>
                `;
            }
        });
    }

    // 3. محرك تصدير الـ PDF العربي الموحد (يمنع تقطع الحروف ويمنع انقسام الصفحات ويضبط العرض)
    window.buildCleanPdfClone = function(sourceEl) {
        const wrapper = document.createElement('div');
        wrapper.dir = 'rtl';
        wrapper.style.cssText = 'position:fixed;left:-9999px;top:0;width:1020px;background:#ffffff;z-index:-1;';
        const clone = sourceEl.cloneNode(true);
        clone.classList.add('erp-pdf-clean-clone');

        // إظهار جميع الصفوف المطابقة للبحث عند التصدير للـ PDF حتى لا تُقص بسبب ترقيم الصفحات
        clone.querySelectorAll('tbody tr').forEach(tr => {
            if (tr.dataset.searchMatched !== '0') {
                tr.style.display = '';
            }
        });

        // إزالة عمود "الإجراءات" من نسخة الطباعة لتوسيع بقية الأعمدة ومنع تداخلها
        clone.querySelectorAll('table').forEach(tbl => {
            const ths = Array.from(tbl.querySelectorAll('thead th'));
            const removeIndices = [];
            ths.forEach((th, idx) => {
                const txt = (th.innerText || '').trim();
                if (['إجراءات', 'الإجراءات', 'إجراء', 'حذف', 'تعديل', 'إجراءات التحصيل والمطالبة'].some(w => txt.includes(w))) {
                    removeIndices.push(idx);
                    th.remove();
                }
            });
            if (removeIndices.length > 0) {
                tbl.querySelectorAll('tbody tr').forEach(tr => {
                    const tds = Array.from(tr.querySelectorAll('td'));
                    removeIndices.forEach(rIdx => {
                        if (tds[rIdx]) tds[rIdx].remove();
                    });
                });
            }
        });

        wrapper.appendChild(clone);
        document.body.appendChild(wrapper);
        return { wrapper, clone };
    };

    window.exportElementToPDF = async function(elementId, filename) {
        const el = document.getElementById(elementId);
        if (!el) {
            alert('العنصر المطلوب للطباعة غير موجود.');
            return;
        }
        if (document.fonts && document.fonts.ready) {
            await document.fonts.ready;
        }

        const isWideTable = el.querySelectorAll('thead th').length > 6;
        const { wrapper, clone } = window.buildCleanPdfClone(el);
        wrapper.style.width = isWideTable ? '1080px' : '760px';

        try {
            await html2pdf().set({
                margin: [8, 8, 8, 8],
                filename: (filename || 'Document.pdf').replace(/\.pdf$/i, '') + '.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: {
                    scale: 2,
                    useCORS: true,
                    letterRendering: false,
                    backgroundColor: '#ffffff',
                    windowWidth: isWideTable ? 1120 : 800
                },
                pagebreak: {
                    mode: ['css', 'legacy'],
                    avoid: ['tr', 'thead', 'tfoot', '.pdf-no-break', '.grid > div']
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: isWideTable ? 'landscape' : 'portrait'
                }
            }).from(clone).save();
        } finally {
            wrapper.remove();
        }
    };

    window.downloadVoucherPDF = function() {
        window.exportElementToPDF('printableVoucherArea', (window.currentVoucherNumber || 'Voucher') + '.pdf');
    };

    document.addEventListener('DOMContentLoaded', function() {
        localizeAllPaymentMethodsInDOM();
        bindJournalFormsLiveCalc();
    });
})();
</script>
<!-- بداية البلوك الشامل المطور: منع تشويش وتداخل الحروف والأكواد في الـ PDF + ترتيب القيود للأحدث بالأعلى -->
<style id="arabic-pdf-and-journal-fixes">
    /* 1. ضبط الخطوط العربية على الشاشة وفي الـ PDF */
    body, body *, table, th, td, div, span, p, h1, h2, h3, h4, b, strong, .font-mono {
        font-family: 'Readex Pro', 'Tahoma', 'Arial', sans-serif !important;
        letter-spacing: 0px !important;
        word-spacing: normal !important;
        font-kerning: normal !important;
        font-variant-ligatures: normal !important;
    }

    /* 2. تطابق العرض 100% لجميع الجداول والحاويات */
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
    #officeInstallmentsPrintableWrap,
    #officeOverviewContractsWrap,
    #adminOverviewOfficesWrap {
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }

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

    /* 3. تثبيت حاوية html2pdf في نقطة الصفر (0,0) لمنع أي إزاحة */
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

    /* 5. تنسيق نسخة الـ PDF وعزل الكلمات العربية والأكواد الإنجليزية لمنع التشويش والتداخل نهائياً */
    .erp-pdf-clean-clone {
        direction: rtl !important;
        text-align: right !important;
        background: #ffffff !important;
        color: #0f172a !important;
        padding: 12px 14px !important;
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
        text-decoration: none !important;
        max-height: none !important;
        overflow: visible !important;
        box-sizing: border-box !important;
    }
    .erp-pdf-clean-clone .pdf-word-token {
        display: inline-block !important;
        white-space: nowrap !important;
        vertical-align: baseline !important;
        unicode-bidi: isolate !important;
        margin: 0 1px !important;
    }
    .erp-pdf-clean-clone .pdf-word-token[dir="ltr"] {
        direction: ltr !important;
        unicode-bidi: isolate !important;
    }
    .erp-pdf-clean-clone .pdf-word-token[dir="rtl"] {
        direction: rtl !important;
        unicode-bidi: isolate !important;
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
    .erp-pdf-clean-clone .md\:grid-cols-4,
    .erp-pdf-clean-clone .grid-cols-4 {
        display: grid !important;
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        gap: 8px !important;
        width: 100% !important;
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
        padding: 8px 8px !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        text-align: right !important;
        direction: rtl !important;
        white-space: nowrap !important;
        line-height: 1.35 !important;
    }
    .erp-pdf-clean-clone td {
        border: 1px solid #cbd5e1 !important;
        padding: 7px 8px !important;
        font-size: 11px !important;
        line-height: 1.5 !important;
        color: #0f172a !important;
        white-space: normal !important;
        text-align: right !important;
        direction: rtl !important;
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
    // 1. ضبط اتجاه رسم النصوص العربية والإنجليزية داخل Canvas أثناء توليد الـ PDF لمنع انقلاب الأقواس والنقطتين
    const origFillText = CanvasRenderingContext2D.prototype.fillText;
    CanvasRenderingContext2D.prototype.fillText = function(text, x, y, maxWidth) {
        if (window.__erpPdfRenderingActive && typeof text === 'string') {
            this.textAlign = 'left';
            if (/[\u0600-\u06FF\u200F]/.test(text)) {
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

    // 3. حساب إجمالي القيد اليومي المباشر في نوافذ الإضافة والتعديل فقط (مع استثناء نموذج الحذف)
    const fmt = n => Number(n || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    window.recalculateJournalFormTotals = function(form) {
        if (!form || (form.action && form.action.includes('/delete'))) return;
        const debits = form.querySelectorAll('input[name="debits[]"]');
        const credits = form.querySelectorAll('input[name="credits[]"]');
        const taxes = form.querySelectorAll('input[name="taxes[]"]');
        if (debits.length === 0 && credits.length === 0) return;

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
        const diff = Math.round(Math.abs(totD - totC) * 100) / 100;
        const isBalanced = diff <= 0.05;

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
                <span class="px-2.5 py-1 rounded font-bold ${isBalanced ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/30 text-rose-200'}">
                    ${isBalanced ? '✔ متوازن (الفرق: 0.00 ر.س)' : '⚠️ غير متوازن — الفرق: ' + fmt(diff) + ' ر.س'}
                </span>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs pt-1 text-center">
                <div class="p-2 rounded bg-white/5">المدين + ضريبته: <b class="text-emerald-400 block font-mono mt-0.5">${fmt(bD)} + ${fmt(tD)} = ${fmt(totD)} ر.س</b></div>
                <div class="p-2 rounded bg-white/5">الدائن + ضريبته: <b class="text-amber-400 block font-mono mt-0.5">${fmt(bC)} + ${fmt(tC)} = ${fmt(totC)} ر.س</b></div>
                <div class="p-2 rounded bg-emerald-500/20">إجمالي القيد المعتمد: <b class="text-white text-sm block font-mono mt-0.5">${fmt(entryTotal)} ر.س</b></div>
                <div class="p-2 rounded bg-white/5">صافي الفرق (التوازن): <b class="${isBalanced ? 'text-emerald-400' : 'text-rose-300'} text-sm block font-mono mt-0.5">${fmt(diff)} ر.س</b></div>
            </div>
        `;
    };

    function initJournalAndLedgerFixes() {
        document.querySelectorAll('#journalEntriesTable .live-journal-summary-bar').forEach(el => el.remove());
        const jeTbl = document.getElementById('journalEntriesTable');
        if (jeTbl) {
            if (jeTbl.querySelector('tfoot')) jeTbl.querySelector('tfoot').remove();
            // ترتيب صفوف جدول القيود اليومية تنازلياً (الأحدث في الأعلى: 3 ثم 2 ثم 1)
            const tbody = jeTbl.querySelector('tbody');
            if (tbody) {
                const rows = Array.from(tbody.querySelectorAll('tr.journal-row'));
                if (rows.length > 1) {
                    rows.sort((a, b) => {
                        const numA = parseInt((a.querySelector('td')?.innerText || '0').replace(/\D/g, ''), 10) || 0;
                        const numB = parseInt((b.querySelector('td')?.innerText || '0').replace(/\D/g, ''), 10) || 0;
                        return numB - numA;
                    });
                    rows.forEach(r => tbody.appendChild(r));
                    if (typeof window.refreshTablePagination === 'function') window.refreshTablePagination(jeTbl, true);
                    else if (typeof window.refreshAdminTablePagination === 'function') window.refreshAdminTablePagination(jeTbl, true);
                }
            }
        }

        ['#cards_view_section table', '#trial_view_section table', '#income_view_section table', '#pro_rep_contracts_table', '#pro_rep_vouchers_table'].forEach(sel => {
            document.querySelectorAll(sel).forEach(tbl => {
                if (tbl._paginationBar) {
                    tbl._paginationBar.remove();
                    tbl._paginationBar = null;
                }
                tbl.querySelectorAll('tbody tr').forEach(r => r.style.display = '');
            });
        });

        document.querySelectorAll('form[action*="journal-entries/store"], form[action*="journal-entries/"][action*="/update"]').forEach(f => {
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
    }

    // 4. الدالة الذكية لعزل الكلمات العربية عن الأكواد الإنجليزية والأرقام لمنع التداخل والتشويش في الـ PDF
    window.tokenizeCloneTextNodesForPdf = function(root) {
        const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, null, false);
        const textNodes = [];
        let n;
        while ((n = walker.nextNode())) {
            const pTag = n.parentElement ? n.parentElement.tagName : '';
            if (['SCRIPT', 'STYLE', 'SVG', 'TEXTAREA', 'OPTION', 'NOSCRIPT'].includes(pTag)) continue;
            if (n.nodeValue && n.nodeValue.trim().length > 0) {
                textNodes.push(n);
            }
        }

        textNodes.forEach(node => {
            let raw = node.nodeValue;
            // فصل أي رقم أو كود ملتصق مباشرة بحرف عربي (مثل 2,600.00ر.س) بمسافة
            raw = raw
                .replace(/([\u0600-\u06FF])([A-Za-z0-9#])/g, '$1 $2')
                .replace(/([A-Za-z0-9])([\u0600-\u06FF])/g, '$1 $2');

            const hasArabicInNode = /[\u0600-\u06FF]/.test(raw);
            // التقسيم بالمسافات فقط للحفاظ على الأقواس ملتصقة بكلماتها وعدم فصل الأكواد مثل (INV-CMP-2026-00001)
            const parts = raw.split(/(\s+)/);

            const frag = document.createDocumentFragment();
            parts.forEach(part => {
                if (!part) return;
                if (/^\s+$/.test(part)) {
                    frag.appendChild(document.createTextNode(' '));
                    return;
                }
                const span = document.createElement('span');
                span.className = 'pdf-word-token';
                const hasAr = /[\u0600-\u06FF]/.test(part);
                const hasLatinOrDigit = /[A-Za-z0-9]/.test(part);

                if (hasAr) {
                    span.dir = 'rtl';
                    span.textContent = part;
                } else if (hasLatinOrDigit) {
                    span.dir = 'ltr';
                    span.textContent = part;
                } else {
                    span.dir = hasArabicInNode ? 'rtl' : 'ltr';
                    span.textContent = hasArabicInNode ? (part + '\u200F') : part;
                }
                frag.appendChild(span);
            });

            if (node.parentNode) {
                node.parentNode.replaceChild(frag, node);
            }
        });
    };

    // 5. تجهيز نسخة الـ PDF بنفس الشكل الحالي مع تطبيق عزل الكلمات لمنع التشويش
    window.prepareSmartPdfClone = function(el) {
        const clone = el.cloneNode(true);
        clone.classList.add('erp-pdf-clean-clone');

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

        clone.querySelectorAll('tbody tr').forEach(tr => {
            if (tr.dataset.searchMatched === '0') {
                tr.remove();
            } else {
                tr.style.display = '';
            }
        });

        clone.querySelectorAll('td[dir="ltr"], th[dir="ltr"]').forEach(cell => {
            cell.setAttribute('dir', 'rtl');
        });

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

        clone.querySelectorAll('td button').forEach(btn => {
            const span = document.createElement('span');
            span.className = 'font-bold text-slate-900';
            span.innerHTML = btn.innerHTML;
            btn.replaceWith(span);
        });

        clone.querySelectorAll('button').forEach(btn => btn.remove());

        // تفعيل عزل الكلمات والأكواد لمنع التشويش والتداخل في الـ PDF
        window.tokenizeCloneTextNodesForPdf(clone);

        const singlePortraitIds = [
            'printableVoucherArea',
            'companyTaxInvoicePrintArea',
            'officeTaxInvoicePrintArea',
            'legalNoticePrintableArea',
            'installmentClaimPdfTemplate',
            'invoicePdfArea'
        ];
        const isJournalPrint = el.id && el.id.startsWith('printableJournalArea_');
        const isSingleDoc = singlePortraitIds.includes(el.id) || isJournalPrint;
        const isLandscape = !isSingleDoc && (maxRemainingCols >= 5 || el.id === 'pro_ownerSettlementPrintableArea' || el.id === 'financialStatementPrintable' || el.scrollWidth > 760);

        clone.style.width = '100%';
        clone.style.maxWidth = '100%';
        clone.style.minWidth = '100%';

        return { clone, isLandscape };
    };

    window.createConfiguredPdfWorker = function(filename, isLandscape) {
        const cleanName = (filename || 'Document.pdf').replace(/\.pdf$/i, '') + '.pdf';

        const worker = html2pdf().set({
            margin: [8, 8, 8, 8],
            filename: cleanName,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: {
                scale: 2,
                useCORS: true,
                letterRendering: false,
                backgroundColor: '#ffffff',
                scrollX: 0,
                scrollY: 0
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
        });

        return { worker };
    };

    window.expandPdfContainerToFitTables = function(c, isLandscape) {
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
            c.style.direction = 'rtl';

            const baseWidthPx = isLandscape ? 1120 : 760;
            c.style.width = baseWidthPx + 'px';
            c.style.maxWidth = baseWidthPx + 'px';
        }
    };

    // 6. الدالة الموحدة لتصدير الـ PDF
    window.exportElementToPDF = async function(elementId, filename) {
        const el = typeof elementId === 'string' ? document.getElementById(elementId) : elementId;
        if (!el) return alert('العنصر المطلوب للطباعة غير موجود.');
        if (document.fonts && document.fonts.ready) await document.fonts.ready;

        const savedScrollX = window.scrollX;
        const savedScrollY = window.scrollY;
        window.scrollTo(0, 0);
        window.__erpPdfRenderingActive = true;

        const { clone, isLandscape } = window.prepareSmartPdfClone(el);
        const { worker } = window.createConfiguredPdfWorker(filename, isLandscape);

        try {
            await worker.from(clone).toContainer().then(function() {
                const c = document.querySelector('.html2pdf__container');
                window.expandPdfContainerToFitTables(c, isLandscape);
            }).toCanvas().toPdf().save();
        } finally {
            window.__erpPdfRenderingActive = false;
            window.scrollTo(savedScrollX, savedScrollY);
        }
    };

    window.exportJournalPDF = function(entryId, entryNumber) {
        window.closeModal('printChoiceJournalModal_' + entryId);
        window.openModal('viewJournalModal_' + entryId);
        setTimeout(() => {
            window.exportElementToPDF('printableJournalArea_' + entryId, 'سند_قيد_يومية_رقم_' + entryNumber + '.pdf');
        }, 200);
    };

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
        const { worker } = window.createConfiguredPdfWorker(cleanFile, isLandscape);

        try {
            const pdfDataUri = await worker.from(clone).toContainer().then(function() {
                const c = document.querySelector('.html2pdf__container');
                window.expandPdfContainerToFitTables(c, isLandscape);
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