<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class OfficeErpController extends Controller
{
    /**
     * بوابة حماية المكتب: تمنع الملاك وأفراد العائلة من الدخول لبوابة المكتب، وتمنع الموظف العادي من تنفيذ مهام مدير المكتب الحساسة
     */
    public function callAction($method, $parameters)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        if ($user->user_type === 'company_admin' && session()->has('impersonate_office_id')) {
            return $this->{$method}(...array_values($parameters));
        }

        if (!in_array($user->user_type, ['office_admin', 'office_staff'], true)) {
            abort(403, 'غير مصرح لهذا الحساب بالدخول إلى بوابة إدارة المكتب العقاري.');
        }

        $officeAdminOnlyMethods = [
            'storeStaff',
            'updateStaff',
            'deleteStaff',
            'saveSmartOfficeSettings',
            'approvePendingVoucher',
            'closeFiscalYear',
            'updateOfficeWhatsappSettings',
            'updateOfficeWhatsapp',
        ];

        if ($user->user_type === 'office_staff' && in_array($method, $officeAdminOnlyMethods, true)) {
            abort(403, 'هذا الإجراء مخصص لمدير المكتب العقاري فقط.');
        }

        CompanyErpController::capturePreActionSnapshot(request());

        return $this->{$method}(...array_values($parameters));
    }

    private function getCurrentOfficeId(): ?int
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }
        if ($user->user_type === 'company_admin' && session()->has('impersonate_office_id')) {
            return (int) session('impersonate_office_id');
        }
        if (!in_array($user->user_type, ['office_admin', 'office_staff'], true)) {
            abort(403, 'غير مصرح لك بالوصول لبيانات المكتب.');
        }
        return $user->office_id ? (int) $user->office_id : null;
    }

   public function dashboard(Request $request)
    {
        app(CompanyErpController::class)->ensureDefaultAdminExists();

        if (!session('erp_fast_boot_done')) {
            app(CompanyErpController::class)->ensureDynamicPropertyTables();
            app(CompanyErpController::class)->ensureDynamicAccountTables();
            app(CompanyErpController::class)->ensurePaymentMethodsTable();
            app(CompanyErpController::class)->ensureJournalTables();
            session(['erp_fast_boot_done' => true]);
        }

        $officeId = $this->getCurrentOfficeId();
        if (!$officeId) {
            return redirect()->route('erp.dashboard')->withErrors(['يرجى اختيار مكتب عقار لمعاينته أولاً.']);
        }

        $office = DB::table('offices')->where('id', $officeId)->first();
        if (!$office) {
            return redirect()->route('erp.dashboard');
        }

        $allowedOfficeTabs = !empty($office->allowed_tabs)
            ? (json_decode($office->allowed_tabs, true) ?: [])
            : array_keys(\App\Http\Controllers\CompanyErpController::getAvailableOfficeTabs());

        if (!in_array('overview', $allowedOfficeTabs)) {
            array_unshift($allowedOfficeTabs, 'overview');
        }
        if (in_array('properties', $allowedOfficeTabs) && !in_array('mullak', $allowedOfficeTabs)) {
            $allowedOfficeTabs[] = 'mullak';
        }
        if ((in_array('accounting', $allowedOfficeTabs) || in_array('vouchers', $allowedOfficeTabs)) && !in_array('auditor', $allowedOfficeTabs)) {
            $allowedOfficeTabs[] = 'auditor';
        }

        $tab = $request->get('tab', 'overview');
        if (!in_array($tab, $allowedOfficeTabs)) {
            return redirect()->route('office.dashboard', ['tab' => 'overview'])
                ->withErrors(['عذراً، هذه الصفحة غير متاحة ضمن باقة اشتراك مكتبكم الحالية. يمكنك ترقية الباقة من الإدارة.']);
        }

        $accountTypes = DB::table('custom_account_types')->orderBy('id')->get();
        $accounts = DB::table('accounts')->where('office_id', $officeId)->orderBy('code')->get();
        if ($accounts->count() === 0) {
            $this->seedOfficeAccountsInternal($officeId);
            $accounts = DB::table('accounts')->where('office_id', $officeId)->orderBy('code')->get();
        }

        $paymentMethods = DB::table('custom_payment_methods')
            ->leftJoin('accounts', 'custom_payment_methods.account_id', '=', 'accounts.id')
            ->where(function ($q) use ($officeId) {
                $q->where('custom_payment_methods.office_id', $officeId)
                  ->orWhereNull('custom_payment_methods.office_id');
            })
            ->select('custom_payment_methods.*', 'accounts.code as account_code', 'accounts.name as account_name')
            ->orderBy('custom_payment_methods.id')
            ->get();

        $defaultTenantParentAcc = $accounts->firstWhere('code', '1103') ?: $accounts->first();
        $defaultOwnerParentAcc  = $accounts->firstWhere('code', '2101') ?: $accounts->first();

        $owners = DB::table('owners')
            ->leftJoin('users', function ($join) {
                $join->on('owners.id', '=', 'users.owner_id')
                     ->where('users.user_type', '=', 'owner');
            })
            ->leftJoin('accounts', 'owners.account_id', '=', 'accounts.id')
            ->where('owners.office_id', $officeId)
            ->select(
                'owners.*',
                'users.username as owner_username',
                'accounts.code as account_code',
                'accounts.name as account_name',
                'accounts.balance as account_balance'
            )
            ->orderBy('owners.id', 'desc')
            ->get();

        $propertyTypes = DB::table('property_types')
            ->leftJoin('accounts', 'property_types.account_id', '=', 'accounts.id')
            ->where('property_types.is_active', 1)
            ->select('property_types.*', 'accounts.code as account_code', 'accounts.name as account_name')
            ->get();
        $allAttributes = DB::table('property_type_attributes')->get();

        $properties = DB::table('properties')
            ->join('owners', 'properties.owner_id', '=', 'owners.id')
            ->join('property_types', 'properties.property_type_id', '=', 'property_types.id')
            ->where('properties.office_id', $officeId)
            ->select(
                'properties.*',
                'owners.name as owner_name',
                'property_types.name as type_name',
                'property_types.category as type_category',
                'property_types.has_units'
            )
            ->orderBy('properties.id', 'desc')
            ->get();

        $units = DB::table('property_units')
            ->join('properties', 'property_units.property_id', '=', 'properties.id')
            ->where('properties.office_id', $officeId)
            ->select('property_units.*', 'properties.title as property_title')
            ->orderBy('property_units.id', 'desc')
            ->get();

        $vouchers = DB::table('vouchers')
            ->where('vouchers.office_id', $officeId)
            ->leftJoin('accounts as debit_acc', 'vouchers.debit_account_id', '=', 'debit_acc.id')
            ->leftJoin('accounts as credit_acc', 'vouchers.credit_account_id', '=', 'credit_acc.id')
            ->leftJoin('owners', 'vouchers.owner_id', '=', 'owners.id')
            ->leftJoin('tenants', 'vouchers.tenant_id', '=', 'tenants.id')
            ->select(
                'vouchers.*',
                'debit_acc.name as debit_name',
                'debit_acc.code as debit_code',
                'credit_acc.name as credit_name',
                'credit_acc.code as credit_code',
                'owners.name as owner_name',
                'owners.phone as owner_phone',
                'tenants.name as tenant_name',
                'tenants.phone as tenant_phone'
            )
            ->orderBy('vouchers.id', 'desc')
            ->get()
            ->map(function ($v) {
                $v->payment_method = CompanyErpController::translatePaymentMethod($v->payment_method);
                return $v;
            });

        $journalEntries = DB::table('journal_entries')
            ->where('office_id', $officeId)
            ->orderBy('entry_number', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $journalItems = DB::table('journal_entry_items')
            ->join('journal_entries', 'journal_entry_items.journal_entry_id', '=', 'journal_entries.id')
            ->leftJoin('accounts', 'journal_entry_items.account_id', '=', 'accounts.id')
            ->where('journal_entries.office_id', $officeId)
            ->select(
                'journal_entry_items.*',
                'journal_entries.entry_number',
                'journal_entries.entry_date',
                'journal_entries.general_notes',
                'accounts.code as account_code',
                'accounts.name as account_name',
                'accounts.type as account_type'
            )
            ->orderBy('journal_entry_items.id', 'asc')
            ->get();

        $journalItemsByEntry = $journalItems->groupBy('journal_entry_id');

        $tenants = DB::table('tenants')
            ->leftJoin('accounts', 'tenants.account_id', '=', 'accounts.id')
            ->where('tenants.office_id', $officeId)
            ->select(
                'tenants.*',
                'accounts.code as account_code',
                'accounts.name as account_name',
                'accounts.balance as account_balance'
            )
            ->orderBy('tenants.id', 'desc')
            ->get()
            ->map(function ($tenant) use ($vouchers) {
                $tVouchers = $vouchers->where('tenant_id', $tenant->id)->values();
                $tenant->total_receipts = $tVouchers->where('voucher_type', 'receipt')->sum('amount');
                $tenant->total_payments = $tVouchers->where('voucher_type', 'payment')->sum('amount');
                $tenant->statement_vouchers = $tVouchers;
                return $tenant;
            });

        $contracts = DB::table('lease_contracts')
            ->join('properties', 'lease_contracts.property_id', '=', 'properties.id')
            ->join('owners', 'lease_contracts.owner_id', '=', 'owners.id')
            ->join('tenants', 'lease_contracts.tenant_id', '=', 'tenants.id')
            ->leftJoin('property_units', 'lease_contracts.property_unit_id', '=', 'property_units.id')
            ->where('lease_contracts.office_id', $officeId)
            ->select(
                'lease_contracts.*',
                'properties.title as property_title',
                'properties.deed_number',
                'owners.name as owner_name',
                'tenants.name as tenant_name',
                'tenants.phone as tenant_phone',
                'property_units.unit_number'
            )
            ->orderBy('lease_contracts.id', 'desc')
            ->get();

        $staff = DB::table('users')
            ->where('office_id', $officeId)
            ->whereIn('user_type', ['office_admin', 'office_staff'])
            ->orderBy('id')
            ->get();

        $companyAdminPhone = CompanyErpController::getSetting('whatsapp_sender_phone', '+966500000000');
        $nextReceiptNo = CompanyErpController::generateNextVoucherNumber('receipt', $officeId);
        $nextPaymentNo = CompanyErpController::generateNextVoucherNumber('payment', $officeId);

        app(CompanyErpController::class)->ensureTaxInvoicesTable();
        $officeInvoices = DB::table('tax_invoices')
            ->where('office_id', $officeId)
            ->orderBy('id', 'desc')
            ->get();

        $defaultVatRate = (float) CompanyErpController::getSetting('default_vat_rate', '15');
        $activityLogs = CompanyErpController::getFilteredActivityLogs($officeId, null, 'office');

        // توليد وتحديث جدول الدفعات تلقائياً لجميع العقود وتحديث حالتها (مستحق، متأخر جزئي، متأخر، مسدد)
        CompanyErpController::ensureContractInstallmentsTable();
        foreach ($contracts as $cntItem) {
            CompanyErpController::generateInstallmentsForContract($cntItem);
        }

        $todayStr = date('Y-m-d');
        DB::table('contract_installments')
            ->where('office_id', $officeId)
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', $todayStr)
            ->whereRaw('paid_amount <= 0')
            ->update(['status' => 'overdue']);

        DB::table('contract_installments')
            ->where('office_id', $officeId)
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', $todayStr)
            ->whereRaw('paid_amount > 0 AND paid_amount < amount')
            ->update(['status' => 'partial_overdue']);

        $installments = DB::table('contract_installments')
            ->join('lease_contracts', 'contract_installments.lease_contract_id', '=', 'lease_contracts.id')
            ->join('properties', 'contract_installments.property_id', '=', 'properties.id')
            ->join('owners', 'contract_installments.owner_id', '=', 'owners.id')
            ->join('tenants', 'contract_installments.tenant_id', '=', 'tenants.id')
            ->leftJoin('property_units', 'contract_installments.property_unit_id', '=', 'property_units.id')
            ->where('contract_installments.office_id', $officeId)
            ->select(
                'contract_installments.*',
                'lease_contracts.contract_number',
                'lease_contracts.payment_cycle',
                'properties.title as property_title',
                'owners.name as owner_name',
                'tenants.name as tenant_name',
                'tenants.phone as tenant_phone',
                'property_units.unit_number'
            )
            ->orderBy('contract_installments.due_date', 'asc')
            ->get();

        // حساب أعمار الديون العقارية (Aging Receivables: 1-30، 31-60، 61-90، +90 يوم)
        $agingBuckets = ['b_30' => 0, 'b_60' => 0, 'b_90' => 0, 'b_plus' => 0, 'rows' => []];
        foreach ($installments->where('status', '!=', 'paid') as $inst) {
            $rem = round($inst->amount - $inst->paid_amount, 2);
            if ($rem <= 0 || $inst->due_date >= $todayStr) continue;
            $daysLate = (int) \Carbon\Carbon::parse($inst->due_date)->diffInDays(now());
            if ($daysLate <= 30) $agingBuckets['b_30'] += $rem;
            elseif ($daysLate <= 60) $agingBuckets['b_60'] += $rem;
            elseif ($daysLate <= 90) $agingBuckets['b_90'] += $rem;
            else $agingBuckets['b_plus'] += $rem;
            $inst->days_late = $daysLate;
            $inst->remaining_amount = $rem;
            $agingBuckets['rows'][] = $inst;
        }

        // فحص كاشف الشذوذ المحاسبي حسب الشروط التي حددها صاحب المكتب
        $anomalyAlerts = collect();
        if ($office->enable_anomaly_detector ?? 1) {
            $maxAmt = (float) ($office->anomaly_max_amount ?? 20000);
            $nStart = (int) ($office->anomaly_night_start ?? 23);
            $nEnd = (int) ($office->anomaly_night_end ?? 5);

            foreach ($vouchers as $v) {
                $hour = (int) date('G', strtotime($v->created_at ?? now()));
                $isNight = ($nStart > $nEnd) ? ($hour >= $nStart || $hour <= $nEnd) : ($hour >= $nStart && $hour <= $nEnd);
                if ($v->amount >= $maxAmt) {
                    $anomalyAlerts->push((object)[
                        'type' => 'مبلغ يتجاوز سقف التنبيه (' . number_format($maxAmt, 0) . ' ر.س)',
                        'details' => "سند رقم {$v->voucher_number} بمبلغ " . number_format($v->amount, 2) . " ر.س ({$v->notes})",
                        'time' => $v->created_at,
                        'severity' => 'high'
                    ]);
                }
                if ($isNight) {
                    $anomalyAlerts->push((object)[
                        'type' => 'عملية مالية في وقت متأخر من الليل',
                        'details' => "تم إصدار السند {$v->voucher_number} الساعة {$hour}:00 ليلاً",
                        'time' => $v->created_at,
                        'severity' => 'warning'
                    ]);
                }
            }
            if ($office->anomaly_alert_on_delete ?? 1) {
                foreach ($activityLogs->where('action_type', 'حذف') as $delLog) {
                    $anomalyAlerts->push((object)[
                        'type' => 'عملية حذف حساسة في النظام',
                        'details' => $delLog->details . ' بواسطة ' . $delLog->user_name,
                        'time' => $delLog->created_at,
                        'severity' => 'high'
                    ]);
                }
            }
        }

        $stats = [
            'owners_count' => $owners->count(),
            'properties_count' => $properties->count(),
            'units_count' => $units->count(),
            'vacant_units' => $units->where('status', 'vacant')->count(),
            'rented_units' => $units->where('status', 'rented')->count(),
            'active_contracts' => $contracts->where('status', 'active')->count(),
            'total_rent_value' => $contracts->sum('total_rent_amount'),
            'company_due_fees' => $contracts->sum('company_fee_amount'),
            'total_receipts' => $vouchers->where('voucher_type', 'receipt')->sum('amount'),
            'total_payments' => $vouchers->where('voucher_type', 'payment')->sum('amount'),
        ];

        $mullakBuildings = DB::table('mullak_buildings')->where('office_id', $officeId)->orderBy('id', 'desc')->get();
        $mullakMembers = DB::table('mullak_members')
            ->join('mullak_buildings', 'mullak_members.mullak_building_id', '=', 'mullak_buildings.id')
            ->where('mullak_members.office_id', $officeId)
            ->select('mullak_members.*', 'mullak_buildings.association_name')
            ->orderBy('mullak_members.id', 'desc')
            ->get();
        $mullakExpenses = DB::table('mullak_expenses')
            ->join('mullak_buildings', 'mullak_expenses.mullak_building_id', '=', 'mullak_buildings.id')
            ->where('mullak_expenses.office_id', $officeId)
            ->select('mullak_expenses.*', 'mullak_buildings.association_name')
            ->orderBy('mullak_expenses.id', 'desc')
            ->get();

        $auditFindings = $this->runPreClosingAccountingAuditInternal($officeId, $vouchers, $owners, $contracts, $journalEntries);

        // 1. كاشف الوحدات الراكدة (Stagnant Vacancy Alert):
        // يعرض فقط الوحدات الشاغرة (status === 'vacant')، بدون تكرار الوحدة، مع إمكانية تكرار اسم العمارة، مرتبة من الفترة الأكبر نزولاً للأقل
        $propertiesByIdMap = $properties->keyBy('id');
        $seenUnitKeys = [];
        $stagnantVacantUnits = collect();

        foreach ($units as $u) {
            if (($u->status ?? '') !== 'vacant') {
                continue; // استبعاد الوحدات المؤجرة نهائياً
            }
            $uniqueUnitKey = $u->property_id . '|' . mb_strtolower(trim((string) $u->unit_number));
            if (isset($seenUnitKeys[$uniqueUnitKey])) {
                continue; // منع تكرار نفس الوحدة في نفس العمارة
            }
            $seenUnitKeys[$uniqueUnitKey] = true;

            // التحقق أيضاً من عدم وجود عقد نشط ساري حالياً على هذه الوحدة
            $hasActiveContractNow = $contracts->contains(function ($c) use ($u) {
                return (int) ($c->property_unit_id ?? 0) === (int) $u->id && ($c->status ?? '') === 'active' && ($c->end_date ?? '') >= date('Y-m-d');
            });
            if ($hasActiveContractNow) {
                continue;
            }

            $parentProp = $propertiesByIdMap[$u->property_id] ?? null;
            $lastEndedContract = $contracts
                ->where('property_unit_id', $u->id)
                ->sortByDesc('end_date')
                ->first();

            $vacantSinceDate = $lastEndedContract && !empty($lastEndedContract->end_date)
                ? $lastEndedContract->end_date
                : (!empty($u->created_at) ? substr((string) $u->created_at, 0, 10) : (!empty($parentProp->created_at) ? substr((string) $parentProp->created_at, 0, 10) : date('Y-m-d')));

            $daysVacant = max(1, (int) \Carbon\Carbon::parse($vacantSinceDate)->startOfDay()->diffInDays(now()->startOfDay()));

            $addressParts = array_filter([
                $parentProp->city ?? '',
                $parentProp->district ?? '',
                $parentProp->street ?? '',
            ]);

            $stagnantVacantUnits->push((object) [
                'unit_id'            => $u->id,
                'property_id'        => $u->property_id,
                'building_name'      => $parentProp->title ?? ($u->property_title ?? 'عمارة عقارية'),
                'owner_name'         => $parentProp->owner_name ?? '-',
                'unit_number'        => $u->unit_number,
                'floor_number'       => $u->floor_number,
                'area'               => $u->area,
                'default_rent_price' => (float) ($u->default_rent_price ?? 0),
                'address'            => !empty($addressParts) ? implode(' - ', $addressParts) : 'غير محدد',
                'vacant_since'       => $vacantSinceDate,
                'days_vacant'        => $daysVacant,
            ]);
        }

        // ترتيب تنازلي: الفترة الشاغرة الأكبر في الأعلى نزولاً إلى الفترة الأقل
        $stagnantVacantUnits = $stagnantVacantUnits->sortByDesc('days_vacant')->values();

        // 2. جلب بيانات التصويت الإلكتروني لجمعيات اتحاد الملاك
        CompanyErpController::ensureMullakVotingTables();
        $mullakPolls = DB::table('mullak_polls')
            ->join('mullak_buildings', 'mullak_polls.mullak_building_id', '=', 'mullak_buildings.id')
            ->where('mullak_polls.office_id', $officeId)
            ->select('mullak_polls.*', 'mullak_buildings.association_name')
            ->orderByDesc('mullak_polls.id')
            ->get();

        $mullakPollVotes = DB::table('mullak_poll_votes')
            ->whereIn('poll_id', $mullakPolls->pluck('id')->toArray())
            ->get()
            ->groupBy('poll_id');

        // 3. التحقق من تفعيل ميزة تخصيص الهوية البصرية والشعار (White-Labeling) ضمن خيارات الباقة أو المكتب
        $officeAllowWhiteLabel = in_array('white_label', $allowedOfficeTabs, true) || !empty($office->allow_white_label);
        if (!$officeAllowWhiteLabel && !empty($office->plan_id) && \Illuminate\Support\Facades\Schema::hasTable('office_plans')) {
            $planRow = DB::table('office_plans')->where('id', $office->plan_id)->first();
            if ($planRow) {
                $pTabs = json_decode($planRow->allowed_tabs ?? '[]', true) ?: [];
                if (!empty($planRow->allow_white_label) || in_array('white_label', $pTabs, true)) {
                    $officeAllowWhiteLabel = true;
                }
            }
        }
        if ($officeAllowWhiteLabel && !in_array('staff', $allowedOfficeTabs, true)) {
            $allowedOfficeTabs[] = 'staff';
        }

        return view('office_dashboard', compact(
            'office', 'tab', 'allowedOfficeTabs', 'owners', 'propertyTypes', 'allAttributes',
            'properties', 'units', 'tenants', 'contracts', 'accountTypes',
            'accounts', 'vouchers', 'journalEntries', 'journalItems', 'journalItemsByEntry',
            'staff', 'stats', 'defaultTenantParentAcc', 'defaultOwnerParentAcc',
            'companyAdminPhone', 'nextReceiptNo', 'nextPaymentNo',
            'paymentMethods', 'officeInvoices', 'defaultVatRate', 'activityLogs',
            'installments', 'agingBuckets', 'anomalyAlerts',
            'mullakBuildings', 'mullakMembers', 'mullakExpenses', 'auditFindings',
            'stagnantVacantUnits', 'mullakPolls', 'mullakPollVotes', 'officeAllowWhiteLabel'
        ));
    }

    public function storeOwner(Request $request)
    {
        $officeId = $this->getCurrentOfficeId();

        $request->validate([
            'name' => 'required',
            'national_id' => 'required',
            'phone' => ['required', 'regex:/^05[0-9]{8}$/'],
            'username' => 'required|unique:users,username',
        ], [
            'phone.regex' => 'رقم جوال المالك يجب أن يتكون من 10 أرقام ويبدأ بـ 05.',
            'username.unique' => 'اسم المستخدم للمالك مسجل مسبقاً، اختر اسماً آخر.'
        ]);

        $formattedPhone = CompanyErpController::formatSaudiPhone($request->phone);

        DB::transaction(function () use ($request, $officeId, $formattedPhone) {
            $parentAccId = $request->filled('parent_account_id') ? (int) $request->parent_account_id : null;
            if (!$parentAccId) {
                $defaultParent = DB::table('accounts')->where('office_id', $officeId)->where('code', '2101')->first();
                $parentAccId = $defaultParent ? $defaultParent->id : null;
            }

            $linkedAccountId = CompanyErpController::createSequentialSubAccount(
                $officeId,
                $parentAccId,
                'أمانات المالك: ' . trim($request->name)
            );

            $ownerId = DB::table('owners')->insertGetId([
                'office_id' => $officeId,
                'name' => trim($request->name),
                'national_id' => trim($request->national_id),
                'tax_number' => $request->filled('tax_number') ? trim($request->tax_number) : null,
                'national_address' => $request->filled('national_address') ? trim($request->national_address) : null,
                'phone' => $formattedPhone,
                'email' => trim($request->username) . '@owner.local',
                'iban' => trim($request->iban),
                'bank_name' => trim($request->bank_name),
                'office_management_fee_rate' => $request->office_management_fee_rate ?? 5.00,
                'account_id' => $linkedAccountId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('users')->insert([
                'name' => trim($request->name),
                'username' => trim($request->username),
                'phone' => $formattedPhone,
                'email' => trim($request->username) . '@owner.local',
                'national_id' => trim($request->national_id),
                'password' => Hash::make($request->password ?: '123456'),
                'user_type' => 'owner',
                'office_id' => $officeId,
                'owner_id' => $ownerId,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('office.dashboard', ['tab' => 'owners'])
            ->with('success', 'تم تسجيل المالك وربطه بشجرة الحسابات وإنشاء حساب الدخول الخاص به بنجاح.');
    }

    public function updateOwner(Request $request, $id)
    {
        $officeId = $this->getCurrentOfficeId();
        $request->validate([
            'name' => 'required',
            'phone' => ['required', 'regex:/^05[0-9]{8}$/'],
        ]);

        $formattedPhone = CompanyErpController::formatSaudiPhone($request->phone);
        $owner = DB::table('owners')->where('id', $id)->where('office_id', $officeId)->first();

        if ($owner) {
            DB::table('owners')->where('id', $id)->where('office_id', $officeId)->update([
                'name' => trim($request->name),
                'national_id' => trim($request->national_id),
                'tax_number' => $request->has('tax_number') ? (trim($request->tax_number) ?: null) : $owner->tax_number,
                'national_address' => $request->has('national_address') ? (trim($request->national_address) ?: null) : $owner->national_address,
                'phone' => $formattedPhone,
                'iban' => trim($request->iban),
                'bank_name' => trim($request->bank_name),
                'office_management_fee_rate' => $request->office_management_fee_rate ?? 5,
                'updated_at' => now(),
            ]);

            if ($owner->account_id) {
                DB::table('accounts')->where('id', $owner->account_id)->update([
                    'name' => 'أمانات المالك: ' . trim($request->name),
                    'updated_at' => now(),
                ]);
            }

            $userUpdate = [
                'name' => trim($request->name),
                'phone' => $formattedPhone,
                'national_id' => trim($request->national_id),
                'updated_at' => now(),
            ];
            if ($request->filled('username')) {
                $userUpdate['username'] = trim($request->username);
            }
            if ($request->filled('password')) {
                $userUpdate['password'] = Hash::make($request->password);
            }

            DB::table('users')->where('owner_id', $id)->where('user_type', 'owner')->update($userUpdate);
        }

        return redirect()->route('office.dashboard', ['tab' => 'owners'])
            ->with('success', 'تم تحديث بيانات المالك وحسابه بنجاح.');
    }

    public function deleteOwner($id)
    {
        $officeId = $this->getCurrentOfficeId();
        DB::table('owners')->where('id', $id)->where('office_id', $officeId)->delete();
        return redirect()->route('office.dashboard', ['tab' => 'owners'])
            ->with('success', 'تم حذف سجل المالك.');
    }

    public function storeProperty(Request $request)
    {
        $officeId = $this->getCurrentOfficeId();

        $request->validate([
            'owner_id' => 'required',
            'property_type_id' => 'required',
            'title' => 'required',
            'deed_number' => 'required',
            'total_area' => 'required|numeric',
            'city' => 'required',
            'district' => 'required',
            'custom_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,gif|max:5120',
        ], [
            'custom_files.*.mimes' => 'يجب أن يكون المرفق بصيغة PDF أو صورة معروفة (JPG, PNG, WEBP, GIF).',
            'custom_files.*.max' => 'حجم الملف المرفق يجب ألا يتجاوز 5 ميغابايت (5MB).',
        ]);

        $ownerExists = DB::table('owners')->where('id', $request->owner_id)->where('office_id', $officeId)->exists();
        if (!$ownerExists) {
            return back()->withErrors(['المالك المحدد غير مسجل ضمن هذا المكتب.']);
        }

        $customAttrs = $request->input('custom_attrs', []);
        $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'webp', 'gif'];

        if ($request->hasFile('custom_files')) {
            $uploadDir = public_path('uploads/properties');
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0775, true);
            }
            foreach ($request->file('custom_files') as $attrKey => $fileObj) {
                if ($fileObj && $fileObj->isValid()) {
                    $guessedExt = strtolower($fileObj->extension() ?: $fileObj->getClientOriginalExtension());
                    if (!in_array($guessedExt, $allowedExtensions, true)) {
                        continue;
                    }
                    $fileName = 'prop_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $guessedExt;
                    $fileObj->move($uploadDir, $fileName);
                    $customAttrs[$attrKey] = '/uploads/properties/' . $fileName;
                }
            }
        }

        DB::table('properties')->insert([
            'office_id' => $officeId,
            'owner_id' => $request->owner_id,
            'property_type_id' => $request->property_type_id,
            'title' => trim($request->title),
            'deed_number' => trim($request->deed_number),
            'deed_date' => $request->deed_date ?: null,
            'total_area' => $request->total_area,
            'estimated_value' => $request->estimated_value ?: 0,
            'city' => trim($request->city),
            'district' => trim($request->district),
            'street' => trim($request->street),
            'building_number' => trim($request->building_number),
            'postal_code' => trim($request->postal_code),
            'additional_number' => trim($request->additional_number),
            'custom_attributes' => json_encode($customAttrs, JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('office.dashboard', ['tab' => 'properties'])
            ->with('success', 'تم تسجيل العقار وبيانات الصك والمرفقات بنجاح.');
    }

    public function updateProperty(Request $request, $id)
    {
        $officeId = $this->getCurrentOfficeId();
        $prop = DB::table('properties')->where('id', $id)->where('office_id', $officeId)->first();
        if (!$prop) {
            return back()->withErrors(['العقار غير موجود.']);
        }

        $request->validate([
            'owner_id' => 'required',
            'property_type_id' => 'required',
            'title' => 'required',
            'deed_number' => 'required',
            'total_area' => 'required|numeric',
            'city' => 'required',
            'district' => 'required',
        ]);

        $ownerExists = DB::table('owners')->where('id', $request->owner_id)->where('office_id', $officeId)->exists();
        if (!$ownerExists) {
            return back()->withErrors(['المالك المحدد غير مسجل ضمن هذا المكتب.']);
        }

        DB::table('properties')->where('id', $id)->where('office_id', $officeId)->update([
            'owner_id' => $request->owner_id,
            'property_type_id' => $request->property_type_id,
            'title' => trim($request->title),
            'deed_number' => trim($request->deed_number),
            'deed_date' => $request->deed_date ?: $prop->deed_date,
            'total_area' => $request->total_area,
            'estimated_value' => $request->estimated_value ?: 0,
            'city' => trim($request->city),
            'district' => trim($request->district),
            'street' => trim($request->street),
            'building_number' => trim($request->building_number),
            'postal_code' => trim($request->postal_code),
            'updated_at' => now(),
        ]);

        return redirect()->route('office.dashboard', ['tab' => 'properties'])
            ->with('success', 'تم تعديل بيانات العقار والصك بنجاح.');
    }

    public function deleteProperty($id)
    {
        $officeId = $this->getCurrentOfficeId();
        $prop = DB::table('properties')->where('id', $id)->where('office_id', $officeId)->first();
        if ($prop) {
            DB::table('property_units')->where('property_id', $prop->id)->delete();
            DB::table('properties')->where('id', $prop->id)->where('office_id', $officeId)->delete();
        }
        return redirect()->route('office.dashboard', ['tab' => 'properties'])
            ->with('success', 'تم حذف العقار بنجاح.');
    }

    public function storeUnit(Request $request)
    {
        $officeId = $this->getCurrentOfficeId();
        $request->validate([
            'property_id' => 'required',
            'unit_number' => 'required',
            'default_rent_price' => 'required|numeric',
        ]);

        $property = DB::table('properties')
            ->where('id', $request->property_id)
            ->where('office_id', $officeId)
            ->first();

        if (!$property) {
            return back()->withErrors(['العقار المحدد غير موجود ضمن هذا المكتب.']);
        }

        DB::table('property_units')->insert([
            'property_id' => $property->id,
            'property_type_id' => $property->property_type_id,
            'unit_number' => trim($request->unit_number),
            'floor_number' => $request->floor_number ?: 0,
            'area' => $request->area ?: 0,
            'electricity_meter' => trim($request->electricity_meter),
            'water_meter' => trim($request->water_meter),
            'default_rent_price' => $request->default_rent_price,
            'status' => 'vacant',
            'custom_attributes' => json_encode([
                'rooms' => $request->rooms ?? '',
                'unit_kind' => $request->unit_kind ?? 'شقة',
            ], JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('office.dashboard', ['tab' => 'properties'])
            ->with('success', 'تم إضافة الوحدة العقارية (' . $request->unit_number . ') بنجاح.');
    }

    public function updateUnit(Request $request, $id)
    {
        $officeId = $this->getCurrentOfficeId();
        $unit = DB::table('property_units')
            ->join('properties', 'property_units.property_id', '=', 'properties.id')
            ->where('property_units.id', $id)
            ->where('properties.office_id', $officeId)
            ->select('property_units.*')
            ->first();

        if ($unit) {
            DB::table('property_units')->where('id', $unit->id)->update([
                'unit_number' => trim($request->unit_number),
                'floor_number' => $request->floor_number ?: 0,
                'area' => $request->area ?: 0,
                'electricity_meter' => trim($request->electricity_meter),
                'water_meter' => trim($request->water_meter),
                'default_rent_price' => $request->default_rent_price ?: 0,
                'status' => $request->status ?: $unit->status,
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('office.dashboard', ['tab' => 'properties'])
            ->with('success', 'تم تعديل بيانات الوحدة العقارية بنجاح.');
    }

    public function deleteUnit($id)
    {
        $officeId = $this->getCurrentOfficeId();
        $unit = DB::table('property_units')
            ->join('properties', 'property_units.property_id', '=', 'properties.id')
            ->where('property_units.id', $id)
            ->where('properties.office_id', $officeId)
            ->select('property_units.id')
            ->first();

        if ($unit) {
            DB::table('property_units')->where('id', $unit->id)->delete();
        }
        return redirect()->route('office.dashboard', ['tab' => 'properties'])
            ->with('success', 'تم حذف الوحدة العقارية.');
    }

    public function storeTenant(Request $request)
    {
        $officeId = $this->getCurrentOfficeId();

        $request->validate([
            'name' => 'required',
            'id_number' => 'required',
            'phone' => ['required', 'regex:/^05[0-9]{8}$/'],
        ], [
            'phone.regex' => 'رقم جوال المستأجر يجب أن يتكون من 10 أرقام ويبدأ بـ 05.'
        ]);

        $formattedPhone = CompanyErpController::formatSaudiPhone($request->phone);

        DB::transaction(function () use ($request, $officeId, $formattedPhone) {
            $parentAccId = null;
            if ($request->filled('parent_account_id')) {
                $validParent = DB::table('accounts')->where('office_id', $officeId)->where('id', (int) $request->parent_account_id)->first();
                $parentAccId = $validParent ? $validParent->id : null;
            }
            if (!$parentAccId) {
                $defaultParent = DB::table('accounts')->where('office_id', $officeId)->where('code', '1103')->first();
                $parentAccId = $defaultParent ? $defaultParent->id : null;
            }

            $linkedAccountId = CompanyErpController::createSequentialSubAccount(
                $officeId,
                $parentAccId,
                'المستأجر: ' . trim($request->name)
            );

            DB::table('tenants')->insert([
                'office_id' => $officeId,
                'name' => trim($request->name),
                'id_type' => $request->id_type ?: 'national_id',
                'id_number' => trim($request->id_number),
                'tax_number' => $request->filled('tax_number') ? trim($request->tax_number) : null,
                'national_address' => $request->filled('national_address') ? trim($request->national_address) : null,
                'phone' => $formattedPhone,
                'email' => null,
                'account_id' => $linkedAccountId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('office.dashboard', ['tab' => 'contracts'])
            ->with('success', 'تم تسجيل المستأجر وربطه تلقائياً في شجرة حسابات المكتب بالتسلسل.');
    }

    public function updateTenant(Request $request, $id)
    {
        $officeId = $this->getCurrentOfficeId();
        $formattedPhone = CompanyErpController::formatSaudiPhone($request->phone);

        $tenant = DB::table('tenants')->where('id', $id)->where('office_id', $officeId)->first();
        if ($tenant) {
            $accountId = $tenant->account_id;
            if (!$accountId && $request->filled('parent_account_id')) {
                $validParent = DB::table('accounts')->where('office_id', $officeId)->where('id', (int) $request->parent_account_id)->first();
                $accountId = CompanyErpController::createSequentialSubAccount(
                    $officeId,
                    $validParent ? $validParent->id : null,
                    'المستأجر: ' . trim($request->name)
                );
            } elseif ($accountId) {
                DB::table('accounts')->where('id', $accountId)->where('office_id', $officeId)->update(['name' => 'المستأجر: ' . trim($request->name)]);
            }

            DB::table('tenants')->where('id', $id)->where('office_id', $officeId)->update([
                'name' => trim($request->name),
                'id_type' => $request->id_type ?: $tenant->id_type,
                'id_number' => trim($request->id_number),
                'tax_number' => $request->has('tax_number') ? (trim($request->tax_number) ?: null) : $tenant->tax_number,
                'national_address' => $request->has('national_address') ? (trim($request->national_address) ?: null) : $tenant->national_address,
                'phone' => $formattedPhone,
                'account_id' => $accountId,
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('office.dashboard', ['tab' => 'contracts'])
            ->with('success', 'تم تحديث بيانات المستأجر بنجاح.');
    }

    public function deleteTenant($id)
    {
        $officeId = $this->getCurrentOfficeId();
        DB::table('tenants')->where('id', $id)->where('office_id', $officeId)->delete();
        return redirect()->route('office.dashboard', ['tab' => 'contracts'])
            ->with('success', 'تم حذف المستأجر.');
    }

    public function storeContract(Request $request)
    {
        $officeId = $this->getCurrentOfficeId();
        $office = DB::table('offices')->where('id', $officeId)->first();

        $request->validate([
            'property_id' => 'required',
            'tenant_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'total_rent_amount' => 'required|numeric',
        ]);

        $property = DB::table('properties')->where('id', $request->property_id)->where('office_id', $officeId)->first();
        $tenant = DB::table('tenants')->where('id', $request->tenant_id)->where('office_id', $officeId)->first();
        if (!$property || !$tenant) {
            return back()->withErrors(['العقار أو المستأجر غير مسجل ضمن هذا المكتب.']);
        }

        $unitId = null;
        if ($request->filled('property_unit_id')) {
            $unit = DB::table('property_units')->where('id', $request->property_unit_id)->where('property_id', $property->id)->first();
            $unitId = $unit ? $unit->id : null;
        }

        $owner = DB::table('owners')->where('id', $property->owner_id)->where('office_id', $officeId)->first();

        $companyFee = 0;
        if (in_array($office->billing_type, ['per_property_percentage', 'hybrid']) && $office->company_commission_rate > 0) {
            $companyFee = ($request->total_rent_amount * $office->company_commission_rate) / 100;
        }

        $officeCommission = $request->filled('office_commission')
            ? $request->office_commission
            : (($request->total_rent_amount * ($owner->office_management_fee_rate ?? 5)) / 100);

        CompanyErpController::ensureContractInstallmentsTable();
        $contractNumber = 'CNT-' . date('Y') . '-' . random_int(10000, 99999);

        $graceMonths = $request->has('enable_grace_period') ? max(0, (int) ($request->grace_period_months ?? 0)) : 0;
        $graceDays   = $request->has('enable_grace_period') ? max(0, (int) ($request->grace_period_days ?? 0)) : 0;
        $installmentsStartDate = \Carbon\Carbon::parse($request->start_date)
            ->addMonths($graceMonths)
            ->addDays($graceDays)
            ->toDateString();

        DB::transaction(function () use ($request, $officeId, $property, $tenant, $unitId, $contractNumber, $officeCommission, $companyFee, $graceMonths, $graceDays, $installmentsStartDate) {
            $newContractId = DB::table('lease_contracts')->insertGetId([
                'contract_number' => $contractNumber,
                'ejar_contract_number' => $request->ejar_contract_number ?: null,
                'ejar_status' => $request->filled('ejar_contract_number') ? 'active' : 'not_sent',
                'office_id' => $officeId,
                'property_id' => $property->id,
                'property_unit_id' => $unitId,
                'owner_id' => $property->owner_id,
                'tenant_id' => $tenant->id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'grace_period_months' => $graceMonths,
                'grace_period_days' => $graceDays,
                'installments_start_date' => $installmentsStartDate,
                'payment_cycle' => $request->payment_cycle ?: 'annual',
                'total_rent_amount' => $request->total_rent_amount,
                'security_deposit' => $request->security_deposit ?: 0,
                'office_commission' => $officeCommission,
                'company_fee_amount' => $companyFee,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($unitId) {
                DB::table('property_units')->where('id', $unitId)->update(['status' => 'rented']);
            }

            $contractObj = DB::table('lease_contracts')->where('id', $newContractId)->first();
            if ($contractObj) {
                CompanyErpController::generateInstallmentsForContract($contractObj);
            }
        });

        $graceNote = ($graceMonths > 0 || $graceDays > 0)
            ? " مع منح المستأجر فترة سماح وتجهيز مجانية (يبدأ جدول الدفعات المحاسبي بتاريخ {$installmentsStartDate})."
            : ".";

        return redirect()->route('office.dashboard', ['tab' => 'contracts'])
            ->with('success', 'تم إبرام عقد الإيجار رقم (' . $contractNumber . ') بنجاح' . $graceNote);
    }

    public function updateContract(Request $request, $id)
    {
        $officeId = $this->getCurrentOfficeId();
        $office = DB::table('offices')->where('id', $officeId)->first();
        $contract = DB::table('lease_contracts')->where('id', $id)->where('office_id', $officeId)->first();

        if (!$contract) {
            return back()->withErrors(['العقد غير موجود.']);
        }

        $property = DB::table('properties')->where('id', $request->property_id ?: $contract->property_id)->where('office_id', $officeId)->first();
        if (!$property) {
            return back()->withErrors(['العقار غير موجود ضمن هذا المكتب.']);
        }

        $owner = DB::table('owners')->where('id', $property->owner_id)->where('office_id', $officeId)->first();
        $tenantId = $contract->tenant_id;
        if ($request->filled('tenant_id')) {
            $validTenant = DB::table('tenants')->where('id', $request->tenant_id)->where('office_id', $officeId)->first();
            if ($validTenant) {
                $tenantId = $validTenant->id;
            }
        }

        $unitId = null;
        if ($request->filled('property_unit_id')) {
            $validUnit = DB::table('property_units')->where('id', $request->property_unit_id)->where('property_id', $property->id)->first();
            $unitId = $validUnit ? $validUnit->id : null;
        }

        $totalRent = (float) ($request->total_rent_amount ?? $contract->total_rent_amount);
        $companyFee = 0;
        if (in_array($office->billing_type, ['per_property_percentage', 'hybrid']) && $office->company_commission_rate > 0) {
            $companyFee = ($totalRent * $office->company_commission_rate) / 100;
        }

        $officeCommission = $request->filled('office_commission')
            ? (float) $request->office_commission
            : (($totalRent * ($owner->office_management_fee_rate ?? 5)) / 100);

        DB::table('lease_contracts')->where('id', $id)->where('office_id', $officeId)->update([
            'property_id' => $property->id,
            'property_unit_id' => $unitId,
            'owner_id' => $property->owner_id,
            'tenant_id' => $tenantId,
            'start_date' => $request->start_date ?: $contract->start_date,
            'end_date' => $request->end_date ?: $contract->end_date,
            'payment_cycle' => $request->payment_cycle ?: $contract->payment_cycle,
            'total_rent_amount' => $totalRent,
            'security_deposit' => $request->security_deposit ?? $contract->security_deposit,
            'office_commission' => $officeCommission,
            'company_fee_amount' => $companyFee,
            'ejar_contract_number' => $request->ejar_contract_number ?: $contract->ejar_contract_number,
            'ejar_status' => $request->filled('ejar_contract_number') ? 'active' : $contract->ejar_status,
            'status' => $request->status ?: $contract->status,
            'updated_at' => now(),
        ]);

        return redirect()->route('office.dashboard', ['tab' => 'contracts'])
            ->with('success', 'تم تعديل بيانات عقد الإيجار رقم (' . $contract->contract_number . ') بنجاح.');
    }

    public function deleteContract($id)
    {
        $officeId = $this->getCurrentOfficeId();
        $contract = DB::table('lease_contracts')->where('id', $id)->where('office_id', $officeId)->first();
        if ($contract) {
            if ($contract->property_unit_id) {
                DB::table('property_units')->where('id', $contract->property_unit_id)->update(['status' => 'vacant']);
            }
            DB::table('contract_installments')->where('lease_contract_id', $contract->id)->where('office_id', $officeId)->delete();
            DB::table('lease_contracts')->where('id', $id)->where('office_id', $officeId)->delete();
        }
        return redirect()->route('office.dashboard', ['tab' => 'contracts'])
            ->with('success', 'تم حذف عقد الإيجار.');
    }

    public function syncWithEjar($id)
    {
        $officeId = $this->getCurrentOfficeId();
        $ejarUnifiedNo = 'EJR-' . rand(1000000000, 9999999999);

        DB::table('lease_contracts')
            ->where('id', $id)
            ->where('office_id', $officeId)
            ->update([
                'ejar_contract_number' => $ejarUnifiedNo,
                'ejar_status' => 'active',
                'updated_at' => now(),
            ]);

        return redirect()->route('office.dashboard', ['tab' => 'contracts'])
            ->with('success', 'تم إرسال وتوثيق العقد في شبكة إيجار برقم موحد: (' . $ejarUnifiedNo . ')');
    }

    public function storeOfficeAccountType(Request $request)
    {
        $request->validate(['name' => 'required']);
        DB::table('custom_account_types')->insert([
            'name' => trim($request->name),
            'code_prefix' => $request->code_prefix ?: (string) (DB::table('custom_account_types')->count() + 1),
            'color' => $request->color ?: 'emerald',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return redirect()->route('office.dashboard', ['tab' => 'accounting'])->with('success', 'تم إضافة نوع الحساب الجديد بنجاح.');
    }

    public function updateOfficeAccountType(Request $request, $id)
    {
        $officeId = $this->getCurrentOfficeId();
        $old = DB::table('custom_account_types')->where('id', $id)->first();
        if ($old) {
            DB::table('accounts')->where('office_id', $officeId)->where('type', $old->name)->update(['type' => trim($request->name)]);
            DB::table('custom_account_types')->where('id', $id)->update([
                'name' => trim($request->name),
                'code_prefix' => $request->code_prefix ?: $old->code_prefix,
                'updated_at' => now(),
            ]);
        }
        return redirect()->route('office.dashboard', ['tab' => 'accounting'])->with('success', 'تم تعديل نوع الحساب بنجاح.');
    }

    public function deleteOfficeAccountType($id)
    {
        DB::table('custom_account_types')->where('id', $id)->delete();
        return redirect()->route('office.dashboard', ['tab' => 'accounting'])->with('success', 'تم حذف نوع الحساب.');
    }

    private function seedOfficeAccountsInternal(int $officeId): void
    {
        if (DB::table('accounts')->where('office_id', $officeId)->count() > 0) {
            return;
        }

        $tree = [
            [
                'code' => '1000', 'name' => 'الأصول (صناديق وبنوك المكتب)', 'type' => 'الأصول',
                'children' => [
                    ['code' => '1101', 'name' => 'صندوق وخزينة المكتب', 'is_group' => 0],
                    ['code' => '1102', 'name' => 'حساب المكتب البنكي (تحصيل الإيجارات)', 'is_group' => 0],
                    ['code' => '1103', 'name' => 'ذمم المستأجرين (حسابات المستأجرين)', 'is_group' => 1],
                ]
            ],
            [
                'code' => '2000', 'name' => 'الخصوم والأمانات', 'type' => 'الخصوم والالتزامات',
                'children' => [
                    ['code' => '2101', 'name' => 'حساب أمانات الملاك (صافي الإيجارات)', 'is_group' => 1],
                    ['code' => '2102', 'name' => 'تأمينات المستأجرين المستردة', 'is_group' => 0],
                    ['code' => '2103', 'name' => 'مستحقات الشركة الأم (نسب العقود والاشتراك)', 'is_group' => 0],
                ]
            ],
            [
                'code' => '4000', 'name' => 'إيرادات المكتب العقاري', 'type' => 'الإيرادات',
                'children' => [
                    ['code' => '4101', 'name' => 'إيرادات عمولات إدارة الأملاك', 'is_group' => 0],
                    ['code' => '4102', 'name' => 'إيرادات السعي وتوثيق عقود إيجار', 'is_group' => 0],
                ]
            ],
            [
                'code' => '5000', 'name' => 'مصروفات المكتب والصيانة', 'type' => 'المصروفات التشغيلية',
                'children' => [
                    ['code' => '5101', 'name' => 'مصروفات صيانة العقارات والوحدات', 'is_group' => 0],
                    ['code' => '5102', 'name' => 'مصروفات رواتب موظفي المكتب', 'is_group' => 0],
                ]
            ],
        ];

        foreach ($tree as $root) {
            $parentId = DB::table('accounts')->insertGetId([
                'office_id' => $officeId,
                'parent_id' => null,
                'code' => $root['code'],
                'name' => $root['name'],
                'type' => $root['type'],
                'is_group' => 1,
                'balance' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($root['children'] as $child) {
                DB::table('accounts')->insert([
                    'office_id' => $officeId,
                    'parent_id' => $parentId,
                    'code' => $child['code'],
                    'name' => $child['name'],
                    'type' => $root['type'],
                    'is_group' => $child['is_group'],
                    'balance' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function seedOfficeAccounts()
    {
        $officeId = $this->getCurrentOfficeId();
        $this->seedOfficeAccountsInternal($officeId);

        return redirect()->route('office.dashboard', ['tab' => 'accounting'])
            ->with('success', 'تم تأسيس شجرة الحسابات العقارية المتفرعة الخاصة بالمكتب بنجاح.');
    }

    public function storeOfficeAccount(Request $request)
    {
        $officeId = $this->getCurrentOfficeId();
        $parentId = $request->parent_id ?: null;
        $parent = $parentId ? DB::table('accounts')->where('office_id', $officeId)->where('id', $parentId)->first() : null;
        $type = $parent ? $parent->type : ($request->type ?: 'الأصول');

        $names = $request->input('names', [$request->name]);
        $codes = $request->input('codes', [$request->code]);
        $isGroups = $request->input('is_groups', [$request->has('is_group') ? 1 : 0]);

        foreach ($names as $idx => $accName) {
            if (empty(trim($accName))) continue;

            $code = trim($codes[$idx] ?? '');
            if (empty($code)) {
                if ($parent) {
                    $siblingsCount = DB::table('accounts')->where('office_id', $officeId)->where('parent_id', $parent->id)->count();
                    $code = $parent->code . str_pad($siblingsCount + 1, 2, '0', STR_PAD_LEFT);
                } else {
                    $rootsCount = DB::table('accounts')->where('office_id', $officeId)->whereNull('parent_id')->count();
                    $code = ($rootsCount + 1) . '000';
                }
            }

            DB::table('accounts')->insert([
                'office_id' => $officeId,
                'parent_id' => $parentId,
                'code' => $code,
                'name' => trim($accName),
                'type' => $type,
                'is_group' => isset($isGroups[$idx]) && $isGroups[$idx] == 1 ? 1 : 0,
                'balance' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('office.dashboard', ['tab' => 'accounting'])
            ->with('success', 'تمت إضافة الحسابات والفروع إلى شجرة المكتب بنجاح.');
    }

    public function updateOfficeAccount(Request $request, $id)
    {
        $officeId = $this->getCurrentOfficeId();
        $acc = DB::table('accounts')->where('office_id', $officeId)->where('id', $id)->first();
        if (!$acc) return back();

        $isAdmin = (Auth::user()->user_type === 'company_admin');

        $debit = ($isAdmin && $request->filled('debit_balance')) ? (float) $request->debit_balance : ($acc->debit_balance ?? 0);
        $credit = ($isAdmin && $request->filled('credit_balance')) ? (float) $request->credit_balance : ($acc->credit_balance ?? 0);
        $netBalance = ($isAdmin && ($request->filled('debit_balance') || $request->filled('credit_balance')))
            ? ($debit - $credit)
            : ($acc->balance ?? 0);

        DB::table('accounts')->where('office_id', $officeId)->where('id', $id)->update([
            'code' => trim($request->code),
            'name' => trim($request->name),
            'type' => $request->type,
            'parent_id' => $request->parent_id ?: null,
            'is_group' => $request->has('is_group') ? 1 : 0,
            'debit_balance' => $debit,
            'credit_balance' => $credit,
            'balance' => $netBalance,
            'updated_at' => now(),
        ]);

        DB::table('accounts')->where('office_id', $officeId)->where('parent_id', $id)->update(['type' => $request->type]);

        return redirect()->route('office.dashboard', ['tab' => 'accounting'])
            ->with('success', 'تم تحديث بيانات الحساب في شجرة المكتب بنجاح.');
    }

    public function deleteOfficeAccount($id)
    {
        $officeId = $this->getCurrentOfficeId();
        $hasChildren = DB::table('accounts')->where('office_id', $officeId)->where('parent_id', $id)->exists();

        if ($hasChildren) {
            return redirect()->route('office.dashboard', ['tab' => 'accounting'])
                ->withErrors(['لا يمكن حذف مجلد رئيسي بداخله حسابات فرعية، قم بحذف أو نقل الفروع أولاً.']);
        }

        DB::table('accounts')->where('office_id', $officeId)->where('id', $id)->delete();

        return redirect()->route('office.dashboard', ['tab' => 'accounting'])
            ->with('success', 'تم حذف الحساب من شجرة المكتب بنجاح.');
    }

    public function storeOfficePaymentMethod(Request $request)
    {
        $officeId = $this->getCurrentOfficeId();
        app(CompanyErpController::class)->ensurePaymentMethodsTable();
        $request->validate(['name' => 'required']);

        $validAccId = null;
        if ($request->filled('account_id')) {
            $accRow = DB::table('accounts')->where('office_id', $officeId)->where('id', (int) $request->account_id)->first();
            $validAccId = $accRow ? $accRow->id : null;
        }

        DB::table('custom_payment_methods')->insert([
            'office_id' => $officeId,
            'account_id' => $validAccId,
            'name' => trim($request->name),
            'code' => trim($request->name),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return back()->with('success', 'تم إضافة طريقة الدفع وربطها برقم الحساب في المكتب بنجاح.');
    }

    public function updateOfficePaymentMethod(Request $request, $id)
    {
        $officeId = $this->getCurrentOfficeId();
        $old = DB::table('custom_payment_methods')->where('id', $id)->where('office_id', $officeId)->first();
        if ($old) {
            $validAccId = null;
            if ($request->filled('account_id')) {
                $accRow = DB::table('accounts')->where('office_id', $officeId)->where('id', (int) $request->account_id)->first();
                $validAccId = $accRow ? $accRow->id : null;
            }
            DB::table('custom_payment_methods')->where('id', $id)->where('office_id', $officeId)->update([
                'name' => trim($request->name),
                'code' => trim($request->name),
                'account_id' => $validAccId,
                'updated_at' => now(),
            ]);
        }
        return back()->with('success', 'تم تعديل طريقة الدفع وحسابها المرتبط بنجاح.');
    }

    public function deleteOfficePaymentMethod($id)
    {
        $officeId = $this->getCurrentOfficeId();
        DB::table('custom_payment_methods')->where('id', $id)->where('office_id', $officeId)->delete();
        return back()->with('success', 'تم حذف طريقة الدفع.');
    }

    public function storeOfficeVoucher(Request $request)
    {
        app(CompanyErpController::class)->ensureDefaultAdminExists();
        $officeId = $this->getCurrentOfficeId();
        $office = DB::table('offices')->where('id', $officeId)->first();
        $vType = $request->voucher_type ?: 'receipt';
        $voucherNumber = CompanyErpController::generateNextVoucherNumber($vType, $officeId);

        $ownerId = null;
        if ($request->filled('owner_id')) {
            $validOwner = DB::table('owners')->where('id', (int) $request->owner_id)->where('office_id', $officeId)->first();
            $ownerId = $validOwner ? (int) $validOwner->id : null;
        }

        $tenantId = null;
        if ($request->filled('tenant_id')) {
            $validTenant = DB::table('tenants')->where('id', (int) $request->tenant_id)->where('office_id', $officeId)->first();
            $tenantId = $validTenant ? (int) $validTenant->id : null;
        }

        $contractId = $request->filled('lease_contract_id') ? (int) $request->lease_contract_id : null;
        $propertyId = $request->filled('property_id') ? (int) $request->property_id : null;
        $unitId = $request->filled('property_unit_id') ? (int) $request->property_unit_id : null;
        $installmentId = $request->filled('installment_id') ? (int) $request->installment_id : null;

        // 1. إذا تم تحديد المستأجر، نتعرف تلقائياً على عقده، العقار، الوحدة، والمالك
        if ($tenantId) {
            $activeContract = DB::table('lease_contracts')
                ->where('office_id', $officeId)
                ->where('tenant_id', $tenantId)
                ->orderBy('id', 'desc')
                ->first();
            if ($activeContract) {
                $ownerId = $ownerId ?: (int) $activeContract->owner_id;
                $contractId = $contractId ?: (int) $activeContract->id;
                $propertyId = $propertyId ?: (int) $activeContract->property_id;
                $unitId = $unitId ?: ($activeContract->property_unit_id ? (int) $activeContract->property_unit_id : null);
            }
        }

        // 2. إذا تم تحديد العقار ولم يحدد المالك، نتعرف على المالك من العقار تلقائياً
        if ($propertyId) {
            $propRow = DB::table('properties')->where('id', $propertyId)->where('office_id', $officeId)->first();
            if ($propRow) {
                $ownerId = $ownerId ?: (int) $propRow->owner_id;
            } else {
                $propertyId = null;
            }
        }

        // 3. تحديد الحساب المدين والدائن تلقائياً من شجرة هذا المكتب حصراً
        $defaultCashAcc = DB::table('accounts')->where('office_id', $officeId)->where('code', '1102')->first()
                       ?: DB::table('accounts')->where('office_id', $officeId)->where('is_group', 0)->first();
        $fallbackAccId = $defaultCashAcc ? (int) $defaultCashAcc->id : 1;

        $debitAccId = $fallbackAccId;
        if ($request->filled('debit_account_id')) {
            $dRow = DB::table('accounts')->where('office_id', $officeId)->where('id', (int) $request->debit_account_id)->first();
            if ($dRow) $debitAccId = (int) $dRow->id;
        }

        $creditAccId = $fallbackAccId;
        if ($request->filled('credit_account_id')) {
            $cRow = DB::table('accounts')->where('office_id', $officeId)->where('id', (int) $request->credit_account_id)->first();
            if ($cRow) $creditAccId = (int) $cRow->id;
        }

        if ($vType === 'receipt' && $tenantId) {
            $tRow = DB::table('tenants')->where('id', $tenantId)->where('office_id', $officeId)->first();
            if ($tRow && $tRow->account_id) {
                $creditAccId = (int) $tRow->account_id;
            }
        } elseif ($vType === 'payment' && $ownerId) {
            $oRow = DB::table('owners')->where('id', $ownerId)->where('office_id', $officeId)->first();
            if ($oRow && $oRow->account_id) {
                $debitAccId = (int) $oRow->account_id;
            }
        }

        $amount = (float) ($request->amount ?? 0);
        if ($amount <= 0) {
            return back()->withErrors(['يرجى إدخال مبلغ صحيح أكبر من الصفر.']);
        }

        // فحص هل سند الصرف يتطلب اعتماد صاحب المكتب عبر الواتساب؟
        $needsWaApproval = false;
        $approvalCode = (string) random_int(1000, 9999);
        if (
            $vType === 'payment' &&
            !empty($office->enable_wa_voucher_approval) &&
            $amount >= (float) ($office->wa_approval_threshold ?? 10000) &&
            Auth::user()->user_type !== 'office_admin' &&
            Auth::user()->user_type !== 'company_admin'
        ) {
            $needsWaApproval = true;
        }

        $newVoucherId = null;
        DB::transaction(function () use (
            $request, $officeId, $voucherNumber, $vType, $debitAccId, $creditAccId,
            $ownerId, $tenantId, $contractId, $propertyId, $unitId, $installmentId,
            $amount, $needsWaApproval, $approvalCode, &$newVoucherId
        ) {
            $payload = [
                'voucher_number' => $voucherNumber,
                'voucher_type' => $vType,
                'office_id' => $officeId,
                'debit_account_id' => $debitAccId,
                'credit_account_id' => $creditAccId,
                'owner_id' => $ownerId,
                'tenant_id' => $tenantId,
                'lease_contract_id' => $contractId,
                'amount' => $amount,
                'payment_method' => CompanyErpController::normalizeVoucherPaymentMethod($request->payment_method),
                'voucher_date' => $request->voucher_date ?: date('Y-m-d'),
                'notes' => trim($request->notes ?: 'سند مالي'),
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (\Illuminate\Support\Facades\Schema::hasColumn('vouchers', 'property_id')) {
                $payload['property_id'] = $propertyId;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('vouchers', 'property_unit_id')) {
                $payload['property_unit_id'] = $unitId;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('vouchers', 'installment_id')) {
                $payload['installment_id'] = $installmentId;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('vouchers', 'approval_status')) {
                $payload['approval_status'] = $needsWaApproval ? 'pending' : 'approved';
                $payload['approval_code'] = $needsWaApproval ? $approvalCode : null;
            }

            $newVoucherId = DB::table('vouchers')->insertGetId($payload);

            if (!$needsWaApproval) {
                CompanyErpController::applyAccountMovement($debitAccId, $amount, 0);
                CompanyErpController::applyAccountMovement($creditAccId, 0, $amount);

                if ($vType === 'receipt' && $ownerId) {
                    $ownerObj = DB::table('owners')->where('id', $ownerId)->where('office_id', $officeId)->first();
                    if ($ownerObj && $ownerObj->account_id && (int)$ownerObj->account_id !== $creditAccId) {
                        CompanyErpController::applyAccountMovement($creditAccId, $amount, 0);
                        CompanyErpController::applyAccountMovement((int) $ownerObj->account_id, 0, $amount);
                    }
                }

                if ($vType === 'receipt' && ($installmentId || $tenantId)) {
                    $this->allocateReceiptToInstallments($officeId, $tenantId, $installmentId, $amount, $newVoucherId);
                }
            }
        });

        if ($needsWaApproval && !empty($office->phone)) {
            $keywordsHint = $office->wa_approval_keywords ?: 'اعتمد, تم, اوك';
            $waMsg = "🔔 *طلب اعتماد سند صرف*\nرقم السند: *{$voucherNumber}*\nالمبلغ: *" . number_format($amount, 2) . " ر.س*\nالبيان: {$request->notes}\nكود السند: *{$approvalCode}*\n\nأرسل رد بكلمة: *({$keywordsHint})* لاعتماده.";
            CompanyErpController::dispatchWhatsappMessage($office->phone, $waMsg, $officeId);

            return redirect()->route('office.dashboard', ['tab' => 'vouchers'])
                ->with('success', "تم حفظ سند الصرف رقم ({$voucherNumber}) بانتظار الاعتماد عبر الواتساب.");
        }

        if ($request->filled('stay_in_accounting')) {
            return redirect()->route('office.dashboard', ['tab' => 'accounting'])
                ->with('success', 'تم إصدار السند المالي رقم (' . $voucherNumber . ') وتحديث رصيد الحساب في شجرة المكتب فوراً.');
        }

        if ($request->filled('stay_in_installments')) {
            return redirect()->route('office.dashboard', ['tab' => 'installments'])
                ->with('success', 'تم تسجيل سداد الدفعة بالسند رقم (' . $voucherNumber . ') وتحديث حالة القسط تلقائياً.');
        }

        return redirect()->route('office.dashboard', ['tab' => 'vouchers'])
            ->with('success', 'تم إصدار السند الرسمي للمكتب رقم (' . $voucherNumber . ') وربطه بالمالك والعقار والشجرة بنجاح.');
    }

    public function updateOfficeVoucher(Request $request, $id)
    {
        app(CompanyErpController::class)->ensurePaymentMethodsTable();
        $officeId = $this->getCurrentOfficeId();
        $oldVoucher = DB::table('vouchers')->where('id', $id)->where('office_id', $officeId)->first();
        if (!$oldVoucher) {
            return back()->withErrors(['السند غير موجود.']);
        }

        $newAmount = (float) ($request->amount ?? $oldVoucher->amount);
        if ($newAmount <= 0) {
            return back()->withErrors(['يرجى إدخال مبلغ صحيح أكبر من الصفر.']);
        }

        $newType = in_array($request->voucher_type, ['receipt', 'payment'], true)
            ? $request->voucher_type
            : $oldVoucher->voucher_type;

        // التحقق من رقم السند إذا قام المستخدم بتعديله يدوياً
        $newVoucherNumber = trim((string) ($request->voucher_number ?: $oldVoucher->voucher_number));
        $duplicateNo = DB::table('vouchers')
            ->where('id', '!=', $id)
            ->where('voucher_number', $newVoucherNumber)
            ->exists();
        if ($duplicateNo) {
            $newVoucherNumber = $oldVoucher->voucher_number;
        }

        // تحديد المستأجر، المالك، العقار، الوحدة، والعقد
        $tenantId = $request->has('tenant_id') ? ($request->tenant_id ? (int) $request->tenant_id : null) : $oldVoucher->tenant_id;
        $ownerId  = $request->has('owner_id') ? ($request->owner_id ? (int) $request->owner_id : null) : $oldVoucher->owner_id;
        $propId   = $request->has('property_id') ? ($request->property_id ? (int) $request->property_id : null) : ($oldVoucher->property_id ?? null);
        $unitId   = $request->has('property_unit_id') ? ($request->property_unit_id ? (int) $request->property_unit_id : null) : ($oldVoucher->property_unit_id ?? null);
        $contractId = $request->has('lease_contract_id') ? ($request->lease_contract_id ? (int) $request->lease_contract_id : null) : ($oldVoucher->lease_contract_id ?? null);

        if ($tenantId) {
            $activeContract = DB::table('lease_contracts')
                ->where('office_id', $officeId)
                ->where('tenant_id', $tenantId)
                ->orderBy('id', 'desc')
                ->first();
            if ($activeContract) {
                if (!$ownerId) $ownerId = (int) $activeContract->owner_id;
                if (!$propId) $propId = (int) $activeContract->property_id;
                if (!$unitId && $activeContract->property_unit_id) $unitId = (int) $activeContract->property_unit_id;
                if (!$contractId) $contractId = (int) $activeContract->id;
            }
        }

        if ($propId && !$ownerId) {
            $propRow = DB::table('properties')->where('id', $propId)->where('office_id', $officeId)->first();
            if ($propRow) $ownerId = (int) $propRow->owner_id;
        }

        $newDebitId = (int) $oldVoucher->debit_account_id;
        if ($request->filled('debit_account_id')) {
            $dRow = DB::table('accounts')->where('office_id', $officeId)->where('id', (int) $request->debit_account_id)->first();
            if ($dRow) $newDebitId = (int) $dRow->id;
        }

        $newCreditId = (int) $oldVoucher->credit_account_id;
        if ($request->filled('credit_account_id')) {
            $cRow = DB::table('accounts')->where('office_id', $officeId)->where('id', (int) $request->credit_account_id)->first();
            if ($cRow) $newCreditId = (int) $cRow->id;
        }

        $newApprovalStatus = $request->filled('approval_status')
            ? $request->approval_status
            : ($oldVoucher->approval_status ?? 'approved');

        $safePaymentMethod = CompanyErpController::normalizeVoucherPaymentMethod(
            $request->payment_method ?: $oldVoucher->payment_method
        );

        DB::transaction(function () use (
            $request, $id, $officeId, $oldVoucher, $newVoucherNumber, $newType,
            $newDebitId, $newCreditId, $ownerId, $tenantId, $propId, $unitId,
            $contractId, $newAmount, $safePaymentMethod, $newApprovalStatus
        ) {
            // 1. عكس القيد المحاسبي القديم إذا كان السند معتمداً مسبقاً
            if (($oldVoucher->approval_status ?? 'approved') === 'approved') {
                CompanyErpController::applyAccountMovement((int) $oldVoucher->debit_account_id, (float) $oldVoucher->amount, 0, true);
                CompanyErpController::applyAccountMovement((int) $oldVoucher->credit_account_id, 0, (float) $oldVoucher->amount, true);

                if ($oldVoucher->voucher_type === 'receipt' && !empty($oldVoucher->owner_id)) {
                    $oldOwnerObj = DB::table('owners')->where('id', $oldVoucher->owner_id)->where('office_id', $officeId)->first();
                    if ($oldOwnerObj && $oldOwnerObj->account_id && (int) $oldOwnerObj->account_id !== (int) $oldVoucher->credit_account_id) {
                        CompanyErpController::applyAccountMovement((int) $oldVoucher->credit_account_id, (float) $oldVoucher->amount, 0, true);
                        CompanyErpController::applyAccountMovement((int) $oldOwnerObj->account_id, 0, (float) $oldVoucher->amount, true);
                    }
                }
            }

            // 2. تجهيز كافة الحقول المحدثة
            $updatePayload = [
                'voucher_number'    => $newVoucherNumber,
                'voucher_type'      => $newType,
                'debit_account_id'  => $newDebitId,
                'credit_account_id' => $newCreditId,
                'owner_id'          => $ownerId,
                'tenant_id'         => $tenantId,
                'lease_contract_id' => $contractId,
                'amount'            => $newAmount,
                'payment_method'    => $safePaymentMethod,
                'reference_number'  => $request->has('reference_number') ? (trim((string) $request->reference_number) ?: null) : ($oldVoucher->reference_number ?? null),
                'voucher_date'      => $request->voucher_date ?: $oldVoucher->voucher_date,
                'notes'             => trim((string) ($request->notes ?: $oldVoucher->notes)),
                'updated_at'        => now(),
            ];

            if (\Illuminate\Support\Facades\Schema::hasColumn('vouchers', 'property_id')) {
                $updatePayload['property_id'] = $propId;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('vouchers', 'property_unit_id')) {
                $updatePayload['property_unit_id'] = $unitId;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('vouchers', 'approval_status')) {
                $updatePayload['approval_status'] = $newApprovalStatus;
            }

            DB::table('vouchers')->where('id', $id)->where('office_id', $officeId)->update($updatePayload);

            // 3. تطبيق القيد المحاسبي الجديد إذا كانت حالة السند معتمدة
            if ($newApprovalStatus === 'approved') {
                CompanyErpController::applyAccountMovement($newDebitId, $newAmount, 0);
                CompanyErpController::applyAccountMovement($newCreditId, 0, $newAmount);

                if ($newType === 'receipt' && $ownerId) {
                    $newOwnerObj = DB::table('owners')->where('id', $ownerId)->where('office_id', $officeId)->first();
                    if ($newOwnerObj && $newOwnerObj->account_id && (int) $newOwnerObj->account_id !== $newCreditId) {
                        CompanyErpController::applyAccountMovement($newCreditId, $newAmount, 0);
                        CompanyErpController::applyAccountMovement((int) $newOwnerObj->account_id, 0, $newAmount);
                    }
                }
            }
        });

        return back()->with('success', '✅ تم تعديل كافة بيانات السند رقم (' . $newVoucherNumber . ') وتحديث الأرصدة المرتبطة في شجرة الحسابات بنجاح.');
    }

    public function deleteOfficeVoucher($id)
    {
        $officeId = $this->getCurrentOfficeId();
        $oldVoucher = DB::table('vouchers')->where('id', $id)->where('office_id', $officeId)->first();
        if ($oldVoucher) {
            DB::transaction(function () use ($id, $officeId, $oldVoucher) {
                if (($oldVoucher->approval_status ?? 'approved') === 'approved') {
                    CompanyErpController::applyAccountMovement((int) $oldVoucher->debit_account_id, (float) $oldVoucher->amount, 0, true);
                    CompanyErpController::applyAccountMovement((int) $oldVoucher->credit_account_id, 0, (float) $oldVoucher->amount, true);
                }
                DB::table('vouchers')->where('id', $id)->where('office_id', $officeId)->delete();
            });
        }

        return back()->with('success', 'تم حذف السند وإلغاء أثره من شجرة حسابات المكتب.');
    }

    public function storeOfficeJournalEntry(Request $request)
    {
        $officeId = $this->getCurrentOfficeId();
        app(CompanyErpController::class)->ensureJournalTables();

        $allowedAccountIds = DB::table('accounts')->where('office_id', $officeId)->pluck('id')->map(fn($v) => (int) $v)->toArray();

        $accountIds = $request->input('account_ids', []);
        $debits     = $request->input('debits', []);
        $credits    = $request->input('credits', []);
        $taxes      = $request->input('taxes', []);
        $itemNotes  = $request->input('item_notes', []);

        $rows = [];
        $totalDebit = 0;
        $totalCredit = 0;
        $debitTaxSum = 0;
        $creditTaxSum = 0;

        foreach ($accountIds as $idx => $accId) {
            if (empty($accId) || !in_array((int) $accId, $allowedAccountIds, true)) continue;
            $d = round((float) ($debits[$idx] ?? 0), 2);
            $c = round((float) ($credits[$idx] ?? 0), 2);
            $t = round((float) ($taxes[$idx] ?? 0), 2);
            if ($d == 0 && $c == 0) continue;

            // إذا كان المبلغ مدين فإن الضريبة مدين، وإذا كان دائن فإن الضريبة دائن
            $rowEffectiveDebit  = $d > 0 ? ($d + $t) : 0;
            $rowEffectiveCredit = $c > 0 ? ($c + $t) : 0;

            if ($d > 0) $debitTaxSum += $t;
            if ($c > 0) $creditTaxSum += $t;

            $totalDebit  += $rowEffectiveDebit;
            $totalCredit += $rowEffectiveCredit;

            $rows[] = [
                'account_id'       => (int) $accId,
                'debit'            => $d,
                'credit'           => $c,
                'tax'              => $t,
                'effective_debit'  => $rowEffectiveDebit,
                'effective_credit' => $rowEffectiveCredit,
                'notes'            => trim($itemNotes[$idx] ?? ''),
            ];
        }

        if (count($rows) === 0) {
            return back()->withErrors(['يرجى إدخال سطر واحد صحيح على الأقل في القيد اليومي.']);
        }

        if (abs($totalDebit - $totalCredit) > 0.05) {
            return back()->withErrors([
                'القيد غير متوازن! إجمالي المدين الشامل (' . number_format($totalDebit, 2) . ' ر.س) لا يساوي إجمالي الدائن الشامل (' . number_format($totalCredit, 2) . ' ر.س) — الفرق: ' . number_format(abs($totalDebit - $totalCredit), 2) . ' ر.س.'
            ]);
        }

        $totalTax = max($debitTaxSum, $creditTaxSum);

        DB::transaction(function () use ($request, $officeId, $rows, $totalDebit, $totalCredit, $totalTax) {
            $maxNo = DB::table('journal_entries')->where('office_id', $officeId)->max('entry_number') ?? 0;
            $nextEntryNo = $maxNo + 1;

            $entryId = DB::table('journal_entries')->insertGetId([
                'office_id'     => $officeId,
                'entry_number'  => $nextEntryNo,
                'entry_date'    => $request->entry_date ?: date('Y-m-d'),
                'general_notes' => trim($request->general_notes),
                'total_debit'   => $totalDebit,
                'total_credit'  => $totalCredit,
                'total_tax'     => $totalTax,
                'created_by'    => Auth::id(),
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            foreach ($rows as $r) {
                DB::table('journal_entry_items')->insert([
                    'journal_entry_id' => $entryId,
                    'account_id'       => $r['account_id'],
                    'debit'            => $r['debit'],
                    'credit'           => $r['credit'],
                    'tax'              => $r['tax'],
                    'notes'            => $r['notes'] ?: trim($request->general_notes),
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);

                CompanyErpController::applyAccountMovement($r['account_id'], $r['effective_debit'], $r['effective_credit']);
            }
        });

        return redirect()->route('office.dashboard', ['tab' => 'journal'])
            ->with('success', 'تم حفظ القيد اليومي رقم (#' . ($maxNo ?? 0) + 1 . ') بإجمالي (' . number_format($totalDebit, 2) . ' ر.س) وترحيله لشجرة حسابات المكتب بنجاح.');
    }

    public function updateOfficeJournalEntry(Request $request, $id)
    {
        $officeId = $this->getCurrentOfficeId();
        $entry = DB::table('journal_entries')->where('id', $id)->where('office_id', $officeId)->first();
        if (!$entry) return back()->withErrors(['القيد غير موجود.']);

        $allowedAccountIds = DB::table('accounts')->where('office_id', $officeId)->pluck('id')->map(fn($v) => (int) $v)->toArray();

        $accountIds = $request->input('account_ids', []);
        $debits     = $request->input('debits', []);
        $credits    = $request->input('credits', []);
        $taxes      = $request->input('taxes', []);
        $itemNotes  = $request->input('item_notes', []);

        $rows = [];
        $totalDebit = 0;
        $totalCredit = 0;
        $debitTaxSum = 0;
        $creditTaxSum = 0;

        foreach ($accountIds as $idx => $accId) {
            if (empty($accId) || !in_array((int) $accId, $allowedAccountIds, true)) continue;
            $d = round((float) ($debits[$idx] ?? 0), 2);
            $c = round((float) ($credits[$idx] ?? 0), 2);
            $t = round((float) ($taxes[$idx] ?? 0), 2);
            if ($d == 0 && $c == 0) continue;

            $rowEffectiveDebit  = $d > 0 ? ($d + $t) : 0;
            $rowEffectiveCredit = $c > 0 ? ($c + $t) : 0;

            if ($d > 0) $debitTaxSum += $t;
            if ($c > 0) $creditTaxSum += $t;

            $totalDebit  += $rowEffectiveDebit;
            $totalCredit += $rowEffectiveCredit;

            $rows[] = [
                'account_id'       => (int) $accId,
                'debit'            => $d,
                'credit'           => $c,
                'tax'              => $t,
                'effective_debit'  => $rowEffectiveDebit,
                'effective_credit' => $rowEffectiveCredit,
                'notes'            => trim($itemNotes[$idx] ?? ''),
            ];
        }

        if (count($rows) === 0) {
            return back()->withErrors(['يرجى إدخال سطر واحد صحيح على الأقل في القيد اليومي.']);
        }

        if (abs($totalDebit - $totalCredit) > 0.05) {
            return back()->withErrors([
                'القيد غير متوازن! إجمالي المدين الشامل (' . number_format($totalDebit, 2) . ' ر.س) لا يساوي إجمالي الدائن الشامل (' . number_format($totalCredit, 2) . ' ر.س) — الفرق: ' . number_format(abs($totalDebit - $totalCredit), 2) . ' ر.س.'
            ]);
        }

        $totalTax = max($debitTaxSum, $creditTaxSum);

        DB::transaction(function () use ($request, $id, $officeId, $rows, $totalDebit, $totalCredit, $totalTax) {
            $oldItems = DB::table('journal_entry_items')->where('journal_entry_id', $id)->get();
            foreach ($oldItems as $old) {
                $oldEffD = $old->debit > 0 ? ((float)$old->debit + (float)$old->tax) : 0;
                $oldEffC = $old->credit > 0 ? ((float)$old->credit + (float)$old->tax) : 0;
                CompanyErpController::applyAccountMovement((int) $old->account_id, $oldEffD, $oldEffC, true);
            }
            DB::table('journal_entry_items')->where('journal_entry_id', $id)->delete();

            DB::table('journal_entries')->where('id', $id)->where('office_id', $officeId)->update([
                'entry_date'    => $request->entry_date ?: date('Y-m-d'),
                'general_notes' => trim($request->general_notes),
                'total_debit'   => $totalDebit,
                'total_credit'  => $totalCredit,
                'total_tax'     => $totalTax,
                'updated_at'    => now(),
            ]);

            foreach ($rows as $r) {
                DB::table('journal_entry_items')->insert([
                    'journal_entry_id' => $id,
                    'account_id'       => $r['account_id'],
                    'debit'            => $r['debit'],
                    'credit'           => $r['credit'],
                    'tax'              => $r['tax'],
                    'notes'            => $r['notes'] ?: trim($request->general_notes),
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);

                CompanyErpController::applyAccountMovement($r['account_id'], $r['effective_debit'], $r['effective_credit']);
            }
        });

        return redirect()->route('office.dashboard', ['tab' => 'journal'])
            ->with('success', 'تم تعديل القيد اليومي رقم (' . $entry->entry_number . ') بإجمالي (' . number_format($totalDebit, 2) . ' ر.س) وتحديث الأرصدة.');
    }

    public function deleteOfficeJournalEntry($id)
    {
        $officeId = $this->getCurrentOfficeId();
        $entry = DB::table('journal_entries')->where('id', $id)->where('office_id', $officeId)->first();
        if ($entry) {
            DB::transaction(function () use ($id, $officeId) {
                $oldItems = DB::table('journal_entry_items')->where('journal_entry_id', $id)->get();
                foreach ($oldItems as $old) {
                    CompanyErpController::applyAccountMovement((int) $old->account_id, (float) $old->debit, (float) $old->credit, true);
                }
                DB::table('journal_entry_items')->where('journal_entry_id', $id)->delete();
                DB::table('journal_entries')->where('id', $id)->where('office_id', $officeId)->delete();
            });
        }

        return redirect()->route('office.dashboard', ['tab' => 'journal'])
            ->with('success', 'تم حذف القيد اليومي وعكس قيده من شجرة حسابات المكتب.');
    }

    public function sendOfficeVoucherWhatsapp(Request $request, $id)
    {
        $officeId = $this->getCurrentOfficeId();
        $office = DB::table('offices')->where('id', $officeId)->first();

        $voucher = DB::table('vouchers')
            ->leftJoin('tenants', 'vouchers.tenant_id', '=', 'tenants.id')
            ->leftJoin('owners', 'vouchers.owner_id', '=', 'owners.id')
            ->select('vouchers.*', 'tenants.name as tenant_name', 'tenants.phone as tenant_phone', 'owners.name as owner_name', 'owners.phone as owner_phone')
            ->where('vouchers.id', $id)
            ->where('vouchers.office_id', $officeId)
            ->first();

        if (!$voucher) {
            return back()->withErrors(['السند غير موجود.']);
        }

        $targetPhone = $request->phone ?: ($voucher->tenant_phone ?: $voucher->owner_phone);
        if (!$targetPhone) {
            return back()->withErrors(['يرجى تحديد مستأجر أو مالك مرتبط بالسند أو إدخال رقم الجوال للإرسال.']);
        }

        $typeTitle = $voucher->voucher_type === 'receipt' ? 'سند قبض رسمي' : 'سند صرف رسمي';
        $printUrl = route('erp.vouchers.print', $voucher->id);
        $msg = "🏢 *{$office->name}*\n"
             . "🧾 *{$typeTitle}*\n"
             . "رقم السند: *{$voucher->voucher_number}*\n"
             . "التاريخ: {$voucher->voucher_date}\n"
             . "المبلغ: *" . number_format($voucher->amount, 2) . " ر.س*\n"
             . "البيان: {$voucher->notes}\n"
             . "رابط السند الرسمي (PDF): {$printUrl}";

        $sent = CompanyErpController::dispatchWhatsappMessage($targetPhone, $msg, $officeId);

        if ($sent) {
            return back()->with('success', 'تم إرسال السند رقم (' . $voucher->voucher_number . ') عبر واتساب إلى (' . $targetPhone . ') بنجاح.');
        }

        $cleanPhone = ltrim(CompanyErpController::formatSaudiPhone($targetPhone), '+');
        $waWebUrl = "https://wa.me/{$cleanPhone}?text=" . urlencode($msg);
        return back()->with('success', 'تم تجهيز السند للإرسال عبر واتساب.')
                     ->with('wa_fallback_url', $waWebUrl);
    }

    public function storeStaff(Request $request)
    {
        $officeId = $this->getCurrentOfficeId();

        $request->validate([
            'name' => 'required',
            'phone' => ['required', 'regex:/^05[0-9]{8}$/'],
            'username' => 'required|unique:users,username',
        ], [
            'phone.regex' => 'رقم جوال الموظف يجب أن يتكون من 10 أرقام ويبدأ بـ 05.',
            'username.unique' => 'اسم المستخدم مستخدم مسبقاً.'
        ]);

        $formattedPhone = CompanyErpController::formatSaudiPhone($request->phone);
        $perms = $request->input('permissions', []);

        DB::table('users')->insert([
            'name' => trim($request->name),
            'username' => trim($request->username),
            'phone' => $formattedPhone,
            'email' => trim($request->username) . '@staff.local',
            'password' => Hash::make($request->password ?: '123456'),
            'user_type' => 'office_staff',
            'office_id' => $officeId,
            'is_active' => 1,
            'custom_permissions' => json_encode($perms, JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('office.dashboard', ['tab' => 'staff'])
            ->with('success', 'تم إضافة الموظف وتخصيص صلاحياته بنجاح.');
    }

    public function updateStaff(Request $request, $id)
    {
        $officeId = $this->getCurrentOfficeId();
        $request->validate([
            'name' => 'required',
            'phone' => ['required', 'regex:/^05[0-9]{8}$/'],
            'username' => 'required',
        ]);

        $formattedPhone = CompanyErpController::formatSaudiPhone($request->phone);
        $perms = $request->input('permissions', []);

        $updateData = [
            'name' => trim($request->name),
            'username' => trim($request->username),
            'phone' => $formattedPhone,
            'custom_permissions' => json_encode($perms, JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        DB::table('users')->where('id', $id)->where('office_id', $officeId)->update($updateData);

        return redirect()->route('office.dashboard', ['tab' => 'staff'])
            ->with('success', 'تم تحديث بيانات وصلاحيات الموظف بنجاح.');
    }

    public function deleteStaff($id)
    {
        $officeId = $this->getCurrentOfficeId();
        DB::table('users')->where('id', $id)->where('office_id', $officeId)->where('user_type', 'office_staff')->delete();
        return redirect()->route('office.dashboard', ['tab' => 'staff'])
            ->with('success', 'تم حذف حساب الموظف.');
    }

   public function updateOfficeWhatsappSettings(Request $request)
    {
        $officeId = $this->getCurrentOfficeId();
        $office = DB::table('offices')->where('id', $officeId)->first();
        if (!\Illuminate\Support\Facades\Schema::hasColumn('offices', 'reminder_settings_json')) {
            \Illuminate\Support\Facades\Schema::table('offices', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->text('reminder_settings_json')->nullable();
            });
        }

        $useCustom = $request->input('use_custom_whatsapp') == '1' || $request->input('whatsapp_mode') === 'custom';
        $senderPhone = $request->filled('whatsapp_sender_phone')
            ? CompanyErpController::formatSaudiPhone($request->whatsapp_sender_phone)
            : null;

        // تنظيف وحفظ الأرقام الإضافية المخصصة التي أدخلها صاحب المكتب
        $rawCustomPhones = $request->input('custom_recipient_phones', []);
        $rawCustomNames  = $request->input('custom_recipient_names', []);
        $customRecipients = [];
        if (is_array($rawCustomPhones)) {
            foreach ($rawCustomPhones as $idx => $ph) {
                $cleanPh = preg_replace('/\D/', '', (string) $ph);
                if (strlen($cleanPh) >= 9) {
                    $customRecipients[] = [
                        'name'  => trim((string) ($rawCustomNames[$idx] ?? 'مستلم إضافي')) ?: 'مستلم إضافي',
                        'phone' => CompanyErpController::toLocalPhone($cleanPh),
                    ];
                }
            }
        }

        // حفظ كافة خيارات التنبيهات المتقدمة والمستلمين المحددين
        $advancedReminderSettings = [
            // 1. المستلمون المحددون من قِبل صاحب المكتب
            'send_to_tenant'       => $request->has('send_to_tenant') ? 1 : 0,
            'send_to_owner'        => $request->has('send_to_owner') ? 1 : 0,
            'send_to_office_admin' => $request->has('send_to_office_admin') ? 1 : 0,
            'selected_staff_ids'   => array_values(array_filter(array_map('intval', (array) $request->input('selected_staff_ids', [])))),
            'custom_recipients'    => $customRecipients,

            // 2. أنواع ومراحل التنبيهات المفعلة
            'notify_installments_before' => $request->has('notify_installments_before') ? 1 : 0,
            'installment_days_before'    => (int) ($request->reminder_days_before ?? 15),
            'notify_second_chance'       => $request->has('notify_second_chance') ? 1 : 0,
            'second_days_before'         => (int) ($request->second_days_before ?? 3),
            'notify_on_due_day'          => $request->has('notify_on_due_day') ? 1 : 0,
            'notify_overdue_after'       => $request->has('notify_overdue_after') ? 1 : 0,
            'overdue_days_after'         => (int) ($request->overdue_days_after ?? 7),
            'notify_contract_expiry'     => $request->has('notify_contract_expiry') ? 1 : 0,
            'contract_expiry_days'       => (int) ($request->contract_expiry_days ?? 30),

            // 3. تفضيلات الإرسال والوقت والقوالب
            'preferred_send_time'  => $request->input('preferred_send_time', '09:00'),
            'skip_fully_paid'      => $request->has('skip_fully_paid') ? 1 : 0,
            'send_owner_summary'   => $request->has('send_owner_summary') ? 1 : 0,
            'overdue_template'     => trim((string) $request->input('overdue_template', '')),
            'owner_template'       => trim((string) $request->input('owner_template', '')),
        ];

        $updatePayload = [
            'use_custom_whatsapp'   => $useCustom ? 1 : 0,
            'whatsapp_mode'         => $useCustom ? 'custom' : 'company',
            'whatsapp_api_url'      => trim($request->whatsapp_api_url ?? ''),
            'whatsapp_api_token'    => trim($request->whatsapp_api_token ?? ''),
            'whatsapp_instance_id'  => trim($request->whatsapp_instance_id ?? ''),
            'whatsapp_sender_phone' => $senderPhone,
            'reminder_days_before'  => (int) ($request->reminder_days_before ?? 15),
            'reminder_target'       => 'custom_advanced',
            'reminder_template'     => trim($request->reminder_template ?? ''),
            'enable_auto_reminders' => $request->has('enable_auto_reminders') ? 1 : 0,
            'reminder_settings_json'=> json_encode($advancedReminderSettings, JSON_UNESCAPED_UNICODE),
            'updated_at'            => now(),
        ];

        if ($request->has('tax_number')) {
            $updatePayload['tax_number'] = trim($request->tax_number) ?: null;
        }
        if ($request->has('cr_number')) {
            $updatePayload['cr_number'] = trim($request->cr_number) ?: ($office->cr_number ?? null);
        }
        if ($request->has('national_address')) {
            $updatePayload['national_address'] = trim($request->national_address) ?: null;
        }

        DB::table('offices')->where('id', $officeId)->update($updatePayload);

        return redirect()->route('office.dashboard', ['tab' => 'whatsapp'])
            ->with('success', 'تم حفظ إعدادات التنبيهات الآلية والمستلمين المحددين والربط مع واتساب بنجاح.');
    }

    public function updateOfficeWhatsapp(Request $request)
    {
        return $this->updateOfficeWhatsappSettings($request);
    }

    /**
     * فحص وإرسال تذكيرات واتساب الذكية (للدفعات، المتأخرات، وانتهاء العقود) إلى جميع المستلمين الذين حددهم صاحب المكتب
     */
    public function triggerLeaseReminders(Request $request)
    {
        $officeId = $this->getCurrentOfficeId();
        $office = DB::table('offices')->where('id', $officeId)->first();
        if (!$office) return back();

        if (empty($office->enable_auto_reminders)) {
            return redirect()->route('office.dashboard', ['tab' => 'whatsapp'])
                ->withErrors(['خيار الإرسال الآلي للتنبيهات متوقف حالياً، يرجى تفعيله أولاً ثم حفظ الإعدادات.']);
        }

        $cfg = json_decode($office->reminder_settings_json ?? '{}', true) ?: [];

        $sendToTenant      = $cfg['send_to_tenant'] ?? 1;
        $sendToOwner       = $cfg['send_to_owner'] ?? 0;
        $sendToOfficeAdmin = $cfg['send_to_office_admin'] ?? 1;
        $selectedStaffIds  = $cfg['selected_staff_ids'] ?? [];
        $customRecipients  = $cfg['custom_recipients'] ?? [];

        $daysLimit          = (int) ($cfg['installment_days_before'] ?? ($office->reminder_days_before ?? 15));
        $overdueDaysAfter   = (int) ($cfg['overdue_days_after'] ?? 7);
        $contractExpiryDays = (int) ($cfg['contract_expiry_days'] ?? 30);
        $notifyOverdue      = !empty($cfg['notify_overdue_after']);
        $notifyExpiry       = !empty($cfg['notify_contract_expiry']);

        $defaultTpl = "عزيزي {اسم_المستأجر}، نود تذكيركم بقرب استحقاق دفعة الإيجار للعقد رقم {رقم_العقد} للعقار ({اسم_العقار} - {رقم_الوحدة}) بمبلغ {المبلغ_المتبقي} ر.س بتاريخ {تاريخ_الاستحقاق} (متبقي {الأيام_المتبقية} يوم). مع تحيات {اسم_المكتب}.";
        $template = !empty($office->reminder_template) ? $office->reminder_template : $defaultTpl;
        $overdueTpl = !empty($cfg['overdue_template'])
            ? $cfg['overdue_template']
            : "⚠️ إشعار مطالبة سداد: المكرم {اسم_المستأجر}، نفيدكم بتأخر سداد الدفعة المستحقة للعقد {رقم_العقد} ({اسم_العقار} - {رقم_الوحدة}) بمبلغ متبقي {المبلغ_المتبقي} ر.س منذ تاريخ {تاريخ_الاستحقاق}. نأمل سرعة السداد. {اسم_المكتب}";
        $ownerTpl = !empty($cfg['owner_template'])
            ? $cfg['owner_template']
            : "👑 إشعار للمالك {اسم_المالك}: نحيطكم علماً بأن دفعة المستأجر ({اسم_المستأجر}) للعقار ({اسم_العقار} - {رقم_الوحدة}) بمبلغ {المبلغ_المتبقي} ر.س تاريخ استحقاقها {تاريخ_الاستحقاق}. إدارة {اسم_المكتب}.";

        // جلب أرقام الموظفين المحددين من صاحب المكتب
        $staffPhones = [];
        if (!empty($selectedStaffIds)) {
            $staffPhones = DB::table('users')
                ->where('office_id', $officeId)
                ->whereIn('id', $selectedStaffIds)
                ->whereNotNull('phone')
                ->pluck('phone')
                ->toArray();
        }

        // جلب الدفعات غير المسددة مع بيانات المالك والمستأجر والعقار
        CompanyErpController::ensureContractInstallmentsTable();
        $installments = DB::table('contract_installments')
            ->join('lease_contracts', 'contract_installments.lease_contract_id', '=', 'lease_contracts.id')
            ->join('properties', 'contract_installments.property_id', '=', 'properties.id')
            ->join('owners', 'contract_installments.owner_id', '=', 'owners.id')
            ->join('tenants', 'contract_installments.tenant_id', '=', 'tenants.id')
            ->leftJoin('property_units', 'contract_installments.property_unit_id', '=', 'property_units.id')
            ->where('contract_installments.office_id', $officeId)
            ->where('contract_installments.status', '!=', 'paid')
            ->select(
                'contract_installments.*',
                'lease_contracts.contract_number',
                'lease_contracts.end_date as contract_end_date',
                'properties.title as property_title',
                'owners.name as owner_name',
                'owners.phone as owner_phone',
                'tenants.name as tenant_name',
                'tenants.phone as tenant_phone',
                'property_units.unit_number'
            )
            ->get();

        $sentCount = 0;
        $matchedCount = 0;
        $today = now()->startOfDay();

        foreach ($installments as $inst) {
            $remAmount = max(0, round($inst->amount - $inst->paid_amount, 2));
            if ($remAmount <= 0.01) continue;

            $dueDate = \Carbon\Carbon::parse($inst->due_date)->startOfDay();
            $diffDays = (int) $today->diffInDays($dueDate, false);

            $isUpcomingMatch = ($diffDays >= 0 && $diffDays <= $daysLimit);
            $isOverdueMatch  = ($notifyOverdue && $diffDays < 0 && abs($diffDays) >= 1 && abs($diffDays) <= max(90, $overdueDaysAfter));

            if (!$isUpcomingMatch && !$isOverdueMatch) {
                continue;
            }

            $matchedCount++;
            $replacements = [
                '{اسم_المستأجر}'     => $inst->tenant_name,
                '{اسم_المالك}'       => $inst->owner_name,
                '{اسم_العقار}'       => $inst->property_title,
                '{رقم_الوحدة}'       => $inst->unit_number ?: 'كامل العقار',
                '{رقم_العقد}'        => $inst->contract_number,
                '{رقم_الدفعة}'       => (string) $inst->installment_number,
                '{المبلغ}'           => number_format($inst->amount, 2),
                '{المبلغ_المتبقي}'   => number_format($remAmount, 2),
                '{تاريخ_الاستحقاق}'  => $inst->due_date,
                '{تاريخ_انتهاء_العقد}'=> $inst->contract_end_date,
                '{الأيام_المتبقية}'  => (string) max(0, $diffDays),
                '{أيام_التأخير}'     => (string) ($diffDays < 0 ? abs($diffDays) : 0),
                '{اسم_المكتب}'       => $office->name,
            ];

            $chosenTemplate = $isOverdueMatch ? $overdueTpl : $template;
            $finalMessage = str_replace(array_keys($replacements), array_values($replacements), $chosenTemplate);
            $ownerMessage = str_replace(array_keys($replacements), array_values($replacements), $ownerTpl);

            // 1. الإرسال للمستأجر
            if ($sendToTenant && !empty($inst->tenant_phone)) {
                if (CompanyErpController::dispatchWhatsappMessage($inst->tenant_phone, $finalMessage, $officeId)) {
                    $sentCount++;
                }
            }

            // 2. الإرسال لمالك العقار
            if ($sendToOwner && !empty($inst->owner_phone)) {
                if (CompanyErpController::dispatchWhatsappMessage($inst->owner_phone, $ownerMessage, $officeId)) {
                    $sentCount++;
                }
            }

            // 3. الإرسال لمدير المكتب العقاري
            $adminNotice = "🔔 *تذكير آلي ({$office->name})*\n" . $finalMessage;
            if ($sendToOfficeAdmin && !empty($office->phone)) {
                if (CompanyErpController::dispatchWhatsappMessage($office->phone, $adminNotice, $officeId)) {
                    $sentCount++;
                }
            }

            // 4. الإرسال للموظفين المحددين من صاحب المكتب
            foreach ($staffPhones as $stPhone) {
                if (CompanyErpController::dispatchWhatsappMessage($stPhone, $adminNotice, $officeId)) {
                    $sentCount++;
                }
            }

            // 5. الإرسال للأرقام المخصصة الإضافية التي كتبها صاحب المكتب
            foreach ($customRecipients as $cr) {
                if (!empty($cr['phone'])) {
                    $customNotice = "🔔 *إشعار إلى ({$cr['name']}) - {$office->name}*\n" . $finalMessage;
                    if (CompanyErpController::dispatchWhatsappMessage($cr['phone'], $customNotice, $officeId)) {
                        $sentCount++;
                    }
                }
            }
        }

        return redirect()->route('office.dashboard', ['tab' => 'whatsapp'])
            ->with('success', "تم فحص الدفعات والعقود النشطة: تم العثور على ({$matchedCount}) دفعة/عقد مستحق للتنبيه، وإرسال ({$sentCount}) رسالة واتساب إلى المستلمين المحددين.");
    }

    // =========================================================================
    // حفظ البيانات الضريبية وإصدار الفواتير الضريبية للمكتب والملاك والمستأجرين
    // =========================================================================
    public function saveOfficeTaxProfile(Request $request)
    {
        $officeId = $this->getCurrentOfficeId();
        DB::table('offices')->where('id', $officeId)->update([
            'tax_number' => trim($request->tax_number) ?: null,
            'cr_number' => trim($request->cr_number) ?: null,
            'national_address' => trim($request->national_address) ?: null,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'تم حفظ الرقم الضريبي والبيانات الضريبية للمكتب بنجاح.');
    }

    public function saveOwnerTaxProfile(Request $request, $id)
    {
        $officeId = $this->getCurrentOfficeId();
        DB::table('owners')->where('id', $id)->where('office_id', $officeId)->update([
            'tax_number' => trim($request->tax_number) ?: null,
            'cr_number' => trim($request->cr_number ?? '') ?: null,
            'national_address' => trim($request->national_address ?? '') ?: null,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'تم حفظ البيانات الضريبية للمالك بنجاح.');
    }

    public function saveTenantTaxProfile(Request $request, $id)
    {
        $officeId = $this->getCurrentOfficeId();
        DB::table('tenants')->where('id', $id)->where('office_id', $officeId)->update([
            'tax_number' => trim($request->tax_number) ?: null,
            'national_address' => trim($request->national_address ?? '') ?: null,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'تم تحديث الرقم الضريبي والعنوان للمستأجر بنجاح.');
    }

    public function storeOfficeInvoice(Request $request)
    {
        $officeId = $this->getCurrentOfficeId();
        $office = DB::table('offices')->where('id', $officeId)->first();
        app(CompanyErpController::class)->ensureTaxInvoicesTable();
        app(CompanyErpController::class)->ensureJournalTables();

        $request->validate([
            'tenant_id' => 'required',
            'subtotal' => 'required|numeric|min:0.01',
            'description' => 'required',
        ]);

        $tenant = DB::table('tenants')->where('id', $request->tenant_id)->where('office_id', $officeId)->first();
        if (!$tenant) {
            return back()->withErrors(['المستأجر غير موجود.']);
        }

        $tenantTaxNo = trim($request->buyer_tax_number ?: ($tenant->tax_number ?? ''));
        $tenantAddress = trim($request->buyer_address ?: ($tenant->national_address ?? ''));
        if ($tenantTaxNo && empty($tenant->tax_number)) {
            DB::table('tenants')->where('id', $tenant->id)->update([
                'tax_number' => $tenantTaxNo,
                'national_address' => $tenantAddress,
            ]);
        }

        $issuerMode = $request->input('issuer_mode', 'office'); // office | owner
        $ownerId = null;
        $owner = null;

        if ($issuerMode === 'owner') {
            if (empty($office->allow_owner_tax_invoice)) {
                return back()->withErrors(['ميزة إصدار فاتورة ضريبية باسم المالك غير مفعلة لمكتبكم حالياً من قبل الإدارة.']);
            }
            $owner = DB::table('owners')->where('id', $request->owner_id)->where('office_id', $officeId)->first();
            if (!$owner) {
                return back()->withErrors(['يرجى اختيار المالك لإصدار الفاتورة باسمه.']);
            }
            $ownerId = $owner->id;
            $ownerTaxNo = trim($request->seller_tax_number ?: ($owner->tax_number ?? ''));
            $ownerAddress = trim($request->seller_address ?: ($owner->national_address ?? ''));

            if ($ownerTaxNo && empty($owner->tax_number)) {
                DB::table('owners')->where('id', $owner->id)->update([
                    'tax_number' => $ownerTaxNo,
                    'national_address' => $ownerAddress,
                ]);
            }

            $sellerName = $owner->name;
            $sellerTax = $ownerTaxNo;
            $sellerAddress = $ownerAddress;
        } else {
            $officeTaxNo = trim($request->seller_tax_number ?: ($office->tax_number ?? ''));
            $officeAddress = trim($request->seller_address ?: ($office->national_address ?: $office->address));
            if ($officeTaxNo && empty($office->tax_number)) {
                DB::table('offices')->where('id', $office->id)->update([
                    'tax_number' => $officeTaxNo,
                    'national_address' => $officeAddress,
                ]);
            }
            $sellerName = $office->name;
            $sellerTax = $officeTaxNo;
            $sellerAddress = $officeAddress;
        }

        $invoiceType = !empty($tenantTaxNo) ? 'tax' : 'simplified';
        if ($request->filled('forced_invoice_type') && in_array($request->forced_invoice_type, ['tax', 'simplified'])) {
            $invoiceType = $request->forced_invoice_type;
            if ($invoiceType === 'simplified') {
                $tenantTaxNo = '';
            }
        }

        // قراءة نسبة الضريبة المعتمدة من إعدادات الأدمن المركزية فقط (لا يسمح بتعديلها يدوياً)
        $subtotal = round((float) $request->subtotal, 2);
        $vatRate = (float) CompanyErpController::getSetting('default_vat_rate', 15);
        $vatAmount = round(($subtotal * $vatRate) / 100, 2);
        $totalAmount = round($subtotal + $vatAmount, 2);

        $seq = DB::table('tax_invoices')->where('office_id', $officeId)->count() + 1;
        $prefix = $issuerMode === 'owner' ? "INV-OWN-{$officeId}-" : "INV-OFF-{$officeId}-";
        $invNumber = $prefix . date('Y') . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
        $issueDate = $request->issue_date ?: date('Y-m-d');

        $qrBase64 = CompanyErpController::generateZatcaQrBase64(
            $sellerName,
            $sellerTax ?: '300000000000003',
            $issueDate . 'T' . date('H:i:s') . 'Z',
            number_format($totalAmount, 2, '.', ''),
            number_format($vatAmount, 2, '.', '')
        );

        // ربط الفاتورة بشجرة حسابات المكتب:
        // المدين: حساب المستأجر (أو ما يختاره المستخدم)
        $debitAccId = $request->filled('debit_account_id') ? (int) $request->debit_account_id : ($tenant->account_id ?: null);
        if (!$debitAccId) {
            $firstAsset = DB::table('accounts')->where('office_id', $officeId)->where('is_group', 0)->first();
            $debitAccId = $firstAsset ? $firstAsset->id : null;
        }

        // الدائن: إذا كانت باسم المالك -> حساب أمانات المالك، وإذا كانت باسم المكتب -> حساب إيرادات عمولات المكتب (4101)
        $creditAccId = $request->filled('credit_account_id') ? (int) $request->credit_account_id : null;
        if (!$creditAccId) {
            if ($issuerMode === 'owner' && $owner && !empty($owner->account_id)) {
                $creditAccId = (int) $owner->account_id;
            } else {
                $revAcc = DB::table('accounts')->where('office_id', $officeId)->where('code', '4101')->first()
                       ?: DB::table('accounts')->where('office_id', $officeId)->where('type', 'like', '%إيراد%')->where('is_group', 0)->first();
                $creditAccId = $revAcc ? $revAcc->id : $debitAccId;
            }
        }

        $taxAccId = CompanyErpController::getOrCreateVatAccount($officeId);

        DB::transaction(function () use (
            $invNumber, $invoiceType, $officeId, $ownerId, $tenant, $request,
            $sellerName, $sellerTax, $sellerAddress, $tenantTaxNo, $tenantAddress,
            $subtotal, $vatRate, $vatAmount, $totalAmount, $issueDate, $qrBase64,
            $debitAccId, $creditAccId, $taxAccId
        ) {
            $journalEntryId = null;

            if ($debitAccId && $creditAccId) {
                $maxNo = DB::table('journal_entries')->where('office_id', $officeId)->max('entry_number') ?? 0;
                $journalEntryId = DB::table('journal_entries')->insertGetId([
                    'office_id' => $officeId,
                    'entry_number' => $maxNo + 1,
                    'entry_date' => $issueDate,
                    'general_notes' => "قيد فاتورة ضريبية رقم ({$invNumber}) - المستأجر: {$tenant->name} - " . trim($request->description),
                    'total_debit' => $totalAmount,
                    'total_credit' => $totalAmount,
                    'total_tax' => $vatAmount,
                    'created_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // مدين: حساب المستأجر بإجمالي الفاتورة
                DB::table('journal_entry_items')->insert([
                    'journal_entry_id' => $journalEntryId,
                    'account_id' => $debitAccId,
                    'debit' => $totalAmount,
                    'credit' => 0,
                    'tax' => $vatAmount,
                    'notes' => "استحقاق فاتورة ({$invNumber}) على المستأجر {$tenant->name}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                CompanyErpController::applyAccountMovement((int) $debitAccId, $totalAmount, 0);

                // دائن: حساب الإيراد أو أمانات المالك بالمبلغ قبل الضريبة
                DB::table('journal_entry_items')->insert([
                    'journal_entry_id' => $journalEntryId,
                    'account_id' => $creditAccId,
                    'debit' => 0,
                    'credit' => $subtotal,
                    'tax' => 0,
                    'notes' => "صافي فاتورة ({$invNumber}) قبل الضريبة",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                CompanyErpController::applyAccountMovement((int) $creditAccId, 0, $subtotal);

                // دائن: حساب ضريبة القيمة المضافة المستحقة
                if ($vatAmount > 0 && $taxAccId) {
                    DB::table('journal_entry_items')->insert([
                        'journal_entry_id' => $journalEntryId,
                        'account_id' => $taxAccId,
                        'debit' => 0,
                        'credit' => $vatAmount,
                        'tax' => $vatAmount,
                        'notes' => "ضريبة القيمة المضافة ({$vatRate}%) للفاتورة ({$invNumber})",
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    CompanyErpController::applyAccountMovement((int) $taxAccId, 0, $vatAmount);
                }
            }

            DB::table('tax_invoices')->insert([
                'invoice_number' => $invNumber,
                'issuer_level' => 'office',
                'invoice_type' => $invoiceType,
                'office_id' => $officeId,
                'owner_id' => $ownerId,
                'tenant_id' => $tenant->id,
                'lease_contract_id' => $request->lease_contract_id ?: null,
                'debit_account_id' => $debitAccId,
                'credit_account_id' => $creditAccId,
                'tax_account_id' => $taxAccId,
                'journal_entry_id' => $journalEntryId,
                'seller_name' => $sellerName,
                'seller_tax_number' => $sellerTax ?: null,
                'seller_address' => $sellerAddress ?: null,
                'buyer_name' => $tenant->name,
                'buyer_tax_number' => $tenantTaxNo ?: null,
                'buyer_address' => $tenantAddress ?: null,
                'description' => trim($request->description),
                'subtotal' => $subtotal,
                'vat_rate' => $vatRate,
                'vat_amount' => $vatAmount,
                'total_amount' => $totalAmount,
                'issue_date' => $issueDate,
                'zatca_qr' => $qrBase64,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        $typeAr = $invoiceType === 'tax' ? 'فاتورة ضريبية' : 'فاتورة ضريبية مبسطة';
        return redirect()->route('office.dashboard', ['tab' => 'invoicing'])
            ->with('success', "تم إصدار ({$typeAr}) رقم ({$invNumber}) وترحيل قيدها إلى شجرة حسابات المكتب بنجاح.");
    }

    public function sendOfficeInvoiceWhatsapp($id)
    {
        $officeId = $this->getCurrentOfficeId();
        $inv = DB::table('tax_invoices')
            ->leftJoin('tenants', 'tax_invoices.tenant_id', '=', 'tenants.id')
            ->leftJoin('owners', 'tax_invoices.owner_id', '=', 'owners.id')
            ->select('tax_invoices.*', 'tenants.phone as tenant_phone', 'owners.phone as owner_phone')
            ->where('tax_invoices.id', $id)
            ->where('tax_invoices.office_id', $officeId)
            ->first();

        if (!$inv) {
            return back()->withErrors(['الفاتورة غير موجودة.']);
        }

        $targetPhone = $inv->tenant_phone ?: $inv->owner_phone;
        if (empty($targetPhone)) {
            return back()->withErrors(['لا يوجد رقم جوال مسجل للعميل لإرسال الفاتورة عبر واتساب.']);
        }

        $typeAr = $inv->invoice_type === 'tax' ? 'فاتورة ضريبية' : 'فاتورة ضريبية مبسطة';
        $msg = "🧾 *إشعار {$typeAr} إلكترونية*\n"
             . "رقم الفاتورة: *{$inv->invoice_number}*\n"
             . "المُصدِر: {$inv->seller_name}\n"
             . "العميل: {$inv->buyer_name}\n"
             . "البيان: {$inv->description}\n"
             . "المبلغ قبل الضريبة: " . number_format($inv->subtotal, 2) . " ر.س\n"
             . "ضريبة القيمة المضافة ({$inv->vat_rate}%): " . number_format($inv->vat_amount, 2) . " ر.س\n"
             . "الإجمالي شامل الضريبة: *" . number_format($inv->total_amount, 2) . " ر.س*\n"
             . "تاريخ الإصدار: {$inv->issue_date}";

        $pdfToken = CompanyErpController::generateInvoicePublicToken($inv->id, $inv->invoice_number);
        $pdfDirectUrl = route('public.invoice_pdf', ['id' => $inv->id, 'token' => $pdfToken]);
        $msg .= "\n📥 رابط ملف الفاتورة الضريبية الأصلية (PDF):\n" . $pdfDirectUrl;

        $sent = CompanyErpController::sendWhatsAppDocument($targetPhone, $pdfDirectUrl, $inv->invoice_number . '.pdf', $msg, $officeId);
        if ($sent) {
            return back()->with('success', "تم إرسال الفاتورة الضريبية رقم ({$inv->invoice_number}) كملف PDF أصلي عبر الواتساب إلى ({$inv->buyer_name}) بنجاح.");
        }

        $cleanPhone = ltrim(CompanyErpController::formatSaudiPhone($targetPhone), '+');
        $waUrl = "https://wa.me/{$cleanPhone}?text=" . urlencode($msg);
        return redirect()->away($waUrl);
    }

    public function deleteOfficeInvoice($id)
    {
        return back()->withErrors(['تنبيه نظامي: الفواتير الضريبية لا يُسمح بحذفها أو تعديلها بعد إصدارها.']);
    }

    /**
     * تسديد وتحديث جدول الدفعات تلقائياً (كلي أو جزئي) وربط السند بالدفعة
     */
    private function allocateReceiptToInstallments(int $officeId, ?int $tenantId, ?int $installmentId, float $paymentAmount, int $voucherId): void
    {
        CompanyErpController::ensureContractInstallmentsTable();
        $remainingToAllocate = $paymentAmount;

        if ($installmentId) {
            $inst = DB::table('contract_installments')->where('id', $installmentId)->where('office_id', $officeId)->first();
            if ($inst) {
                $needed = max(0, $inst->amount - $inst->paid_amount);
                $applied = min($remainingToAllocate, $needed > 0 ? $needed : $remainingToAllocate);
                $newPaid = round($inst->paid_amount + $applied, 2);
                $vIds = json_decode($inst->voucher_ids_json ?? '[]', true) ?: [];
                $vIds[] = $voucherId;
                $newStatus = $newPaid >= ($inst->amount - 0.01)
                    ? 'paid'
                    : ($inst->due_date < date('Y-m-d') ? 'partial_overdue' : 'partial');

                DB::table('contract_installments')->where('id', $inst->id)->update([
                    'paid_amount' => $newPaid,
                    'status' => $newStatus,
                    'last_voucher_id' => $voucherId,
                    'voucher_ids_json' => json_encode(array_values(array_unique($vIds))),
                    'updated_at' => now(),
                ]);
                $remainingToAllocate -= $applied;
            }
        }

        if ($remainingToAllocate > 0.01 && $tenantId) {
            $openInstallments = DB::table('contract_installments')
                ->where('office_id', $officeId)
                ->where('tenant_id', $tenantId)
                ->where('status', '!=', 'paid')
                ->orderBy('due_date', 'asc')
                ->get();

            foreach ($openInstallments as $inst) {
                if ($remainingToAllocate <= 0.01) break;
                $needed = max(0, $inst->amount - $inst->paid_amount);
                if ($needed <= 0) continue;
                $applied = min($remainingToAllocate, $needed);
                $newPaid = round($inst->paid_amount + $applied, 2);
                $vIds = json_decode($inst->voucher_ids_json ?? '[]', true) ?: [];
                $vIds[] = $voucherId;
                $newStatus = $newPaid >= ($inst->amount - 0.01)
                    ? 'paid'
                    : ($inst->due_date < date('Y-m-d') ? 'partial_overdue' : 'partial');

                DB::table('contract_installments')->where('id', $inst->id)->update([
                    'paid_amount' => $newPaid,
                    'status' => $newStatus,
                    'last_voucher_id' => $voucherId,
                    'voucher_ids_json' => json_encode(array_values(array_unique($vIds))),
                    'updated_at' => now(),
                ]);
                $remainingToAllocate -= $applied;
            }
        }
    }

    public function payInstallmentPart(Request $request)
    {
        $officeId = $this->getCurrentOfficeId();
        $inst = DB::table('contract_installments')->where('id', $request->installment_id)->where('office_id', $officeId)->first();
        if (!$inst) return back()->withErrors(['الدفعة غير موجودة.']);

        $tenant = DB::table('tenants')->where('id', $inst->tenant_id)->first();
        $cashAcc = DB::table('accounts')->where('office_id', $officeId)->where('code', '1102')->first()
                ?: DB::table('accounts')->where('office_id', $officeId)->where('is_group', 0)->first();

        $request->merge([
            'voucher_type' => 'receipt',
            'tenant_id' => $inst->tenant_id,
            'owner_id' => $inst->owner_id,
            'property_id' => $inst->property_id,
            'property_unit_id' => $inst->property_unit_id,
            'lease_contract_id' => $inst->lease_contract_id,
            'installment_id' => $inst->id,
            'debit_account_id' => $request->debit_account_id ?: ($cashAcc->id ?? 1),
            'credit_account_id' => $tenant->account_id ?: ($cashAcc->id ?? 1),
            'stay_in_installments' => 1,
        ]);

        return $this->storeOfficeVoucher($request);
    }

    public function approvePendingVoucher($id)
    {
        $officeId = $this->getCurrentOfficeId();
        $v = DB::table('vouchers')->where('id', $id)->where('office_id', $officeId)->where('approval_status', 'pending')->first();
        if ($v) {
            DB::transaction(function () use ($v) {
                DB::table('vouchers')->where('id', $v->id)->update(['approval_status' => 'approved', 'updated_at' => now()]);
                CompanyErpController::applyAccountMovement((int) $v->debit_account_id, (float) $v->amount, 0);
                CompanyErpController::applyAccountMovement((int) $v->credit_account_id, 0, (float) $v->amount);
            });
        }
        return back()->with('success', 'تم اعتماد سند الصرف وترحيله لشجرة الحسابات بنجاح.');
    }

    /**
     * إصدار سندات قبض مجمّعة (Batch Receipts) مع فصل طريقة الدفع وحساب الصندوق/البنك لكل مستأجر على حدة
     * والربط التلقائي بحساب المستأجر وحساب أمانات المالك في الشجرة
     */
    public function storeBatchReceipts(Request $request)
    {
        app(CompanyErpController::class)->ensureDefaultAdminExists();
        $officeId = $this->getCurrentOfficeId();
        $tenantIds = $request->input('tenant_ids', []);
        $amounts = $request->input('amounts', []);
        $methods = $request->input('payment_methods', []);
        $notesList = $request->input('batch_notes', []);
        $vDate = $request->voucher_date ?: date('Y-m-d');

        $officeAccounts = DB::table('accounts')->where('office_id', $officeId)->where('is_group', 0)->get();
        $defaultCashAcc = $officeAccounts->firstWhere('code', '1102') ?: ($officeAccounts->firstWhere('code', '1101') ?: $officeAccounts->first());
        $defaultDebitId = $defaultCashAcc ? (int) $defaultCashAcc->id : 1;

        $paymentMethodsRows = DB::table('custom_payment_methods')
            ->where(function ($q) use ($officeId) {
                $q->where('office_id', $officeId)->orWhereNull('office_id');
            })->get();

        $countCreated = 0;
        DB::transaction(function () use ($officeId, $tenantIds, $amounts, $methods, $notesList, $defaultDebitId, $officeAccounts, $paymentMethodsRows, $vDate, &$countCreated) {
            foreach ($tenantIds as $idx => $tnId) {
                $amt = (float) ($amounts[$idx] ?? 0);
                if (empty($tnId) || $amt <= 0) continue;

                $tenant = DB::table('tenants')->where('id', $tnId)->where('office_id', $officeId)->first();
                if (!$tenant) continue;

                $chosenMethodName = trim((string) ($methods[$idx] ?? 'حوالة بنكية'));
                $pmObj = $paymentMethodsRows->firstWhere('name', $chosenMethodName);

                // التأكد أن حساب طريقة الدفع ينتمي لشجرة هذا المكتب وإلا نستخدم حساب البنك/الصندوق الافتراضي للمكتب
                $debitAccId = $defaultDebitId;
                if ($pmObj && !empty($pmObj->account_id) && $officeAccounts->contains('id', $pmObj->account_id)) {
                    $debitAccId = (int) $pmObj->account_id;
                }

                $activeContract = DB::table('lease_contracts')
                    ->where('office_id', $officeId)
                    ->where('tenant_id', $tnId)
                    ->orderBy('id', 'desc')
                    ->first();

                $vNo = CompanyErpController::generateNextVoucherNumber('receipt', $officeId);
                $creditAccId = $tenant->account_id ?: $debitAccId;
                $ownerId = $activeContract ? (int) $activeContract->owner_id : null;

                $payload = [
                    'voucher_number' => $vNo,
                    'voucher_type' => 'receipt',
                    'office_id' => $officeId,
                    'debit_account_id' => $debitAccId,
                    'credit_account_id' => $creditAccId,
                    'owner_id' => $ownerId,
                    'tenant_id' => (int) $tnId,
                    'lease_contract_id' => $activeContract->id ?? null,
                    'amount' => $amt,
                    'payment_method' => CompanyErpController::normalizeVoucherPaymentMethod($chosenMethodName),
                    'voucher_date' => $vDate,
                    'notes' => trim($notesList[$idx] ?? '') ?: ('تحصيل دفعة إيجار مجمعة - ' . $tenant->name),
                    'created_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (\Illuminate\Support\Facades\Schema::hasColumn('vouchers', 'property_id')) {
                    $payload['property_id'] = $activeContract->property_id ?? null;
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn('vouchers', 'property_unit_id')) {
                    $payload['property_unit_id'] = $activeContract->property_unit_id ?? null;
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn('vouchers', 'approval_status')) {
                    $payload['approval_status'] = 'approved';
                }

                $vId = DB::table('vouchers')->insertGetId($payload);

                // تحديث شجرة الحسابات: مدين (بنك/صندوق المكتب) ودائن (حساب المستأجر)
                CompanyErpController::applyAccountMovement($debitAccId, $amt, 0);
                CompanyErpController::applyAccountMovement((int) $creditAccId, 0, $amt);

                // ترحيل الاستحقاق لأمانات المالك في الشجرة تلقائياً
                if ($ownerId) {
                    $ownerRow = DB::table('owners')->where('id', $ownerId)->first();
                    if ($ownerRow && $ownerRow->account_id) {
                        CompanyErpController::applyAccountMovement((int) $creditAccId, $amt, 0);
                        CompanyErpController::applyAccountMovement((int) $ownerRow->account_id, 0, $amt);
                    }
                }

                $this->allocateReceiptToInstallments($officeId, (int) $tnId, null, $amt, $vId);
                $countCreated++;
            }
        });

        if ($countCreated === 0) {
            return back()->withErrors(['يرجى اختيار مستأجر واحد على الأقل وإدخال المبلغ المحصل أمامه.']);
        }

        return redirect()->route('office.dashboard', ['tab' => 'vouchers'])
            ->with('success', "تم إصدار ({$countCreated}) سند قبض مجمّع وإضافتها إلى جدول سندات القبض وترحيلها للشجرة بنجاح.");
    }

    /**
     * حفظ إعدادات كاشف الشذوذ المحاسبي + اعتماد الواتساب + نصوص رسائل الواتساب المرفقة بملفات الـ PDF
     */
    public function saveSmartOfficeSettings(Request $request)
    {
        $officeId = $this->getCurrentOfficeId();
        DB::table('offices')->where('id', $officeId)->update([
            'enable_wa_voucher_approval' => $request->has('enable_wa_voucher_approval') ? 1 : 0,
            'wa_approval_threshold' => (float) ($request->wa_approval_threshold ?? 10000),
            'wa_approval_keywords' => trim($request->wa_approval_keywords ?: 'اعتمد,تم,اوك,موافق,نعم'),
            'enable_anomaly_detector' => $request->has('enable_anomaly_detector') ? 1 : 0,
            'anomaly_max_amount' => (float) ($request->anomaly_max_amount ?? 20000),
            'anomaly_night_start' => (int) ($request->anomaly_night_start ?? 23),
            'anomaly_night_end' => (int) ($request->anomaly_night_end ?? 5),
            'anomaly_alert_on_delete' => $request->has('anomaly_alert_on_delete') ? 1 : 0,
            'wa_invoice_caption' => trim((string) $request->input('wa_invoice_caption', '')),
            'wa_voucher_caption' => trim((string) $request->input('wa_voucher_caption', '')),
            'wa_legal_caption' => trim((string) $request->input('wa_legal_caption', '')),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'تم حفظ إعدادات الواتساب ونصوص رسائل ملفات الـ PDF وشروط الشذوذ والاعتماد بنجاح.');
    }

    /**
     * إقفال السنة المالية للمكتب العقاري وترحيل صافي الأرباح لحقوق الملكية
     */
    public function closeFiscalYear(Request $request)
    {
        $officeId = $this->getCurrentOfficeId();
        $accounts = DB::table('accounts')->where('office_id', $officeId)->where('is_group', 0)->get();
        $revAccounts = $accounts->filter(fn($a) => str_contains($a->type, 'إيراد') || str_starts_with($a->code, '4'));
        $expAccounts = $accounts->filter(fn($a) => str_contains($a->type, 'مصروف') || str_starts_with($a->code, '5'));

        $totalRev = $revAccounts->sum(fn($a) => abs($a->balance));
        $totalExp = $expAccounts->sum(fn($a) => abs($a->balance));
        $netProfit = $totalRev - $totalExp;

        if ($totalRev == 0 && $totalExp == 0) {
            return back()->withErrors(['لا توجد أرصدة إيرادات أو مصروفات مفتوحة لإقفالها في هذه السنة.']);
        }

        DB::transaction(function () use ($officeId, $revAccounts, $expAccounts, $netProfit) {
            $eqAcc = DB::table('accounts')->where('office_id', $officeId)->where('code', '3101')->first();
            if (!$eqAcc) {
                $eqId = DB::table('accounts')->insertGetId([
                    'office_id' => $officeId, 'parent_id' => null, 'code' => '3101',
                    'name' => 'الأرباح المبقاة وحقوق ملكية المكتب (إقفال سنوي)', 'type' => 'حقوق الملكية',
                    'is_group' => 0, 'balance' => 0, 'debit_balance' => 0, 'credit_balance' => 0,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            } else {
                $eqId = $eqAcc->id;
            }

            foreach ($revAccounts as $r) {
                DB::table('accounts')->where('id', $r->id)->update(['balance' => 0, 'debit_balance' => 0, 'credit_balance' => 0]);
            }
            foreach ($expAccounts as $e) {
                DB::table('accounts')->where('id', $e->id)->update(['balance' => 0, 'debit_balance' => 0, 'credit_balance' => 0]);
            }

            CompanyErpController::applyAccountMovement((int) $eqId, $netProfit < 0 ? abs($netProfit) : 0, $netProfit >= 0 ? $netProfit : 0);
        });

        return back()->with('success', 'تم إقفال السنة المالية للمكتب بنجاح وترحيل صافي الربح (' . number_format($netProfit, 2) . ' ر.س) إلى حساب الأرباح المبقاة.');
    }

    // =========================================================================
    // 1. ماسح الصكوك والعقود بالذكاء الاصطناعي (AI OCR) + إنشاء المالك وربط الوحدة تلقائياً
    // =========================================================================
    public function syncOcrDeedOrContract(Request $request)
    {
        $officeId = $this->getCurrentOfficeId();
        CompanyErpController::ensureContractInstallmentsTable();

        $ownerName     = trim((string) $request->input('owner_name', ''));
        $ownerNatId    = preg_replace('/\D/', '', (string) $request->input('owner_national_id', ''));
        $ownerPhoneRaw = trim((string) $request->input('owner_phone', ''));
        $deedNumber    = trim((string) $request->input('deed_number', ''));
        $totalArea     = (float) ($request->input('total_area', 0) ?: 500);
        $propTitle     = trim((string) $request->input('property_title', ''));
        $unitNumber    = trim((string) $request->input('unit_number', ''));
        $rentAmount    = (float) ($request->input('total_rent_amount', 0) ?: 25000);
        $tenantName    = trim((string) $request->input('tenant_name', ''));
        $tenantNatId   = preg_replace('/\D/', '', (string) $request->input('tenant_id_number', ''));
        $tenantPhoneRaw= trim((string) $request->input('tenant_phone', ''));

        $createdNewOwner = false;
        $createdNewTenant = false;
        $linkedUnitInfo = '';

        $result = DB::transaction(function () use (
            $officeId, $ownerName, $ownerNatId, $ownerPhoneRaw, $deedNumber, $totalArea,
            $propTitle, $unitNumber, $rentAmount, $tenantName, $tenantNatId, $tenantPhoneRaw,
            &$createdNewOwner, &$createdNewTenant, &$linkedUnitInfo
        ) {
            // 1. البحث عن المالك في النظام أو إنشاؤه وحفظه تلقائياً إذا لم يكن موجوداً
            $owner = null;
            if (!empty($ownerNatId)) {
                $owner = DB::table('owners')->where('office_id', $officeId)->where('national_id', $ownerNatId)->first();
            }
            if (!$owner && !empty($ownerName)) {
                $owner = DB::table('owners')->where('office_id', $officeId)->where('name', 'like', '%' . $ownerName . '%')->first();
            }

            if (!$owner) {
                $finalOwnerName = $ownerName !== '' ? $ownerName : ('مالك صك ' . ($deedNumber ?: rand(1000, 9999)));
                $finalOwnerNat  = $ownerNatId !== '' ? $ownerNatId : ('10' . rand(10000000, 99999999));
                $cleanPhone     = preg_replace('/\D/', '', $ownerPhoneRaw);
                if (strlen($cleanPhone) < 9) {
                    $cleanPhone = '050' . rand(1000000, 9999999);
                }
                $formattedOwnerPhone = CompanyErpController::formatSaudiPhone($cleanPhone);

                $defaultParent = DB::table('accounts')->where('office_id', $officeId)->where('code', '2101')->first();
                $ownerAccountId = CompanyErpController::createSequentialSubAccount(
                    $officeId,
                    $defaultParent ? $defaultParent->id : null,
                    'أمانات المالك: ' . $finalOwnerName
                );

                $ownerUsername = 'owner_ocr_' . time() . '_' . rand(10, 99);
                $newOwnerId = DB::table('owners')->insertGetId([
                    'office_id' => $officeId,
                    'name' => $finalOwnerName,
                    'national_id' => $finalOwnerNat,
                    'phone' => $formattedOwnerPhone,
                    'email' => $ownerUsername . '@owner.local',
                    'office_management_fee_rate' => 5.00,
                    'account_id' => $ownerAccountId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('users')->insert([
                    'name' => $finalOwnerName,
                    'username' => $ownerUsername,
                    'phone' => $formattedOwnerPhone,
                    'email' => $ownerUsername . '@owner.local',
                    'national_id' => $finalOwnerNat,
                    'password' => Hash::make('123456'),
                    'user_type' => 'owner',
                    'office_id' => $officeId,
                    'owner_id' => $newOwnerId,
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $owner = DB::table('owners')->where('id', $newOwnerId)->first();
                $createdNewOwner = true;
            }

            // 2. البحث عن الوحدة أو العقار الموجود في النظام وربط المالك به تلقائياً
            $matchedUnit = null;
            $matchedProperty = null;

            if (!empty($unitNumber)) {
                $matchedUnit = DB::table('property_units')
                    ->join('properties', 'property_units.property_id', '=', 'properties.id')
                    ->where('properties.office_id', $officeId)
                    ->where(function ($q) use ($unitNumber) {
                        $q->where('property_units.unit_number', $unitNumber)
                          ->orWhere('property_units.unit_number', 'like', '%' . $unitNumber . '%');
                    })
                    ->select('property_units.*')
                    ->first();

                if ($matchedUnit) {
                    $matchedProperty = DB::table('properties')->where('id', $matchedUnit->property_id)->first();
                }
            }

            if (!$matchedProperty && !empty($deedNumber)) {
                $matchedProperty = DB::table('properties')
                    ->where('office_id', $officeId)
                    ->where('deed_number', $deedNumber)
                    ->first();
            }

            if (!$matchedProperty && !empty($propTitle)) {
                $matchedProperty = DB::table('properties')
                    ->where('office_id', $officeId)
                    ->where('title', 'like', '%' . $propTitle . '%')
                    ->first();
            }

            // إذا لم يكن هناك عقار مطابق، نبحث عن أول وحدة شاغرة في النظام لربطها أو ننشئ العقار والوحدة تلقائياً
            if (!$matchedProperty) {
                $vacantUnitJoin = DB::table('property_units')
                    ->join('properties', 'property_units.property_id', '=', 'properties.id')
                    ->where('properties.office_id', $officeId)
                    ->where('property_units.status', 'vacant')
                    ->select('property_units.*')
                    ->first();

                if ($vacantUnitJoin && empty($deedNumber) && empty($propTitle)) {
                    $matchedUnit = $vacantUnitJoin;
                    $matchedProperty = DB::table('properties')->where('id', $vacantUnitJoin->property_id)->first();
                } else {
                    $firstPropType = DB::table('property_types')->first();
                    $propTypeId = $firstPropType ? $firstPropType->id : 1;
                    $newPropTitle = $propTitle !== '' ? $propTitle : ('عقار صك رقم ' . ($deedNumber ?: rand(1000, 9999)));

                    $newPropId = DB::table('properties')->insertGetId([
                        'office_id' => $officeId,
                        'owner_id' => $owner->id,
                        'property_type_id' => $propTypeId,
                        'title' => $newPropTitle,
                        'deed_number' => $deedNumber ?: ('3101' . rand(10000000, 99999999)),
                        'deed_date' => date('Y-m-d'),
                        'total_area' => $totalArea > 0 ? $totalArea : 500,
                        'city' => 'الرياض',
                        'district' => 'حي الملقا',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $matchedProperty = DB::table('properties')->where('id', $newPropId)->first();
                }
            }

            // ربط المالك بالعقار والوحدة الموجودة على النظام وتحديث رقم الصك والمساحة
            if ($matchedProperty) {
                $propUpdates = [
                    'owner_id' => $owner->id,
                    'updated_at' => now(),
                ];
                if (!empty($deedNumber)) $propUpdates['deed_number'] = $deedNumber;
                if ($totalArea > 0) $propUpdates['total_area'] = $totalArea;
                DB::table('properties')->where('id', $matchedProperty->id)->update($propUpdates);
                $matchedProperty = DB::table('properties')->where('id', $matchedProperty->id)->first();
            }

            if (!$matchedUnit && $matchedProperty) {
                $matchedUnit = DB::table('property_units')
                    ->where('property_id', $matchedProperty->id)
                    ->orderByRaw("CASE WHEN status = 'vacant' THEN 0 ELSE 1 END")
                    ->first();

                if (!$matchedUnit) {
                    $newUnitId = DB::table('property_units')->insertGetId([
                        'property_id' => $matchedProperty->id,
                        'property_type_id' => $matchedProperty->property_type_id,
                        'unit_number' => $unitNumber !== '' ? $unitNumber : 'وحدة 1',
                        'floor_number' => 1,
                        'area' => $totalArea > 0 ? min($totalArea, 180) : 150,
                        'default_rent_price' => $rentAmount > 0 ? $rentAmount : 25000,
                        'status' => 'vacant',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $matchedUnit = DB::table('property_units')->where('id', $newUnitId)->first();
                }
            }

            if ($matchedUnit) {
                $linkedUnitInfo = $matchedProperty->title . ' — ' . $matchedUnit->unit_number;
            }

            // 3. البحث عن المستأجر أو إنشاؤه تلقائياً إذا وُجد في ملف عقد إيجار PDF
            $tenant = null;
            if (!empty($tenantNatId)) {
                $tenant = DB::table('tenants')->where('office_id', $officeId)->where('id_number', $tenantNatId)->first();
            }
            if (!$tenant && !empty($tenantName)) {
                $tenant = DB::table('tenants')->where('office_id', $officeId)->where('name', 'like', '%' . $tenantName . '%')->first();
            }
            if (!$tenant && !empty($tenantName)) {
                $cleanTPhone = preg_replace('/\D/', '', $tenantPhoneRaw);
                if (strlen($cleanTPhone) < 9) {
                    $cleanTPhone = '055' . rand(1000000, 9999999);
                }
                $defaultTenantParent = DB::table('accounts')->where('office_id', $officeId)->where('code', '1103')->first();
                $tAccId = CompanyErpController::createSequentialSubAccount(
                    $officeId,
                    $defaultTenantParent ? $defaultTenantParent->id : null,
                    'المستأجر: ' . $tenantName
                );
                $newTenantId = DB::table('tenants')->insertGetId([
                    'office_id' => $officeId,
                    'name' => $tenantName,
                    'id_type' => 'national_id',
                    'id_number' => $tenantNatId ?: ('10' . rand(10000000, 99999999)),
                    'phone' => CompanyErpController::formatSaudiPhone($cleanTPhone),
                    'account_id' => $tAccId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $tenant = DB::table('tenants')->where('id', $newTenantId)->first();
                $createdNewTenant = true;
            }

            return [
                'owner' => $owner,
                'property' => $matchedProperty,
                'unit' => $matchedUnit,
                'tenant' => $tenant,
            ];
        });

        return response()->json([
            'success' => true,
            'created_new_owner' => $createdNewOwner,
            'created_new_tenant' => $createdNewTenant,
            'owner_id' => $result['owner']->id ?? null,
            'owner_name' => $result['owner']->name ?? '',
            'owner_national_id' => $result['owner']->national_id ?? '',
            'property_id' => $result['property']->id ?? null,
            'property_title' => $result['property']->title ?? '',
            'deed_number' => $result['property']->deed_number ?? $deedNumber,
            'total_area' => $result['property']->total_area ?? $totalArea,
            'property_unit_id' => $result['unit']->id ?? null,
            'unit_number' => $result['unit']->unit_number ?? '',
            'tenant_id' => $result['tenant']->id ?? null,
            'tenant_name' => $result['tenant']->name ?? '',
            'linked_unit_info' => $linkedUnitInfo,
            'message' => $createdNewOwner
                ? "✅ تم قراءة الملف وإنشاء سجل جديد للمالك ({$result['owner']->name}) تلقائياً وربطه بالوحدة ({$linkedUnitInfo}) وتعبئة الحقول!"
                : "✅ تم قراءة الملف والتعرف على المالك ({$result['owner']->name}) وربطه بالوحدة ({$linkedUnitInfo}) وتعبئة الحقول تلقائياً!",
        ]);
    }

    // =========================================================================
    // 2. المدقق المحاسبي الآلي قبل الإقفال (Pre-Closing Automated Accounting Auditor)
    // =========================================================================
    private function runPreClosingAccountingAuditInternal(int $officeId, $vouchers, $owners, $contracts, $journalEntries): array
    {
        $untransferredReceipts = [];
        $unrecordedCommissions = [];
        $unlinkedVouchers = [];

        $ownersById = collect($owners)->keyBy('id');
        $contractsByTenant = collect($contracts)->keyBy('tenant_id');
        $journalNotesConcat = collect($journalEntries)->pluck('general_notes')->implode(' | ');

        foreach ($vouchers as $v) {
            if ($v->voucher_type !== 'receipt') {
                if (empty($v->owner_id) && empty($v->tenant_id)) {
                    $unlinkedVouchers[] = $v;
                }
                continue;
            }

            $resolvedOwnerId = $v->owner_id;
            if (!$resolvedOwnerId && $v->tenant_id && isset($contractsByTenant[$v->tenant_id])) {
                $resolvedOwnerId = $contractsByTenant[$v->tenant_id]->owner_id;
            }

            $ownerObj = $resolvedOwnerId && isset($ownersById[$resolvedOwnerId]) ? $ownersById[$resolvedOwnerId] : null;
            $feeRate = $ownerObj ? (float) ($ownerObj->office_management_fee_rate ?? 5) : 5.0;
            $expectedCommission = round(($v->amount * $feeRate) / 100, 2);
            $expectedOwnerNet = round($v->amount - $expectedCommission, 2);

            $v->expected_commission = $expectedCommission;
            $v->expected_owner_net = $expectedOwnerNet;
            $v->fee_rate = $feeRate;
            $v->resolved_owner_id = $resolvedOwnerId;
            $v->resolved_owner_name = $ownerObj->name ?? ($v->owner_name ?: 'غير محدد');

            // 1. فحص هل تم ترحيل الدفعة لحساب أمانات المالك؟
            $isOwnerCredited = !empty($v->owner_credited)
                || ($ownerObj && (int) $v->credit_account_id === (int) $ownerObj->account_id)
                || str_contains($journalNotesConcat, $v->voucher_number);

            if (!$isOwnerCredited || empty($v->owner_id)) {
                $untransferredReceipts[] = $v;
            }

            // 2. فحص هل تم احتساب عمولة المكتب على دفعة الإيجار؟
            $isCommissionRecorded = !empty($v->commission_recorded)
                || (str_contains($journalNotesConcat, 'عمولة') && str_contains($journalNotesConcat, $v->voucher_number));

            if (!$isCommissionRecorded && $expectedCommission > 0) {
                $unrecordedCommissions[] = $v;
            }
        }

        return [
            'untransferred_receipts' => $untransferredReceipts,
            'unrecorded_commissions' => $unrecordedCommissions,
            'unlinked_vouchers' => $unlinkedVouchers,
            'total_issues' => count($untransferredReceipts) + count($unrecordedCommissions),
        ];
    }

    public function autoFixAuditedVoucher(Request $request, $id)
    {
        $officeId = $this->getCurrentOfficeId();
        app(CompanyErpController::class)->ensureJournalTables();
        CompanyErpController::ensureContractInstallmentsTable();

        $v = DB::table('vouchers')->where('id', $id)->where('office_id', $officeId)->first();
        if (!$v) {
            return back()->withErrors(['السند غير موجود.']);
        }

        $ownerId = $request->filled('owner_id') ? (int) $request->owner_id : $v->owner_id;
        if (!$ownerId && $v->tenant_id) {
            $c = DB::table('lease_contracts')->where('office_id', $officeId)->where('tenant_id', $v->tenant_id)->orderBy('id', 'desc')->first();
            if ($c) $ownerId = $c->owner_id;
        }

        $owner = $ownerId ? DB::table('owners')->where('id', $ownerId)->where('office_id', $officeId)->first() : null;
        if (!$owner) {
            return redirect()->route('office.dashboard', ['tab' => 'auditor'])
                ->withErrors(['يرجى تحديد المالك المرتبط بالسند أولاً من زر (تعديل السند) ليتم ترحيل الأمانات والعمولة.']);
        }

        $feeRate = (float) ($owner->office_management_fee_rate ?? 5);
        $commissionAmt = round(($v->amount * $feeRate) / 100, 2);
        $netOwnerAmt = round($v->amount - $commissionAmt, 2);

        $revAcc = DB::table('accounts')->where('office_id', $officeId)->where('code', '4101')->first()
               ?: DB::table('accounts')->where('office_id', $officeId)->where('type', 'like', '%إيراد%')->where('is_group', 0)->first();

        DB::transaction(function () use ($officeId, $v, $owner, $feeRate, $commissionAmt, $netOwnerAmt, $revAcc) {
            DB::table('vouchers')->where('id', $v->id)->update([
                'owner_id' => $owner->id,
                'owner_credited' => 1,
                'commission_recorded' => 1,
                'updated_at' => now(),
            ]);

            $maxNo = DB::table('journal_entries')->where('office_id', $officeId)->max('entry_number') ?? 0;
            $entryId = DB::table('journal_entries')->insertGetId([
                'office_id' => $officeId,
                'entry_number' => $maxNo + 1,
                'entry_date' => date('Y-m-d'),
                'general_notes' => "قيد تدقيق آلي للسند ({$v->voucher_number}): ترحيل أمانات المالك ({$owner->name}) واحتساب عمولة المكتب ({$feeRate}%)",
                'total_debit' => $v->amount,
                'total_credit' => $v->amount,
                'total_tax' => 0,
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // مدين: حساب المستأجر/الوسيط
            DB::table('journal_entry_items')->insert([
                'journal_entry_id' => $entryId,
                'account_id' => $v->credit_account_id,
                'debit' => $v->amount,
                'credit' => 0,
                'tax' => 0,
                'notes' => "تسوية وترحيل دفعة السند ({$v->voucher_number})",
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // دائن 1: صافي أمانات المالك
            if ($owner->account_id && $netOwnerAmt > 0) {
                DB::table('journal_entry_items')->insert([
                    'journal_entry_id' => $entryId,
                    'account_id' => $owner->account_id,
                    'debit' => 0,
                    'credit' => $netOwnerAmt,
                    'tax' => 0,
                    'notes' => "صافي أمانات المالك ({$owner->name}) بعد خصم عمولة {$feeRate}%",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                CompanyErpController::applyAccountMovement((int) $owner->account_id, 0, $netOwnerAmt);
            }

            // دائن 2: إيراد عمولة المكتب
            if ($revAcc && $commissionAmt > 0) {
                DB::table('journal_entry_items')->insert([
                    'journal_entry_id' => $entryId,
                    'account_id' => $revAcc->id,
                    'debit' => 0,
                    'credit' => $commissionAmt,
                    'tax' => 0,
                    'notes' => "عمولة إدارة الأملاك ({$feeRate}%) على السند ({$v->voucher_number})",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                CompanyErpController::applyAccountMovement((int) $revAcc->id, 0, $commissionAmt);
            }
        });

        return redirect()->route('office.dashboard', ['tab' => 'auditor'])
            ->with('success', "✅ تم تدقيق ومعالجة السند ({$v->voucher_number}): ترحيل صافي ({$netOwnerAmt} ر.س) لأمانات المالك ({$owner->name}) وتسجيل عمولة المكتب ({$commissionAmt} ر.س) في قيد يومي متوازن!");
    }

    // =========================================================================
    // 3. حفظ إعدادات التقرير اليومي المصغر لمدير المكتب الساعة 9 مساءً (PDF عبر واتساب)
    // =========================================================================
    public function saveDailyReportSettings(Request $request)
    {
        $officeId = $this->getCurrentOfficeId();
        CompanyErpController::ensureContractInstallmentsTable();

        $settings = [
            'enabled' => $request->has('enable_daily_report') ? 1 : 0,
            'send_time' => $request->input('daily_report_time', '21:00'),
            'recipient_phone' => trim((string) $request->input('daily_report_phone', '')),
            'inc_receipts' => $request->has('inc_receipts') ? 1 : 0,
            'inc_payments' => $request->has('inc_payments') ? 1 : 0,
            'inc_new_contracts' => $request->has('inc_new_contracts') ? 1 : 0,
            'inc_overdue' => $request->has('inc_overdue') ? 1 : 0,
            'inc_net_cash' => $request->has('inc_net_cash') ? 1 : 0,
        ];

        DB::table('offices')->where('id', $officeId)->update([
            'daily_report_settings_json' => json_encode($settings, JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);

        return redirect()->route('office.dashboard', ['tab' => 'whatsapp'])
            ->with('success', 'تم حفظ إعدادات وتوقيت التقرير اليومي المصغر لمدير المكتب (PDF أصلي عبر واتساب) بنجاح.');
    }

    // =========================================================================
    // 4. نظام إدارة اتحاد الملاك (Mullak) المطور مع جلب بيانات العمارة والمستأجرين وإعادة احتساب الرسوم تلقائياً
    // =========================================================================

    /**
     * إعادة احتساب وتوزيع رسوم الصيانة السنوية للعمارة تلقائياً على جميع الأعضاء المسجلين
     * سواء بالتساوي (equal) أو حسب عدد الوحدات لكل عضو (by_units)
     */
    private function recalculateMullakBuildingFees(int $officeId, int $buildingId): void
    {
        $building = DB::table('mullak_buildings')->where('id', $buildingId)->where('office_id', $officeId)->first();
        if (!$building) {
            return;
        }

        $members = DB::table('mullak_members')->where('mullak_building_id', $buildingId)->where('office_id', $officeId)->get();
        $membersCount = $members->count();
        if ($membersCount === 0) {
            return;
        }

        $totalPool = (float) ($building->default_annual_fee ?? 0);
        if ($totalPool <= 0) {
            return;
        }

        $method = $building->distribution_method ?: 'equal';
        $totalMemberUnits = max(1, (int) $members->sum(fn($m) => max(1, (int) ($m->units_count ?? 1))));

        foreach ($members as $m) {
            $memberUnits = max(1, (int) ($m->units_count ?? 1));
            if ($method === 'by_units') {
                $sharePct = round(($memberUnits / $totalMemberUnits) * 100, 2);
                $calculatedFee = round($totalPool * ($memberUnits / $totalMemberUnits), 2);
            } else {
                $sharePct = round((1 / $membersCount) * 100, 2);
                $calculatedFee = round($totalPool / $membersCount, 2);
            }

            $paid = (float) ($m->paid_amount ?? 0);
            $newStatus = $paid >= ($calculatedFee - 0.01) ? 'paid' : ($paid > 0 ? 'partial' : 'due');

            DB::table('mullak_members')->where('id', $m->id)->update([
                'annual_fee' => $calculatedFee,
                'area_share_pct' => $sharePct,
                'status' => $newStatus,
                'updated_at' => now(),
            ]);
        }
    }

    public function storeMullakBuilding(Request $request)
    {
        $officeId = $this->getCurrentOfficeId();
        CompanyErpController::ensureContractInstallmentsTable();

        $prop = null;
        if ($request->filled('property_id')) {
            $prop = DB::table('properties')
                ->leftJoin('owners', 'properties.owner_id', '=', 'owners.id')
                ->where('properties.id', (int) $request->property_id)
                ->where('properties.office_id', $officeId)
                ->select('properties.*', 'owners.name as owner_name')
                ->first();
        }

        $assocName = trim((string) $request->input('association_name', ''));
        if ($assocName === '' && $prop) {
            $assocName = 'اتحاد ملاك ' . $prop->title;
        }
        if ($assocName === '') {
            return back()->withErrors(['يرجى اختيار العمارة أو كتابة اسم جمعية اتحاد الملاك.']);
        }

        DB::table('mullak_buildings')->insert([
            'office_id' => $officeId,
            'property_id' => $prop ? $prop->id : null,
            'association_name' => $assocName,
            'bylaws_number' => trim($request->bylaws_number ?: ('MLK-' . date('Y') . '-' . rand(1000, 9999))),
            'deed_number' => trim($request->deed_number ?: ($prop->deed_number ?? '')),
            'owner_name' => trim($request->owner_name ?: ($prop->owner_name ?? '')),
            'address' => trim($request->address ?: ($prop ? ($prop->city . ' - ' . $prop->district) : '')),
            'total_area' => (float) ($request->total_area ?: ($prop->total_area ?? 0)),
            'units_count' => max(1, (int) ($request->units_count ?: 1)),
            'distribution_method' => $request->input('distribution_method', 'equal'),
            'default_annual_fee' => (float) ($request->default_annual_fee ?? 12000),
            'guard_monthly_salary' => (float) ($request->guard_monthly_salary ?? 1500),
            'electricity_common_meter' => trim($request->electricity_common_meter ?? ''),
            'water_common_meter' => trim($request->water_common_meter ?? ''),
            'notes' => trim($request->notes ?? ''),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('office.dashboard', ['tab' => 'mullak'])
            ->with('success', "تم إنشاء جمعية اتحاد الملاك على العقار ({$assocName}) وإضافتها في الجدول بنجاح.");
    }

    public function deleteMullakBuilding($id)
    {
        $officeId = $this->getCurrentOfficeId();
        DB::transaction(function () use ($officeId, $id) {
            DB::table('mullak_members')->where('mullak_building_id', $id)->where('office_id', $officeId)->delete();
            DB::table('mullak_expenses')->where('mullak_building_id', $id)->where('office_id', $officeId)->delete();
            DB::table('mullak_buildings')->where('id', $id)->where('office_id', $officeId)->delete();
        });

        return redirect()->route('office.dashboard', ['tab' => 'mullak'])
            ->with('success', 'تم حذف جمعية اتحاد الملاك وكافة السجلات المرتبطة بها.');
    }

    public function storeMullakMember(Request $request)
    {
        $officeId = $this->getCurrentOfficeId();
        CompanyErpController::ensureContractInstallmentsTable();

        $request->validate([
            'mullak_building_id' => 'required',
        ]);

        $buildingId = (int) $request->mullak_building_id;
        $building = DB::table('mullak_buildings')->where('id', $buildingId)->where('office_id', $officeId)->first();
        if (!$building) {
            return back()->withErrors(['يرجى اختيار عمارة مسجل عليها اتحاد ملاك أولاً.']);
        }

        // تحديث طريقة توزيع الرسوم وإجمالي الرسوم السنوية للعمارة إذا قام المستخدم بتحديدها في النافذة
        $buildingUpdates = ['updated_at' => now()];
        if ($request->filled('distribution_method')) {
            $buildingUpdates['distribution_method'] = $request->input('distribution_method');
        }
        if ($request->filled('total_building_fee') && (float) $request->total_building_fee > 0) {
            $buildingUpdates['default_annual_fee'] = (float) $request->total_building_fee;
        }
        DB::table('mullak_buildings')->where('id', $buildingId)->update($buildingUpdates);

        DB::transaction(function () use ($request, $officeId, $buildingId) {
            // 1. إذا تم استيراد المستأجرين تلقائياً من العمارة (قائمة متعددة)
            $selectedRows = $request->input('selected_tenants', []);
            $bulkNames    = $request->input('bulk_member_names', []);
            $bulkPhones   = $request->input('bulk_phones', []);
            $bulkUnits    = $request->input('bulk_unit_numbers', []);
            $bulkUnitCnts = $request->input('bulk_units_counts', []);
            $bulkRoles    = $request->input('bulk_roles', []);
            $bulkTenantIds= $request->input('bulk_tenant_ids', []);

            if (is_array($selectedRows) && count($selectedRows) > 0) {
                foreach ($selectedRows as $rowIdx) {
                    $mName = trim((string) ($bulkNames[$rowIdx] ?? ''));
                    if ($mName === '') continue;

                    $uNo = trim((string) ($bulkUnits[$rowIdx] ?? 'وحدة 1'));
                    $tId = !empty($bulkTenantIds[$rowIdx]) ? (int) $bulkTenantIds[$rowIdx] : null;
                    $uCount = max(1, (int) ($bulkUnitCnts[$rowIdx] ?? 1));

                    // منع تكرار نفس المستأجر في نفس العمارة
                    $exists = DB::table('mullak_members')
                        ->where('mullak_building_id', $buildingId)
                        ->where('member_name', $mName)
                        ->where('unit_number', $uNo)
                        ->exists();

                    if (!$exists) {
                        DB::table('mullak_members')->insert([
                            'office_id' => $officeId,
                            'mullak_building_id' => $buildingId,
                            'tenant_id' => $tId,
                            'unit_number' => $uNo,
                            'units_count' => $uCount,
                            'member_role' => $bulkRoles[$rowIdx] ?? 'tenant',
                            'member_name' => $mName,
                            'phone' => trim((string) ($bulkPhones[$rowIdx] ?? '')),
                            'annual_fee' => 0,
                            'paid_amount' => 0,
                            'billing_year' => date('Y'),
                            'status' => 'due',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            // 2. إذا تم إدخال عضو إضافي جديد يدوياً في نفس النافذة
            $singleName = trim((string) $request->input('member_name', ''));
            if ($singleName !== '') {
                DB::table('mullak_members')->insert([
                    'office_id' => $officeId,
                    'mullak_building_id' => $buildingId,
                    'unit_number' => trim((string) $request->input('unit_number', 'وحدة إضافية')),
                    'floor_number' => trim((string) $request->input('floor_number', '')),
                    'units_count' => max(1, (int) ($request->input('units_count', 1) ?: 1)),
                    'member_role' => $request->input('member_role', 'tenant'),
                    'member_name' => $singleName,
                    'phone' => trim((string) $request->input('phone', '')),
                    'national_id' => trim((string) $request->input('national_id', '')),
                    'annual_fee' => (float) ($request->input('annual_fee', 0) ?: 0),
                    'paid_amount' => (float) ($request->input('paid_amount', 0) ?: 0),
                    'billing_year' => date('Y'),
                    'status' => 'due',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 3. إعادة احتساب وتوزيع رسوم العمارة تلقائياً على جميع الأعضاء (السابقين والجدد)
            $this->recalculateMullakBuildingFees($officeId, $buildingId);
        });

        return redirect()->route('office.dashboard', ['tab' => 'mullak', 'open_mullak_building' => $buildingId])
            ->with('success', 'تم حفظ الأعضاء وإعادة احتساب وتوزيع رسوم اتحاد الملاك على الجميع تلقائياً بنجاح.');
    }

    public function updateMullakMember(Request $request, $id)
    {
        $officeId = $this->getCurrentOfficeId();
        $member = DB::table('mullak_members')->where('id', $id)->where('office_id', $officeId)->first();
        if (!$member) {
            return back()->withErrors(['العضو غير موجود.']);
        }

        $unitsCount = max(1, (int) ($request->input('units_count', $member->units_count ?? 1) ?: 1));
        $customAnnualFee = $request->filled('annual_fee') ? (float) $request->annual_fee : (float) $member->annual_fee;
        $paidAmount = $request->filled('paid_amount') ? (float) $request->paid_amount : (float) $member->paid_amount;
        $status = $paidAmount >= ($customAnnualFee - 0.01) ? 'paid' : ($paidAmount > 0 ? 'partial' : 'due');

        DB::table('mullak_members')->where('id', $member->id)->update([
            'member_name' => trim($request->input('member_name', $member->member_name)),
            'phone' => trim($request->input('phone', $member->phone)),
            'unit_number' => trim($request->input('unit_number', $member->unit_number)),
            'member_role' => $request->input('member_role', $member->member_role),
            'units_count' => $unitsCount,
            'annual_fee' => $customAnnualFee,
            'paid_amount' => $paidAmount,
            'status' => $status,
            'updated_at' => now(),
        ]);

        if ($request->has('recalculate_all')) {
            $this->recalculateMullakBuildingFees($officeId, (int) $member->mullak_building_id);
        }

        return redirect()->route('office.dashboard', ['tab' => 'mullak', 'open_mullak_building' => $member->mullak_building_id])
            ->with('success', 'تم تعديل بيانات عضو اتحاد الملاك وتحديث الكشف بنجاح.');
    }

    public function payMullakMemberFee(Request $request, $id)
    {
        $officeId = $this->getCurrentOfficeId();
        $member = DB::table('mullak_members')->where('id', $id)->where('office_id', $officeId)->first();
        if (!$member) {
            return back()->withErrors(['العضو غير موجود في اتحاد الملاك.']);
        }

        $payAmt = (float) ($request->amount ?? 0);
        if ($payAmt <= 0) {
            return back()->withErrors(['يرجى إدخال مبلغ سداد صحيح.']);
        }

        $newPaid = round($member->paid_amount + $payAmt, 2);
        $newStatus = $newPaid >= ($member->annual_fee - 0.01) ? 'paid' : 'partial';

        DB::transaction(function () use ($officeId, $member, $payAmt, $newPaid, $newStatus, $request) {
            DB::table('mullak_members')->where('id', $member->id)->update([
                'paid_amount' => $newPaid,
                'status' => $newStatus,
                'updated_at' => now(),
            ]);

            $cashAcc = DB::table('accounts')->where('office_id', $officeId)->where('code', '1102')->first()
                    ?: DB::table('accounts')->where('office_id', $officeId)->where('is_group', 0)->first();
            $accId = $cashAcc ? $cashAcc->id : 1;

            $vNo = CompanyErpController::generateNextVoucherNumber('receipt', $officeId);
            DB::table('vouchers')->insert([
                'voucher_number' => $vNo,
                'voucher_type' => 'receipt',
                'office_id' => $officeId,
                'debit_account_id' => $accId,
                'credit_account_id' => $accId,
                'amount' => $payAmt,
                'payment_method' => CompanyErpController::normalizeVoucherPaymentMethod($request->payment_method ?: 'حوالة بنكية'),
                'voucher_date' => date('Y-m-d'),
                'notes' => "تحصيل رسوم صيانة اتحاد الملاك - وحدة ({$member->unit_number}) - {$member->member_name}",
                'owner_credited' => 1,
                'commission_recorded' => 1,
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            CompanyErpController::applyAccountMovement((int) $accId, $payAmt, 0);
        });

        return redirect()->route('office.dashboard', ['tab' => 'mullak', 'open_mullak_building' => $member->mullak_building_id])
            ->with('success', "تم تحصيل مبلغ ({$payAmt} ر.س) لرسوم صيانة الأجزاء المشتركة للوحدة ({$member->unit_number}) وإصدار سند قبض رسمي.");
    }

    public function deleteMullakMember($id)
    {
        $officeId = $this->getCurrentOfficeId();
        $member = DB::table('mullak_members')->where('id', $id)->where('office_id', $officeId)->first();
        $buildingId = $member ? (int) $member->mullak_building_id : null;

        DB::table('mullak_members')->where('id', $id)->where('office_id', $officeId)->delete();

        if ($buildingId) {
            $this->recalculateMullakBuildingFees($officeId, $buildingId);
        }

        return redirect()->route('office.dashboard', ['tab' => 'mullak', 'open_mullak_building' => $buildingId])
            ->with('success', 'تم حذف العضو وإعادة توزيع رسوم الصيانة تلقائياً على باقي الأعضاء المسجلين.');
    }

    public function storeMullakExpense(Request $request)
    {
        $officeId = $this->getCurrentOfficeId();
        $request->validate([
            'mullak_building_id' => 'required',
            'title' => 'required',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $buildingId = (int) $request->mullak_building_id;
        $amt = (float) $request->amount;

        DB::transaction(function () use ($officeId, $request, $buildingId, $amt) {
            $cashAcc = DB::table('accounts')->where('office_id', $officeId)->where('code', '1102')->first()
                    ?: DB::table('accounts')->where('office_id', $officeId)->where('is_group', 0)->first();
            $expAcc  = DB::table('accounts')->where('office_id', $officeId)->where('code', '5101')->first() ?: $cashAcc;

            $vId = null;
            if ($cashAcc && $expAcc) {
                $vNo = CompanyErpController::generateNextVoucherNumber('payment', $officeId);
                $vId = DB::table('vouchers')->insertGetId([
                    'voucher_number' => $vNo,
                    'voucher_type' => 'payment',
                    'office_id' => $officeId,
                    'debit_account_id' => $expAcc->id,
                    'credit_account_id' => $cashAcc->id,
                    'amount' => $amt,
                    'payment_method' => CompanyErpController::normalizeVoucherPaymentMethod($request->payment_method ?: 'حوالة بنكية'),
                    'voucher_date' => $request->expense_date ?: date('Y-m-d'),
                    'notes' => "مصروف اتحاد الملاك ({$request->expense_category}): " . trim($request->title),
                    'created_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                CompanyErpController::applyAccountMovement((int) $expAcc->id, $amt, 0);
                CompanyErpController::applyAccountMovement((int) $cashAcc->id, 0, $amt);
            }

            DB::table('mullak_expenses')->insert([
                'office_id' => $officeId,
                'mullak_building_id' => $buildingId,
                'expense_category' => $request->expense_category ?: 'راتب الحارس',
                'title' => trim($request->title),
                'amount' => $amt,
                'expense_date' => $request->expense_date ?: date('Y-m-d'),
                'payment_method' => $request->payment_method ?: 'حوالة بنكية',
                'voucher_id' => $vId,
                'notes' => trim($request->notes ?? ''),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('office.dashboard', ['tab' => 'mullak', 'open_mullak_building' => $buildingId])
            ->with('success', 'تم تسجيل مصروف الأجزاء المشتركة في كشف العمارة وإصدار سند الصرف بنجاح.');
    }

    public function deleteMullakExpense($id)
    {
        $officeId = $this->getCurrentOfficeId();
        $exp = DB::table('mullak_expenses')->where('id', $id)->where('office_id', $officeId)->first();
        $buildingId = $exp ? (int) $exp->mullak_building_id : null;

        DB::table('mullak_expenses')->where('id', $id)->where('office_id', $officeId)->delete();

        return redirect()->route('office.dashboard', ['tab' => 'mullak', 'open_mullak_building' => $buildingId])
            ->with('success', 'تم حذف مصروف اتحاد الملاك.');
    }

    /**
     * طرح قرار جديد للتصويت الإلكتروني في جمعية اتحاد الملاك (Mullak Digital Voting)
     */
    public function storeMullakPoll(Request $request)
    {
        $officeId = $this->getCurrentOfficeId();
        CompanyErpController::ensureMullakVotingTables();

        $request->validate([
            'mullak_building_id' => 'required',
            'title'              => 'required|string|max:220',
        ]);

        $token = bin2hex(random_bytes(16));
        DB::table('mullak_polls')->insert([
            'office_id'           => $officeId,
            'mullak_building_id'  => (int) $request->mullak_building_id,
            'title'               => trim($request->title),
            'description'         => trim((string) $request->input('description', '')),
            'estimated_cost'      => (float) ($request->input('estimated_cost', 0) ?: 0),
            'required_quorum_pct' => (float) ($request->input('required_quorum_pct', 51) ?: 51),
            'closes_at'           => $request->input('closes_at') ?: now()->addDays(7)->toDateString(),
            'status'              => 'open',
            'public_token'        => $token,
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        return redirect()->route('office.dashboard', ['tab' => 'mullak', 'open_polls' => 1])
            ->with('success', '🗳️ تم طرح القرار في جدول التصويت الإلكتروني بنجاح.');
    }

    /**
     * اعتماد أو إلغاء أو إعادة فتح قرار التصويت الإلكتروني
     */
    public function updateMullakPollStatus(Request $request, $id)
    {
        $officeId = $this->getCurrentOfficeId();
        CompanyErpController::ensureMullakVotingTables();

        $status = $request->input('status', 'approved');
        if (!in_array($status, ['open', 'approved', 'cancelled'], true)) {
            $status = 'approved';
        }

        DB::table('mullak_polls')
            ->where('id', $id)
            ->where('office_id', $officeId)
            ->update([
                'status'     => $status,
                'updated_at' => now(),
            ]);

        $msg = match ($status) {
            'approved'  => '✅ تم اعتماد قرار التصويت رسمياً.',
            'cancelled' => '🚫 تم إلغاء التصويت وإيقاف استقبال الأصوات.',
            default     => '🔓 تم إعادة فتح باب التصويت للقرار.',
        };

        return redirect()->route('office.dashboard', ['tab' => 'mullak', 'open_polls' => 1])
            ->with('success', $msg);
    }

    /**
     * مشاركة وإرسال رابط التصويت مباشرة لجميع المستأجرين/الأعضاء في العمارة عبر الواتساب بضغطة زر
     */
    public function shareMullakPollWhatsapp(Request $request, $id)
    {
        $officeId = $this->getCurrentOfficeId();
        CompanyErpController::ensureMullakVotingTables();

        $poll = DB::table('mullak_polls')
            ->join('mullak_buildings', 'mullak_polls.mullak_building_id', '=', 'mullak_buildings.id')
            ->where('mullak_polls.id', $id)
            ->where('mullak_polls.office_id', $officeId)
            ->select('mullak_polls.*', 'mullak_buildings.association_name')
            ->first();

        if (!$poll) {
            return back()->withErrors(['قرار التصويت غير موجود.']);
        }

        $members = DB::table('mullak_members')
            ->where('mullak_building_id', $poll->mullak_building_id)
            ->where('office_id', $officeId)
            ->whereNotNull('phone')
            ->get();

        if ($members->count() === 0) {
            return redirect()->route('office.dashboard', ['tab' => 'mullak', 'open_polls' => 1])
                ->withErrors(['لا يوجد أعضاء أو مستأجرون بأرقام جوال مسجلة في هذه العمارة لإرسال الرابط إليهم.']);
        }

        $voteUrl = route('public.mullak_vote', ['id' => $poll->id, 'token' => $poll->public_token]);
        $sentCount = 0;
        $firstPhone = '';

        foreach ($members as $m) {
            $cleanPhone = trim((string) $m->phone);
            if ($cleanPhone === '') continue;
            if ($firstPhone === '') $firstPhone = $cleanPhone;

            $msg = "🗳️ *دعوة للتصويت الإلكتروني — {$poll->association_name}*\n"
                 . "الوحدة: *{$m->unit_number}*\n"
                 . "الموضوع: *{$poll->title}*\n"
                 . ($poll->estimated_cost > 0 ? "التكلفة التقديرية: " . number_format($poll->estimated_cost, 2) . " ر.س\n" : "")
                 . "\n🔗 اضغط على الرابط التالي لاختيار رقم وحدتك والتصويت (سيصلك كود تحقق من 4 أرقام على جوالك لاعتماد صوتك):\n"
                 . $voteUrl;

            if (CompanyErpController::dispatchWhatsappMessage($cleanPhone, $msg, $officeId)) {
                $sentCount++;
            }
        }

        if ($sentCount > 0) {
            return redirect()->route('office.dashboard', ['tab' => 'mullak', 'open_polls' => 1])
                ->with('success', "📲 تم إرسال رابط التصويت الإلكتروني مباشرة عبر الواتساب إلى ({$sentCount}) من مستأجري وأعضاء العمارة بنجاح!");
        }

        // في حال عدم تفعيل توكن الـ API، نفتح واتساب ويب لأول مستأجر مع نسخ الرابط
        if ($firstPhone !== '') {
            $intlPhone = ltrim(CompanyErpController::formatSaudiPhone($firstPhone), '+');
            $fallbackMsg = "🗳️ دعوة للتصويت الإلكتروني ({$poll->association_name})\nالموضوع: {$poll->title}\nالرابط المباشر للتصويت:\n{$voteUrl}";
            return redirect()->away("https://wa.me/{$intlPhone}?text=" . urlencode($fallbackMsg));
        }

        return redirect()->route('office.dashboard', ['tab' => 'mullak', 'open_polls' => 1])
            ->with('success', 'تم تجهيز رابط التصويت للمستأجرين.');
    }

    public function deleteMullakPoll($id)
    {
        $officeId = $this->getCurrentOfficeId();
        CompanyErpController::ensureMullakVotingTables();
        DB::table('mullak_poll_votes')->where('poll_id', $id)->delete();
        DB::table('mullak_polls')->where('id', $id)->where('office_id', $officeId)->delete();

        return redirect()->route('office.dashboard', ['tab' => 'mullak', 'open_polls' => 1])
            ->with('success', 'تم حذف قرار التصويت الإلكتروني.');
    }

    /**
     * حفظ إعدادات الهوية البصرية والشعار وأماكن ظهوره في المستندات ولون القائمة الجانبية (بدون الدومين المخصص)
     */
    public function saveOfficeBranding(Request $request)
    {
        $officeId = $this->getCurrentOfficeId();
        app(CompanyErpController::class)->ensureOfficePlansTable();

        $office = DB::table('offices')->where('id', $officeId)->first();
        if (!$office) {
            return back();
        }

        $logoUrl = $office->logo_url ?? '';
        if ($request->input('remove_logo') == '1') {
            $logoUrl = '';
        } elseif ($request->hasFile('logo_file') && $request->file('logo_file')->isValid()) {
            $file = $request->file('logo_file');
            $ext = strtolower($file->extension() ?: $file->getClientOriginalExtension());
            if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'svg'], true)) {
                $dir = public_path('uploads/branding');
                if (!is_dir($dir)) @mkdir($dir, 0775, true);
                $fname = 'office_logo_' . $officeId . '_' . time() . '.' . $ext;
                $file->move($dir, $fname);
                $logoUrl = '/uploads/branding/' . $fname;
            }
        }

        $targets = $request->input('logo_targets', []);
        if (!is_array($targets)) {
            $targets = [];
        }

        DB::table('offices')->where('id', $officeId)->update([
            'name'                => trim($request->input('office_display_name', $office->name)) ?: $office->name,
            'logo_url'            => $logoUrl,
            'brand_sidebar_color' => trim($request->input('brand_sidebar_color', '#0f172a')),
            'logo_enabled'        => $request->has('logo_enabled') ? 1 : 0,
            'logo_targets_json'   => json_encode(array_values(array_unique($targets)), JSON_UNESCAPED_UNICODE),
            'updated_at'          => now(),
        ]);

        return redirect()->route('office.dashboard', ['tab' => 'staff'])
            ->with('success', '🎨 تم حفظ الهوية البصرية ولون القائمة الجانبية وإعدادات إظهار الشعار في المستندات بنجاح.');
    }
}