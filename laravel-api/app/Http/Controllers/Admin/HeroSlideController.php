<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\Request;

class HeroSlideController extends Controller
{
    public function index()
    {
        $slides = HeroSlide::orderBy('sort_order')->get();
        return view('admin.hero.index', compact('slides'));
    }

    public function create()
    {
        return view('admin.hero.form');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('hero', 'public');
        }

        $data['is_active']  = $request->boolean('is_active');

        HeroSlide::create($data);

        return redirect()->route('admin.hero.index')->with('success', 'Slide créé.');
    }

    public function edit(HeroSlide $hero)
    {
        return view('admin.hero.form', ['slide' => $hero]);
    }

    public function update(Request $request, HeroSlide $hero)
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('hero', 'public');
        }

        $data['is_active'] = $request->boolean('is_active');

        $hero->update($data);

        return redirect()->route('admin.hero.index')->with('success', 'Slide mis à jour.');
    }

    public function destroy(HeroSlide $hero)
    {
        $hero->delete();
        return redirect()->route('admin.hero.index')->with('success', 'Slide supprimé.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title'         => 'required|string|max:255',
            'subtitle'      => 'nullable|string|max:255',
            'btn_label'     => 'nullable|string|max:80',
            'btn_url'       => 'nullable|string|max:255',
            'image'         => 'nullable|image|max:4096',
            'overlay_color' => 'nullable|string|max:80',
            'sort_order'    => 'nullable|integer|min:0',
        ]);
    }
}
