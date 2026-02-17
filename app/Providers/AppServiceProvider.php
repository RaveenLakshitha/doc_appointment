<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use App\Models\Setting;

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
        $setting = cache('settings')
            ?? cache()->remember('settings', now()->addHour(), fn() => Setting::first() ?? (object)[]);

        View::share([
            'clinic_name'   => $setting?->clinic_name ?? config('app.name'),
            'clinic_logo'   => $setting?->logo_path
                ? Storage::url($setting->logo_path)     // ← Recommended: reliable on cPanel / no-symlink setups
                : null,
            'primary_color' => $setting?->primary_color ?? '#1e40af',
            'currency_code' => $setting?->currency      ?? 'USD',
        ]);
    }
}
