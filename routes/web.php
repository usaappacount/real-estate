<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyErpController;
use App\Http\Controllers\OfficeErpController;
use App\Http\Controllers\OwnerErpController;

// مسارات تسجيل الدخول الموحد (واتساب OTP + اسم المستخدم وكلمة المرور)
Route::get('/login', [CompanyErpController::class, 'showLogin'])->name('login');
Route::post('/auth/send-otp', [CompanyErpController::class, 'sendOtp'])->name('auth.send_otp');
Route::post('/auth/verify-otp', [CompanyErpController::class, 'verifyOtp'])->name('auth.verify_otp');
Route::post('/auth/login-password', [CompanyErpController::class, 'loginWithPassword'])->name('auth.login_password');
Route::post('/logout', [CompanyErpController::class, 'logout'])->name('logout');

// مسارات النظام المحمية بتسجيل الدخول
Route::middleware('auth')->group(function () {
    // اللوحة الرئيسية للشركة الأم (الأدمن - مالك المنصة)
    Route::get('/', [CompanyErpController::class, 'dashboard'])->name('erp.dashboard');

    // ميزة دخول الأدمن لمعاينة وإدارة أي مكتب عقار بضغطة زر
    Route::get('/admin/switch-to-office/{id}', [CompanyErpController::class, 'switchToOffice'])->name('erp.switch_office');
    Route::get('/admin/return-to-company', [CompanyErpController::class, 'returnToCompany'])->name('erp.return_company');

    // تحديث بيانات الدخول الشخصية وإعدادات الواتساب المركزي (بدعم جميع التسميات)
    Route::post('/profile/update', [CompanyErpController::class, 'updateProfile'])->name('erp.profile.update');
    Route::post('/whatsapp/save', [CompanyErpController::class, 'updateWhatsappSettings'])->name('erp.whatsapp.save');
    Route::post('/whatsapp/update', [CompanyErpController::class, 'updateWhatsappSettings'])->name('erp.whatsapp.update');
    Route::post('/whatsapp-settings/update', [CompanyErpController::class, 'updateWhatsappSettings'])->name('erp.whatsapp_settings.update');

    // إدارة وتعديل مكاتب العقار والاتفاقيات المالية
    Route::post('/offices/store', [CompanyErpController::class, 'storeOffice'])->name('erp.offices.store');
    Route::post('/offices/{id}/update', [CompanyErpController::class, 'updateOffice'])->name('erp.offices.update');

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

    // =========================================================================
    // مسارات بوابة مكتب العقار (Office Portal Routes)
    // =========================================================================
    Route::prefix('office')->name('office.')->group(function () {
        Route::get('/dashboard', [OfficeErpController::class, 'dashboard'])->name('dashboard');

        // معاينة بوابة المالك (سرية ومحصورة فقط للأدمن مالك المنصة company_admin)
        Route::get('/switch-to-owner/{id}', [OwnerErpController::class, 'switchToOwner'])->name('switch_owner');
        Route::get('/return-to-office', [OwnerErpController::class, 'returnToOffice'])->name('return_office');

        Route::post('/owners/store', [OfficeErpController::class, 'storeOwner'])->name('owners.store');
        Route::post('/owners/{id}/update', [OfficeErpController::class, 'updateOwner'])->name('owners.update');
        Route::post('/owners/{id}/delete', [OfficeErpController::class, 'deleteOwner'])->name('owners.delete');

        Route::post('/properties/store', [OfficeErpController::class, 'storeProperty'])->name('properties.store');
        Route::post('/properties/{id}/delete', [OfficeErpController::class, 'deleteProperty'])->name('properties.delete');
        Route::post('/units/store', [OfficeErpController::class, 'storeUnit'])->name('units.store');
        Route::post('/units/{id}/delete', [OfficeErpController::class, 'deleteUnit'])->name('units.delete');

        Route::post('/tenants/store', [OfficeErpController::class, 'storeTenant'])->name('tenants.store');
        Route::post('/tenants/{id}/update', [OfficeErpController::class, 'updateTenant'])->name('tenants.update');
        Route::post('/tenants/{id}/delete', [OfficeErpController::class, 'deleteTenant'])->name('tenants.delete');

        Route::post('/contracts/store', [OfficeErpController::class, 'storeContract'])->name('contracts.store');
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

        Route::post('/staff/store', [OfficeErpController::class, 'storeStaff'])->name('staff.store');
        Route::post('/staff/{id}/delete', [OfficeErpController::class, 'deleteStaff'])->name('staff.delete');

        Route::post('/whatsapp/update', [OfficeErpController::class, 'updateOfficeWhatsappSettings'])->name('whatsapp.update');
        Route::post('/whatsapp-settings/update', [OfficeErpController::class, 'updateOfficeWhatsappSettings'])->name('whatsapp_settings.update');
    });

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