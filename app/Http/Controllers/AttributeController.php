<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attributes = Attribute::all();
        return view('Backend.pages.attribute', compact('attributes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $options = DB::select("SHOW COLUMNS FROM attributes WHERE Field = 'option'");

        // Enum seçeneklerini işle
        $enumValues = [];
        preg_match('/^enum\((.*)\)$/', $options[0]->Type, $matches);
        $enumValues = explode(',', str_replace("'", "", $matches[1]));

        return view('Backend.pages.attribute_add_edit', compact('enumValues'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Veritabanından enum seçeneklerini çek
        $options = DB::select("SHOW COLUMNS FROM attributes WHERE Field = 'option'");

        // Enum seçeneklerini işle
        $enumValues = [];
        preg_match('/^enum\((.*)\)$/', $options[0]->Type, $matches);
        $enumValues = explode(',', str_replace("'", "", $matches[1]));

        // Gelen isteği doğrula
        $validatedData = $request->validate([
            'name' => 'required|string|unique:attributes,name',
            'option' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($enumValues) {
                    if (!in_array($value, $enumValues)) {
                        $fail($attribute . ' geçersiz bir seçenek.');
                    }
                }
            ],
            'description' => 'nullable|string',
            'status' => 'boolean'
        ]);

        // Slug oluştur
        $slug = Str::slug($validatedData['name']);
        $validatedData['slug'] = Str::slug($slug);

        // Yeni özelliği oluştur
        $attribute = Attribute::create($validatedData);

        if ($attribute) {
            return redirect()->route('ozellik.index')->with('success', $attribute->name . ' başarıyla veritabanına eklendi.');
        } else {
            return redirect()->route('ozellik.index')->with('fail', $attribute->name . ' özelliği eklenirken bir hata oluştu.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Attribute $attribute)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $attribute = Attribute::findOrFail($id);

        // Veritabanından enum seçeneklerini çek
        $options = DB::select("SHOW COLUMNS FROM attributes WHERE Field = 'option'");

        // Enum seçeneklerini işle
        $enumValues = [];
        preg_match('/^enum\((.*)\)$/', $options[0]->Type, $matches);
        $enumValues = explode(',', str_replace("'", "", $matches[1]));

        return view('Backend.pages.attribute_add_edit', compact('attribute', 'enumValues'));
    }

    public function changeStatus($id)
    {
        $attribute = Attribute::findOrFail($id);
        $attribute->update(['status' => !$attribute->status]);
        return redirect()->route('ozellik.index')->with('success', $attribute->name . ' durumu başarıyla değiştirildi.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Veritabanından enum seçeneklerini çek
        $options = DB::select("SHOW COLUMNS FROM attributes WHERE Field = 'option'");

        // Enum seçeneklerini işle
        $enumValues = [];
        preg_match('/^enum\((.*)\)$/', $options[0]->Type, $matches);
        $enumValues = explode(',', str_replace("'", "", $matches[1]));

        // Gelen isteği doğrula
        $validatedData = $request->validate([
            'name' => 'required|string|unique:attributes,name,' . $id,
            'option' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($enumValues) {
                    if (!in_array($value, $enumValues)) {
                        $fail($attribute . ' geçersiz bir seçenek.');
                    }
                }
            ],
            'description' => 'nullable|string',
            'status' => 'boolean'
        ]);

        // Slug oluştur
        $slug = Str::slug($validatedData['name']);
        $validatedData['slug'] = $slug;

        // Özelliği güncelle
        $attribute = Attribute::findOrFail($id);

        if ($attribute->update($validatedData)) {
            return redirect()->route('ozellik.index')->with('success', $attribute->name . ' başarıyla güncellendi.');
        } else {
            return redirect()->route('ozellik.index')->with('fail', $attribute->name . ' özelliği güncellenirken bir hata oluştu.');
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $attribute = Attribute::find($id);
        if (!$attribute) {
            return redirect()->back()->with('error', 'Özellik bulunamadı.');
        }

        if ($attribute->delete()) {
            return redirect()->route('ozellik.index')->with('success', $attribute->name . ' başarıyla özelliklerden silindi.');
        } else {
            return redirect()->route('ozellik.index')->with('fail', $attribute->name . ' özelliği silinirken bir hata oluştu.');
        }
    }
}
