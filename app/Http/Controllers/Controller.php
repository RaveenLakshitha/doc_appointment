<?php
// app/Http/Controllers/Controller.php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
         $setting = Setting::first();
         view()->share('clinic_name', $setting?->clinic_name ?? config('app.name'));
         view()->share('clinic_logo', $setting?->logo_path ? Storage::url($setting->logo_path) : null);
         view()->share('primary_color', $setting?->primary_color ?? '#1e40af');
    }
}