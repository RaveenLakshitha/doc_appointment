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
        if (!app()->runningInConsole() && !app()->runningUnitTests()) {
            try {
                $setting = cache('settings')
                    ?? cache()->remember('settings', now()->addHour(), fn() => Setting::first() ?? new Setting());
            } catch (\Throwable $e) {
                $setting = new Setting();
            }
        } else {
            $setting = new Setting();
        }

        View::share([
            'clinic_name' => $setting->clinic_name ?? config('app.name'),
            'clinic_address' => $setting->address ?? '123 Healthcare Avenue, Colombo 05, Sri Lanka',
            'clinic_email' => $setting->email ?? 'info@yourclinic.lk',
            'clinic_phone' => $setting->phone ?? '+1 11 234 5678',
            'clinic_logo' => !empty($setting->logo_path)
                ? asset('storage/' . $setting->logo_path)
                : null,
            'primary_color' => $setting->primary_color ?? '#1e40af',
            'currency_code' => $setting->currency ?? 'USD',
        ]);
    }
}