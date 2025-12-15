<?php
// app/Http/Controllers/SettingsController.php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    // This is the method most admin templates expect
    public function general(): View
    {
        $setting = Setting::firstOrFail();
        return view('settings.general', compact('setting'));
        // or: return view('settings.edit', compact('setting'));
    }

    // Alternative/standard method
    public function edit(): View
    {
        return $this->general();
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $setting = Setting::firstOrFail();

        $data = $request->validated();

        // Operating Hours
        $data['operating_hours'] = [
            'weekdays' => [$request->weekday_open, $request->weekday_close],
            'weekends' => [
                $request->weekend_open ?? 'closed',
                $request->weekend_close ?? 'closed'
            ],
        ];

        // Logo Upload
        if ($request->hasFile('logo')) {
            if ($setting->logo_path) {
                Storage::disk('public')->delete($setting->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        // Favicon Upload
        if ($request->hasFile('favicon')) {
            if ($setting->favicon_path) {
                Storage::disk('public')->delete($setting->favicon_path);
            }
            $data['favicon_path'] = $request->file('favicon')->store('favicons', 'public');
        }

        $setting->update($data);

        return redirect()
            ->route('settings.general') // or 'settings.edit'
            ->with('success', 'Clinic settings updated successfully!');
    }
}