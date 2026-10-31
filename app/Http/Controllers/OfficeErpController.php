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
        app(CompanyErpController::class)->ensureJournalTables();

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

        $journalEntries = DB::table('journal_entries')
            ->where('office_id', $officeId)
            ->orderBy('entry_number', 'asc')
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
            'accounts', 'vouchers', 'journalEntries', 'journalItems', 'journalItemsByEntry',
            'staff', 'stats', 'defaultTenantParentAcc', 'defaultOwnerParentAcc',
            'companyAdminPhone', 'nextReceiptNo', 'nextPaymentNo',
            'paymentMethods', 'officeInvoices', 'defaultVatRate'
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

        $customAttrs = $request->input('custom_attrs', []);

        if ($request->hasFile('custom_files')) {
            $uploadDir = public_path('uploads/properties');
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0775, true);
            }
            foreach ($request->file('custom_files') as $attrKey => $fileObj) {
                if ($fileObj && $fileObj->isValid()) {
                    $ext = strtolower($fileObj->getClientOriginalExtension());
                    $fileName = 'prop_' . time() . '_' . uniqid() . '.' . $ext;
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

    public function updateUnit(Request $request, $id)
    {
        $unit = DB::table('property_units')->where('id', $id)->first();
        if ($unit) {
            DB::table('property_units')->where('id', $id)->update([
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

    public function updateContract(Request $request, $id)
    {
        $officeId = $this->getCurrentOfficeId();
        $office = DB::table('offices')->where('id', $officeId)->first();
        $contract = DB::table('lease_contracts')->where('id', $id)->where('office_id', $officeId)->first();

        if (!$contract) {
            return back()->withErrors(['العقد غير موجود.']);
        }

        $property = DB::table('properties')->where('id', $request->property_id ?: $contract->property_id)->first();
        $owner = DB::table('owners')->where('id', $property->owner_id)->first();

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
            'property_unit_id' => $request->property_unit_id ?: null,
            'owner_id' => $property->owner_id,
            'tenant_id' => $request->tenant_id ?: $contract->tenant_id,
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
            DB::table('lease_contracts')->where('id', $id)->delete();
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

        $debit = $request->filled('debit_balance') ? (float) $request->debit_balance : ($acc->debit_balance ?? 0);
        $credit = $request->filled('credit_balance') ? (float) $request->credit_balance : ($acc->credit_balance ?? 0);
        $netBalance = ($request->filled('debit_balance') || $request->filled('credit_balance'))
            ? ($debit - $credit)
            : ($request->filled('balance') ? (float) $request->balance : ($acc->balance ?? 0));

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
            ->with('success', 'تم تعديل الحساب وأرصدة المدين والدائن في شجرة المكتب بنجاح.');
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
        DB::table('custom_payment_methods')->insert([
            'office_id' => $officeId,
            'account_id' => $request->filled('account_id') ? (int) $request->account_id : null,
            'name' => trim($request->name),
            'code' => trim($request->name),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return back()->with('success', 'تم إضافة طريقة الدفع وربطها برقم الحساب في المكتب بنجاح.');
    }

    public function updateOfficePaymentMethod(Request $request, $id)
    {
        $old = DB::table('custom_payment_methods')->where('id', $id)->first();
        if ($old) {
            DB::table('custom_payment_methods')->where('id', $id)->update([
                'name' => trim($request->name),
                'code' => trim($request->name),
                'account_id' => $request->filled('account_id') ? (int) $request->account_id : null,
                'updated_at' => now(),
            ]);
        }
        return back()->with('success', 'تم تعديل طريقة الدفع وحسابها المرتبط بنجاح.');
    }

    public function deleteOfficePaymentMethod($id)
    {
        DB::table('custom_payment_methods')->where('id', $id)->delete();
        return back()->with('success', 'تم حذف طريقة الدفع.');
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
                'payment_method' => CompanyErpController::normalizeVoucherPaymentMethod($request->payment_method),
                'voucher_date' => $request->voucher_date ?: date('Y-m-d'),
                'notes' => trim($request->notes),
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            CompanyErpController::applyAccountMovement((int) $request->debit_account_id, (float) $request->amount, 0);
            CompanyErpController::applyAccountMovement((int) $request->credit_account_id, 0, (float) $request->amount);
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

        DB::transaction(function () use ($request, $id, $oldVoucher) {
            CompanyErpController::applyAccountMovement((int) $oldVoucher->debit_account_id, (float) $oldVoucher->amount, 0, true);
            CompanyErpController::applyAccountMovement((int) $oldVoucher->credit_account_id, 0, (float) $oldVoucher->amount, true);

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

            CompanyErpController::applyAccountMovement((int) $newDebitId, (float) $request->amount, 0);
            CompanyErpController::applyAccountMovement((int) $newCreditId, 0, (float) $request->amount);
        });

        return back()->with('success', 'تم تعديل السند رقم (' . $oldVoucher->voucher_number . ') وتحديث أرصدة المكتب.');
    }

    public function deleteOfficeVoucher($id)
    {
        $officeId = $this->getCurrentOfficeId();
        $oldVoucher = DB::table('vouchers')->where('id', $id)->where('office_id', $officeId)->first();
        if ($oldVoucher) {
            DB::transaction(function () use ($id, $oldVoucher) {
                CompanyErpController::applyAccountMovement((int) $oldVoucher->debit_account_id, (float) $oldVoucher->amount, 0, true);
                CompanyErpController::applyAccountMovement((int) $oldVoucher->credit_account_id, 0, (float) $oldVoucher->amount, true);
                DB::table('vouchers')->where('id', $id)->delete();
            });
        }

        return back()->with('success', 'تم حذف السند وإلغاء أثره من شجرة حسابات المكتب.');
    }

    public function storeOfficeJournalEntry(Request $request)
    {
        $officeId = $this->getCurrentOfficeId();
        app(CompanyErpController::class)->ensureJournalTables();

        $accountIds = $request->input('account_ids', []);
        $debits     = $request->input('debits', []);
        $credits    = $request->input('credits', []);
        $taxes      = $request->input('taxes', []);
        $itemNotes  = $request->input('item_notes', []);

        $rows = [];
        $totalDebit = 0;
        $totalCredit = 0;
        $totalTax = 0;

        foreach ($accountIds as $idx => $accId) {
            if (empty($accId)) continue;
            $d = (float) ($debits[$idx] ?? 0);
            $c = (float) ($credits[$idx] ?? 0);
            $t = (float) ($taxes[$idx] ?? 0);
            if ($d == 0 && $c == 0) continue;

            $totalDebit += $d;
            $totalCredit += $c;
            $totalTax += $t;

            $rows[] = [
                'account_id' => (int) $accId,
                'debit' => $d,
                'credit' => $c,
                'tax' => $t,
                'notes' => trim($itemNotes[$idx] ?? ''),
            ];
        }

        if (count($rows) === 0) {
            return back()->withErrors(['يرجى إدخال سطر واحد على الأقل في القيد اليومي.']);
        }

        DB::transaction(function () use ($request, $officeId, $rows, $totalDebit, $totalCredit, $totalTax) {
            $maxNo = DB::table('journal_entries')->where('office_id', $officeId)->max('entry_number') ?? 0;
            $nextEntryNo = $maxNo + 1;

            $entryId = DB::table('journal_entries')->insertGetId([
                'office_id' => $officeId,
                'entry_number' => $nextEntryNo,
                'entry_date' => $request->entry_date ?: date('Y-m-d'),
                'general_notes' => trim($request->general_notes),
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'total_tax' => $totalTax,
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($rows as $r) {
                DB::table('journal_entry_items')->insert([
                    'journal_entry_id' => $entryId,
                    'account_id' => $r['account_id'],
                    'debit' => $r['debit'],
                    'credit' => $r['credit'],
                    'tax' => $r['tax'],
                    'notes' => $r['notes'] ?: trim($request->general_notes),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                CompanyErpController::applyAccountMovement($r['account_id'], $r['debit'], $r['credit']);
            }
        });

        return redirect()->route('office.dashboard', ['tab' => 'accounting'])
            ->with('success', 'تم حفظ القيد اليومي وترحيل جميع العمليات إلى شجرة حسابات المكتب بنجاح.');
    }

    public function updateOfficeJournalEntry(Request $request, $id)
    {
        $officeId = $this->getCurrentOfficeId();
        $entry = DB::table('journal_entries')->where('id', $id)->where('office_id', $officeId)->first();
        if (!$entry) return back()->withErrors(['القيد غير موجود.']);

        $accountIds = $request->input('account_ids', []);
        $debits     = $request->input('debits', []);
        $credits    = $request->input('credits', []);
        $taxes      = $request->input('taxes', []);
        $itemNotes  = $request->input('item_notes', []);

        $rows = [];
        $totalDebit = 0;
        $totalCredit = 0;
        $totalTax = 0;

        foreach ($accountIds as $idx => $accId) {
            if (empty($accId)) continue;
            $d = (float) ($debits[$idx] ?? 0);
            $c = (float) ($credits[$idx] ?? 0);
            $t = (float) ($taxes[$idx] ?? 0);
            if ($d == 0 && $c == 0) continue;

            $totalDebit += $d;
            $totalCredit += $c;
            $totalTax += $t;

            $rows[] = [
                'account_id' => (int) $accId,
                'debit' => $d,
                'credit' => $c,
                'tax' => $t,
                'notes' => trim($itemNotes[$idx] ?? ''),
            ];
        }

        DB::transaction(function () use ($request, $id, $rows, $totalDebit, $totalCredit, $totalTax) {
            $oldItems = DB::table('journal_entry_items')->where('journal_entry_id', $id)->get();
            foreach ($oldItems as $old) {
                CompanyErpController::applyAccountMovement((int) $old->account_id, (float) $old->debit, (float) $old->credit, true);
            }
            DB::table('journal_entry_items')->where('journal_entry_id', $id)->delete();

            DB::table('journal_entries')->where('id', $id)->update([
                'entry_date' => $request->entry_date ?: date('Y-m-d'),
                'general_notes' => trim($request->general_notes),
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'total_tax' => $totalTax,
                'updated_at' => now(),
            ]);

            foreach ($rows as $r) {
                DB::table('journal_entry_items')->insert([
                    'journal_entry_id' => $id,
                    'account_id' => $r['account_id'],
                    'debit' => $r['debit'],
                    'credit' => $r['credit'],
                    'tax' => $r['tax'],
                    'notes' => $r['notes'] ?: trim($request->general_notes),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                CompanyErpController::applyAccountMovement($r['account_id'], $r['debit'], $r['credit']);
            }
        });

        return redirect()->route('office.dashboard', ['tab' => 'journal'])
            ->with('success', 'تم تعديل القيد اليومي رقم (' . $entry->entry_number . ') وتحديث الأرصدة.');
    }

    public function deleteOfficeJournalEntry($id)
    {
        $officeId = $this->getCurrentOfficeId();
        $entry = DB::table('journal_entries')->where('id', $id)->where('office_id', $officeId)->first();
        if ($entry) {
            DB::transaction(function () use ($id) {
                $oldItems = DB::table('journal_entry_items')->where('journal_entry_id', $id)->get();
                foreach ($oldItems as $old) {
                    CompanyErpController::applyAccountMovement((int) $old->account_id, (float) $old->debit, (float) $old->credit, true);
                }
                DB::table('journal_entry_items')->where('journal_entry_id', $id)->delete();
                DB::table('journal_entries')->where('id', $id)->delete();
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
        $useCustom = $request->input('use_custom_whatsapp') == '1' || $request->input('whatsapp_mode') === 'custom';
        $senderPhone = $request->filled('whatsapp_sender_phone')
            ? CompanyErpController::formatSaudiPhone($request->whatsapp_sender_phone)
            : null;

        $updatePayload = [
            'use_custom_whatsapp' => $useCustom ? 1 : 0,
            'whatsapp_mode' => $useCustom ? 'custom' : 'company',
            'whatsapp_api_url' => trim($request->whatsapp_api_url ?? ''),
            'whatsapp_api_token' => trim($request->whatsapp_api_token ?? ''),
            'whatsapp_instance_id' => trim($request->whatsapp_instance_id ?? ''),
            'whatsapp_sender_phone' => $senderPhone,
            'reminder_days_before' => (int) ($request->reminder_days_before ?? 15),
            'reminder_target' => $request->reminder_target ?: 'both',
            'reminder_template' => trim($request->reminder_template ?? ''),
            'enable_auto_reminders' => $request->has('enable_auto_reminders') ? 1 : 0,
            'updated_at' => now(),
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
            ->with('success', 'تم حفظ إعدادات المكتب والرقم الضريبي والربط مع واتساب بنجاح.');
    }

    public function updateOfficeWhatsapp(Request $request)
    {
        return $this->updateOfficeWhatsappSettings($request);
    }

    /**
     * فحص وإرسال تذكيرات واتساب للعقود التي قاربت على الانتهاء أو حلت دفعتها
     */
    public function triggerLeaseReminders(Request $request)
    {
        $officeId = $this->getCurrentOfficeId();
        $office = DB::table('offices')->where('id', $officeId)->first();
        if (!$office) return back();

        $daysLimit = (int) ($office->reminder_days_before ?? 15);
        $targetMode = $office->reminder_target ?: 'both';
        $defaultTpl = "عزيزي {اسم_المستأجر}، نود تذكيركم بقرب استحقاق دفعة الإيجار / انتهاء العقد رقم {رقم_العقد} للعقار ({اسم_العقار} - {رقم_الوحدة}) بمبلغ {المبلغ} ر.س بتاريخ {تاريخ_الاستحقاق} (متبقي {الأيام_المتبقية} يوم). مع تحيات {اسم_المكتب}.";
        $template = !empty($office->reminder_template) ? $office->reminder_template : $defaultTpl;

        $contracts = DB::table('lease_contracts')
            ->join('properties', 'lease_contracts.property_id', '=', 'properties.id')
            ->join('tenants', 'lease_contracts.tenant_id', '=', 'tenants.id')
            ->leftJoin('property_units', 'lease_contracts.property_unit_id', '=', 'property_units.id')
            ->where('lease_contracts.office_id', $officeId)
            ->where('lease_contracts.status', 'active')
            ->select(
                'lease_contracts.*',
                'properties.title as property_title',
                'tenants.name as tenant_name',
                'tenants.phone as tenant_phone',
                'property_units.unit_number'
            )
            ->get();

        $sentCount = 0;
        $matchedCount = 0;
        $today = now()->startOfDay();

        foreach ($contracts as $c) {
            $endDate = \Carbon\Carbon::parse($c->end_date)->startOfDay();
            $diffDays = $today->diffInDays($endDate, false);

            if ($diffDays <= $daysLimit && $diffDays >= -30) {
                $matchedCount++;
                $replacements = [
                    '{اسم_المستأجر}' => $c->tenant_name,
                    '{اسم_العقار}' => $c->property_title,
                    '{رقم_الوحدة}' => $c->unit_number ?: 'كامل العقار',
                    '{رقم_العقد}' => $c->contract_number,
                    '{المبلغ}' => number_format($c->total_rent_amount, 2),
                    '{تاريخ_الاستحقاق}' => $c->end_date,
                    '{تاريخ_انتهاء_العقد}' => $c->end_date,
                    '{الأيام_المتبقية}' => max(0, $diffDays),
                    '{اسم_المكتب}' => $office->name,
                ];
                $finalMessage = str_replace(array_keys($replacements), array_values($replacements), $template);

                if (in_array($targetMode, ['tenant', 'both']) && !empty($c->tenant_phone)) {
                    if (CompanyErpController::dispatchWhatsappMessage($c->tenant_phone, $finalMessage, $officeId)) {
                        $sentCount++;
                    }
                }
                if (in_array($targetMode, ['office', 'both']) && !empty($office->phone)) {
                    $officeNotice = "🔔 *تذكير إداري للمكتب ({$office->name})*\n" . $finalMessage;
                    if (CompanyErpController::dispatchWhatsappMessage($office->phone, $officeNotice, $officeId)) {
                        $sentCount++;
                    }
                }
            }
        }

        return redirect()->route('office.dashboard', ['tab' => 'whatsapp'])
            ->with('success', "تم فحص العقود النشطة (ضمن مهلة {$daysLimit} يوم): تم العثور على ({$matchedCount}) عقد مستحق للتنبيه، وإرسال ({$sentCount}) رسالة واتساب.");
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

        $sent = CompanyErpController::dispatchWhatsappMessage($targetPhone, $msg, $officeId);
        if ($sent) {
            return back()->with('success', "تم إرسال الفاتورة رقم ({$inv->invoice_number}) عبر الواتساب إلى ({$inv->buyer_name}) بنجاح.");
        }

        $cleanPhone = ltrim(CompanyErpController::formatSaudiPhone($targetPhone), '+');
        $waUrl = "https://wa.me/{$cleanPhone}?text=" . urlencode($msg);
        return redirect()->away($waUrl);
    }

    public function deleteOfficeInvoice($id)
    {
        return back()->withErrors(['تنبيه نظامي: الفواتير الضريبية لا يُسمح بحذفها أو تعديلها بعد إصدارها.']);
    }
}