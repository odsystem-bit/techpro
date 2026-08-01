<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name'              => 'required|string|max:100',
            'site_tagline'           => 'nullable|string|max:200',
            'site_description'       => 'nullable|string|max:500',
            'contact_email'          => 'nullable|email',
            'whatsapp_number'        => 'nullable|string|max:25',
            'systemeio_form_id'      => 'nullable|string|max:100',
            'systemeio_embed'        => 'nullable|string',
            'footer_text'            => 'nullable|string|max:300',
            'logo'                   => 'nullable|image|max:2048',
            'favicon'                => 'nullable|image|max:512',
            'moneroo_api_key'        => 'nullable|string|max:200',
            'moneroo_webhook_secret' => 'nullable|string|max:200',
            'moneroo_base_url'       => 'nullable|url|max:200',
            'admin_email'            => 'nullable|email|max:100',
            'admin_password'         => 'nullable|string|min:8|max:100',
            'odibot_apk'             => 'nullable|file|max:204800',
            'odibot_version'         => 'nullable|string|max:50',
            'odibot_description'     => 'nullable|string|max:1000',
            'meta_pixel_id'          => 'nullable|string|max:50',
            'facebook_url'           => 'nullable|url|max:255',
            'instagram_url'          => 'nullable|url|max:255',
            'twitter_url'            => 'nullable|url|max:255',
            'linkedin_url'           => 'nullable|url|max:255',
            'youtube_url'            => 'nullable|url|max:255',
        ]);

        $fields = [
            'site_name', 'site_tagline', 'site_description',
            'contact_email', 'whatsapp_number',
            'systemeio_form_id', 'systemeio_embed', 'footer_text',
            'moneroo_api_key', 'moneroo_webhook_secret', 'moneroo_base_url',
            'admin_email', 'meta_pixel_id',
            'facebook_url', 'instagram_url', 'twitter_url', 'linkedin_url', 'youtube_url',
        ];

        foreach ($fields as $field) {
            if ($request->filled($field) || in_array($field, ['site_name'])) {
                SiteSetting::set($field, $request->input($field));
            }
        }

        if ($request->filled('admin_password')) {
            SiteSetting::set('admin_password', $request->input('admin_password'));
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('site', 'public');
            SiteSetting::set('logo', $path);
        }

        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('site', 'public');
            SiteSetting::set('favicon', $path);
        }

        if ($request->hasFile('odibot_apk')) {
            if ($settings['odibot_apk_path'] ?? null) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($settings['odibot_apk_path']);
            }
            $path = $request->file('odibot_apk')->store('odibot', 'public');
            SiteSetting::set('odibot_apk_path', $path);
            SiteSetting::set('odibot_apk_size', $request->file('odibot_apk')->getSize());
        }

        if ($request->filled('odibot_version')) {
            SiteSetting::set('odibot_version', $request->input('odibot_version'));
        }
        if ($request->filled('odibot_description')) {
            SiteSetting::set('odibot_description', $request->input('odibot_description'));
        }

        return back()->with('success', 'Paramètres enregistrés avec succès.');
    }
}
