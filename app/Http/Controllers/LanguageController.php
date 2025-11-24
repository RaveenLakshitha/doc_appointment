<?php
// app/Http/Controllers/LanguageController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LanguageController extends Controller
{
    public function switch(Request $request)
    {
        $locale = $request->input('locale');

        if (in_array($locale, ['en', 'es'])) {
            // Session is available here
            $request->session()->put('locale', $locale);

            Log::info('Language changed', [
                'user_id' => auth()->id() ?? 'guest',
                'to'      => $locale,
            ]);
        }

        return redirect()->back();
    }
}