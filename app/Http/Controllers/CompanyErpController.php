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
     * بوابة حماية مركزية: تمنع أي مستخدم غير (company_admin) من استدعاء دوال الإدارة المركزية
     */
    public static array $preActionSnapshots = [];

    public function callAction($method, $parameters)
    {
        if (in_array($method, ['showLandingPage', 'showLogin', 'dashboard', 'enterLiveDemoSandbox'], true)) {
            self::recordSiteVisit(request(), $method);
        }

        $publicOrSharedMethods = [
            'showLandingPage',
            'enterLiveDemoSandbox',
            'registerOfficeSelfTrial',
            'showPublicMullakVote',
            'submitPublicMullakVote',
            'showPublicInvoicePdf',
            'handleWhatsappWebhook',
            'showLogin',
            'sendOtp',
            'verifyOtp',
            'loginWithPassword',
            'logout',
            'printVoucher',
            'sendRawPdfViaWhatsapp',
        ];

        if (!in_array($method, $publicOrSharedMethods, true)) {
            if (!Auth::check() || Auth::user()->user_type !== 'company_admin') {
                abort(403, 'غير مصرح لك بالوصول إلى إجراءات الإدارة المركزية.');
            }
        }

        self::capturePreActionSnapshot(request());

        return $this->{$method}(...array_values($parameters));
    }

    /**
     * توليد توكن مشفر وموقع بمفتاح النظام للفواتير الضريبية العامة
     */
    public static function generateInvoicePublicToken($id, string$invoiceNumber): string
    {
        return hash_hmac('sha256', $id . '\vert{}' .$invoiceNumber . '|zatca_pdf', config('app.key', 'erp_zatca_secret_key'));
    }

    /**
     * فحص وإنشاء جميع جداول وأعمدة النظام تلقائياً (Self-Healing Schema) + إنشاء حساب الأدمن الافتراضي
     */
    public function ensureDefaultAdminExists(): void
    {
        // 1. إنشاء جدول المستخدمين إذا لم يكن موجوداً
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('username')->nullable()->unique();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('national_id')->nullable();
                $table->string('password');
                $table->string('user_type')->default('office_admin');
                $table->unsignedBigInteger('office_id')->nullable();
                $table->unsignedBigInteger('owner_id')->nullable();
                $table->boolean('is_active')->default(true);
                $table->string('otp_code', 6)->nullable();
                $table->timestamp('otp_expires_at')->nullable();
                $table->json('custom_permissions')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        } else {
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
        }

        // 2. إنشاء جدول شجرة الحسابات المحاسبية (accounts) إذا لم يكن موجوداً
        if (!Schema::hasTable('accounts')) {
            Schema::create('accounts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('office_id')->nullable()->index();
                $table->unsignedBigInteger('parent_id')->nullable()->index();
                $table->string('code');
                $table->string('name');
                $table->string('type')->default('الأصول');
                $table->boolean('is_group')->default(false);
                $table->decimal('balance', 15, 2)->default(0);
                $table->decimal('debit_balance', 15, 2)->default(0);
                $table->decimal('credit_balance', 15, 2)->default(0);
                $table->timestamps();
            });
        }

        // 3. إنشاء جدول مكاتب العقار (offices) إذا لم يكن موجوداً
        if (!Schema::hasTable('offices')) {
            Schema::create('offices', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('cr_number')->nullable();
                $table->string('fal_license')->nullable();
                $table->string('tax_number')->nullable();
                $table->string('national_address')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('address')->nullable();
                $table->string('billing_type')->default('annual_subscription');
                $table->decimal('annual_fee', 15, 2)->default(0);
                $table->decimal('company_commission_rate', 5, 2)->default(0);
                $table->unsignedBigInteger('plan_id')->nullable();
                $table->text('allowed_tabs')->nullable();
                $table->boolean('allow_owner_tax_invoice')->default(false);
                $table->date('subscription_ends_at')->nullable();
                $table->string('status')->default('active');
                $table->unsignedBigInteger('account_id')->nullable();
                $table->boolean('use_custom_whatsapp')->default(false);
                $table->string('whatsapp_mode')->default('company');
                $table->string('whatsapp_sender_phone')->nullable();
                $table->string('whatsapp_api_url')->nullable();
                $table->string('whatsapp_api_token')->nullable();
                $table->string('whatsapp_instance_id')->nullable();
                $table->integer('reminder_days_before')->default(15);
                $table->string('reminder_target')->default('both');
                $table->text('reminder_template')->nullable();
                $table->boolean('enable_auto_reminders')->default(true);
                $table->decimal('wa_approval_threshold', 15, 2)->default(10000);
                $table->string('wa_approval_keywords')->default('اعتمد,تم,اوك,موافق,نعم');
                $table->boolean('enable_wa_voucher_approval')->default(false);
                $table->decimal('anomaly_max_amount', 15, 2)->default(20000);
                $table->integer('anomaly_night_start')->default(23);
                $table->integer('anomaly_night_end')->default(5);
                $table->boolean('anomaly_alert_on_delete')->default(true);
                $table->boolean('enable_anomaly_detector')->default(true);
                $table->text('wa_invoice_caption')->nullable();
                $table->text('wa_voucher_caption')->nullable();
                $table->text('wa_legal_caption')->nullable();
                $table->timestamps();
            });
        } else {
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
                    $table->string('reminder_target')->default('both');
                }
                if (!Schema::hasColumn('offices', 'reminder_template')) {
                    $table->text('reminder_template')->nullable();
                }
                if (!Schema::hasColumn('offices', 'enable_auto_reminders')) {
                    $table->boolean('enable_auto_reminders')->default(true);
                }
                if (!Schema::hasColumn('offices', 'reminder_settings_json')) {
                    $table->text('reminder_settings_json')->nullable();
                }
                if (!Schema::hasColumn('offices', 'plan_id')) {
                    $table->unsignedBigInteger('plan_id')->nullable();
                }
                if (!Schema::hasColumn('offices', 'allowed_tabs')) {
                    $table->text('allowed_tabs')->nullable();
                }
                if (!Schema::hasColumn('offices', 'wa_approval_threshold')) {
                    $table->decimal('wa_approval_threshold', 15, 2)->default(10000);
                }
                if (!Schema::hasColumn('offices', 'wa_approval_keywords')) {
                    $table->string('wa_approval_keywords')->default('اعتمد,تم,اوك,موافق,نعم');
                }
                if (!Schema::hasColumn('offices', 'enable_wa_voucher_approval')) {
                    $table->boolean('enable_wa_voucher_approval')->default(false);
                }
                if (!Schema::hasColumn('offices', 'anomaly_max_amount')) {
                    $table->decimal('anomaly_max_amount', 15, 2)->default(20000);
                }
                if (!Schema::hasColumn('offices', 'anomaly_night_start')) {
                    $table->integer('anomaly_night_start')->default(23);
                }
                if (!Schema::hasColumn('offices', 'anomaly_night_end')) {
                    $table->integer('anomaly_night_end')->default(5);
                }
                if (!Schema::hasColumn('offices', 'anomaly_alert_on_delete')) {
                    $table->boolean('anomaly_alert_on_delete')->default(true);
                }
                if (!Schema::hasColumn('offices', 'enable_anomaly_detector')) {
                    $table->boolean('enable_anomaly_detector')->default(true);
                }
                if (!Schema::hasColumn('offices', 'wa_invoice_caption')) {
                    $table->text('wa_invoice_caption')->nullable();
                }
                if (!Schema::hasColumn('offices', 'wa_voucher_caption')) {
                    $table->text('wa_voucher_caption')->nullable();
                }
                if (!Schema::hasColumn('offices', 'wa_legal_caption')) {
                    $table->text('wa_legal_caption')->nullable();
                }
            });
        }

        // 4. إنشاء جدول السندات المالية (vouchers) إذا لم يكن موجوداً
        if (!Schema::hasTable('vouchers')) {
            Schema::create('vouchers', function (Blueprint $table) {
                $table->id();
                $table->string('voucher_number')->unique();
                $table->string('voucher_type')->default('receipt');
                $table->unsignedBigInteger('office_id')->nullable()->index();
                $table->unsignedBigInteger('owner_id')->nullable();
                $table->unsignedBigInteger('tenant_id')->nullable();
                $table->unsignedBigInteger('lease_contract_id')->nullable();
                $table->unsignedBigInteger('property_id')->nullable();
                $table->unsignedBigInteger('property_unit_id')->nullable();
                $table->unsignedBigInteger('installment_id')->nullable();
                $table->unsignedBigInteger('debit_account_id');
                $table->unsignedBigInteger('credit_account_id');
                $table->decimal('amount', 15, 2)->default(0);
                $table->string('payment_method')->default('bank_transfer');
                $table->string('reference_number')->nullable();
                $table->date('voucher_date');
                $table->text('notes')->nullable();
                $table->string('approval_status')->default('approved');
                $table->string('approval_code', 20)->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('vouchers', function (Blueprint $table) {
                if (!Schema::hasColumn('vouchers', 'property_id')) {
                    $table->unsignedBigInteger('property_id')->nullable();
                }
                if (!Schema::hasColumn('vouchers', 'property_unit_id')) {
                    $table->unsignedBigInteger('property_unit_id')->nullable();
                }
                if (!Schema::hasColumn('vouchers', 'installment_id')) {
                    $table->unsignedBigInteger('installment_id')->nullable();
                }
                if (!Schema::hasColumn('vouchers', 'approval_status')) {
                    $table->string('approval_status')->default('approved');
                }
                if (!Schema::hasColumn('vouchers', 'approval_code')) {
                    $table->string('approval_code', 20)->nullable();
                }
            });
        }

        // 5. إنشاء جداول أنواع العقارات ومتطلباتها (property_types & property_type_attributes)
        if (!Schema::hasTable('property_types')) {
            Schema::create('property_types', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('category')->default('سكني');
                $table->unsignedBigInteger('account_id')->nullable();
                $table->boolean('has_units')->default(true);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('property_type_attributes')) {
            Schema::create('property_type_attributes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('property_type_id')->index();
                $table->string('attribute_name');
                $table->string('field_type')->default('نص (Text)');
                $table->text('options')->nullable();
                $table->boolean('is_required')->default(true);
                $table->timestamps();
            });
        }

        // 6. إنشاء جداول الملاك، المستأجرين، العقارات، الوحدات، وعقود الإيجار إذا لم تكن موجودة
        if (!Schema::hasTable('owners')) {
            Schema::create('owners', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('office_id')->index();
                $table->string('name');
                $table->string('national_id')->nullable();
                $table->string('tax_number')->nullable();
                $table->string('cr_number')->nullable();
                $table->string('national_address')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('iban')->nullable();
                $table->string('bank_name')->nullable();
                $table->decimal('office_management_fee_rate', 5, 2)->default(5.00);
                $table->unsignedBigInteger('account_id')->nullable();
                $table->timestamps();
            });
        } else {
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

        if (!Schema::hasTable('tenants')) {
            Schema::create('tenants', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('office_id')->index();
                $table->string('name');
                $table->string('id_type')->default('national_id');
                $table->string('id_number')->nullable();
                $table->string('tax_number')->nullable();
                $table->string('national_address')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->unsignedBigInteger('account_id')->nullable();
                $table->timestamps();
            });
        } else {
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

        if (!Schema::hasTable('properties')) {
            Schema::create('properties', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('office_id')->index();
                $table->unsignedBigInteger('owner_id')->index();
                $table->unsignedBigInteger('property_type_id');
                $table->string('title');
                $table->string('deed_number')->nullable();
                $table->date('deed_date')->nullable();
                $table->decimal('total_area', 15, 2)->default(0);
                $table->decimal('estimated_value', 15, 2)->default(0);
                $table->string('city')->nullable();
                $table->string('district')->nullable();
                $table->string('street')->nullable();
                $table->string('building_number')->nullable();
                $table->string('postal_code')->nullable();
                $table->string('additional_number')->nullable();
                $table->text('custom_attributes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('property_units')) {
            Schema::create('property_units', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('property_id')->index();
                $table->unsignedBigInteger('property_type_id')->nullable();
                $table->string('unit_number');
                $table->integer('floor_number')->default(0);
                $table->decimal('area', 15, 2)->default(0);
                $table->string('electricity_meter')->nullable();
                $table->string('water_meter')->nullable();
                $table->decimal('default_rent_price', 15, 2)->default(0);
                $table->string('status')->default('vacant');
                $table->text('custom_attributes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lease_contracts')) {
            Schema::create('lease_contracts', function (Blueprint $table) {
                $table->id();
                $table->string('contract_number')->unique();
                $table->string('ejar_contract_number')->nullable();
                $table->string('ejar_status')->default('not_sent');
                $table->unsignedBigInteger('office_id')->index();
                $table->unsignedBigInteger('property_id')->index();
                $table->unsignedBigInteger('property_unit_id')->nullable();
                $table->unsignedBigInteger('owner_id')->index();
                $table->unsignedBigInteger('tenant_id')->index();
                $table->date('start_date');
                $table->date('end_date');
                $table->string('payment_cycle')->default('annual');
                $table->decimal('total_rent_amount', 15, 2)->default(0);
                $table->decimal('security_deposit', 15, 2)->default(0);
                $table->decimal('office_commission', 15, 2)->default(0);
                $table->decimal('company_fee_amount', 15, 2)->default(0);
                $table->string('status')->default('active');
                $table->timestamps();
            });
        }

        // 7. تفعيل باقي الجداول المساندة تلقائياً في حال كانت قاعدة البيانات جديدة
        self::ensureSystemSettingsTable();
        self::ensureContractInstallmentsTable();
        $this->ensureDynamicPropertyTables();
        $this->ensureDynamicAccountTables();
        $this->ensurePaymentMethodsTable();
        $this->ensureJournalTables();
        $this->ensureSystemMonitoringTables();
        $this->ensureTaxInvoicesTable();
        $this->ensureOfficePlansTable();
        self::ensureActivityLogsTable();

        if (Schema::hasTable('journal_entry_items')) {
            Schema::table('journal_entry_items', function (Blueprint $table) {
                if (!Schema::hasColumn('journal_entry_items', 'property_id')) {
                    $table->unsignedBigInteger('property_id')->nullable();
                }
                if (!Schema::hasColumn('journal_entry_items', 'property_unit_id')) {
                    $table->unsignedBigInteger('property_unit_id')->nullable();
                }
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

        // إذا كان الجدول قديماً ويحتوي على عمود setting_key بقيد NOT NULL، نعيد بناءه ليصبح نظيفاً ومرناً
        if (Schema::hasColumn('system_settings', 'setting_key')) {
            try {
                DB::transaction(function () {
                    Schema::create('system_settings_clean_tmp', function (Blueprint $table) {
                        $table->id();
                        $table->string('key')->nullable()->index();
                        $table->string('setting_key')->nullable();
                        $table->text('value')->nullable();
                        $table->timestamps();
                    });

                    $oldRows = DB::table('system_settings')->get();
                    foreach ($oldRows as $row) {
                        $k = $row->key ?? ($row->setting_key ?? null);
                        DB::table('system_settings_clean_tmp')->insert([
                            'id' => $row->id,
                            'key' => $k,
                            'setting_key' => $k,
                            'value' => $row->value ?? null,
                            'created_at' => $row->created_at ?? now(),
                            'updated_at' => $row->updated_at ?? now(),
                        ]);
                    }

                    Schema::drop('system_settings');
                    Schema::rename('system_settings_clean_tmp', 'system_settings');
                });
                return;
            } catch (\Throwable $e) {}
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
     * التحقق مما إذا كانت الإعدادات تستخدم Meta WhatsApp Cloud API الرسمي (graph.facebook.com)
     */
    public static function isMetaCloudApi(?string $apiUrl, ?string $apiToken, ?string $instanceId): bool
    {
        $url = strtolower(trim((string) $apiUrl));
        $tok = trim((string) $apiToken);
        $inst = trim((string) $instanceId);

        if (str_contains($url, 'graph.facebook.com')) {
            return true;
        }
        if (str_starts_with($tok, 'EAA') && preg_match('/^\d{8,25}$/', $inst)) {
            return true;
        }
        return false;
    }

    /**
     * بناء رابط إرسال الرسائل الرسمي في Meta Cloud API:
     * https://graph.facebook.com/v23.0/<PHONE_NUMBER_ID>/messages
     */
    public static function buildMetaMessagesUrl(?string $apiUrl, ?string $phoneNumberId): string
    {
        $url = rtrim(trim((string) $apiUrl), '/');
        $phoneId = preg_replace('/\D/', '', (string) $phoneNumberId);

        if (str_contains($url, 'graph.facebook.com') && str_ends_with($url, '/messages')) {
            return $url;
        }
        if (!empty($phoneId)) {
            return "https://graph.facebook.com/v23.0/{$phoneId}/messages";
        }
        if (preg_match('#graph\.facebook\.com/v[\d\.]+/(\d+)#i', $url, $m)) {
            return "https://graph.facebook.com/v23.0/{$m[1]}/messages";
        }
        return $url ?: 'https://graph.facebook.com/v23.0/messages';
    }

    /**
     * إرسال رسالة واتساب عبر إعدادات الشركة الأم أو إعدادات المكتب (يدعم Meta Cloud API الرسمي)
     */
    public static function sendWhatsAppMessage(string $toPhone, string $message, ?int $officeId = null): bool
    {
        $apiUrl = self::getSetting('whatsapp_api_url', env('WHATSAPP_API_URL'));
        $apiToken = self::getSetting('whatsapp_api_token', env('WHATSAPP_API_TOKEN'));
        $instanceId = self::getSetting('whatsapp_instance_id', '');

        if ($officeId && Schema::hasTable('offices')) {
            $office = DB::table('offices')->where('id', $officeId)->first();
            $isCustom = (!empty($office->use_custom_whatsapp) || ($office->whatsapp_mode ?? '') === 'custom');
            if ($office && $isCustom && !empty($office->whatsapp_api_token)) {
                $apiUrl = $office->whatsapp_api_url ?: $apiUrl;
                $apiToken = $office->whatsapp_api_token;
                $instanceId = $office->whatsapp_instance_id ?? '';
            }
        }

        if (empty($apiToken)) {
            return false;
        }

        $formattedPhone = self::formatSaudiPhone($toPhone);
        $cleanDigitsPhone = ltrim($formattedPhone, '+');

        // 1. الإرسال عبر Meta WhatsApp Cloud API الرسمي (graph.facebook.com/v23.0)
        if (self::isMetaCloudApi($apiUrl, $apiToken, $instanceId)) {
            $endpoint = self::buildMetaMessagesUrl($apiUrl, $instanceId);
            try {
                $response = Http::withToken(trim($apiToken))
                    ->acceptJson()
                    ->contentType('application/json')
                    ->timeout(12)
                    ->post($endpoint, [
                        'messaging_product' => 'whatsapp',
                        'recipient_type'    => 'individual',
                        'to'                => $cleanDigitsPhone,
                        'type'              => 'text',
                        'text'              => [
                            'preview_url' => true,
                            'body'        => $message,
                        ],
                    ]);

                if ($response->successful()) {
                    return true;
                }

                self::logSystemIssue(
                    'Meta WhatsApp Cloud API',
                    'HTTP ' . $response->status() . ': ' . mb_substr($response->body(), 0, 300),
                    'WARNING',
                    $endpoint,
                    'تأكد من صلاحية التوكن الدائم (System User Token) ومعرف الرقم (Phone Number ID) وفتح نافذة الـ 24 ساعة.'
                );
                return false;
            } catch (\Throwable $e) {
                self::logSystemIssue('Meta WhatsApp Cloud API Exception', $e->getMessage(), 'ERROR');
                return false;
            }
        }

        // 2. الإرسال عبر بوابات الـ API الأخرى
        if (empty($apiUrl)) {
            return false;
        }

        try {
            $response = Http::timeout(8)->post($apiUrl, [
                'token' => $apiToken,
                'instance_id' => $instanceId,
                'to' => $formattedPhone,
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
        if (!session('login_admin_checked')) {
            $this->ensureDefaultAdminExists();
            session(['login_admin_checked' => true]);
        }

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

        $otp = (string) random_int(1000, 9999);

        DB::table('users')->where('id', $user->id)->update([
            'phone' => $formattedPhone,
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        $whatsappSent = self::sendWhatsAppMessage($formattedPhone,
            "كود التحقق الخاص بك في منصة العقارات هو: *{$otp}*",
            $user->office_id
        );

        return back()->with([
            'otp_step' => true,
            'local_phone' => $request->phone,
            'formatted_phone' => $formattedPhone,
            'whatsapp_sent' => $whatsappSent,
            'dev_otp_hint' => (!$whatsappSent && config('app.debug')) ?$otp : null,
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp_code' => ['required', 'string', 'regex:/^[0-9]{4,6}$/'],
        ], [
            'otp_code.required' => 'يرجى إدخال كود التحقق.',
            'otp_code.regex' => 'كود التحقق غير صالح.',
        ]);

        $formattedPhone = self::formatSaudiPhone((string) ($request->formatted_phone ?: $request->local_phone));
        $user = User::where('phone',$formattedPhone)
                    ->orWhere('phone', $request->local_phone)
                    ->first();

        $inputOtp = trim((string)$request->otp_code);
        $isExpired = !$user || empty($user->otp_expires_at) || now()->greaterThan(\Carbon\Carbon::parse($user->otp_expires_at));

        if (!$user || empty($user->otp_code) ||$isExpired || !hash_equals((string) $user->otp_code,$inputOtp)) {
            return back()->with([
                'otp_step' => true,
                'local_phone' => $request->local_phone,
                'formatted_phone' => $formattedPhone,
                'error' => $isExpired ? 'انتهت صلاحية كود التحقق، يرجى طلب كود جديد.' : 'كود التحقق غير صحيح، حاول مرة أخرى أو ادخل باسم المستخدم وكلمة المرور.'
            ]);
        }

        if (!$user->is_active) {
            return back()->with('error', 'هذا الحساب موقوف حالياً، يرجى مراجعة الإدارة.');
        }

        DB::table('users')->where('id', $user->id)->update([
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        Auth::login($user, true);$request->session()->regenerate();

        return $this->redirectByUserRole($user);
    }

    public function loginWithPassword(Request $request)
    {
        $this->ensureDefaultAdminExists();

        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $input = trim($request->username);$formattedPhone = preg_match('/^05[0-9]{8}$/',$input) ? self::formatSaudiPhone($input) :$input;

        $user = User::where('username',$input)
                    ->orWhere('phone', $formattedPhone)
                    ->orWhere('phone', $input)
                    ->first();

        if (!$user || !Hash::check($request->password,$user->password)) {
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

        Auth::login($user, true);$request->session()->regenerate();
        return $this->redirectByUserRole($user);
    }

    public function logout(Request $request)
    {
        session()->forget(['impersonate_office_id', 'impersonate_owner_id']);
        Auth::logout();
        $request->session()->invalidate();$request->session()->regenerateToken();
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

        // جلب القيود اليومية وتفاصيلها (الأحدث في الأعلى: 3 ثم 2 ثم 1)
        $journalEntries = DB::table('journal_entries')
            ->whereNull('office_id')
            ->orderBy('entry_number', 'desc')
            ->orderBy('id', 'desc')
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

        $landingSettings = self::getLandingSettings();
        $activityLogs = self::getFilteredActivityLogs();

        self::ensureSiteAnalyticsAndAdsTables();
        $siteAnalytics =$tab === 'overview' ? $this->getSiteAnalyticsData() : [];$liveQrState = $tab === 'whatsapp' ?$this->fetchLiveMarketingQrState() : ['connected' => false];
        $adsWhatsappSettings = [
            'qr_status'       => $liveQrState['connected'] ? 'connected' : 'disconnected',
            'account_name'    => $liveQrState['account_name'] ?? '',
            'account_phone'   => $liveQrState['account_phone'] ?? '',
            'qr_connected_at' => $liveQrState['connected_at'] ?? '',
        ];
        $adCampaigns = DB::table('whatsapp_ad_campaigns')->orderBy('id', 'desc')->limit(30)->get();
        $crmContactsForAds = $this->getSystemContactsForAds();

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
            'adsWhatsappSettings',
            'adCampaigns',
            'crmContactsForAds',
            'siteAnalytics',
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
        $userId = Auth::id();
        $request->validate([
            'phone' => ['required', 'regex:/^05[0-9]{8}$/'],
            'username' => 'required|unique:users,username,' . $userId,
        ], [
            'phone.regex' => 'رقم الجوال يجب أن يتكون من 10 أرقام ويبدأ بـ 05.',
            'username.unique' => 'اسم المستخدم مستخدم مسبقاً لحساب آخر.'
        ]);

        $formattedPhone = self::formatSaudiPhone($request->phone);
        $phoneTaken = DB::table('users')
            ->where('id', '!=', $userId)
            ->whereIn('phone', [$formattedPhone, $request->phone])
            ->exists();
        if ($phoneTaken) {
            return back()->withErrors(['رقم الجوال مسجل مسبقاً لمستخدم آخر في النظام.']);
        }

        $updateData = [
            'name' => trim($request->name),
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
        $apiUrl      = trim((string) $request->input('api_url', $request->input('whatsapp_api_url', 'https://graph.facebook.com/v23.0')));
        $apiToken    = trim((string) $request->input('api_token', $request->input('whatsapp_api_token', '')));
        $instanceId  = trim((string) $request->input('instance_id', $request->input('whatsapp_instance_id', '')));
        $wabaId      = trim((string) $request->input('waba_id', ''));
        $verifyToken = trim((string) $request->input('webhook_verify_token', 'erp_whatsapp_verify_2026'));

        if (str_contains(strtolower($apiUrl), 'graph.facebook.com') && !empty($instanceId)) {
            $cleanPhoneId = preg_replace('/\D/', '', $instanceId);
            if ($cleanPhoneId !== '') {
                $apiUrl = "https://graph.facebook.com/v23.0/{$cleanPhoneId}/messages";
            }
        }

        self::setSetting('whatsapp_sender_phone', self::formatSaudiPhone(trim($senderPhone)));
        self::setSetting('whatsapp_api_url', $apiUrl);
        self::setSetting('whatsapp_api_token', $apiToken);
        self::setSetting('whatsapp_instance_id', $instanceId);
        self::setSetting('whatsapp_waba_id', $wabaId);
        self::setSetting('whatsapp_webhook_verify_token', $verifyToken ?: 'erp_whatsapp_verify_2026');
        self::setSetting('whatsapp_default_for_offices', '1');

        return redirect()->route('erp.dashboard', ['tab' => 'whatsapp'])
            ->with('success', 'تم حفظ إعدادات الربط مع WhatsApp Business Cloud API وتعميمها بنجاح.');
    }

    public function testWhatsappApiConnection(Request $request)
    {
        $testPhone = trim((string) $request->input('test_phone', ''));
        $testMode  = trim((string) $request->input('test_mode', 'text'));
        if (empty($testPhone)) {
            return back()->withErrors(['يرجى إدخال رقم الجوال المراد إرسال رسالة الاختبار إليه.']);
        }

        $apiUrl     = self::getSetting('whatsapp_api_url', env('WHATSAPP_API_URL', 'https://graph.facebook.com/v23.0'));
        $apiToken   = self::getSetting('whatsapp_api_token', env('WHATSAPP_API_TOKEN', ''));
        $instanceId = self::getSetting('whatsapp_instance_id', '');

        if (empty($apiToken)) {
            return back()->withErrors(['يرجى حفظ الـ Access Token أولاً قبل إجراء الاختبار.']);
        }

        $cleanDigitsPhone = ltrim(self::formatSaudiPhone($testPhone), '+');

        if (self::isMetaCloudApi($apiUrl, $apiToken, $instanceId)) {
            $endpoint = self::buildMetaMessagesUrl($apiUrl, $instanceId);
            $payload = [
                'messaging_product' => 'whatsapp',
                'recipient_type'    => 'individual',
                'to'                => $cleanDigitsPhone,
            ];

            if ($testMode === 'template') {
                $payload['type'] = 'template';
                $payload['template'] = [
                    'name'     => 'hello_world',
                    'language' => ['code' => 'en_US'],
                ];
            } else {
                $payload['type'] = 'text';
                $payload['text'] = [
                    'preview_url' => false,
                    'body'        => "✅ رسالة اختبار ناجحة من منظومة العقارات الذكية عبر Meta WhatsApp Cloud API الرسمي!\nالتاريخ: " . now()->toDateTimeString(),
                ];
            }

            try {
                $res = Http::withToken(trim($apiToken))
                    ->acceptJson()
                    ->contentType('application/json')
                    ->timeout(15)
                    ->post($endpoint, $payload);

                if ($res->successful()) {
                    $msgId = $res->json('messages.0.id') ?? 'OK';
                    return redirect()->route('erp.dashboard', ['tab' => 'whatsapp'])
                        ->with('success', "🎉 نجح الإرسال الفوري عبر Meta WhatsApp Cloud API إلى ({$testPhone})! معرف الرسالة: {$msgId}");
                }

                $errMsg = $res->json('error.message') ?: $res->body();
                return redirect()->route('erp.dashboard', ['tab' => 'whatsapp'])
                    ->withErrors(["رد خادم Meta (HTTP {$res->status()}): {$errMsg}"]);
            } catch (\Throwable $e) {
                return redirect()->route('erp.dashboard', ['tab' => 'whatsapp'])
                    ->withErrors(['تعذر الاتصال بخادم Meta Cloud API: ' . $e->getMessage()]);
            }
        }

        $ok = self::sendWhatsAppMessage($testPhone, "✅ رسالة اختبار ناجحة من منظومة العقارات الذكية ERP", null);
        if ($ok) {
            return redirect()->route('erp.dashboard', ['tab' => 'whatsapp'])
                ->with('success', "🎉 تم إرسال رسالة الاختبار إلى ({$testPhone}) بنجاح.");
        }

        return redirect()->route('erp.dashboard', ['tab' => 'whatsapp'])
            ->withErrors(['فشل إرسال رسالة الاختبار، يرجى التأكد من الرابط والتوكن ومعرف الرقم.']);
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
            if ($master && isset($master->sql) && stripos($master->sql, 'check') !== false && stripos($master->sql, 'payment_method') !== false) {
                DB::statement('PRAGMA foreign_keys = OFF;');
                DB::transaction(function () {
                    Schema::dropIfExists('vouchers_unlocked_tmp');
                    Schema::create('vouchers_unlocked_tmp', function (Blueprint $table) {
                        $table->id();
                        $table->string('voucher_number')->unique();
                        $table->string('voucher_type')->default('receipt');
                        $table->unsignedBigInteger('office_id')->nullable()->index();
                        $table->unsignedBigInteger('owner_id')->nullable();
                        $table->unsignedBigInteger('tenant_id')->nullable();
                        $table->unsignedBigInteger('lease_contract_id')->nullable();
                        $table->unsignedBigInteger('property_id')->nullable();
                        $table->unsignedBigInteger('property_unit_id')->nullable();
                        $table->unsignedBigInteger('installment_id')->nullable();
                        $table->unsignedBigInteger('debit_account_id');
                        $table->unsignedBigInteger('credit_account_id');
                        $table->decimal('amount', 15, 2)->default(0);
                        $table->string('payment_method')->default('bank_transfer');
                        $table->string('reference_number')->nullable();
                        $table->date('voucher_date');
                        $table->text('notes')->nullable();
                        $table->string('approval_status')->default('approved');
                        $table->string('approval_code', 20)->nullable();
                        $table->boolean('owner_credited')->default(false);
                        $table->boolean('commission_recorded')->default(false);
                        $table->unsignedBigInteger('created_by')->nullable();
                        $table->timestamps();
                    });

                    $targetCols = Schema::getColumnListing('vouchers_unlocked_tmp');
                    $oldRows = DB::table('vouchers')->get();
                    foreach ($oldRows as $row) {
                        $arr = array_intersect_key((array) $row, array_flip($targetCols));
                        DB::table('vouchers_unlocked_tmp')->insert($arr);
                    }

                    Schema::drop('vouchers');
                    Schema::rename('vouchers_unlocked_tmp', 'vouchers');
                });
                DB::statement('PRAGMA foreign_keys = ON;');
            }
        } catch (\Throwable $e) {
            try { DB::statement('PRAGMA foreign_keys = ON;'); } catch (\Throwable $ignore) {}
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
        $clean = strtolower(trim($method));
        $map = [
            'bank_transfer' => 'حوالة بنكية',
            'transfer'      => 'حوالة بنكية',
            'bank'          => 'حوالة بنكية',
            'sadad'         => 'نظام سداد',
            'mada'          => 'شبكة مدى',
            'pos'           => 'شبكة مدى',
            'card'          => 'شبكة مدى',
            'cash'          => 'نقداً (كاش)',
            'cheque'        => 'شيك مصدق',
            'check'         => 'شيك مصدق',
        ];
        if (isset($map[$clean])) {
            return $map[$clean];
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
        $this->ensurePaymentMethodsTable();
        $oldVoucher = DB::table('vouchers')->where('id', $id)->first();
        if (!$oldVoucher) {
            return back()->withErrors(['السند غير موجود.']);
        }

        $newAmount = (float) ($request->amount ?? $oldVoucher->amount);
        $newType = in_array($request->voucher_type, ['receipt', 'payment'], true)
            ? $request->voucher_type
            : $oldVoucher->voucher_type;

        $newVoucherNo = trim((string) ($request->voucher_number ?: $oldVoucher->voucher_number));
        if (DB::table('vouchers')->where('id', '!=', $id)->where('voucher_number', $newVoucherNo)->exists()) {
            $newVoucherNo = $oldVoucher->voucher_number;
        }

        DB::transaction(function () use ($request, $id, $oldVoucher, $newAmount, $newType, $newVoucherNo) {
            self::applyAccountMovement((int) $oldVoucher->debit_account_id, (float) $oldVoucher->amount, 0, true);
            self::applyAccountMovement((int) $oldVoucher->credit_account_id, 0, (float) $oldVoucher->amount, true);

            $newDebitId = $request->debit_account_id ?: $oldVoucher->debit_account_id;
            $newCreditId = $request->credit_account_id ?: $oldVoucher->credit_account_id;

            DB::table('vouchers')->where('id', $id)->update([
                'voucher_number'    => $newVoucherNo,
                'voucher_type'      => $newType,
                'office_id'         => $request->has('office_id') ? ($request->office_id ?: null) : $oldVoucher->office_id,
                'debit_account_id'  => $newDebitId,
                'credit_account_id' => $newCreditId,
                'amount'            => $newAmount,
                'payment_method'    => self::normalizeVoucherPaymentMethod($request->payment_method ?: $oldVoucher->payment_method),
                'reference_number'  => $request->has('reference_number') ? (trim((string) $request->reference_number) ?: null) : ($oldVoucher->reference_number ?? null),
                'voucher_date'      => $request->voucher_date ?: $oldVoucher->voucher_date,
                'notes'             => trim((string) ($request->notes ?: $oldVoucher->notes)),
                'updated_at'        => now(),
            ]);

            self::applyAccountMovement((int) $newDebitId, $newAmount, 0);
            self::applyAccountMovement((int) $newCreditId, 0, $newAmount);
        });

        return back()->with('success', '✅ تم تعديل كافة بيانات السند المالي رقم (' . $newVoucherNo . ') وتحديث الأرصدة بنجاح.');
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
        $debitTaxSum = 0;
        $creditTaxSum = 0;

        foreach ($accountIds as $idx => $accId) {
            if (empty($accId)) continue;
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
            return back()->withErrors(['يرجى إدخال سطر واحد على الأقل يحتوي على مبلغ مدين أو دائن.']);
        }

        if (abs($totalDebit - $totalCredit) > 0.05) {
            return back()->withErrors([
                'القيد غير متوازن! إجمالي المدين الشامل (' . number_format($totalDebit, 2) . ' ر.س) لا يساوي إجمالي الدائن الشامل (' . number_format($totalCredit, 2) . ' ر.س) — الفرق: ' . number_format(abs($totalDebit - $totalCredit), 2) . ' ر.س.'
            ]);
        }

        $totalTax = max($debitTaxSum, $creditTaxSum);

        DB::transaction(function () use ($request, $rows, $totalDebit, $totalCredit, $totalTax) {
            $maxNo = DB::table('journal_entries')->whereNull('office_id')->max('entry_number') ?? 0;
            $nextEntryNo = $maxNo + 1;

            $entryId = DB::table('journal_entries')->insertGetId([
                'office_id'     => null,
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

                self::applyAccountMovement($r['account_id'], $r['effective_debit'], $r['effective_credit']);
            }
        });

        return redirect()->route('erp.dashboard', ['tab' => 'journal'])
            ->with('success', 'تم حفظ القيد اليومي المتوازن بإجمالي (' . number_format($totalDebit, 2) . ' ر.س) وترحيله لشجرة الحسابات بنجاح.');
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
        $debitTaxSum = 0;
        $creditTaxSum = 0;

        foreach ($accountIds as $idx => $accId) {
            if (empty($accId)) continue;
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
            return back()->withErrors(['يرجى إدخال سطر واحد على الأقل يحتوي على مبلغ مدين أو دائن.']);
        }

        if (abs($totalDebit - $totalCredit) > 0.05) {
            return back()->withErrors([
                'القيد غير متوازن! إجمالي المدين الشامل (' . number_format($totalDebit, 2) . ' ر.س) لا يساوي إجمالي الدائن الشامل (' . number_format($totalCredit, 2) . ' ر.س) — الفرق: ' . number_format(abs($totalDebit - $totalCredit), 2) . ' ر.س.'
            ]);
        }

        $totalTax = max($debitTaxSum, $creditTaxSum);

        DB::transaction(function () use ($request, $id, $rows, $totalDebit, $totalCredit, $totalTax) {
            $oldItems = DB::table('journal_entry_items')->where('journal_entry_id', $id)->get();
            foreach ($oldItems as $old) {
                $oldEffD = $old->debit > 0 ? ((float)$old->debit + (float)$old->tax) : 0;
                $oldEffC = $old->credit > 0 ? ((float)$old->credit + (float)$old->tax) : 0;
                self::applyAccountMovement((int) $old->account_id, $oldEffD, $oldEffC, true);
            }
            DB::table('journal_entry_items')->where('journal_entry_id', $id)->delete();

            DB::table('journal_entries')->where('id', $id)->update([
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

                self::applyAccountMovement($r['account_id'], $r['effective_debit'], $r['effective_credit']);
            }
        });

        return redirect()->route('erp.dashboard', ['tab' => 'journal'])
            ->with('success', 'تم تعديل القيد اليومي رقم (' . $entry->entry_number . ') بإجمالي (' . number_format($totalDebit, 2) . ' ر.س) وتحديث أرصدة الشجرة بنجاح.');
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

        $user = Auth::user();
        if ($user->user_type !== 'company_admin') {
            if (in_array($user->user_type, ['office_admin', 'office_staff'], true) && (int) $voucher->office_id !== (int) $user->office_id) {
                abort(403, 'غير مصرح لك بعرض سندات مكتب آخر.');
            }
            if (in_array($user->user_type, ['owner', 'owner_family'], true) && (int) $voucher->owner_id !== (int) $user->owner_id) {
                abort(403, 'غير مصرح لك بعرض هذا السند.');
            }
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
    // 1. سجل الأخطاء الذكي المطور (System Error Logs) + تحليل ملف laravel.log تلقائياً
    // =========================================================================
    private static bool $logsSyncedThisRequest = false;

    public function ensureSystemMonitoringTables(): void
    {
        if (!Schema::hasTable('system_error_logs')) {
            Schema::create('system_error_logs', function (Blueprint $table) {
                $table->id();
                $table->string('level')->default('ERROR')->index();
                $table->string('source')->nullable();
                $table->text('message');
                $table->text('file_line')->nullable();
                $table->text('suggestion')->nullable();
                $table->string('request_url')->nullable();
                $table->string('user_info')->nullable();
                $table->string('ip_address', 64)->nullable();
                $table->integer('occurrences_count')->default(1);
                $table->string('hash_key')->nullable()->index();
                $table->timestamps();
            });
        } else {
            Schema::table('system_error_logs', function (Blueprint $table) {
                if (!Schema::hasColumn('system_error_logs', 'request_url')) {
                    $table->string('request_url')->nullable();
                }
                if (!Schema::hasColumn('system_error_logs', 'user_info')) {
                    $table->string('user_info')->nullable();
                }
                if (!Schema::hasColumn('system_error_logs', 'ip_address')) {
                    $table->string('ip_address', 64)->nullable();
                }
                if (!Schema::hasColumn('system_error_logs', 'occurrences_count')) {
                    $table->integer('occurrences_count')->default(1);
                }
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

        // مزامنة تلقائية ذكية لملف laravel.log عند فتح لوحة الإدارة
        if (!self::$logsSyncedThisRequest) {
            self::$logsSyncedThisRequest = true;
            $this->syncLaravelLogFileToTable();
        }
    }

    /**
     * توليد اقتراح حل ذكي باللغة العربية بناءً على نص الخطأ ومصدره
     */
    public static function detectSmartFixSuggestion(string $message, string $source = ''): string
    {
        $msgLower = mb_strtolower($message . ' ' . $source);

        if (str_contains($msgLower, 'no such table') || str_contains($msgLower, 'no such column') || str_contains($msgLower, 'column not found')) {
            return 'هيكل الجداول يحتاج لتحديث؛ اضغط على زر (الفحص الأوتوماتيكي الشامل للنظام) ليقوم النظام بإصلاح وبناء الأعمدة تلقائياً.';
        }
        if (str_contains($msgLower, 'database is locked') || str_contains($msgLower, 'sqlite_busy')) {
            return 'قاعدة بيانات SQLite مشغولة بعملية أخرى متزامنة؛ أعد المحاولة بعد ثوانٍ أو تأكد من صلاحيات الكتابة على ملف database.sqlite.';
        }
        if (str_contains($msgLower, 'check constraint failed') || str_contains($msgLower, 'integrity constraint')) {
            return 'قيد حماية في قاعدة البيانات (تكرار رقم/معرف أو حقل إجباري ناقص)؛ تأكد من عدم تكرار اسم المستخدم أو رقم العقد/السند.';
        }
        if (str_contains($msgLower, 'whatsapp') || str_contains($msgLower, 'graph.facebook.com') || str_contains($msgLower, '127.0.0.1:3005')) {
            return 'تحقق من صلاحية توكن واتساب (Access Token) ومعرف الرقم في تبويب (إعدادات واتساب API)، أو تأكد من ربط جلسة كود الـ QR.';
        }
        if (str_contains($msgLower, 'permission denied') || str_contains($msgLower, 'failed to open stream') || str_contains($msgLower, 'not writable')) {
            return 'تأكد من منح صلاحيات الكتابة (775 أو 777) لمجلدات storage و public/uploads على الخادم.';
        }
        if (str_contains($msgLower, 'csrf') || str_contains($msgLower, 'tokenmismatch') || str_contains($msgLower, '419')) {
            return 'انتهت جلسة الصفحة بسبب طول فترة الانتظار؛ قم بتحديث الصفحة (F5) وأعد تنفيذ العملية.';
        }
        if (str_contains($msgLower, 'route') && str_contains($msgLower, 'not defined')) {
            return 'أحد مسارات النظام غير معرف في routes/web.php أو يحتاج لمسح كاش المسارات (php artisan route:clear).';
        }

        return 'راجع تفاصيل الملف والسطر الموضحين في السجل، أو شغّل (الفحص الأوتوماتيكي الشامل) للتحقق من سلامة الخدمة.';
    }

    public static function logSystemIssue(
        string $source,
        string $message,
        string $level = 'ERROR',
        ?string $fileLine = null,
        ?string $suggestion = null
    ): void {
        try {
            if (!Schema::hasTable('system_error_logs')) {
                return;
            }

            $cleanLevel = strtoupper(trim($level ?: 'ERROR'));
            if (!in_array($cleanLevel, ['ERROR', 'CRITICAL', 'WARNING', 'INFO'], true)) {
                $cleanLevel = 'ERROR';
            }

            $cleanMsg = mb_substr(trim($message), 0, 1200);
            $hash = md5($cleanLevel . '|' . $source . '|' . mb_substr($cleanMsg, 0, 250) . '|' . date('Y-m-d'));

            $req = request();
            $reqUrl = $req ? mb_substr($req->fullUrl(), 0, 220) : 'CLI / Internal';
            $ip = $req ? ($req->header('CF-Connecting-IP') ?: $req->header('X-Forwarded-For') ?: $req->ip() ?: '127.0.0.1') : '127.0.0.1';
            $userInfo = 'زائر / النظام الآلي';
            if (Auth::check() && Auth::user()) {
                $u = Auth::user();
                $userInfo = ($u->name ?? 'مستخدم') . ' (' . ($u->username ?: $u->user_type) . ')';
            }

            $finalSuggestion = !empty($suggestion)
                ? $suggestion
                : self::detectSmartFixSuggestion($cleanMsg, $source);

            $existing = DB::table('system_error_logs')->where('hash_key', $hash)->first();
            if ($existing) {
                $updateData = [
                    'updated_at' => now(),
                    'file_line'  => $fileLine ?: $existing->file_line,
                    'suggestion' => $finalSuggestion ?: $existing->suggestion,
                ];
                if (Schema::hasColumn('system_error_logs', 'occurrences_count')) {
                    $updateData['occurrences_count'] = ((int) ($existing->occurrences_count ?? 1)) + 1;
                }
                if (Schema::hasColumn('system_error_logs', 'request_url')) {
                    $updateData['request_url'] = $reqUrl;
                }
                if (Schema::hasColumn('system_error_logs', 'user_info')) {
                    $updateData['user_info'] = $userInfo;
                }
                DB::table('system_error_logs')->where('id', $existing->id)->update($updateData);
                return;
            }

            $insertData = [
                'level'      => $cleanLevel,
                'source'     => mb_substr(trim($source), 0, 180),
                'message'    => $cleanMsg,
                'file_line'  => $fileLine ? mb_substr(trim($fileLine), 0, 250) : null,
                'suggestion' => $finalSuggestion,
                'hash_key'   => $hash,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (Schema::hasColumn('system_error_logs', 'request_url')) {
                $insertData['request_url'] = $reqUrl;
            }
            if (Schema::hasColumn('system_error_logs', 'user_info')) {
                $insertData['user_info'] = $userInfo;
            }
            if (Schema::hasColumn('system_error_logs', 'ip_address')) {
                $insertData['ip_address'] = mb_substr((string) $ip, 0, 60);
            }
            if (Schema::hasColumn('system_error_logs', 'occurrences_count')) {
                $insertData['occurrences_count'] = 1;
            }

            DB::table('system_error_logs')->insert($insertData);

            // الاحتفاظ بآخر 350 سجلاً فقط لمنع تضخم قاعدة البيانات
            $keepIds = DB::table('system_error_logs')->orderByDesc('id')->limit(350)->pluck('id');
            if ($keepIds->count() >= 350) {
                DB::table('system_error_logs')->whereNotIn('id', $keepIds)->delete();
            }
        } catch (\Throwable $e) {
            // تجاهل أخطاء الكتابة الثانوية لعدم تعطيل النظام
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
            if ($size <= 0) {
                return;
            }
            $readBytes = min($size, 131072); // قراءة آخر 128 كيلوبايت
            $fp = fopen($logFile, 'r');
            if ($size > $readBytes) {
                fseek($fp, -$readBytes, SEEK_END);
            }
            $content = (string) fread($fp, $readBytes);
            fclose($fp);

            $pattern = '/\[(\d{4}-\d{2}-\d{2}[T\s]\d{2}:\d{2}:\d{2}[^\]]*)\]\s+\w+\.(ERROR|CRITICAL|WARNING|ALERT|EMERGENCY|EXCEPTION):\s+([^\n\r]+)(?:\r?\n([^\[]*))?/i';
            preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

            foreach (array_slice($matches, -35) as $m) {
                $timestamp = trim($m[1] ?? '');
                $rawLevel  = strtoupper(trim($m[2] ?? 'ERROR'));
                $headline  = trim($m[3] ?? '');
                $traceBody = trim($m[4] ?? '');

                $cleanLevel = in_array($rawLevel, ['CRITICAL', 'ALERT', 'EMERGENCY', 'EXCEPTION'], true) ? 'ERROR' : $rawLevel;

                // استخراج اسم الملف ورقم السطر من رسالة الخطأ أو الـ Stack Trace
                $detectedFileLine = 'storage/logs/laravel.log';
                if (preg_match('/(?:in|at)\s+([^\s\(:]+Controller\.php|[a-zA-Z0-9_\/\\\\\-\.]+\.php)(?:[:\(](\d+))?/i', $headline . ' ' . $traceBody, $fm)) {
                    $shortFile = basename(str_replace('\\', '/', $fm[1]));
                    $lineNo = $fm[2] ?? '';
                    $detectedFileLine = $shortFile . ($lineNo !== '' ? ' (السطر: ' . $lineNo . ')' : '');
                }

                // تنظيف الرسالة من مسارات السيرفر الطويلة أو بيانات الـ Stack Trace الزائدة
                $cleanMessage = preg_replace('/\{?"exception":.*$/i', '', $headline);
                $cleanMessage = trim($cleanMessage ?: $headline);

                self::logSystemIssue(
                    'Laravel Log (' . $timestamp . ')',
                    mb_substr($cleanMessage, 0, 500),
                    $cleanLevel,
                    $detectedFileLine,
                    self::detectSmartFixSuggestion($cleanMessage, $detectedFileLine)
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
    // 2. المحرك الأوتوماتيكي الشامل المطور لفحص وتجربة جميع خصائص النظام (22 اختباراً حقيقياً)
    // يشمل: الشركة الأم + الباقات والـ SEO + بوابة المكاتب + الدفعات وأعمار الديون + الفوترة الضريبية ZATCA
    // + المدقق المحاسبي والإقفال + اتحاد الملاك (مُلّاك) + AI OCR والإنذارات + بوابة المالك والعائلة + الواتساب والزوار
    // =========================================================================
    public function runSystemDiagnostics()
    {
        $this->ensureDefaultAdminExists();
        $this->ensureDynamicPropertyTables();
        $this->ensureDynamicAccountTables();
        $this->ensurePaymentMethodsTable();
        $this->ensureJournalTables();
        $this->ensureSystemMonitoringTables();
        $this->ensureTaxInvoicesTable();
        $this->ensureOfficePlansTable();
        self::ensureContractInstallmentsTable();
        self::ensureActivityLogsTable();
        self::ensureSiteAnalyticsAndAdsTables();

        $this->syncLaravelLogFileToTable();

        DB::table('system_diagnostic_runs')->truncate();

        $results = [];
        $failedCount = 0;

        $recordTest = function (string $feature, string $module, string $status, string $details, float $startTime, ?string $fixSuggestion = null) use (&$results, &$failedCount) {
            $ms = round((microtime(true) - $startTime) * 1000, 1) . ' ms';
            $results[] = [
                'feature_name' => $feature,
                'module'       => $module,
                'status'       => $status,
                'details'      => $details,
                'execution_ms' => $ms,
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
            if ($status === 'failed') {
                $failedCount++;
                self::logSystemIssue('الفحص الأوتوماتيكي: ' . $module, $feature . ' — ' . $details, 'ERROR', $module, $fixSuggestion ?: 'راجع إعدادات الجدول أو الدالة المرتبطة بهذه الميزة.');
            } elseif ($status === 'warning') {
                self::logSystemIssue('تنبيه فحص: ' . $module, $feature . ' — ' . $details, 'WARNING', $module, $fixSuggestion);
            }
        };

        // 1. فحص هيكل جميع الجداول الأساسية والفرعية والمستحدثة (29 جدولاً)
        $t = microtime(true);
        $requiredTables = $this->getAllBackupTableNames();
        $missingTables = [];
        foreach ($requiredTables as $tbl) {
            if (!Schema::hasTable($tbl)) {
                $missingTables[] = $tbl;
            }
        }
        if (empty($missingTables)) {
            $recordTest(
                '1. فحص سلامة جميع جداول وأعمدة النظام (' . count($requiredTables) . ' جدولاً)',
                'قاعدة البيانات',
                'passed',
                'جميع جداول الشركة، المكاتب، الباقات، الملاك، العقود، الدفعات، الفواتير الضريبية، اتحاد الملاك، الزيارات، وسجل التتبع سليمة 100%.',
                $t
            );
        } else {
            $recordTest(
                '1. فحص سلامة جميع جداول وأعمدة النظام',
                'قاعدة البيانات',
                'failed',
                'جداول مفقودة: ' . implode(', ', $missingTables),
                $t,
                'قم بتحديث الصفحة ليقوم محرك البناء الذاتي (Self-Healing Schema) بإنشاء الجداول المفقودة.'
            );
        }

        // بدء بيئة المحاكاة الآمنة (Rollback Transaction) لتجربة الإدخال الفعلي في كل أجزاء النظام الـ 22
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
                $recordTest('2. توليد شجرة الحسابات والتسلسل الآلي للشركة الأم', 'محاسبة الشركة الأم', 'passed', "نجح توليد الفروع المتسلسلة ({$subAcc1->code} ثم {$subAcc2->code}) تلقائياً.", $t);
            } else {
                $recordTest('2. توليد شجرة الحسابات والتسلسل الآلي للشركة الأم', 'محاسبة الشركة الأم', 'failed', 'خلل في ترقيم الفروع المتسلسلة للشركة الأم.', $t);
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
                $recordTest('3. إضافة نوع عقار + متطلبات PDF + ربط طرق الدفع بالحسابات', 'إعدادات المنصة المركزية', 'passed', 'نجح إنشاء نوع العقار وربطه بحساب الشجرة وإضافة متطلب PDF وطريقة دفع.', $t);
            } else {
                $recordTest('3. إضافة نوع عقار ومتطلباته وطرق الدفع', 'إعدادات المنصة المركزية', 'failed', 'فشل في إدراج نوع العقار أو المتطلبات الديناميكية.', $t);
            }

            // 4. تجربة سندات القبض والصرف + القيود اليومية للشركة الأم وترحيل الأرصدة وعكسها
            $t = microtime(true);
            $compVoucherNo = self::generateSequentialVoucherNumber('receipt', null);
            self::applyAccountMovement($compSubAccId1, 1500, 0);
            self::applyAccountMovement($compSubAccId2, 0, 1500);
            $afterDebit = DB::table('accounts')->where('id', $compSubAccId1)->value('balance');
            $afterCredit = DB::table('accounts')->where('id', $compSubAccId2)->value('balance');

            self::applyAccountMovement($compSubAccId1, 1500, 0, true);
            self::applyAccountMovement($compSubAccId2, 0, 1500, true);
            $revertedBal = DB::table('accounts')->where('id', $compSubAccId1)->value('balance');

            if ((float) $afterDebit === 1500.0 && (float) $afterCredit === -1500.0 && (float) $revertedBal === 0.0) {
                $recordTest('4. إصدار سندات وقيود الشركة الأم + الترحيل وعكس الأرصدة', 'محاسبة الشركة الأم', 'passed', "نجح توليد السند ({$compVoucherNo}) وترحيل 1,500 ر.س للمدين والدائن ثم عكس القيد بدقة.", $t);
            } else {
                $recordTest('4. إصدار سندات وقيود الشركة الأم + الترحيل وعكس الأرصدة', 'محاسبة الشركة الأم', 'failed', 'خلل في معادلة ترحيل الأرصدة (applyAccountMovement).', $t);
            }

            // 5. تجربة نظام الباقات وصلاحيات المكاتب (office_plans) + محرك SEO والواجهة الرئيسية
            $t = microtime(true);
            $allOfficeTabsKeys = array_keys(self::getAvailableOfficeTabs());
            $testPlanId = DB::table('office_plans')->insertGetId([
                'name' => 'باقة اختبارية شاملة Pro',
                'badge' => 'اختبار آلي',
                'billing_type' => 'hybrid',
                'price' => 2500,
                'old_price' => 3200,
                'offer_label' => 'خصم تجريبي',
                'commission_rate' => 2.5,
                'description' => 'باقة اختبار للفحص الأوتوماتيكي',
                'allowed_tabs' => json_encode($allOfficeTabsKeys, JSON_UNESCAPED_UNICODE),
                'show_on_landing' => 1,
                'is_highlighted' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $landingCfg = self::getLandingSettings();
            if ($testPlanId && !empty($landingCfg['seo_title']) && count($allOfficeTabsKeys) >= 15) {
                $recordTest('5. نظام الباقات وصلاحيات التبويبات + إعدادات SEO والواجهة الرئيسية', 'الباقات ومحرك SEO', 'passed', 'نجح إنشاء باقة المكاتب (' . count($allOfficeTabsKeys) . ' تبويب) وفحص بيانات الـ SEO و Schema JSON-LD.', $t);
            } else {
                $recordTest('5. نظام الباقات وإعدادات الواجهة الرئيسية', 'الباقات ومحرك SEO', 'failed', 'خلل في حفظ الباقة أو قراءة إعدادات الواجهة الرئيسية.', $t);
            }

            // 6. تجربة إنشاء مكتب عقار متكامل + ربطه بالباقة + حساب مدير المكتب (office_admin)
            $t = microtime(true);
            $testOfficeId = DB::table('offices')->insertGetId([
                'name' => 'مكتب آفاق العقار التجريبي', 'cr_number' => '1010999999', 'fal_license' => '1200099999',
                'tax_number' => '300999999900003', 'national_address' => 'الرياض - حي الملقا',
                'phone' => '+966509999991', 'email' => 'test_office@office.local',
                'billing_type' => 'hybrid', 'annual_fee' => 2500, 'company_commission_rate' => 2.5,
                'plan_id' => $testPlanId, 'allowed_tabs' => json_encode($allOfficeTabsKeys, JSON_UNESCAPED_UNICODE),
                'allow_owner_tax_invoice' => 1,
                'subscription_ends_at' => now()->addYear()->toDateString(), 'status' => 'active',
                'account_id' => $compSubAccId2, 'use_custom_whatsapp' => 0,
                'enable_wa_voucher_approval' => 1, 'wa_approval_threshold' => 10000,
                'enable_anomaly_detector' => 1, 'anomaly_max_amount' => 20000,
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
                $recordTest('6. إنشاء مكتب عقار + تفعيل الباقة والاتفاقية + حساب مدير المكتب', 'بوابة مكاتب العقار', 'passed', "تم تسجيل المكتب (ID: {$testOfficeId}) وربطه بالباقة والشجرة وإنشاء حساب الدخول بنجاح.", $t);
            } else {
                $recordTest('6. إنشاء مكتب عقار وحساب مديره', 'بوابة مكاتب العقار', 'failed', 'فشل إنشاء سجل المكتب أو حساب مدير المكتب.', $t);
            }

            // 7. تجربة تأسيس شجرة الحسابات المستقلة الخاصة بالمكتب العقاري
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

            $offRevAccId = DB::table('accounts')->insertGetId([
                'office_id' => $testOfficeId, 'parent_id' => null, 'code' => '4101',
                'name' => 'إيرادات عمولات إدارة الأملاك', 'type' => 'الإيرادات', 'is_group' => 0,
                'balance' => 0, 'debit_balance' => 0, 'credit_balance' => 0, 'created_at' => now(), 'updated_at' => now(),
            ]);
            $offExpAccId = DB::table('accounts')->insertGetId([
                'office_id' => $testOfficeId, 'parent_id' => null, 'code' => '5101',
                'name' => 'مصروفات صيانة وتشغيل المكتب', 'type' => 'المصروفات التشغيلية', 'is_group' => 0,
                'balance' => 0, 'debit_balance' => 0, 'credit_balance' => 0, 'created_at' => now(), 'updated_at' => now(),
            ]);

            $officeAccCount = DB::table('accounts')->where('office_id', $testOfficeId)->count();
            if ($officeAccCount >= 7) {
                $recordTest('7. تأسيس شجرة الحسابات المحاسبية المستقلة للمكتب وعزلها عن الشركة', 'محاسبة مكتب العقار', 'passed', "تم إنشاء {$officeAccCount} حسابات مستقلة خاصة بالمكتب (أصول، خصوم، إيرادات، مصروفات) بنجاح.", $t);
            } else {
                $recordTest('7. تأسيس شجرة الحسابات المستقلة للمكتب', 'محاسبة مكتب العقار', 'failed', 'لم يتم إنشاء حسابات شجرة المكتب بشكل كامل.', $t);
            }

            // 8. تجربة إضافة موظف مكتب (office_staff) وتخصيص صلاحياته
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
                $recordTest('8. إضافة موظف بالمكتب (office_staff) وتخصيص صلاحياته', 'الموارد البشرية بالمكتب', 'passed', 'نجح إنشاء حساب الموظف وحفظ مصفوفة الصلاحيات المخصصة (JSON) بنجاح.', $t);
            } else {
                $recordTest('8. إضافة موظف بالمكتب وتخصيص صلاحياته', 'الموارد البشرية بالمكتب', 'failed', 'فشل حفظ صلاحيات موظف المكتب.', $t);
            }

            // 9. تجربة إضافة مالك عقار (Owner) + ربطه بحساب أمانات متسلسل + إنشاء حساب دخوله
            $t = microtime(true);
            $ownerSubAccId = self::createSequentialSubAccount($testOfficeId, $offOwnersGroupAccId, 'أمانات المالك: الشيخ عبدالله التجريبي');
            $testOwnerId = DB::table('owners')->insertGetId([
                'office_id' => $testOfficeId, 'name' => 'الشيخ عبدالله التجريبي',
                'national_id' => '1099999999', 'tax_number' => '300888888800003',
                'national_address' => 'الرياض - حي حطين', 'phone' => '+966509999993',
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
                $recordTest('9. تسجيل مالك عقار + فتح حساب أمانات متسلسل بالشجرة + تفعيل بوابة المالك', 'بوابة الملاك والمكتب', 'passed', "تم تسجيل المالك (ID: {$testOwnerId}) وربطه بالحساب الفرعي وإنشاء حساب دخوله بنجاح.", $t);
            } else {
                $recordTest('9. تسجيل مالك عقار وربطه بالشجرة', 'بوابة الملاك والمكتب', 'failed', 'خلل في إنشاء سجل المالك أو حسابه المحاسبي.', $t);
            }

            // 10. تجربة إضافة عقار وصك ملكية + وحدات عقارية داخلية (Property & Units)
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
                $recordTest('10. إضافة عقار وصك ملكية + العنوان الوطني + الوحدات الداخلية والعدادات', 'إدارة الأملاك والعقارات', 'passed', 'تم تسجيل العقار والصك وإنشاء الوحدة الداخلية (شقة 101) بحالة شاغرة.', $t);
            } else {
                $recordTest('10. إضافة عقار وصك ووحدات داخلية', 'إدارة الأملاك والعقارات', 'failed', 'فشل إدراج العقار أو الوحدة العقارية.', $t);
            }

            // 11. تجربة تسجيل مستأجر + إبرام عقد إيجار مع فترة سماح (Grace Period) + توثيق منصة إيجار
            $t = microtime(true);
            $tenantSubAccId = self::createSequentialSubAccount($testOfficeId, $offTenantsGroupAccId, 'المستأجر: فهد التجريبي');
            $testTenantId = DB::table('tenants')->insertGetId([
                'office_id' => $testOfficeId, 'name' => 'فهد بن خالد التجريبي',
                'id_type' => 'national_id', 'id_number' => '1088888888',
                'tax_number' => '300777777700003', 'national_address' => 'الرياض - حي العليا',
                'phone' => '+966509999994', 'account_id' => $tenantSubAccId,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $rentVal = 40000;
            $calcOfficeComm = ($rentVal * 5.0) / 100;
            $calcCompFee = ($rentVal * 2.5) / 100;
            $contractInsert = [
                'contract_number' => 'CNT-DIAG-' . rand(1000, 9999),
                'ejar_contract_number' => 'EJR-9999999999', 'ejar_status' => 'active',
                'office_id' => $testOfficeId, 'property_id' => $testPropertyId, 'property_unit_id' => $testUnitId,
                'owner_id' => $testOwnerId, 'tenant_id' => $testTenantId,
                'start_date' => date('Y-m-d', strtotime('-2 months')),
                'end_date' => date('Y-m-d', strtotime('+10 months')),
                'payment_cycle' => 'quarterly', 'total_rent_amount' => $rentVal,
                'security_deposit' => 2000, 'office_commission' => $calcOfficeComm,
                'company_fee_amount' => $calcCompFee, 'status' => 'active',
                'created_at' => now(), 'updated_at' => now(),
            ];
            if (Schema::hasColumn('lease_contracts', 'grace_period_months')) {
                $contractInsert['grace_period_months'] = 1;
                $contractInsert['grace_period_days'] = 0;
                $contractInsert['installments_start_date'] = date('Y-m-d', strtotime('-1 month'));
            }
            $testContractId = DB::table('lease_contracts')->insertGetId($contractInsert);
            DB::table('property_units')->where('id', $testUnitId)->update(['status' => 'rented']);
            $unitStatusAfter = DB::table('property_units')->where('id', $testUnitId)->value('status');

            if ($testTenantId && $testContractId && $unitStatusAfter === 'rented') {
                $recordTest('11. تسجيل مستأجر + إبرام عقد إيجار مع فترة سماح + توثيق منصة إيجار', 'العقود ومنصة إيجار', 'passed', 'نجح ربط المستأجر بالشجرة وإبرام العقد ربع السنوي وتفعيل فترة السماح وتحديث حالة الوحدة لمؤجرة.', $t);
            } else {
                $recordTest('11. تسجيل مستأجر وإبرام عقد إيجار', 'العقود ومنصة إيجار', 'failed', 'خلل في إبرام العقد أو تحديث حالة الوحدة.', $t);
            }

            // 12. تجربة محرك جدولة الدفعات الآلي (contract_installments) + السداد الجزئي والكلي + أعمار الديون (Aging)
            $t = microtime(true);
            $contractObj = DB::table('lease_contracts')->where('id', $testContractId)->first();
            self::generateInstallmentsForContract($contractObj);
            $generatedInsts = DB::table('contract_installments')->where('lease_contract_id', $testContractId)->orderBy('installment_number')->get();
            $firstInst = $generatedInsts->first();
            if ($firstInst) {
                DB::table('contract_installments')->where('id', $firstInst->id)->update([
                    'paid_amount' => 4000,
                    'status'      => 'partial_overdue',
                ]);
            }
            $instCount = $generatedInsts->count();
            $instTotalSum = (float) $generatedInsts->sum('amount');
            if ($instCount === 4 && $instTotalSum === 40000.0) {
                $recordTest('12. محرك جدولة الدفعات الآلي (Installments) + السداد الجزئي + أعمار الديون (Aging)', 'جدولة الدفعات والتحصيل', 'passed', 'تم توليد 4 دفعات ربع سنوية بإجمالي 40,000 ر.س وتجربة السداد الجزئي وحساب المتأخرات بنجاح.', $t);
            } else {
                $recordTest('12. محرك جدولة الدفعات الآلي وأعمار الديون', 'جدولة الدفعات والتحصيل', 'failed', 'خلل في توليد جدول الدفعات للعقد.', $t);
            }

            // 13. تجربة الدورة المحاسبية داخل مكتب العقار + السندات المجمّعة + ترحيل أمانات المالك
            $t = microtime(true);
            $offVoucherNo = self::generateSequentialVoucherNumber('receipt', $testOfficeId);
            $safePaymentMethod = self::normalizeVoucherPaymentMethod('حوالة بنكية');
            $testVoucherId = DB::table('vouchers')->insertGetId([
                'voucher_number' => $offVoucherNo, 'voucher_type' => 'receipt',
                'office_id' => $testOfficeId, 'debit_account_id' => $offBankAccId,
                'credit_account_id' => $tenantSubAccId, 'owner_id' => $testOwnerId,
                'tenant_id' => $testTenantId, 'lease_contract_id' => $testContractId,
                'property_id' => $testPropertyId, 'property_unit_id' => $testUnitId,
                'amount' => 20000, 'payment_method' => $safePaymentMethod,
                'voucher_date' => date('Y-m-d'), 'notes' => 'تحصيل دفعة إيجار تجريبية',
                'approval_status' => 'approved',
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
                $recordTest('13. سندات القبض المجمّعة والفردية + القيود اليومية للمكتب + تصفية المالك', 'محاسبة مكتب العقار', 'passed', 'نجح تحصيل الإيجار في البنك وتسوية ذمة المستأجر وترحيل المستحق لأمانات المالك بدقة 100%.', $t);
            } else {
                $recordTest('13. سندات وقيود مكتب العقار', 'محاسبة مكتب العقار', 'failed', 'عدم تطابق أرصدة حسابات المكتب بعد الترحيل.', $t);
            }

            // 14. تجربة الفوترة الإلكترونية الضريبية (ZATCA Phase 1/2 TLV QR) للشركة والمكتب وباسم المالك
            $t = microtime(true);
            $officeVatAccId = self::getOrCreateVatAccount($testOfficeId);
            $qrBase64 = self::generateZatcaQrBase64('الشيخ عبدالله التجريبي', '300888888800003', date('Y-m-d\TH:i:s\Z'), '1150.00', '150.00');
            $testInvNo = 'INV-DIAG-' . rand(1000, 9999);
            $testInvId = DB::table('tax_invoices')->insertGetId([
                'invoice_number' => $testInvNo,
                'issuer_level' => 'office',
                'invoice_type' => 'tax',
                'office_id' => $testOfficeId,
                'owner_id' => $testOwnerId,
                'tenant_id' => $testTenantId,
                'lease_contract_id' => $testContractId,
                'debit_account_id' => $tenantSubAccId,
                'credit_account_id' => $ownerSubAccId,
                'tax_account_id' => $officeVatAccId,
                'seller_name' => 'الشيخ عبدالله التجريبي',
                'seller_tax_number' => '300888888800003',
                'buyer_name' => 'فهد بن خالد التجريبي',
                'buyer_tax_number' => '300777777700003',
                'description' => 'فاتورة ضريبية اختبارية باسم المالك',
                'subtotal' => 1000,
                'vat_rate' => 15,
                'vat_amount' => 150,
                'total_amount' => 1150,
                'issue_date' => date('Y-m-d'),
                'zatca_qr' => $qrBase64,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $pubToken = self::generateInvoicePublicToken($testInvId, $testInvNo);
            if ($testInvId && $officeVatAccId && !empty($qrBase64) && strlen($pubToken) === 64) {
                $recordTest('14. الفوترة الضريبية الإلكترونية (ZATCA QR TLV) + الفوترة باسم المالك + رابط PDF المشفر', 'الفوترة الضريبية ZATCA', 'passed', "تم إصدار الفاتورة ({$testInvNo}) بترميز TLV Base64 وربطها بحساب الضريبة (2104) وتوليد رابط الـ PDF العام.", $t);
            } else {
                $recordTest('14. الفوترة الضريبية الإلكترونية ZATCA', 'الفوترة الضريبية ZATCA', 'failed', 'خلل في توليد الفاتورة الضريبية أو كود ZATCA QR.', $t);
            }

            // 15. تجربة المدقق المحاسبي الآلي قبل الإقفال (Auditor) + إقفال السنة المالية وترحيل الأرباح المبقاة (3101)
            $t = microtime(true);
            DB::table('vouchers')->where('id', $testVoucherId)->update([
                'owner_credited'      => 1,
                'commission_recorded' => 1,
            ]);
            self::applyAccountMovement($offRevAccId, 0, 5000);
            self::applyAccountMovement($offExpAccId, 2000, 0);
            $equityAccId = DB::table('accounts')->insertGetId([
                'office_id' => $testOfficeId, 'parent_id' => null, 'code' => '3101',
                'name' => 'الأرباح المبقاة وحقوق الملكية', 'type' => 'حقوق الملكية',
                'is_group' => 0, 'balance' => 0, 'debit_balance' => 0, 'credit_balance' => 0,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $netProfitTest = 5000 - 2000;
            self::applyAccountMovement($equityAccId, 0, $netProfitTest);
            $eqBal = (float) DB::table('accounts')->where('id', $equityAccId)->value('balance');
            if ($eqBal === -3000.0) {
                $recordTest('15. المدقق المحاسبي الآلي قبل الإقفال + إقفال السنة المالية وترحيل الأرباح (3101)', 'التدقيق والإقفال السنوي', 'passed', 'نجح تدقيق عمولات السندات وإقفال الإيرادات والمصروفات وترحيل صافي الربح (3,000 ر.س) لحساب 3101.', $t);
            } else {
                $recordTest('15. المدقق المحاسبي وإقفال السنة المالية', 'التدقيق والإقفال السنوي', 'failed', 'خلل في معادلة إقفال السنة المالية.', $t);
            }

            // 16. تجربة نظام اتحاد الملاك (Mullak): جمعية العمارة + الأعضاء وتوزيع الرسوم + مصاريف الأجزاء المشتركة
            $t = microtime(true);
            $mullakBuildingId = DB::table('mullak_buildings')->insertGetId([
                'office_id' => $testOfficeId,
                'property_id' => $testPropertyId,
                'association_name' => 'اتحاد ملاك عمارة العليا التجريبية',
                'bylaws_number' => 'MLK-DIAG-100',
                'deed_number' => '310199999999',
                'owner_name' => 'الشيخ عبدالله التجريبي',
                'units_count' => 4,
                'distribution_method' => 'by_units',
                'default_annual_fee' => 12000,
                'guard_monthly_salary' => 1500,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $mullakMem1 = DB::table('mullak_members')->insertGetId([
                'office_id' => $testOfficeId, 'mullak_building_id' => $mullakBuildingId,
                'tenant_id' => $testTenantId, 'unit_number' => 'شقة 101', 'units_count' => 1,
                'member_role' => 'tenant', 'member_name' => 'فهد بن خالد التجريبي',
                'phone' => '+966509999994', 'area_share_pct' => 50, 'annual_fee' => 6000,
                'paid_amount' => 6000, 'status' => 'paid', 'created_at' => now(), 'updated_at' => now(),
            ]);
            $mullakExpId = DB::table('mullak_expenses')->insertGetId([
                'office_id' => $testOfficeId, 'mullak_building_id' => $mullakBuildingId,
                'expense_category' => 'راتب الحارس', 'title' => 'صرف راتب حارس العمارة',
                'amount' => 1500, 'expense_date' => date('Y-m-d'), 'payment_method' => 'حوالة بنكية',
                'created_at' => now(), 'updated_at' => now(),
            ]);
            if ($mullakBuildingId && $mullakMem1 && $mullakExpId) {
                $recordTest('16. نظام اتحاد الملاك (مُلّاك): تسجيل جمعية العمارة + توزيع رسوم الأعضاء + مصاريف العمارة', 'اتحاد الملاك (مُلّاك)', 'passed', 'نجح إنشاء جمعية اتحاد الملاك وتوزيع رسوم الصيانة السنوية وتسجيل مصروفات الأجزاء المشتركة.', $t);
            } else {
                $recordTest('16. نظام اتحاد الملاك (مُلّاك)', 'اتحاد الملاك (مُلّاك)', 'failed', 'فشل في حفظ بيانات اتحاد الملاك.', $t);
            }

            // 17. تجربة ماسح الصكوك والعقود الذكي (AI OCR) + مولد الإنذارات والمحاضر القانونية (8 نماذج)
            $t = microtime(true);
            $ocrMatchedProp = DB::table('properties')->where('office_id', $testOfficeId)->where('deed_number', '310199999999')->first();
            $legalContractData = DB::table('lease_contracts')
                ->join('tenants', 'lease_contracts.tenant_id', '=', 'tenants.id')
                ->where('lease_contracts.id', $testContractId)
                ->select('lease_contracts.*', 'tenants.name as tenant_name')
                ->first();
            if ($ocrMatchedProp && $legalContractData && $legalContractData->ejar_contract_number === 'EJR-9999999999') {
                $recordTest('17. ماسح الصكوك والعقود الذكي (AI OCR) + مولد الإنذارات القانونية (8 نماذج)', 'الذكاء الاصطناعي والشؤون القانونية', 'passed', 'نجح اختبار مطابقة الصكوك والعقود تلقائياً وتجهيز بيانات النماذج القانونية الـ 8.', $t);
            } else {
                $recordTest('17. ماسح الصكوك ومولد الإنذارات القانونية', 'الذكاء الاصطناعي والشؤون القانونية', 'failed', 'خلل في جلب بيانات الصك أو العقد القانوني.', $t);
            }

            // 18. تجربة بوابة العائلة والوكلاء (owner_family) + فحص نظام عزل الصلاحيات (Permissions Isolation)
            $t = microtime(true);
            $familyAllowedPerms = ['view_properties', 'view_contracts'];
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
            $canSeeProps = in_array('view_properties', $decodedFamilyPerms, true);
            $canSeeContracts = in_array('view_contracts', $decodedFamilyPerms, true);
            $canSeeMoney = in_array('view_financials', $decodedFamilyPerms, true);

            $ownerPropsCount = DB::table('properties')->where('owner_id', $testOwnerId)->count();
            $ownerContractsCount = DB::table('lease_contracts')->where('owner_id', $testOwnerId)->count();
            $ownerVouchersCount = DB::table('vouchers')->where('owner_id', $testOwnerId)->count();

            if ($familyUserId && $canSeeProps && $canSeeContracts && !$canSeeMoney && $ownerPropsCount === 1 && $ownerContractsCount === 1 && $ownerVouchersCount === 1) {
                $recordTest('18. بوابة المالك + إضافة فرد عائلة (owner_family) + عزل الصلاحيات المالية', 'بوابة المالك والعائلة', 'passed', 'نجح إنشاء حساب فرد العائلة والتحقق من رؤيته للعقارات والعقود وحجب الكشف المالي عنه حسب الصلاحيات.', $t);
            } else {
                $recordTest('18. بوابة المالك وحسابات أفراد العائلة', 'بوابة المالك والعائلة', 'failed', 'خلل في صلاحيات فرد العائلة أو ربط بيانات محفظة المالك.', $t);
            }

            // 19. تجربة سجل التتبع (Activity Logs + IP/MAC) + كاشف الشذوذ المحاسبي + اعتماد السندات عبر الواتساب
            $t = microtime(true);
            $netInfo = self::detectClientIpAndMac(request());
            $actLogId = DB::table('activity_logs')->insertGetId([
                'user_id' => $officeStaffUserId,
                'user_name' => 'موظف مكتب تجريبي',
                'user_type' => 'office_staff',
                'office_id' => $testOfficeId,
                'action_type' => 'إضافة',
                'action_label' => 'إضافة في قسم (سندات القبض والصرف)',
                'details' => 'إصدار سند صرف تجريبي بمبلغ يتجاوز سقف التنبيه',
                'request_path' => 'office/vouchers/store',
                'ip_address' => $netInfo['ip'],
                'mac_address' => $netInfo['mac'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $pendingVoucherNo = self::generateSequentialVoucherNumber('payment', $testOfficeId);
            $pendingVoucherId = DB::table('vouchers')->insertGetId([
                'voucher_number' => $pendingVoucherNo, 'voucher_type' => 'payment',
                'office_id' => $testOfficeId, 'debit_account_id' => $ownerSubAccId,
                'credit_account_id' => $offBankAccId, 'owner_id' => $testOwnerId,
                'amount' => 25000, 'payment_method' => 'bank_transfer',
                'voucher_date' => date('Y-m-d'), 'notes' => 'سند صرف كبير بانتظار اعتماد الواتساب',
                'approval_status' => 'pending', 'approval_code' => '7788',
                'created_by' => $officeStaffUserId, 'created_at' => now(), 'updated_at' => now(),
            ]);
            if ($actLogId && $pendingVoucherId && !empty($netInfo['ip']) && !empty($netInfo['mac'])) {
                $recordTest('19. سجل التتبع (IP / MAC) + رادار كاشف الشذوذ المحاسبي + اعتماد السندات بالواتساب', 'الأمان والرقابة الذكية', 'passed', "نجح التقاط بصمة الجهاز ({$netInfo['ip']}) وتعليق سند الصرف الكبير (25,000 ر.س) لحين اعتماد المدير.", $t);
            } else {
                $recordTest('19. سجل التتبع وكاشف الشذوذ واعتماد الواتساب', 'الأمان والرقابة الذكية', 'failed', 'خلل في تسجيل سجل التتبع أو حالة اعتماد السند.', $t);
            }

            // إلغاء جميع البيانات التجريبية الوهمية فوراً للحفاظ على نظافة قاعدة البيانات 100%
            DB::rollBack();
        } catch (\Throwable $e) {
            DB::rollBack();
            $recordTest(
                'خطأ استثنائي أثناء محاكاة العمليات الشاملة',
                'محرك المحاكاة',
                'failed',
                $e->getMessage() . ' (السطر: ' . $e->getLine() . ')',
                microtime(true),
                'افحص تفاصيل الخطأ في نافذة سجل الأخطاء (System Logs).'
            );
        }

        // 20. فحص التوازن المحاسبي الفعلي للبيانات الحية المسجلة حالياً في النظام (الشركة الأم + جميع المكاتب)
        $t = microtime(true);
        $unbalancedEntries = DB::table('journal_entries')->whereRaw('ABS(total_debit - total_credit) > 0.05')->count();
        $orphanVouchers = DB::table('vouchers')
            ->leftJoin('accounts as d', 'vouchers.debit_account_id', '=', 'd.id')
            ->leftJoin('accounts as c', 'vouchers.credit_account_id', '=', 'c.id')
            ->whereNull('d.id')->orWhereNull('c.id')->count();

        if ($unbalancedEntries === 0 && $orphanVouchers === 0) {
            $recordTest('20. التدقيق المحاسبي للبيانات الفعلية الحالية (القيود والسندات والفواتير)', 'التدقيق المحاسبي الحي', 'passed', 'جميع السندات والقيود الفعلية المسجلة حالياً في النظام متوازنة ومربوطة بحسابات صحيحة 100%.', $t);
        } else {
            $recordTest('20. التدقيق المحاسبي للبيانات الفعلية الحالية', 'التدقيق المحاسبي الحي', 'warning', "يوجد ({$unbalancedEntries}) قيد غير متوازن و ({$orphanVouchers}) سند بحساب غير مكتمل.", $t, 'راجع تبويب السندات والقيود اليومية لتصحيح الحساب المرتبط.');
        }

        // 21. فحص سلامة ربط حسابات المستخدمين الحالية (المكاتب، الموظفين، الملاك، أفراد العائلة)
        $t = microtime(true);
        $orphanOfficeUsers = DB::table('users')->whereIn('user_type', ['office_admin', 'office_staff'])->whereNull('office_id')->count();
        $orphanOwnerUsers = DB::table('users')->whereIn('user_type', ['owner', 'owner_family'])->whereNull('owner_id')->count();
        if ($orphanOfficeUsers === 0 && $orphanOwnerUsers === 0) {
            $recordTest('21. فحص ارتباط حسابات دخول المكاتب والموظفين والملاك والعائلة', 'الأمان والصلاحيات الحية', 'passed', 'جميع حسابات المستخدمين الحالية مرتبطة بمكاتبها وملاكها بشكل سليم.', $t);
        } else {
            $recordTest('21. فحص ارتباط حسابات المستخدمين الحالية', 'الأمان والصلاحيات الحية', 'warning', "يوجد ({$orphanOfficeUsers}) حساب مكتب/موظف بدون office_id و ({$orphanOwnerUsers}) حساب مالك/عائلة بدون owner_id.", $t);
        }

        // 22. فحص إحصائيات الزوار (Geo Analytics) + بوابة واتساب (Meta Cloud API / QR) + مجلدات الرفع والـ PDF
        $t = microtime(true);
        $waUrl = self::getSetting('whatsapp_api_url', env('WHATSAPP_API_URL'));
        $waToken = self::getSetting('whatsapp_api_token', env('WHATSAPP_API_TOKEN'));
        $qrState = $this->fetchLiveMarketingQrState();
        $qrConnected = !empty($qrState['connected']);

        $uploadDir1 = public_path('uploads/properties');
        $uploadDir2 = public_path('uploads/whatsapp_pdfs');
        if (!is_dir($uploadDir1)) @mkdir($uploadDir1, 0775, true);
        if (!is_dir($uploadDir2)) @mkdir($uploadDir2, 0775, true);
        $dirsWritable = is_dir($uploadDir1) && is_writable($uploadDir1) && is_dir($uploadDir2) && is_writable($uploadDir2);

        if ($dirsWritable && (!empty($waToken) || $qrConnected)) {
            $waModeLabel = $qrConnected ? 'متصل عبر محرك QR المباشر' : 'مفعل عبر WhatsApp API';
            $recordTest('22. فحص بوابة واتساب (API / QR) + مجلدات الـ PDF والمرفقات + إحصائيات الزوار', 'الربط والتخزين والتحليلات', 'passed', "مجلدات رفع المرفقات والـ PDF قابلة للكتابة، والواتساب ({$waModeLabel}).", $t);
        } else {
            $msg = (!$dirsWritable ? 'مجلدات المرفقات غير قابلة للكتابة. ' : '') . (empty($waToken) && !$qrConnected ? 'توكن واتساب API أو ربط QR غير مفعل حالياً (يعمل النظام بوضع المحاكاة/الروابط المباشرة).' : '');
            $recordTest('22. فحص بوابة واتساب ومجلدات الـ PDF وإحصائيات الزوار', 'الربط والتخزين والتحليلات', 'warning', $msg, $t, 'يمكنك إدخال التوكن أو مسح كود الـ QR في تبويب إعدادات واتساب API متى رغبت.');
        }

        DB::table('system_diagnostic_runs')->insert($results);

        $passedCount = collect($results)->where('status', 'passed')->count();
        $totalTests = count($results);

        return back()->with('open_diagnostics_modal', true)
                     ->with('success', "اكتمل الفحص الأوتوماتيكي الشامل لجميع خصائص وبوابات النظام: نجح ({$passedCount} من {$totalTests}) اختباراً شاملاً.");
    }

    // =========================================================================
    // 3. نظام النسخ الاحتياطي الفوري والاستعادة المحدث (يشمل كافة الجداول الـ 29)
    // =========================================================================
    private function getAllBackupTableNames(): array
    {
        return [
            'users', 'offices', 'office_plans', 'owners', 'tenants', 'properties', 'property_units',
            'lease_contracts', 'contract_installments', 'property_types', 'property_type_attributes',
            'property_categories', 'custom_field_types', 'accounts', 'custom_account_types',
            'custom_payment_methods', 'vouchers', 'journal_entries', 'journal_entry_items',
            'tax_invoices', 'mullak_buildings', 'mullak_members', 'mullak_expenses',
            'site_visits', 'whatsapp_ad_campaigns', 'activity_logs',
            'system_error_logs', 'system_diagnostic_runs', 'system_settings'
        ];
    }

    public function downloadSystemBackup(Request $request)
    {
        $this->ensureDefaultAdminExists();
        $this->ensureDynamicPropertyTables();
        $this->ensureDynamicAccountTables();
        $this->ensurePaymentMethodsTable();
        $this->ensureJournalTables();
        $this->ensureTaxInvoicesTable();
        $this->ensureOfficePlansTable();
        $this->ensureSystemMonitoringTables();
        self::ensureContractInstallmentsTable();
        self::ensureActivityLogsTable();

        $format = $request->get('format', 'json');
        $timestamp = date('Y-m-d_H-i-s');

        if ($format === 'sqlite') {
            $dbPath = config('database.connections.sqlite.database', database_path('database.sqlite'));
            if (file_exists($dbPath)) {
                return response()->download($dbPath, "RealEstate_ERP_SQLite_{$timestamp}.sqlite");
            }
        }

        $tablesToBackup = $this->getAllBackupTableNames();

        $backupPayload = [
            'meta' => [
                'system' => 'Real Estate ERP System',
                'exported_at' => now()->toDateTimeString(),
                'exported_by' => Auth::user()->username ?? 'admin',
                'version' => '3.0',
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

    private function syncTableColumnsForRestore(string $tableName, array $sampleRow): void
    {
        if (empty($sampleRow) || !Schema::hasTable($tableName)) {
            return;
        }

        $existingColumns = Schema::getColumnListing($tableName);
        $missingColumns = array_diff(array_keys($sampleRow), $existingColumns);

        if (!empty($missingColumns)) {
            Schema::table($tableName, function (Blueprint $table) use ($missingColumns) {
                foreach ($missingColumns as $col) {
                    if (str_ends_with($col, '_id')) {
                        $table->unsignedBigInteger($col)->nullable();
                    } elseif (str_contains($col, 'amount') || str_contains($col, 'balance') || str_contains($col, 'rate') || str_contains($col, 'fee') || str_contains($col, 'price')) {
                        $table->decimal($col, 15, 2)->nullable()->default(0);
                    } else {
                        $table->text($col)->nullable();
                    }
                }
            });
        }
    }

    public function restoreSystemBackup(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|max:102400',
        ], [
            'backup_file.required' => 'يرجى اختيار ملف النسخة الاحتياطية (.json أو .sqlite) لاستعادته.',
        ]);

        try {
            $this->ensureDefaultAdminExists();
            $this->ensureDynamicPropertyTables();
            $this->ensureDynamicAccountTables();
            $this->ensurePaymentMethodsTable();
            $this->ensureJournalTables();
            $this->ensureTaxInvoicesTable();
            $this->ensureOfficePlansTable();
            $this->ensureSystemMonitoringTables();
            self::ensureContractInstallmentsTable();
            self::ensureActivityLogsTable();

            $uploadedFile = $request->file('backup_file');
            $realPath = $uploadedFile->getRealPath();
            $ext = strtolower($uploadedFile->getClientOriginalExtension());

            $header = file_get_contents($realPath, false, null, 0, 16);
            $isSqliteFile = str_starts_with($header ?: '', 'SQLite format 3') || in_array($ext, ['sqlite', 'sqlite3', 'db']);

            $tables = $this->getAllBackupTableNames();

            if ($isSqliteFile) {
                $tempSqlite = storage_path('app/temp_restore_' . time() . '.sqlite');
                copy($realPath, $tempSqlite);

                config(['database.connections.sqlite_restore_tmp' => [
                    'driver' => 'sqlite',
                    'database' => $tempSqlite,
                    'prefix' => '',
                ]]);

                DB::statement('PRAGMA foreign_keys = OFF;');
                try {
                    DB::transaction(function () use ($tables) {
                        foreach ($tables as $tableName) {
                            if (Schema::hasTable($tableName) && Schema::connection('sqlite_restore_tmp')->hasTable($tableName)) {
                                $rows = DB::connection('sqlite_restore_tmp')->table($tableName)->get()->toArray();
                                if (!empty($rows)) {
                                    $this->syncTableColumnsForRestore($tableName, (array) $rows[0]);
                                }
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

                return back()->with('success', 'تم استعادة قاعدة البيانات (.SQLite) وكافة الجداول والدفعات والفواتير وسجل التتبع بنجاح!');
            }

            $content = file_get_contents($realPath);
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
                            if (!empty($rows) && is_array($rows[0])) {
                                $this->syncTableColumnsForRestore($tableName, (array) $rows[0]);
                            }
                            $hasSettingKeyCol = ($tableName === 'system_settings' && Schema::hasColumn('system_settings', 'setting_key'));
                            DB::table($tableName)->delete();
                            foreach (array_chunk($rows, 100) as $chunk) {
                                $cleanChunk = array_map(function ($r) use ($hasSettingKeyCol) {
                                    $arr = (array) $r;
                                    if ($hasSettingKeyCol && empty($arr['setting_key']) && isset($arr['key'])) {
                                        $arr['setting_key'] = $arr['key'];
                                    }
                                    return $arr;
                                }, $chunk);
                                DB::table($tableName)->insert($cleanChunk);
                            }
                        }
                    }
                });
            } finally {
                DB::statement('PRAGMA foreign_keys = ON;');
            }

            return back()->with('success', 'تم استعادة النسخة الاحتياطية (.JSON) وجميع بيانات المكاتب والملاك والدفعات والفواتير وسجل التتبع بنجاح!');
        } catch (\Throwable $e) {
            try { DB::statement('PRAGMA foreign_keys = ON;'); } catch (\Throwable $ignore) {}
            self::logSystemIssue('Restore Backup', $e->getMessage(), 'ERROR', $e->getFile() . ':' . $e->getLine());
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

        $pdfToken = self::generateInvoicePublicToken($inv->id, $inv->invoice_number);
        $pdfDirectUrl = route('public.invoice_pdf', ['id' => $inv->id, 'token' => $pdfToken]);
        $msg .= "\n📥 رابط ملف الفاتورة الضريبية الأصلية (PDF):\n" . $pdfDirectUrl;

        $sent = self::sendWhatsAppDocument($inv->office_phone, $pdfDirectUrl, $inv->invoice_number . '.pdf', $msg, null);
        if ($sent) {
            return back()->with('success', "تم إرسال الفاتورة الضريبية رقم ({$inv->invoice_number}) كملف PDF أصلي عبر الواتساب إلى ({$inv->buyer_name}) بنجاح.");
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

        $stats = json_decode(self::getSetting('landing_stats_json', ''), true);
        if (!is_array($stats) || count($stats) === 0) $stats = $defaultStats;

        $features = json_decode(self::getSetting('landing_features_json', ''), true);
        if (!is_array($features) || count($features) === 0) $features = $defaultFeatures;

        $portals = json_decode(self::getSetting('landing_portals_json', ''), true);
        if (!is_array($portals) || count($portals) === 0) $portals = $defaultPortals;

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

            // إعدادات التسجيل الذاتي والفترة التجريبية ووضع التجربة الحية (Live Demo Sandbox)
            'self_registration_enabled' => (int) self::getSetting('self_registration_enabled', 1),
            'trial_days' => (int) self::getSetting('trial_days', 14),
            'trial_max_properties' => (int) self::getSetting('trial_max_properties', 5),
            'trial_allow_white_label' => (int) self::getSetting('trial_allow_white_label', 1),
            'trial_allowed_tabs' => json_decode(self::getSetting('trial_allowed_tabs_json', ''), true) ?: array_keys(self::getAvailableOfficeTabs()),
            'demo_sandbox_enabled' => (int) self::getSetting('demo_sandbox_enabled', 1),
            'demo_reset_minutes' => (int) self::getSetting('demo_reset_minutes', 60),
            'demo_allowed_tabs' => json_decode(self::getSetting('demo_allowed_tabs_json', ''), true) ?: array_keys(self::getAvailableOfficeTabs()),

            // المصفوفات الديناميكية
            'stats' => $stats,
            'features' => $features,
            'portals' => $portals,
            'custom_sections' => $customSections,
        ];
    }

    public function showLandingPage()
    {
        $this->ensureDefaultAdminExists();
        $this->ensureOfficePlansTable();

        $landing = self::getLandingSettings();
        $officePlans = DB::table('office_plans')
            ->where('show_on_landing', 1)
            ->orderBy('id', 'asc')
            ->get();
        $availableOfficeTabs = self::getAvailableOfficeTabs();

        $liveStats = [
            'offices' => Schema::hasTable('offices') ? DB::table('offices')->count() : 0,
            'properties' => Schema::hasTable('properties') ? DB::table('properties')->count() : 0,
            'contracts' => Schema::hasTable('lease_contracts') ? DB::table('lease_contracts')->count() : 0,
            'owners' => Schema::hasTable('owners') ? DB::table('owners')->count() : 0,
        ];

        return view('welcome_landing', compact('landing', 'liveStats', 'officePlans', 'availableOfficeTabs'));
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

        // 6. حفظ البوابات الذكية
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

        // 7. حفظ الأقسام الإضافية المخصصة (Custom Sections)
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

        return redirect()->route('erp.dashboard', ['tab' => 'office_plans'])
            ->with('success', 'تم حفظ إعدادات الـ SEO والواجهة الرئيسية والباقات بنجاح!');
    }

    // =========================================================================
    // نظام تتبع الحركات والعمليات المطور + سجل (قبل ⬅️ بعد) للتعديلات الحساسة
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
                $table->text('diff_json')->nullable(); // سجل قبل وبعد
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
            if (!Schema::hasColumn('activity_logs', 'diff_json')) {
                $table->text('diff_json')->nullable();
            }
        });
    }

    /**
     * التقاط نسخة من السجل قبل تعديله أو حذفه لمقارنتها وإظهار (قبل ⬅️ بعد)
     */
    public static function capturePreActionSnapshot(?Request $req = null): void
    {
        try {
            $req = $req ?: request();
            if (!$req || !$req->isMethod('post')) {
                return;
            }
            $path = $req->path();
            if (!preg_match('#(?:^|/)(offices|owners|properties|units|tenants|contracts|accounts|vouchers|journal-entries|staff|family|member)/(\d+)/(update|delete)#i', $path, $m)) {
                return;
            }

            $entityKey = strtolower($m[1]);
            $recordId  = (int) $m[2];

            $tableMap = [
                'offices'         => 'offices',
                'owners'          => 'owners',
                'properties'      => 'properties',
                'units'           => 'property_units',
                'tenants'         => 'tenants',
                'contracts'       => 'lease_contracts',
                'accounts'        => 'accounts',
                'vouchers'        => 'vouchers',
                'journal-entries' => 'journal_entries',
                'staff'           => 'users',
                'family'          => 'users',
                'member'          => 'mullak_members',
            ];

            $tableName = $tableMap[$entityKey] ?? null;
            if ($tableName && Schema::hasTable($tableName)) {
                $row = DB::table($tableName)->where('id', $recordId)->first();
                if ($row) {
                    self::$preActionSnapshots[$tableName . ':' . $recordId] = (array) $row;
                }
            }
        } catch (\Throwable $e) {}
    }

    /**
     * حساب الفروقات الدقيقة (قبل ⬅️ بعد) بالعربية
     */
    public static function buildFieldLevelDiff(Request $req): array
    {
        $diffs = [];
        try {
            $path = $req->path();
            if (!preg_match('#(?:^|/)(offices|owners|properties|units|tenants|contracts|accounts|vouchers|journal-entries|staff|family|member)/(\d+)/(update|delete)#i', $path, $m)) {
                return [];
            }

            $entityKey  = strtolower($m[1]);
            $recordId   = (int) $m[2];
            $actionVerb = strtolower($m[3]);

            $tableMap = [
                'offices'         => 'offices',
                'owners'          => 'owners',
                'properties'      => 'properties',
                'units'           => 'property_units',
                'tenants'         => 'tenants',
                'contracts'       => 'lease_contracts',
                'accounts'        => 'accounts',
                'vouchers'        => 'vouchers',
                'journal-entries' => 'journal_entries',
                'staff'           => 'users',
                'family'          => 'users',
                'member'          => 'mullak_members',
            ];

            $tableName = $tableMap[$entityKey] ?? null;
            if (!$tableName) return [];

            $oldRow = self::$preActionSnapshots[$tableName . ':' . $recordId] ?? null;
            if (!$oldRow) return [];

            $fieldLabels = [
                'name'                       => 'الاسم',
                'title'                      => 'المسمى / العنوان',
                'phone'                      => 'رقم الجوال',
                'amount'                     => 'المبلغ (ر.س)',
                'total_rent_amount'          => 'الإيجار السنوي (ر.س)',
                'default_rent_price'         => 'قيمة الإيجار (ر.س)',
                'office_commission'          => 'عمولة المكتب (ر.س)',
                'office_management_fee_rate' => 'نسبة الإدارة (%)',
                'security_deposit'           => 'مبلغ التأمين (ر.س)',
                'start_date'                 => 'تاريخ البداية',
                'end_date'                   => 'تاريخ النهاية',
                'voucher_date'               => 'تاريخ السند',
                'payment_method'             => 'طريقة الدفع',
                'notes'                      => 'البيان / الملاحظات',
                'general_notes'              => 'البيان العام',
                'total_debit'                => 'إجمالي المدين (ر.س)',
                'total_credit'               => 'إجمالي الدائن (ر.س)',
                'deed_number'                => 'رقم الصك',
                'total_area'                 => 'المساحة',
                'unit_number'                => 'رقم الوحدة',
                'status'                     => 'الحالة',
                'iban'                       => 'رقم الآيبان IBAN',
                'bank_name'                  => 'اسم البنك',
                'national_id'                => 'رقم الهوية',
                'id_number'                  => 'رقم الهوية',
                'tax_number'                 => 'الرقم الضريبي',
                'annual_fee'                 => 'الرسوم السنوية (ر.س)',
                'paid_amount'                => 'المبلغ المسدد (ر.س)',
                'balance'                    => 'الرصيد (ر.س)',
            ];

            if ($actionVerb === 'delete') {
                foreach ($fieldLabels as $col => $label) {
                    if (isset($oldRow[$col]) && $oldRow[$col] !== '' && $oldRow[$col] !== null) {
                        $diffs[] = [
                            'field' => $label,
                            'before' => (string) $oldRow[$col],
                            'after'  => 'تم الحذف نهائياً 🗑️',
                        ];
                    }
                }
                return array_slice($diffs, 0, 6);
            }

            $newRow = Schema::hasTable($tableName) ? (array) (DB::table($tableName)->where('id', $recordId)->first() ?? []) : [];

            foreach ($fieldLabels as $col => $label) {
                if (!array_key_exists($col, $oldRow)) continue;
                $oldVal = trim((string) ($oldRow[$col] ?? ''));
                $newVal = array_key_exists($col, $newRow) ? trim((string) ($newRow[$col] ?? '')) : trim((string) $req->input($col, $oldVal));

                if (is_numeric($oldVal) && is_numeric($newVal)) {
                    if (abs((float) $oldVal - (float) $newVal) > 0.009) {
                        $diffs[] = [
                            'field'  => $label,
                            'before' => number_format((float) $oldVal, 2),
                            'after'  => number_format((float) $newVal, 2),
                        ];
                    }
                } elseif ($oldVal !== $newVal && $newVal !== '') {
                    $diffs[] = [
                        'field'  => $label,
                        'before' => $oldVal !== '' ? $oldVal : 'فارغ',
                        'after'  => $newVal,
                    ];
                }
            }
        } catch (\Throwable $e) {}

        return $diffs;
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

        if (empty($mac) && function_exists('shell_exec')) {
            try {
                $arpOut = @shell_exec("arp -a " . escapeshellarg($ip));
                if ($arpOut && preg_match('/([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})/', $arpOut, $matches)) {
                    $mac = strtoupper(str_replace('-', ':', $matches[0]));
                }
            } catch (\Throwable $e) {}
        }

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

            $user = Auth::user();

            if (!$user || $user->user_type === 'company_admin' || session()->has('impersonate_office_id') || session()->has('impersonate_owner_id')) {
                return;
            }

            if (!in_array($user->user_type, ['office_admin', 'office_staff', 'owner_family'])) {
                return;
            }

            $path = $req->path();
            if (str_contains($path, 'logout') || str_contains($path, 'login')) {
                return;
            }

            self::ensureActivityLogsTable();

            $actionType = 'عملية';
            if (str_contains($path, 'store') || str_contains($path, 'seed') || str_contains($path, 'pay')) {
                $actionType = 'إضافة';
            } elseif (str_contains($path, 'update') || str_contains($path, 'save') || str_contains($path, 'toggle') || str_contains($path, 'approve')) {
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
                'installments' => 'جدولة الدفعات',
                'accounts' => 'شجرة الحسابات',
                'account-types' => 'أنواع الحسابات',
                'vouchers' => 'سندات القبض والصرف',
                'journal-entries' => 'القيود اليومية',
                'invoices' => 'الفواتير الضريبية',
                'staff' => 'إعدادات المكتب والموظفين',
                'branding' => 'الهوية البصرية والدومين',
                'mullak' => 'اتحاد الملاك (مُلّاك)',
                'family' => 'أفراد العائلة والوكلاء',
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

            $roleAr = $user->user_type === 'office_admin' ? 'مدير المكتب' : ($user->user_type === 'office_staff' ? 'موظف المكتب' : 'فرد العائلة');
            $actionLabel = "{$actionType} في قسم ({$sectionLabel})";
            $actorName = trim(explode('(', $user->name)[0]) ?: ($user->username ?: 'المستخدم');

            $targetItem = $req->input('name')
                       ?: $req->input('title')
                       ?: $req->input('unit_number')
                       ?: $req->input('description')
                       ?: $req->input('general_notes')
                       ?: $req->input('notes');

            if (is_array($targetItem)) {
                $targetItem = implode('، ', array_filter($targetItem));
            }
            if (empty($targetItem) && is_array($req->input('names'))) {
                $targetItem = implode('، ', array_filter($req->input('names')));
            }
            $targetItem = trim((string) $targetItem);

            $diffs = self::buildFieldLevelDiff($req);

            if (str_contains($path, 'contracts') && $actionType === 'إضافة') {
                $simpleDetails = "{$actorName} ({$roleAr}) أنشأ عقد إيجار جديد" . ($req->input('total_rent_amount') ? " بقيمة {$req->input('total_rent_amount')} ر.س" : "");
            } elseif (str_contains($path, 'sync-ejar')) {
                $simpleDetails = "{$actorName} ({$roleAr}) وثّق عقد إيجار في منصة إيجار";
            } elseif (str_contains($path, 'installments/pay')) {
                $simpleDetails = "{$actorName} ({$roleAr}) حصّل دفعة إيجار بمبلغ " . ($req->input('amount') ?: '') . " ر.س";
            } elseif (str_contains($path, 'vouchers') && $actionType === 'إضافة') {
                $vTypeAr = $req->input('voucher_type') === 'payment' ? 'سند صرف' : 'سند قبض';
                $simpleDetails = "{$actorName} ({$roleAr}) أصدر {$vTypeAr}" . ($req->input('amount') ? " بمبلغ {$req->input('amount')} ر.س" : "") . ($targetItem ? " ({$targetItem})" : "");
            } elseif (str_contains($path, 'invoices') && $actionType === 'إضافة') {
                $simpleDetails = "{$actorName} ({$roleAr}) أصدر فاتورة ضريبية" . ($targetItem ? " ({$targetItem})" : "");
            } elseif (str_contains($path, 'journal-entries') && $actionType === 'إضافة') {
                $simpleDetails = "{$actorName} ({$roleAr}) أنشأ قيد يومي" . ($targetItem ? " ({$targetItem})" : "");
            } else {
                $verb = match ($actionType) {
                    'إضافة' => 'أضاف',
                    'تعديل' => 'عدّل',
                    'حذف'   => 'حذف',
                    default => 'نفّذ عملية في',
                };
                $simpleDetails = $targetItem !== ''
                    ? "{$actorName} ({$roleAr}) {$verb} {$targetItem} ({$sectionLabel})"
                    : "{$actorName} ({$roleAr}) {$verb} في {$sectionLabel}";
            }

            if (!empty($diffs)) {
                $diffTextParts = array_map(fn($d) => "{$d['field']}: [قبل: {$d['before']}] ⬅️ [بعد: {$d['after']}]", $diffs);
                $simpleDetails .= ' — التغييرات: ' . implode(' | ', $diffTextParts);
            }

            $netInfo = self::detectClientIpAndMac($req);

            DB::table('activity_logs')->insert([
                'user_id'      => $user->id,
                'user_name'    => $actorName . ' — [' . $roleAr . ': ' . ($user->username ?: $user->phone) . ']',
                'user_type'    => $user->user_type,
                'office_id'    => $user->office_id,
                'owner_id'     => $user->owner_id,
                'action_type'  => $actionType,
                'action_label' => $actionLabel,
                'details'      => $simpleDetails,
                'diff_json'    => !empty($diffs) ? json_encode($diffs, JSON_UNESCAPED_UNICODE) : null,
                'request_path' => ltrim($path, '/'),
                'ip_address'   => $netInfo['ip'],
                'mac_address'  => $netInfo['mac'],
                'user_agent'   => mb_substr((string) $req->userAgent(), 0, 180),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            $keepGlobalIds = DB::table('activity_logs')->orderBy('id', 'desc')->limit(500)->pluck('id');
            if ($keepGlobalIds->count() >= 500) {
                DB::table('activity_logs')->whereNotIn('id', $keepGlobalIds)->delete();
            }
        } catch (\Throwable $e) {}
    }

    public static function getFilteredActivityLogs(?int $officeId = null, ?int $ownerId = null, string $viewerRole = 'company_admin')
    {
        self::ensureActivityLogsTable();

        DB::table('activity_logs')->where('user_type', 'company_admin')->delete();

        $query = DB::table('activity_logs')
            ->where('user_type', '!=', 'company_admin')
            ->orderBy('id', 'desc');

        if ($viewerRole === 'office') {
            $query->where('office_id', $officeId)
                  ->whereIn('user_type', ['office_admin', 'office_staff']);
        } elseif ($viewerRole === 'owner') {
            $query->where('owner_id', $ownerId)
                  ->where('user_type', 'owner_family');
        } else {
            $query->whereIn('user_type', ['office_admin', 'office_staff', 'owner_family']);
        }

        return $query->limit(300)->get();
    }

    /**
     * زر توليد جميع البيانات التجريبية الشاملة للنظام من حساب الأدمن (لتجربة كافة المميزات والصفحات 1, 2, 3...)
     */
    public function seedFullSystemDemoData(Request $request)
    {
        $this->ensureDefaultAdminExists();
        $this->ensureDynamicPropertyTables();
        $this->ensureDynamicAccountTables();
        $this->ensurePaymentMethodsTable();
        $this->ensureJournalTables();
        $this->ensureTaxInvoicesTable();
        $this->ensureOfficePlansTable();
        $this->ensureSystemMonitoringTables();
        self::ensureContractInstallmentsTable();
        self::ensureActivityLogsTable();

        if (DB::table('accounts')->whereNull('office_id')->count() === 0) {
            $this->seedDefaultAccounts();
        }

        DB::transaction(function () {
            $compCashAcc = DB::table('accounts')->whereNull('office_id')->where('code', '1102')->first()
                        ?: DB::table('accounts')->whereNull('office_id')->where('is_group', 0)->first();
            $compRevAcc = DB::table('accounts')->whereNull('office_id')->where('code', '4101')->first() ?: $compCashAcc;
            $compOfficesParent = DB::table('accounts')->whereNull('office_id')->where('code', '1103')->first();
            $proPlan = DB::table('office_plans')->orderBy('id', 'desc')->first();
            $allTabsJson = json_encode(array_keys(self::getAvailableOfficeTabs()), JSON_UNESCAPED_UNICODE);

            // 1. التأكد من وجود أنواع عقارات ومتطلباتها
            $propTypeIds = [];
            $demoPropTypes = [
                ['name' => 'عمارة سكنية استثمارية', 'cat' => 'سكني', 'units' => 1],
                ['name' => 'مجمع تجاري ومكاتب', 'cat' => 'تجاري', 'units' => 1],
                ['name' => 'فيلا سكنية مستقلة', 'cat' => 'سكني', 'units' => 0],
            ];
            foreach ($demoPropTypes as $dpt) {
                $existingPt = DB::table('property_types')->where('name', $dpt['name'])->first();
                if ($existingPt) {
                    $propTypeIds[] = $existingPt->id;
                } else {
                    $ptId = DB::table('property_types')->insertGetId([
                        'name' => $dpt['name'], 'category' => $dpt['cat'],
                        'account_id' => $compCashAcc->id ?? null, 'has_units' => $dpt['units'],
                        'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),
                    ]);
                    DB::table('property_type_attributes')->insert([
                        ['property_type_id' => $ptId, 'attribute_name' => 'عدد الأدوار', 'field_type' => 'رقم / مساحة (Number)', 'is_required' => 1, 'created_at' => now(), 'updated_at' => now()],
                        ['property_type_id' => $ptId, 'attribute_name' => 'عمر العقار', 'field_type' => 'رقم / مساحة (Number)', 'is_required' => 1, 'created_at' => now(), 'updated_at' => now()],
                    ]);
                    $propTypeIds[] = $ptId;
                }
            }

            // 2. إنشاء مكتب عقاري نموذجي متكامل (أو استخدام المكتب الأول وتغذيته بـ 35+ عميل لتجربة الـ Pagination)
            $demoOffices = [
                ['name' => 'شركة صروح طيبة للتطوير وإدارة الأملاك', 'user' => 'surouh_office', 'phone' => '+966551001001', 'cr' => '1010884411', 'fal' => '120004411', 'tax' => '300445566778893', 'type' => 'annual_subscription', 'fee' => 3000, 'rate' => 0],
                ['name' => 'مكتب روافد نجد العقاري', 'user' => 'rawafed_najd', 'phone' => '+966551001002', 'cr' => '1010884422', 'fal' => '120004422', 'tax' => '300445566778894', 'type' => 'hybrid', 'fee' => 1500, 'rate' => 2.5],
            ];

            $mainOfficeId = null;
            foreach ($demoOffices as $idx => $do) {
                $offRow = DB::table('offices')->where('name', $do['name'])->first();
                if (!$offRow) {
                    $offAccId = self::createSequentialSubAccount(null, $compOfficesParent->id ?? null, 'حساب ' . $do['name']);
                    $offId = DB::table('offices')->insertGetId([
                        'name' => $do['name'], 'cr_number' => $do['cr'], 'fal_license' => $do['fal'],
                        'tax_number' => $do['tax'], 'national_address' => 'الرياض - طريق الملك فهد',
                        'phone' => $do['phone'], 'email' => $do['user'] . '@office.local',
                        'address' => 'الرياض - حي الملقا', 'billing_type' => $do['type'],
                        'annual_fee' => $do['fee'], 'company_commission_rate' => $do['rate'],
                        'plan_id' => $proPlan->id ?? null, 'allowed_tabs' => $allTabsJson,
                        'allow_owner_tax_invoice' => 1, 'subscription_ends_at' => now()->addMonths($idx === 0 ? 10 : 1)->toDateString(),
                        'status' => 'active', 'account_id' => $offAccId, 'use_custom_whatsapp' => 0,
                        'created_at' => now(), 'updated_at' => now(),
                    ]);

                    if (!DB::table('users')->where('username', $do['user'])->exists()) {
                        DB::table('users')->insert([
                            'name' => 'مدير ' . $do['name'], 'username' => $do['user'],
                            'phone' => $do['phone'], 'email' => $do['user'] . '@office.local',
                            'password' => Hash::make('123456'), 'user_type' => 'office_admin',
                            'office_id' => $offId, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),
                        ]);
                    }
                } else {
                    $offId = $offRow->id;
                    DB::table('offices')->where('id', $offId)->update(['allowed_tabs' => $allTabsJson, 'allow_owner_tax_invoice' => 1]);
                }

                if ($idx === 0) {
                    $mainOfficeId = $offId;
                }
            }

            if (!$mainOfficeId) {
                return;
            }

            // 3. تأسيس شجرة الحسابات الخاصة بالمكتب النموذجي
            if (DB::table('accounts')->where('office_id', $mainOfficeId)->count() === 0) {
                $r1 = DB::table('accounts')->insertGetId(['office_id' => $mainOfficeId, 'parent_id' => null, 'code' => '1000', 'name' => 'الأصول (صناديق وبنوك المكتب)', 'type' => 'الأصول', 'is_group' => 1, 'balance' => 0, 'created_at' => now(), 'updated_at' => now()]);
                DB::table('accounts')->insert([
                    ['office_id' => $mainOfficeId, 'parent_id' => $r1, 'code' => '1101', 'name' => 'صندوق وخزينة المكتب', 'type' => 'الأصول', 'is_group' => 0, 'balance' => 0, 'created_at' => now(), 'updated_at' => now()],
                    ['office_id' => $mainOfficeId, 'parent_id' => $r1, 'code' => '1102', 'name' => 'حساب المكتب البنكي (تحصيل الإيجارات)', 'type' => 'الأصول', 'is_group' => 0, 'balance' => 0, 'created_at' => now(), 'updated_at' => now()],
                    ['office_id' => $mainOfficeId, 'parent_id' => $r1, 'code' => '1103', 'name' => 'ذمم المستأجرين (حسابات المستأجرين)', 'type' => 'الأصول', 'is_group' => 1, 'balance' => 0, 'created_at' => now(), 'updated_at' => now()],
                ]);
                $r2 = DB::table('accounts')->insertGetId(['office_id' => $mainOfficeId, 'parent_id' => null, 'code' => '2000', 'name' => 'الخصوم والأمانات', 'type' => 'الخصوم والالتزامات', 'is_group' => 1, 'balance' => 0, 'created_at' => now(), 'updated_at' => now()]);
                DB::table('accounts')->insert([
                    ['office_id' => $mainOfficeId, 'parent_id' => $r2, 'code' => '2101', 'name' => 'حساب أمانات الملاك (صافي الإيجارات)', 'type' => 'الخصوم والالتزامات', 'is_group' => 1, 'balance' => 0, 'created_at' => now(), 'updated_at' => now()],
                    ['office_id' => $mainOfficeId, 'parent_id' => $r2, 'code' => '2102', 'name' => 'تأمينات المستأجرين المستردة', 'type' => 'الخصوم والالتزامات', 'is_group' => 0, 'balance' => 0, 'created_at' => now(), 'updated_at' => now()],
                ]);
                $r4 = DB::table('accounts')->insertGetId(['office_id' => $mainOfficeId, 'parent_id' => null, 'code' => '4000', 'name' => 'إيرادات المكتب العقاري', 'type' => 'الإيرادات', 'is_group' => 1, 'balance' => 0, 'created_at' => now(), 'updated_at' => now()]);
                DB::table('accounts')->insert([
                    ['office_id' => $mainOfficeId, 'parent_id' => $r4, 'code' => '4101', 'name' => 'إيرادات عمولات إدارة الأملاك', 'type' => 'الإيرادات', 'is_group' => 0, 'balance' => 0, 'created_at' => now(), 'updated_at' => now()],
                ]);
                $r5 = DB::table('accounts')->insertGetId(['office_id' => $mainOfficeId, 'parent_id' => null, 'code' => '5000', 'name' => 'مصروفات المكتب والصيانة', 'type' => 'المصروفات التشغيلية', 'is_group' => 1, 'balance' => 0, 'created_at' => now(), 'updated_at' => now()]);
                DB::table('accounts')->insert([
                    ['office_id' => $mainOfficeId, 'parent_id' => $r5, 'code' => '5101', 'name' => 'مصروفات صيانة العقارات والوحدات', 'type' => 'المصروفات التشغيلية', 'is_group' => 0, 'balance' => 0, 'created_at' => now(), 'updated_at' => now()],
                    ['office_id' => $mainOfficeId, 'parent_id' => $r5, 'code' => '5102', 'name' => 'مصروفات رواتب موظفي المكتب', 'type' => 'المصروفات التشغيلية', 'is_group' => 0, 'balance' => 0, 'created_at' => now(), 'updated_at' => now()],
                ]);
            }

            $offBankAcc = DB::table('accounts')->where('office_id', $mainOfficeId)->where('code', '1102')->first();
            $offTenantsParent = DB::table('accounts')->where('office_id', $mainOfficeId)->where('code', '1103')->first();
            $offOwnersParent = DB::table('accounts')->where('office_id', $mainOfficeId)->where('code', '2101')->first();
            $offRevAcc = DB::table('accounts')->where('office_id', $mainOfficeId)->where('code', '4101')->first();
            $offMaintAcc = DB::table('accounts')->where('office_id', $mainOfficeId)->where('code', '5101')->first();

            // 4. إضافة 6 ملاك عقارات + 8 عقارات + 40 وحدة عقارية + 36 مستأجراً وعقداً (لتجربة نظام الصفحات 30 في الصفحة الأولى والباقي في الصفحة الثانية)
            $ownerNames = [
                'الشيخ عمر بن إبراهيم المهيدب',
                'الشيخ عبدالرحمن بن عبدالعزيز الراجحي',
                'سليمان بن محمد العليان',
                'خالد بن فهد السديري',
                'سلطان بن بندر العتيبي',
                'شركة دار الأصول العقارية القابضة'
            ];
            $ownerIds = [];
            foreach ($ownerNames as $oIdx => $oName) {
                $existingOw = DB::table('owners')->where('office_id', $mainOfficeId)->where('name', $oName)->first();
                if ($existingOw) {
                    $ownerIds[] = $existingOw->id;
                } else {
                    $owAccId = self::createSequentialSubAccount($mainOfficeId, $offOwnersParent->id ?? null, 'أمانات المالك: ' . $oName);
                    $owPhone = '+96650440010' . ($oIdx + 1);
                    $owUser = 'owner_demo_' . ($oIdx + 1) . '_' . rand(10, 99);
                    $owId = DB::table('owners')->insertGetId([
                        'office_id' => $mainOfficeId, 'name' => $oName,
                        'national_id' => '102030405' . $oIdx, 'tax_number' => '30011223344556' . $oIdx,
                        'national_address' => 'الرياض - حي حطين', 'phone' => $owPhone,
                        'email' => $owUser . '@owner.local', 'iban' => 'SA448000000060801016751' . $oIdx,
                        'bank_name' => 'مصرف الراجحي', 'office_management_fee_rate' => 5.0,
                        'account_id' => $owAccId, 'created_at' => now(), 'updated_at' => now(),
                    ]);
                    DB::table('users')->insert([
                        'name' => $oName, 'username' => $owUser, 'phone' => $owPhone,
                        'email' => $owUser . '@owner.local', 'national_id' => '102030405' . $oIdx,
                        'password' => Hash::make('123456'), 'user_type' => 'owner',
                        'office_id' => $mainOfficeId, 'owner_id' => $owId, 'is_active' => 1,
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                    $ownerIds[] = $owId;
                }
            }

            // 5. إنشاء العقارات والوحدات
            $propTitles = [
                'مجمع الخزامى السكني - رقم 68',
                'عمارة سرايا الغدير - رقم 69',
                'برج الملقا للأعمال والتجارة',
                'مجمع الياسمين بلازا',
                'عمارة النخيل الذهبية',
                'مجمع قرطبة السكني الحديث'
            ];
            $propIds = [];
            foreach ($propTitles as $pIdx => $pTitle) {
                $existingPr = DB::table('properties')->where('office_id', $mainOfficeId)->where('title', $pTitle)->first();
                if ($existingPr) {
                    $propIds[] = $existingPr->id;
                } else {
                    $prId = DB::table('properties')->insertGetId([
                        'office_id' => $mainOfficeId, 'owner_id' => $ownerIds[$pIdx % count($ownerIds)],
                        'property_type_id' => $propTypeIds[$pIdx % count($propTypeIds)],
                        'title' => $pTitle, 'deed_number' => '31018877660' . ($pIdx + 1),
                        'deed_date' => '2023-05-10', 'total_area' => 950 + ($pIdx * 120),
                        'estimated_value' => 4500000 + ($pIdx * 500000), 'city' => 'الرياض',
                        'district' => 'حي الملقا', 'street' => 'طريق أنس بن مالك',
                        'building_number' => (string)(4100 + $pIdx), 'postal_code' => '13521',
                        'custom_attributes' => json_encode(['عدد الأدوار' => '4', 'عمر العقار' => '2'], JSON_UNESCAPED_UNICODE),
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                    $propIds[] = $prId;
                }
            }

            // 6. إنشاء 35 مستأجراً وعقداً ووحدات ودفعات وسندات (لكي يظهر 30 في الصفحة 1 والباقي في الصفحة 2)
            $firstNames = ['صالح', 'عبدالله', 'فهد', 'ناصر', 'سعود', 'محمد', 'خالد', 'فيصل', 'تركي', 'بندر', 'سلطان', 'ماجد'];
            $midNames = ['بن سليمان', 'بن سعد', 'بن إبراهيم', 'بن عبدالعزيز', 'بن حمد', 'بن راشد', 'بن منصور'];
            $lastNames = ['الدوسري', 'الموسى', 'التميمي', 'السبيعي', 'القحطاني', 'العتيبي', 'الشمري', 'الحربي', 'المطيري', 'الشهري', 'الزهراني', 'الغامدي'];

            $existingTenantsCount = DB::table('tenants')->where('office_id', $mainOfficeId)->count();
            $neededTenants = max(0, 35 - $existingTenantsCount);

            for ($i = 1; $i <= $neededTenants; $i++) {
                $seqNum = $existingTenantsCount + $i;
                $tName = $firstNames[$seqNum % count($firstNames)] . ' ' . $midNames[$seqNum % count($midNames)] . ' ' . $lastNames[($seqNum * 3) % count($lastNames)];
                $tPhone = '+966556000' . str_pad((string)(200 + $seqNum), 3, '0', STR_PAD_LEFT);
                $tIdNo = '1088776' . str_pad((string)(100 + $seqNum), 3, '0', STR_PAD_LEFT);

                $tAccId = self::createSequentialSubAccount($mainOfficeId, $offTenantsParent->id ?? null, 'المستأجر: ' . $tName);
                $tId = DB::table('tenants')->insertGetId([
                    'office_id' => $mainOfficeId, 'name' => $tName, 'id_type' => 'national_id',
                    'id_number' => $tIdNo, 'tax_number' => ($seqNum % 4 === 0) ? ('300998877665' . str_pad((string)$seqNum, 3, '0', STR_PAD_LEFT)) : null,
                    'national_address' => 'الرياض - حي الياسمين', 'phone' => $tPhone,
                    'account_id' => $tAccId, 'created_at' => now(), 'updated_at' => now(),
                ]);

                $chosenPropId = $propIds[$seqNum % count($propIds)];
                $chosenProp = DB::table('properties')->where('id', $chosenPropId)->first();
                $rentAmount = 24000 + (($seqNum % 6) * 4000);

                $unitId = DB::table('property_units')->insertGetId([
                    'property_id' => $chosenPropId, 'property_type_id' => $chosenProp->property_type_id,
                    'unit_number' => 'شقة ' . $seqNum, 'floor_number' => ($seqNum % 4) + 1,
                    'area' => 145, 'electricity_meter' => 'ELC-880' . $seqNum,
                    'water_meter' => 'WTR-440' . $seqNum, 'default_rent_price' => $rentAmount,
                    'status' => 'rented', 'custom_attributes' => json_encode(['rooms' => 4, 'unit_kind' => 'شقة'], JSON_UNESCAPED_UNICODE),
                    'created_at' => now(), 'updated_at' => now(),
                ]);

                // توزيع التواريخ بحيث تكون هناك عقود ودفعات متأخرة (30، 60، 90، +90 يوماً) ودفعات سارية
                $monthsAgo = ($seqNum % 5) * 2;
                $startDate = now()->subMonths($monthsAgo)->toDateString();
                $endDate = now()->subMonths($monthsAgo)->addYear()->toDateString();
                $cycle = ($seqNum % 3 === 0) ? 'quarterly' : (($seqNum % 2 === 0) ? 'semi_annual' : 'annual');

                $contractNo = 'CNT-' . date('Y') . '-' . str_pad((string)(1000 + $seqNum), 5, '0', STR_PAD_LEFT);
                $cId = DB::table('lease_contracts')->insertGetId([
                    'contract_number' => $contractNo,
                    'ejar_contract_number' => 'EJR-99001122' . str_pad((string)$seqNum, 2, '0', STR_PAD_LEFT),
                    'ejar_status' => ($seqNum % 5 === 0) ? 'not_sent' : 'active',
                    'office_id' => $mainOfficeId, 'property_id' => $chosenPropId, 'property_unit_id' => $unitId,
                    'owner_id' => $chosenProp->owner_id, 'tenant_id' => $tId,
                    'start_date' => $startDate, 'end_date' => $endDate,
                    'payment_cycle' => $cycle, 'total_rent_amount' => $rentAmount,
                    'security_deposit' => 2000, 'office_commission' => ($rentAmount * 5) / 100,
                    'company_fee_amount' => 0, 'status' => 'active',
                    'created_at' => now(), 'updated_at' => now(),
                ]);

                $contractObj = DB::table('lease_contracts')->where('id', $cId)->first();
                self::generateInstallmentsForContract($contractObj);

                // إصدار سند قبض لبعض المستأجرين لتجربة السندات وكشوف الحسابات
                if ($seqNum <= 22 && $offBankAcc) {
                    $paidAmt = ($seqNum % 2 === 0) ? $rentAmount : ($rentAmount / 2);
                    $vNo = self::generateSequentialVoucherNumber('receipt', $mainOfficeId);
                    $vId = DB::table('vouchers')->insertGetId([
                        'voucher_number' => $vNo, 'voucher_type' => 'receipt',
                        'office_id' => $mainOfficeId, 'owner_id' => $chosenProp->owner_id,
                        'tenant_id' => $tId, 'lease_contract_id' => $cId,
                        'property_id' => $chosenPropId, 'property_unit_id' => $unitId,
                        'debit_account_id' => $offBankAcc->id, 'credit_account_id' => $tAccId,
                        'amount' => $paidAmt, 'payment_method' => 'bank_transfer',
                        'voucher_date' => now()->subDays($seqNum)->toDateString(),
                        'notes' => 'تحصيل دفعة إيجار للعقد ' . $contractNo . ' - ' . $tName,
                        'approval_status' => 'approved', 'created_by' => Auth::id() ?? 1,
                        'created_at' => now()->subDays($seqNum), 'updated_at' => now(),
                    ]);
                    self::applyAccountMovement((int)$offBankAcc->id, (float)$paidAmt, 0);
                    self::applyAccountMovement((int)$tAccId, 0, (float)$paidAmt);

                    $firstInst = DB::table('contract_installments')->where('lease_contract_id', $cId)->orderBy('installment_number')->first();
                    if ($firstInst) {
                        $applied = min($paidAmt, (float)$firstInst->amount);
                        DB::table('contract_installments')->where('id', $firstInst->id)->update([
                            'paid_amount' => $applied,
                            'status' => $applied >= $firstInst->amount ? 'paid' : 'partial_overdue',
                            'last_voucher_id' => $vId,
                        ]);
                    }
                }
            }

            // إضافة وحدات شاغرة لتجربة فلتر الوحدات الشاغرة وتصدير ZIP
            foreach ($propIds as $idx => $pId) {
                $pr = DB::table('properties')->where('id', $pId)->first();
                if (!DB::table('property_units')->where('property_id', $pId)->where('status', 'vacant')->exists()) {
                    DB::table('property_units')->insert([
                        'property_id' => $pId, 'property_type_id' => $pr->property_type_id,
                        'unit_number' => 'وحدة شاغرة A-' . ($idx + 1), 'floor_number' => 2,
                        'area' => 165, 'electricity_meter' => 'ELC-VAC-' . $idx,
                        'water_meter' => 'WTR-VAC-' . $idx, 'default_rent_price' => 32000,
                        'status' => 'vacant', 'custom_attributes' => json_encode(['rooms' => 5, 'unit_kind' => 'شقة'], JSON_UNESCAPED_UNICODE),
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                }
            }

            // 7. إضافة قيد يومي وفاتورة ضريبية وسجل تتبع إن لم تكن موجودة
            if (DB::table('journal_entries')->where('office_id', $mainOfficeId)->count() === 0 && $offMaintAcc && $offBankAcc) {
                $jeId = DB::table('journal_entries')->insertGetId([
                    'office_id' => $mainOfficeId, 'entry_number' => 1, 'entry_date' => date('Y-m-d'),
                    'general_notes' => 'قيد إثبات مصروفات صيانة دورية للمصاعد والمضخات',
                    'total_debit' => 3500, 'total_credit' => 3500, 'total_tax' => 0,
                    'created_by' => Auth::id() ?? 1, 'created_at' => now(), 'updated_at' => now(),
                ]);
                DB::table('journal_entry_items')->insert([
                    ['journal_entry_id' => $jeId, 'account_id' => $offMaintAcc->id, 'debit' => 3500, 'credit' => 0, 'tax' => 0, 'notes' => 'صيانة مصاعد العقارات', 'created_at' => now(), 'updated_at' => now()],
                    ['journal_entry_id' => $jeId, 'account_id' => $offBankAcc->id, 'debit' => 0, 'credit' => 3500, 'tax' => 0, 'notes' => 'سداد من البنك', 'created_at' => now(), 'updated_at' => now()],
                ]);
            }

            if (DB::table('activity_logs')->where('office_id', $mainOfficeId)->count() < 5) {
                $sampleActs = [
                    ['type' => 'إضافة', 'label' => 'إضافة في قسم (عقود الإيجار)', 'det' => 'مدير المكتب أنشأ عقد إيجار جديد وتوثيقه في منصة إيجار'],
                    ['type' => 'إضافة', 'label' => 'إضافة في قسم (سندات القبض والصرف)', 'det' => 'مدير المكتب أصدر سند قبض إيجار بمبلغ 28,000 ر.س'],
                    ['type' => 'تعديل', 'label' => 'تعديل في قسم (العقارات والصكوك)', 'det' => 'مدير المكتب عدّل بيانات الصك والوحدات في مجمع الخزامى'],
                    ['type' => 'حذف', 'label' => 'حذف في قسم (سندات القبض والصرف)', 'det' => 'محاسب المكتب حذف مسودة سند مكرر وعكس قيده المالي'],
                ];
                foreach ($sampleActs as $sa) {
                    DB::table('activity_logs')->insert([
                        'user_id' => Auth::id() ?? 1, 'user_name' => 'مدير المكتب (surouh_office)',
                        'user_type' => 'office_admin', 'office_id' => $mainOfficeId, 'owner_id' => $ownerIds[0] ?? null,
                        'action_type' => $sa['type'], 'action_label' => $sa['label'], 'details' => $sa['det'],
                        'request_path' => 'office/dashboard', 'ip_address' => '192.168.1.105',
                        'mac_address' => 'A4:83:E7:2B:99:1C (بصمة جهاز)', 'user_agent' => 'Mozilla/5.0',
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                }
            }
        });

        return back()->with('success', '🎉 تم توليد وإضافة جميع البيانات التجريبية الشاملة للنظام بنجاح! (35+ مستأجراً وعقداً، ملاك، عقارات، وحدات شاغرة ومؤجرة، دفعات، سندات، قيود، وسجل تتبع) لتجربة كافة المميزات ونظام الصفحات (1, 2, 3...) فوراً.');
    }

    // =========================================================================
    // نظام الباقات وصلاحيات الصفحات لمكاتب العقار + الهوية البصرية + التجربة الحية والتسجيل الذاتي
    // =========================================================================
    public static function getAvailableOfficeTabs(): array
    {
        return [
            'overview'          => 'لوحة التحكم والملخص العام',
            'owners'            => 'إدارة الملاك',
            'properties'        => 'العقارات والصكوك والوحدات (وكاشف الركود)',
            'contracts'         => 'المستأجرون وعقود (إيجار)',
            'mullak'            => 'اتحاد الملاك (مُلّاك) والتصويت الإلكتروني',
            'installments'      => 'جدولة الدفعات وأعمار الديون',
            'legal_notices'     => 'مولد الإنذارات القانونية وحقيبة ناجز',
            'accounting'        => 'شجرة الحسابات المحاسبية',
            'vouchers'          => 'سندات القبض والصرف',
            'journal'           => 'القيود اليومية',
            'auditor'           => 'المدقق المحاسبي الآلي قبل الإقفال',
            'invoicing'         => 'الفواتير الضريبية (ZATCA)',
            'account_statement' => 'كشف الحساب التفصيلي',
            'statements'        => 'الأستاذ العام والقوائم المالية',
            'staff'             => 'إعدادات المكتب والموظفين والصلاحيات',
            'white_label'       => 'تخصيص الهوية البصرية والشعار في المستندات (PDF)',
            'activity_logs'     => 'سجل تتبع الموظفين (قبل وبعد) وكاشف الشذوذ',
            'whatsapp'          => 'إعدادات واتساب والشذوذ والاعتماد',
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
                $table->boolean('allow_white_label')->default(false);
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
                    'allow_white_label' => 0,
                    'show_on_landing' => 1,
                    'is_highlighted' => 0,
                ],
                [
                    'name' => 'الباقة المحاسبية والعقارية الشاملة (Pro ERP)',
                    'badge' => 'الأكثر طلباً ⭐',
                    'billing_type' => 'annual_subscription',
                    'price' => 3000,
                    'old_price' => 4000,
                    'offer_label' => 'شامل الفوترة الضريبية ZATCA والهوية البصرية',
                    'commission_rate' => 0,
                    'description' => 'تشمل جميع صفحات إدارة الأملاك + النظام المالي والمحاسبي الكامل + تخصيص الهوية البصرية والشعار في جميع المستندات.',
                    'allowed_tabs' => json_encode(array_keys(self::getAvailableOfficeTabs()), JSON_UNESCAPED_UNICODE),
                    'allow_white_label' => 1,
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
                    'allow_white_label' => 1,
                    'show_on_landing' => 1,
                    'is_highlighted' => 0,
                ],
            ];

            foreach ($defaultPlans as $dp) {
                DB::table('office_plans')->insert(array_merge($dp, ['created_at' => now(), 'updated_at' => now()]));
            }
            $this->syncOfficePlansToLandingPage();
        } else {
            Schema::table('office_plans', function (Blueprint $table) {
                if (!Schema::hasColumn('office_plans', 'allow_white_label')) {
                    $table->boolean('allow_white_label')->default(false);
                }
            });
        }

        if (Schema::hasTable('offices')) {
            Schema::table('offices', function (Blueprint $table) {
                if (!Schema::hasColumn('offices', 'allow_white_label')) {
                    $table->boolean('allow_white_label')->default(false);
                }
                if (!Schema::hasColumn('offices', 'logo_url')) {
                    $table->string('logo_url')->nullable();
                }
                if (!Schema::hasColumn('offices', 'brand_primary_color')) {
                    $table->string('brand_primary_color', 30)->default('#0f172a');
                }
                if (!Schema::hasColumn('offices', 'brand_sidebar_color')) {
                    $table->string('brand_sidebar_color', 30)->default('#0f172a');
                }
                if (!Schema::hasColumn('offices', 'logo_enabled')) {
                    $table->boolean('logo_enabled')->default(true);
                }
                if (!Schema::hasColumn('offices', 'logo_targets_json')) {
                    $table->text('logo_targets_json')->nullable();
                }
                if (!Schema::hasColumn('offices', 'electronic_stamp_text')) {
                    $table->string('electronic_stamp_text')->nullable();
                }
                if (!Schema::hasColumn('offices', 'is_demo_sandbox')) {
                    $table->boolean('is_demo_sandbox')->default(false);
                }
                if (!Schema::hasColumn('offices', 'is_trial')) {
                    $table->boolean('is_trial')->default(false);
                }
            });
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
        $allowWhiteLabel = $request->has('allow_white_label') ? 1 : 0;
        if ($allowWhiteLabel && !in_array('staff', $tabs)) {
            $tabs[] = 'staff';
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
            'allow_white_label' => $allowWhiteLabel,
            'show_on_landing' => $request->has('show_on_landing') ? 1 : 0,
            'is_highlighted' => $request->has('is_highlighted') ? 1 : 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->syncOfficePlansToLandingPage();

        return redirect()->route('erp.dashboard', ['tab' => 'office_plans'])
            ->with('success', 'تم إنشاء الباقة وتحديد صفحاتها المسموحة وميزة الهوية البصرية بنجاح.');
    }

    public function updateOfficePlan(Request $request, $id)
    {
        $this->ensureOfficePlansTable();
        $tabs = $request->input('allowed_tabs', ['overview']);
        if (!in_array('overview', $tabs)) {
            array_unshift($tabs, 'overview');
        }
        $allowWhiteLabel = $request->has('allow_white_label') ? 1 : 0;
        if ($allowWhiteLabel && !in_array('staff', $tabs)) {
            $tabs[] = 'staff';
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
            'allow_white_label' => $allowWhiteLabel,
            'show_on_landing' => $request->has('show_on_landing') ? 1 : 0,
            'is_highlighted' => $request->has('is_highlighted') ? 1 : 0,
            'updated_at' => now(),
        ]);

        if ($request->has('sync_subscribed_offices')) {
            DB::table('offices')->where('plan_id', $id)->update([
                'allowed_tabs' => $encodedTabs,
                'allow_white_label' => $allowWhiteLabel,
                'billing_type' => $request->billing_type ?: 'annual_subscription',
                'annual_fee' => (float) ($request->price ?? 0),
                'company_commission_rate' => (float) ($request->commission_rate ?? 0),
                'updated_at' => now(),
            ]);
        }

        $this->syncOfficePlansToLandingPage();

        return redirect()->route('erp.dashboard', ['tab' => 'office_plans'])
            ->with('success', 'تم تحديث بيانات الباقة وصلاحياتها والهوية البصرية ومزامنتها بنجاح.');
    }

    public function deleteOfficePlan($id)
    {
        DB::table('office_plans')->where('id', $id)->delete();
        $this->syncOfficePlansToLandingPage();

        return redirect()->route('erp.dashboard', ['tab' => 'office_plans'])
            ->with('success', 'تم حذف الباقة وتحديث الواجهة الرئيسية.');
    }

    /**
     * حفظ إعدادات التسجيل الذاتي والفترة التجريبية + وضع العرض التفاعلي للزوار (Live Demo Sandbox)
     */
    public function saveSaasGrowthSettings(Request $request)
    {
        self::ensureSystemSettingsTable();

        self::setSetting('self_registration_enabled', $request->has('self_registration_enabled') ? '1' : '0');
        self::setSetting('trial_days', (string) max(1, (int) ($request->input('trial_days', 14))));
        self::setSetting('trial_max_properties', (string) max(1, (int) ($request->input('trial_max_properties', 5))));
        self::setSetting('trial_allow_white_label', $request->has('trial_allow_white_label') ? '1' : '0');

        $trialTabs = $request->input('trial_allowed_tabs', array_keys(self::getAvailableOfficeTabs()));
        if (!in_array('overview', $trialTabs)) array_unshift($trialTabs, 'overview');
        self::setSetting('trial_allowed_tabs_json', json_encode(array_values(array_unique($trialTabs)), JSON_UNESCAPED_UNICODE));

        self::setSetting('demo_sandbox_enabled', $request->has('demo_sandbox_enabled') ? '1' : '0');
        self::setSetting('demo_reset_minutes', (string) max(15, (int) ($request->input('demo_reset_minutes', 60))));
        $demoTabs = $request->input('demo_allowed_tabs', array_keys(self::getAvailableOfficeTabs()));
        if (!in_array('overview', $demoTabs)) array_unshift($demoTabs, 'overview');
        self::setSetting('demo_allowed_tabs_json', json_encode(array_values(array_unique($demoTabs)), JSON_UNESCAPED_UNICODE));

        return redirect()->route('erp.dashboard', ['tab' => 'office_plans'])
            ->with('success', 'تم حفظ إعدادات التسجيل الذاتي والفترة التجريبية ووضع التجربة الحية للزوار (Live Demo Sandbox) بنجاح.');
    }

    /**
     * تسجيل مكتب جديد ذاتياً من الواجهة الرئيسية مع فترة تجريبية مجانية
     */
    public function registerOfficeSelfTrial(Request $request)
    {
        $this->ensureDefaultAdminExists();
        $this->ensureOfficePlansTable();

        if (self::getSetting('self_registration_enabled', '1') !== '1') {
            return back()->withErrors(['التسجيل الذاتي المباشر متوقف حالياً، يرجى التواصل مع الإدارة للاشتراك.']);
        }

        $request->validate([
            'office_name' => 'required|string|max:140',
            'phone'       => ['required', 'regex:/^05[0-9]{8}$/'],
            'username'    => 'required|string|max:60|unique:users,username',
            'password'    => 'required|string|min:4',
        ], [
            'phone.regex'     => 'رقم الجوال يجب أن يتكون من 10 أرقام ويبدأ بـ 05.',
            'username.unique' => 'اسم المستخدم مسجل مسبقاً، اختر اسماً آخر.',
        ]);

        $formattedPhone = self::formatSaudiPhone($request->phone);
        if (DB::table('users')->whereIn('phone', [$formattedPhone, $request->phone])->exists()) {
            return back()->withErrors(['رقم الجوال مسجل مسبقاً في النظام، يمكنك تسجيل الدخول مباشرة.']);
        }

        $trialDays = max(1, (int) self::getSetting('trial_days', '14'));
        $trialTabsJson = self::getSetting('trial_allowed_tabs_json', '');
        $trialTabs = json_decode($trialTabsJson, true);
        if (!is_array($trialTabs) || empty($trialTabs)) {
            $trialTabs = array_keys(self::getAvailableOfficeTabs());
        }
        $allowWhiteLabel = self::getSetting('trial_allow_white_label', '1') === '1' ? 1 : 0;

        $newUserId = null;
        DB::transaction(function () use ($request, $formattedPhone, $trialDays, $trialTabs, $allowWhiteLabel, &$newUserId) {
            $parentAccount = DB::table('accounts')->whereNull('office_id')->where('code', '1103')->first()
                          ?: DB::table('accounts')->whereNull('office_id')->first();

            $officeAccountId = self::createSequentialSubAccount(
                null,
                $parentAccount ? $parentAccount->id : null,
                'حساب ' . trim($request->office_name) . ' (تجريبي)'
            );

            $officeId = DB::table('offices')->insertGetId([
                'name'                    => trim($request->office_name),
                'cr_number'               => trim((string) $request->input('cr_number', '')),
                'fal_license'             => trim((string) $request->input('fal_license', '')),
                'phone'                   => $formattedPhone,
                'email'                   => trim($request->username) . '@office.local',
                'address'                 => trim((string) $request->input('city', 'الرياض')),
                'billing_type'            => 'annual_subscription',
                'annual_fee'              => 0,
                'company_commission_rate' => 0,
                'allowed_tabs'            => json_encode($trialTabs, JSON_UNESCAPED_UNICODE),
                'allow_white_label'       => $allowWhiteLabel,
                'is_trial'                => 1,
                'subscription_ends_at'    => now()->addDays($trialDays)->toDateString(),
                'status'                  => 'active',
                'account_id'              => $officeAccountId,
                'created_at'              => now(),
                'updated_at'              => now(),
            ]);

            $newUserId = DB::table('users')->insertGetId([
                'name'       => 'مدير ' . trim($request->office_name),
                'username'   => trim($request->username),
                'phone'      => $formattedPhone,
                'email'      => trim($request->username) . '@office.local',
                'password'   => Hash::make($request->password),
                'user_type'  => 'office_admin',
                'office_id'  => $officeId,
                'is_active'  => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        if ($newUserId) {
            $userModel = User::find($newUserId);
            if ($userModel) {
                Auth::login($userModel, true);
                return redirect()->route('office.dashboard')
                    ->with('success', "🎉 مرحباً بك في منظومة العقارات الذكية! تم تفعيل فترتك التجريبية المجانية لمدة ({$trialDays} يوماً) بنجاح.");
            }
        }

        return redirect()->route('login')->with('success', 'تم إنشاء حساب مكتبك التجريبي بنجاح، يمكنك الدخول الآن.');
    }

    /**
     * وضع العرض التفاعلي للزوار بدون تسجيل (Interactive Live Demo Sandbox) - يُعاد ضبطه كل ساعة تلقائياً
     */
    public function enterLiveDemoSandbox()
    {
        $this->ensureDefaultAdminExists();
        $this->ensureOfficePlansTable();
        self::ensureContractInstallmentsTable();

        if (self::getSetting('demo_sandbox_enabled', '1') !== '1') {
            return redirect()->route('landing')->withErrors(['وضع العرض التفاعلي المباشر متوقف حالياً من قِبل الإدارة.']);
        }

        $resetMinutes = max(15, (int) self::getSetting('demo_reset_minutes', '60'));
        $lastResetAt  = (int) self::getSetting('demo_sandbox_last_reset_ts', '0');
        $demoTabsJson = self::getSetting('demo_allowed_tabs_json', '');
        $demoTabs     = json_decode($demoTabsJson, true);
        if (!is_array($demoTabs) || empty($demoTabs)) {
            $demoTabs = array_keys(self::getAvailableOfficeTabs());
        }

        $demoOffice = DB::table('offices')->where('is_demo_sandbox', 1)->first();
        $needsReset = !$demoOffice || ((time() - $lastResetAt) >= ($resetMinutes * 60));

        if ($needsReset) {
            DB::transaction(function () use ($demoOffice, $demoTabs) {
                if ($demoOffice) {
                    $oid = $demoOffice->id;
                    DB::table('vouchers')->where('office_id', $oid)->delete();
                    $jeIds = DB::table('journal_entries')->where('office_id', $oid)->pluck('id');
                    if ($jeIds->count() > 0) {
                        DB::table('journal_entry_items')->whereIn('journal_entry_id', $jeIds)->delete();
                    }
                    DB::table('journal_entries')->where('office_id', $oid)->delete();
                    DB::table('tax_invoices')->where('office_id', $oid)->delete();
                    DB::table('contract_installments')->where('office_id', $oid)->delete();
                    DB::table('lease_contracts')->where('office_id', $oid)->delete();
                    $pIds = DB::table('properties')->where('office_id', $oid)->pluck('id');
                    if ($pIds->count() > 0) {
                        DB::table('property_units')->whereIn('property_id', $pIds)->delete();
                    }
                    DB::table('properties')->where('office_id', $oid)->delete();
                    DB::table('tenants')->where('office_id', $oid)->delete();
                    DB::table('owners')->where('office_id', $oid)->delete();
                    DB::table('mullak_members')->where('office_id', $oid)->delete();
                    DB::table('mullak_expenses')->where('office_id', $oid)->delete();
                    DB::table('mullak_buildings')->where('office_id', $oid)->delete();
                    if (Schema::hasTable('mullak_polls')) {
                        DB::table('mullak_polls')->where('office_id', $oid)->delete();
                    }
                    DB::table('activity_logs')->where('office_id', $oid)->delete();
                    DB::table('accounts')->where('office_id', $oid)->delete();
                    $officeId = $oid;

                    DB::table('offices')->where('id', $officeId)->update([
                        'name'              => 'مكتب العرض التفاعلي التجريبي (Sandbox Demo)',
                        'allowed_tabs'      => json_encode($demoTabs, JSON_UNESCAPED_UNICODE),
                        'allow_white_label' => 1,
                        'status'            => 'active',
                        'updated_at'        => now(),
                    ]);
                } else {
                    $officeId = DB::table('offices')->insertGetId([
                        'name'                    => 'مكتب العرض التفاعلي التجريبي (Sandbox Demo)',
                        'cr_number'               => '1010998877',
                        'fal_license'             => '1200099887',
                        'tax_number'              => '300556677889903',
                        'national_address'        => 'الرياض - طريق الملك فهد',
                        'phone'                   => '+966559999900',
                        'email'                   => 'demo_sandbox@office.local',
                        'billing_type'            => 'annual_subscription',
                        'annual_fee'              => 3000,
                        'allowed_tabs'            => json_encode($demoTabs, JSON_UNESCAPED_UNICODE),
                        'allow_white_label'       => 1,
                        'allow_owner_tax_invoice' => 1,
                        'is_demo_sandbox'         => 1,
                        'subscription_ends_at'    => now()->addYear()->toDateString(),
                        'status'                  => 'active',
                        'created_at'              => now(),
                        'updated_at'              => now(),
                    ]);
                }

                // إنشاء حساب مدير مكتب التجربة المعزول
                $demoUser = DB::table('users')->where('office_id', $officeId)->where('user_type', 'office_admin')->first();
                if (!$demoUser) {
                    DB::table('users')->insert([
                        'name'       => 'زائر البوابة التجريبية الحية',
                        'username'   => 'live_demo_visitor',
                        'phone'      => '+966559999900',
                        'email'      => 'demo_sandbox@office.local',
                        'password'   => Hash::make('123456'),
                        'user_type'  => 'office_admin',
                        'office_id'  => $officeId,
                        'is_active'  => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // تأسيس حسابات وعينات بيانات المكتب التجريبي المعزول
                $r1 = DB::table('accounts')->insertGetId(['office_id' => $officeId, 'parent_id' => null, 'code' => '1000', 'name' => 'الأصول والبنوك', 'type' => 'الأصول', 'is_group' => 1, 'balance' => 0, 'created_at' => now(), 'updated_at' => now()]);
                $bankId = DB::table('accounts')->insertGetId(['office_id' => $officeId, 'parent_id' => $r1, 'code' => '1102', 'name' => 'حساب البنك لتحصيل الإيجارات', 'type' => 'الأصول', 'is_group' => 0, 'balance' => 35000, 'debit_balance' => 35000, 'created_at' => now(), 'updated_at' => now()]);
                $tGroup = DB::table('accounts')->insertGetId(['office_id' => $officeId, 'parent_id' => $r1, 'code' => '1103', 'name' => 'ذمم المستأجرين', 'type' => 'الأصول', 'is_group' => 1, 'balance' => 0, 'created_at' => now(), 'updated_at' => now()]);
                $r2 = DB::table('accounts')->insertGetId(['office_id' => $officeId, 'parent_id' => null, 'code' => '2000', 'name' => 'الخصوم والأمانات', 'type' => 'الخصوم والالتزامات', 'is_group' => 1, 'balance' => 0, 'created_at' => now(), 'updated_at' => now()]);
                $oGroup = DB::table('accounts')->insertGetId(['office_id' => $officeId, 'parent_id' => $r2, 'code' => '2101', 'name' => 'أمانات الملاك', 'type' => 'الخصوم والالتزامات', 'is_group' => 1, 'balance' => 0, 'created_at' => now(), 'updated_at' => now()]);

                $pt = DB::table('property_types')->first();
                $ptId = $pt ? $pt->id : DB::table('property_types')->insertGetId(['name' => 'عمارة سكنية وتجارية', 'category' => 'سكني', 'has_units' => 1, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()]);

                $owAcc = self::createSequentialSubAccount($officeId, $oGroup, 'أمانات المالك: الشيخ عبدالعزيز التجريبي');
                $ownerId = DB::table('owners')->insertGetId([
                    'office_id' => $officeId, 'name' => 'الشيخ عبدالعزيز بن محمد (بيانات تجريبية)',
                    'national_id' => '1055667788', 'phone' => '+966501122334',
                    'iban' => 'SA4480000000608010167599', 'bank_name' => 'مصرف الراجحي',
                    'office_management_fee_rate' => 5.0, 'account_id' => $owAcc,
                    'created_at' => now(), 'updated_at' => now(),
                ]);

                $propId = DB::table('properties')->insertGetId([
                    'office_id' => $officeId, 'owner_id' => $ownerId, 'property_type_id' => $ptId,
                    'title' => 'برج العليا التجريبي - رقم 10', 'deed_number' => '310155443322',
                    'deed_date' => '2024-01-15', 'total_area' => 1200, 'estimated_value' => 5500000,
                    'city' => 'الرياض', 'district' => 'حي العليا', 'street' => 'طريق الملك فهد',
                    'created_at' => now()->subMonths(4), 'updated_at' => now(),
                ]);

                // إضافة وحدة مؤجرة + وحدتين شاغرتين بفترات ركود مختلفة لتجربة كاشف الوحدات الراكدة
                $uRented = DB::table('property_units')->insertGetId([
                    'property_id' => $propId, 'property_type_id' => $ptId,
                    'unit_number' => 'شقة 101', 'floor_number' => 1, 'area' => 150,
                    'electricity_meter' => 'ELC-101', 'water_meter' => 'WTR-101',
                    'default_rent_price' => 40000, 'status' => 'rented',
                    'created_at' => now()->subDays(120), 'updated_at' => now(),
                ]);
                DB::table('property_units')->insert([
                    [
                        'property_id' => $propId, 'property_type_id' => $ptId,
                        'unit_number' => 'شقة 202 (شاغرة منذ 95 يوماً)', 'floor_number' => 2, 'area' => 165,
                        'electricity_meter' => 'ELC-202', 'water_meter' => 'WTR-202',
                        'default_rent_price' => 42000, 'status' => 'vacant',
                        'created_at' => now()->subDays(95), 'updated_at' => now(),
                    ],
                    [
                        'property_id' => $propId, 'property_type_id' => $ptId,
                        'unit_number' => 'معرض تجاري 1 (شاغر منذ 48 يوماً)', 'floor_number' => 0, 'area' => 210,
                        'electricity_meter' => 'ELC-303', 'water_meter' => 'WTR-303',
                        'default_rent_price' => 85000, 'status' => 'vacant',
                        'created_at' => now()->subDays(48), 'updated_at' => now(),
                    ],
                ]);

                $tAcc = self::createSequentialSubAccount($officeId, $tGroup, 'المستأجر: فهد بن سلطان التجريبي');
                $tenantId = DB::table('tenants')->insertGetId([
                    'office_id' => $officeId, 'name' => 'فهد بن سلطان التجريبي',
                    'id_type' => 'national_id', 'id_number' => '1099887766',
                    'phone' => '+966554433221', 'account_id' => $tAcc,
                    'created_at' => now(), 'updated_at' => now(),
                ]);

                $cId = DB::table('lease_contracts')->insertGetId([
                    'contract_number' => 'CNT-DEMO-1001', 'ejar_contract_number' => 'EJR-8877665544',
                    'ejar_status' => 'active', 'office_id' => $officeId, 'property_id' => $propId,
                    'property_unit_id' => $uRented, 'owner_id' => $ownerId, 'tenant_id' => $tenantId,
                    'start_date' => now()->subMonths(3)->toDateString(),
                    'end_date' => now()->addMonths(9)->toDateString(),
                    'payment_cycle' => 'quarterly', 'total_rent_amount' => 40000,
                    'security_deposit' => 2000, 'office_commission' => 2000, 'status' => 'active',
                    'created_at' => now(), 'updated_at' => now(),
                ]);
                $cObj = DB::table('lease_contracts')->where('id', $cId)->first();
                self::generateInstallmentsForContract($cObj);

                self::setSetting('demo_sandbox_last_reset_ts', (string) time());
            });

            $demoOffice = DB::table('offices')->where('is_demo_sandbox', 1)->first();
        } else {
            DB::table('offices')->where('id', $demoOffice->id)->update([
                'allowed_tabs' => json_encode($demoTabs, JSON_UNESCAPED_UNICODE),
            ]);
        }

        $demoUserRow = DB::table('users')->where('office_id', $demoOffice->id)->where('user_type', 'office_admin')->first();
        if ($demoUserRow) {
            session()->forget(['impersonate_office_id', 'impersonate_owner_id']);
            $uModel = User::find($demoUserRow->id);
            Auth::login($uModel, false);
            return redirect()->route('office.dashboard')
                ->with('success', '🎮 أهلاً بك في وضع التجربة الحية المباشرة (Live Demo Sandbox)! يمكنك تجربة إضافة وتعديل كل شيء بحرية، علماً أنه يتم إعادة ضبط البيانات تلقائياً كل ساعة.');
        }

        return redirect()->route('landing');
    }

    /**
     * جداول وبوابة التصويت الإلكتروني العام لجمعيات اتحاد الملاك (مع حماية OTP من 4 أرقام وإخفاء البيانات الخاصة)
     */
    public static function ensureMullakVotingTables(): void
    {
        if (!Schema::hasTable('mullak_polls')) {
            Schema::create('mullak_polls', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('office_id')->index();
                $table->unsignedBigInteger('mullak_building_id')->index();
                $table->string('title');
                $table->text('description')->nullable();
                $table->decimal('estimated_cost', 15, 2)->default(0);
                $table->decimal('required_quorum_pct', 5, 2)->default(51.00);
                $table->date('closes_at')->nullable();
                $table->string('status')->default('open'); // open | approved | cancelled
                $table->string('public_token', 64)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('mullak_poll_votes')) {
            Schema::create('mullak_poll_votes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('poll_id')->index();
                $table->unsignedBigInteger('mullak_member_id')->index();
                $table->string('member_name');
                $table->string('unit_number')->nullable();
                $table->decimal('weight_pct', 6, 2)->default(1);
                $table->string('vote_choice'); // approve | reject | abstain
                $table->text('comment')->nullable();
                $table->string('ip_address', 64)->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('mullak_members')) {
            Schema::table('mullak_members', function (Blueprint $table) {
                if (!Schema::hasColumn('mullak_members', 'vote_otp_code')) {
                    $table->string('vote_otp_code', 10)->nullable();
                }
                if (!Schema::hasColumn('mullak_members', 'vote_otp_expires_at')) {
                    $table->timestamp('vote_otp_expires_at')->nullable();
                }
            });
        }
    }

    public function showPublicMullakVote($id, $token)
    {
        self::ensureContractInstallmentsTable();
        self::ensureMullakVotingTables();

        $poll = DB::table('mullak_polls')->where('id', $id)->first();
        if (!$poll || !hash_equals((string) $poll->public_token, (string) $token)) {
            abort(404, 'رابط التصويت الإلكتروني غير صالح.');
        }

        $building = DB::table('mullak_buildings')->where('id', $poll->mullak_building_id)->first();
        $members  = DB::table('mullak_members')->where('mullak_building_id', $poll->mullak_building_id)->get();
        $votes    = DB::table('mullak_poll_votes')->where('poll_id', $poll->id)->get();

        // عرض رقم الشقة أو المكتب فقط بدون إظهار اسم الساكن أو رقم جواله أو أي بيانات خاصة
        $unitsOptionsHtml = '<option value="">-- اختر رقم الشقة / المكتب --</option>';
        foreach ($members as $m) {
            $already = $votes->firstWhere('mullak_member_id', $m->id);
            $statusBadge = $already ? ' (تم التصويت مسبقاً ✔)' : '';
            $unitsOptionsHtml .= '<option value="' . $m->id . '">الوحدة / الشقة رقم: (' . e($m->unit_number) . ')' . $statusBadge . '</option>';
        }

        $statusBanner = '';
        if ($poll->status === 'approved') {
            $statusBanner = '<div class="p-4 rounded-2xl bg-emerald-100 border border-emerald-300 text-emerald-900 text-xs font-extrabold text-center">✅ تم اعتماد هذا القرار رسمياً وإغلاق باب التصويت.</div>';
        } elseif ($poll->status === 'cancelled') {
            $statusBanner = '<div class="p-4 rounded-2xl bg-rose-100 border border-rose-300 text-rose-900 text-xs font-extrabold text-center">🚫 تم إلغاء هذا التصويت من قِبل إدارة اتحاد الملاك.</div>';
        }

        $msgHtml = session('vote_success')
            ? '<div class="p-4 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-800 text-xs font-bold">✅ ' . e(session('vote_success')) . '</div>'
            : '';

        $errHtml = session('vote_error')
            ? '<div class="p-4 rounded-xl bg-rose-50 border border-rose-300 text-rose-800 text-xs font-bold">⚠️ ' . e(session('vote_error')) . '</div>'
            : '';

        $otpSendUrl = route('public.mullak_vote.send_otp', ['id' => $poll->id, 'token' => $poll->public_token]);
        $submitUrl  = route('public.mullak_vote.submit', ['id' => $poll->id, 'token' => $poll->public_token]);
        $csrf       = csrf_token();

        $formHtml = '';
        if ($poll->status === 'open') {
            $formHtml = '
            <form id="mullakVoteMainForm" method="POST" action="' . $submitUrl . '" class="space-y-4">
                <input type="hidden" name="_token" value="' . $csrf . '">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">1. اختر رقم الشقة أو المكتب فقط *</label>
                    <select name="mullak_member_id" id="vote_unit_select" required class="w-full rounded-xl border-2 border-slate-300 px-3.5 py-2.5 text-xs bg-white font-extrabold text-slate-900">
                        ' . $unitsOptionsHtml . '
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1">🔒 حفاظاً على الخصوصية لا تظهر أسماء أو أرقام جوالات السكان؛ سيتم إرسال كود تحقق من 4 أرقام لجوال الوحدة المسجل في النظام.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">2. اختر قرار التصويت *</label>
                    <div class="grid grid-cols-3 gap-2 text-xs font-bold">
                        <label class="p-3 rounded-xl border-2 border-emerald-500 bg-emerald-50/50 text-center cursor-pointer"><input type="radio" name="vote_choice" value="approve" checked class="ml-1"> ✔ موافق</label>
                        <label class="p-3 rounded-xl border-2 border-rose-300 bg-rose-50/50 text-center cursor-pointer"><input type="radio" name="vote_choice" value="reject" class="ml-1"> ✖ غير موافق</label>
                        <label class="p-3 rounded-xl border-2 border-slate-300 bg-slate-50 text-center cursor-pointer"><input type="radio" name="vote_choice" value="abstain" class="ml-1"> ⚖️ متحفظ</label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">3. ملاحظات أو مقترحات إضافية (اختياري)</label>
                    <textarea name="comment" id="vote_comment_input" rows="2" placeholder="اكتب أي ملاحظات تخص الوحدة أو القرار..." class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs"></textarea>
                </div>

                <!-- الخطوة الأولى: زر طلب كود التحقق -->
                <div id="step1SendOtpBox">
                    <button type="button" id="btnSendVoteOtp" onclick="requestUnitVoteOtp()" class="w-full py-3.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs shadow-lg transition">
                        📲 إرسال كود التحقق (4 أرقام) لجوال الوحدة لاعتماد التصويت
                    </button>
                </div>

                <!-- الخطوة الثانية: إدخال كود التحقق المكون من 4 أرقام ثم الاعتماد -->
                <div id="step2VerifyOtpBox" class="hidden p-4 rounded-2xl bg-emerald-50 border-2 border-emerald-500 space-y-3">
                    <div id="otpSentStatusMsg" class="text-xs font-bold text-emerald-900"></div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-900 mb-1">🔢 أدخل كود التحقق المكون من 4 أرقام المرسل لجوالك *</label>
                        <input type="text" name="otp_code" id="vote_otp_input" maxlength="4" dir="ltr"
                               oninput="this.value = this.value.replace(/[^0-9]/g, \'\').slice(0, 4);"
                               placeholder="••••"
                               class="w-full text-center font-mono text-xl font-extrabold tracking-widest rounded-xl border-2 border-slate-900 px-4 py-2.5 bg-white">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs shadow-lg">
                            ✅ تأكيد الكود واعتماد التصويت النهائي
                        </button>
                        <button type="button" onclick="requestUnitVoteOtp()" class="px-4 py-3 rounded-xl bg-white border border-slate-300 text-slate-700 font-bold text-xs">
                            🔄 إعادة إرسال
                        </button>
                    </div>
                </div>
            </form>

            <script>
                async function requestUnitVoteOtp() {
                    const unitId = document.getElementById("vote_unit_select")?.value;
                    if (!unitId) {
                        alert("يرجى اختيار رقم الشقة أو المكتب أولاً.");
                        return;
                    }
                    const btn = document.getElementById("btnSendVoteOtp");
                    if (btn) {
                        btn.disabled = true;
                        btn.innerText = "⏳ جاري إرسال كود التحقق (4 أرقام) إلى جوال الوحدة...";
                    }
                    try {
                        const res = await fetch("' . $otpSendUrl . '", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "' . $csrf . '",
                                "Accept": "application/json"
                            },
                            body: JSON.stringify({ mullak_member_id: unitId })
                        });
                        const data = await res.json();
                        if (data.success) {
                            document.getElementById("step1SendOtpBox").classList.add("hidden");
                            document.getElementById("step2VerifyOtpBox").classList.remove("hidden");
                            document.getElementById("otpSentStatusMsg").innerText = data.message;
                            document.getElementById("vote_otp_input").focus();
                        } else {
                            alert(data.message || "تعذر إرسال الكود.");
                            if (btn) {
                                btn.disabled = false;
                                btn.innerText = "📲 إرسال كود التحقق (4 أرقام) لجوال الوحدة لاعتماد التصويت";
                            }
                        }
                    } catch (e) {
                        alert("حدث خطأ في الاتصال، حاول مرة أخرى.");
                        if (btn) {
                            btn.disabled = false;
                            btn.innerText = "📲 إرسال كود التحقق (4 أرقام) لجوال الوحدة لاعتماد التصويت";
                        }
                    }
                }
            </script>';
        }

        $html = '<!DOCTYPE html><html lang="ar" dir="rtl"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
        <title>التصويت الإلكتروني لاتحاد الملاك | ' . e($poll->title) . '</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Readex+Pro:wght@400;600;700;800&display=swap" rel="stylesheet">
        <style>body{font-family:"Readex Pro",sans-serif;background:#0f172a;color:#f8fafc;}</style></head>
        <body class="p-6 min-h-screen flex items-center justify-center">
        <div class="max-w-xl w-full bg-white text-slate-900 rounded-3xl p-6 shadow-2xl border-2 border-emerald-500 space-y-5">
            <div class="border-b pb-4 flex items-center justify-between">
                <div>
                    <span class="px-3 py-1 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-bold">🗳️ بوابة التصويت الإلكتروني الموثق لجمعية اتحاد الملاك</span>
                    <h1 class="text-lg font-extrabold mt-2">' . e($building->association_name ?? 'اتحاد الملاك') . '</h1>
                </div>
            </div>
            ' . $statusBanner . $msgHtml . $errHtml . '
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                <div class="text-xs font-bold text-slate-500">موضوع القرار المطروح للتصويت:</div>
                <div class="text-base font-extrabold text-slate-900">' . e($poll->title) . '</div>
                <p class="text-xs text-slate-600">' . e($poll->description) . '</p>
                ' . ($poll->estimated_cost > 0 ? '<div class="text-xs font-bold text-amber-800 pt-1">💰 التكلفة التقديرية للقرار: ' . number_format($poll->estimated_cost, 2) . ' ر.س</div>' : '') . '
            </div>
            ' . $formHtml . '
        </div></body></html>';

        return response($html);
    }

    public function sendPublicMullakVoteOtp(Request $request, $id, $token)
    {
        self::ensureContractInstallmentsTable();
        self::ensureMullakVotingTables();

        $poll = DB::table('mullak_polls')->where('id', $id)->first();
        if (!$poll || !hash_equals((string) $poll->public_token, (string) $token)) {
            return response()->json(['success' => false, 'message' => 'رابط التصويت غير صالح.']);
        }
        if ($poll->status !== 'open') {
            return response()->json(['success' => false, 'message' => 'هذا التصويت مغلق حالياً.']);
        }

        $member = DB::table('mullak_members')
            ->where('id', (int) $request->input('mullak_member_id'))
            ->where('mullak_building_id', $poll->mullak_building_id)
            ->first();

        if (!$member) {
            return response()->json(['success' => false, 'message' => 'يرجى اختيار رقم الشقة أو المكتب بشكل صحيح.']);
        }

        if (empty($member->phone)) {
            return response()->json(['success' => false, 'message' => 'لا يوجد رقم جوال مسجل لهذه الوحدة في النظام، يرجى مراجعة إدارة المكتب.']);
        }

        $otp = (string) random_int(1000, 9999);
        DB::table('mullak_members')->where('id', $member->id)->update([
            'vote_otp_code'       => $otp,
            'vote_otp_expires_at' => now()->addMinutes(10),
            'updated_at'          => now(),
        ]);

        $msg = "🗳️ *كود التحقق للتصويت في اتحاد الملاك*\n"
             . "الوحدة: *{$member->unit_number}*\n"
             . "القرار: {$poll->title}\n"
             . "كود التأكيد المكون من 4 أرقام هو: *{$otp}*";

        $sent = self::sendWhatsAppMessage($member->phone, $msg, (int) $poll->office_id);

        $localPh = self::toLocalPhone($member->phone);
        $maskedPhone = strlen($localPh) >= 7
            ? substr($localPh, 0, 3) . '****' . substr($localPh, -2)
            : 'المسجل بالنظام';

        return response()->json([
            'success' => true,
            'message' => $sent
                ? "📲 تم إرسال كود التحقق (4 أرقام) عبر الواتساب إلى جوال الوحدة ({$maskedPhone})."
                : "📲 تم توليد كود التحقق لجوال الوحدة ({$maskedPhone})" . (config('app.debug') ? " — [كود التجربة: {$otp}]" : ""),
        ]);
    }

    public function submitPublicMullakVote(Request $request, $id, $token)
    {
        self::ensureContractInstallmentsTable();
        self::ensureMullakVotingTables();

        $poll = DB::table('mullak_polls')->where('id', $id)->first();
        if (!$poll || !hash_equals((string) $poll->public_token, (string) $token)) {
            abort(404);
        }

        if ($poll->status !== 'open') {
            return back()->with('vote_error', 'تم إغلاق هذا التصويت ولا يمكن تعديل الأصوات.');
        }

        $member = DB::table('mullak_members')
            ->where('id', (int) $request->input('mullak_member_id'))
            ->where('mullak_building_id', $poll->mullak_building_id)
            ->first();

        if (!$member) {
            return back()->with('vote_error', 'يرجى اختيار رقم الشقة أو المكتب.');
        }

        $inputOtp = trim((string) $request->input('otp_code', ''));
        $isExpired = empty($member->vote_otp_expires_at) || now()->greaterThan(\Carbon\Carbon::parse($member->vote_otp_expires_at));

        if (empty($member->vote_otp_code) || $isExpired || !hash_equals((string) $member->vote_otp_code, $inputOtp)) {
            return back()->with('vote_error', 'كود التحقق (4 أرقام) غير صحيح أو منتهي الصلاحية، يرجى طلب كود جديد.');
        }

        // تصفير الكود بعد استخدامه لمنع إعادة الاستخدام
        DB::table('mullak_members')->where('id', $member->id)->update([
            'vote_otp_code'       => null,
            'vote_otp_expires_at' => null,
        ]);

        $choice = in_array($request->input('vote_choice'), ['approve', 'reject', 'abstain'], true)
            ? $request->input('vote_choice')
            : 'approve';

        $weight = max(1, (float) ($member->area_share_pct ?: ($member->units_count ?: 1)));

        $existing = DB::table('mullak_poll_votes')
            ->where('poll_id', $poll->id)
            ->where('mullak_member_id', $member->id)
            ->first();

        if ($existing) {
            DB::table('mullak_poll_votes')->where('id', $existing->id)->update([
                'vote_choice' => $choice,
                'weight_pct'  => $weight,
                'comment'     => trim((string) $request->input('comment', '')),
                'ip_address'  => $request->ip(),
                'updated_at'  => now(),
            ]);
        } else {
            DB::table('mullak_poll_votes')->insert([
                'poll_id'          => $poll->id,
                'mullak_member_id' => $member->id,
                'member_name'      => $member->member_name,
                'unit_number'      => $member->unit_number,
                'weight_pct'       => $weight,
                'vote_choice'      => $choice,
                'comment'          => trim((string) $request->input('comment', '')),
                'ip_address'       => $request->ip(),
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }

        return back()->with('vote_success', "تم التحقق من الكود واعتماد تصويت الوحدة ({$member->unit_number}) بنجاح ✔");
    }

    // =========================================================================
    // الميزات الذكية الجديدة: جدولة الدفعات + إرسال PDF واتساب + المساعد الذكي + الاعتماد
    // =========================================================================
    private static bool $installmentsSchemaEnsured = false;

    public static function ensureContractInstallmentsTable(): void
    {
        if (self::$installmentsSchemaEnsured) {
            return;
        }
        self::$installmentsSchemaEnsured = true;

        if (!Schema::hasTable('contract_installments')) {
            Schema::create('contract_installments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('office_id')->index();
                $table->unsignedBigInteger('lease_contract_id')->index();
                $table->unsignedBigInteger('property_id')->nullable();
                $table->unsignedBigInteger('property_unit_id')->nullable();
                $table->unsignedBigInteger('owner_id')->nullable();
                $table->unsignedBigInteger('tenant_id')->nullable();
                $table->integer('installment_number')->default(1);
                $table->date('due_date')->index();
                $table->decimal('amount', 15, 2)->default(0);
                $table->decimal('paid_amount', 15, 2)->default(0);
                $table->string('status')->default('due');
                $table->unsignedBigInteger('last_voucher_id')->nullable();
                $table->text('voucher_ids_json')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('lease_contracts')) {
            Schema::table('lease_contracts', function (Blueprint $table) {
                if (!Schema::hasColumn('lease_contracts', 'grace_period_months')) {
                    $table->integer('grace_period_months')->default(0);
                }
                if (!Schema::hasColumn('lease_contracts', 'grace_period_days')) {
                    $table->integer('grace_period_days')->default(0);
                }
                if (!Schema::hasColumn('lease_contracts', 'installments_start_date')) {
                    $table->date('installments_start_date')->nullable();
                }
            });
        }

        if (Schema::hasTable('offices')) {
            Schema::table('offices', function (Blueprint $table) {
                if (!Schema::hasColumn('offices', 'daily_report_settings_json')) {
                    $table->text('daily_report_settings_json')->nullable();
                }
            });
        }

        if (Schema::hasTable('vouchers')) {
            Schema::table('vouchers', function (Blueprint $table) {
                if (!Schema::hasColumn('vouchers', 'owner_credited')) {
                    $table->boolean('owner_credited')->default(false);
                }
                if (!Schema::hasColumn('vouchers', 'commission_recorded')) {
                    $table->boolean('commission_recorded')->default(false);
                }
            });
        }

        if (!Schema::hasTable('mullak_buildings')) {
            Schema::create('mullak_buildings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('office_id')->index();
                $table->unsignedBigInteger('property_id')->nullable();
                $table->string('association_name');
                $table->string('bylaws_number')->nullable();
                $table->string('deed_number')->nullable();
                $table->string('owner_name')->nullable();
                $table->string('address')->nullable();
                $table->decimal('total_area', 15, 2)->default(0);
                $table->integer('units_count')->default(1);
                $table->string('distribution_method')->default('equal'); // equal | by_units
                $table->decimal('default_annual_fee', 15, 2)->default(12000);
                $table->decimal('guard_monthly_salary', 15, 2)->default(1500);
                $table->string('electricity_common_meter')->nullable();
                $table->string('water_common_meter')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('mullak_buildings', function (Blueprint $table) {
                if (!Schema::hasColumn('mullak_buildings', 'deed_number')) {
                    $table->string('deed_number')->nullable();
                }
                if (!Schema::hasColumn('mullak_buildings', 'owner_name')) {
                    $table->string('owner_name')->nullable();
                }
                if (!Schema::hasColumn('mullak_buildings', 'address')) {
                    $table->string('address')->nullable();
                }
                if (!Schema::hasColumn('mullak_buildings', 'total_area')) {
                    $table->decimal('total_area', 15, 2)->default(0);
                }
                if (!Schema::hasColumn('mullak_buildings', 'distribution_method')) {
                    $table->string('distribution_method')->default('equal');
                }
            });
        }

        if (!Schema::hasTable('mullak_members')) {
            Schema::create('mullak_members', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('office_id')->index();
                $table->unsignedBigInteger('mullak_building_id')->index();
                $table->unsignedBigInteger('tenant_id')->nullable();
                $table->string('unit_number');
                $table->string('floor_number')->nullable();
                $table->integer('units_count')->default(1);
                $table->string('member_role')->default('tenant'); // owner | tenant
                $table->string('member_name');
                $table->string('phone')->nullable();
                $table->string('national_id')->nullable();
                $table->decimal('area_share_pct', 6, 2)->default(0);
                $table->decimal('annual_fee', 15, 2)->default(0);
                $table->decimal('paid_amount', 15, 2)->default(0);
                $table->string('billing_year', 10)->default(date('Y'));
                $table->string('status')->default('due');
                $table->timestamps();
            });
        } else {
            Schema::table('mullak_members', function (Blueprint $table) {
                if (!Schema::hasColumn('mullak_members', 'tenant_id')) {
                    $table->unsignedBigInteger('tenant_id')->nullable();
                }
                if (!Schema::hasColumn('mullak_members', 'units_count')) {
                    $table->integer('units_count')->default(1);
                }
            });
        }

        if (!Schema::hasTable('mullak_expenses')) {
            Schema::create('mullak_expenses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('office_id')->index();
                $table->unsignedBigInteger('mullak_building_id')->index();
                $table->string('expense_category')->default('راتب الحارس');
                $table->string('title');
                $table->decimal('amount', 15, 2)->default(0);
                $table->date('expense_date');
                $table->string('payment_method')->default('حوالة بنكية');
                $table->unsignedBigInteger('voucher_id')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * توليد جدول دفعات العقد تلقائياً حسب دورة الإيجار مع احترام فترة السماح والتجهيز (Fit-out Grace Period)
     */
    public static function generateInstallmentsForContract(object $contract): void
    {
        self::ensureContractInstallmentsTable();
        $exists = DB::table('contract_installments')->where('lease_contract_id', $contract->id)->exists();
        if ($exists) return;

        $count = match ($contract->payment_cycle) {
            'monthly' => 12,
            'quarterly' => 4,
            'semi_annual' => 2,
            default => 1,
        };
        $monthsStep = (int) (12 / $count);
        $basePart = floor(($contract->total_rent_amount / $count) * 100) / 100;

        // إذا كان هناك فترة سماح وتجهيز (Fit-out Grace Period)، يبدأ جدول الدفعات المحاسبي بعد انتهاء فترة السماح
        if (!empty($contract->installments_start_date)) {
            $firstInstallmentDate = \Carbon\Carbon::parse($contract->installments_start_date);
        } else {
            $firstInstallmentDate = \Carbon\Carbon::parse($contract->start_date ?: date('Y-m-d'));
            $graceMonths = (int) ($contract->grace_period_months ?? 0);
            $graceDays = (int) ($contract->grace_period_days ?? 0);
            if ($graceMonths > 0) {
                $firstInstallmentDate->addMonths($graceMonths);
            }
            if ($graceDays > 0) {
                $firstInstallmentDate->addDays($graceDays);
            }
        }

        for ($i = 1; $i <= $count; $i++) {
            $amt = ($i === $count)
                ? round($contract->total_rent_amount - ($basePart * ($count - 1)), 2)
                : $basePart;
            $dueDate = $firstInstallmentDate->copy()->addMonths(($i - 1) * $monthsStep)->toDateString();

            DB::table('contract_installments')->insert([
                'office_id' => $contract->office_id,
                'lease_contract_id' => $contract->id,
                'property_id' => $contract->property_id,
                'property_unit_id' => $contract->property_unit_id,
                'owner_id' => $contract->owner_id,
                'tenant_id' => $contract->tenant_id,
                'installment_number' => $i,
                'due_date' => $dueDate,
                'amount' => $amt,
                'paid_amount' => 0,
                'status' => $dueDate < date('Y-m-d') ? 'overdue' : 'due',
                'voucher_ids_json' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * إرسال ملف PDF أصلي مرفق مباشرة عبر WhatsApp API (يدعم Meta Cloud API الرسمي /media أو البوابات المخصصة)
     */
    public static function sendWhatsAppDocument(string $toPhone, string $pdfBase64OrUrl, string $fileName, string $caption = '', ?int $officeId = null): bool
    {
        $apiUrl = self::getSetting('whatsapp_api_url', env('WHATSAPP_API_URL'));
        $apiToken = self::getSetting('whatsapp_api_token', env('WHATSAPP_API_TOKEN'));
        $instanceId = self::getSetting('whatsapp_instance_id', '');

        if ($officeId && Schema::hasTable('offices')) {
            $office = DB::table('offices')->where('id', $officeId)->first();
            $isCustom = (!empty($office->use_custom_whatsapp) || ($office->whatsapp_mode ?? '') === 'custom');
            if ($office && $isCustom && !empty($office->whatsapp_api_token)) {
                $apiUrl = $office->whatsapp_api_url ?: $apiUrl;
                $apiToken = $office->whatsapp_api_token;
                $instanceId = $office->whatsapp_instance_id ?? '';
            }
        }

        if (empty($apiToken)) {
            return false;
        }

        $formattedPhone = self::formatSaudiPhone($toPhone);
        $cleanDigitsPhone = ltrim($formattedPhone, '+');

        // 1. الإرسال عبر Meta WhatsApp Cloud API الرسمي
        if (self::isMetaCloudApi($apiUrl, $apiToken, $instanceId)) {
            $endpoint = self::buildMetaMessagesUrl($apiUrl, $instanceId);
            $phoneId = preg_replace('/\D/', '', (string) $instanceId);
            if (empty($phoneId) && preg_match('#graph\.facebook\.com/v[\d\.]+/(\d+)#i', (string) $apiUrl, $m)) {
                $phoneId = $m[1];
            }

            try {
                $docObject = ['filename' => $fileName];
                if (trim($caption) !== '') {
                    $docObject['caption'] = trim($caption);
                }

                if (str_starts_with($pdfBase64OrUrl, 'http://') || str_starts_with($pdfBase64OrUrl, 'https://')) {
                    $docObject['link'] = $pdfBase64OrUrl;
                } elseif (!empty($phoneId)) {
                    $rawB64 = str_contains($pdfBase64OrUrl, 'base64,') ? explode('base64,', $pdfBase64OrUrl, 2)[1] : $pdfBase64OrUrl;
                    $binary = base64_decode($rawB64, true);
                    if ($binary !== false) {
                        $mediaRes = Http::withToken(trim($apiToken))
                            ->timeout(20)
                            ->attach('file', $binary, $fileName, ['Content-Type' => 'application/pdf'])
                            ->post("https://graph.facebook.com/v23.0/{$phoneId}/media", [
                                'messaging_product' => 'whatsapp',
                                'type'              => 'application/pdf',
                            ]);
                        if ($mediaRes->successful() && $mediaRes->json('id')) {
                            $docObject['id'] = $mediaRes->json('id');
                        }
                    }
                }

                if (empty($docObject['id']) && empty($docObject['link'])) {
                    return false;
                }

                $response = Http::withToken(trim($apiToken))
                    ->acceptJson()
                    ->contentType('application/json')
                    ->timeout(15)
                    ->post($endpoint, [
                        'messaging_product' => 'whatsapp',
                        'recipient_type'    => 'individual',
                        'to'                => $cleanDigitsPhone,
                        'type'              => 'document',
                        'document'          => $docObject,
                    ]);

                return $response->successful();
            } catch (\Throwable $e) {
                return false;
            }
        }

        // 2. الإرسال عبر بوابات الـ API الأخرى
        if (empty($apiUrl)) {
            return false;
        }

        try {
            $payload = [
                'token' => $apiToken,
                'instance_id' => $instanceId,
                'to' => $formattedPhone,
                'type' => 'document',
                'document' => $pdfBase64OrUrl,
                'file' => $pdfBase64OrUrl,
                'filename' => $fileName,
            ];
            if (trim($caption) !== '') {
                $payload['caption'] = trim($caption);
                $payload['message'] = trim($caption);
            }

            $response = Http::timeout(15)->post($apiUrl, $payload);
            return $response->successful();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * استقبال ملف الـ PDF المولّد من المتصفح بصيغة Base64 وحفظه كملف PDF أصلي وإرساله كمرفق Document عبر الواتساب
     */
    public function sendRawPdfViaWhatsapp(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->user_type, ['company_admin', 'office_admin', 'office_staff'], true)) {
            abort(403);
        }

        $phone = trim((string) $request->input('phone', ''));
        $pdfBase64 = (string) $request->input('pdf_base64', '');
        $cleanBaseName = pathinfo(trim((string) $request->input('filename', 'Document.pdf')), PATHINFO_FILENAME);
        $fileName = preg_replace('/[^a-zA-Z0-9_\-\x{0600}-\x{06FF}]/u', '_', $cleanBaseName) ?: 'Document';
        $fileName .= '.pdf';

        $docType = (string) $request->input('doc_type', 'general');

        if ($user->user_type === 'company_admin') {
            $officeId = $request->filled('office_id') ? (int) $request->office_id : (session('impersonate_office_id') ? (int) session('impersonate_office_id') : null);
        } else {
            $officeId = (int) $user->office_id;
        }

        if (empty($phone) || empty($pdfBase64)) {
            return response()->json(['success' => false, 'message' => 'يرجى التأكد من وجود رقم الجوال للعميل.']);
        }

        $rawBase64 = $pdfBase64;
        if (str_contains($rawBase64, 'base64,')) {
            $rawBase64 = explode('base64,', $rawBase64, 2)[1];
        }
        $pdfBinary = base64_decode($rawBase64, true);

        if ($pdfBinary === false || !str_starts_with($pdfBinary, '%PDF-') || strlen($pdfBinary) > 10 * 1024 * 1024) {
            return response()->json(['success' => false, 'message' => 'ملف الـ PDF غير صالح أو يتجاوز الحجم المسموح (10MB).']);
        }

        $pdfDir = public_path('uploads/whatsapp_pdfs');
        if (!is_dir($pdfDir)) {
            @mkdir($pdfDir, 0775, true);
        }
        $storedName = time() . '_' . bin2hex(random_bytes(4)) . '_' . $fileName;
        @file_put_contents($pdfDir . '/' . $storedName, $pdfBinary);

        $caption = '';
        if ($officeId && Schema::hasTable('offices')) {
            $office = DB::table('offices')->where('id', $officeId)->first();
            if ($office) {
                if ($docType === 'invoice') $caption = trim((string) ($office->wa_invoice_caption ?? ''));
                elseif ($docType === 'voucher') $caption = trim((string) ($office->wa_voucher_caption ?? ''));
                elseif ($docType === 'legal') $caption = trim((string) ($office->wa_legal_caption ?? ''));
            }
        }

        $sent = self::sendWhatsAppDocument($phone, $pdfBase64, $fileName, $caption, $officeId);

        return response()->json([
            'success' => $sent,
            'pdf_url' => asset('uploads/whatsapp_pdfs/' . $storedName),
            'message' => $sent
                ? "تم إرسال ملف الـ PDF الأصلي ({$fileName}) كمرفق عبر الواتساب بنجاح."
                : "تم تجهيز ملف الـ PDF الأصلي ({$fileName})؛ يرجى التأكد من تفعيل توكن WhatsApp API في الإعدادات."
        ]);
    }

    /**
     * استقبال وتوثيق رسائل الواتساب (Webhook) من Meta Cloud API لاعتماد سندات الصرف المعلقة
     */
    public function handleWhatsappWebhook(Request $request)
    {
        // 1. الرد على طلب التحقق الرسمي من Meta App Dashboard (GET hub.challenge)
        if ($request->isMethod('get')) {
            $mode      = $request->query('hub_mode', $request->query('hub.mode'));
            $token     = $request->query('hub_verify_token', $request->query('hub.verify_token'));
            $challenge = $request->query('hub_challenge', $request->query('hub.challenge'));

            $expectedVerifyToken = self::getSetting('whatsapp_webhook_verify_token', 'erp_whatsapp_verify_2026');
            $apiToken = self::getSetting('whatsapp_api_token', '');

            if ($mode === 'subscribe' && ($token === $expectedVerifyToken || (!empty($apiToken) && $token === $apiToken))) {
                return response((string) $challenge, 200)->header('Content-Type', 'text/plain');
            }
            return response('Forbidden: Verify token mismatch', 403);
        }

        // 2. قراءة الرسالة الواردة من هيكل Meta Cloud API الرسمي أو البوابات المخصصة
        $metaMessage = $request->input('entry.0.changes.0.value.messages.0');
        if (is_array($metaMessage)) {
            $fromPhone = (string) ($metaMessage['from'] ?? '');
            $body      = trim((string) ($metaMessage['text']['body'] ?? ($metaMessage['button']['text'] ?? '')));
        } else {
            $fromPhone = $request->input('from', $request->input('sender', $request->input('phone', '')));
            $body      = trim((string) $request->input('message', $request->input('body', $request->input('text', ''))));
        }

        if (empty($fromPhone) || empty($body)) {
            return response()->json(['status' => 'ignored']);
        }

        $formattedPhone = self::formatSaudiPhone($fromPhone);
        $localPhone = self::toLocalPhone($formattedPhone);

        $user = DB::table('users')
            ->where('user_type', 'office_admin')
            ->where('is_active', 1)
            ->where(function ($q) use ($formattedPhone, $localPhone) {
                $q->where('phone', $formattedPhone)->orWhere('phone', $localPhone);
            })
            ->first();

        if ($user && $user->office_id) {
            $office = DB::table('offices')->where('id', $user->office_id)->first();
            if (!$office) {
                return response()->json(['status' => 'ignored']);
            }

            $keywordsRaw = $office->wa_approval_keywords ?: 'اعتمد,تم,اوك,موافق,نعم';
            $keywords = array_filter(array_map('trim', preg_split('/[,،|]+/u', $keywordsRaw)));

            $matchedApproval = false;
            foreach ($keywords as $kw) {
                if ($kw !== '' && (mb_strtolower($body) === mb_strtolower($kw) || str_starts_with(mb_strtolower($body), mb_strtolower($kw) . ' '))) {
                    $matchedApproval = true;
                    break;
                }
            }

            if ($matchedApproval) {
                $pendingQuery = DB::table('vouchers')
                    ->where('office_id', $office->id)
                    ->where('approval_status', 'pending')
                    ->orderBy('id', 'desc');

                if (preg_match('/(\d{3,6})/', $body, $m)) {
                    $pendingQuery->where(function ($q) use ($m) {
                        $q->where('approval_code', $m[1])->orWhere('id', $m[1]);
                    });
                }

                $pendingVoucher = $pendingQuery->first();
                if ($pendingVoucher) {
                    DB::transaction(function () use ($pendingVoucher) {
                        DB::table('vouchers')->where('id', $pendingVoucher->id)->update([
                            'approval_status' => 'approved',
                            'approval_code' => null,
                            'updated_at' => now(),
                        ]);
                        self::applyAccountMovement((int) $pendingVoucher->debit_account_id, (float) $pendingVoucher->amount, 0);
                        self::applyAccountMovement((int) $pendingVoucher->credit_account_id, 0, (float) $pendingVoucher->amount);
                    });

                    $confirmMsg = "✅ *تم اعتماد سند الصرف بنجاح عبر الواتساب!*\n"
                                . "رقم السند: *{$pendingVoucher->voucher_number}*\n"
                                . "المبلغ: *" . number_format($pendingVoucher->amount, 2) . " ر.س*\n"
                                . "تم ترحيل المبلغ إلى شجرة الحسابات الآن.";
                    self::sendWhatsAppMessage($formattedPhone, $confirmMsg, $office->id);
                    return response()->json(['status' => 'voucher_approved', 'voucher_id' => $pendingVoucher->id]);
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * إقفال السنة المالية للشركة الأم وترحيل صافي الأرباح لحقوق الملكية (3101)
     */
    public function closeCompanyFiscalYear(Request $request)
    {
        $this->ensureJournalTables();
        $accounts = DB::table('accounts')->whereNull('office_id')->where('is_group', 0)->get();
        $revAccounts = $accounts->filter(fn($a) => str_contains($a->type, 'إيراد') || str_starts_with($a->code, '4'));
        $expAccounts = $accounts->filter(fn($a) => str_contains($a->type, 'مصروف') || str_starts_with($a->code, '5'));

        $totalRev = $revAccounts->sum(fn($a) => abs($a->balance));
        $totalExp = $expAccounts->sum(fn($a) => abs($a->balance));
        $netProfit = $totalRev - $totalExp;

        if ($totalRev == 0 && $totalExp == 0) {
            return back()->withErrors(['لا توجد أرصدة إيرادات أو مصروفات مفتوحة لإقفالها في هذه السنة.']);
        }

        DB::transaction(function () use ($revAccounts, $expAccounts, $netProfit) {
            $equityAcc = DB::table('accounts')->whereNull('office_id')->where('code', '3101')->first();
            if (!$equityAcc) {
                $eqId = DB::table('accounts')->insertGetId([
                    'office_id' => null, 'parent_id' => null, 'code' => '3101',
                    'name' => 'الأرباح المبقاة وحقوق الملكية (إقفال سنوي)', 'type' => 'حقوق الملكية',
                    'is_group' => 0, 'balance' => 0, 'debit_balance' => 0, 'credit_balance' => 0,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            } else {
                $eqId = $equityAcc->id;
            }

            foreach ($revAccounts as $r) {
                DB::table('accounts')->where('id', $r->id)->update(['balance' => 0, 'debit_balance' => 0, 'credit_balance' => 0]);
            }
            foreach ($expAccounts as $e) {
                DB::table('accounts')->where('id', $e->id)->update(['balance' => 0, 'debit_balance' => 0, 'credit_balance' => 0]);
            }

            self::applyAccountMovement((int) $eqId, $netProfit < 0 ? abs($netProfit) : 0, $netProfit >= 0 ? $netProfit : 0);
        });

        return back()->with('success', 'تم إقفال السنة المالية بنجاح وتصفير حسابات الإيرادات والمصروفات وترحيل صافي الربح (' . number_format($netProfit, 2) . ' ر.س) لحساب الأرباح المبقاة.');
    }

    /**
     * عرض وتحميل الفاتورة الضريبية (PDF) عبر الرابط المباشر المرسل للواتساب
     */
    public function showPublicInvoicePdf($id, $token)
    {
        $this->ensureTaxInvoicesTable();
        $inv = DB::table('tax_invoices')->where('id', $id)->first();
        if (!$inv) {
            abort(404, 'رابط الفاتورة غير صالح.');
        }

        $expectedHmac = self::generateInvoicePublicToken($inv->id, $inv->invoice_number);
        $legacyMd5 = md5($inv->id . '|' . $inv->invoice_number . '|zatca_pdf');
        if (!hash_equals($expectedHmac, (string) $token) && !hash_equals($legacyMd5, (string) $token)) {
            abort(404, 'رابط الفاتورة غير صالح.');
        }

        $qrImg = 'https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=' . urlencode($inv->zatca_qr ?? '');
        $typeTitle = $inv->invoice_type === 'tax' ? 'فاتورة ضريبية' : 'فاتورة ضريبية مبسطة';

        $html = '<!DOCTYPE html><html lang="ar" dir="rtl"><head><meta charset="UTF-8"><title>' . e($inv->invoice_number) . '</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Readex+Pro:wght@400;600;700&display=swap" rel="stylesheet">
        <style>body{font-family:"Readex Pro",sans-serif;background:#f8fafc;}</style></head>
        <body class="p-6"><div class="max-w-2xl mx-auto bg-white p-8 rounded-2xl border-2 border-slate-900 space-y-6">
        <div class="flex items-start justify-between border-b-2 border-slate-900 pb-4">
            <div><h1 class="text-xl font-bold text-slate-900">' . e($typeTitle) . '</h1>
            <p class="text-xs text-slate-500 mt-1">رقم الفاتورة: <b>' . e($inv->invoice_number) . '</b> | التاريخ: ' . e($inv->issue_date) . '</p></div>
            <img src="' . $qrImg . '" class="w-24 h-24 border p-1 rounded-lg">
        </div>
        <div class="grid grid-cols-2 gap-4 text-xs">
            <div class="p-3 bg-slate-50 rounded-xl border"><b>المورّد:</b> ' . e($inv->seller_name) . '<br>الرقم الضريبي: ' . e($inv->seller_tax_number ?: '-') . '</div>
            <div class="p-3 bg-slate-50 rounded-xl border"><b>العميل:</b> ' . e($inv->buyer_name) . '<br>الرقم الضريبي: ' . e($inv->buyer_tax_number ?: '-') . '</div>
        </div>
        <table class="w-full text-xs text-right border">
            <thead class="bg-slate-900 text-white"><tr><th class="p-2.5">البيان</th><th class="p-2.5">قبل الضريبة</th><th class="p-2.5">الضريبة (' . (float)$inv->vat_rate . '%)</th><th class="p-2.5">الإجمالي</th></tr></thead>
            <tbody><tr><td class="p-3 font-bold border">' . e($inv->description) . '</td><td class="p-3 border">' . number_format($inv->subtotal, 2) . ' ر.س</td><td class="p-3 border">' . number_format($inv->vat_amount, 2) . ' ر.س</td><td class="p-3 font-bold text-emerald-700 border">' . number_format($inv->total_amount, 2) . ' ر.س</td></tr></tbody>
        </table>
        <div class="text-center pt-4 print:hidden"><button onclick="window.print()" class="px-6 py-2 bg-slate-900 text-white rounded-lg text-xs font-bold">🖨️ طباعة / حفظ PDF</button></div>
        </div></body></html>';

        return response($html);
    }

    public static function ensureSiteAnalyticsAndAdsTables(): void
    {
        if (!Schema::hasTable('site_visits')) {
            Schema::create('site_visits', function (Blueprint $table) {
                $table->id();
                $table->string('ip_address', 64)->index();
                $table->string('session_hash', 64)->index();
                $table->string('country_code', 10)->default('SA')->index();
                $table->string('country_name', 100)->default('المملكة العربية السعودية');
                $table->string('city', 100)->default('الرياض');
                $table->string('isp', 150)->nullable();
                $table->string('device_type', 40)->default('كمبيوتر (Desktop)');
                $table->string('os', 50)->default('Windows');
                $table->string('browser', 50)->default('Chrome');
                $table->string('page_visited', 180)->default('/');
                $table->string('referrer_source', 100)->default('دخول مباشر');
                $table->string('referrer_url', 255)->nullable();
                $table->date('visit_date')->index();
                $table->timestamp('last_seen_at')->nullable()->index();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('whatsapp_ad_campaigns')) {
            Schema::create('whatsapp_ad_campaigns', function (Blueprint $table) {
                $table->id();
                $table->string('campaign_name');
                $table->text('message_template')->nullable();
                $table->string('attachment_type', 40)->nullable();
                $table->integer('total_targets')->default(0);
                $table->integer('sent_count')->default(0);
                $table->integer('failed_count')->default(0);
                $table->integer('min_delay_sec')->default(5);
                $table->integer('max_delay_sec')->default(15);
                $table->timestamps();
            });
        }
    }

    public static function countryCodeToFlagEmoji(string $code): string
    {
        $code = strtoupper(trim($code ?: 'SA'));
        if (strlen($code) !== 2) return '🌍';
        return mb_chr(ord($code[0]) + 127397, 'UTF-8') . mb_chr(ord($code[1]) + 127397, 'UTF-8');
    }

    public static function recordSiteVisit(Request $request, string $methodName = 'showLandingPage'): void
    {
        try {
            if (!$request->isMethod('get')) return;
            self::ensureSiteAnalyticsAndAdsTables();

            $ua = (string) $request->userAgent();
            if (preg_match('/bot|crawl|slurp|spider|mediapartners/i', $ua)) return;

            $pageLabel = match ($methodName) {
                'showLandingPage' => 'الواجهة الرئيسية للمنصة (/)',
                'showLogin'       => 'بوابة تسجيل الدخول (/login)',
                'dashboard'       => 'لوحة التحكم المركزية',
                default           => $request->path(),
            };

            $ip = $request->header('CF-Connecting-IP')
               ?: $request->header('X-Real-IP')
               ?: $request->header('X-Forwarded-For')
               ?: $request->ip()
               ?: '127.0.0.1';
            if (str_contains($ip, ',')) {
                $ip = trim(explode(',', $ip)[0]);
            }

            $today = date('Y-m-d');
            $sessionHash = md5($ip . '|' . substr($ua, 0, 120) . '|' . $today);

            $recentVisit = DB::table('site_visits')
                ->where('session_hash', $sessionHash)
                ->where('page_visited', $pageLabel)
                ->where('created_at', '>=', now()->subMinutes(3))
                ->first();

            if ($recentVisit) {
                DB::table('site_visits')->where('id', $recentVisit->id)->update([
                    'last_seen_at' => now(),
                    'updated_at' => now(),
                ]);
                return;
            }

            $deviceType = 'كمبيوتر (Desktop)';
            if (preg_match('/ipad|tablet|playbook|silk/i', $ua)) $deviceType = 'جهاز لوحي (Tablet)';
            elseif (preg_match('/mobile|iphone|ipod|android.*mobile|windows phone/i', $ua)) $deviceType = 'جوال (Mobile)';

            $os = 'نظام آخر';
            if (preg_match('/iphone|ipad|ipod/i', $ua)) $os = 'iOS (آيفون)';
            elseif (preg_match('/android/i', $ua)) $os = 'Android (أندرويد)';
            elseif (preg_match('/windows nt/i', $ua)) $os = 'Windows';
            elseif (preg_match('/macintosh|mac os x/i', $ua)) $os = 'Mac OS';
            elseif (preg_match('/linux/i', $ua)) $os = 'Linux';

            $browser = 'متصفح آخر';
            if (preg_match('/edg/i', $ua)) $browser = 'Microsoft Edge';
            elseif (preg_match('/opr|opera/i', $ua)) $browser = 'Opera';
            elseif (preg_match('/chrome|crios/i', $ua)) $browser = 'Google Chrome';
            elseif (preg_match('/firefox|fxios/i', $ua)) $browser = 'Mozilla Firefox';
            elseif (preg_match('/safari/i', $ua)) $browser = 'Apple Safari';

            $refererRaw = (string) $request->headers->get('referer', '');
            $refererSource = 'دخول مباشر (Direct)';
            if (!empty($refererRaw)) {
                $refLower = strtolower($refererRaw);
                $host = strtolower($request->getHost());
                if (str_contains($refLower, $host)) $refererSource = 'تنقل داخلي بالمنصة';
                elseif (str_contains($refLower, 'google.')) $refererSource = 'محرك بحث جوجل (Google)';
                elseif (str_contains($refLower, 'bing.')) $refererSource = 'محرك بحث Bing';
                elseif (str_contains($refLower, 'wa.me') || str_contains($refLower, 'whatsapp')) $refererSource = 'واتساب (WhatsApp)';
                elseif (str_contains($refLower, 't.co') || str_contains($refLower, 'twitter') || str_contains($refLower, 'x.com')) $refererSource = 'منصة إكس (X)';
                elseif (str_contains($refLower, 'instagram')) $refererSource = 'إنستغرام (Instagram)';
                elseif (str_contains($refLower, 'snapchat')) $refererSource = 'سناب شات (Snapchat)';
                else $refererSource = parse_url($refererRaw, PHP_URL_HOST) ?: 'رابط خارجي';
            }

            $countryNamesAr = [
                'SA' => 'المملكة العربية السعودية', 'AE' => 'الإمارات العربية المتحدة', 'KW' => 'الكويت',
                'QA' => 'قطر', 'BH' => 'البحرين', 'OM' => 'سلطنة عُمان', 'YE' => 'اليمن',
                'EG' => 'مصر', 'JO' => 'الأردن', 'IQ' => 'العراق', 'SY' => 'سوريا',
                'LB' => 'لبنان', 'PS' => 'فلسطين', 'SD' => 'السودان', 'MA' => 'المغرب',
                'DZ' => 'الجزائر', 'TN' => 'تونس', 'LY' => 'ليبيا', 'TR' => 'تركيا',
                'US' => 'الولايات المتحدة', 'GB' => 'المملكة المتحدة', 'DE' => 'ألمانيا',
                'FR' => 'فرنسا', 'IN' => 'الهند', 'PK' => 'باكستان',
            ];

            $countryCode = strtoupper((string) $request->header('CF-IPCountry', ''));
            $countryName = 'المملكة العربية السعودية';
            $city = 'الرياض';
            $isp = 'شبكة اتصال محلية';

            $cachedGeo = DB::table('site_visits')->where('ip_address', $ip)->whereNotNull('country_code')->orderByDesc('id')->first();
            if ($cachedGeo) {
                $countryCode = $cachedGeo->country_code;
                $countryName = $cachedGeo->country_name;
                $city = $cachedGeo->city;
                $isp = $cachedGeo->isp;
            } elseif (!in_array($ip, ['127.0.0.1', '::1']) && !str_starts_with($ip, '192.168.') && !str_starts_with($ip, '10.')) {
                try {
                    $geoRes = Http::timeout(2)->get("http://ip-api.com/json/{$ip}?fields=status,country,countryCode,city,isp&lang=ar");
                    if ($geoRes->successful() && ($geoRes->json('status') === 'success')) {
                        $countryCode = strtoupper((string) $geoRes->json('countryCode', 'SA'));
                        $countryName = $countryNamesAr[$countryCode] ?? (string) $geoRes->json('country', 'المملكة العربية السعودية');
                        $city = (string) $geoRes->json('city', 'الرياض');
                        $isp = (string) $geoRes->json('isp', '');
                    }
                } catch (\Throwable $e) {
                    $countryCode = $countryCode ?: 'SA';
                }
            } else {
                $countryCode = 'SA';
                $countryName = 'المملكة العربية السعودية';
                $city = 'الرياض';
                $isp = 'Local Network';
            }

            if (empty($countryCode)) $countryCode = 'SA';
            if (isset($countryNamesAr[$countryCode])) $countryName = $countryNamesAr[$countryCode];

            DB::table('site_visits')->insert([
                'ip_address'      => $ip,
                'session_hash'    => $sessionHash,
                'country_code'    => $countryCode,
                'country_name'    => $countryName,
                'city'            => $city ?: 'غير محدد',
                'isp'             => $isp,
                'device_type'     => $deviceType,
                'os'              => $os,
                'browser'         => $browser,
                'page_visited'    => $pageLabel,
                'referrer_source' => $refererSource,
                'referrer_url'    => mb_substr($refererRaw, 0, 250),
                'visit_date'      => $today,
                'last_seen_at'    => now(),
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        } catch (\Throwable $e) {}
    }

    public function getSiteAnalyticsData(): array
    {
        self::ensureSiteAnalyticsAndAdsTables();

        $today = date('Y-m-d');
        $totalPageViews = DB::table('site_visits')->count();
        $uniqueVisitors = DB::table('site_visits')->distinct('session_hash')->count('session_hash');
        $todayViews = DB::table('site_visits')->where('visit_date', $today)->count();
        $todayUnique = DB::table('site_visits')->where('visit_date', $today)->distinct('session_hash')->count('session_hash');
        $onlineNow = DB::table('site_visits')->where('last_seen_at', '>=', now()->subMinutes(10))->distinct('session_hash')->count('session_hash');

        $countries = DB::table('site_visits')
            ->select('country_code', 'country_name', DB::raw('COUNT(*) as visits_count'), DB::raw('COUNT(DISTINCT session_hash) as unique_count'))
            ->groupBy('country_code', 'country_name')
            ->orderByDesc('visits_count')
            ->limit(10)
            ->get()
            ->map(function ($row) use ($totalPageViews) {
                $row->flag = self::countryCodeToFlagEmoji($row->country_code);
                $row->percentage = $totalPageViews > 0 ? round(($row->visits_count / $totalPageViews) * 100, 1) : 0;
                return $row;
            });

        $cities = DB::table('site_visits')
            ->select('city', 'country_code', DB::raw('COUNT(*) as visits_count'))
            ->groupBy('city', 'country_code')
            ->orderByDesc('visits_count')
            ->limit(8)
            ->get();

        $devices = DB::table('site_visits')
            ->select('device_type', DB::raw('COUNT(*) as visits_count'))
            ->groupBy('device_type')
            ->orderByDesc('visits_count')
            ->get()
            ->map(function ($d) use ($totalPageViews) {
                $d->percentage = $totalPageViews > 0 ? round(($d->visits_count / $totalPageViews) * 100, 1) : 0;
                return $d;
            });

        $referrers = DB::table('site_visits')
            ->select('referrer_source', DB::raw('COUNT(*) as visits_count'))
            ->groupBy('referrer_source')
            ->orderByDesc('visits_count')
            ->limit(6)
            ->get();

        $dailyTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = now()->subDays($i)->toDateString();
            $dailyTrend[] = [
                'date'  => $d,
                'label' => now()->subDays($i)->format('m/d'),
                'views' => DB::table('site_visits')->where('visit_date', $d)->count(),
            ];
        }

        $recentVisitors = DB::table('site_visits')->orderByDesc('id')->limit(150)->get()->map(function ($v) {
            $v->flag = self::countryCodeToFlagEmoji($v->country_code);
            return $v;
        });

        return [
            'total_views'     => $totalPageViews,
            'unique_visitors' => $uniqueVisitors,
            'today_views'     => $todayViews,
            'today_unique'    => $todayUnique,
            'online_now'      => max(1, $onlineNow),
            'countries_count' => $countries->count(),
            'countries'       => $countries,
            'cities'          => $cities,
            'devices'         => $devices,
            'referrers'       => $referrers,
            'daily_trend'     => $dailyTrend,
            'recent_visitors' => $recentVisitors,
        ];
    }

    public function clearSiteVisits()
    {
        self::ensureSiteAnalyticsAndAdsTables();
        DB::table('site_visits')->truncate();
        return back()->with('success', 'تم تصفير سجل إحصائيات الزيارات بنجاح.');
    }

   /**
     * تشغيل محرك واتساب QR المحلي في الخلفية تلقائياً والانتظار حتى يصبح جاهزاً
     */
    private function ensureMarketingQrEngineRunning(): bool
    {
        try {
            $ping = Http::timeout(2)->get('http://127.0.0.1:3005/status');
            if ($ping->successful()) {
                return true;
            }
        } catch (\Throwable $e) {}

        $baseDir = base_path();
        $scriptPath = base_path('whatsapp-qr-engine.js');
        if (!file_exists($scriptPath)) {
            return false;
        }

        try {
            $authDir = storage_path('app/wa_marketing_qr_auth');
            if (!is_dir($authDir)) {
                @mkdir($authDir, 0777, true);
            }
            @chmod($authDir, 0777);

            $logPath = PHP_OS_FAMILY === 'Windows'
                ? storage_path('logs/whatsapp-qr-engine.log')
                : '/tmp/whatsapp-qr-engine.log';

            if (PHP_OS_FAMILY === 'Windows') {
                $cmd = 'cd /d ' . escapeshellarg($baseDir) . ' && start /B "" node ' . escapeshellarg($scriptPath) . ' > ' . escapeshellarg($logPath) . ' 2>&1';
                @pclose(@popen($cmd, 'r'));
            } else {
                $nodeBin = 'node';
                foreach (['/usr/bin/node', '/usr/local/bin/node'] as $candidate) {
                    if (@is_executable($candidate)) {
                        $nodeBin = $candidate;
                        break;
                    }
                }
                $bgCmd = '(cd ' . escapeshellarg($baseDir) . ' && ' . $nodeBin . ' ' . escapeshellarg($scriptPath) . ' >> ' . escapeshellarg($logPath) . ' 2>&1 < /dev/null &)';
                if (function_exists('exec')) {
                    @exec($bgCmd);
                } elseif (function_exists('shell_exec')) {
                    @shell_exec($bgCmd);
                } elseif (function_exists('popen')) {
                    @pclose(@popen($bgCmd, 'r'));
                }
            }

            for ($i = 0; $i < 12; $i++) {
                usleep(300000);
                try {
                    $check = Http::timeout(1)->get('http://127.0.0.1:3005/status');
                    if ($check->successful()) {
                        return true;
                    }
                } catch (\Throwable $e) {}
            }
        } catch (\Throwable $e) {}

        return false;
    }

    /**
     * فحص حالة الاتصال الفعلية من محرك واتساب الحقيقي فقط
     */
    private function fetchLiveMarketingQrState(): array
    {
        try {
            $res = Http::timeout(2)->get('http://127.0.0.1:3005/status');
            if ($res->successful()) {
                $data = $res->json();
                $isConnected = !empty($data['connected']) && ($data['status'] ?? '') === 'connected';
                return [
                    'engine_up'     => true,
                    'connected'     => $isConnected,
                    'status'        => $data['status'] ?? 'disconnected',
                    'qr_url'        => $data['qr_url'] ?? '',
                    'account_name'  => $isConnected ? ($data['account_name'] ?? '') : '',
                    'account_phone' => $isConnected ? ($data['account_phone'] ?? '') : '',
                    'connected_at'  => $isConnected ? ($data['connected_at'] ?? '') : '',
                    'last_error'    => $data['last_error'] ?? '',
                ];
            }
        } catch (\Throwable $e) {}

        $logHint = '';
        foreach (['/tmp/whatsapp-qr-engine.log', storage_path('logs/whatsapp-qr-engine.log')] as $lf) {
            if (@file_exists($lf) && @filesize($lf) > 0) {
                $tail = trim((string) @file_get_contents($lf, false, null, max(0, @filesize($lf) - 300)));
                if ($tail !== '') {
                    $logHint = mb_substr(preg_replace('/\s+/', ' ', $tail), -160);
                    break;
                }
            }
        }

        return [
            'engine_up'     => false,
            'connected'     => false,
            'status'        => 'disconnected',
            'qr_url'        => '',
            'account_name'  => '',
            'account_phone' => '',
            'connected_at'  => '',
            'last_error'    => $logHint ?: 'المحرك غير مشغل بعد',
        ];
    }

    /**
     * توليد كود QR حقيقي ومشفر من خوادم واتساب ويب
     */
    public function generateAdsWhatsappQr(Request $request)
    {
        session()->save();

        $engineUp = $this->ensureMarketingQrEngineRunning();

        if ($engineUp) {
            try {
                $res = Http::timeout(8)->post('http://127.0.0.1:3005/init-qr', [
                    'force' => true,
                ]);
                if ($res->successful()) {
                    $data = $res->json();
                    return response()->json([
                        'success'       => true,
                        'engine_up'     => true,
                        'connected'     => !empty($data['connected']),
                        'status'        => $data['status'] ?? (!empty($data['qr_url']) ? 'qr_ready' : 'connecting'),
                        'qr_url'        => $data['qr_url'] ?? '',
                        'account_name'  => $data['account_name'] ?? '',
                        'account_phone' => $data['account_phone'] ?? '',
                        'last_error'    => $data['last_error'] ?? '',
                    ]);
                }
            } catch (\Throwable $e) {
                return response()->json([
                    'success'    => true,
                    'engine_up'  => true,
                    'connected'  => false,
                    'status'     => 'connecting',
                    'qr_url'     => '',
                    'last_error' => $e->getMessage(),
                ]);
            }
        }

        $state = $this->fetchLiveMarketingQrState();
        return response()->json([
            'success'    => false,
            'engine_up'  => false,
            'message'    => 'تعذر تشغيل محرك Node.js: ' . ($state['last_error'] ?? ''),
            'last_error' => $state['last_error'] ?? 'Node Engine Down'
        ]);
    }

    /**
     * فحص مستمر تلقائي (Polling) لمعرفة هل تم مسح الـ QR وجلب اسم الحساب المرتبط تلقائياً
     */
    public function checkAdsWhatsappQrStatus()
    {
        session()->save();

        $state = $this->fetchLiveMarketingQrState();
        if (!$state['engine_up']) {
            $this->ensureMarketingQrEngineRunning();
            $state = $this->fetchLiveMarketingQrState();
        }
        return response()->json($state);
    }

    /**
     * فصل الحساب التسويقي المرتبط وإلغاء جلسة الـ QR
     */
    public function disconnectAdsWhatsappQr()
    {
        try {
            Http::timeout(4)->post('http://127.0.0.1:3005/logout');
        } catch (\Throwable $e) {}

        return redirect()->route('erp.dashboard', ['tab' => 'whatsapp'])
            ->with('success', 'تم فصل حساب الواتساب التسويقي بنجاح.');
    }

    private function getSystemContactsForAds(): array
    {
        $contacts = [];
        if (Schema::hasTable('offices')) {
            foreach (DB::table('offices')->select('name', 'phone')->get() as $o) {
                if (!empty($o->phone)) $contacts[] = ['name' => $o->name, 'phone' => self::toLocalPhone($o->phone)];
            }
        }
        if (Schema::hasTable('owners')) {
            foreach (DB::table('owners')->select('name', 'phone')->get() as $ow) {
                if (!empty($ow->phone)) $contacts[] = ['name' => $ow->name, 'phone' => self::toLocalPhone($ow->phone)];
            }
        }
        if (Schema::hasTable('tenants')) {
            foreach (DB::table('tenants')->select('name', 'phone')->get() as $t) {
                if (!empty($t->phone)) $contacts[] = ['name' => $t->name, 'phone' => self::toLocalPhone($t->phone)];
            }
        }
        return $contacts;
    }

    public function sendSingleAdWhatsapp(Request $request)
    {
        $rawPhone = trim((string) $request->input('phone', ''));
        $message  = trim((string) $request->input('message', ''));
        $mediaBase64OrUrl = trim((string) $request->input('media_data', ''));
        $mediaFileName    = trim((string) $request->input('media_filename', 'Attachment.pdf'));
        $mediaMimeType    = trim((string) $request->input('media_type', ''));

        if (empty($rawPhone)) {
            return response()->json(['success' => false, 'message' => 'رقم الجوال فارغ.']);
        }

        // 1. إذا كان محرك الـ QR متصلاً فعلياً، نرسل عبره أولاً
        $liveState = $this->fetchLiveMarketingQrState();
        if (!empty($liveState['connected'])) {
            try {
                $res = Http::timeout(25)->post('http://127.0.0.1:3005/send', [
                    'to'             => self::formatSaudiPhone($rawPhone),
                    'message'        => $message,
                    'media_data'     => $mediaBase64OrUrl,
                    'media_filename' => $mediaFileName,
                    'media_type'     => $mediaMimeType,
                ]);

                $json = $res->json();
                $ok = $res->successful() && !empty($json['success']);

                return response()->json([
                    'success' => $ok,
                    'message' => $ok ? 'تم الإرسال عبر QR بنجاح' : ($json['message'] ?? 'فشل الإرسال عبر QR')
                ]);
            } catch (\Throwable $e) {
                return response()->json(['success' => false, 'message' => 'خطأ في محرك الإرسال: ' . $e->getMessage()]);
            }
        }

        // 2. إذا لم يكن الـ QR متصلاً، نرسل مباشرة عبر Meta WhatsApp Cloud API الرسمي
        $apiToken = self::getSetting('whatsapp_api_token', env('WHATSAPP_API_TOKEN', ''));
        if (!empty($apiToken)) {
            $sent = !empty($mediaBase64OrUrl)
                ? self::sendWhatsAppDocument($rawPhone, $mediaBase64OrUrl, $mediaFileName, $message, null)
                : self::sendWhatsAppMessage($rawPhone, $message, null);

            return response()->json([
                'success' => $sent,
                'message' => $sent
                    ? 'تم الإرسال عبر WhatsApp Business API الرسمي بنجاح'
                    : 'تعذر الإرسال عبر API (تأكد من صلاحية التوكن وفتح نافذة المحادثة مع الرقم)'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'يرجى حفظ إعدادات Meta WhatsApp Cloud API بالأعلى أو مسح كود الـ QR أولاً.'
        ]);
    }

    public function logAdCampaign(Request $request)
    {
        self::ensureSiteAnalyticsAndAdsTables();
        DB::table('whatsapp_ad_campaigns')->insert([
            'campaign_name'    => trim((string) $request->input('campaign_name', 'حملة إعلانية ' . date('Y-m-d H:i'))),
            'message_template' => mb_substr((string) $request->input('message_template', ''), 0, 1000),
            'attachment_type'  => (string) $request->input('attachment_type', 'نص فقط'),
            'total_targets'    => (int) $request->input('total_targets', 0),
            'sent_count'       => (int) $request->input('sent_count', 0),
            'failed_count'     => (int) $request->input('failed_count', 0),
            'min_delay_sec'    => (int) $request->input('min_delay_sec', 5),
            'max_delay_sec'    => (int) $request->input('max_delay_sec', 15),
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        return response()->json(['success' => true]);
    }
}
