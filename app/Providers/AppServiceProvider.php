<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // تفعيل HTTPS فقط على السيرفر السحابي (Render) واستثناء الجهاز المحلي (127.0.0.1 / localhost) حتى لا تعلق الروابط
        $host = request()->getHost();
        $isLocal = in_array($host, ['127.0.0.1', 'localhost', '::1']);

        if (!$isLocal && (request()->header('x-forwarded-proto') === 'https' || str_contains($host, 'onrender.com'))) {
            URL::forceScheme('https');
            request()->server->set('HTTPS', 'on');
        }

        // التأكد التلقائي من وجود ملف قاعدة بيانات SQLite وإنشائه وتشغيل الجداول على Render
        if (config('database.default') === 'sqlite') {
            $dbPath = config('database.connections.sqlite.database', database_path('database.sqlite'));

            if ($dbPath && $dbPath !== ':memory:') {
                $dir = dirname($dbPath);
                if (!is_dir($dir)) {
                    @mkdir($dir, 0775, true);
                }

                if (!file_exists($dbPath)) {
                    @touch($dbPath);
                    @chmod($dbPath, 0664);
                }

                // إذا كانت الجداول الأساسية (مثل sessions أو users) غير موجودة بعد، قم بتشغيل المايقريشن تلقائياً
                try {
                    if (!Schema::hasTable('sessions') || !Schema::hasTable('users')) {
                        Artisan::call('migrate', ['--force' => true]);
                    }
                } catch (\Throwable $e) {
                    // تجاهل أي خطأ عابر أثناء التهيئة الأولية
                }
            }
        }
    }
}