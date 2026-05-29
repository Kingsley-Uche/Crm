<?php

namespace App\Http\Controllers;

use App\Models\BrandModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class BrandController extends Controller
{
    public function index()
    {
        $brands = BrandModel::latest()->paginate(20);

        return view('layouts.brand.index', compact('brands'));
    }

    public function create()
    {
        return view('layouts.brand.create');
    }

    /**
     * STORE BRAND
     */
  public function store(Request $request)
{
    
    if (BrandModel::count() > 0) {
        return redirect()
            ->route('brand.index')
            ->with('error', 'Only one brand record is allowed for this application.');
    }

    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:204',
        'website_url' => 'nullable|url',
        'contact_email' => 'nullable|email',
        'contact_phone' => 'nullable|string|max:50',
        'address' => 'nullable|string',
        'slug' => 'nullable|string|unique:brand_models,slug',
        'meta_title' => 'nullable|string|max:255',
        'meta_description' => 'nullable|string',
        'meta_keywords' => 'nullable|string',
        'og_title' => 'nullable|string|max:255',
        'og_description' => 'nullable|string',
        'og_image' => 'nullable|string',
        'og_type' => 'nullable|string|max:100',
        'twitter_title' => 'nullable|string|max:255',
        'twitter_description' => 'nullable|string',
        'twitter_image' => 'nullable|string',
        'twitter_card' => 'nullable|string|max:100',
        'canonical_url' => 'nullable|url',
        'robots' => 'nullable|string|max:100',
        'is_indexed' => 'nullable|boolean',
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    $data = $request->except('logo');

    if ($request->hasFile('logo')) {

        $file = $request->file('logo');

        $size = getimagesize($file);
        if ($size[0] != 150 || $size[1] != 150) {
            return back()->withErrors(['logo' => 'Logo must be 150x150 pixels'])->withInput();
        }

        $path = $file->store('brands', 'public');
        $data['logo_url'] = 'storage/' . $path;
    }

    $brand = BrandModel::create($data);

    // 🔥 CLEAR CACHE AFTER CREATE
    cache()->forget('brand_details');
    session()->forget('brand_details');

    return redirect()->route('brand.index')
        ->with('success', 'Brand created successfully');
}
    /**
     * SHOW
     */
    public function show($id)
    {
        $brand = BrandModel::findOrFail($id);

        return view('layouts.brand.show', compact('brand'));
    }

    /**
     * EDIT
     */
    public function edit($id)
    {
        $brand = BrandModel::findOrFail($id);

        return view('layouts.brand.edit', compact('brand'));
    }

    /**
     * UPDATE
     */
    public function update(Request $request, $id)
    {
        $brand = BrandModel::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',

            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:204',

            'website_url' => 'nullable|url',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',

            'slug' => 'nullable|string|unique:brand_models,slug,' . $brand->id,
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',

            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string',
            'og_image' => 'nullable|string',
            'og_type' => 'nullable|string|max:100',

            'twitter_title' => 'nullable|string|max:255',
            'twitter_description' => 'nullable|string',
            'twitter_image' => 'nullable|string',
            'twitter_card' => 'nullable|string|max:100',

            'canonical_url' => 'nullable|url',
            'robots' => 'nullable|string|max:100',

            'is_indexed' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->except('logo');

        // 🔥 UPDATE LOGO
        if ($request->hasFile('logo')) {

            $file = $request->file('logo');

            $size = getimagesize($file);
            $width = $size[0];
            $height = $size[1];

            if ($width != 150 || $height != 150) {
                return back()
                    ->withErrors(['logo' => 'Logo must be exactly 150x150 pixels'])
                    ->withInput();
            }

            // delete old file
            if ($brand->logo_url && File::exists(public_path($brand->logo_url))) {
                File::delete(public_path($brand->logo_url));
            }

            $path = $file->store('brands', 'public');

            $data['logo_url'] = 'storage/' . $path;
        }

        $brand->update($data);
        $brand->update($data);

// 🔥 CLEAR CACHE AFTER UPDATE
cache()->forget('brand_details');
session()->forget('brand_details');

return redirect()
    ->route('brand.index')
    ->with('success', 'Brand updated successfully');

       
    }

    /**
     * DELETE
     */
    public function destroy($id)
    {
        $brand = BrandModel::findOrFail($id);

        if ($brand->logo_url && File::exists(public_path($brand->logo_url))) {
            File::delete(public_path($brand->logo_url));
        }

        $brand->delete();
        $brand->delete();

// 🔥 CLEAR CACHE AFTER DELETE
cache()->forget('brand_details');
session()->forget('brand_details');

return redirect()
    ->route('brand.index')
    ->with('success', 'Brand deleted successfully');

    }
}