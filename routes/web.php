<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyErpController;
use App\Http\Controllers\OfficeErpController;
use App\Http\Controllers\OwnerErpController;

// الواجهة الرئيسية التعريفية للمنصة وخدماتها (Landing Page)
Route::get('/', [CompanyErpController::class, 'showLandingPage'])->name('landing');

// مسار عرض وتحميل الفاتورة الضريبية العامة (لدعم زر إرسال الفاتورة عبر الواتساب)
Route::get('/public/invoice-pdf/{id}/{token}', [CompanyErpController::class, 'showPublicInvoicePdf'])->name('public.invoice_pdf');

// مسار Webhook واتساب الرسمي (يدعم GET لتوثيق Meta hub.challenge و POST لاستقبال الرسائل واعتماد السندات)
Route::match(['get', 'post'], '/webhook/whatsapp', [CompanyErpController::class, 'handleWhatsappWebhook'])
    ->middleware('throttle:60,1')
    ->name('webhook.whatsapp');

// مسارات تسجيل الدخول الموحد (واتساب OTP + اسم المستخدم وكلمة المرور)
Route::get('/login', [CompanyErpController::class, 'showLogin'])->name('login');
Route::post('/auth/send-otp', [CompanyErpController::class, 'sendOtp'])->middleware('throttle:5,1')->name('auth.send_otp');
Route::post('/auth/verify-otp', [CompanyErpController::class, 'verifyOtp'])->middleware('throttle:5,1')->name('auth.verify_otp');
Route::post('/auth/login-password', [CompanyErpController::class, 'loginWithPassword'])->middleware('throttle:8,1')->name('auth.login_password');
Route::post('/logout', [CompanyErpController::class, 'logout'])->name('logout');

