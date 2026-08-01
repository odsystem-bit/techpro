<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::orderBy('sort_order')->get();
        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        return view('admin.testimonials.form');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data = $this->handleUploads($request, $data);
        $data['is_active'] = $request->boolean('is_active');

        Testimonial::create($data);

        return redirect()->route('admin.testimonials.index')->with('success', 'Témoignage ajouté.');
    }

    public function edit(Testimonial $testimonial)
    {
        return view('admin.testimonials.form', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $data = $this->validated($request);
        $data = $this->handleUploads($request, $data, $testimonial);
        $data['is_active'] = $request->boolean('is_active');

        $testimonial->update($data);

        return redirect()->route('admin.testimonials.index')->with('success', 'Témoignage mis à jour.');
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();
        return redirect()->route('admin.testimonials.index')->with('success', 'Témoignage supprimé.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'author_name'   => 'required|string|max:100',
            'author_title'  => 'nullable|string|max:100',
            'content'       => 'required|string',
            'rating'        => 'required|integer|min:1|max:5',
            'sort_order'    => 'nullable|integer|min:0',
            'author_avatar' => 'nullable|image|max:1024',
            'screenshot'    => 'nullable|image|max:4096',
        ]);
    }

    private function handleUploads(Request $request, array $data, ?Testimonial $model = null): array
    {
        if ($request->hasFile('author_avatar')) {
            $data['author_avatar'] = $request->file('author_avatar')->store('testimonials/avatars', 'public');
        } elseif ($model) {
            $data['author_avatar'] = $model->author_avatar;
        }

        if ($request->hasFile('screenshot')) {
            $data['screenshot'] = $request->file('screenshot')->store('testimonials/screenshots', 'public');
        } elseif ($model) {
            $data['screenshot'] = $model->screenshot;
        }

        return $data;
    }
}
