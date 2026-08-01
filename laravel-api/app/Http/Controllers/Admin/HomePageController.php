<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class HomePageController extends Controller
{
    public function edit()
    {
        $settings = SiteSetting::pluck('value', 'key');
        return view('admin.home.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'hero_title'       => 'required|string|max:255',
            'hero_description' => 'nullable|string|max:500',
            'hero_btn_label'   => 'nullable|string|max:80',
            'hero_btn_url'     => 'nullable|string|max:255',
            'hero_image'       => 'nullable|image|max:5120',
        ]);

        SiteSetting::set('hero_title',       $request->input('hero_title'));
        SiteSetting::set('hero_description', $request->input('hero_description'));
        SiteSetting::set('hero_btn_label',   $request->input('hero_btn_label'));
        SiteSetting::set('hero_btn_url',     $request->input('hero_btn_url'));

        if ($request->hasFile('hero_image')) {
            $path = $request->file('hero_image')->store('hero', 'public');
            SiteSetting::set('hero_image', $path);
        }

        if ($request->boolean('hero_image_remove')) {
            SiteSetting::set('hero_image', null);
        }

        return back()->with('success', 'Page d\'accueil mise à jour.');
    }
}
