<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. جدول المستأجرين
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('id_type', ['national_id', 'iqama', 'cr']); // هوية وطنية، إقامة، سجل تجاري
            $table->string('id_number')->index();
            $table->string('phone'); // الجوال المرتبط بأبشر (لعقود إيجار)
            $table->string('email')->nullable();
            $table->date('birth_date')->nullable();
            $table->timestamps();
        });

        // 2. جدول عقود الإيجار (مجهز للربط مع منصة إيجار)
        Schema::create('lease_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number')->unique(); // رقم العقد الداخلي
            $table->string('ejar_contract_number')->nullable()->index(); // رقم العقد الموحد في منصة إيجار
            $table->enum('ejar_status', ['not_sent', 'pending_approval', 'active', 'expired', 'cancelled'])->default('not_sent');
            
            $table->foreignId('office_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->constrained();
            $table->foreignId('property_unit_id')->nullable()->constrained();
            $table->foreignId('owner_id')->constrained();
            $table->foreignId('tenant_id')->constrained();
            
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('payment_cycle', ['monthly', 'quarterly', 'semi_annual', 'annual']); // دورة السداد
            
            $table->decimal('total_rent_amount', 12, 2); // إجمالي قيمة الإيجار السنوي
            $table->decimal('security_deposit', 12, 2)->default(0); // مبلغ التأمين
            $table->decimal('office_commission', 12, 2)->default(0); // عمولة السعي/المكتب
            $table->decimal('company_fee_amount', 12, 2)->default(0); // حصة الشركة الأم من هذا العقد (إن كان الاتفاق بالنسبة)
            
            $table->enum('status', ['draft', 'active', 'expired', 'terminated'])->default('draft');
            $table->timestamps();
        });

        // 3. شجرة الحسابات المحاسبية (Chart of Accounts)
        // ملاحظة: إذا كان office_id فارغاً (null) فهذا يعني أن الحساب يتبع لشجرة حسابات "الشركة الأم"
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->string('code')->index(); // رقم الحساب (مثال: 1101 الصندوق، 4101 إيرادات الاشتراكات)
            $table->string('name'); // اسم الحساب
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']); // أصول، خصوم، حقوق ملكية، إيرادات، مصروفات
            $table->boolean('is_group')->default(false); // هل هو حساب رئيسي (مجلد) أم حساب فرعي يقبل القيود؟
            $table->decimal('balance', 15, 2)->default(0); // الرصيد الحالي
            $table->timestamps();
        });

        // 4. جدول الفواتير (بين الشركة والمكاتب، أو بين المكاتب والمستأجرين)
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            
            // تحديد مُصدر الفاتورة (الشركة الأم أم مكتب العقار)
            $table->enum('issuer_type', ['company', 'office']);
            $table->foreignId('office_id')->nullable()->constrained(); // المكتب (إما كعميل يدفع للشركة، أو كمُصدر للفاتورة للمستأجر)
            
            // في حال كانت الفاتورة لمستأجر
            $table->foreignId('lease_contract_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            
            $table->string('description'); // بيان الفاتورة (مثال: اشتراك سنوي، أو الدفعة الأولى لعقد إيجار رقم...)
            $table->date('issue_date');
            $table->date('due_date'); // تاريخ الاستحقاق
            
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax_amount', 12, 2)->default(0); // ضريبة القيمة المضافة إن وجدت
            $table->decimal('total_amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0);
            
            $table->enum('status', ['unpaid', 'partially_paid', 'paid', 'overdue', 'cancelled'])->default('unpaid');
            $table->timestamps();
        });

        // 5. جدول السندات المالية (سندات القبض وسندات الصرف)
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_number')->unique();
            $table->enum('voucher_type', ['receipt', 'payment']); // receipt = سند قبض ، payment = سند صرف
            
            // هل السند خاص بحسابات الشركة الأم أم مكتب عقار؟
            $table->foreignId('office_id')->nullable()->constrained()->cascadeOnDelete();
            
            // الربط المحاسبي في شجرة الحسابات
            $table->foreignId('debit_account_id')->constrained('accounts');  // الحساب المدين (من ح/)
            $table->foreignId('credit_account_id')->constrained('accounts'); // الحساب الدائن (إلى ح/)
            
            // الروابط الاختيارية لمعرفة صاحب العلاقة
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', ['cash', 'bank_transfer', 'mada', 'sadad', 'cheque']);
            $table->string('reference_number')->nullable(); // رقم الحوالة البنكية أو الشيك
            $table->date('voucher_date');
            $table->text('notes')->nullable(); // البيان (وذلك عن...)
            
            $table->foreignId('created_by')->constrained('users'); // الموظف الذي أصدر السند
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('lease_contracts');
        Schema::dropIfExists('tenants');
    }
};