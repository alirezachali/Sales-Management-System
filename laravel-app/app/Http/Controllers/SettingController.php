<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;


class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key');

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'store_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'store_favicon' => 'nullable|mimes:ico,png|max:512',
        ]);

        // همه تنظیمات غیر از فایل‌ها
        $exclude = [
            '_token',
            'store_logo',
            'store_favicon',
        ];

        $data = $request->except($exclude);

        foreach ($data as $key => $value) {

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );

        }

        // لوگو
        if ($request->hasFile('store_logo')) {

            $oldLogo = Setting::where('key', 'store_logo')->value('value');

            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            $extension = $request->file('store_logo')->extension();

            $path = $request->file('store_logo')->storeAs(
                'settings',
                'logo.' . $extension,
                'public'
            );

            Setting::updateOrCreate(
                ['key' => 'store_logo'],
                ['value' => $path]
            );
        }

        // فاوآیکن
        if ($request->hasFile('store_favicon')) {

            $oldFavicon = Setting::where('key', 'store_favicon')->value('value');

            if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                Storage::disk('public')->delete($oldFavicon);
            }

            $extension = $request->file('store_favicon')->extension();

            $path = $request->file('store_favicon')->storeAs(
                'settings',
                'favicon.' . $extension,
                'public'
            );

            Setting::updateOrCreate(
                ['key' => 'store_favicon'],
                ['value' => $path]
            );
        }

        return redirect()->route('settings.index', ['tab' => $request->active_tab ?: 'store'])
            ->with('success', 'تنظیمات با موفقیت ذخیره شد.');
        }
}
