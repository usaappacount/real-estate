<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class OwnerErpController extends Controller
{
    /**
     * معرفة رقم المالك الحالي (يدخل المالك نفسه أو فرد عائلته، أو معاينة سرية حصرية لمالك المنصة company_admin فقط)
     */
    private function getCurrentOwnerId(): ?int
    {
        $user = Auth::user();
        // المعاينة السرية مسموحة فقط لمالك المنصة (company_admin) وغير مسموحة لمكاتب العقار
        if ($user->user_type === 'company_admin' && session()->has('impersonate_owner_id')) {
            return (int) session('impersonate_owner_id');
        }
        return $user->owner_id ? (int) $user->owner_id : null;
    }

    /**
     * فحص هل المستخدم الحالي يملك صلاحية معينة؟
     */
    public static function hasOwnerPermission(string $permKey): bool
    {
        $user = Auth::user();
        if ($user->user_type !== 'owner_family') {
            return true;
        }
        $perms = json_decode($user->custom_permissions ?? '[]', true) ?: [];
        return in_array($permKey,$perms);
    }

    /**
     * الدخول السري لمعاينة بوابة المالك (حصري فقط للأدمن مالك المنصة company_admin)
     */
    public function switchToOwner($id)
    {
        if (Auth::user()->user_type !== 'company_admin') {
            abort(403, 'غير مصرح لمكاتب العقار بالاطلاع على بوابة المالك الخاصة.');
        }

        session(['impersonate_owner_id' => $id]);
        return redirect()->route('owner.dashboard')->with('success', 'تم فتح بوابة المالك بنجاح (وضع المعاينة السرية لمالك المنصة).');
    }

    public function returnToOffice()
    {
        if (Auth::user()->user_type !== 'company_admin') {
            abort(403);
        }

        session()->forget('impersonate_owner_id');
        return redirect()->route('office.dashboard', ['tab' => 'owners']);
    }

    public function dashboard(Request $request)
    {
        $ownerId =$this->getCurrentOwnerId();
        if (!$ownerId) {
            return redirect()->route('login')->with('error', 'غير مصرح بالدخول أو لم يتم العثور على سجل مالك مرتبط بهذا الحساب.');
        }

        $owner = DB::table('owners')->where('id',$ownerId)->first();
        if (!$owner) {
            return redirect()->route('login');
        }

        $office = DB::table('offices')->where('id',$owner->office_id)->first();
        $tab =$request->get('tab', 'overview');

        // 1. جلب عقارات المالك وصكوكه
        $properties = DB::table('properties')
            ->join('property_types', 'properties.property_type_id', '=', 'property_types.id')
            ->where('properties.owner_id', $ownerId)
            ->select(
                'properties.*',
                'property_types.name as type_name',
                'property_types.category as type_category',
                'property_types.has_units'
            )
            ->orderBy('properties.id', 'desc')
            ->get();

        // 2. جلب جميع الوحدات التابعة لعقارات هذا المالك
        $propertyIds =$properties->pluck('id')->toArray();
        $units = count($propertyIds) > 0
            ? DB::table('property_units')
                ->join('properties', 'property_units.property_id', '=', 'properties.id')
                ->whereIn('property_units.property_id', $propertyIds)
                ->select('property_units.*', 'properties.title as property_title')
                ->get()
            : collect();

        // 3. جلب عقود الإيجار الخاصة بعقارات المالك
        $contracts = DB::table('lease_contracts')
            ->join('properties', 'lease_contracts.property_id', '=', 'properties.id')
            ->join('tenants', 'lease_contracts.tenant_id', '=', 'tenants.id')
            ->leftJoin('property_units', 'lease_contracts.property_unit_id', '=', 'property_units.id')
            ->where('lease_contracts.owner_id', $ownerId)
            ->select(
                'lease_contracts.*',
                'properties.title as property_title',
                'properties.deed_number',
                'tenants.name as tenant_name',
                'tenants.phone as tenant_phone',
                'property_units.unit_number'
            )
            ->orderBy('lease_contracts.id', 'desc')
            ->get();

        // 4. جلب السندات المالية المرتبطة بالمالك
        $vouchers = DB::table('vouchers')
            ->where('vouchers.owner_id', $ownerId)
            ->leftJoin('accounts as debit_acc', 'vouchers.debit_account_id', '=', 'debit_acc.id')
            ->leftJoin('accounts as credit_acc', 'vouchers.credit_account_id', '=', 'credit_acc.id')
            ->leftJoin('tenants', 'vouchers.tenant_id', '=', 'tenants.id')
            ->select(
                'vouchers.*',
                'debit_acc.name as debit_name',
                'credit_acc.name as credit_name',
                'tenants.name as tenant_name'
            )
            ->orderBy('vouchers.id', 'desc')
            ->get();

        // 5. جلب أفراد العائلة والوكلاء التابعين لهذا المالك
        $familyMembers = DB::table('users')
            ->where('owner_id', $ownerId)
            ->where('user_type', 'owner_family')
            ->orderBy('id', 'desc')
            ->get();

        $grossRent = $contracts->sum('total_rent_amount');$officeCommissionTotal = $contracts->sum('office_commission');$netExpectedIncome = $grossRent -$officeCommissionTotal;

        $stats = [
            'properties_count' => $properties->count(),
            'units_count' => $units->count(),
            'rented_units' => $units->where('status', 'rented')->count(),
            'vacant_units' => $units->where('status', 'vacant')->count(),
            'active_contracts' => $contracts->where('status', 'active')->count(),
            'gross_rent' => $grossRent,
            'office_fees' => $officeCommissionTotal,
            'net_income' => $netExpectedIncome,
            'total_received_vouchers' => $vouchers->where('voucher_type', 'receipt')->sum('amount'),
            'total_paid_vouchers' => $vouchers->where('voucher_type', 'payment')->sum('amount'),
        ];

        return view('owner_dashboard', compact(
            'owner', 'office', 'tab', 'properties', 'units',
            'contracts', 'vouchers', 'familyMembers', 'stats'
        ));
    }

    public function storeFamilyMember(Request $request)
    {
        $ownerId =$this->getCurrentOwnerId();
        $owner = DB::table('owners')->where('id',$ownerId)->first();

        $request->validate([
            'name' => 'required',
            'phone' => ['required', 'regex:/^05[0-9]{8}$/'],
            'username' => 'required|unique:users,username',
        ], [
            'phone.regex' => 'رقم الجوال يجب أن يتكون من 10 أرقام ويبدأ بـ 05.',
            'username.unique' => 'اسم المستخدم مسجل مسبقاً، اختر اسماً آخر.'
        ]);

        $formattedPhone = CompanyErpController::formatSaudiPhone($request->phone);
        $perms =$request->input('permissions', ['view_properties']);

        DB::table('users')->insert([
            'name' => trim($request->name) . ' (' . ($request->relation ?: 'فرد عائلة') . ')',
            'username' => trim($request->username),
            'phone' => $formattedPhone,
            'email' => trim($request->username) . '@family.local',
            'password' => Hash::make($request->password ?: '123456'),
            'user_type' => 'owner_family',
            'office_id' => $owner->office_id,
            'owner_id' => $ownerId,
            'is_active' => 1,
            'custom_permissions' => json_encode($perms, JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('owner.dashboard', ['tab' => 'family'])
            ->with('success', 'تم إضافة فرد العائلة / الوكيل وتخصيص صلاحياته بنجاح.');
    }

    public function updateFamilyMember(Request $request, $id)
    {
        $ownerId = $this->getCurrentOwnerId();
        $perms = $request->input('permissions', []);

        $updateData = [
            'name' => trim($request->name),
            'custom_permissions' => json_encode($perms, JSON_UNESCAPED_UNICODE),
            'is_active' => 1,
            'updated_at' => now(),
        ];

        if ($request->filled('phone')) {
            $updateData['phone'] = CompanyErpController::formatSaudiPhone($request->phone);
        }
        if ($request->filled('username')) {
            $updateData['username'] = trim($request->username);
        }
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        DB::table('users')
            ->where('id', $id)
            ->where('owner_id', $ownerId)
            ->where('user_type', 'owner_family')
            ->update($updateData);

        return redirect()->route('owner.dashboard', ['tab' => 'family'])
            ->with('success', 'تم تحديث بيانات وصلاحيات فرد العائلة بنجاح.');
    }

    public function deleteFamilyMember($id)
    {
        $ownerId =$this->getCurrentOwnerId();
        DB::table('users')
            ->where('id', $id)
            ->where('owner_id', $ownerId)
            ->where('user_type', 'owner_family')
            ->delete();

        return redirect()->route('owner.dashboard', ['tab' => 'family'])
            ->with('success', 'تم إلغاء حساب فرد العائلة بنجاح.');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'regex:/^05[0-9]{8}$/'],
            'username' => 'required',
        ]);

        $formattedPhone = CompanyErpController::formatSaudiPhone($request->phone);

        $updateData = [
            'name' => trim($request->name),
            'phone' => $formattedPhone,
            'username' => trim($request->username),
            'updated_at' => now(),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        DB::table('users')->where('id', Auth::id())->update($updateData);

        return back()->with('success', 'تم تحديث بيانات الدخول الخاصة بك بنجاح.');
    }
}