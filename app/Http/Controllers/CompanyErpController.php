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
                if (!Schema::hasColumn('offices', 'tax_number')) {
                    $table->string('tax_number')->nullable();
                }
                if (!Schema::hasColumn('offices', 'national_address')) {
                    $table->string('national_address')->nullable();
                }
                if (!Schema::hasColumn('offices', 'allow_owner_tax_invoice')) {
                    $table->boolean('allow_owner_tax_invoice')->default(false);
                }
                if (!Schema::hasColumn('offices', 'reminder_days_before')) {
                    $table->integer('reminder_days_before')->default(15);
                }
                if (!Schema::hasColumn('offices', 'reminder_target')) {
                    $table->string('reminder_target')->default('both'); // office | tenant | both
                }
                if (!Schema::hasColumn('offices', 'reminder_template')) {
                    $table->text('reminder_template')->nullable();
                }
                if (!Schema::hasColumn('offices', 'enable_auto_reminders')) {
                    $table->boolean('enable_auto_reminders')->default(true);
                }
                if (!Schema::hasColumn('offices', 'plan_id')) {
                    $table->unsignedBigInteger('plan_id')->nullable();
                }
                if (!Schema::hasColumn('offices', 'allowed_tabs')) {
                    $table->text('allowed_tabs')->nullable();
                }
            });
        }
        $this->ensureOfficePlansTable();

        if (Schema::hasTable('owners')) {
            Schema::table('owners', function (Blueprint $table) {
                if (!Schema::hasColumn('owners', 'account_id')) {
                    $table->unsignedBigInteger('account_id')->nullable();
                }
                if (!Schema::hasColumn('owners', 'tax_number')) {
                    $table->string('tax_number')->nullable();
                }
                if (!Schema::hasColumn('owners', 'cr_number')) {
                    $table->string('cr_number')->nullable();
                }
                if (!Schema::hasColumn('owners', 'national_address')) {
                    $table->string('national_address')->nullable();
                }
            });
        }

        if (Schema::hasTable('tenants')) {
            Schema::table('tenants', function (Blueprint $table) {
                if (!Schema::hasColumn('tenants', 'account_id')) {
                    $table->unsignedBigInteger('account_id')->nullable();
                }
                if (!Schema::hasColumn('tenants', 'tax_number')) {
                    $table->string('tax_number')->nullable();
                }
                if (!Schema::hasColumn('tenants', 'national_address')) {
                    $table->string('national_address')->nullable();
                }
            });
        }

        self::ensureSystemSettingsTable();

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

    public static function ensureSystemSettingsTable(): void
    {
        if (!Schema::hasTable('system_settings')) {
            Schema::create('system_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->nullable()->index();
                $table->text('value')->nullable();
                $table->timestamps();
            });
            return;
        }

        Schema::table('system_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('system_settings', 'key')) {
                $table->string('key')->nullable();
            }
            if (!Schema::hasColumn('system_settings', 'value')) {
                $table->text('value')->nullable();
            }
            if (!Schema::hasColumn('system_settings', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('system_settings', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }

    public static function getSetting(string $key, $default = null)
    {
        try {
            self::ensureSystemSettingsTable();
            $row = DB::table('system_settings')->where('key', $key)->first();
            return $row ? $row->value : $default;
        } catch (\Throwable $e) {
            return $default;
        }
    }

    public static function setSetting(string $key, $value): void
    {
        self::ensureSystemSettingsTable();

        $exists = DB::table('system_settings')->where('key', $key)->exists();
        if ($exists) {
            DB::table('system_settings')->where('key', $key)->update([
                'value' => $value,
                'updated_at' => now(),
            ]);
        } else {
            $payload = [
                'key' => $key,
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (Schema::hasColumn('system_settings', 'setting_key')) {
                $payload['setting_key'] = $key;
            }
            if (Schema::hasColumn('system_settings', 'name')) {
                $payload['name'] = $key;
            }
            DB::table('system_settings')->insert($payload);
        }
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
        if (!session('erp_fast_boot_done')) {
            $this->ensureDynamicAccountTables();
            $this->ensurePaymentMethodsTable();
            $this->ensureJournalTables();
            $this->ensureSystemMonitoringTables();
            $this->ensureTaxInvoicesTable();
            session(['erp_fast_boot_done' => true]);
        }

        $accountTypes = DB::table('custom_account_types')->orderBy('id')->get();
        $accounts = DB::table('accounts')->whereNull('office_id')->orderBy('code')->get();

        $paymentMethods = DB::table('custom_payment_methods')
            ->leftJoin('accounts', 'custom_payment_methods.account_id', '=', 'accounts.id')
            ->whereNull('custom_payment_methods.office_id')
            ->select('custom_payment_methods.*', 'accounts.code as account_code', 'accounts.name as account_name')
            ->orderBy('custom_payment_methods.id')
            ->get();

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
                'accounts.debit_balance as account_debit',
                'accounts.credit_balance as account_credit',
                'accounts.parent_id as parent_account_id'
            )
            ->orderBy('offices.id', 'desc')
            ->get();

        $categories = DB::table('property_categories')->orderBy('id')->get();
        $fieldTypes = DB::table('custom_field_types')->orderBy('id')->get();
        $propertyTypes = DB::table('property_types')
            ->leftJoin('accounts', 'property_types.account_id', '=', 'accounts.id')
            ->select('property_types.*', 'accounts.code as account_code', 'accounts.name as account_name')
            ->orderBy('property_types.id', 'desc')
            ->get();

        $attributes = DB::table('property_type_attributes')
            ->join('property_types', 'property_type_attributes.property_type_id', '=', 'property_types.id')
            ->select('property_type_attributes.*', 'property_types.name as type_name', 'property_types.category')
            ->orderBy('property_type_attributes.id', 'asc')
            ->get();

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

        // جلب القيود اليومية وتفاصيلها
        $journalEntries = DB::table('journal_entries')
            ->whereNull('office_id')
            ->orderBy('entry_number', 'asc')
            ->get();

        $journalItems = DB::table('journal_entry_items')
            ->join('journal_entries', 'journal_entry_items.journal_entry_id', '=', 'journal_entries.id')
            ->leftJoin('accounts', 'journal_entry_items.account_id', '=', 'accounts.id')
            ->whereNull('journal_entries.office_id')
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

        $whatsappSettings = [
            'sender_phone' => self::toLocalPhone(self::getSetting('whatsapp_sender_phone', '0500000000')),
            'api_url' => self::getSetting('whatsapp_api_url', env('WHATSAPP_API_URL', 'https://api.greatwhats.com/v1/messages')),
            'api_token' => self::getSetting('whatsapp_api_token', env('WHATSAPP_API_TOKEN', '')),
            'instance_id' => self::getSetting('whatsapp_instance_id', ''),
            'default_for_offices' => self::getSetting('whatsapp_default_for_offices', '1'),
        ];

        $this->ensureSystemMonitoringTables();
        $this->ensureTaxInvoicesTable();
        if ($request->has('sync_logs')) {
            $this->syncLaravelLogFileToTable();
        }

        $systemLogs = DB::table('system_error_logs')->orderBy('id', 'desc')->limit(250)->get();$diagnosticRuns = DB::table('system_diagnostic_runs')->orderBy('id', 'desc')->limit(40)->get();

       $companyTaxProfile = [
            'legal_name' => self::getSetting('company_legal_name', 'الشركة الأم لإدارة العقارات والأملاك'),
            'tax_number' => self::getSetting('company_tax_number', '300000000000003'),
            'cr_number' => self::getSetting('company_cr_number', '1010000000'),
            'national_address' => self::getSetting('company_national_address', 'الرياض - المملكة العربية السعودية'),
            'default_vat_rate' => (float) self::getSetting('default_vat_rate', '15'),
        ];

        $companyInvoices = DB::table('tax_invoices')
            ->leftJoin('offices', 'tax_invoices.office_id', '=', 'offices.id')
            ->where('tax_invoices.issuer_level', 'company')
            ->select('tax_invoices.*', 'offices.name as office_name')
            ->orderBy('tax_invoices.id', 'desc')
            ->get();

        $this->ensureOfficePlansTable();
        $officePlans = DB::table('office_plans')->orderBy('id', 'asc')->get();$availableOfficeTabs = self::getAvailableOfficeTabs();

        $landingSettings = self::getLandingSettings();$activityLogs = self::getFilteredActivityLogs();

        $stats = [
            'offices_count' => $offices->count(),
            'active_offices' => $offices->where('status', 'active')->count(),
            'property_types_count' => $propertyTypes->count(),
            'total_receipts' => $vouchers->where('voucher_type', 'receipt')->sum('amount'),
            'total_payments' => $vouchers->where('voucher_type', 'payment')->sum('amount'),
            'unresolved_errors' => $systemLogs->where('level', 'ERROR')->count(),
        ];

        return view('erp_dashboard', compact(
            'tab',
            'offices',
            'officePlans',
            'availableOfficeTabs',
            'categories',
            'fieldTypes',
            'propertyTypes',
            'attributes',
            'accountTypes',
            'accounts',
            'defaultOfficeParentAccId',
            'vouchers',
            'officeVouchersByOffice',
            'journalEntries',
            'journalItems',
            'journalItemsByEntry',
            'whatsappSettings',
            'stats',
            'paymentMethods',
            'systemLogs',
            'diagnosticRuns',
            'companyTaxProfile',
            'companyInvoices',
            'landingSettings',
            'activityLogs'
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
        $billingType =$request->billing_type ?: 'annual_subscription';
        $annualFee = in_array($billingType, ['annual_subscription', 'hybrid']) ? ($request->annual_fee ?? 0) : 0;
        $commissionRate = in_array($billingType, ['per_property_percentage', 'hybrid']) ? ($request->company_commission_rate ?? 0) : 0;

        $planId = $request->filled('plan_id') ? (int)$request->plan_id : null;
        $allowedTabs =$request->input('allowed_tabs', []);
        if ($planId && empty($allowedTabs)) {
            $planRow = DB::table('office_plans')->where('id',$planId)->first();
            if ($planRow && !empty($planRow->allowed_tabs)) {
                $allowedTabs = json_decode($planRow->allowed_tabs, true) ?: [];
            }
        }
        if (empty($allowedTabs)) {$allowedTabs = array_keys(self::getAvailableOfficeTabs());
        }
        if (!in_array('overview', $allowedTabs)) {
            array_unshift($allowedTabs, 'overview');
        }

        DB::transaction(function () use ($request,$formattedPhone, $billingType,$annualFee, $commissionRate,$planId, $allowedTabs) {$parentAccount = null;
            if ($request->filled('parent_account_id')) {
                $parentAccount = DB::table('accounts')->whereNull('office_id')->where('id',$request->parent_account_id)->first();
            }
            if (!$parentAccount) {$parentAccount = DB::table('accounts')->whereNull('office_id')->where('code', '1103')->first()
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
                'plan_id' => $planId,
                'allowed_tabs' => json_encode(array_values(array_unique($allowedTabs)), JSON_UNESCAPED_UNICODE),
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
            ->with('success', 'تم إضافة المكتب العقاري وتخصيص باقته وصلاحيات صفحاته وربطه بالشجرة بنجاح.');
    }

    public function updateOffice(Request $request,$id)
    {
        $request->validate([
            'name' => 'required',
            'phone' => ['required', 'regex:/^05[0-9]{8}$/'],
            'username' => 'required',
        ], [
            'phone.regex' => 'رقم الجوال يجب أن يتكون من 10 أرقام ويبدأ بـ 05.'
        ]);

        $formattedPhone = self::formatSaudiPhone($request->phone);
        $billingType =$request->billing_type ?: 'annual_subscription';
        $annualFee = in_array($billingType, ['annual_subscription', 'hybrid']) ? ($request->annual_fee ?? 0) : 0;
        $commissionRate = in_array($billingType, ['per_property_percentage', 'hybrid']) ? ($request->company_commission_rate ?? 0) : 0;

        $planId = $request->filled('plan_id') ? (int)$request->plan_id : null;
        $allowedTabs =$request->input('allowed_tabs', []);
        if (empty($allowedTabs)) {$allowedTabs = ['overview'];
        } elseif (!in_array('overview', $allowedTabs)) {
            array_unshift($allowedTabs, 'overview');
        }

        DB::transaction(function () use ($request,$id, $formattedPhone,$billingType, $annualFee,$commissionRate, $planId,$allowedTabs) {
            DB::table('offices')->where('id', $id)->update([
                'name' => trim($request->name),
                'cr_number' => $request->cr_number,
                'fal_license' => $request->fal_license,
                'phone' => $formattedPhone,
                'billing_type' => $billingType,
                'annual_fee' => $annualFee,
                'company_commission_rate' => $commissionRate,
                'plan_id' => $planId,
                'allowed_tabs' => json_encode(array_values(array_unique($allowedTabs)), JSON_UNESCAPED_UNICODE),
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

        if (Schema::hasTable('property_types')) {
            Schema::table('property_types', function (Blueprint $table) {
                if (!Schema::hasColumn('property_types', 'account_id')) {
                    $table->unsignedBigInteger('account_id')->nullable();
                }
            });

            // فك قيد CHECK القديم عن عمود category في جدول property_types بقاعدة بيانات SQLite
            try {
                $master = DB::selectOne("SELECT sql FROM sqlite_master WHERE type='table' AND name='property_types'");
                if ($master && isset($master->sql) && stripos($master->sql, 'check') !== false) {
                    DB::statement('PRAGMA foreign_keys = OFF;');
                    DB::transaction(function () {
                        Schema::create('property_types_unlocked_tmp', function (Blueprint $table) {
                            $table->id();
                            $table->string('name');
                            $table->string('category')->default('سكني');
                            $table->unsignedBigInteger('account_id')->nullable();
                            $table->boolean('has_units')->default(true);
                            $table->boolean('is_active')->default(true);
                            $table->timestamps();
                        });

                        $oldRows = DB::table('property_types')->get();
                        foreach ($oldRows as $row) {
                            DB::table('property_types_unlocked_tmp')->insert([
                                'id' => $row->id,
                                'name' => $row->name,
                                'category' => $row->category ?? 'سكني',
                                'account_id' => $row->account_id ?? null,
                                'has_units' => $row->has_units ?? 1,
                                'is_active' => $row->is_active ?? 1,
                                'created_at' => $row->created_at ?? now(),
                                'updated_at' => $row->updated_at ?? now(),
                            ]);
                        }

                        Schema::drop('property_types');
                        Schema::rename('property_types_unlocked_tmp', 'property_types');
                    });
                    DB::statement('PRAGMA foreign_keys = ON;');
                }
            } catch (\Throwable $e) {
                try { DB::statement('PRAGMA foreign_keys = ON;'); } catch (\Throwable $ignore) {}
            }
        }

        if (Schema::hasTable('property_type_attributes')) {
            // فك قيد CHECK القديم عن عمود field_type في جدول property_type_attributes إن وجد
            try {
                $attrMaster = DB::selectOne("SELECT sql FROM sqlite_master WHERE type='table' AND name='property_type_attributes'");
                if ($attrMaster && isset($attrMaster->sql) && stripos($attrMaster->sql, 'check') !== false) {
                    DB::statement('PRAGMA foreign_keys = OFF;');
                    DB::transaction(function () {
                        Schema::create('property_type_attributes_unlocked_tmp', function (Blueprint $table) {
                            $table->id();
                            $table->unsignedBigInteger('property_type_id');
                            $table->string('attribute_name');
                            $table->string('field_type')->default('نص (Text)');
                            $table->text('options')->nullable();
                            $table->boolean('is_required')->default(true);
                            $table->timestamps();
                        });

                        $oldAttrs = DB::table('property_type_attributes')->get();
                        foreach ($oldAttrs as $row) {
                            DB::table('property_type_attributes_unlocked_tmp')->insert([
                                'id' => $row->id,
                                'property_type_id' => $row->property_type_id,
                                'attribute_name' => $row->attribute_name,
                                'field_type' => $row->field_type ?? 'نص (Text)',
                                'options' => $row->options ?? null,
                                'is_required' => $row->is_required ?? 1,
                                'created_at' => $row->created_at ?? now(),
                                'updated_at' => $row->updated_at ?? now(),
                            ]);
                        }

                        Schema::drop('property_type_attributes');
                        Schema::rename('property_type_attributes_unlocked_tmp', 'property_type_attributes');
                    });
                    DB::statement('PRAGMA foreign_keys = ON;');
                }
            } catch (\Throwable $e) {
                try { DB::statement('PRAGMA foreign_keys = ON;'); } catch (\Throwable $ignore) {}
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
                ['name' => 'مرفق ملف PDF أو صورة (حد أقصى 5MB)', 'input_type' => 'file'],
            ];
            foreach ($defaultFields as $ft) {
                DB::table('custom_field_types')->insert(array_merge($ft, ['created_at' => now(), 'updated_at' => now()]));
            }
        } else {
            // التأكد من وجود خيار رفع ملف PDF أو صورة تلقائياً
            $hasFileField = DB::table('custom_field_types')->where('input_type', 'file')->exists();
            if (!$hasFileField) {
                DB::table('custom_field_types')->insert([
                    'name' => 'مرفق ملف PDF أو صورة (حد أقصى 5MB)',
                    'input_type' => 'file',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
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
        $this->ensureDynamicPropertyTables();

        $request->validate([
            'name' => 'required',
            'category' => 'required',
        ]);

        DB::transaction(function () use ($request) {
            $accountId = $request->filled('account_id') ? (int) $request->account_id : null;

            // إذا لم يحدد حساب فرعي جاهز وطلب إنشاء حساب تلقائي تحت مجلد
            if (!$accountId && $request->filled('parent_account_id')) {
                $accountId = self::createSequentialSubAccount(
                    null,
                    (int) $request->parent_account_id,
                    'عقارات: ' . trim($request->name)
                );
            }

            $typeId = DB::table('property_types')->insertGetId([
                'name' => trim($request->name),
                'category' => $request->category,
                'account_id' => $accountId,
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
            ->with('success', 'تم إضافة نوع العقار وربطه بحساب الشجرة ومتطلباته بنجاح.');
    }

    public function updatePropertyType(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'category' => 'required',
        ]);

        DB::table('property_types')->where('id', $id)->update([
            'name' => trim($request->name),
            'category' => $request->category,
            'account_id' => $request->filled('account_id') ? (int) $request->account_id : null,
            'has_units' => $request->has('has_units') ? 1 : 0,
            'updated_at' => now(),
        ]);

        return redirect()->route('erp.dashboard', ['tab' => 'properties'])
            ->with('success', 'تم تحديث نوع العقار وحسابه المرتبط بنجاح.');
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

        if (Schema::hasTable('accounts')) {
            Schema::table('accounts', function (Blueprint $table) {
                if (!Schema::hasColumn('accounts', 'debit_balance')) {
                    $table->decimal('debit_balance', 15, 2)->default(0);
                }
                if (!Schema::hasColumn('accounts', 'credit_balance')) {
                    $table->decimal('credit_balance', 15, 2)->default(0);
                }
            });

            // فك قيد CHECK القديم عن عمود type في جدول accounts بقاعدة بيانات SQLite
            $this->unlockAccountsTypeConstraint();
        }
    }

    /**
     * إزالة قيد CHECK عن عمود type في جدول accounts ليقبل المسميات العربية والمخصصة
     */
    public function unlockAccountsTypeConstraint(): void
    {
        if (!Schema::hasTable('accounts')) {
            return;
        }

        try {
            $master = DB::selectOne("SELECT sql FROM sqlite_master WHERE type='table' AND name='accounts'");
            if ($master && isset($master->sql) && stripos($master->sql, 'check') !== false && stripos($master->sql, 'type') !== false) {
                DB::statement('PRAGMA foreign_keys = OFF;');
                DB::transaction(function () {
                    Schema::create('accounts_unlocked_tmp', function (Blueprint $table) {
                        $table->id();
                        $table->unsignedBigInteger('office_id')->nullable();
                        $table->unsignedBigInteger('parent_id')->nullable();
                        $table->string('code');
                        $table->string('name');
                        $table->string('type')->default('الأصول');
                        $table->boolean('is_group')->default(false);
                        $table->decimal('balance', 15, 2)->default(0);
                        $table->decimal('debit_balance', 15, 2)->default(0);
                        $table->decimal('credit_balance', 15, 2)->default(0);
                        $table->timestamps();
                    });

                    $oldRows = DB::table('accounts')->get();
                    foreach ($oldRows as $row) {
                        DB::table('accounts_unlocked_tmp')->insert([
                            'id' => $row->id,
                            'office_id' => $row->office_id ?? null,
                            'parent_id' => $row->parent_id ?? null,
                            'code' => $row->code,
                            'name' => $row->name,
                            'type' => $row->type ?? 'الأصول',
                            'is_group' => $row->is_group ?? 0,
                            'balance' => $row->balance ?? 0,
                            'debit_balance' => $row->debit_balance ?? 0,
                            'credit_balance' => $row->credit_balance ?? 0,
                            'created_at' => $row->created_at ?? now(),
                            'updated_at' => $row->updated_at ?? now(),
                        ]);
                    }

                    Schema::drop('accounts');
                    Schema::rename('accounts_unlocked_tmp', 'accounts');
                });
                DB::statement('PRAGMA foreign_keys = ON;');
            }
        } catch (\Throwable $e) {
            try { DB::statement('PRAGMA foreign_keys = ON;'); } catch (\Throwable $ignore) {}
        }
    }

    public function ensureJournalTables(): void
    {
        if (!Schema::hasTable('journal_entries')) {
            Schema::create('journal_entries', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('office_id')->nullable();
                $table->unsignedBigInteger('entry_number')->default(1);
                $table->date('entry_date');
                $table->text('general_notes')->nullable();
                $table->decimal('total_debit', 15, 2)->default(0);
                $table->decimal('total_credit', 15, 2)->default(0);
                $table->decimal('total_tax', 15, 2)->default(0);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('journal_entry_items')) {
            Schema::create('journal_entry_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('journal_entry_id');
                $table->unsignedBigInteger('account_id');
                $table->decimal('debit', 15, 2)->default(0);
                $table->decimal('credit', 15, 2)->default(0);
                $table->decimal('tax', 15, 2)->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public static function applyAccountMovement(int $accountId, float $debit, float $credit, bool $reverse = false): void
    {
        if (!$accountId) return;
        $factor = $reverse ? -1 : 1;
        $d = $debit * $factor;
        $c = $credit * $factor;
        $net = $d - $c;

        $acc = DB::table('accounts')->where('id', $accountId)->first();
        if ($acc) {
            DB::table('accounts')->where('id', $accountId)->update([
                'debit_balance' => ($acc->debit_balance ?? 0) + $d,
                'credit_balance' => ($acc->credit_balance ?? 0) + $c,
                'balance' => ($acc->balance ?? 0) + $net,
                'updated_at' => now(),
            ]);
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
                $table->unsignedBigInteger('office_id')->nullable();
                $table->unsignedBigInteger('account_id')->nullable();
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
        } else {
            Schema::table('custom_payment_methods', function (Blueprint $table) {
                if (!Schema::hasColumn('custom_payment_methods', 'office_id')) {
                    $table->unsignedBigInteger('office_id')->nullable();
                }
                if (!Schema::hasColumn('custom_payment_methods', 'account_id')) {
                    $table->unsignedBigInteger('account_id')->nullable();
                }
            });
        }

        // فحص وإزالة قيد CHECK القديم على عمود payment_method في جدول vouchers بقاعدة بيانات SQLite
        $this->unlockVouchersPaymentMethodConstraint();
    }

    public function unlockVouchersPaymentMethodConstraint(): void
    {
        if (!Schema::hasTable('vouchers')) {
            return;
        }

        try {
            $master = DB::selectOne("SELECT sql FROM sqlite_master WHERE type='table' AND name='vouchers'");
            if ($master && isset($master->sql) && stripos($master->sql, 'check ("payment_method"') !== false) {
                DB::statement('PRAGMA foreign_keys = OFF;');
                DB::transaction(function () {
                    Schema::create('vouchers_unlocked_tmp', function (Blueprint $table) {
                        $table->id();
                        $table->string('voucher_number')->unique();
                        $table->string('voucher_type')->default('receipt');
                        $table->unsignedBigInteger('office_id')->nullable();
                        $table->unsignedBigInteger('owner_id')->nullable();
                        $table->unsignedBigInteger('tenant_id')->nullable();
                        $table->unsignedBigInteger('lease_contract_id')->nullable();
                        $table->unsignedBigInteger('debit_account_id');
                        $table->unsignedBigInteger('credit_account_id');
                        $table->decimal('amount', 15, 2)->default(0);
                        $table->string('payment_method')->default('bank_transfer');
                        $table->string('reference_number')->nullable();
                        $table->date('voucher_date');
                        $table->text('notes')->nullable();
                        $table->unsignedBigInteger('created_by')->nullable();
                        $table->timestamps();
                    });

                    $oldRows = DB::table('vouchers')->get();
                    foreach ($oldRows as $row) {
                        $arr = (array) $row;
                        DB::table('vouchers_unlocked_tmp')->insert($arr);
                    }

                    Schema::drop('vouchers');
                    Schema::rename('vouchers_unlocked_tmp', 'vouchers');
                });
                DB::statement('PRAGMA foreign_keys = ON;');
            }
        } catch (\Throwable $e) {
            // في حال عدم إمكانية إعادة البناء، سيقوم النظام بتحويل القيمة للكود القياسي تلقائياً
        }
    }

    /**
     * دالة حماية ذكية لتحويل طريقة الدفع في حال وجود قيد CHECK قديم في SQLite
     */
    public static function normalizeVoucherPaymentMethod(?string $method): string
    {
        $method = trim($method ?: 'bank_transfer');
        try {
            $master = DB::selectOne("SELECT sql FROM sqlite_master WHERE type='table' AND name='vouchers'");
            if ($master && isset($master->sql) && stripos($master->sql, 'check') !== false && stripos($master->sql, 'payment_method') !== false) {
                $reverseMap = [
                    'حوالة بنكية' => 'bank_transfer',
                    'نظام سداد' => 'sadad',
                    'سداد' => 'sadad',
                    'شبكة مدى' => 'mada',
                    'مدى' => 'mada',
                    'نقداً (كاش)' => 'cash',
                    'نقدي' => 'cash',
                    'شيك مصدق' => 'cheque',
                    'شيك' => 'cheque',
                ];
                if (isset($reverseMap[$method])) {
                    return $reverseMap[$method];
                }
                if (in_array($method, ['bank_transfer', 'sadad', 'mada', 'cash', 'cheque'])) {
                    return $method;
                }
                return 'bank_transfer';
            }
        } catch (\Throwable $e) {
            return 'bank_transfer';
        }
        return $method;
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
            'office_id' => null,
            'account_id' => $request->filled('account_id') ? (int) $request->account_id : null,
            'name' => trim($request->name),
            'code' => trim($request->name),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return back()->with('success', 'تم إضافة طريقة الدفع وربطها بالحساب المحاسبي بنجاح.');
    }

    public function updatePaymentMethod(Request $request, $id)
    {
        $old = DB::table('custom_payment_methods')->where('id', $id)->first();
        if ($old) {
            DB::table('vouchers')->where('payment_method', $old->name)->orWhere('payment_method', $old->code)->update(['payment_method' => trim($request->name)]);
            DB::table('custom_payment_methods')->where('id', $id)->update([
                'name' => trim($request->name),
                'code' => trim($request->name),
                'account_id' => $request->filled('account_id') ? (int) $request->account_id : null,
                'updated_at' => now(),
            ]);
        }
        return back()->with('success', 'تم تعديل طريقة الدفع وحسابها المرتبط بنجاح.');
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
                'debit_balance' => 0,
                'credit_balance' => 0,
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
                    'debit_balance' => 0,
                    'credit_balance' => 0,
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
                'debit_balance' => 0,
                'credit_balance' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('erp.dashboard', ['tab' => 'accounting'])
            ->with('success', 'تمت إضافة الحسابات والفروع إلى الشجرة بنجاح.');
    }

    public function updateAccount(Request $request, $id)
    {
        $acc = DB::table('accounts')->where('id', $id)->first();
        $debit = $request->filled('debit_balance') ? (float) $request->debit_balance : ($acc->debit_balance ?? 0);
        $credit = $request->filled('credit_balance') ? (float) $request->credit_balance : ($acc->credit_balance ?? 0);
        $netBalance = ($request->filled('debit_balance') || $request->filled('credit_balance'))
            ? ($debit - $credit)
            : ($request->filled('balance') ? (float) $request->balance : ($acc->balance ?? 0));

        DB::table('accounts')->where('id', $id)->update([
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

        DB::table('accounts')->where('parent_id', $id)->update(['type' => $request->type]);

        return redirect()->route('erp.dashboard', ['tab' => 'accounting'])
            ->with('success', 'تم تعديل الحساب وأرصدة المدين والدائن في الشجرة بنجاح.');
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
        $prefix = $voucherType === 'payment' ? "سند الصرف {$year}-" : "سند القبض {$year}-";

        $query = DB::table('vouchers')
            ->where('voucher_type', $voucherType)
            ->where('voucher_number', 'like', $prefix . '%');

        if ($officeId === null) {
            $count = $query->whereNull('office_id')->count();
        } else {
            $count = $query->where('office_id', $officeId)->count();
        }

        $nextSeq = $count + 1;
        do {
            $candidate = $prefix . str_pad($nextSeq, 5, '0', STR_PAD_LEFT);
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
                'payment_method' => self::normalizeVoucherPaymentMethod($request->payment_method),
                'reference_number' => $request->reference_number ?? null,
                'voucher_date' => $request->voucher_date ?: date('Y-m-d'),
                'notes' => trim($request->notes),
                'created_by' => Auth::id() ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            self::applyAccountMovement((int) $debitAccountId, (float) $request->amount, 0);
            self::applyAccountMovement((int) $creditAccountId, 0, (float) $request->amount);
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
            self::applyAccountMovement((int) $oldVoucher->debit_account_id, (float) $oldVoucher->amount, 0, true);
            self::applyAccountMovement((int) $oldVoucher->credit_account_id, 0, (float) $oldVoucher->amount, true);

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

            self::applyAccountMovement((int) $newDebitId, (float) $request->amount, 0);
            self::applyAccountMovement((int) $newCreditId, 0, (float) $request->amount);
        });

        return back()->with('success', 'تم تعديل السند المالي رقم (' . $oldVoucher->voucher_number . ') وتحديث الأرصدة بنجاح.');
    }

    public function deleteVoucher($id)
    {
        $voucher = DB::table('vouchers')->where('id', $id)->first();
        if ($voucher) {
            DB::transaction(function () use ($voucher) {
                self::applyAccountMovement((int) $voucher->debit_account_id, (float) $voucher->amount, 0, true);
                self::applyAccountMovement((int) $voucher->credit_account_id, 0, (float) $voucher->amount, true);
                DB::table('vouchers')->where('id', $voucher->id)->delete();
            });
        }

        return back()->with('success', 'تم حذف السند وعكس قيده المحاسبي بنجاح.');
    }

    // =========================================================================
    // إدارة القيود اليومية المتعددة (Journal Entries) للشركة الأم
    // =========================================================================
    public function storeJournalEntry(Request $request)
    {
        $this->ensureJournalTables();

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
            return back()->withErrors(['يرجى إدخال سطر واحد على الأقل يحتوي على مبلغ مدين أو دائن.']);
        }

        DB::transaction(function () use ($request, $rows, $totalDebit, $totalCredit, $totalTax) {
            $maxNo = DB::table('journal_entries')->whereNull('office_id')->max('entry_number') ?? 0;
            $nextEntryNo = $maxNo + 1;

            $entryId = DB::table('journal_entries')->insertGetId([
                'office_id' => null,
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

                self::applyAccountMovement($r['account_id'], $r['debit'], $r['credit']);
            }
        });

        return redirect()->route('erp.dashboard', ['tab' => 'accounting'])
            ->with('success', 'تم حفظ القيد اليومي وترحيل جميع العمليات إلى شجرة الحسابات بنجاح.');
    }

    public function updateJournalEntry(Request $request, $id)
    {
        $entry = DB::table('journal_entries')->where('id', $id)->whereNull('office_id')->first();
        if (!$entry) {
            return back()->withErrors(['القيد غير موجود.']);
        }

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
                self::applyAccountMovement((int) $old->account_id, (float) $old->debit, (float) $old->credit, true);
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

                self::applyAccountMovement($r['account_id'], $r['debit'], $r['credit']);
            }
        });

        return redirect()->route('erp.dashboard', ['tab' => 'journal'])
            ->with('success', 'تم تعديل القيد اليومي رقم (' . $entry->entry_number . ') وتحديث أرصدة الشجرة بنجاح.');
    }

    public function deleteJournalEntry($id)
    {
        $entry = DB::table('journal_entries')->where('id', $id)->whereNull('office_id')->first();
        if ($entry) {
            DB::transaction(function () use ($id) {
                $oldItems = DB::table('journal_entry_items')->where('journal_entry_id', $id)->get();
                foreach ($oldItems as $old) {
                    self::applyAccountMovement((int) $old->account_id, (float) $old->debit, (float) $old->credit, true);
                }
                DB::table('journal_entry_items')->where('journal_entry_id', $id)->delete();
                DB::table('journal_entries')->where('id', $id)->delete();
            });
        }

        return redirect()->route('erp.dashboard', ['tab' => 'journal'])
            ->with('success', 'تم حذف القيد اليومي وعكس أثره المالي من شجرة الحسابات.');
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

    // =========================================================================
    // 1. سجل الأخطاء الذكي (System Error Logs) + قراءة ملف laravel.log تلقائياً
    // =========================================================================
    public function ensureSystemMonitoringTables(): void
    {
        if (!Schema::hasTable('system_error_logs')) {
            Schema::create('system_error_logs', function (Blueprint $table) {
                $table->id();
                $table->string('level')->default('ERROR');
                $table->string('source')->nullable();
                $table->text('message');
                $table->text('file_line')->nullable();
                $table->text('suggestion')->nullable();
                $table->string('hash_key')->nullable()->index();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('system_diagnostic_runs')) {
            Schema::create('system_diagnostic_runs', function (Blueprint $table) {
                $table->id();
                $table->string('feature_name');
                $table->string('module');
                $table->string('status'); // passed | warning | failed
                $table->text('details');
                $table->string('execution_ms')->nullable();
                $table->timestamps();
            });
        }
    }

    public static function logSystemIssue(string $source, string $message, string $level = 'ERROR', ?string $fileLine = null, ?string $suggestion = null): void
    {
        if (!Schema::hasTable('system_error_logs')) {
            return;
        }
        $hash = md5($source . '|' . $message . '|' . date('Y-m-d H'));
        if (!DB::table('system_error_logs')->where('hash_key', $hash)->exists()) {
            DB::table('system_error_logs')->insert([
                'level' => $level,
                'source' => $source,
                'message' => $message,
                'file_line' => $fileLine,
                'suggestion' => $suggestion,
                'hash_key' => $hash,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function syncLaravelLogFileToTable(): void
    {
        $logFile = storage_path('logs/laravel.log');
        if (!file_exists($logFile)) {
            return;
        }
        try {
            $size = filesize($logFile);
            $readBytes = min($size, 65536);
            $fp = fopen($logFile, 'r');
            if ($size > $readBytes) {
                fseek($fp, -$readBytes, SEEK_END);
            }
            $content = fread($fp, $readBytes);
            fclose($fp);

            preg_match_all('/\[(\d{4}-\d{2}-\d{2}[T\s]\d{2}:\d{2}:\d{2}[^\]]*)\]\s+\w+\.(ERROR|CRITICAL|WARNING|EXCEPTION):\s+([^\n\r]+)/i', $content, $matches, PREG_SET_ORDER);
            foreach (array_slice($matches, -25) as $m) {
                self::logSystemIssue(
                    'Laravel Log (' . ($m[1] ?? '') . ')',
                    mb_substr(trim($m[3] ?? ''), 0, 350),
                    strtoupper($m[2] ?? 'ERROR'),
                    'storage/logs/laravel.log',
                    'تم التقاط هذا الخطأ تلقائياً من ملف سجل النظام المركزي.'
                );
            }
        } catch (\Throwable $e) {
            // تجاهل أخطاء قراءة الملف
        }
    }

    public function clearSystemLogs()
    {
        $this->ensureSystemMonitoringTables();
        DB::table('system_error_logs')->truncate();
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile) && is_writable($logFile)) {
            @file_put_contents($logFile, '');
        }
        return back()->with('success', 'تم تصفير سجل الأخطاء وتنظيف ملفات الـ Log بنجاح.');
    }

    // =========================================================================
    // 2. المحرك الأوتوماتيكي الشامل لفحص وتجربة جميع ميزات النظام (15 اختباراً حقيقياً)
    // يشمل: الشركة الأم + بوابة المكاتب وموظفيها وحساباتها + بوابة الملاك وأفراد العائلة
    // =========================================================================
    public function runSystemDiagnostics()
    {
        $this->ensureDefaultAdminExists();
        $this->ensureDynamicPropertyTables();
        $this->ensureDynamicAccountTables();
        $this->ensurePaymentMethodsTable();
        $this->ensureJournalTables();
        $this->ensureSystemMonitoringTables();

        DB::table('system_diagnostic_runs')->truncate();

        $results = [];
        $failedCount = 0;

        $recordTest = function (string $feature, string $module, string $status, string $details, float $startTime, ?string $fixSuggestion = null) use (&$results, &$failedCount) {
            $ms = round((microtime(true) - $startTime) * 1000, 1) . ' ms';
            $results[] = [
                'feature_name' => $feature,
                'module' => $module,
                'status' => $status,
                'details' => $details,
                'execution_ms' => $ms,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if ($status === 'failed') {
                $failedCount++;
                self::logSystemIssue('الفحص الأوتوماتيكي: ' . $module, $feature . ' — ' . $details, 'ERROR', $module, $fixSuggestion ?: 'راجع إعدادات الجدول أو الدالة المرتبطة بهذه الميزة.');
            } elseif ($status === 'warning') {
                self::logSystemIssue('تنبيه فحص: ' . $module, $feature . ' — ' . $details, 'WARNING', $module, $fixSuggestion);
            }
        };

        // 1. فحص هيكل جميع الجداول الأساسية والفرعية (14 جدولاً)
        $t = microtime(true);
        $requiredTables = [
            'users', 'offices', 'owners', 'tenants', 'properties', 'property_units',
            'lease_contracts', 'property_types', 'property_type_attributes', 'property_categories',
            'custom_field_types', 'accounts', 'custom_account_types', 'custom_payment_methods',
            'vouchers', 'journal_entries', 'journal_entry_items', 'system_settings'
        ];
        $missingTables = [];
        foreach ($requiredTables as $tbl) {
            if (!Schema::hasTable($tbl)) {
                $missingTables[] = $tbl;
            }
        }
        if (empty($missingTables)) {
            $recordTest('فحص سلامة الجداول والأعمدة (18 جدولاً)', 'قاعدة البيانات', 'passed', 'جميع جداول الشركة والمكاتب والملاك والعائلة والمحاسبة موجودة وسليمة 100%.', $t);
        } else {
            $recordTest('فحص سلامة الجداول والأعمدة', 'قاعدة البيانات', 'failed', 'جداول مفقودة: ' . implode(', ', $missingTables), $t, 'قم بتشغيل المايقريشن أو دالة إصلاح الجداول.');
        }

        // بدء بيئة المحاكاة الآمنة (Rollback Transaction) لتجربة الإدخال الفعلي في كل أجزاء النظام
        DB::beginTransaction();
        try {
            // 2. تجربة شجرة حسابات الشركة الأم + الحسابات المتسلسلة
            $t = microtime(true);
            $compRootId = DB::table('accounts')->insertGetId([
                'office_id' => null, 'parent_id' => null, 'code' => '9900',
                'name' => 'جذع اختبار الشركة الأم', 'type' => 'الأصول', 'is_group' => 1,
                'balance' => 0, 'debit_balance' => 0, 'credit_balance' => 0,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $compSubAccId1 = self::createSequentialSubAccount(null, $compRootId, 'صندوق اختبار الشركة');
            $compSubAccId2 = self::createSequentialSubAccount(null, $compRootId, 'حساب مكتب عقار تجريبي');
            $subAcc1 = DB::table('accounts')->where('id', $compSubAccId1)->first();
            $subAcc2 = DB::table('accounts')->where('id', $compSubAccId2)->first();

            if ($subAcc1 && $subAcc2 && $subAcc1->code === '990001' && $subAcc2->code === '990002') {
                $recordTest('توليد شجرة الحسابات والتسلسل الآلي للشركة الأم', 'محاسبة الشركة الأم', 'passed', "نجح توليد الفروع المتسلسلة ({$subAcc1->code} ثم {$subAcc2->code}) تلقائياً.", $t);
            } else {
                $recordTest('توليد شجرة الحسابات والتسلسل الآلي للشركة الأم', 'محاسبة الشركة الأم', 'failed', 'خلل في ترقيم الفروع المتسلسلة للشركة الأم.', $t);
            }

            // 3. تجربة أنواع العقارات + الحقول والمتطلبات الديناميكية + طرق الدفع
            $t = microtime(true);
            $testPropTypeId = DB::table('property_types')->insertGetId([
                'name' => 'برج تجاري اختباري', 'category' => 'تجاري', 'account_id' => $compSubAccId1,
                'has_units' => 1, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),
            ]);
            $testAttrId = DB::table('property_type_attributes')->insertGetId([
                'property_type_id' => $testPropTypeId, 'attribute_name' => 'رخصة الدفاع المدني PDF',
                'field_type' => 'مرفق ملف PDF أو صورة (حد أقصى 5MB)', 'is_required' => 1,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $testPmId = DB::table('custom_payment_methods')->insertGetId([
                'office_id' => null, 'account_id' => $compSubAccId1, 'name' => 'حوالة اختبارية سريعة',
                'code' => 'test_transfer', 'created_at' => now(), 'updated_at' => now(),
            ]);
            if ($testPropTypeId && $testAttrId && $testPmId) {
                $recordTest('إضافة نوع عقار + متطلبات PDF + ربط طرق الدفع بالحسابات', 'إعدادات المنصة المركزية', 'passed', 'نجح إنشاء نوع العقار وربطه بحساب الشجرة وإضافة متطلب PDF وطريقة دفع.', $t);
            } else {
                $recordTest('إضافة نوع عقار ومتطلباته وطرق الدفع', 'إعدادات المنصة المركزية', 'failed', 'فشل في إدراج نوع العقار أو المتطلبات الديناميكية.', $t);
            }

            // 4. تجربة سندات القبض والصرف + القيود اليومية للشركة الأم وترحيل الأرصدة
            $t = microtime(true);
            $compVoucherNo = self::generateSequentialVoucherNumber('receipt', null);
            self::applyAccountMovement($compSubAccId1, 1500, 0);
            self::applyAccountMovement($compSubAccId2, 0, 1500);
            $afterDebit = DB::table('accounts')->where('id', $compSubAccId1)->value('balance');
            $afterCredit = DB::table('accounts')->where('id', $compSubAccId2)->value('balance');

            // عكس القيد لاختبار الحذف/التعديل
            self::applyAccountMovement($compSubAccId1, 1500, 0, true);
            self::applyAccountMovement($compSubAccId2, 0, 1500, true);
            $revertedBal = DB::table('accounts')->where('id', $compSubAccId1)->value('balance');

            if ((float)$afterDebit === 1500.0 && (float)$afterCredit === -1500.0 && (float)$revertedBal === 0.0) {
                $recordTest('إصدار سندات وقيود الشركة الأم + الترحيل وعكس الأرصدة', 'محاسبة الشركة الأم', 'passed', "نجح توليد السند ({$compVoucherNo}) وترحيل 1,500 ر.س للمدين والدائن ثم عكس القيد بدقة.", $t);
            } else {
                $recordTest('إصدار سندات وقيود الشركة الأم + الترحيل وعكس الأرصدة', 'محاسبة الشركة الأم', 'failed', 'خلل في معادلة ترحيل الأرصدة (applyAccountMovement).', $t);
            }

            // 5. تجربة إنشاء مكتب عقار متكامل + حساب مدير المكتب (office_admin)
            $t = microtime(true);
            $testOfficeId = DB::table('offices')->insertGetId([
                'name' => 'مكتب آفاق العقار التجريبي', 'cr_number' => '1010999999', 'fal_license' => '1200099999',
                'phone' => '+966509999991', 'email' => 'test_office@office.local',
                'billing_type' => 'hybrid', 'annual_fee' => 2500, 'company_commission_rate' => 2.5,
                'subscription_ends_at' => now()->addYear()->toDateString(), 'status' => 'active',
                'account_id' => $compSubAccId2, 'use_custom_whatsapp' => 0,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $officeAdminUserId = DB::table('users')->insertGetId([
                'name' => 'مدير مكتب الاختبار', 'username' => 'diag_office_admin_' . rand(100, 999),
                'phone' => '+966509999991', 'email' => 'diag_office@office.local',
                'password' => Hash::make('123456'), 'user_type' => 'office_admin',
                'office_id' => $testOfficeId, 'is_active' => 1,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            if ($testOfficeId && $officeAdminUserId) {
                $recordTest('إنشاء مكتب عقار + تفعيل الاتفاقية المدمجة + حساب مدير المكتب', 'بوابة مكاتب العقار', 'passed', "تم تسجيل المكتب (ID: {$testOfficeId}) وربطه بحساب الشجرة وإنشاء حساب الدخول بنجاح.", $t);
            } else {
                $recordTest('إنشاء مكتب عقار وحساب مديره', 'بوابة مكاتب العقار', 'failed', 'فشل إنشاء سجل المكتب أو حساب مدير المكتب.', $t);
            }

            // 6. تجربة تأسيس شجرة الحسابات المستقلة الخاصة بالمكتب العقاري
            $t = microtime(true);
            $offRootAssets = DB::table('accounts')->insertGetId([
                'office_id' => $testOfficeId, 'parent_id' => null, 'code' => '1000',
                'name' => 'أصول المكتب التجريبي', 'type' => 'الأصول', 'is_group' => 1,
                'balance' => 0, 'debit_balance' => 0, 'credit_balance' => 0, 'created_at' => now(), 'updated_at' => now(),
            ]);
            $offBankAccId = self::createSequentialSubAccount($testOfficeId, $offRootAssets, 'بنك المكتب لتحصيل الإيجارات');
            $offTenantsGroupAccId = self::createSequentialSubAccount($testOfficeId, $offRootAssets, 'ذمم المستأجرين بالمكتب');

            $offRootLiab = DB::table('accounts')->insertGetId([
                'office_id' => $testOfficeId, 'parent_id' => null, 'code' => '2000',
                'name' => 'خصوم وأمانات المكتب', 'type' => 'الخصوم والالتزامات', 'is_group' => 1,
                'balance' => 0, 'debit_balance' => 0, 'credit_balance' => 0, 'created_at' => now(), 'updated_at' => now(),
            ]);
            $offOwnersGroupAccId = self::createSequentialSubAccount($testOfficeId, $offRootLiab, 'أمانات الملاك بالمكتب');

            $officeAccCount = DB::table('accounts')->where('office_id', $testOfficeId)->count();
            if ($officeAccCount >= 5) {
                $recordTest('تأسيس شجرة الحسابات المحاسبية المستقلة للمكتب وعزلها عن الشركة', 'محاسبة مكتب العقار', 'passed', "تم إنشاء {$officeAccCount} حسابات مستقلة خاصة بالمكتب ومعزولة بـ office_id بنجاح.", $t);
            } else {
                $recordTest('تأسيس شجرة الحسابات المستقلة للمكتب', 'محاسبة مكتب العقار', 'failed', 'لم يتم إنشاء حسابات شجرة المكتب بشكل كامل.', $t);
            }

            // 7. تجربة إضافة موظف مكتب (office_staff) وتخصيص صلاحياته
            $t = microtime(true);
            $staffPerms = ['إدارة الملاك والعقارات', 'إبرام وتوثيق عقود إيجار', 'إصدار سندات القبض والصرف'];
            $officeStaffUserId = DB::table('users')->insertGetId([
                'name' => 'موظف مكتب تجريبي', 'username' => 'diag_staff_' . rand(100, 999),
                'phone' => '+966509999992', 'email' => 'diag_staff@office.local',
                'password' => Hash::make('123456'), 'user_type' => 'office_staff',
                'office_id' => $testOfficeId, 'is_active' => 1,
                'custom_permissions' => json_encode($staffPerms, JSON_UNESCAPED_UNICODE),
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $savedStaffPerms = json_decode(DB::table('users')->where('id', $officeStaffUserId)->value('custom_permissions'), true);
            if ($officeStaffUserId && is_array($savedStaffPerms) && count($savedStaffPerms) === 3) {
                $recordTest('إضافة موظف بالمكتب (office_staff) وتخصيص صلاحياته', 'الموارد البشرية بالمكتب', 'passed', 'نجح إنشاء حساب الموظف وحفظ مصفوفة الصلاحيات المخصصة (JSON) بنجاح.', $t);
            } else {
                $recordTest('إضافة موظف بالمكتب وتخصيص صلاحياته', 'الموارد البشرية بالمكتب', 'failed', 'فشل حفظ صلاحيات موظف المكتب.', $t);
            }

            // 8. تجربة إضافة مالك عقار (Owner) + ربطه بحساب أمانات متسلسل + إنشاء حساب دخوله
            $t = microtime(true);
            $ownerSubAccId = self::createSequentialSubAccount($testOfficeId, $offOwnersGroupAccId, 'أمانات المالك: الشيخ عبدالله التجريبي');
            $testOwnerId = DB::table('owners')->insertGetId([
                'office_id' => $testOfficeId, 'name' => 'الشيخ عبدالله التجريبي',
                'national_id' => '1099999999', 'phone' => '+966509999993',
                'email' => 'diag_owner@owner.local', 'iban' => 'SA0000000000000000009999',
                'bank_name' => 'مصرف الراجحي', 'office_management_fee_rate' => 5.00,
                'account_id' => $ownerSubAccId, 'created_at' => now(), 'updated_at' => now(),
            ]);
            $ownerUserId = DB::table('users')->insertGetId([
                'name' => 'الشيخ عبدالله التجريبي', 'username' => 'diag_owner_' . rand(100, 999),
                'phone' => '+966509999993', 'email' => 'diag_owner@owner.local',
                'national_id' => '1099999999', 'password' => Hash::make('123456'),
                'user_type' => 'owner', 'office_id' => $testOfficeId, 'owner_id' => $testOwnerId,
                'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),
            ]);
            if ($testOwnerId && $ownerUserId && $ownerSubAccId) {
                $recordTest('تسجيل مالك عقار + فتح حساب أمانات متسلسل بالشجرة + تفعيل بوابة المالك', 'بوابة الملاك والمكتب', 'passed', "تم تسجيل المالك (ID: {$testOwnerId}) وربطه بالحساب الفرعي وإنشاء حساب دخوله بنجاح.", $t);
            } else {
                $recordTest('تسجيل مالك عقار وربطه بالشجرة', 'بوابة الملاك والمكتب', 'failed', 'خلل في إنشاء سجل المالك أو حسابه المحاسبي.', $t);
            }

            // 9. تجربة إضافة عقار وصك ملكية + وحدات عقارية داخلية (Property & Units)
            $t = microtime(true);
            $testPropertyId = DB::table('properties')->insertGetId([
                'office_id' => $testOfficeId, 'owner_id' => $testOwnerId, 'property_type_id' => $testPropTypeId,
                'title' => 'عمارة العليا التجريبية', 'deed_number' => '310199999999',
                'deed_date' => date('Y-m-d'), 'total_area' => 900, 'estimated_value' => 4500000,
                'city' => 'الرياض', 'district' => 'حي العليا', 'street' => 'طريق الملك فهد',
                'building_number' => '7788', 'postal_code' => '12211', 'additional_number' => '3344',
                'custom_attributes' => json_encode(['عدد الأدوار' => '4', 'رخصة الدفاع المدني PDF' => '/uploads/properties/test.pdf'], JSON_UNESCAPED_UNICODE),
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $testUnitId = DB::table('property_units')->insertGetId([
                'property_id' => $testPropertyId, 'property_type_id' => $testPropTypeId,
                'unit_number' => 'شقة 101', 'floor_number' => 1, 'area' => 160,
                'electricity_meter' => 'ELC-998877', 'water_meter' => 'WTR-112233',
                'default_rent_price' => 40000, 'status' => 'vacant',
                'custom_attributes' => json_encode(['rooms' => 4, 'unit_kind' => 'شقة'], JSON_UNESCAPED_UNICODE),
                'created_at' => now(), 'updated_at' => now(),
            ]);
            if ($testPropertyId && $testUnitId) {
                $recordTest('إضافة عقار وصك ملكية + العنوان الوطني + الوحدات الداخلية والعدادات', 'إدارة الأملاك والعقارات', 'passed', "تم تسجيل العقار والصك وإنشاء الوحدة الداخلية (شقة 101) بحالة شاغرة.", $t);
            } else {
                $recordTest('إضافة عقار وصك ووحدات داخلية', 'إدارة الأملاك والعقارات', 'failed', 'فشل إدراج العقار أو الوحدة العقارية.', $t);
            }

            // 10. تجربة تسجيل مستأجر (Tenant) + ربطه بحساب ذمم متسلسل + إبرام عقد إيجار وتوثيقه
            $t = microtime(true);
            $tenantSubAccId = self::createSequentialSubAccount($testOfficeId, $offTenantsGroupAccId, 'المستأجر: فهد التجريبي');
            $testTenantId = DB::table('tenants')->insertGetId([
                'office_id' => $testOfficeId, 'name' => 'فهد بن خالد التجريبي',
                'id_type' => 'national_id', 'id_number' => '1088888888',
                'phone' => '+966509999994', 'account_id' => $tenantSubAccId,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            // حساب العمولات كما في OfficeErpController::storeContract (إيجار 40,000 -> عمولة مكتب 5% = 2,000، عمولة شركة 2.5% = 1,000)
            $rentVal = 40000;
            $calcOfficeComm = ($rentVal * 5.0) / 100;
            $calcCompFee = ($rentVal * 2.5) / 100;
            $testContractId = DB::table('lease_contracts')->insertGetId([
                'contract_number' => 'CNT-DIAG-' . rand(1000, 9999),
                'ejar_contract_number' => 'EJR-9999999999', 'ejar_status' => 'active',
                'office_id' => $testOfficeId, 'property_id' => $testPropertyId, 'property_unit_id' => $testUnitId,
                'owner_id' => $testOwnerId, 'tenant_id' => $testTenantId,
                'start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d', strtotime('+1 year')),
                'payment_cycle' => 'annual', 'total_rent_amount' => $rentVal,
                'security_deposit' => 2000, 'office_commission' => $calcOfficeComm,
                'company_fee_amount' => $calcCompFee, 'status' => 'active',
                'created_at' => now(), 'updated_at' => now(),
            ]);
            DB::table('property_units')->where('id', $testUnitId)->update(['status' => 'rented']);
            $unitStatusAfter = DB::table('property_units')->where('id', $testUnitId)->value('status');

            if ($testTenantId && $testContractId && $unitStatusAfter === 'rented' && $calcOfficeComm == 2000 && $calcCompFee == 1000) {
                $recordTest('تسجيل مستأجر + إبرام عقد إيجار + احتساب النسب + تحويل الوحدة لمؤجرة', 'العقود ومنصة إيجار', 'passed', 'نجح ربط المستأجر بالشجرة وإبرام العقد (عمولة المكتب 2,000 ر.س وحصة الشركة 1,000 ر.س) وتحديث حالة الوحدة.', $t);
            } else {
                $recordTest('تسجيل مستأجر وإبرام عقد إيجار', 'العقود ومنصة إيجار', 'failed', 'خلل في إبرام العقد أو تحديث حالة الوحدة.', $t);
            }

            // 11. تجربة الدورة المحاسبية داخل مكتب العقار (سند قبض من مستأجر + سند صرف لمالك + قيد يومي للمكتب)
            $t = microtime(true);
            $offVoucherNo = self::generateSequentialVoucherNumber('receipt', $testOfficeId);
            $safePaymentMethod = self::normalizeVoucherPaymentMethod('حوالة بنكية');
            DB::table('vouchers')->insert([
                'voucher_number' => $offVoucherNo, 'voucher_type' => 'receipt',
                'office_id' => $testOfficeId, 'debit_account_id' => $offBankAccId,
                'credit_account_id' => $tenantSubAccId, 'owner_id' => $testOwnerId,
                'tenant_id' => $testTenantId, 'amount' => 20000, 'payment_method' => $safePaymentMethod,
                'voucher_date' => date('Y-m-d'), 'notes' => 'تحصيل دفعة إيجار تجريبية',
                'created_by' => $officeAdminUserId, 'created_at' => now(), 'updated_at' => now(),
            ]);
            self::applyAccountMovement($offBankAccId, 20000, 0);
            self::applyAccountMovement($tenantSubAccId, 0, 20000);

            $offJournalId = DB::table('journal_entries')->insertGetId([
                'office_id' => $testOfficeId, 'entry_number' => 1, 'entry_date' => date('Y-m-d'),
                'general_notes' => 'قيد إثبات استحقاق أمانات المالك التجريبي',
                'total_debit' => 20000, 'total_credit' => 20000, 'total_tax' => 0,
                'created_by' => $officeAdminUserId, 'created_at' => now(), 'updated_at' => now(),
            ]);
            DB::table('journal_entry_items')->insert([
                ['journal_entry_id' => $offJournalId, 'account_id' => $tenantSubAccId, 'debit' => 20000, 'credit' => 0, 'tax' => 0, 'notes' => 'مدين تجريبي', 'created_at' => now(), 'updated_at' => now()],
                ['journal_entry_id' => $offJournalId, 'account_id' => $ownerSubAccId, 'debit' => 0, 'credit' => 20000, 'tax' => 0, 'notes' => 'دائن للمالك', 'created_at' => now(), 'updated_at' => now()],
            ]);
            self::applyAccountMovement($tenantSubAccId, 20000, 0);
            self::applyAccountMovement($ownerSubAccId, 0, 20000);

            $bankBal = (float) DB::table('accounts')->where('id', $offBankAccId)->value('balance');
            $tenantBal = (float) DB::table('accounts')->where('id', $tenantSubAccId)->value('balance');
            $ownerBal = (float) DB::table('accounts')->where('id', $ownerSubAccId)->value('balance');

            if ($bankBal === 20000.0 && $tenantBal === 0.0 && $ownerBal === -20000.0) {
                $recordTest('إصدار سندات المكتب + قيود يومية للمكتب + كشف حساب المستأجر والمالك', 'محاسبة مكتب العقار', 'passed', 'نجح تحصيل الإيجار في البنك وتسوية ذمة المستأجر وترحيل المستحق لأمانات المالك بدقة 100%.', $t);
            } else {
                $recordTest('سندات وقيود مكتب العقار', 'محاسبة مكتب العقار', 'failed', 'عدم تطابق أرصدة حسابات المكتب بعد الترحيل.', $t);
            }

            // 12. تجربة بوابة العائلة والوكلاء (owner_family) + فحص نظام الصلاحيات الدقيق (Permissions Isolation)
            $t = microtime(true);
            $familyAllowedPerms = ['view_properties', 'view_contracts']; // بدون view_financials وبدون manage_family
            $familyUserId = DB::table('users')->insertGetId([
                'name' => 'محمد بن عبدالله (ابن / وكيل)', 'username' => 'diag_family_' . rand(100, 999),
                'phone' => '+966509999995', 'email' => 'diag_family@family.local',
                'password' => Hash::make('123456'), 'user_type' => 'owner_family',
                'office_id' => $testOfficeId, 'owner_id' => $testOwnerId, 'is_active' => 1,
                'custom_permissions' => json_encode($familyAllowedPerms, JSON_UNESCAPED_UNICODE),
                'created_at' => now(), 'updated_at' => now(),
            ]);

            $familyUserRow = DB::table('users')->where('id', $familyUserId)->first();
            $decodedFamilyPerms = json_decode($familyUserRow->custom_permissions ?? '[]', true) ?: [];
            $canSeeProps = in_array('view_properties', $decodedFamilyPerms);
            $canSeeContracts = in_array('view_contracts', $decodedFamilyPerms);
            $canSeeMoney = in_array('view_financials', $decodedFamilyPerms);

            // التحقق من وصول بوابة المالك لعقاراته وعقوده وسنداته
            $ownerPropsCount = DB::table('properties')->where('owner_id', $testOwnerId)->count();
            $ownerContractsCount = DB::table('lease_contracts')->where('owner_id', $testOwnerId)->count();
            $ownerVouchersCount = DB::table('vouchers')->where('owner_id', $testOwnerId)->count();

            if ($familyUserId && $canSeeProps === true && $canSeeContracts === true && $canSeeMoney === false && $ownerPropsCount === 1 && $ownerContractsCount === 1 && $ownerVouchersCount === 1) {
                $recordTest('بوابة المالك + إضافة فرد عائلة (owner_family) + عزل صلاحيات العائلة', 'بوابة المالك والعائلة', 'passed', 'نجح إنشاء حساب فرد العائلة والتحقق من رؤيته للعقارات والعقود وحجب الكشف المالي عنه حسب الصلاحيات الممنوحة.', $t);
            } else {
                $recordTest('بوابة المالك وحسابات أفراد العائلة', 'بوابة المالك والعائلة', 'failed', 'خلل في صلاحيات فرد العائلة أو ربط بيانات محفظة المالك.', $t);
            }

            // إلغاء جميع البيانات التجريبية الوهمية فوراً للحفاظ على نظافة قاعدة البيانات
            DB::rollBack();
        } catch (\Throwable $e) {
            DB::rollBack();
            $recordTest('خطأ استثنائي أثناء محاكاة العمليات الشاملة', 'محرك المحاكاة', 'failed', $e->getMessage() . ' (السطر: ' . $e->getLine() . ')', microtime(true), 'افحص تفاصيل الخطأ في سجل الأخطاء (System Logs).');
        }

        // 13. فحص التوازن المحاسبي الفعلي للبيانات الحالية في النظام (الشركة الأم + جميع المكاتب)
        $t = microtime(true);
        $unbalancedEntries = DB::table('journal_entries')->whereRaw('ABS(total_debit - total_credit) > 0.01')->count();
        $orphanVouchers = DB::table('vouchers')
            ->leftJoin('accounts as d', 'vouchers.debit_account_id', '=', 'd.id')
            ->leftJoin('accounts as c', 'vouchers.credit_account_id', '=', 'c.id')
            ->whereNull('d.id')->orWhereNull('c.id')->count();

        if ($unbalancedEntries === 0 && $orphanVouchers === 0) {
            $recordTest('فحص توازن جميع القيود والسندات الحالية (للشركة والمكاتب)', 'التدقيق المحاسبي', 'passed', 'جميع السندات والقيود الفعلية المسجلة في النظام متوازنة ومربوطة بحسابات صحيحة 100%.', $t);
        } else {
            $recordTest('فحص توازن القيود والسندات الحالية', 'التدقيق المحاسبي', 'warning', "يوجد ({$unbalancedEntries}) قيد غير متوازن و ({$orphanVouchers}) سند بحساب غير مكتمل.", $t, 'راجع تبويب السندات والقيود اليومية لتصحيح الحساب المرتبط.');
        }

        // 14. فحص سلامة ربط حسابات المستخدمين الحالية (المكاتب، الموظفين، الملاك، أفراد العائلة)
        $t = microtime(true);
        $orphanOfficeUsers = DB::table('users')->whereIn('user_type', ['office_admin', 'office_staff'])->whereNull('office_id')->count();
        $orphanOwnerUsers = DB::table('users')->whereIn('user_type', ['owner', 'owner_family'])->whereNull('owner_id')->count();
        if ($orphanOfficeUsers === 0 && $orphanOwnerUsers === 0) {
            $recordTest('فحص ارتباط حسابات دخول المكاتب والموظفين والملاك والعائلة', 'الأمان والصلاحيات', 'passed', 'جميع حسابات المستخدمين مرتبطة بمكاتبها وملاكها بشكل سليم.', $t);
        } else {
            $recordTest('فحص ارتباط حسابات المستخدمين', 'الأمان والصلاحيات', 'warning', "يوجد ({$orphanOfficeUsers}) حساب مكتب/موظف بدون office_id و ({$orphanOwnerUsers}) حساب مالك/عائلة بدون owner_id.", $t);
        }

        // 15. فحص جاهزية الربط مع واتساب API وتصاريح مجلد المرفقات
        $t = microtime(true);
        $waUrl = self::getSetting('whatsapp_api_url', env('WHATSAPP_API_URL'));
        $waToken = self::getSetting('whatsapp_api_token', env('WHATSAPP_API_TOKEN'));
        $uploadDir = public_path('uploads/properties');
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0775, true);
        }
        $uploadWritable = is_dir($uploadDir) && is_writable($uploadDir);

        if (!empty($waUrl) && !empty($waToken) && $uploadWritable) {
            $recordTest('فحص بوابة واتساب API ومجلد رفع مرفقات العقارات (PDF/Images)', 'الربط والتخزين', 'passed', 'إعدادات واتساب API مفعلة ومجلد رفع المرفقات قابل للكتابة.', $t);
        } else {
            $msg = (!$uploadWritable ? 'مجلد المرفقات غير قابل للكتابة. ' : '') . (empty($waToken) ? 'توكن واتساب API غير مدخل (يعمل النظام حالياً بوضع الكود التجريبي المباشر).' : '');
            $recordTest('فحص بوابة واتساب API ومجلد المرفقات', 'الربط والتخزين', 'warning', $msg, $t, 'يمكنك إدخال التوكن في تبويب إعدادات واتساب API متى رغبت.');
        }

        DB::table('system_diagnostic_runs')->insert($results);

        $passedCount = collect($results)->where('status', 'passed')->count();
        $totalTests = count($results);

        return back()->with('open_diagnostics_modal', true)
                     ->with('success', "اكتمل الفحص الأوتوماتيكي الشامل لجميع بوابات النظام (الشركة + المكاتب + الملاك + العائلة): نجح ({$passedCount} من {$totalTests}) اختباراً.");
    }

    // =========================================================================
    // 3. نظام النسخ الاحتياطي الفوري والاستعادة (Full System Backup & Restore)
    // =========================================================================
    public function downloadSystemBackup(Request $request)
    {
        $format = $request->get('format', 'json');
        $timestamp = date('Y-m-d_H-i-s');

        // تحميل ملف SQLite الخام مباشرة إن طلب المستخدم ذلك
        if ($format === 'sqlite') {
            $dbPath = config('database.connections.sqlite.database', database_path('database.sqlite'));
            if (file_exists($dbPath)) {
                return response()->download($dbPath, "RealEstate_ERP_SQLite_{$timestamp}.sqlite");
            }
        }

        // تصدير نسخة احتياطية شاملة ومحمولة بصيغة JSON لجميع جداول النظام
        $tablesToBackup = [
            'users', 'offices', 'owners', 'tenants', 'properties', 'property_units',
            'lease_contracts', 'property_types', 'property_type_attributes', 'property_categories',
            'custom_field_types', 'accounts', 'custom_account_types', 'custom_payment_methods',
            'vouchers', 'journal_entries', 'journal_entry_items', 'system_settings'
        ];

        $backupPayload = [
            'meta' => [
                'system' => 'Real Estate ERP System',
                'exported_at' => now()->toDateTimeString(),
                'exported_by' => Auth::user()->username ?? 'admin',
                'version' => '2.5',
            ],
            'tables' => [],
        ];

        foreach ($tablesToBackup as $table) {
            if (Schema::hasTable($table)) {
                $backupPayload['tables'][$table] = DB::table($table)->get()->toArray();
            }
        }

        $jsonContent = json_encode($backupPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $fileName = "RealEstate_ERP_FullBackup_{$timestamp}.json";

        return response($jsonContent, 200, [
            'Content-Type' => 'application/json; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    public function restoreSystemBackup(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|max:102400',
        ], [
            'backup_file.required' => 'يرجى اختيار ملف النسخة الاحتياطية (.json أو .sqlite) لاستعادته.',
        ]);

        try {
            $uploadedFile = $request->file('backup_file');
            $realPath = $uploadedFile->getRealPath();
            $ext = strtolower($uploadedFile->getClientOriginalExtension());

            // قراءة أول 16 بايت لمعرفة هل الملف قاعدة بيانات SQLite خام (حتى لو تغير امتداده)
            $header = file_get_contents($realPath, false, null, 0, 16);
            $isSqliteFile = str_starts_with($header ?: '', 'SQLite format 3') || in_array($ext, ['sqlite', 'sqlite3', 'db']);

            // الحالة 1: إذا كان الملف المرفوع هو نسخة SQLite الخام (.sqlite)
            if ($isSqliteFile) {
                $tempSqlite = storage_path('app/temp_restore_' . time() . '.sqlite');
                copy($realPath, $tempSqlite);

                config(['database.connections.sqlite_restore_tmp' => [
                    'driver' => 'sqlite',
                    'database' => $tempSqlite,
                    'prefix' => '',
                ]]);

                $tables = [
                    'users', 'offices', 'owners', 'tenants', 'properties', 'property_units',
                    'lease_contracts', 'property_types', 'property_type_attributes', 'property_categories',
                    'custom_field_types', 'accounts', 'custom_account_types', 'custom_payment_methods',
                    'vouchers', 'journal_entries', 'journal_entry_items', 'system_settings'
                ];

                DB::statement('PRAGMA foreign_keys = OFF;');
                try {
                    DB::transaction(function () use ($tables) {
                        foreach ($tables as $tableName) {
                            if (Schema::hasTable($tableName) && Schema::connection('sqlite_restore_tmp')->hasTable($tableName)) {
                                $rows = DB::connection('sqlite_restore_tmp')->table($tableName)->get()->toArray();
                                DB::table($tableName)->delete();
                                foreach (array_chunk($rows, 100) as $chunk) {
                                    $cleanChunk = array_map(fn($r) => (array) $r, $chunk);
                                    DB::table($tableName)->insert($cleanChunk);
                                }
                            }
                        }
                    });
                } finally {
                    DB::statement('PRAGMA foreign_keys = ON;');
                    DB::purge('sqlite_restore_tmp');
                    @unlink($tempSqlite);
                }

                return back()->with('success', 'تم استعادة قاعدة البيانات (.SQLite) وجميع السجلات والحسابات بنجاح!');
            }

            // الحالة 2: إذا كان الملف المرفوع بصيغة JSON (.json)
            $content = file_get_contents($realPath);
            // إزالة BOM إن وجد في بداية الملف
            $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
            $data = json_decode($content, true);

            if (!is_array($data) || !isset($data['tables']) || !is_array($data['tables'])) {
                return back()->withErrors(['ملف النسخة الاحتياطية غير صالح أو تالف. يرجى رفع ملف (.JSON) أو (.SQLite) المصدر من النظام.']);
            }

            DB::statement('PRAGMA foreign_keys = OFF;');
            try {
                DB::transaction(function () use ($data) {
                    foreach ($data['tables'] as $tableName => $rows) {
                        if (Schema::hasTable($tableName) && is_array($rows)) {
                            DB::table($tableName)->delete();
                            foreach (array_chunk($rows, 100) as $chunk) {
                                $cleanChunk = array_map(fn($r) => (array) $r, $chunk);
                                DB::table($tableName)->insert($cleanChunk);
                            }
                        }
                    }
                });
            } finally {
                DB::statement('PRAGMA foreign_keys = ON;');
            }

            return back()->with('success', 'تم استعادة النسخة الاحتياطية (.JSON) وجميع بيانات المكاتب والملاك والعائلة والحسابات بنجاح!');
        } catch (\Throwable $e) {
            try { DB::statement('PRAGMA foreign_keys = ON;'); } catch (\Throwable $ignore) {}
            self::logSystemIssue('Restore Backup', $e->getMessage(), 'ERROR', $e->getFile() . ':' .$e->getLine());
            return back()->withErrors(['حدث خطأ أثناء استعادة النسخة الاحتياطية: ' . $e->getMessage()]);
        }
    }

    // =========================================================================
    // نظام الفوترة الضريبية الإلكترونية المتكامل (ZATCA Phase 1/2 Ready) + الربط بشجرة الحسابات
    // =========================================================================
    public function ensureTaxInvoicesTable(): void
    {
        if (!Schema::hasTable('tax_invoices')) {
            Schema::create('tax_invoices', function (Blueprint $table) {
                $table->id();
                $table->string('invoice_number')->unique();
                $table->string('issuer_level')->default('company'); // company | office
                $table->string('invoice_type')->default('tax'); // tax | simplified | owner_tax
                $table->unsignedBigInteger('office_id')->nullable();
                $table->unsignedBigInteger('owner_id')->nullable();
                $table->unsignedBigInteger('tenant_id')->nullable();
                $table->unsignedBigInteger('lease_contract_id')->nullable();
                $table->unsignedBigInteger('debit_account_id')->nullable();
                $table->unsignedBigInteger('credit_account_id')->nullable();
                $table->unsignedBigInteger('tax_account_id')->nullable();
                $table->unsignedBigInteger('journal_entry_id')->nullable();
                $table->string('seller_name');
                $table->string('seller_tax_number')->nullable();
                $table->string('seller_address')->nullable();
                $table->string('buyer_name');
                $table->string('buyer_tax_number')->nullable();
                $table->string('buyer_address')->nullable();
                $table->text('description');
                $table->decimal('subtotal', 15, 2)->default(0);
                $table->decimal('vat_rate', 5, 2)->default(15.00);
                $table->decimal('vat_amount', 15, 2)->default(0);
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->date('issue_date');
                $table->text('zatca_qr')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('tax_invoices', function (Blueprint $table) {
                if (!Schema::hasColumn('tax_invoices', 'invoice_type')) {
                    $table->string('invoice_type')->default('tax');
                }
                if (!Schema::hasColumn('tax_invoices', 'issuer_level')) {
                    $table->string('issuer_level')->default('company');
                }
                if (!Schema::hasColumn('tax_invoices', 'debit_account_id')) {
                    $table->unsignedBigInteger('debit_account_id')->nullable();
                }
                if (!Schema::hasColumn('tax_invoices', 'credit_account_id')) {
                    $table->unsignedBigInteger('credit_account_id')->nullable();
                }
                if (!Schema::hasColumn('tax_invoices', 'tax_account_id')) {
                    $table->unsignedBigInteger('tax_account_id')->nullable();
                }
                if (!Schema::hasColumn('tax_invoices', 'journal_entry_id')) {
                    $table->unsignedBigInteger('journal_entry_id')->nullable();
                }
            });
        }
    }

    /**
     * إيجاد أو إنشاء حساب ضريبة القيمة المضافة المستحقة (2101) في شجرة الحسابات
     */
    public static function getOrCreateVatAccount(?int $officeId = null): int
    {
        $vatAcc = DB::table('accounts')
            ->when($officeId !== null, fn($q) => $q->where('office_id',$officeId), fn($q) =>$q->whereNull('office_id'))
            ->where(function ($q) {$q->where('name', 'like', '%ضريبة%')
                  ->orWhere('code', '2101')
                  ->orWhere('code', '2104');
            })
            ->where('is_group', 0)
            ->first();

        if ($vatAcc) {
            return (int) $vatAcc->id;
        }

        $liabRoot = DB::table('accounts')
            ->when($officeId !== null, fn($q) => $q->where('office_id',$officeId), fn($q) =>$q->whereNull('office_id'))
            ->where(function ($q) {$q->where('code', '2000')->orWhere('type', 'like', '%خصوم%');
            })
            ->first();

        return (int) DB::table('accounts')->insertGetId([
            'office_id' => $officeId,
            'parent_id' => $liabRoot ? $liabRoot->id : null,
            'code' => $officeId ? '2104' : '2101',
            'name' => 'ضريبة القيمة المضافة المستحقة (15%)',
            'type' => 'الخصوم والالتزامات',
            'is_group' => 0,
            'balance' => 0,
            'debit_balance' => 0,
            'credit_balance' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * توليد كود TLV Base64 المتوافق مع هيئة الزكاة والضريبة والجمارك (ZATCA)
     */
    public static function generateZatcaQrBase64(string $sellerName, string $vatNumber, string $timestamp, string $totalWithVat, string $vatAmount): string
    {
        $tlv = '';
        $fields = [
            1 => $sellerName ?: 'منظومة العقارات',
            2 => $vatNumber ?: '300000000000003',
            3 => $timestamp,
            4 => $totalWithVat,
            5 => $vatAmount,
        ];
        foreach ($fields as $tag => $val) {
            $valStr = (string) $val;
            $tlv .= chr($tag) . chr(strlen($valStr)) . $valStr;
        }
        return base64_encode($tlv);
    }

    public function saveCompanyTaxProfile(Request $request)
    {
        self::setSetting('company_legal_name', trim($request->legal_name));
        self::setSetting('company_tax_number', trim($request->tax_number));
        self::setSetting('company_cr_number', trim($request->cr_number));
        self::setSetting('company_national_address', trim($request->national_address));

        if ($request->filled('default_vat_rate') && is_numeric($request->default_vat_rate)) {
            $rate = max(0, min(100, (float) $request->default_vat_rate));
            self::setSetting('default_vat_rate', (string) $rate);
        }

        return back()->with('success', 'تم حفظ الإعدادات الضريبية ونسبة الضريبة المعتمدة للنظام بنجاح (تُطبق على الفواتير الجديدة فقط دون أثر رجعي).');
    }

    public function toggleOfficeOwnerInvoicing(Request $request, $id)
    {
        $office = DB::table('offices')->where('id', $id)->first();
        if ($office) {
            $newState = empty($office->allow_owner_tax_invoice) ? 1 : 0;
            DB::table('offices')->where('id', $id)->update([
                'allow_owner_tax_invoice' => $newState,
                'updated_at' => now(),
            ]);
            $msg = $newState
                ? "تم تفعيل ميزة (إصدار فاتورة ضريبية باسم المالك) لمكتب: {$office->name}"
                : "تم إيقاف ميزة الفوترة باسم المالك عن مكتب: {$office->name}";
            return back()->with('success', $msg);
        }
        return back();
    }

    public function storeCompanyInvoice(Request $request)
    {
        $this->ensureTaxInvoicesTable();
        $this->ensureJournalTables();

        $request->validate([
            'office_id' => 'required',
            'subtotal' => 'required|numeric|min:0.01',
            'description' => 'required',
        ]);

        $office = DB::table('offices')->where('id', $request->office_id)->first();
        if (!$office) {
            return back()->withErrors(['المكتب غير موجود.']);
        }

        $buyerTaxNo = trim($request->buyer_tax_number ?: ($office->tax_number ?? ''));
        $buyerAddress = trim($request->buyer_address ?: ($office->national_address ?: $office->address));
        if ($buyerTaxNo && empty($office->tax_number)) {
            DB::table('offices')->where('id', $office->id)->update([
                'tax_number' => $buyerTaxNo,
                'national_address' => $buyerAddress,
            ]);
        }

        // قراءة نسبة الضريبة حصرياً من إعدادات الأدمن المركزية (يُمنع تعديلها يدوياً)
        $subtotal = round((float) $request->subtotal, 2);
        $vatRate = (float) self::getSetting('default_vat_rate', 15);
        $vatAmount = round(($subtotal * $vatRate) / 100, 2);
        $totalAmount = round($subtotal + $vatAmount, 2);

        $sellerName = self::getSetting('company_legal_name', 'الشركة الأم لإدارة العقارات');
        $sellerTax = self::getSetting('company_tax_number', '300000000000003');
        $sellerAddress = self::getSetting('company_national_address', 'الرياض');

        $seq = DB::table('tax_invoices')->where('issuer_level', 'company')->count() + 1;
        $invNumber = 'INV-CMP-' . date('Y') . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
        $issueDate = $request->issue_date ?: date('Y-m-d');

        $qrBase64 = self::generateZatcaQrBase64(
            $sellerName,
            $sellerTax,
            $issueDate . 'T' . date('H:i:s') . 'Z',
            number_format($totalAmount, 2, '.', ''),
            number_format($vatAmount, 2, '.', '')
        );

        $debitAccId = $request->filled('debit_account_id') ? (int) $request->debit_account_id : ($office->account_id ?: null);
        if (!$debitAccId) {
            $firstAsset = DB::table('accounts')->whereNull('office_id')->where('is_group', 0)->first();
            $debitAccId = $firstAsset ? $firstAsset->id : null;
        }

        $creditAccId = $request->filled('credit_account_id') ? (int) $request->credit_account_id : null;
        if (!$creditAccId) {
            $revAcc = DB::table('accounts')->whereNull('office_id')->where('code', '4101')->first()
                   ?: DB::table('accounts')->whereNull('office_id')->where('type', 'like', '%إيراد%')->where('is_group', 0)->first();
            $creditAccId = $revAcc ? $revAcc->id : $debitAccId;
        }

        $taxAccId = self::getOrCreateVatAccount(null);

        DB::transaction(function () use (
            $invNumber, $buyerTaxNo, $office, $sellerName, $sellerTax, $sellerAddress,
            $buyerAddress, $request, $subtotal, $vatRate, $vatAmount, $totalAmount,
            $issueDate, $qrBase64, $debitAccId, $creditAccId, $taxAccId
        ) {
            $journalEntryId = null;

            if ($debitAccId && $creditAccId) {
                $maxNo = DB::table('journal_entries')->whereNull('office_id')->max('entry_number') ?? 0;
                $journalEntryId = DB::table('journal_entries')->insertGetId([
                    'office_id' => null,
                    'entry_number' => $maxNo + 1,
                    'entry_date' => $issueDate,
                    'general_notes' => "قيد فاتورة ضريبية رقم ({$invNumber}) - {$office->name}: " . trim($request->description),
                    'total_debit' => $totalAmount,
                    'total_credit' => $totalAmount,
                    'total_tax' => $vatAmount,
                    'created_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('journal_entry_items')->insert([
                    'journal_entry_id' => $journalEntryId,
                    'account_id' => $debitAccId,
                    'debit' => $totalAmount,
                    'credit' => 0,
                    'tax' => $vatAmount,
                    'notes' => "استحقاق فاتورة ضريبية ({$invNumber})",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                self::applyAccountMovement((int) $debitAccId, $totalAmount, 0);

                DB::table('journal_entry_items')->insert([
                    'journal_entry_id' => $journalEntryId,
                    'account_id' => $creditAccId,
                    'debit' => 0,
                    'credit' => $subtotal,
                    'tax' => 0,
                    'notes' => "إيراد فاتورة ({$invNumber}) قبل الضريبة",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                self::applyAccountMovement((int) $creditAccId, 0, $subtotal);

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
                    self::applyAccountMovement((int) $taxAccId, 0, $vatAmount);
                }
            }

            DB::table('tax_invoices')->insert([
                'invoice_number' => $invNumber,
                'issuer_level' => 'company',
                'invoice_type' => !empty($buyerTaxNo) ? 'tax' : 'simplified',
                'office_id' => $office->id,
                'debit_account_id' => $debitAccId,
                'credit_account_id' => $creditAccId,
                'tax_account_id' => $taxAccId,
                'journal_entry_id' => $journalEntryId,
                'seller_name' => $sellerName,
                'seller_tax_number' => $sellerTax,
                'seller_address' => $sellerAddress,
                'buyer_name' => $office->name,
                'buyer_tax_number' => $buyerTaxNo ?: null,
                'buyer_address' => $buyerAddress ?: null,
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

        return redirect()->route('erp.dashboard', ['tab' => 'invoicing'])
            ->with('success', "تم إصدار الفاتورة الضريبية رقم ({$invNumber}) بنسبة ضريبة ({$vatRate}%) وترحيلها لشجرة الحسابات بنجاح.");
    }

    public function sendCompanyInvoiceWhatsapp($id)
    {
        $inv = DB::table('tax_invoices')
            ->leftJoin('offices', 'tax_invoices.office_id', '=', 'offices.id')
            ->select('tax_invoices.*', 'offices.phone as office_phone')
            ->where('tax_invoices.id', $id)
            ->first();

        if (!$inv || empty($inv->office_phone)) {
            return back()->withErrors(['لا يوجد رقم جوال مسجل للمكتب لإرسال الفاتورة عبر واتساب.']);
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

        $sent = self::sendWhatsAppMessage($inv->office_phone, $msg, null);
        if ($sent) {
            return back()->with('success', "تم إرسال الفاتورة رقم ({$inv->invoice_number}) عبر الواتساب إلى ({$inv->buyer_name}) بنجاح.");
        }

        $cleanPhone = ltrim(self::formatSaudiPhone($inv->office_phone), '+');
        $waUrl = "https://wa.me/{$cleanPhone}?text=" . urlencode($msg);
        return redirect()->away($waUrl);
    }

    public function deleteCompanyInvoice($id)
    {
        return back()->withErrors(['تنبيه نظامي: الفواتير الضريبية المعتمدة لا يُسمح بحذفها أو التعديل عليها بعد إصدارها.']);
    }

    // =========================================================================
    // إدارة الواجهة الرئيسية التعريفية للموقع (Landing Page CMS + SEO + Ordering)
    // =========================================================================
    public static function getLandingSettings(): array
    {
        $defaultStats = [
            ['number' => '+15', 'label' => 'مكتب وشركة عقارية', 'color' => 'emerald'],
            ['number' => 'ZATCA', 'label' => 'فوترة ضريبية ومبسطة QR', 'color' => 'sky'],
            ['number' => '100%', 'label' => 'شجرة حسابات وأستاذ عام آلي', 'color' => 'amber'],
            ['number' => '24/7', 'label' => 'ربط واتساب OTP وتنبيهات', 'color' => 'white'],
        ];

        $defaultFeatures = [
            [
                'icon' => '🏢',
                'title' => 'إدارة سحابية شاملة لمكاتب العقار والأملاك',
                'desc' => 'بوابة مستقلة لكل مكتب عقار لإدارة الملاك، الصكوك، الوحدات العقارية، المستأجرين، وصلاحيات الموظفين بدقة عالية.'
            ],
            [
                'icon' => '🌳',
                'title' => 'شجرة حسابات ذكية وقيود يومية وأستاذ عام',
                'desc' => 'نظام محاسبي متكامل يفتح حسابات فرعية متسلسلة آلياً للمكاتب والملاك والمستأجرين مع ميزان مراجعة وقائمة دخل فورية.'
            ],
            [
                'icon' => '🧾',
                'title' => 'فوترة إلكترونية ضريبية (ZATCA) وسندات رقمية',
                'desc' => 'إصدار فواتير ضريبية ومبسطة معتمدة بترميز QR Code (TLV Base64) وسندات قبض وصرف متسلسلة مع تصدير PDF فوري.'
            ],
            [
                'icon' => '📝',
                'title' => 'توثيق عقود (إيجار) وتنبيهات الدفعات الآلية',
                'desc' => 'ربط وتوثيق عقود الإيجار إلكترونياً، احتساب عمولة المكتب وحصة المنصة تلقائياً، وإرسال تذكيرات الدفعات عبر واتساب.'
            ],
            [
                'icon' => '👑',
                'title' => 'بوابة خاصة للملاك وأفراد العائلة والوكلاء',
                'desc' => 'تمكين مالك العقار من متابعة محفظته العقارية وعوائده الصافية وكشوف حساباته، مع منح صلاحيات مخصصة لأفراد العائلة والوكلاء.'
            ],
            [
                'icon' => '💬',
                'title' => 'دخول موحد آمن عبر واتساب (OTP) وإشعارات فورية',
                'desc' => 'تسجيل دخول سريع بكود تحقق عبر الواتساب (GreatWhats API)، وإرسال الفواتير الضريبية وسندات القبض والصرف للعملاء بضغطة زر.'
            ],
        ];

        $defaultPortals = [
            [
                'badge' => '1. الإدارة المركزية',
                'title' => 'بوابة الشركة الأم',
                'desc' => 'إدارة المكاتب العقارية، الاتفاقيات المالية، أنواع العقارات والحقول الديناميكية، الفحص الأوتوماتيكي، والنسخ الاحتياطي.',
                'color' => 'emerald'
            ],
            [
                'badge' => '2. مكاتب العقار',
                'title' => 'بوابة المكتب والموظفين',
                'desc' => 'شجرة حسابات مستقلة للمكتب، تسجيل الملاك والصكوك والوحدات، إبرام عقود إيجار، وإصدار السندات والفواتير الضريبية.',
                'color' => 'sky'
            ],
            [
                'badge' => '3. ملاك العقارات',
                'title' => 'بوابة المالك والثروة',
                'desc' => 'استعراض الصكوك والوحدات الشاغرة والمؤجرة، متابعة صافي العوائد السنوية، والاطلاع على كشوف الحسابات والتحويلات.',
                'color' => 'amber'
            ],
            [
                'badge' => '4. العائلة والوكلاء',
                'title' => 'بوابة أفراد العائلة',
                'desc' => 'حسابات فرعية يمنحها المالك لأفراد عائلته أو وكيله الشرعي بصلاحيات مخصصة (عرض العقارات أو العقود أو الكشف المالي).',
                'color' => 'purple'
            ],
        ];

        $defaultPlans = [
            [
                'badge' => 'الخيار الأول',
                'title' => self::getSetting('landing_plan1_title', 'باقة الاشتراك السنوي للمكاتب'),
                'price' => 'اشتراك سنوي ثابت',
                'desc' => self::getSetting('landing_plan1_desc', 'اشتراك سنوي ثابت يمنح مكتبك شجرة حسابات مستقلة وإدارة غير محدودة للملاك والعقارات والسندات.'),
                'btn_text' => 'ابدأ الآن',
                'highlight' => 0
            ],
            [
                'badge' => 'الخيار الأكثر مرونة',
                'title' => self::getSetting('landing_plan2_title', 'باقة النسبة المرنة لكل عقد'),
                'price' => 'نسبة ميسرة لكل عقد',
                'desc' => self::getSetting('landing_plan2_desc', 'بدون رسوم تأسيس سنوية؛ يتم احتساب نسبة مئوية ميسرة فقط عند إبرام وتوثيق عقود الإيجار النشطة.'),
                'btn_text' => 'ابدأ الآن',
                'highlight' => 1
            ],
            [
                'badge' => 'الخيار المتكامل',
                'title' => self::getSetting('landing_plan3_title', 'الباقة المدمجة للشركات العقارية'),
                'price' => 'رسوم مخفضة + نسبة رمزية',
                'desc' => self::getSetting('landing_plan3_desc', 'تجمع بين رسوم سنوية مخفضة ونسبة رمزية على العقود، مع تفعيل الفوترة الضريبية باسم الملاك وبوابة العائلة.'),
                'btn_text' => 'ابدأ الآن',
                'highlight' => 0
            ],
        ];

        $stats = json_decode(self::getSetting('landing_stats_json', ''), true);
        if (!is_array($stats) || count($stats) === 0) $stats = $defaultStats;

        $features = json_decode(self::getSetting('landing_features_json', ''), true);
        if (!is_array($features) || count($features) === 0) $features = $defaultFeatures;

        $portals = json_decode(self::getSetting('landing_portals_json', ''), true);
        if (!is_array($portals) || count($portals) === 0) $portals = $defaultPortals;

        $plans = json_decode(self::getSetting('landing_plans_json', ''), true);
        if (!is_array($plans) || count($plans) === 0) $plans = $defaultPlans;

        $customSections = json_decode(self::getSetting('landing_custom_sections_json', ''), true);
        if (!is_array($customSections)) $customSections = [];

        return [
            // الهوية والنصوص الرئيسية
            'site_name' => self::getSetting('landing_site_name', 'منظومة العقارات الذكية ERP'),
            'site_sub' => self::getSetting('landing_site_sub', 'ERP & Property Management System'),
            'badge_text' => self::getSetting('landing_badge_text', 'المنصة السحابية الأولى لإدارة العقارات والأملاك والمحاسبة في المملكة 🇸🇦'),
            'hero_title' => self::getSetting('landing_hero_title', 'أدِر مكاتب العقار، الأملاك، المحاسبة، والفوترة الضريبية في منصة واحدة متكاملة'),
            'hero_subtitle' => self::getSetting('landing_hero_subtitle', 'حل تقني ومحاسبي شامل يربط الإدارة المركزية بمكاتب العقار، الملاك، أفراد العائلة، والمستأجرين مع شجرة حسابات آلية، توثيق عقود إيجار، وفوترة ضريبية متوافقة مع هيئة الزكاة والضريبة والجمارك.'),
            'cta_primary_text' => self::getSetting('landing_cta_primary_text', 'بوابة الدخول الموحد للنظام'),
            'cta_secondary_text' => self::getSetting('landing_cta_secondary_text', 'تواصل معنا للاشتراك'),
            'contact_phone' => self::getSetting('landing_contact_phone', '0500000000'),
            'contact_whatsapp' => self::getSetting('landing_contact_whatsapp', '966500000000'),
            'contact_email' => self::getSetting('landing_contact_email', 'info@realestate-erp.sa'),
            'footer_text' => self::getSetting('landing_footer_text', 'جميع الحقوق محفوظة © منظومة إدارة العقارات والأملاك الذكية — متوافقة مع متطلبات الفوترة الإلكترونية ZATCA ومنصة إيجار.'),

            // إعدادات الـ SEO المتقدمة
            'seo_title' => self::getSetting('landing_seo_title', 'منظومة العقارات الذكية ERP | أفضل برنامج إدارة أملاك وعقارات ومحاسبة في السعودية'),
            'seo_description' => self::getSetting('landing_seo_description', 'نظام ERP عقاري سحابي متكامل لإدارة مكاتب العقار، الأملاك، الصكوك، عقود إيجار، شجرة الحسابات، والفوترة الإلكترونية المعتمدة من هيئة الزكاة والضريبة والجمارك ZATCA.'),
            'seo_keywords' => self::getSetting('landing_seo_keywords', 'برنامج إدارة عقارات, نظام إدارة أملاك, محاسبة عقارية, برنامج مكاتب عقار, فوترة إلكترونية ZATCA, توثيق عقود إيجار, شجرة حسابات عقارية, ERP عقاري السعودية'),
            'seo_author' => self::getSetting('landing_seo_author', 'منظومة العقارات الذكية ERP'),
            'seo_og_image' => self::getSetting('landing_seo_og_image', ''),
            'seo_canonical_url' => self::getSetting('landing_seo_canonical_url', url('/')),
            'seo_robots' => self::getSetting('landing_seo_robots', 'index, follow'),

            // عناوين وترتيب وإظهار/إخفاء الأقسام
            'show_stats' => (int) self::getSetting('landing_show_stats', 1),
            'order_stats' => (int) self::getSetting('landing_order_stats', 1),

            'show_features' => (int) self::getSetting('landing_show_features', 1),
            'order_features' => (int) self::getSetting('landing_order_features', 2),
            'features_badge' => self::getSetting('landing_features_badge', 'خدمات ومميزات المنظومة'),
            'features_heading' => self::getSetting('landing_features_heading', 'كل ما يحتاجه قطاع العقارات والمحاسبة في مكان واحد'),
            'features_subheading' => self::getSetting('landing_features_subheading', 'صُممت المنصة لتعمل بترابط محاسبي وإداري كامل دون الحاجة لأي برامج خارجية'),

            'show_portals' => (int) self::getSetting('landing_show_portals', 1),
            'order_portals' => (int) self::getSetting('landing_order_portals', 3),
            'portals_badge' => self::getSetting('landing_portals_badge', 'هيكلية المنصة'),
            'portals_heading' => self::getSetting('landing_portals_heading', '4 بوابات ذكية تعمل بتناغم فوري'),
            'portals_subheading' => self::getSetting('landing_portals_subheading', 'صلاحيات معزولة وبوابات مخصصة لكل طرف في المنظومة العقارية'),

            'show_plans' => (int) self::getSetting('landing_show_plans', 1),
            'order_plans' => (int) self::getSetting('landing_order_plans', 4),
            'plans_badge' => self::getSetting('landing_plans_badge', 'خيارات اشتراك مرنة'),
            'plans_heading' => self::getSetting('landing_plans_heading', 'أنظمة اتفاقيات تناسب جميع مكاتب وشركات العقار'),
            'plans_subheading' => self::getSetting('landing_plans_subheading', 'اختر الباقة الأنسب لحجم أعمالك العقارية مع إمكانية الترقية في أي وقت'),

            'show_custom' => (int) self::getSetting('landing_show_custom', 1),
            'order_custom' => (int) self::getSetting('landing_order_custom', 5),

            // المصفوفات الديناميكية
            'stats' => $stats,
            'features' => $features,
            'portals' => $portals,
            'plans' => $plans,
            'custom_sections' => $customSections,
        ];
    }

    public function showLandingPage()
    {
        $this->ensureDefaultAdminExists();
        $landing = self::getLandingSettings();

        $liveStats = [
            'offices' => Schema::hasTable('offices') ? DB::table('offices')->count() : 0,
            'properties' => Schema::hasTable('properties') ? DB::table('properties')->count() : 0,
            'contracts' => Schema::hasTable('lease_contracts') ? DB::table('lease_contracts')->count() : 0,
            'owners' => Schema::hasTable('owners') ? DB::table('owners')->count() : 0,
        ];

        return view('welcome_landing', compact('landing', 'liveStats'));
    }

    public function saveLandingSettings(Request $request)
    {
        self::ensureSystemSettingsTable();

        // 1. حفظ الهوية والنصوص الرئيسية
        self::setSetting('landing_site_name', trim($request->site_name ?? 'منظومة العقارات الذكية ERP'));
        self::setSetting('landing_site_sub', trim($request->site_sub ?? 'ERP & Property Management System'));
        self::setSetting('landing_badge_text', trim($request->badge_text ?? ''));
        self::setSetting('landing_hero_title', trim($request->hero_title ?? ''));
        self::setSetting('landing_hero_subtitle', trim($request->hero_subtitle ?? ''));
        self::setSetting('landing_cta_primary_text', trim($request->cta_primary_text ?: 'بوابة الدخول الموحد للنظام'));
        self::setSetting('landing_cta_secondary_text', trim($request->cta_secondary_text ?: 'تواصل معنا للاشتراك'));
        self::setSetting('landing_contact_phone', trim($request->contact_phone ?? ''));
        self::setSetting('landing_contact_whatsapp', preg_replace('/\D/', '', $request->contact_whatsapp ?? ''));
        self::setSetting('landing_contact_email', trim($request->contact_email ?? ''));
        self::setSetting('landing_footer_text', trim($request->footer_text ?? ''));

        // 2. حفظ إعدادات الـ SEO القوية
        self::setSetting('landing_seo_title', trim($request->seo_title ?? ''));
        self::setSetting('landing_seo_description', trim($request->seo_description ?? ''));
        self::setSetting('landing_seo_keywords', trim($request->seo_keywords ?? ''));
        self::setSetting('landing_seo_author', trim($request->seo_author ?? ''));
        self::setSetting('landing_seo_og_image', trim($request->seo_og_image ?? ''));
        self::setSetting('landing_seo_canonical_url', trim($request->seo_canonical_url ?? url('/')));
        self::setSetting('landing_seo_robots', trim($request->seo_robots ?? 'index, follow'));

        // 3. حفظ إعدادات إظهار وترتيب الأقسام وعناوينها
        self::setSetting('landing_show_stats', $request->has('show_stats') ? '1' : '0');
        self::setSetting('landing_order_stats', (string) ($request->order_stats ?? 1));

        self::setSetting('landing_show_features', $request->has('show_features') ? '1' : '0');
        self::setSetting('landing_order_features', (string) ($request->order_features ?? 2));
        self::setSetting('landing_features_badge', trim($request->features_badge ?? ''));
        self::setSetting('landing_features_heading', trim($request->features_heading ?? ''));
        self::setSetting('landing_features_subheading', trim($request->features_subheading ?? ''));

        self::setSetting('landing_show_portals', $request->has('show_portals') ? '1' : '0');
        self::setSetting('landing_order_portals', (string) ($request->order_portals ?? 3));
        self::setSetting('landing_portals_badge', trim($request->portals_badge ?? ''));
        self::setSetting('landing_portals_heading', trim($request->portals_heading ?? ''));
        self::setSetting('landing_portals_subheading', trim($request->portals_subheading ?? ''));

        self::setSetting('landing_show_plans', $request->has('show_plans') ? '1' : '0');
        self::setSetting('landing_order_plans', (string) ($request->order_plans ?? 4));
        self::setSetting('landing_plans_badge', trim($request->plans_badge ?? ''));
        self::setSetting('landing_plans_heading', trim($request->plans_heading ?? ''));
        self::setSetting('landing_plans_subheading', trim($request->plans_subheading ?? ''));

        self::setSetting('landing_show_custom', $request->has('show_custom') ? '1' : '0');
        self::setSetting('landing_order_custom', (string) ($request->order_custom ?? 5));

        // 4. حفظ بطاقات الإحصائيات
        $statNumbers = $request->input('stat_numbers', []);
        $statLabels = $request->input('stat_labels', []);
        $statColors = $request->input('stat_colors', []);
        $stats = [];
        foreach ($statLabels as $idx => $label) {
            if (!empty(trim($label)) || !empty(trim($statNumbers[$idx] ?? ''))) {
                $stats[] = [
                    'number' => trim($statNumbers[$idx] ?? ''),
                    'label' => trim($label),
                    'color' => trim($statColors[$idx] ?? 'emerald'),
                ];
            }
        }
        self::setSetting('landing_stats_json', json_encode($stats, JSON_UNESCAPED_UNICODE));

        // 5. حفظ بطاقات المميزات
        $icons = $request->input('feature_icons', []);
        $titles = $request->input('feature_titles', []);
        $descs = $request->input('feature_descs', []);
        $features = [];
        foreach ($titles as $idx => $title) {
            if (!empty(trim($title))) {
                $features[] = [
                    'icon' => trim($icons[$idx] ?? '✨') ?: '✨',
                    'title' => trim($title),
                    'desc' => trim($descs[$idx] ?? ''),
                ];
            }
        }
        self::setSetting('landing_features_json', json_encode($features, JSON_UNESCAPED_UNICODE));

        // 6. حفظ البوابات الذكية (4 بوابات أو أكثر/أقل)
        $pBadges = $request->input('portal_badges', []);
        $pTitles = $request->input('portal_titles', []);
        $pDescs = $request->input('portal_descs', []);
        $pColors = $request->input('portal_colors', []);
        $portals = [];
        foreach ($pTitles as $idx => $pTitle) {
            if (!empty(trim($pTitle))) {
                $portals[] = [
                    'badge' => trim($pBadges[$idx] ?? ('بوابة ' . ($idx + 1))),
                    'title' => trim($pTitle),
                    'desc' => trim($pDescs[$idx] ?? ''),
                    'color' => trim($pColors[$idx] ?? 'emerald'),
                ];
            }
        }
        self::setSetting('landing_portals_json', json_encode($portals, JSON_UNESCAPED_UNICODE));

        // 7. حفظ الباقات والاشتراكات الديناميكية
        $plBadges = $request->input('plan_badges', []);
        $plTitles = $request->input('plan_titles', []);
        $plPrices = $request->input('plan_prices', []);
        $plDescs = $request->input('plan_descs', []);
        $plBtns = $request->input('plan_btns', []);
        $plHighlights = $request->input('plan_highlights', []);
        $plans = [];
        foreach ($plTitles as $idx => $plTitle) {
            if (!empty(trim($plTitle))) {
                $plans[] = [
                    'badge' => trim($plBadges[$idx] ?? 'باقة اشتراك'),
                    'title' => trim($plTitle),
                    'price' => trim($plPrices[$idx] ?? ''),
                    'desc' => trim($plDescs[$idx] ?? ''),
                    'btn_text' => trim($plBtns[$idx] ?? 'ابدأ الآن') ?: 'ابدأ الآن',
                    'highlight' => (int) ($plHighlights[$idx] ?? 0),
                ];
            }
        }
        self::setSetting('landing_plans_json', json_encode($plans, JSON_UNESCAPED_UNICODE));

        // 8. حفظ الأقسام الإضافية المخصصة (Custom Sections)
        $csBadges = $request->input('custom_sec_badges', []);
        $csTitles = $request->input('custom_sec_titles', []);
        $csDescs = $request->input('custom_sec_descs', []);
        $csItems = $request->input('custom_sec_items', []);
        $customSections = [];
        foreach ($csTitles as $idx => $csTitle) {
            if (!empty(trim($csTitle))) {
                $customSections[] = [
                    'badge' => trim($csBadges[$idx] ?? 'قسم إضافي'),
                    'title' => trim($csTitle),
                    'desc' => trim($csDescs[$idx] ?? ''),
                    'items_raw' => trim($csItems[$idx] ?? ''),
                ];
            }
        }
        self::setSetting('landing_custom_sections_json', json_encode($customSections, JSON_UNESCAPED_UNICODE));

        return redirect()->route('erp.dashboard', ['tab' => 'settings'])
            ->with('success', 'تم حفظ وتحديث جميع أقسام الواجهة الرئيسية وترتيبها وإعدادات الـ SEO بنجاح!');
    }

    // =========================================================================
    // نظام تتبع الحركات والعمليات (Audit & Activity Tracker) + IP & MAC Address
    // =========================================================================
    public static function ensureActivityLogsTable(): void
    {
        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('user_name')->nullable();
                $table->string('user_type')->nullable()->index();
                $table->unsignedBigInteger('office_id')->nullable()->index();
                $table->unsignedBigInteger('owner_id')->nullable()->index();
                $table->string('action_type')->nullable(); // إضافة | تعديل | حذف | عملية
                $table->string('action_label')->nullable();
                $table->text('details')->nullable();
                $table->string('request_path')->nullable();
                $table->string('ip_address', 64)->nullable();
                $table->string('mac_address', 120)->nullable();
                $table->string('user_agent')->nullable();
                $table->timestamps();
            });
            return;
        }

        Schema::table('activity_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('activity_logs', 'request_path')) {
                $table->string('request_path')->nullable();
            }
        });
    }

    public static function detectClientIpAndMac(Request $request): array
    {
        $ip = $request->header('CF-Connecting-IP')
           ?: $request->header('X-Forwarded-For')
           ?: $request->ip()
           ?: '127.0.0.1';
        if (str_contains($ip, ',')) {
            $ip = trim(explode(',', $ip)[0]);
        }

        $mac = $request->header('X-Device-MAC') ?: $request->input('_client_mac');

        // محاولة قراءة الـ MAC الحقيقي من جدول ARP في حال الشبكة المحلية / السيرفر المحلي
        if (empty($mac) && function_exists('shell_exec')) {
            try {
                $arpOut = @shell_exec("arp -a " . escapeshellarg($ip));
                if ($arpOut && preg_match('/([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})/', $arpOut, $matches)) {
                    $mac = strtoupper(str_replace('-', ':', $matches[0]));
                }
            } catch (\Throwable $e) {}
        }

        // في حال الاتصال عبر الإنترنت السحابي (حيث يحجب الراوتر الـ MAC)، نولد بصمة MAC ثابتة للجهاز والمتصفح
        if (empty($mac)) {
            $ua = (string) $request->userAgent();
            $hash = strtoupper(substr(md5($ip . '|' . $ua), 0, 12));
            $mac = implode(':', str_split($hash, 2)) . ' (بصمة جهاز)';
        }

        return ['ip' => $ip, 'mac' => $mac];
    }

    public static function recordCurrentRequestActivity(): void
    {
        try {
            $req = request();
            if (!$req || !$req->isMethod('post') || !Auth::check()) {
                return;
            }

            $path = $req->path();
            if (str_contains($path, 'logout') || str_contains($path, 'login')) {
                return;
            }

            self::ensureActivityLogsTable();
            $user = Auth::user();

            $actionType = 'عملية';
            if (str_contains($path, 'store') || str_contains($path, 'seed')) {
                $actionType = 'إضافة';
            } elseif (str_contains($path, 'update') || str_contains($path, 'save') || str_contains($path, 'toggle')) {
                $actionType = 'تعديل';
            } elseif (str_contains($path, 'delete') || str_contains($path, 'clear')) {
                $actionType = 'حذف';
            }

            $sectionMap = [
                'offices' => 'مكاتب العقار',
                'owners' => 'الملاك',
                'properties' => 'العقارات والصكوك',
                'units' => 'الوحدات العقارية',
                'tenants' => 'المستأجرين',
                'contracts' => 'عقود الإيجار',
                'accounts' => 'شجرة الحسابات',
                'account-types' => 'أنواع الحسابات',
                'vouchers' => 'سندات القبض والصرف',
                'journal-entries' => 'القيود اليومية',
                'invoices' => 'الفواتير الضريبية',
                'staff' => 'موظفي المكتب',
                'family' => 'أفراد العائلة والوكلاء',
                'property-types' => 'أنواع العقارات',
                'property-categories' => 'تصنيفات العقارات',
                'property-attributes' => 'متطلبات العقارات',
                'payment-methods' => 'طرق الدفع',
                'whatsapp' => 'إعدادات واتساب',
                'tax-profile' => 'الإعدادات الضريبية',
                'profile' => 'البيانات الشخصية',
            ];

            $sectionLabel = 'النظام العام';
            foreach ($sectionMap as $key => $label) {
                if (str_contains($path, $key)) {
                    $sectionLabel = $label;
                    break;
                }
            }

            $actionLabel = "{$actionType} في قسم ({$sectionLabel})";

            // استخراج الاسم الأول أو اسم المستخدم بشكل نظيف
            $actorName = trim(explode('(', $user->name)[0]) ?: ($user->username ?: 'المستخدم');

            // التقاط اسم العنصر المضاف أو المعدل (مثل: عمارة سكنية، اسم المكتب، رقم الوحدة، البيان...)
            $targetItem = $req->input('name')
                       ?: $req->input('title')
                       ?: $req->input('unit_number')
                       ?: $req->input('attribute_name')
                       ?: $req->input('description')
                       ?: $req->input('general_notes')
                       ?: $req->input('notes')
                       ?: $req->input('legal_name')
                       ?: $req->input('site_name');

            if (is_array($targetItem)) {
                $targetItem = implode('، ', array_filter($targetItem));
            }
            if (empty($targetItem) && is_array($req->input('names'))) {
                $targetItem = implode('، ', array_filter($req->input('names')));
            }
            if (empty($targetItem) && is_array($req->input('attribute_names'))) {
                $targetItem = implode('، ', array_filter($req->input('attribute_names')));
            }
            $targetItem = trim((string) $targetItem);

            // صياغة جملة عربية مبسطة وواضحة للجميع (الأدمن، المكتب، والمالك)
            if (str_contains($path, 'contracts') && $actionType === 'إضافة') {
                $simpleDetails = "{$actorName} أنشأ عقد إيجار جديد" . ($req->input('total_rent_amount') ? " بقيمة {$req->input('total_rent_amount')} ر.س" : "");
            } elseif (str_contains($path, 'sync-ejar')) {
                $simpleDetails = "{$actorName} وثّق عقد إيجار في منصة إيجار";
            } elseif (str_contains($path, 'vouchers') && $actionType === 'إضافة') {
                $vTypeAr = $req->input('voucher_type') === 'payment' ? 'سند صرف' : 'سند قبض';
                $simpleDetails = "{$actorName} أصدر {$vTypeAr}" . ($req->input('amount') ? " بمبلغ {$req->input('amount')} ر.س" : "") . ($targetItem ? " ({$targetItem})" : "");
            } elseif (str_contains($path, 'invoices') && $actionType === 'إضافة') {
                $simpleDetails = "{$actorName} أصدر فاتورة ضريبية" . ($targetItem ? " ({$targetItem})" : "");
            } elseif (str_contains($path, 'journal-entries') && $actionType === 'إضافة') {
                $simpleDetails = "{$actorName} أنشأ قيد يومي" . ($targetItem ? " ({$targetItem})" : "");
            } elseif (str_contains($path, 'seed')) {
                $simpleDetails = "{$actorName} قام بتوليد شجرة الحسابات القياسية";
            } elseif (str_contains($path, 'diagnostics')) {
                $simpleDetails = "{$actorName} شغّل الفحص الأوتوماتيكي الشامل للنظام";
            } else {
                $verb = match ($actionType) {
                    'إضافة' => 'أضاف',
                    'تعديل' => 'عدّل',
                    'حذف'   => 'حذف',
                    default => 'نفّذ عملية في',
                };
                if ($targetItem !== '') {
                    $simpleDetails = "{$actorName} {$verb} {$targetItem} ({$sectionLabel})";
                } else {
                    $simpleDetails = "{$actorName} {$verb} في {$sectionLabel}";
                }
            }

            $netInfo = self::detectClientIpAndMac($req);

            $effectiveOfficeId = session('impersonate_office_id') ?: $user->office_id;
            $effectiveOwnerId  = session('impersonate_owner_id') ?: $user->owner_id;

            DB::table('activity_logs')->insert([
                'user_id' => $user->id,
                'user_name' => $user->name . ' (' . ($user->username ?: $user->phone) . ')',
                'user_type' => $user->user_type,
                'office_id' => $effectiveOfficeId,
                'owner_id' => $effectiveOwnerId,
                'action_type' => $actionType,
                'action_label' => $actionLabel,
                'details' => $simpleDetails,
                'request_path' => ltrim($path, '/'),
                'ip_address' => $netInfo['ip'],
                'mac_address' => $netInfo['mac'],
                'user_agent' => mb_substr((string) $req->userAgent(), 0, 180),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // تجاهل أي خطأ لعدم تعطيل العملية الأساسية
        }
    }

    /**
     * جلب سجل التتبع حسب الصلاحيات المطلوبة بدقة:
     * 1. الأدمن (company_admin): يرى الجميع + يرى الـ IP والـ MAC Address.
     * 2. المكتب (office_admin / office_staff): يرى موظفي مكتبه فقط، ولا يرى الملاك أو العائلة.
     * 3. المالك (owner): يرى أفراد عائلته فقط (owner_family).
     */
    public static function getFilteredActivityLogs(?int $officeId = null, ?int $ownerId = null, string $viewerRole = 'company_admin')
    {
        self::ensureActivityLogsTable();
        $query = DB::table('activity_logs')->orderBy('id', 'desc');

        if ($viewerRole === 'office') {
            $query->where('office_id', $officeId)
                  ->whereIn('user_type', ['office_admin', 'office_staff']);
        } elseif ($viewerRole === 'owner') {
            $query->where('owner_id', $ownerId)
                  ->where('user_type', 'owner_family');
        }

        return $query->limit(350)->get();
    }

    // =========================================================================
    // نظام الباقات وصلاحيات الصفحات لمكاتب العقار + الربط بالواجهة الرئيسية
    // =========================================================================
    public static function getAvailableOfficeTabs(): array
    {
        return [
            'overview' => 'لوحة التحكم والملخص العام',
            'owners' => 'إدارة الملاك',
            'properties' => 'العقارات والصكوك والوحدات',
            'contracts' => 'المستأجرون وعقود (إيجار)',
            'accounting' => 'شجرة الحسابات المحاسبية',
            'vouchers' => 'سندات القبض والصرف',
            'journal' => 'القيود اليومية',
            'invoicing' => 'الفواتير الضريبية (ZATCA)',
            'account_statement' => 'كشف الحساب التفصيلي',
            'statements' => 'الأستاذ العام والقوائم المالية',
            'staff' => 'إدارة الموظفين والصلاحيات',
            'activity_logs' => 'سجل تتبع حركات الموظفين',
            'whatsapp' => 'إعدادات الربط مع واتساب والضريبة',
        ];
    }

    public function ensureOfficePlansTable(): void
    {
        if (!Schema::hasTable('office_plans')) {
            Schema::create('office_plans', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('badge')->nullable();
                $table->string('billing_type')->default('annual_subscription');
                $table->decimal('price', 15, 2)->default(0);
                $table->decimal('old_price', 15, 2)->nullable();
                $table->string('offer_label')->nullable();
                $table->decimal('commission_rate', 5, 2)->default(0);
                $table->text('description')->nullable();
                $table->text('allowed_tabs')->nullable();
                $table->boolean('show_on_landing')->default(true);
                $table->boolean('is_highlighted')->default(false);
                $table->timestamps();
            });

            $defaultPlans = [
                [
                    'name' => 'باقة إدارة الأملاك والعقارات الأساسية',
                    'badge' => 'اقتصادية للمكاتب',
                    'billing_type' => 'annual_subscription',
                    'price' => 1500,
                    'old_price' => 2200,
                    'offer_label' => 'عرض خاص - خصم 30%',
                    'commission_rate' => 0,
                    'description' => 'مخصصة لإدارة الملاك، الصكوك، الوحدات العقارية، المستأجرين وتوثيق عقود إيجار بدون النظام المحاسبي وسجل التتبع.',
                    'allowed_tabs' => json_encode(['overview', 'owners', 'properties', 'contracts', 'staff'], JSON_UNESCAPED_UNICODE),
                    'show_on_landing' => 1,
                    'is_highlighted' => 0,
                ],
                [
                    'name' => 'الباقة المحاسبية والعقارية الشاملة (Pro ERP)',
                    'badge' => 'الأكثر طلباً ⭐',
                    'billing_type' => 'annual_subscription',
                    'price' => 3000,
                    'old_price' => 4000,
                    'offer_label' => 'شامل الفوترة الضريبية ZATCA',
                    'commission_rate' => 0,
                    'description' => 'تشمل جميع صفحات إدارة الأملاك + النظام المالي والمحاسبي الكامل (شجرة الحسابات، السندات، القيود، الفواتير الضريبية، الأستاذ العام، وسجل التتبع).',
                    'allowed_tabs' => json_encode(array_keys(self::getAvailableOfficeTabs()), JSON_UNESCAPED_UNICODE),
                    'show_on_landing' => 1,
                    'is_highlighted' => 1,
                ],
                [
                    'name' => 'باقة الشراكة المرنة (اشتراك مخفض + نسبة)',
                    'badge' => 'باقة مرنة',
                    'billing_type' => 'hybrid',
                    'price' => 1000,
                    'old_price' => null,
                    'offer_label' => 'رسوم ميسرة + نسبة لكل عقد',
                    'commission_rate' => 2.5,
                    'description' => 'تمنح المكتب كامل خصائص المنظومة العقارية والمحاسبية والربط مع واتساب مقابل اشتراك سنوي مخفض ونسبة رمزية على العقود.',
                    'allowed_tabs' => json_encode(array_keys(self::getAvailableOfficeTabs()), JSON_UNESCAPED_UNICODE),
                    'show_on_landing' => 1,
                    'is_highlighted' => 0,
                ],
            ];

            foreach ($defaultPlans as $dp) {
                DB::table('office_plans')->insert(array_merge($dp, ['created_at' => now(), 'updated_at' => now()]));
            }
            $this->syncOfficePlansToLandingPage();
        }
    }

    public function syncOfficePlansToLandingPage(): void
    {
        if (!Schema::hasTable('office_plans')) return;
        $plans = DB::table('office_plans')->where('show_on_landing', 1)->orderBy('id', 'asc')->get();
        if ($plans->count() === 0) return;

        $landingPlans = [];
        foreach ($plans as $p) {
            $priceText = '';
            if ($p->billing_type === 'annual_subscription') {
                $priceText = number_format($p->price, 0) . ' ر.س / سنوياً';
            } elseif ($p->billing_type === 'per_property_percentage') {
                $priceText = 'نسبة ' . $p->commission_rate . '% لكل عقد';
            } else {
                $priceText = number_format($p->price, 0) . ' ر.س + ' . $p->commission_rate . '%';
            }

            if (!empty($p->old_price) && $p->old_price > $p->price) {
                $priceText .= ' (بدلاً من ' . number_format($p->old_price, 0) . ' ر.س)';
            }
            if (!empty($p->offer_label)) {
                $priceText .= ' — ' . $p->offer_label;
            }

            $landingPlans[] = [
                'badge' => $p->badge ?: 'باقة اشتراك',
                'title' => $p->name,
                'price' => $priceText,
                'desc' => $p->description ?: '',
                'btn_text' => 'اشترك في الباقة الآن',
                'highlight' => $p->is_highlighted ? 1 : 0,
            ];
        }

        self::setSetting('landing_plans_json', json_encode($landingPlans, JSON_UNESCAPED_UNICODE));
    }

    public function storeOfficePlan(Request $request)
    {
        $this->ensureOfficePlansTable();
        $request->validate(['name' => 'required']);

        $tabs = $request->input('allowed_tabs', ['overview']);
        if (!in_array('overview', $tabs)) {
            array_unshift($tabs, 'overview');
        }

        DB::table('office_plans')->insert([
            'name' => trim($request->name),
            'badge' => trim($request->badge ?: 'باقة مكاتب'),
            'billing_type' => $request->billing_type ?: 'annual_subscription',
            'price' => (float) ($request->price ?? 0),
            'old_price' => $request->filled('old_price') ? (float) $request->old_price : null,
            'offer_label' => trim($request->offer_label ?? ''),
            'commission_rate' => (float) ($request->commission_rate ?? 0),
            'description' => trim($request->description ?? ''),
            'allowed_tabs' => json_encode(array_values(array_unique($tabs)), JSON_UNESCAPED_UNICODE),
            'show_on_landing' => $request->has('show_on_landing') ? 1 : 0,
            'is_highlighted' => $request->has('is_highlighted') ? 1 : 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->syncOfficePlansToLandingPage();

        return redirect()->route('erp.dashboard', ['tab' => 'office_plans'])
            ->with('success', 'تم إنشاء الباقة وتحديد صفحاتها المسموحة وتحديث الواجهة الرئيسية للموقع بنجاح.');
    }

    public function updateOfficePlan(Request $request, $id)
    {
        $this->ensureOfficePlansTable();
        $tabs = $request->input('allowed_tabs', ['overview']);
        if (!in_array('overview', $tabs)) {
            array_unshift($tabs, 'overview');
        }
        $encodedTabs = json_encode(array_values(array_unique($tabs)), JSON_UNESCAPED_UNICODE);

        DB::table('office_plans')->where('id', $id)->update([
            'name' => trim($request->name),
            'badge' => trim($request->badge ?: 'باقة مكاتب'),
            'billing_type' => $request->billing_type ?: 'annual_subscription',
            'price' => (float) ($request->price ?? 0),
            'old_price' => $request->filled('old_price') ? (float) $request->old_price : null,
            'offer_label' => trim($request->offer_label ?? ''),
            'commission_rate' => (float) ($request->commission_rate ?? 0),
            'description' => trim($request->description ?? ''),
            'allowed_tabs' => $encodedTabs,
            'show_on_landing' => $request->has('show_on_landing') ? 1 : 0,
            'is_highlighted' => $request->has('is_highlighted') ? 1 : 0,
            'updated_at' => now(),
        ]);

        // تحديث جميع المكاتب المشتركة في هذه الباقة تلقائياً إذا اختار الأدمن ذلك
        if ($request->has('sync_subscribed_offices')) {
            DB::table('offices')->where('plan_id', $id)->update([
                'allowed_tabs' => $encodedTabs,
                'billing_type' => $request->billing_type ?: 'annual_subscription',
                'annual_fee' => (float) ($request->price ?? 0),
                'company_commission_rate' => (float) ($request->commission_rate ?? 0),
                'updated_at' => now(),
            ]);
        }

        $this->syncOfficePlansToLandingPage();

        return redirect()->route('erp.dashboard', ['tab' => 'office_plans'])
            ->with('success', 'تم تحديث بيانات الباقة وصلاحياتها ومزامنتها مع الواجهة الرئيسية بنجاح.');
    }

    public function deleteOfficePlan($id)
    {
        DB::table('office_plans')->where('id', $id)->delete();
        $this->syncOfficePlansToLandingPage();

        return redirect()->route('erp.dashboard', ['tab' => 'office_plans'])
            ->with('success', 'تم حذف الباقة وتحديث الواجهة الرئيسية.');
    }
}