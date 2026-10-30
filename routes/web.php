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
    // اللوحة الرئيسية للشركة الأم (الأدمن)
    Route::get('/', [CompanyErpController::class, 'dashboard'])->name('erp.dashboard');

    // ميزة دخول الأدمن لمعاينة وإدارة أي مكتب عقار بضغطة زر
    Route::get('/admin/switch-to-office/{id}', [CompanyErpController::class, 'switchToOffice'])->name('erp.switch_office');
    Route::get('/admin/return-to-company', [CompanyErpController::class, 'returnToCompany'])->name('erp.return_company');

    // تحديث بيانات الدخول الشخصية (الجوال، اسم المستخدم، كلمة المرور)
    Route::post('/profile/update', [CompanyErpController::class, 'updateProfile'])->name('erp.profile.update');

    // إدارة وتعديل مكاتب العقار والاتفاقيات المالية
    Route::post('/offices/store', [CompanyErpController::class, 'storeOffice'])->name('erp.offices.store');
    Route::post('/offices/{id}/update', [CompanyErpController::class, 'updateOffice'])->name('erp.offices.update');

    // إدارة التصنيفات الرئيسية (إضافة، تعديل، حذف)
    Route::post('property-categories/store', [CompanyErpController::class, 'storePropertyCategory'])->name('erp.property_categories.store');
    Route::post('property-categories/{id}/update', [CompanyErpController::class, 'updatePropertyCategory'])->name('erp.property_categories.update');
    Route::post('property-categories/{id}/delete', [CompanyErpController::class, 'deletePropertyCategory'])->name('erp.property_categories.delete');

    // إدارة أنواع الحقول (إضافة، تعديل، حذف)
    Route::post('field-types/store', [CompanyErpController::class, 'storeFieldType'])->name('erp.field_types.store');
    Route::post('field-types/{id}/update', [CompanyErpController::class, 'updateFieldType'])->name('erp.field_types.update');
    Route::post('field-types/{id}/delete', [CompanyErpController::class, 'deleteFieldType'])->name('erp.field_types.delete');

    // إدارة أنواع العقارات (إضافة، تعديل، حذف)
    Route::post('property-types/store', [CompanyErpController::class, 'storePropertyType'])->name('erp.property_types.store');
    Route::post('property-types/{id}/update', [CompanyErpController::class, 'updatePropertyType'])->name('erp.property_types.update');
    Route::post('property-types/{id}/delete', [CompanyErpController::class, 'deletePropertyType'])->name('erp.property_types.delete');

    // إدارة المتطلبات الديناميكية (إضافة متعددة، تعديل، حذف)
    Route::post('property-attributes/store', [CompanyErpController::class, 'storePropertyAttribute'])->name('erp.property_attributes.store');
    Route::post('property-attributes/{id}/update', [CompanyErpController::class, 'updatePropertyAttribute'])->name('erp.property_attributes.update');
    Route::post('property-attributes/{id}/delete', [CompanyErpController::class, 'deletePropertyAttribute'])->name('erp.property_attributes.delete');

    // إدارة أنواع الحسابات المحاسبية (إضافة، تعديل، حذف)
    Route::post('account-types/store', [CompanyErpController::class, 'storeAccountType'])->name('erp.account_types.store');
    Route::post('account-types/{id}/update', [CompanyErpController::class, 'updateAccountType'])->name('erp.account_types.update');
    Route::post('account-types/{id}/delete', [CompanyErpController::class, 'deleteAccountType'])->name('erp.account_types.delete');

    // شجرة الحسابات المحاسبية والسندات (للشركة الأم)
    Route::post('/accounts/seed-default', [CompanyErpController::class, 'seedDefaultAccounts'])->name('erp.accounts.seed');
    Route::post('/accounts/store', [CompanyErpController::class, 'storeAccount'])->name('erp.accounts.store');
    Route::post('/accounts/{id}/update', [CompanyErpController::class, 'updateAccount'])->name('erp.accounts.update');
    Route::post('/accounts/{id}/delete', [CompanyErpController::class, 'deleteAccount'])->name('erp.accounts.delete');
    Route::post('/vouchers/store', [CompanyErpController::class, 'storeVoucher'])->name('erp.vouchers.store');

    // =========================================================================
    // مسارات بوابة مكتب العقار (Office Portal Routes)
    // =========================================================================
    Route::prefix('office')->name('office.')->group(function () {
        Route::get('/dashboard', [OfficeErpController::class, 'dashboard'])->name('dashboard');

        // معاينة بوابة المالك من داخل المكتب
        Route::get('/switch-to-owner/{id}', [OwnerErpController::class, 'switchToOwner'])->name('switch_owner');
        Route::get('/return-to-office', [OwnerErpController::class, 'returnToOffice'])->name('return_office');

        // 1. إدارة الملاك (إضافة وتعديل وحذف)
        Route::post('/owners/store', [OfficeErpController::class, 'storeOwner'])->name('owners.store');
        Route::post('/owners/{id}/update', [OfficeErpController::class, 'updateOwner'])->name('owners.update');
        Route::post('/owners/{id}/delete', [OfficeErpController::class, 'deleteOwner'])->name('owners.delete');

        // 2. إدارة العقارات والصكوك والوحدات العقارية
        Route::post('/properties/store', [OfficeErpController::class, 'storeProperty'])->name('properties.store');
        Route::post('/properties/{id}/delete', [OfficeErpController::class, 'deleteProperty'])->name('properties.delete');
        Route::post('/units/store', [OfficeErpController::class, 'storeUnit'])->name('units.store');
        Route::post('/units/{id}/delete', [OfficeErpController::class, 'deleteUnit'])->name('units.delete');

        // 3. إدارة المستأجرين وعقود الإيجار (منصة إيجار)
        Route::post('/tenants/store', [OfficeErpController::class, 'storeTenant'])->name('tenants.store');
        Route::post('/contracts/store', [OfficeErpController::class, 'storeContract'])->name('contracts.store');
        Route::post('/contracts/{id}/sync-ejar', [OfficeErpController::class, 'syncWithEjar'])->name('contracts.sync_ejar');

        // 4. شجرة الحسابات المتفرعة والسندات المالية المستقلة الخاصة بالمكتب
        Route::post('/account-types/store', [OfficeErpController::class, 'storeOfficeAccountType'])->name('account_types.store');
        Route::post('/account-types/{id}/update', [OfficeErpController::class, 'updateOfficeAccountType'])->name('account_types.update');
        Route::post('/account-types/{id}/delete', [OfficeErpController::class, 'deleteOfficeAccountType'])->name('account_types.delete');

        Route::post('/accounts/seed', [OfficeErpController::class, 'seedOfficeAccounts'])->name('accounts.seed');
        Route::post('/accounts/store', [OfficeErpController::class, 'storeOfficeAccount'])->name('accounts.store');
        Route::post('/accounts/{id}/update', [OfficeErpController::class, 'updateOfficeAccount'])->name('accounts.update');
        Route::post('/accounts/{id}/delete', [OfficeErpController::class, 'deleteOfficeAccount'])->name('accounts.delete');
        Route::post('/vouchers/store', [OfficeErpController::class, 'storeOfficeVoucher'])->name('vouchers.store');

        // 5. إدارة موظفي المكتب وصلاحياتهم
        Route::post('/staff/store', [OfficeErpController::class, 'storeStaff'])->name('staff.store');
        Route::post('/staff/{id}/delete', [OfficeErpController::class, 'deleteStaff'])->name('staff.delete');
    });

    // =========================================================================
    // مسارات بوابة المالك وأفراد العائلة (Owner & Family Portal Routes)
    // =========================================================================
    Route::prefix('owner')->name('owner.')->group(function () {
        Route::get('/dashboard', [OwnerErpController::class, 'dashboard'])->name('dashboard');

        // إدارة أفراد العائلة والوكلاء وصلاحياتهم
        Route::post('/family/store', [OwnerErpController::class, 'storeFamilyMember'])->name('family.store');
        Route::post('/family/{id}/update', [OwnerErpController::class, 'updateFamilyMember'])->name('family.update');
        Route::post('/family/{id}/delete', [OwnerErpController::class, 'deleteFamilyMember'])->name('family.delete');

        // تحديث بيانات دخول المالك أو فرد العائلة
        Route::post('/profile/update', [OwnerErpController::class, 'updateProfile'])->name('profile.update');
    });
});