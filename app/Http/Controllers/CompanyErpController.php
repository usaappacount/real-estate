<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\User;

class CompanyErpController extends Controller
{
    /**
     * فحص وإصلاح أعمدة الجداول تلقائياً + إنشاء حساب الأدمن الافتراضي
     */
    public function ensureDefaultAdminExists(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->unique();
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable();
            }
            if (!Schema::hasColumn('users', 'user_type')) {
                $table->string('user_type')->default('office_admin');
            }
            if (!Schema::hasColumn('users', 'office_id')) {
                $table->unsignedBigInteger('office_id')->nullable();
            }
            if (!Schema::hasColumn('users', 'owner_id')) {
                $table->unsignedBigInteger('owner_id')->nullable();
            }
            if (!Schema::hasColumn('users', 'national_id')) {
                $table->string('national_id')->nullable();
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            if (!Schema::hasColumn('users', 'otp_code')) {
                $table->string('otp_code', 6)->nullable();
            }
            if (!Schema::hasColumn('users', 'otp_expires_at')) {
                $table->timestamp('otp_expires_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'custom_permissions')) {
                $table->json('custom_permissions')->nullable();
            }
        });

        if (Schema::hasTable('offices')) {
            Schema::table('offices', function (Blueprint $table) {
                if (!Schema::hasColumn('offices', 'account_id')) {
                    $table->unsignedBigInteger('account_id')->nullable();
                }
                if (!Schema::hasColumn('offices', 'use_custom_whatsapp')) {
                    $table->boolean('use_custom_whatsapp')->default(false);
                }
                if (!Schema::hasColumn('offices', 'whatsapp_mode')) {
                    $table->string('whatsapp_mode')->default('company');
                }
                if (!Schema::hasColumn('offices', 'whatsapp_sender_phone')) {
                    $table->string('whatsapp_sender_phone')->nullable();
                }
                if (!Schema::hasColumn('offices', 'whatsapp_api_url')) {
                    $table->string('whatsapp_api_url')->nullable();
                }
                if (!Schema::hasColumn('offices', 'whatsapp_api_token')) {
                    $table->string('whatsapp_api_token')->nullable();
                }
                if (!Schema::hasColumn('offices', 'whatsapp_instance_id')) {
                    $table->string('whatsapp_instance_id')->nullable();
                }
            });
        }

        if (Schema::hasTable('owners')) {
            Schema::table('owners', function (Blueprint $table) {
                if (!Schema::hasColumn('owners', 'account_id')) {
                    $table->unsignedBigInteger('account_id')->nullable();
                }
            });
        }

        if (Schema::hasTable('tenants')) {
            Schema::table('tenants', function (Blueprint $table) {
                if (!Schema::hasColumn('tenants', 'account_id')) {
                    $table->unsignedBigInteger('account_id')->nullable();
                }
            });
        }

