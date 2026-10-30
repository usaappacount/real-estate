<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class OfficeErpController extends Controller
{
    private function getCurrentOfficeId(): ?int
    {
        $user = Auth::user();
        if ($user->user_type === 'company_admin' && session()->has('impersonate_office_id')) {
            return (int) session('impersonate_office_id');
        }
        return $user->office_id ? (int) $user->office_id : null;
    }

    public function dashboard(Request $request)
    {
        app(CompanyErpController::class)->ensureDefaultAdminExists();
        app(CompanyErpController::class)->ensureDynamicPropertyTables();
        app(CompanyErpController::class)->ensureDynamicAccountTables();
        app(CompanyErpController::class)->ensurePaymentMethodsTable();
        $paymentMethods = DB::table('custom_payment_methods')->orderBy('id')->get();

        $officeId = $this->getCurrentOfficeId();
        if (!$officeId) {
            return redirect()->route('erp.dashboard')->withErrors(['يرجى اختيار مكتب عقار لمعاينته أولاً.']);
        }

        $office = DB::table('offices')->where('id', $officeId)->first();
        if (!$office) {
            return redirect()->route('erp.dashboard');
        }

        $tab = $request->get('tab', 'overview');

        $accountTypes = DB::table('custom_account_types')->orderBy('id')->get();
        $accounts = DB::table('accounts')->where('office_id', $officeId)->orderBy('code')->get();
        if ($accounts->count() === 0) {
            $this->seedOfficeAccountsInternal($officeId);
            $accounts = DB::table('accounts')->where('office_id', $officeId)->orderBy('code')->get();
        }

        $defaultTenantParentAcc = $accounts->firstWhere('code', '1103') ?: $accounts->first();
        $defaultOwnerParentAcc  = $accounts->firstWhere('code', '2101') ?: $accounts->first();

        // 1. جلب ملاك العقارات مع حسابهم في شجرة الحسابات
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

        // 2. جلب أنواع العقارات والمتطلبات الديناميكية
        $propertyTypes = DB::table('property_types')->where('is_active', 1)->get();
        $allAttributes = DB::table('property_type_attributes')->get();

        // 3. جلب العقارات والصكوك التابعة للمكتب
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

        // 4. جلب الوحدات العقارية
        $units = DB::table('property_units')
            ->join('properties', 'property_units.property_id', '=', 'properties.id')
            ->where('properties.office_id', $officeId)
            ->select('property_units.*', 'properties.title as property_title')
            ->orderBy('property_units.id', 'desc')
            ->get();

        // 5. جلب السندات الخاصة بالمكتب فقط
        $officeAccountIds = $accounts->pluck('id')->toArray();
        $vouchers = DB::table('vouchers')
            ->where('vouchers.office_id', $officeId)
            ->whereIn('vouchers.debit_account_id', $officeAccountIds)
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
            ->get();

        // 6. جلب المستأجرين مع ربطهم بشجرة الحسابات وكشف حساب كل مستأجر
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

        // 7. موظفو المكتب وصلاحياتهم
        $staff = DB::table('users')
            ->where('office_id', $officeId)
            ->whereIn('user_type', ['office_admin', 'office_staff'])
            ->orderBy('id')
            ->get();

        $companyAdminPhone = CompanyErpController::getSetting('whatsapp_sender_phone', '+966500000000');
        $nextReceiptNo = CompanyErpController::generateNextVoucherNumber('receipt', $officeId);
        $nextPaymentNo = CompanyErpController::generateNextVoucherNumber('payment', $officeId);

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

        return view('office_dashboard', compact(
            'office', 'tab', 'owners', 'propertyTypes', 'allAttributes',
            'properties', 'units', 'tenants', 'contracts', 'accountTypes',
            'accounts', 'vouchers', 'staff', 'stats',
            'defaultTenantParentAcc', 'defaultOwnerParentAcc',
            'companyAdminPhone', 'nextReceiptNo', 'nextPaymentNo',
            'paymentMethods'
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

        DB::table('owners')->where('id', $id)->where('office_id', $officeId)->update([
            'name' => trim($request->name),
            'national_id' => trim($request->national_id),
            'phone' => $formattedPhone,
            'iban' => trim($request->iban),
            'bank_name' => trim($request->bank_name),
            'office_management_fee_rate' => $request->office_management_fee_rate ?? 5,
            'updated_at' => now(),
        ]);

        DB::table('users')->where('owner_id', $id)->where('user_type', 'owner')->update([
            'name' => trim($request->name),
            'phone' => $formattedPhone,
            'updated_at' => now(),
        ]);

        return redirect()->route('office.dashboard', ['tab' => 'owners'])
            ->with('success', 'تم تحديث بيانات المالك بنجاح.');
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
        ]);

        $customAttrs = $request->input('custom_attrs', []);

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
            ->with('success', 'تم تسجيل العقار وبيانات الصك والمتطلبات بنجاح.');
    }

    public function deleteProperty($id)
    {
        $officeId = $this->getCurrentOfficeId();
        DB::table('properties')->where('id', $id)->where('office_id', $officeId)->delete();
        return redirect()->route('office.dashboard', ['tab' => 'properties'])
            ->with('success', 'تم حذف العقار بنجاح.');
    }

    public function storeUnit(Request $request)
    {
        $request->validate([
            'property_id' => 'required',
            'unit_number' => 'required',
            'default_rent_price' => 'required|numeric',
        ]);

        $property = DB::table('properties')->where('id', $request->property_id)->first();

        DB::table('property_units')->insert([
            'property_id' => $request->property_id,
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

    public function deleteUnit($id)
    {
        DB::table('property_units')->where('id', $id)->delete();
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
            $parentAccId = $request->filled('parent_account_id') ? (int) $request->parent_account_id : null;
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
                $accountId = CompanyErpController::createSequentialSubAccount(
                    $officeId,
                    (int) $request->parent_account_id,
                    'المستأجر: ' . trim($request->name)
                );
            } elseif ($accountId) {
                DB::table('accounts')->where('id', $accountId)->update(['name' => 'المستأجر: ' . trim($request->name)]);
            }

            DB::table('tenants')->where('id', $id)->update([
                'name' => trim($request->name),
                'id_type' => $request->id_type ?: $tenant->id_type,
                'id_number' => trim($request->id_number),
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

        $property = DB::table('properties')->where('id', $request->property_id)->first();
        $owner = DB::table('owners')->where('id', $property->owner_id)->first();

        $companyFee = 0;
        if (in_array($office->billing_type, ['per_property_percentage', 'hybrid']) && $office->company_commission_rate > 0) {
            $companyFee = ($request->total_rent_amount * $office->company_commission_rate) / 100;
        }

        $officeCommission = $request->filled('office_commission')
            ? $request->office_commission
            : (($request->total_rent_amount * ($owner->office_management_fee_rate ?? 5)) / 100);

        $contractNumber = 'CNT-' . date('Y') . '-' . rand(10000, 99999);

        DB::transaction(function () use ($request, $officeId, $property, $contractNumber, $officeCommission, $companyFee) {
            DB::table('lease_contracts')->insert([
                'contract_number' => $contractNumber,
                'ejar_contract_number' => $request->ejar_contract_number ?: null,
                'ejar_status' => $request->filled('ejar_contract_number') ? 'active' : 'not_sent',
                'office_id' => $officeId,
                'property_id' => $property->id,
                'property_unit_id' => $request->property_unit_id ?: null,
                'owner_id' => $property->owner_id,
                'tenant_id' => $request->tenant_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'payment_cycle' => $request->payment_cycle ?: 'annual',
                'total_rent_amount' => $request->total_rent_amount,
                'security_deposit' => $request->security_deposit ?: 0,
                'office_commission' => $officeCommission,
                'company_fee_amount' => $companyFee,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($request->filled('property_unit_id')) {
                DB::table('property_units')->where('id', $request->property_unit_id)->update(['status' => 'rented']);
            }
        });

        return redirect()->route('office.dashboard', ['tab' => 'contracts'])
            ->with('success', 'تم إبرام عقد الإيجار رقم (' . $contractNumber . ') بنجاح.');
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

        DB::table('accounts')->where('office_id', $officeId)->where('id', $id)->update([
            'code' => trim($request->code),
            'name' => trim($request->name),
            'type' => $request->type,
            'parent_id' => $request->parent_id ?: null,
            'is_group' => $request->has('is_group') ? 1 : 0,
            'updated_at' => now(),
        ]);

        DB::table('accounts')->where('office_id', $officeId)->where('parent_id', $id)->update(['type' => $request->type]);

        return redirect()->route('office.dashboard', ['tab' => 'accounting'])
            ->with('success', 'تم تعديل الحساب في شجرة المكتب بنجاح.');
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

    public function storeOfficeVoucher(Request $request)
    {
        $officeId = $this->getCurrentOfficeId();
        $voucherNumber = CompanyErpController::generateNextVoucherNumber($request->voucher_type, $officeId);

        DB::transaction(function () use ($request, $officeId, $voucherNumber) {
            DB::table('vouchers')->insert([
                'voucher_number' => $voucherNumber,
                'voucher_type' => $request->voucher_type,
                'office_id' => $officeId,
                'debit_account_id' => $request->debit_account_id,
                'credit_account_id' => $request->credit_account_id,
                'owner_id' => $request->owner_id ?: null,
                'tenant_id' => $request->tenant_id ?: null,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method ?: 'bank_transfer',
                'voucher_date' => $request->voucher_date ?: date('Y-m-d'),
                'notes' => trim($request->notes),
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('accounts')->where('office_id', $officeId)->where('id', $request->debit_account_id)->increment('balance', $request->amount);
            DB::table('accounts')->where('office_id', $officeId)->where('id', $request->credit_account_id)->decrement('balance', $request->amount);
        });

        if ($request->filled('stay_in_accounting')) {
            return redirect()->route('office.dashboard', ['tab' => 'accounting'])
                ->with('success', 'تم إصدار السند المالي رقم (' . $voucherNumber . ') وتحديث رصيد الحساب في شجرة المكتب فوراً.');
        }

        return redirect()->route('office.dashboard', ['tab' => 'vouchers'])
            ->with('success', 'تم إصدار السند الرسمي للمكتب رقم (' . $voucherNumber . ') وترحيل القيد بنجاح.');
    }

    public function updateOfficeVoucher(Request $request, $id)
    {
        $officeId = $this->getCurrentOfficeId();
        $oldVoucher = DB::table('vouchers')->where('id', $id)->where('office_id', $officeId)->first();
        if (!$oldVoucher) {
            return back()->withErrors(['السند غير موجود.']);
        }

        DB::transaction(function () use ($request, $id, $officeId, $oldVoucher) {
            DB::table('accounts')->where('office_id', $officeId)->where('id', $oldVoucher->debit_account_id)->decrement('balance', $oldVoucher->amount);
            DB::table('accounts')->where('office_id', $officeId)->where('id', $oldVoucher->credit_account_id)->increment('balance', $oldVoucher->amount);

            $newDebitId = $request->debit_account_id ?: $oldVoucher->debit_account_id;
            $newCreditId = $request->credit_account_id ?: $oldVoucher->credit_account_id;

            DB::table('vouchers')->where('id', $id)->update([
                'voucher_type' => $request->voucher_type ?: $oldVoucher->voucher_type,
                'debit_account_id' => $newDebitId,
                'credit_account_id' => $newCreditId,
                'owner_id' => $request->has('owner_id') ? ($request->owner_id ?: null) : $oldVoucher->owner_id,
                'tenant_id' => $request->has('tenant_id') ? ($request->tenant_id ?: null) : $oldVoucher->tenant_id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method ?: 'bank_transfer',
                'voucher_date' => $request->voucher_date ?: $oldVoucher->voucher_date,
                'notes' => trim($request->notes),
                'updated_at' => now(),
            ]);

            DB::table('accounts')->where('office_id', $officeId)->where('id', $newDebitId)->increment('balance', $request->amount);
            DB::table('accounts')->where('office_id', $officeId)->where('id', $newCreditId)->decrement('balance', $request->amount);
        });

        return back()->with('success', 'تم تعديل السند رقم (' . $oldVoucher->voucher_number . ') وتحديث أرصدة المكتب.');
    }

    public function deleteOfficeVoucher($id)
    {
        $officeId = $this->getCurrentOfficeId();
        $oldVoucher = DB::table('vouchers')->where('id', $id)->where('office_id', $officeId)->first();
        if ($oldVoucher) {
            DB::transaction(function () use ($id, $officeId, $oldVoucher) {
                DB::table('accounts')->where('office_id', $officeId)->where('id', $oldVoucher->debit_account_id)->decrement('balance', $oldVoucher->amount);
                DB::table('accounts')->where('office_id', $officeId)->where('id', $oldVoucher->credit_account_id)->increment('balance', $oldVoucher->amount);
                DB::table('vouchers')->where('id', $id)->delete();
            });
        }

        return back()->with('success', 'تم حذف السند وإلغاء أثره من شجرة حسابات المكتب.');
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
        $useCustom = $request->input('use_custom_whatsapp') == '1' || $request->input('whatsapp_mode') === 'custom';
        $senderPhone = $request->filled('whatsapp_sender_phone')
            ? CompanyErpController::formatSaudiPhone($request->whatsapp_sender_phone)
            : null;

        DB::table('offices')->where('id', $officeId)->update([
            'use_custom_whatsapp' => $useCustom ? 1 : 0,
            'whatsapp_mode' => $useCustom ? 'custom' : 'company',
            'whatsapp_api_url' => trim($request->whatsapp_api_url ?? ''),
            'whatsapp_api_token' => trim($request->whatsapp_api_token ?? ''),
            'whatsapp_instance_id' => trim($request->whatsapp_instance_id ?? ''),
            'whatsapp_sender_phone' => $senderPhone,
            'updated_at' => now(),
        ]);

        return redirect()->route('office.dashboard', ['tab' => 'whatsapp'])
            ->with('success', 'تم تحديث إعدادات الربط مع واتساب للمكتب بنجاح.');
    }

    public function updateOfficeWhatsapp(Request $request)
    {
        return $this->updateOfficeWhatsappSettings($request);
    }
}