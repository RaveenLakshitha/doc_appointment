<?php
// app/Http/Controllers/LanguageController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;

class LanguageController extends Controller
{
    public function switch (Request $request)
    {
        $locale = $request->input('locale');

        if (in_array($locale, ['en', 'es'])) {
            // Session is available here
            $request->session()->put('locale', $locale);
            Cookie::queue('locale', $locale, 60 * 24 * 365);
        }

        return redirect()->back();
    }
}