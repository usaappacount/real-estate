<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

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