        if (!Schema::hasTable('system_settings')) {
            Schema::create('system_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }

        $admin = DB::table('users')
            ->where('username', 'admin')
            ->orWhere('phone', '+966500000000')
            ->orWhere('phone', '0500000000')
            ->orWhere('user_type', 'company_admin')
            ->first();

        if (!$admin) {
            DB::table('users')->insert([
                'name' => 'المدير العام (صاحب الشركة)',
                'username' => 'admin',
                'phone' => '+966500000000',
                'email' => 'admin@system.local',
                'password' => Hash::make('123456'),
                'user_type' => 'company_admin',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table('users')->where('id', $admin->id)->update([
                'username' => $admin->username ?: 'admin',
                'phone' => $admin->phone ?: '+966500000000',
                'user_type' => 'company_admin',
                'is_active' => 1,
            ]);
        }
    }

    public static function getSetting(string $key, $default = null)
    {
        if (!Schema::hasTable('system_settings')) {
            return $default;
        }
        $row = DB::table('system_settings')->where('key', $key)->first();
        return $row ? $row->value : $default;
    }

    public static function setSetting(string $key, $value): void
    {
        if (!Schema::hasTable('system_settings')) {
            return;
        }
        DB::table('system_settings')->updateOrInsert(
            ['key' => $key],
            ['value' => $value, 'updated_at' => now()]
        );
    }

    /**
     * إنشاء حساب فرعي متسلسل تلقائياً في شجرة الحسابات (للمكاتب أو الملاك أو المستأجرين)
     */
    public static function createSequentialSubAccount(?int $officeId, ?int $parentAccountId, string $accountName): int
    {
        $parent = null;
        if ($parentAccountId) {
            $parent = DB::table('accounts')->where('id', $parentAccountId)->first();
        }

        if ($parent) {
            DB::table('accounts')->where('id', $parent->id)->update(['is_group' => 1]);
            $siblingsCount = DB::table('accounts')
                ->where('parent_id', $parent->id)
                ->when($officeId !== null, fn($q) => $q->where('office_id', $officeId), fn($q) => $q->whereNull('office_id'))
                ->count();
            $nextCode = $parent->code . str_pad($siblingsCount + 1, 2, '0', STR_PAD_LEFT);
            $accType = $parent->type;
            $parentId = $parent->id;
        } else {
            $rootsCount = DB::table('accounts')
                ->when($officeId !== null, fn($q) => $q->where('office_id', $officeId), fn($q) => $q->whereNull('office_id'))
                ->whereNull('parent_id')
                ->count();
            $nextCode = '1103' . str_pad($rootsCount + 1, 2, '0', STR_PAD_LEFT);
            $accType = 'الأصول';
            $parentId = null;
        }

        return DB::table('accounts')->insertGetId([
            'office_id' => $officeId,
            'parent_id' => $parentId,
            'code' => $nextCode,
            'name' => $accountName,
            'type' => $accType,
            'is_group' => 0,
            'balance' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * إرسال رسالة واتساب عبر إعدادات الشركة الأم أو إعدادات المكتب
     */
    public static function sendWhatsAppMessage(string $toPhone, string $message, ?int $officeId = null): bool
    {
        $apiUrl = self::getSetting('whatsapp_api_url', env('WHATSAPP_API_URL'));
        $apiToken = self::getSetting('whatsapp_api_token', env('WHATSAPP_API_TOKEN'));
        $instanceId = self::getSetting('whatsapp_instance_id', '');

        if ($officeId && Schema::hasTable('offices')) {
            $office = DB::table('offices')->where('id', $officeId)->first();
            $isCustom = (!empty($office->use_custom_whatsapp) || ($office->whatsapp_mode ?? '') === 'custom');
            if ($office && $isCustom && !empty($office->whatsapp_api_url)) {
                $apiUrl = $office->whatsapp_api_url;
                $apiToken = $office->whatsapp_api_token;
                $instanceId = $office->whatsapp_instance_id ?? '';
            }
        }

        if (empty($apiUrl) || empty($apiToken)) {
            return false;
        }

        try {
            $response = Http::timeout(8)->post($apiUrl, [
                'token' => $apiToken,
                'instance_id' => $instanceId,
                'to' => self::formatSaudiPhone($toPhone),
                'message' => $message,
            ]);
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function dispatchWhatsappMessage(string $toPhone, string $message, ?int $officeId = null): bool
    {
        return self::sendWhatsAppMessage($toPhone, $message, $officeId);
    }

    public static function formatSaudiPhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);
        if (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }
        if (str_starts_with($digits, '966')) {
            return '+' . $digits;
        }
        return '+966' . $digits;
    }

    public static function toLocalPhone(?string $intlPhone): string
    {
        if (!$intlPhone) return '';
        $digits = preg_replace('/\D/', '', $intlPhone);
        if (str_starts_with($digits, '966')) {
            return '0' . substr($digits, 3);
        }
        return $digits;
    }

    private function redirectByUserRole($user)
    {
        if (in_array($user->user_type, ['office_admin', 'office_staff'])) {
            return redirect()->route('office.dashboard');
        }
        if (in_array($user->user_type, ['owner', 'owner_family'])) {
            return redirect()->route('owner.dashboard');
        }
        return redirect()->route('erp.dashboard');
    }

    public function showLogin()
    {
        $this->ensureDefaultAdminExists();

        if (Auth::check()) {
            return $this->redirectByUserRole(Auth::user());
        }
        return view('auth_login');
    }

    public function sendOtp(Request $request)
    {
        $this->ensureDefaultAdminExists();

        $request->validate([
            'phone' => ['required', 'regex:/^05[0-9]{8}$/']
        ], [
            'phone.required' => 'يرجى إدخال رقم الجوال.',
            'phone.regex' => 'رقم الجوال يجب أن يتكون من 10 أرقام بالضبط ويبدأ بـ 05.'
        ]);

        $formattedPhone = self::formatSaudiPhone($request->phone);

        $user = User::where('phone', $formattedPhone)
                    ->orWhere('phone', $request->phone)
                    ->first();

        if (!$user) {
            return back()->with('error', 'رقم الجوال (' . $request->phone . ') غير مسجل في النظام.');
        }

        if (!$user->is_active) {
            return back()->with('error', 'هذا الحساب موقوف حالياً، يرجى مراجعة الإدارة.');
        }

        $otp = (string) rand(1000, 9999);

        DB::table('users')->where('id', $user->id)->update([
            'phone' => $formattedPhone,
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        $whatsappSent = self::sendWhatsAppMessage(
            $formattedPhone,
            "كود التحقق الخاص بك في منصة العقارات هو: *{$otp}*",
            $user->office_id
        );

        return back()->with([
            'otp_step' => true,
            'local_phone' => $request->phone,
            'formatted_phone' => $formattedPhone,
            'whatsapp_sent' => $whatsappSent,
            'dev_otp_hint' => !$whatsappSent ? $otp : null,
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $formattedPhone = $request->formatted_phone;
        $user = User::where('phone', $formattedPhone)
                    ->orWhere('phone', $request->local_phone)
                    ->first();

        if (!$user || $user->otp_code !== $request->otp_code) {
            return back()->with([
                'otp_step' => true,
                'local_phone' => $request->local_phone,
                'formatted_phone' => $formattedPhone,
                'error' => 'كود التحقق غير صحيح، حاول مرة أخرى أو ادخل باسم المستخدم وكلمة المرور.'
            ]);
        }

        DB::table('users')->where('id', $user->id)->update(['otp_code' => null]);
        Auth::login($user, true);

        return $this->redirectByUserRole($user);
    }

    public function loginWithPassword(Request $request)
    {
        $this->ensureDefaultAdminExists();

        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $input = trim($request->username);
        $formattedPhone = preg_match('/^05[0-9]{8}$/', $input) ? self::formatSaudiPhone($input) : $input;

        $user = User::where('username', $input)
                    ->orWhere('phone', $formattedPhone)
                    ->orWhere('phone', $input)
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->with([
                'password_mode' => true,
                'error' => 'اسم المستخدم أو كلمة المرور غير صحيحة.'
            ]);
        }

        if (!$user->is_active) {
            return back()->with([
                'password_mode' => true,
                'error' => 'هذا الحساب موقوف حالياً.'
            ]);
        }

        Auth::login($user, true);
        return $this->redirectByUserRole($user);
    }

    public function logout()
    {
        session()->forget(['impersonate_office_id', 'impersonate_owner_id']);
        Auth::logout();
        return redirect()->route('login');
    }

    public function switchToOffice($id)
    {
        if (Auth::user()->user_type !== 'company_admin') {
            abort(403);
        }
        session(['impersonate_office_id' => $id]);
        return redirect()->route('office.dashboard')->with('success', 'تم الدخول إلى بوابة مكتب العقار بنجاح (وضع الإشراف المباشر).');
    }

    public function returnToCompany()
    {
        session()->forget(['impersonate_office_id', 'impersonate_owner_id']);
        return redirect()->route('erp.dashboard', ['tab' => 'offices']);
    }

    public function dashboard(Request $request)
    {
        $this->ensureDefaultAdminExists();

        if (in_array(Auth::user()->user_type, ['office_admin', 'office_staff'])) {
            return redirect()->route('office.dashboard');
        }
        if (in_array(Auth::user()->user_type, ['owner', 'owner_family'])) {
            return redirect()->route('owner.dashboard');
        }

        $tab = $request->get('tab', 'overview');

        $this->ensureDynamicPropertyTables();
        $this->ensureDynamicAccountTables();
        $this->ensurePaymentMethodsTable();
        $paymentMethods = DB::table('custom_payment_methods')->orderBy('id')->get();

        $offices = DB::table('offices')
            ->leftJoin('users', function ($join) {
                $join->on('offices.id', '=', 'users.office_id')
                     ->where('users.user_type', '=', 'office_admin');
            })
            ->leftJoin('accounts', 'offices.account_id', '=', 'accounts.id')
            ->select(
                'offices.*',
                'users.id as manager_user_id',
                'users.username as manager_username',
                'users.phone as manager_phone',
                'accounts.code as account_code',
                'accounts.name as account_name',
                'accounts.balance as account_balance',
                'accounts.parent_id as parent_account_id'
            )
            ->orderBy('offices.id', 'desc')
            ->get();

        $categories = DB::table('property_categories')->orderBy('id')->get();
        $fieldTypes = DB::table('custom_field_types')->orderBy('id')->get();
        $propertyTypes = DB::table('property_types')->orderBy('id', 'desc')->get();
        $attributes = DB::table('property_type_attributes')
            ->join('property_types', 'property_type_attributes.property_type_id', '=', 'property_types.id')
            ->select('property_type_attributes.*', 'property_types.name as type_name', 'property_types.category')
            ->orderBy('property_type_attributes.id', 'asc')
            ->get();

        $accountTypes = DB::table('custom_account_types')->orderBy('id')->get();
        $accounts = DB::table('accounts')->whereNull('office_id')->orderBy('code')->get();

        $defaultOfficeParentAcc = $accounts->firstWhere('code', '1103') ?: $accounts->first();
        $defaultOfficeParentAccId = $defaultOfficeParentAcc ? $defaultOfficeParentAcc->id : '';

        $vouchers = DB::table('vouchers')
            ->leftJoin('accounts as debit_acc', 'vouchers.debit_account_id', '=', 'debit_acc.id')
            ->leftJoin('accounts as credit_acc', 'vouchers.credit_account_id', '=', 'credit_acc.id')
            ->leftJoin('offices', 'vouchers.office_id', '=', 'offices.id')
            ->where(function ($q) {
                $q->whereNull('vouchers.office_id')
                  ->orWhereNull('debit_acc.office_id');
            })
            ->select(
                'vouchers.*',
                'debit_acc.name as debit_name',
                'debit_acc.code as debit_code',
                'credit_acc.name as credit_name',
                'credit_acc.code as credit_code',
                'offices.name as office_name',
                'offices.phone as office_phone'
            )
            ->orderBy('vouchers.id', 'desc')
            ->get();

        $officeVouchersByOffice = $vouchers->whereNotNull('office_id')->groupBy('office_id');

        $whatsappSettings = [
            'sender_phone' => self::toLocalPhone(self::getSetting('whatsapp_sender_phone', '0500000000')),
            'api_url' => self::getSetting('whatsapp_api_url', env('WHATSAPP_API_URL', 'https://api.greatwhats.com/v1/messages')),
            'api_token' => self::getSetting('whatsapp_api_token', env('WHATSAPP_API_TOKEN', '')),
            'instance_id' => self::getSetting('whatsapp_instance_id', ''),
            'default_for_offices' => self::getSetting('whatsapp_default_for_offices', '1'),
        ];

        $stats = [
            'offices_count' => $offices->count(),
            'active_offices' => $offices->where('status', 'active')->count(),
            'property_types_count' => $propertyTypes->count(),
            'total_receipts' => $vouchers->where('voucher_type', 'receipt')->sum('amount'),
            'total_payments' => $vouchers->where('voucher_type', 'payment')->sum('amount'),
        ];

        return view('erp_dashboard', compact(
            'tab',
            'offices',
            'categories',
            'fieldTypes',
            'propertyTypes',
            'attributes',
            'accountTypes',
            'accounts',
            'defaultOfficeParentAccId',
            'vouchers',
            'officeVouchersByOffice',
            'whatsappSettings',
            'stats',
            'paymentMethods'
        ));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'regex:/^05[0-9]{8}$/'],
            'username' => 'required',
        ], [
            'phone.regex' => 'رقم الجوال يجب أن يتكون من 10 أرقام ويبدأ بـ 05.'
        ]);

        $formattedPhone = self::formatSaudiPhone($request->phone);
        $userId = Auth::id();

        $updateData = [
            'name' => $request->name,
            'phone' => $formattedPhone,
            'username' => trim($request->username),
            'updated_at' => now(),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        DB::table('users')->where('id', $userId)->update($updateData);

        return back()->with('success', 'تم تحديث بيانات الدخول الخاصة بك بنجاح.');
    }

    public function updateWhatsappSettings(Request $request)
    {
        $senderPhone = $request->input('sender_phone', $request->input('whatsapp_sender_phone', '0500000000'));
        $apiUrl      = $request->input('api_url', $request->input('whatsapp_api_url', ''));
        $apiToken    = $request->input('api_token', $request->input('whatsapp_api_token', ''));
        $instanceId  = $request->input('instance_id', $request->input('whatsapp_instance_id', ''));

        self::setSetting('whatsapp_sender_phone', self::formatSaudiPhone(trim($senderPhone)));
        self::setSetting('whatsapp_api_url', trim($apiUrl));
        self::setSetting('whatsapp_api_token', trim($apiToken));
        self::setSetting('whatsapp_instance_id', trim($instanceId));
        self::setSetting('whatsapp_default_for_offices', '1');

        return redirect()->route('erp.dashboard', ['tab' => 'whatsapp'])
            ->with('success', 'تم حفظ إعدادات الربط مع واتساب API وتعميمها بنجاح.');
    }

    public function storeOffice(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => ['required', 'regex:/^05[0-9]{8}$/'],
            'username' => 'required|unique:users,username',
        ], [
            'phone.regex' => 'رقم جوال المكتب يجب أن يتكون من 10 أرقام بالضبط ويبدأ بـ 05.',
            'username.unique' => 'اسم المستخدم هذا مستخدم مسبقاً، اختر اسماً آخر.'
        ]);

        $formattedPhone = self::formatSaudiPhone($request->phone);
        $billingType = $request->billing_type ?: 'annual_subscription';
        $annualFee = in_array($billingType, ['annual_subscription', 'hybrid']) ? ($request->annual_fee ?? 0) : 0;
        $commissionRate = in_array($billingType, ['per_property_percentage', 'hybrid']) ? ($request->company_commission_rate ?? 0) : 0;

        DB::transaction(function () use ($request, $formattedPhone, $billingType, $annualFee, $commissionRate) {
            $parentAccount = null;
            if ($request->filled('parent_account_id')) {
                $parentAccount = DB::table('accounts')->whereNull('office_id')->where('id', $request->parent_account_id)->first();
            }
            if (!$parentAccount) {
                $parentAccount = DB::table('accounts')->whereNull('office_id')->where('code', '1103')->first()
                              ?: DB::table('accounts')->whereNull('office_id')->where('code', '1000')->first();
            }

            $officeAccountId = self::createSequentialSubAccount(
                null,
                $parentAccount ? $parentAccount->id : null,
                'حساب ' . trim($request->name)
            );

            $officeId = DB::table('offices')->insertGetId([
                'name' => trim($request->name),
                'cr_number' => $request->cr_number,
                'fal_license' => $request->fal_license,
                'phone' => $formattedPhone,
                'email' => trim($request->username) . '@office.local',
                'address' => $request->address,
                'billing_type' => $billingType,
                'annual_fee' => $annualFee,
                'company_commission_rate' => $commissionRate,
                'subscription_ends_at' => $request->subscription_ends_at ?: now()->addYear()->toDateString(),
                'status' => 'active',
                'account_id' => $officeAccountId,
                'use_custom_whatsapp' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('users')->insert([
                'name' => 'مدير ' . trim($request->name),
                'username' => trim($request->username),
                'phone' => $formattedPhone,
                'email' => trim($request->username) . '@office.local',
                'password' => Hash::make($request->password ?: '123456'),
                'user_type' => 'office_admin',
                'office_id' => $officeId,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('erp.dashboard', ['tab' => 'offices'])
            ->with('success', 'تم إضافة المكتب العقاري وربطه تلقائياً وبشكل متسلسل في شجرة الحسابات وتفعيل اتفاقيته بنجاح.');
    }

    public function updateOffice(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'phone' => ['required', 'regex:/^05[0-9]{8}$/'],
            'username' => 'required',
        ], [
            'phone.regex' => 'رقم الجوال يجب أن يتكون من 10 أرقام ويبدأ بـ 05.'
        ]);

        $formattedPhone = self::formatSaudiPhone($request->phone);
        $billingType = $request->billing_type ?: 'annual_subscription';
        $annualFee = in_array($billingType, ['annual_subscription', 'hybrid']) ? ($request->annual_fee ?? 0) : 0;
        $commissionRate = in_array($billingType, ['per_property_percentage', 'hybrid']) ? ($request->company_commission_rate ?? 0) : 0;

        DB::transaction(function () use ($request, $id, $formattedPhone, $billingType, $annualFee, $commissionRate) {
            DB::table('offices')->where('id', $id)->update([
                'name' => trim($request->name),
                'cr_number' => $request->cr_number,
                'fal_license' => $request->fal_license,
                'phone' => $formattedPhone,
                'billing_type' => $billingType,
                'annual_fee' => $annualFee,
                'company_commission_rate' => $commissionRate,
                'subscription_ends_at' => $request->subscription_ends_at,
                'status' => $request->status,
                'updated_at' => now(),
            ]);

            $userUpdate = [
                'name' => 'مدير ' . trim($request->name),
                'phone' => $formattedPhone,
                'username' => trim($request->username),
                'is_active' => $request->status === 'active' ? 1 : 0,
                'updated_at' => now(),
            ];

            if ($request->filled('password')) {
                $userUpdate['password'] = Hash::make($request->password);
            }

            DB::table('users')
                ->where('office_id', $id)
                ->where('user_type', 'office_admin')
                ->update($userUpdate);
        });

        return redirect()->route('erp.dashboard', ['tab' => 'offices'])
            ->with('success', 'تم تحديث بيانات المكتب والاتفاقية المالية وبيانات الدخول بنجاح.');
    }

    public function ensureDynamicPropertyTables(): void
    {
        if (!Schema::hasTable('property_categories')) {
            Schema::create('property_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->nullable();
                $table->timestamps();
            });

            $defaultCats = [
                ['name' => 'سكني', 'slug' => 'residential'],
                ['name' => 'تجاري', 'slug' => 'commercial'],
                ['name' => 'زراعي', 'slug' => 'agricultural'],
                ['name' => 'صناعي', 'slug' => 'industrial'],
            ];
            foreach ($defaultCats as $cat) {
                DB::table('property_categories')->insert(array_merge($cat, ['created_at' => now(), 'updated_at' => now()]));
            }
        }

        if (!Schema::hasTable('custom_field_types')) {
            Schema::create('custom_field_types', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('input_type')->default('text');
                $table->timestamps();
            });

            $defaultFields = [
                ['name' => 'نص (Text)', 'input_type' => 'text'],
                ['name' => 'رقم / مساحة (Number)', 'input_type' => 'number'],
                ['name' => 'تاريخ (Date)', 'input_type' => 'date'],
                ['name' => 'نعم / لا', 'input_type' => 'boolean'],
                ['name' => 'قائمة خيارات منسدلة', 'input_type' => 'select'],
            ];
            foreach ($defaultFields as $ft) {
                DB::table('custom_field_types')->insert(array_merge($ft, ['created_at' => now(), 'updated_at' => now()]));
            }
        }
    }

    public function storePropertyCategory(Request $request)
    {
        $request->validate(['name' => 'required']);
        DB::table('property_categories')->insert([
            'name' => trim($request->name),
            'slug' => trim($request->name),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return redirect()->route('erp.dashboard', ['tab' => 'properties'])->with('success', 'تم إضافة التصنيف الرئيسي بنجاح.');
    }

    public function updatePropertyCategory(Request $request, $id)
    {
        $old = DB::table('property_categories')->where('id', $id)->first();
        if ($old) {
            DB::table('property_types')->where('category', $old->name)->update(['category' => trim($request->name)]);
            DB::table('property_categories')->where('id', $id)->update([
                'name' => trim($request->name),
                'updated_at' => now(),
            ]);
        }
        return redirect()->route('erp.dashboard', ['tab' => 'properties'])->with('success', 'تم تعديل التصنيف الرئيسي بنجاح.');
    }

    public function deletePropertyCategory($id)
    {
        DB::table('property_categories')->where('id', $id)->delete();
        return redirect()->route('erp.dashboard', ['tab' => 'properties'])->with('success', 'تم حذف التصنيف الرئيسي.');
    }

    public function storeFieldType(Request $request)
    {
        $request->validate(['name' => 'required']);
        DB::table('custom_field_types')->insert([
            'name' => trim($request->name),
            'input_type' => $request->input_type ?: 'text',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return redirect()->route('erp.dashboard', ['tab' => 'properties'])->with('success', 'تم إضافة نوع الحقل الجديد بنجاح.');
    }

    public function updateFieldType(Request $request, $id)
    {
        $old = DB::table('custom_field_types')->where('id', $id)->first();
        if ($old) {
            DB::table('property_type_attributes')->where('field_type', $old->name)->update(['field_type' => trim($request->name)]);
            DB::table('custom_field_types')->where('id', $id)->update([
                'name' => trim($request->name),
                'input_type' => $request->input_type ?: 'text',
                'updated_at' => now(),
            ]);
        }
        return redirect()->route('erp.dashboard', ['tab' => 'properties'])->with('success', 'تم تعديل نوع الحقل بنجاح.');
    }

    public function deleteFieldType($id)
    {
        DB::table('custom_field_types')->where('id', $id)->delete();
        return redirect()->route('erp.dashboard', ['tab' => 'properties'])->with('success', 'تم حذف نوع الحقل.');
    }

    public function storePropertyType(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category' => 'required',
        ]);

        DB::transaction(function () use ($request) {
            $typeId = DB::table('property_types')->insertGetId([
                'name' => trim($request->name),
                'category' => $request->category,
                'has_units' => $request->has('has_units') ? 1 : 0,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $names = $request->input('attribute_names', []);
            $types = $request->input('field_types', []);

            foreach ($names as $index => $attrName) {
                if (!empty(trim($attrName))) {
                    DB::table('property_type_attributes')->insert([
                        'property_type_id' => $typeId,
                        'attribute_name' => trim($attrName),
                        'field_type' => $types[$index] ?? 'نص (Text)',
                        'options' => null,
                        'is_required' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });

        return redirect()->route('erp.dashboard', ['tab' => 'properties'])
            ->with('success', 'تم إضافة نوع العقار وربط متطلباته بنجاح.');
    }

    public function deletePropertyType($id)
    {
        DB::table('property_type_attributes')->where('property_type_id', $id)->delete();
        DB::table('property_types')->where('id', $id)->delete();

        return redirect()->route('erp.dashboard', ['tab' => 'properties'])
            ->with('success', 'تم حذف نوع العقار وجميع متطلباته بنجاح.');
    }

    public function storePropertyAttribute(Request $request)
    {
        $names = $request->input('attribute_names', []);
        $types = $request->input('field_types', []);

        foreach ($names as $index => $attrName) {
            if (!empty(trim($attrName))) {
                DB::table('property_type_attributes')->insert([
                    'property_type_id' => $request->property_type_id,
                    'attribute_name' => trim($attrName),
                    'field_type' => $types[$index] ?? 'نص (Text)',
                    'options' => null,
                    'is_required' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->route('erp.dashboard', ['tab' => 'properties'])
            ->with('success', 'تم ربط المتطلبات بنوع العقار بنجاح.');
    }

    public function updatePropertyAttribute(Request $request, $id)
    {
        DB::table('property_type_attributes')->where('id', $id)->update([
            'attribute_name' => trim($request->attribute_name),
            'field_type' => $request->field_type,
            'updated_at' => now(),
        ]);

        return redirect()->route('erp.dashboard', ['tab' => 'properties'])
            ->with('success', 'تم تعديل المتطلب بنجاح.');
    }

    public function deletePropertyAttribute($id)
    {
        DB::table('property_type_attributes')->where('id', $id)->delete();

        return redirect()->route('erp.dashboard', ['tab' => 'properties'])
            ->with('success', 'تم حذف المتطلب بنجاح.');
    }

    public function ensureDynamicAccountTables(): void
    {
        if (!Schema::hasTable('custom_account_types')) {
            Schema::create('custom_account_types', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code_prefix')->default('1');
                $table->string('color')->default('emerald');
                $table->timestamps();
            });

            $defaultAccTypes = [
                ['name' => 'الأصول', 'code_prefix' => '1', 'color' => 'emerald'],
                ['name' => 'الخصوم والالتزامات', 'code_prefix' => '2', 'color' => 'amber'],
                ['name' => 'حقوق الملكية', 'code_prefix' => '3', 'color' => 'purple'],
                ['name' => 'الإيرادات', 'code_prefix' => '4', 'color' => 'blue'],
                ['name' => 'المصروفات التشغيلية', 'code_prefix' => '5', 'color' => 'rose'],
            ];
            foreach ($defaultAccTypes as $at) {
                DB::table('custom_account_types')->insert(array_merge($at, ['created_at' => now(), 'updated_at' => now()]));
            }
        }
    }

    public function storeAccountType(Request $request)
    {
        $request->validate(['name' => 'required']);
        DB::table('custom_account_types')->insert([
            'name' => trim($request->name),
            'code_prefix' => $request->code_prefix ?: (string) (DB::table('custom_account_types')->count() + 1),
            'color' => $request->color ?: 'emerald',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return redirect()->route('erp.dashboard', ['tab' => 'accounting'])->with('success', 'تم إضافة نوع الحساب الجديد بنجاح.');
    }

    public function updateAccountType(Request $request, $id)
    {
        $old = DB::table('custom_account_types')->where('id', $id)->first();
        if ($old) {
            DB::table('accounts')->where('type', $old->name)->update(['type' => trim($request->name)]);
            DB::table('custom_account_types')->where('id', $id)->update([
                'name' => trim($request->name),
                'code_prefix' => $request->code_prefix ?: $old->code_prefix,
                'updated_at' => now(),
            ]);
        }
        return redirect()->route('erp.dashboard', ['tab' => 'accounting'])->with('success', 'تم تعديل نوع الحساب بنجاح.');
    }

    public function deleteAccountType($id)
    {
        DB::table('custom_account_types')->where('id', $id)->delete();
        return redirect()->route('erp.dashboard', ['tab' => 'accounting'])->with('success', 'تم حذف نوع الحساب.');
    }

    public function ensurePaymentMethodsTable(): void
    {
        if (!Schema::hasTable('custom_payment_methods')) {
            Schema::create('custom_payment_methods', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->nullable();
                $table->timestamps();
            });

            $defaults = [
                ['name' => 'حوالة بنكية', 'code' => 'bank_transfer'],
                ['name' => 'نظام سداد', 'code' => 'sadad'],
                ['name' => 'شبكة مدى', 'code' => 'mada'],
                ['name' => 'نقداً (كاش)', 'code' => 'cash'],
                ['name' => 'شيك مصدق', 'code' => 'cheque'],
            ];
            foreach ($defaults as $pm) {
                DB::table('custom_payment_methods')->insert(array_merge($pm, ['created_at' => now(), 'updated_at' => now()]));
            }
        }
    }

    public static function translatePaymentMethod(?string $method): string
    {
        if (!$method) return 'حوالة بنكية';
        $map = [
            'bank_transfer' => 'حوالة بنكية',
            'sadad' => 'نظام سداد',
            'mada' => 'شبكة مدى',
            'cash' => 'نقداً (كاش)',
            'cheque' => 'شيك مصدق',
        ];
        if (isset($map[$method])) {
            return $map[$method];
        }
        if (Schema::hasTable('custom_payment_methods')) {
            $row = DB::table('custom_payment_methods')->where('code', $method)->orWhere('name', $method)->first();
            if ($row) return $row->name;
        }
        return $method;
    }

    public function storePaymentMethod(Request $request)
    {
        $this->ensurePaymentMethodsTable();
        $request->validate(['name' => 'required']);
        DB::table('custom_payment_methods')->insert([
            'name' => trim($request->name),
            'code' => trim($request->name),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return back()->with('success', 'تم إضافة طريقة الدفع الجديدة بنجاح.');
    }

    public function updatePaymentMethod(Request $request, $id)
    {
        $old = DB::table('custom_payment_methods')->where('id', $id)->first();
        if ($old) {
            DB::table('vouchers')->where('payment_method', $old->name)->orWhere('payment_method', $old->code)->update(['payment_method' => trim($request->name)]);
            DB::table('custom_payment_methods')->where('id', $id)->update([
                'name' => trim($request->name),
                'code' => trim($request->name),
                'updated_at' => now(),
            ]);
        }
        return back()->with('success', 'تم تعديل طريقة الدفع بنجاح.');
    }

    public function deletePaymentMethod($id)
    {
        DB::table('custom_payment_methods')->where('id', $id)->delete();
        return back()->with('success', 'تم حذف طريقة الدفع.');
    }

    public function seedDefaultAccounts()
    {
        $this->ensureDynamicAccountTables();

        if (DB::table('accounts')->whereNull('office_id')->count() > 0) {
            return redirect()->route('erp.dashboard', ['tab' => 'accounting'])
                ->with('success', 'شجرة الحسابات مفعلة مسبقاً.');
        }

        $tree = [
            [
                'code' => '1000', 'name' => 'الأصول', 'type' => 'الأصول', 'is_group' => 1,
                'children' => [
                    ['code' => '1101', 'name' => 'الصندوق والخزينة الرئيسية', 'is_group' => 0],
                    ['code' => '1102', 'name' => 'الحساب البنكي (الراجحي / الأهلي)', 'is_group' => 0],
                    ['code' => '1103', 'name' => 'ذمم مكاتب العقار (الاشتراكات والنسب)', 'is_group' => 1],
                ]
            ],
            [
                'code' => '2000', 'name' => 'الخصوم والالتزامات', 'type' => 'الخصوم والالتزامات', 'is_group' => 1,
                'children' => [
                    ['code' => '2101', 'name' => 'ضريبة القيمة المضافة المستحقة (15%)', 'is_group' => 0],
                    ['code' => '2102', 'name' => 'أمانات ومستحقات الملاك والمكاتب', 'is_group' => 0],
                ]
            ],
            [
                'code' => '4000', 'name' => 'الإيرادات', 'type' => 'الإيرادات', 'is_group' => 1,
                'children' => [
                    ['code' => '4101', 'name' => 'إيرادات الاشتراكات السنوية للمكاتب', 'is_group' => 0],
                    ['code' => '4102', 'name' => 'إيرادات نسب العقارات وتوثيق عقود إيجار', 'is_group' => 0],
                ]
            ],
            [
                'code' => '5000', 'name' => 'المصروفات التشغيلية', 'type' => 'المصروفات التشغيلية', 'is_group' => 1,
                'children' => [
                    ['code' => '5101', 'name' => 'مصروفات الرواتب والأجور', 'is_group' => 0],
                    ['code' => '5102', 'name' => 'مصروفات الخوادم والربط التقني (إيجار)', 'is_group' => 0],
                ]
            ],
        ];

        foreach ($tree as $root) {
            $parentId = DB::table('accounts')->insertGetId([
                'office_id' => null,
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
                    'office_id' => null,
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

        return redirect()->route('erp.dashboard', ['tab' => 'accounting'])
            ->with('success', 'تم بناء شجرة الحسابات المتفرعة القياسية بنجاح.');
    }

    public function storeAccount(Request $request)
    {
        $parentId = $request->parent_id ?: null;
        $parent = $parentId ? DB::table('accounts')->where('id', $parentId)->first() : null;
        $type = $parent ? $parent->type : $request->type;

        $names = $request->input('names', [$request->name]);
        $codes = $request->input('codes', [$request->code]);
        $isGroups = $request->input('is_groups', [$request->has('is_group') ? 1 : 0]);

        foreach ($names as $idx => $accName) {
            if (empty(trim($accName))) continue;

            $code = trim($codes[$idx] ?? '');
            if (empty($code)) {
                if ($parent) {
                    $siblingsCount = DB::table('accounts')->where('parent_id', $parent->id)->count();
                    $code = $parent->code . str_pad($siblingsCount + 1, 2, '0', STR_PAD_LEFT);
                } else {
                    $rootsCount = DB::table('accounts')->whereNull('office_id')->whereNull('parent_id')->count();
                    $code = ($rootsCount + 1) . '000';
                }
            }

            DB::table('accounts')->insert([
                'office_id' => null,
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

        return redirect()->route('erp.dashboard', ['tab' => 'accounting'])
            ->with('success', 'تمت إضافة الحسابات والفروع إلى الشجرة بنجاح.');
    }

    public function updateAccount(Request $request, $id)
    {
        DB::table('accounts')->where('id', $id)->update([
            'code' => trim($request->code),
            'name' => trim($request->name),
            'type' => $request->type,
            'parent_id' => $request->parent_id ?: null,
            'is_group' => $request->has('is_group') ? 1 : 0,
            'updated_at' => now(),
        ]);

        DB::table('accounts')->where('parent_id', $id)->update(['type' => $request->type]);

        return redirect()->route('erp.dashboard', ['tab' => 'accounting'])
            ->with('success', 'تم تعديل الحساب في الشجرة بنجاح.');
    }

    public function deleteAccount($id)
    {
        $hasChildren = DB::table('accounts')->where('parent_id', $id)->exists();
        if ($hasChildren) {
            return redirect()->route('erp.dashboard', ['tab' => 'accounting'])
                ->withErrors(['لا يمكن حذف مجلد رئيسي بداخله حسابات فرعية، قم بحذف أو نقل الفروع أولاً.']);
        }

        DB::table('accounts')->where('id', $id)->delete();

        return redirect()->route('erp.dashboard', ['tab' => 'accounting'])
            ->with('success', 'تم حذف الحساب من الشجرة بنجاح.');
    }

    public static function generateSequentialVoucherNumber(string $voucherType, ?int $officeId = null): string
    {
        $year = date('Y');
        $prefix = $voucherType === 'payment' ? "SA-{$year}-" : "RV-{$year}-";

        $query = DB::table('vouchers')
            ->where('voucher_type', $voucherType)
            ->where('voucher_number', 'like', $prefix . '%');

        if ($officeId === null) {
            $count = $query->count();
        } else {
            $count = $query->where('office_id', $officeId)->count();
        }

        $nextSeq = $count + 1;
        do {
            $candidate = $prefix . str_pad($nextSeq, 6, '0', STR_PAD_LEFT);
            $exists = DB::table('vouchers')->where('voucher_number', $candidate)->exists();
            $nextSeq++;
        } while ($exists);

        return $candidate;
    }

    public static function generateNextVoucherNumber(string $voucherType, ?int $officeId = null): string
    {
        return self::generateSequentialVoucherNumber($voucherType, $officeId);
    }

    public function storeVoucher(Request $request)
    {
        $voucherNumber = self::generateSequentialVoucherNumber($request->voucher_type, null);

        $debitAccountId = $request->debit_account_id;
        $creditAccountId = $request->credit_account_id;

        // دعم النموذج السريع من داخل نافذة كشف حساب المكتب
        if ((!$debitAccountId || !$creditAccountId) && $request->filled('cash_account_id')) {
            $cashAccId = $request->cash_account_id;
            $officeAccId = null;
            if ($request->filled('office_id')) {
                $officeRow = DB::table('offices')->where('id', $request->office_id)->first();
                $officeAccId = $officeRow->account_id ?? null;
            }
            $officeAccId = $officeAccId ?: $cashAccId;

            if ($request->voucher_type === 'receipt') {
                $debitAccountId = $cashAccId;
                $creditAccountId = $officeAccId;
            } else {
                $debitAccountId = $officeAccId;
                $creditAccountId = $cashAccId;
            }
        }

        DB::transaction(function () use ($request, $voucherNumber, $debitAccountId, $creditAccountId) {
            DB::table('vouchers')->insert([
                'voucher_number' => $voucherNumber,
                'voucher_type' => $request->voucher_type,
                'office_id' => $request->office_id ?: null,
                'debit_account_id' => $debitAccountId,
                'credit_account_id' => $creditAccountId,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method ?? 'bank_transfer',
                'reference_number' => $request->reference_number ?? null,
                'voucher_date' => $request->voucher_date ?: date('Y-m-d'),
                'notes' => trim($request->notes),
                'created_by' => Auth::id() ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('accounts')->where('id', $debitAccountId)->increment('balance', $request->amount);
            DB::table('accounts')->where('id', $creditAccountId)->decrement('balance', $request->amount);
        });

        if ($request->filled('stay_in_accounting')) {
            return redirect()->route('erp.dashboard', ['tab' => 'accounting'])
                ->with('success', 'تم إصدار السند المالي رقم (' . $voucherNumber . ') وتحديث رصيد الحساب في الشجرة فوراً.');
        }

        if ($request->filled('stay_in_offices')) {
            return redirect()->route('erp.dashboard', ['tab' => 'offices'])
                ->with('success', 'تم تسجيل السند رقم (' . $voucherNumber . ') في كشف حساب المكتب بنجاح.');
        }

        return redirect()->route('erp.dashboard', ['tab' => 'vouchers'])
            ->with('success', 'تم إصدار السند المالي رقم (' . $voucherNumber . ') وترحيل القيد بنجاح.');
    }

    public function updateVoucher(Request $request, $id)
    {
        $oldVoucher = DB::table('vouchers')->where('id', $id)->first();
        if (!$oldVoucher) {
            return back()->withErrors(['السند غير موجود.']);
        }

        DB::transaction(function () use ($request, $id, $oldVoucher) {
            DB::table('accounts')->where('id', $oldVoucher->debit_account_id)->decrement('balance', $oldVoucher->amount);
            DB::table('accounts')->where('id', $oldVoucher->credit_account_id)->increment('balance', $oldVoucher->amount);

            $newDebitId = $request->debit_account_id ?: $oldVoucher->debit_account_id;
            $newCreditId = $request->credit_account_id ?: $oldVoucher->credit_account_id;

            DB::table('vouchers')->where('id', $id)->update([
                'office_id' => $request->has('office_id') ? ($request->office_id ?: null) : $oldVoucher->office_id,
                'debit_account_id' => $newDebitId,
                'credit_account_id' => $newCreditId,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method ?: $oldVoucher->payment_method,
                'voucher_date' => $request->voucher_date ?: $oldVoucher->voucher_date,
                'notes' => trim($request->notes),
                'updated_at' => now(),
            ]);

            DB::table('accounts')->where('id', $newDebitId)->increment('balance', $request->amount);
            DB::table('accounts')->where('id', $newCreditId)->decrement('balance', $request->amount);
        });

        return back()->with('success', 'تم تعديل السند المالي رقم (' . $oldVoucher->voucher_number . ') وتحديث الأرصدة بنجاح.');
    }

    public function deleteVoucher($id)
    {
        $voucher = DB::table('vouchers')->where('id', $id)->first();
        if ($voucher) {
            DB::transaction(function () use ($voucher) {
                DB::table('accounts')->where('id', $voucher->debit_account_id)->decrement('balance', $voucher->amount);
                DB::table('accounts')->where('id', $voucher->credit_account_id)->increment('balance', $voucher->amount);
                DB::table('vouchers')->where('id', $voucher->id)->delete();
            });
        }

        return back()->with('success', 'تم حذف السند وعكس قيده المحاسبي بنجاح.');
    }

    public function printVoucher($id)
    {
        $voucher = DB::table('vouchers')
            ->leftJoin('offices', 'vouchers.office_id', '=', 'offices.id')
            ->leftJoin('owners', 'vouchers.owner_id', '=', 'owners.id')
            ->leftJoin('tenants', 'vouchers.tenant_id', '=', 'tenants.id')
            ->leftJoin('accounts as debit_acc', 'vouchers.debit_account_id', '=', 'debit_acc.id')
            ->leftJoin('accounts as credit_acc', 'vouchers.credit_account_id', '=', 'credit_acc.id')
            ->leftJoin('users', 'vouchers.created_by', '=', 'users.id')
            ->select(
                'vouchers.*',
                'offices.name as office_name',
                'offices.cr_number as office_cr',
                'offices.fal_license as office_fal',
                'owners.name as owner_name',
                'tenants.name as tenant_name',
                'debit_acc.code as debit_code',
                'debit_acc.name as debit_name',
                'credit_acc.code as credit_code',
                'credit_acc.name as credit_name',
                'users.name as creator_name'
            )
            ->where('vouchers.id', $id)
            ->first();

        if (!$voucher) {
            abort(404);
        }

        return view('voucher_print', compact('voucher'));
    }

    public function sendVoucherWhatsApp($id)
    {
        $voucher = DB::table('vouchers')
            ->leftJoin('offices', 'vouchers.office_id', '=', 'offices.id')
            ->select('vouchers.*', 'offices.name as office_name', 'offices.phone as office_phone')
            ->where('vouchers.id', $id)
            ->first();

        if (!$voucher || empty($voucher->office_phone)) {
            return back()->withErrors(['لا يوجد رقم جوال مكتب مرتبط بهذا السند لإرسال الرسالة إليه.']);
        }

        $typeAr = $voucher->voucher_type === 'receipt' ? 'سند قبض' : 'سند صرف';
        $msg = "📄 *إشعار {$typeAr} رسمي*\n"
             . "رقم السند: *{$voucher->voucher_number}*\n"
             . "السادة: {$voucher->office_name}\n"
             . "المبلغ: *" . number_format($voucher->amount, 2) . " ر.س*\n"
             . "التاريخ: {$voucher->voucher_date}\n"
             . "البيان: {$voucher->notes}";

        $sent = self::sendWhatsAppMessage($voucher->office_phone, $msg, null);

        if ($sent) {
            return back()->with('success', 'تم إرسال تفاصيل السند رقم (' . $voucher->voucher_number . ') عبر الواتساب إلى (' . $voucher->office_name . ') بنجاح.');
        }

        return back()->with('success', 'تم تجهيز إشعار السند رقم (' . $voucher->voucher_number . ') للطرف (' . $voucher->office_name . ') - يرجى التأكد من تفعيل إعدادات WhatsApp API للإرسال الفعلي.');
    }
}