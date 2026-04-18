<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class UserPreferencesController extends Controller
{
    /**
     * Display the user preferences page.
     */
    public function index(): View
    {
        $user = Auth::user();
        $barcodeDefaults = $user->getBarcodeDefaults();
        $barcodePresets = config('barcode.presets');
        $hasCustomDefaults = $user->hasCustomBarcodeDefaults();

        return view('preferences.index', compact('barcodeDefaults', 'barcodePresets', 'hasCustomDefaults'));
    }

    /**
     * Update user's default barcode settings.
     */
    public function updateBarcodeDefaults(Request $request): RedirectResponse
    {
        $request->validate([
            'x_percent' => 'required|numeric|min:0|max:100',
            'y_percent' => 'required|numeric|min:0|max:100',
            'width_percent' => 'required|numeric|min:5|max:100',
            'height_percent' => 'required|numeric|min:2|max:50',
            'show_text' => 'boolean',
            'page' => 'nullable|integer|min:0',
        ]);

        $settings = [
            'x_percent' => (float) $request->x_percent,
            'y_percent' => (float) $request->y_percent,
            'width_percent' => (float) $request->width_percent,
            'height_percent' => (float) $request->height_percent,
            'show_text' => $request->boolean('show_text', true),
            'page' => (int) ($request->page ?? 1),
        ];

        Auth::user()->setBarcodeDefaults($settings);

        return redirect()->back()->with('success', 'Default barcode settings saved successfully.');
    }

    /**
     * Update user's default barcode settings via AJAX.
     */
    public function updateBarcodeDefaultsAjax(Request $request): JsonResponse
    {
        $request->validate([
            'x_percent' => 'required|numeric|min:0|max:100',
            'y_percent' => 'required|numeric|min:0|max:100',
            'width_percent' => 'required|numeric|min:5|max:100',
            'height_percent' => 'required|numeric|min:2|max:50',
            'show_text' => 'boolean',
            'page' => 'nullable|integer|min:0',
        ]);

        $settings = [
            'x_percent' => (float) $request->x_percent,
            'y_percent' => (float) $request->y_percent,
            'width_percent' => (float) $request->width_percent,
            'height_percent' => (float) $request->height_percent,
            'show_text' => $request->boolean('show_text', true),
            'page' => (int) ($request->page ?? 1),
        ];

        Auth::user()->setBarcodeDefaults($settings);

        return response()->json([
            'success' => true,
            'message' => 'Default barcode settings saved successfully.',
            'settings' => $settings,
        ]);
    }

    /**
     * Clear user's custom barcode defaults, reverting to system defaults.
     */
    public function clearBarcodeDefaults(): RedirectResponse
    {
        Auth::user()->clearBarcodeDefaults();

        return redirect()->back()->with('success', 'Custom barcode settings cleared. Using system defaults.');
    }

    /**
     * Get user's barcode defaults as JSON (for AJAX requests).
     */
    public function getBarcodeDefaults(): JsonResponse
    {
        $user = Auth::user();
        
        return response()->json([
            'defaults' => $user->getBarcodeDefaults(),
            'has_custom' => $user->hasCustomBarcodeDefaults(),
            'presets' => config('barcode.presets'),
        ]);
    }

    /**
     * Apply a preset to user's defaults.
     */
    public function applyPreset(Request $request): RedirectResponse
    {
        $request->validate([
            'preset' => 'required|string',
        ]);

        $presets = config('barcode.presets');
        $presetKey = $request->preset;

        if (!isset($presets[$presetKey])) {
            return redirect()->back()->with('error', 'Invalid preset selected.');
        }

        $preset = $presets[$presetKey];
        $settings = [
            'x_percent' => $preset['x_percent'],
            'y_percent' => $preset['y_percent'],
            'width_percent' => $preset['width_percent'],
            'height_percent' => $preset['height_percent'],
            'show_text' => true,
            'page' => 1,
        ];

        Auth::user()->setBarcodeDefaults($settings);

        return redirect()->back()->with('success', "Preset '{$preset['name']}' applied successfully.");
    }

    /**
     * Apply a preset via AJAX.
     */
    public function applyPresetAjax(Request $request): JsonResponse
    {
        $request->validate([
            'preset' => 'required|string',
        ]);

        $presets = config('barcode.presets');
        $presetKey = $request->preset;

        if (!isset($presets[$presetKey])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid preset selected.',
            ], 400);
        }

        $preset = $presets[$presetKey];
        $settings = [
            'x_percent' => $preset['x_percent'],
            'y_percent' => $preset['y_percent'],
            'width_percent' => $preset['width_percent'],
            'height_percent' => $preset['height_percent'],
            'show_text' => true,
            'page' => 1,
        ];

        Auth::user()->setBarcodeDefaults($settings);

        return response()->json([
            'success' => true,
            'message' => "Preset '{$preset['name']}' applied successfully.",
            'settings' => $settings,
        ]);
    }
}
