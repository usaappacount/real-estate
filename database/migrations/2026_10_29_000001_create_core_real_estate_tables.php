<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. جدول مكاتب العقار (يشمل اتفاقية التسعير مع الشركة الأم)
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم مكتب العقار
            $table->string('cr_number')->unique()->nullable(); // السجل التجاري
            $table->string('fal_license')->nullable(); // رخصة فال العقارية
            $table->string('phone');
            $table->string('email')->unique();
            $table->text('address')->nullable();
            
            // نوع الاتفاق المالي مع الشركة الأم
            $table->enum('billing_type', ['annual_subscription', 'per_property_percentage', 'hybrid'])->default('annual_subscription');
            $table->decimal('annual_fee', 12, 2)->default(0); // قيمة الاشتراك السنوي
            $table->decimal('company_commission_rate', 5, 2)->default(0); // نسبة الشركة الأم من كل عقار/عقد (%)
            $table->date('subscription_ends_at')->nullable();
            $table->enum('status', ['active', 'suspended', 'pending'])->default('active');
            $table->timestamps();
        });

        // 2. جدول الملاك (Owners)
        Schema::create('owners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained()->cascadeOnDelete(); // المكتب الذي يدير هذا المالك
            $table->string('name');
            $table->string('national_id')->index(); // رقم الهوية / الإقامة
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('iban')->nullable(); // الآيبان لتحويل الإيجارات
            $table->string('bank_name')->nullable();
            $table->decimal('office_management_fee_rate', 5, 2)->default(5.00); // نسبة المكتب من إدارة أملاك هذا المالك (%)
            $table->timestamps();
        });

        // 3. تعديل جدول المستخدمين ليدعم (موظفي الشركة، موظفي المكاتب، الملاك وعائلاتهم، المستأجرين)
        Schema::table('users', function (Blueprint $table) {
            $table->enum('user_type', [
                'company_admin',    // صاحب الشركة الأم
                'company_staff',    // موظف في الشركة الأم
                'office_admin',     // مدير مكتب عقار
                'office_staff',     // موظف في مكتب عقار
                'owner',            // مالك عقار أصيل
                'owner_family',     // فرد من عائلة المالك / وكيل
                'tenant'            // مستأجر
            ])->default('office_admin')->after('id');

            $table->foreignId('office_id')->nullable()->after('user_type')->constrained('offices')->nullOnDelete();
            $table->foreignId('owner_id')->nullable()->after('office_id')->constrained('owners')->nullOnDelete();
            $table->string('phone')->nullable()->after('email');
            $table->string('national_id')->nullable()->after('phone');
            $table->boolean('is_active')->default(true);
            $table->json('custom_permissions')->nullable(); // صلاحيات مخصصة لأفراد العائلة والموظفين
        });

        // 4. إعدادات الشركة الأم: التصنيفات وأنواع العقارات (سكني، تجاري، زراعي، عمارة، شقة...)
        Schema::create('property_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // مثال: أرض، عمارة، شقة، فيلا، معرض
            $table->enum('category', ['residential', 'commercial', 'agricultural', 'industrial']); // سكني، تجاري، زراعي، صناعي
            $table->boolean('has_units')->default(false); // هل يتفرع منه وحدات؟ (مثل العمارة تتفرع لشقق)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. المتطلبات الديناميكية التي يحددها صاحب الشركة لكل نوع عقار
        Schema::create('property_type_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_type_id')->constrained()->cascadeOnDelete();
            $table->string('attribute_name'); // مثال: المساحة، عدد الأدوار، رقم البئر، عرض الشارع
            $table->enum('field_type', ['text', 'number', 'select', 'boolean', 'date']); // نوع الحقل
            $table->json('options')->nullable(); // خيارات في حال كان الحقل قائمة منسدلة
            $table->boolean('is_required')->default(true);
            $table->timestamps();
        });

        // 6. جدول العقارات والصكوك
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained()->cascadeOnDelete();
            $table->foreignId('owner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_type_id')->constrained();
            
            $table->string('title'); // اسم العقار (مثال: عمارة العليا السكنية)
            $table->string('deed_number')->index(); // رقم الصك
            $table->date('deed_date')->nullable(); // تاريخ الصك
            $table->decimal('total_area', 12, 2); // المساحة الإجمالية بالمتر المربع
            $table->decimal('estimated_value', 15, 2)->nullable(); // القيمة التقديرية للعقار (مهم لحساب نسبة الشركة الأم إن وجدت)
            
            // بيانات العنوان الوطني (مطلوبة للربط مع منصة إيجار)
            $table->string('city');
            $table->string('district');
            $table->string('street')->nullable();
            $table->string('building_number')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('additional_number')->nullable();
            
            $table->json('custom_attributes')->nullable(); // تخزين إجابات المتطلبات الديناميكية التي حددها الأدمن
            $table->timestamps();
        });

        // 7. جدول الوحدات العقارية (الشقق، المحلات داخل العقار)
        Schema::create('property_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_type_id')->constrained(); // شقة، محل، مكتب
            $table->string('unit_number'); // رقم الوحدة
            $table->integer('floor_number')->nullable(); // رقم الدور
            $table->decimal('area', 10, 2)->nullable();
            $table->string('electricity_meter')->nullable(); // رقم عداد الكهرباء (مطلوب لإيجار)
            $table->string('water_meter')->nullable(); // رقم عداد المياه
            $table->decimal('default_rent_price', 12, 2); // السعر التأجيري الافتراضي
            $table->enum('status', ['vacant', 'rented', 'maintenance'])->default('vacant');
            $table->json('custom_attributes')->nullable(); // غرف النوم، الصالات، إلخ
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_units');
        Schema::dropIfExists('properties');
        Schema::dropIfExists('property_type_attributes');
        Schema::dropIfExists('property_types');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['office_id']);
            $table->dropForeign(['owner_id']);
            $table->dropColumn(['user_type', 'office_id', 'owner_id', 'phone', 'national_id', 'is_active', 'custom_permissions']);
        });
        Schema::dropIfExists('owners');
        Schema::dropIfExists('offices');
    }
};