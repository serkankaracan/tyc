<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Attribute;
use App\Models\Munition;
use App\Models\MunitionAttribute;
use App\Models\Image;
use App\Models\Sku;
use App\Models\Variant;
use App\Models\VariantValue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Exception;

class MunitionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $munitions = Munition::all();
        return view('Backend.pages.munition', compact('munitions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $munitions = Munition::all();
        $attributes = Attribute::all();
        $variants = Variant::all();
        $variantValues = VariantValue::all();
        return view('Backend.pages.munition_add_edit', compact('categories', 'attributes', 'munitions', 'variants', 'variantValues'));
    }

    public function generateCombinations($variants, $index = 0, $combination = [], $result = [])
    {
        $variantName = array_keys($variants)[$index];
        $values = $variants[$variantName];
        foreach ($values as $value) {
            $combination[$variantName] = $value;
            if ($index < count($variants) - 1) {
                $result = $this->generateCombinations($variants, $index + 1, $combination, $result);
            } else {
                $result[] = $combination;
            }
        }
        return $result;
    }

    function generateSKU($combination)
    {
        $sku = '';
        foreach ($combination as $key => $value) {
            $sku .= $value[0]; // Her varyant değerinin ilk harfini SKU'ya ekle
        }
        return $sku;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /*
        $formData = $request->all();
        try {
            return response()->json(['success' => true, 'message' => $formData]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
        */
        // Gelen isteği doğrula
        $validatedData = $request->validate([
            'name' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'origin' => 'required|string',
            'summary' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'boolean',
            'target_type' => ['required', Rule::in(['SOFT', 'HARD'])], // target_type doğrulama kuralı
            //'score' => 'required|numeric|min:1|max:10',
            'imageInput*' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'croppedImage*' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'stock.*' => 'nullable|integer|min:0', // Kullanıcı tarafından girilen stok değerlerini doğrula
            'sku.*' => 'nullable|string|unique:skus,sku', // Kullanıcı tarafından girilen SKU'ları doğrula ve benzersiz olmasını sağla
        ]);

        // Slug oluştur
        $slug = Str::slug($validatedData['name']);
        $validatedData['slug'] = $slug;

        // Mühimmatı oluştur
        $munition = Munition::create($validatedData);

        // Özellik değerlerini kaydet
        $attributes = $request->input('attributes');
        if (!is_null($attributes)) {
            foreach ($attributes as $attributeId => $attributeValues) {
                $value = $attributeValues['value'] ?? null;
                $score = $attributeValues['score'] ?? 0;
                $min = $attributeValues['min'] ?? null;
                $max = $attributeValues['max'] ?? null;

                // Attribute'u iliştir
                $munition->attributes()->attach($attributeId, [
                    'value' => $value,
                    'score' => $score,
                    'min' => $min,
                    'max' => $max,
                ]);
            }
        } else {
            // attributes boş olduğunda yapılacak işlemler (varsa)
        }

        // resim kayıt işlemleri
        for ($i = 1; $i <= 6; $i++) {
            $imageInputName = 'imageInput' . $i;
            $croppedImageName = 'croppedImage' . $i;

            if ($request->has($imageInputName)) {
                // Get base64 image data
                $croppedImageData = $request->input($croppedImageName);

                $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $croppedImageData));

                // Base64 kodunu dosyaya yaz
                $tempImagePath = tempnam(sys_get_temp_dir(), 'cropped_image');
                file_put_contents($tempImagePath, $image);

                $imageData = $request->file($imageInputName);
                // Generate unique image name
                $imageName = Str::slug(pathinfo($imageData->getClientOriginalName(), PATHINFO_FILENAME), '_') . '_' . uniqid();
                $imageExtension = $imageData->getClientOriginalExtension();
                $fullImageName = $imageName . '.' . $imageExtension;

                //$imagePath = $imageData->storeAs('public/munition_images', $fullImageName);

                // Save image to storage
                $storagePath = Storage::putFileAs('public/munition_images', new File($tempImagePath), $fullImageName);

                // Dosyayı sildikten sonra
                unlink($tempImagePath);

                // Create and save image record in database
                $munitionImage = new Image();
                $munitionImage->url = 'munition_images/' . $fullImageName;
                $munitionImage->munition_id = $munition->id;
                $munitionImage->save();
            }
        }

        // SKU'ları oluştur ve kaydet
        /*
        $variants = $munition->variants;
        $variantValues = $munition->variantValues;
        $stock = $request->input('stock');
        $skus = $request->input('sku');

        foreach ($variants as $variant) {
            foreach ($variantValues as $value) {
                // SKU oluştur
                $sku = isset($skus[$variant->id][$value->id]) ? $skus[$variant->id][$value->id] : $this->generateSKU([$variant, $value]);

                // SKU'yu kaydet
                Sku::create([
                    'munition_variant_id' => $variant->id,
                    'stock' => isset($stock[$variant->id][$value->id]) ? $stock[$variant->id][$value->id] : 0, // Kullanıcı tarafından girilen stok değerini kullan
                    'price' => $validatedData['price'], // Kullanıcının girdiği fiyatı kullan
                    'sku' => $sku,
                    'status' => true, // Varsayılan olarak SKU aktif olabilir
                ]);
            }
        }
        */

        // Başarıyla tamamlandı mesajı ile birlikte index sayfasına yönlendir
        return redirect()->route('muhimmat.index')->with('success', $munition->name . ' veritabanına eklendi.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Munition $munition)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $categories = Category::all();
        $attributes = Attribute::all();
        $variants = Variant::all();
        $variantValues = VariantValue::all();
        $munition = Munition::findOrFail($id);

        return view('Backend.pages.munition_add_edit', compact('categories', 'attributes', 'munition', 'variants', 'variantValues'));
    }

    public function changeStatus($id)
    {
        $munition = Munition::findOrFail($id);
        $munition->update(['status' => !$munition->status]);
        return redirect()->route('muhimmat.index')->with('success', $munition->name . ' durumu değiştirildi.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $munition = Munition::findOrFail($id);

        // Gelen isteği doğrula
        $validatedData = $request->validate([
            'name' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'origin' => 'required|string',
            'summary' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'boolean',
            'target_type' => ['required', Rule::in(['SOFT', 'HARD'])], // target_type doğrulama kuralı
            //'score' => 'required|numeric|min:1|max:10',
            'imageInput*' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'croppedImage*' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'stock.*' => 'nullable|integer|min:0', // Kullanıcı tarafından girilen stok değerlerini doğrula
            'sku.*' => 'nullable|string|unique:skus,sku', // Kullanıcı tarafından girilen SKU'ları doğrula ve benzersiz olmasını sağla
        ]);

        // Slug oluştur
        $slug = Str::slug($validatedData['name']);
        $validatedData['slug'] = $slug;

        // Mühimmatı güncelle
        $munition->update($validatedData);

        // Attribute değerlerini güncelle
        $attributes = $request->input('attributes', []);
        foreach ($attributes as $attributeId => $attributeValues) {
            $pivotData = [];
            if (isset($attributeValues['value'])) {
                $pivotData['value'] = $attributeValues['value'];
            }
            if (isset($attributeValues['min']) && isset($attributeValues['max'])) {
                $pivotData['min'] = $attributeValues['min'];
                $pivotData['max'] = $attributeValues['max'];
            }
            if (isset($attributeValues['score'])) {
                $pivotData['score'] = $attributeValues['score'];
            }

            // Mevcut attribute varsa güncelle, yoksa ekle
            if (!empty($pivotData)) {
                $munition->attributes()->syncWithoutDetaching([$attributeId => $pivotData]);
            }
        }

        // Var olan resimleri sırayla güncelle veya ekle
        $munitionImages = $munition->images()->orderByDesc('id')->get();

        for ($i = 1; $i <= 6; $i++) {
            $imageInputName = 'imageInput' . $i;
            $croppedImageName = 'croppedImage' . $i;

            if ($request->has($imageInputName)) {
                $existingImage = $munitionImages->get($i - 1); // -1 çünkü indeksler 0'dan başlar

                if ($existingImage) {
                    Storage::delete('public/' . $existingImage->url);
                    $existingImage->delete();
                }

                if ($request->has($imageInputName)) {
                    // Get base64 image data
                    $croppedImageData = $request->input($croppedImageName);

                    $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $croppedImageData));

                    // Base64 kodunu dosyaya yaz
                    $tempImagePath = tempnam(sys_get_temp_dir(), 'cropped_image');
                    file_put_contents($tempImagePath, $image);

                    $imageData = $request->file($imageInputName);
                    // Generate unique image name
                    $imageName = Str::slug(pathinfo($imageData->getClientOriginalName(), PATHINFO_FILENAME), '_') . '_' . uniqid();
                    $imageExtension = $imageData->getClientOriginalExtension();
                    $fullImageName = $imageName . '.' . $imageExtension;

                    // Save image to storage
                    $storagePath = Storage::putFileAs('public/munition_images', new File($tempImagePath), $fullImageName);

                    unlink($tempImagePath);

                    // Create and save image record in database
                    $munitionImage = new Image();
                    $munitionImage->url = 'munition_images/' . $fullImageName;
                    $munitionImage->munition_id = $munition->id;
                    $munitionImage->save();
                }
            }
        }

        // Başarıyla tamamlandı mesajı ile birlikte index sayfasına yönlendir
        return redirect()->route('muhimmat.index')->with('success', $munition->name . ' veritabanında güncellendi.');
    }


    public function deleteImage($id)
    {
        // Resmi bul
        $image = Image::findOrFail($id);

        // Storage'den resmi sil
        Storage::delete('public/' . $image->url);

        // Veritabanından resmi sil
        $image->delete();

        // Başarılı yanıt döndür
        return response()->json(['message' => 'Resim başarıyla silindi.'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $munition = Munition::find($id);
        if (!$munition) {
            return redirect()->back()->with('error', ' muhimmat bulunamadı.');
        }

        $munitionImages = Image::where('munition_id', $id)->get();

        // Storage'dan dosyaları sil
        foreach ($munitionImages as $munitionImage) {
            Storage::delete('public/' . $munitionImage->url);
        }

        if ($munition->delete()) {
            return redirect()->route('muhimmat.index')->with('success', $munition->name . ' veritabanından silindi.');
        } else {
            return redirect()->route('muhimmat.index')->with('fail', $munition->name . ' veritabanından silinirken bir hata oluştu.');
        }
    }
}
