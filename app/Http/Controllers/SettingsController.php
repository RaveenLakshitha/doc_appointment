<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:settings.index', ['only' => ['general']]);
        $this->middleware('permission:settings.edit', ['only' => ['edit', 'update']]);
    }
    public function general(): View
    {
        $setting = Setting::firstOrCreate([], [
            'clinic_name' => config('app.name', 'Clinic Name'),
            'primary_color' => '#1e40af',
            'currency' => 'USD',
            'logo_path' => null,
        ]);

        return view('settings.general', compact('setting'));
    }

    public function edit(): View
    {
        return $this->general();
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        try {
            $setting = Setting::firstOrCreate([]);

            $validated = $request->validated();

            if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
                if ($setting->logo_path && Storage::disk('public')->exists($setting->logo_path)) {
                    Storage::disk('public')->delete($setting->logo_path);
                }

                $validated['logo_path'] = $request->file('logo')->store('logos', 'public');
            }

            $setting->update($validated);

            $this->clearSettingsCache();

            Cache::put('settings', $setting->fresh(), now()->addHour());

            return redirect()
                ->route('settings.general')
                ->with('success', __('file.settings_updated_successfully'));

        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error while updating settings', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Database error occurred while saving settings.');

        } catch (\Exception $e) {
            Log::error('Unexpected error updating clinic settings', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $request->file('logo') ? $request->file('logo')->getClientOriginalName() : null,
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update settings. Please try again.');
        }
    }

    protected function clearSettingsCache(): void
    {
        Cache::forget('settings');
        Cache::forget('app_settings');
        Cache::forget('clinic_settings');
    }
}