// مسارات النظام المحمية بتسجيل الدخول + التتبع الفوري للعمليات (Audit Log)
Route::middleware([
    'auth',
    \App\Http\Middleware\RecordRequestActivity::class,
])->group(function () {

    // اللوحة الرئيسية للشركة الأم (الأدمن - مالك المنصة)
    Route::get('/admin/dashboard', [CompanyErpController::class, 'dashboard'])->name('erp.dashboard');
    Route::post('/admin/landing-settings/save', [CompanyErpController::class, 'saveLandingSettings'])->name('erp.landing.save');

    // ميزة دخول الأدمن لمعاينة وإدارة أي مكتب عقار بضغطة زر
    Route::get('/admin/switch-to-office/{id}', [CompanyErpController::class, 'switchToOffice'])->name('erp.switch_office');
    Route::get('/admin/return-to-company', [CompanyErpController::class, 'returnToCompany'])->name('erp.return_company');

    // تحديث بيانات الدخول الشخصية وإعدادات الواتساب المركزي (Meta Cloud API / Custom API)
    Route::post('/profile/update', [CompanyErpController::class, 'updateProfile'])->name('erp.profile.update');
    Route::post('/whatsapp/save', [CompanyErpController::class, 'updateWhatsappSettings'])->name('erp.whatsapp.save');
    Route::post('/whatsapp/update', [CompanyErpController::class, 'updateWhatsappSettings'])->name('erp.whatsapp.update');
    Route::post('/whatsapp-settings/update', [CompanyErpController::class, 'updateWhatsappSettings'])->name('erp.whatsapp_settings.update');
    Route::post('/whatsapp/test-api', [CompanyErpController::class, 'testWhatsappApiConnection'])->name('erp.whatsapp.test');

    // مسارات نظام الحملات الإعلانية عبر الواتساب (ربط QR أو Meta Cloud API) + إحصائيات الزوار
    Route::post('/whatsapp-ads/qr-generate', [CompanyErpController::class, 'generateAdsWhatsappQr'])->name('erp.whatsapp_ads.qr_generate');
    Route::get('/whatsapp-ads/qr-status', [CompanyErpController::class, 'checkAdsWhatsappQrStatus'])->name('erp.whatsapp_ads.qr_status');
    Route::post('/whatsapp-ads/qr-disconnect', [CompanyErpController::class, 'disconnectAdsWhatsappQr'])->name('erp.whatsapp_ads.qr_disconnect');
    Route::post('/whatsapp-ads/send-single', [CompanyErpController::class, 'sendSingleAdWhatsapp'])->name('erp.whatsapp_ads.send_single');
    Route::post('/whatsapp-ads/log-campaign', [CompanyErpController::class, 'logAdCampaign'])->name('erp.whatsapp_ads.log_campaign');
    Route::post('/admin/analytics/clear', [CompanyErpController::class, 'clearSiteVisits'])->name('erp.analytics.clear');

    // إدارة وتعديل مكاتب العقار والاتفاقيات المالية والباقات
    Route::post('/offices/store', [CompanyErpController::class, 'storeOffice'])->name('erp.offices.store');
    Route::post('/offices/{id}/update', [CompanyErpController::class, 'updateOffice'])->name('erp.offices.update');
    Route::post('/office-plans/store', [CompanyErpController::class, 'storeOfficePlan'])->name('erp.office_plans.store');
    Route::post('/office-plans/{id}/update', [CompanyErpController::class, 'updateOfficePlan'])->name('erp.office_plans.update');
    Route::post('/office-plans/{id}/delete', [CompanyErpController::class, 'deleteOfficePlan'])->name('erp.office_plans.delete');

    // إدارة التصنيفات الرئيسية
    Route::post('property-categories/store', [CompanyErpController::class, 'storePropertyCategory'])->name('erp.property_categories.store');
    Route::post('property-categories/{id}/update', [CompanyErpController::class, 'updatePropertyCategory'])->name('erp.property_categories.update');
    Route::post('property-categories/{id}/delete', [CompanyErpController::class, 'deletePropertyCategory'])->name('erp.property_categories.delete');

    // إدارة أنواع الحقول
    Route::post('field-types/store', [CompanyErpController::class, 'storeFieldType'])->name('erp.field_types.store');
    Route::post('field-types/{id}/update', [CompanyErpController::class, 'updateFieldType'])->name('erp.field_types.update');
    Route::post('field-types/{id}/delete', [CompanyErpController::class, 'deleteFieldType'])->name('erp.field_types.delete');

    // إدارة أنواع العقارات
    Route::post('property-types/store', [CompanyErpController::class, 'storePropertyType'])->name('erp.property_types.store');
    Route::post('property-types/{id}/update', [CompanyErpController::class, 'updatePropertyType'])->name('erp.property_types.update');
    Route::post('property-types/{id}/delete', [CompanyErpController::class, 'deletePropertyType'])->name('erp.property_types.delete');

    // إدارة المتطلبات الديناميكية
    Route::post('property-attributes/store', [CompanyErpController::class, 'storePropertyAttribute'])->name('erp.property_attributes.store');
    Route::post('property-attributes/{id}/update', [CompanyErpController::class, 'updatePropertyAttribute'])->name('erp.property_attributes.update');
    Route::post('property-attributes/{id}/delete', [CompanyErpController::class, 'deletePropertyAttribute'])->name('erp.property_attributes.delete');

    // إدارة أنواع الحسابات المحاسبية
    Route::post('account-types/store', [CompanyErpController::class, 'storeAccountType'])->name('erp.account_types.store');
    Route::post('account-types/{id}/update', [CompanyErpController::class, 'updateAccountType'])->name('erp.account_types.update');
    Route::post('account-types/{id}/delete', [CompanyErpController::class, 'deleteAccountType'])->name('erp.account_types.delete');

    // إدارة طرق الدفع (للأدمن والمنصة)
    Route::post('payment-methods/store', [CompanyErpController::class, 'storePaymentMethod'])->name('erp.payment_methods.store');
    Route::post('payment-methods/{id}/update', [CompanyErpController::class, 'updatePaymentMethod'])->name('erp.payment_methods.update');
    Route::post('payment-methods/{id}/delete', [CompanyErpController::class, 'deletePaymentMethod'])->name('erp.payment_methods.delete');

    // شجرة الحسابات المحاسبية والسندات (للشركة الأم)
    Route::post('/accounts/seed-default', [CompanyErpController::class, 'seedDefaultAccounts'])->name('erp.accounts.seed');
    Route::post('/accounts/store', [CompanyErpController::class, 'storeAccount'])->name('erp.accounts.store');
    Route::post('/accounts/{id}/update', [CompanyErpController::class, 'updateAccount'])->name('erp.accounts.update');
    Route::post('/accounts/{id}/delete', [CompanyErpController::class, 'deleteAccount'])->name('erp.accounts.delete');

    Route::post('/vouchers/store', [CompanyErpController::class, 'storeVoucher'])->name('erp.vouchers.store');
    Route::post('/vouchers/{id}/update', [CompanyErpController::class, 'updateVoucher'])->name('erp.vouchers.update');
    Route::post('/vouchers/{id}/delete', [CompanyErpController::class, 'deleteVoucher'])->name('erp.vouchers.delete');
    Route::get('/vouchers/{id}/print', [CompanyErpController::class, 'printVoucher'])->name('erp.vouchers.print');
    Route::post('/vouchers/{id}/whatsapp', [CompanyErpController::class, 'sendVoucherWhatsApp'])->name('erp.vouchers.whatsapp');
    Route::post('/vouchers/{id}/send-whatsapp', [CompanyErpController::class, 'sendVoucherWhatsApp'])->name('erp.vouchers.send_whatsapp');

    // مسارات القيود اليومية وكشف الحساب والقوائم المالية (للشركة الأم)
    Route::post('/journal-entries/store', [CompanyErpController::class, 'storeJournalEntry'])->name('erp.journal.store');
    Route::post('/journal-entries/{id}/update', [CompanyErpController::class, 'updateJournalEntry'])->name('erp.journal.update');
    Route::post('/journal-entries/{id}/delete', [CompanyErpController::class, 'deleteJournalEntry'])->name('erp.journal.delete');

    // مسارات الفوترة الضريبية للشركة الأم (الأدمن)
    Route::post('/tax-profile/save', [CompanyErpController::class, 'saveCompanyTaxProfile'])->name('erp.tax_profile.save');
    Route::post('/invoices/store', [CompanyErpController::class, 'storeCompanyInvoice'])->name('erp.invoices.store');
    Route::post('/invoices/{id}/whatsapp', [CompanyErpController::class, 'sendCompanyInvoiceWhatsapp'])->name('erp.invoices.whatsapp');
    Route::post('/invoices/{id}/delete', [CompanyErpController::class, 'deleteCompanyInvoice'])->name('erp.invoices.delete');
    Route::post('/offices/{id}/toggle-owner-invoicing', [CompanyErpController::class, 'toggleOfficeOwnerInvoicing'])->name('erp.offices.toggle_owner_invoicing');

    // مسارات سجل الأخطاء، الفحص الأوتوماتيكي الشامل، توليد البيانات التجريبية، والنسخ الاحتياطي (للأدمن)
    Route::post('/admin/system/run-diagnostics', [CompanyErpController::class, 'runSystemDiagnostics'])->name('erp.system.diagnostics');
    Route::post('/admin/system/seed-demo-data', [CompanyErpController::class, 'seedFullSystemDemoData'])->name('erp.system.seed_demo');
    Route::post('/admin/system/clear-logs', [CompanyErpController::class, 'clearSystemLogs'])->name('erp.system.clear_logs');
    Route::get('/admin/system/backup', [CompanyErpController::class, 'downloadSystemBackup'])->name('erp.system.backup');
    Route::post('/admin/system/restore', [CompanyErpController::class, 'restoreSystemBackup'])->name('erp.system.restore');

    // =========================================================================
    // مسارات بوابة مكتب العقار (Office Portal Routes)
    // =========================================================================
    Route::prefix('office')->name('office.')->group(function () {
        Route::get('/dashboard', [OfficeErpController::class, 'dashboard'])->name('dashboard');

        Route::get('/switch-to-owner/{id}', [OwnerErpController::class, 'switchToOwner'])->name('switch_owner');
        Route::get('/return-to-office', [OwnerErpController::class, 'returnToOffice'])->name('return_office');

        Route::post('/owners/store', [OfficeErpController::class, 'storeOwner'])->name('owners.store');
        Route::post('/owners/{id}/update', [OfficeErpController::class, 'updateOwner'])->name('owners.update');
        Route::post('/owners/{id}/delete', [OfficeErpController::class, 'deleteOwner'])->name('owners.delete');

        Route::post('/properties/store', [OfficeErpController::class, 'storeProperty'])->name('properties.store');
        Route::post('/properties/{id}/update', [OfficeErpController::class, 'updateProperty'])->name('properties.update');
        Route::post('/properties/{id}/delete', [OfficeErpController::class, 'deleteProperty'])->name('properties.delete');
        Route::post('/units/store', [OfficeErpController::class, 'storeUnit'])->name('units.store');
        Route::post('/units/{id}/update', [OfficeErpController::class, 'updateUnit'])->name('units.update');
        Route::post('/units/{id}/delete', [OfficeErpController::class, 'deleteUnit'])->name('units.delete');

        Route::post('/tenants/store', [OfficeErpController::class, 'storeTenant'])->name('tenants.store');
        Route::post('/tenants/{id}/update', [OfficeErpController::class, 'updateTenant'])->name('tenants.update');
        Route::post('/tenants/{id}/delete', [OfficeErpController::class, 'deleteTenant'])->name('tenants.delete');

        Route::post('/contracts/store', [OfficeErpController::class, 'storeContract'])->name('contracts.store');
        Route::post('/contracts/{id}/update', [OfficeErpController::class, 'updateContract'])->name('contracts.update');
        Route::post('/contracts/{id}/delete', [OfficeErpController::class, 'deleteContract'])->name('contracts.delete');
        Route::post('/contracts/{id}/sync-ejar', [OfficeErpController::class, 'syncWithEjar'])->name('contracts.sync_ejar');

        Route::post('/account-types/store', [OfficeErpController::class, 'storeOfficeAccountType'])->name('account_types.store');
        Route::post('/account-types/{id}/update', [OfficeErpController::class, 'updateOfficeAccountType'])->name('account_types.update');
        Route::post('/account-types/{id}/delete', [OfficeErpController::class, 'deleteOfficeAccountType'])->name('account_types.delete');

        Route::post('/accounts/seed', [OfficeErpController::class, 'seedOfficeAccounts'])->name('accounts.seed');
        Route::post('/accounts/store', [OfficeErpController::class, 'storeOfficeAccount'])->name('accounts.store');
        Route::post('/accounts/{id}/update', [OfficeErpController::class, 'updateOfficeAccount'])->name('accounts.update');
        Route::post('/accounts/{id}/delete', [OfficeErpController::class, 'deleteOfficeAccount'])->name('accounts.delete');

        Route::post('/vouchers/store', [OfficeErpController::class, 'storeOfficeVoucher'])->name('vouchers.store');
        Route::post('/vouchers/{id}/update', [OfficeErpController::class, 'updateOfficeVoucher'])->name('vouchers.update');
        Route::post('/vouchers/{id}/delete', [OfficeErpController::class, 'deleteOfficeVoucher'])->name('vouchers.delete');
        Route::post('/vouchers/{id}/whatsapp', [OfficeErpController::class, 'sendOfficeVoucherWhatsapp'])->name('vouchers.whatsapp');
        Route::post('/vouchers/{id}/send-whatsapp', [OfficeErpController::class, 'sendOfficeVoucherWhatsapp'])->name('vouchers.send_whatsapp');

        Route::post('/payment-methods/store', [OfficeErpController::class, 'storeOfficePaymentMethod'])->name('payment_methods.store');
        Route::post('/payment-methods/{id}/update', [OfficeErpController::class, 'updateOfficePaymentMethod'])->name('payment_methods.update');
        Route::post('/payment-methods/{id}/delete', [OfficeErpController::class, 'deleteOfficePaymentMethod'])->name('payment_methods.delete');

        Route::post('/journal-entries/store', [OfficeErpController::class, 'storeOfficeJournalEntry'])->name('journal.store');
        Route::post('/journal-entries/{id}/update', [OfficeErpController::class, 'updateOfficeJournalEntry'])->name('journal.update');
        Route::post('/journal-entries/{id}/delete', [OfficeErpController::class, 'deleteOfficeJournalEntry'])->name('journal.delete');

        Route::post('/staff/store', [OfficeErpController::class, 'storeStaff'])->name('staff.store');
        Route::post('/staff/{id}/update', [OfficeErpController::class, 'updateStaff'])->name('staff.update');
        Route::post('/staff/{id}/delete', [OfficeErpController::class, 'deleteStaff'])->name('staff.delete');

        Route::post('/whatsapp/update', [OfficeErpController::class, 'updateOfficeWhatsappSettings'])->name('whatsapp.update');
        Route::post('/whatsapp-settings/update', [OfficeErpController::class, 'updateOfficeWhatsappSettings'])->name('whatsapp_settings.update');
        Route::post('/whatsapp/run-reminders', [OfficeErpController::class, 'triggerLeaseReminders'])->name('whatsapp.run_reminders');

        Route::post('/tax-profile/save', [OfficeErpController::class, 'saveOfficeTaxProfile'])->name('tax_profile.save');
        Route::post('/owners/{id}/tax-save', [OfficeErpController::class, 'saveOwnerTaxProfile'])->name('owners.tax_save');
        Route::post('/tenants/{id}/tax-save', [OfficeErpController::class, 'saveTenantTaxProfile'])->name('tenants.tax_save');
        Route::post('/invoices/store', [OfficeErpController::class, 'storeOfficeInvoice'])->name('invoices.store');
        Route::post('/invoices/{id}/whatsapp', [OfficeErpController::class, 'sendOfficeInvoiceWhatsapp'])->name('invoices.whatsapp');
        Route::post('/invoices/{id}/delete', [OfficeErpController::class, 'deleteOfficeInvoice'])->name('invoices.delete');

        Route::post('/smart-settings/save', [OfficeErpController::class, 'saveSmartOfficeSettings'])->name('smart_settings.save');
        Route::post('/vouchers/batch-store', [OfficeErpController::class, 'storeBatchReceipts'])->name('vouchers.batch_store');
        Route::post('/vouchers/{id}/approve', [OfficeErpController::class, 'approvePendingVoucher'])->name('vouchers.approve');
        Route::post('/accounting/close-year', [OfficeErpController::class, 'closeFiscalYear'])->name('accounting.close_year');
        Route::post('/installments/pay', [OfficeErpController::class, 'payInstallmentPart'])->name('installments.pay');

        // مسارات ماسح الصكوك والعقود الذكي (AI OCR) + المدقق المحاسبي + التقرير اليومي + اتحاد الملاك (Mullak)
        Route::post('/ocr/auto-sync', [OfficeErpController::class, 'syncOcrDeedOrContract'])->name('ocr.auto_sync');
        Route::post('/auditor/auto-fix/{id}', [OfficeErpController::class, 'autoFixAuditedVoucher'])->name('auditor.auto_fix');
        Route::post('/whatsapp/daily-report-settings', [OfficeErpController::class, 'saveDailyReportSettings'])->name('whatsapp.daily_report_settings');
        Route::post('/mullak/building/store', [OfficeErpController::class, 'storeMullakBuilding'])->name('mullak.building.store');
        Route::post('/mullak/building/{id}/delete', [OfficeErpController::class, 'deleteMullakBuilding'])->name('mullak.building.delete');
        Route::post('/mullak/member/store', [OfficeErpController::class, 'storeMullakMember'])->name('mullak.member.store');
        Route::post('/mullak/member/{id}/update', [OfficeErpController::class, 'updateMullakMember'])->name('mullak.member.update');
        Route::post('/mullak/member/{id}/pay', [OfficeErpController::class, 'payMullakMemberFee'])->name('mullak.member.pay');
        Route::post('/mullak/member/{id}/delete', [OfficeErpController::class, 'deleteMullakMember'])->name('mullak.member.delete');
        Route::post('/mullak/expense/store', [OfficeErpController::class, 'storeMullakExpense'])->name('mullak.expense.store');
        Route::post('/mullak/expense/{id}/delete', [OfficeErpController::class, 'deleteMullakExpense'])->name('mullak.expense.delete');
    });

    Route::post('/whatsapp/send-raw-pdf', [CompanyErpController::class, 'sendRawPdfViaWhatsapp'])->name('erp.whatsapp.send_raw_pdf');
    Route::post('/admin/accounting/close-year', [CompanyErpController::class, 'closeCompanyFiscalYear'])->name('erp.accounting.close_year');

    // =========================================================================
    // مسارات بوابة المالك وأفراد العائلة (Owner & Family Portal Routes)
    // =========================================================================
    Route::prefix('owner')->name('owner.')->group(function () {
        Route::get('/dashboard', [OwnerErpController::class, 'dashboard'])->name('dashboard');

        Route::post('/family/store', [OwnerErpController::class, 'storeFamilyMember'])->name('family.store');
        Route::post('/family/{id}/update', [OwnerErpController::class, 'updateFamilyMember'])->name('family.update');
        Route::post('/family/{id}/delete', [OwnerErpController::class, 'deleteFamilyMember'])->name('family.delete');

        Route::post('/profile/update', [OwnerErpController::class, 'updateProfile'])->name('profile.update');
    });
});