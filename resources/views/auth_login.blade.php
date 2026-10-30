<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول الموحد | منظومة إدارة العقارات والأملاك</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Readex+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Readex Pro', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <div class="absolute -top-40 -right-40 w-96 h-96 bg-emerald-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-teal-500/15 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md bg-slate-900/90 backdrop-blur-xl border border-slate-800 rounded-3xl shadow-2xl p-8 relative z-10">
        
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-tr from-emerald-600 to-teal-400 rounded-2xl mx-auto flex items-center justify-center shadow-lg shadow-emerald-600/30 mb-4">
                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">بوابة الدخول الموحد</h1>
            <p class="text-slate-400 text-sm mt-1">الإدارة المركزية • مكاتب العقار • الملاك</p>
        </div>

        @if(session('error'))
            <div class="mb-5 p-4 rounded-2xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-5 p-4 rounded-2xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm font-medium">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- الحالة 1: إدخال كود التحقق المرسل للواتساب (تركيز تلقائي + دخول تلقائي فور كتابة 4 أرقام) -->
        @if(session('otp_step'))
            <div id="otpSection">
                <div class="mb-5 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm">
                    <p class="font-semibold">تم إرسال كود التحقق إلى ({{ session('formatted_phone') }})</p>
                    <p class="text-xs text-slate-300 mt-1">بمجرد كتابة الأرقام الأربعة سيتم تسجيل دخولك تلقائياً.</p>
                    @if(session('dev_otp_hint'))
                        <div class="mt-3 p-2.5 bg-amber-500/20 border border-amber-500/40 rounded-xl text-amber-300 text-xs">
                            <strong>كود التحقق المباشر (بيئة التجربة):</strong>
                            <span class="text-base font-bold underline font-mono mr-1">{{ session('dev_otp_hint') }}</span>
                        </div>
                    @endif
                </div>

                <form id="otpVerifyForm" action="{{ route('auth.verify_otp') }}" method="POST" class="space-y-5">
                    @csrf
                    <input type="hidden" name="formatted_phone" value="{{ session('formatted_phone') }}">
                    <input type="hidden" name="local_phone" value="{{ session('local_phone') }}">

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-2 text-center">أدخل كود التحقق (4 أرقام)</label>
                        <input type="tel" name="otp_code" id="otpCodeInput" maxlength="4" required autofocus autocomplete="one-time-code"
                               oninput="handleOtpInput(this)"
                               placeholder="----"
                               dir="ltr"
                               class="w-full text-center tracking-[1em] text-2xl font-bold bg-slate-800/90 border-2 border-emerald-500/60 text-white rounded-2xl py-3.5 focus:outline-none focus:border-emerald-400 focus:ring-4 focus:ring-emerald-500/20">
                    </div>

                    <div id="otpLoadingMsg" class="hidden text-center text-xs text-emerald-400 font-bold animate-pulse">
                        ⏳ جاري التحقق وتسجيل الدخول تلقائياً...
                    </div>

                    <button type="submit" id="otpSubmitBtn" class="w-full py-3.5 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-semibold rounded-2xl shadow-lg shadow-emerald-600/25 transition">
                        تأكيد الكود والدخول للنظام
                    </button>
                </form>

                <div class="mt-6 pt-5 border-t border-slate-800 text-center">
                    <button type="button" onclick="toggleMode('password')" class="text-xs text-amber-400 hover:text-amber-300 font-medium underline">
                        لم يصلك كود الواتساب؟ الدخول باسم المستخدم وكلمة المرور
                    </button>
                </div>
            </div>
        @endif

        <!-- الحالة 2: الدخول الأساسي برقم الجوال (10 أرقام فقط) -->
        <div id="phoneSection" class="{{ (session('otp_step') || session('password_mode')) ? 'hidden' : '' }}">
            <form action="{{ route('auth.send_otp') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="text-xs font-semibold text-slate-300">رقم الجوال (10 أرقام)</label>
                    </div>
                    <div class="relative">
                        <input type="tel" name="phone" id="phoneInput" maxlength="10" required autofocus
                               value="0500000000"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10); updateIntlPreview(this.value);"
                               placeholder="05XXXXXXXX"
                               dir="ltr"
                               class="w-full text-left font-mono text-lg tracking-wider bg-slate-800/80 border border-slate-700 text-white rounded-2xl px-4 py-3.5 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                    </div>
                    <p id="intlPreview" class="text-xs text-slate-400 mt-2">
                        سيتم إرسال كود تحقق 4 أرقام إلى: <span class="text-emerald-400 font-mono font-semibold" dir="ltr">+966500000000</span>
                    </p>
                </div>

                <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-semibold rounded-2xl shadow-lg shadow-emerald-600/25 transition flex items-center justify-center gap-2">
                    <span>استلام كود التحقق عبر واتساب</span>
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-slate-800 text-center">
                <button type="button" onclick="toggleMode('password')" class="text-xs text-slate-400 hover:text-white transition">
                    تواجه مشكلة في استقبال رسائل الواتساب؟ <span class="text-emerald-400 font-semibold underline">الدخول باسم المستخدم وكلمة المرور</span>
                </button>
            </div>
        </div>

        <!-- الحالة 3: الدخول الاحتياطي باسم المستخدم وكلمة المرور -->
        <div id="passwordSection" class="{{ session('password_mode') ? '' : 'hidden' }}">
            <div class="mb-4 p-3 rounded-2xl bg-slate-800/80 border border-slate-700 text-xs text-slate-300">
                الدخول الاحتياطي السريع (في حال تعطل الواتساب).<br>
                <span class="text-emerald-400">بيانات الأدمن الافتراضية:</span> اسم المستخدم <b>admin</b> | كلمة المرور <b>123456</b>
            </div>

            <form action="{{ route('auth.login_password') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">اسم المستخدم (أو رقم الجوال 10 أرقام)</label>
                    <input type="text" name="username" value="admin" required
                           class="w-full bg-slate-800 border border-slate-700 text-white rounded-2xl px-4 py-3 focus:outline-none focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">كلمة المرور</label>
                    <input type="password" name="password" value="123456" required
                           class="w-full bg-slate-800 border border-slate-700 text-white rounded-2xl px-4 py-3 focus:outline-none focus:border-emerald-500">
                </div>
                <button type="submit" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-2xl shadow-lg transition">
                    تسجيل الدخول المباشر
                </button>
            </form>

            <div class="mt-5 pt-4 border-t border-slate-800 text-center">
                <button type="button" onclick="toggleMode('phone')" class="text-xs text-slate-400 hover:text-emerald-400">
                    ← العودة للدخول عبر كود الواتساب (OTP)
                </button>
            </div>
        </div>

    </div>

    <script>
        // تركيز المؤشر فوراً على خانة كود التحقق عند ظهورها
        window.addEventListener('DOMContentLoaded', function() {
            const otpInput = document.getElementById('otpCodeInput');
            if (otpInput) {
                otpInput.focus();
                otpInput.select();
            }
        });

        // التحقق والدخول التلقائي فور كتابة 4 أرقام
        let otpSubmitted = false;
        function handleOtpInput(el) {
            el.value = el.value.replace(/[^0-9]/g, '').slice(0, 4);
            if (el.value.length === 4 && !otpSubmitted) {
                otpSubmitted = true;
                const loadingMsg = document.getElementById('otpLoadingMsg');
                const submitBtn = document.getElementById('otpSubmitBtn');
                if (loadingMsg) loadingMsg.classList.remove('hidden');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerText = 'جاري الدخول...';
                }
                el.readOnly = true;
                document.getElementById('otpVerifyForm').submit();
            }
        }

        function updateIntlPreview(val) {
            const preview = document.getElementById('intlPreview');
            if (val.length > 0) {
                let clean = val.startsWith('0') ? val.substring(1) : val;
                preview.innerHTML = 'سيتم إرسال كود الواتساب إلى: <span class="text-emerald-400 font-mono font-semibold" dir="ltr">+966' + clean + '</span>';
            } else {
                preview.innerHTML = 'أدخل 10 أرقام تبدأ بـ 05';
            }
        }

        function toggleMode(mode) {
            const phoneSec = document.getElementById('phoneSection');
            const passSec = document.getElementById('passwordSection');
            const otpSec = document.getElementById('otpSection');
            if (otpSec) otpSec.classList.add('hidden');

            if (mode === 'password') {
                phoneSec.classList.add('hidden');
                passSec.classList.remove('hidden');
            } else {
                passSec.classList.add('hidden');
                phoneSec.classList.remove('hidden');
            }
        }
    </script>
</body>
</html>