<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    public function edit()
    {
        return view('admin.settings', ['groups' => config('portal.settings')]);
    }

    public function update(Request $request)
    {
        $fields = collect(config('portal.settings'))->flatten(1);

        $rules = [];
        foreach ($fields as $f) {
            $rules[$f['name']] = match ($f['type']) {
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
                'favicon' => 'nullable|file|mimetypes:image/png,image/x-icon,image/vnd.microsoft.icon|max:1024',
                'maps_embed' => ['nullable', 'string', 'max:5000', 'url:https', 'regex:~\Ahttps://www\.google\.com/maps/embed(?:/v1/[a-z]+)?\?.+\z~i'],
                'select' => ['nullable', 'string', Rule::in(array_keys($f['options'] ?? []))],
                default => 'nullable|string|max:1000',
            };
        }
        $request->validate($rules, [
            'google_maps_embed_url.regex' => 'Masukkan URL sematan Google Maps yang diawali https://www.google.com/maps/embed.',
        ]);

        $changed = [];
        foreach ($fields as $f) {
            $name = $f['name'];
            $isImage = in_array($f['type'], ['image', 'favicon'], true);

            if ($isImage) {
                if ($request->hasFile($name) || $request->boolean("remove_$name")) {
                    $changed[] = $name;
                }
                if ($request->hasFile($name)) {
                    $old = Setting::get($name);
                    $path = $request->file($name)->store('uploads/settings', 'public');
                    if ($old) {
                        Storage::disk('public')->delete($old);
                    }
                    Setting::put($name, $path);
                } elseif ($request->boolean("remove_$name")) {
                    if ($old = Setting::get($name)) {
                        Storage::disk('public')->delete($old);
                    }
                    Setting::put($name, null);
                }

                continue;
            }

            if ((string) Setting::get($name, '') !== (string) $request->input($name, '')) {
                $changed[] = $name;
            }
            Setting::put($name, $request->input($name));
        }

        ActivityLog::record('settings_updated', 'Memperbarui pengaturan situs', null, ['kolom' => $changed]);

        return back()->with('ok', 'Pengaturan situs disimpan.');
    }
}
