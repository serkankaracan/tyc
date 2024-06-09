<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Munition;
use App\Models\Post;
use App\Models\MunitionAttribute;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Attribute;
use App\Models\Image;

class FrontendController extends Controller
{
    public function index()
    {
        $munitions = Munition::paginate(12);
        $categories = Category::all();
        $tags = Tag::all();

        return view('Frontend.pages.home', compact(
            'munitions',
            'categories',
            'tags'
        ));
    }

    public function kiyasla(Request $request)
    {
        $categories = Category::all();
        $attributes = Attribute::all();
        $tags = Tag::all();

        $targetType = $request->input('target_type');
        $minRange = $request->input('min');
        $maxRange = $request->input('max');
        $categoryId = $request->input('category_id');

        // Menzil adında bir özellik var mı kontrol et
        $attribute = Attribute::where('name', 'like', '%menzil%')->first();

        // Menzil özelliği varsa ve min veya max değerleri boş değilse, bu özelliği kullanarak filtreleme yap
        if ($attribute && (!empty($minRange) || !empty($maxRange))) {
            $query = MunitionAttribute::where('attribute_id', $attribute->id);

            // Min ve max değerlerinin kontrolü
            if (!empty($minRange) && !empty($maxRange)) {
                $query->whereBetween('value', [$minRange, $maxRange]);
            } elseif (!empty($minRange)) {
                $query->where('value', '>=', $minRange);
            } elseif (!empty($maxRange)) {
                $query->where('value', '<=', $maxRange);
            }

            $munitionIds = $query->pluck('munition_id');

            $munitions = Munition::whereIn('id', $munitionIds)
                ->when($targetType, function ($query) use ($targetType) {
                    return $query->where('target_type', $targetType);
                })
                ->when($categoryId, function ($query) use ($categoryId) {
                    return $query->where('category_id', $categoryId);
                })
                ->get();
        } else {
            // Menzil özelliği yoksa veya min ve max değerleri boşsa sadece target_type ve category_id üzerinden sorgu yap
            $munitions = Munition::when($targetType, function ($query) use ($targetType) {
                return $query->where('target_type', $targetType);
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->get();
        }

        return view('Frontend.pages.munition_compare', compact(
            'munitions',
            'categories',
            'tags',
            'attributes'
        ));
    }

    public function blog()
    {
        $posts = Post::where('status', 1)->paginate(6);
        $categories = Category::all();
        $tags = Tag::all();

        return view('Frontend.pages.blog', compact(
            'posts',
            'categories',
            'tags'
        ));
    }

    public function blogDetail($slug)
    {
        $post = Post::where('slug', $slug)->first();

        if (!$post) {
            abort(404);
        }

        $categories = Category::all();
        $tags = Tag::all();

        return view('Frontend.pages.blog_detail', compact(
            'post',
            'categories',
            'tags'
        ));
    }

    public function about()
    {
        $munitions = Munition::paginate(9);
        $categories = Category::all();

        return view('Frontend.pages.about', compact(
            'munitions',
            'categories'
        ));
    }

    public function contact()
    {
        $munitions = Munition::paginate(9);
        $categories = Category::all();

        return view('Frontend.pages.contact', compact(
            'munitions',
            'categories'
        ));
    }

    public function show($id)
    {
        $munition = Munition::findOrFail($id);

        return view('Frontend.pages.home', compact(
            'munitions',
            'categories'
        ));
    }

    public function ShowMunitionDetail($slug)
    {
        $categories = Category::all();
        $tags = Tag::all();

        $munition = Munition::where('slug', $slug)->first();

        if (!$munition) {
            abort(404);
        }

        $munitionImages = Image::where('munition_id', $munition->id)->get();

        return view('Frontend.pages.munition_detail', compact('munition', 'munitionImages', 'categories', 'tags'));
    }

    public function getMunitionsByCategory($categoryId)
    {
        $munitions = collect(); // Mühimmatları toplamak için bir koleksiyon oluştur

        // Verilen kategori ID'sine ait tüm mühimmatları al
        $munitions = Munition::where('category_id', $categoryId)->get();

        // Verilen kategoriye ait alt kategorileri bul
        $subCategories = Category::where('parent_id', $categoryId)->get();

        // Her bir alt kategori için mühimmatları almak üzere özyinelemeli olarak bu işlemi tekrarla
        foreach ($subCategories as $subCategory) {
            $munitions = $munitions->merge($this->getMunitionsByCategory($subCategory->id));
        }

        return $munitions;
    }

    public function FilterByCategory($slug)
    {
        // Kategoriyi bul
        $category = Category::where('slug', $slug)->firstOrFail();

        // Kategoriye ait mühimmatları ve alt kategorileri çek
        $munitions = $this->getMunitionsByCategory($category->id);
        // Mühimmatları sayfalandır
        $perPage = 10; // Her sayfada gösterilecek mühimmat sayısı
        $currentPage = request()->query('page', 1); // Geçerli sayfa numarası
        $pagedData = $munitions->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $munitions = new \Illuminate\Pagination\LengthAwarePaginator($pagedData, count($munitions), $perPage);

        // Mühimmatların resimlerini çekmek için boş bir dizi oluştur
        $munitionImages = [];

        // Her mühimmat için resimleri çek
        foreach ($munitions as $munition) {
            $images = Image::where('munition_id', $munition->id)->get();
            $munitionImages[$munition->id] = $images;
        }

        $categories = Category::all();
        $tags = Tag::all();

        // Sonucu görünüme aktar ve home.blade.php dosyasını çağır
        return view('Frontend.pages.home', compact('munitions', 'munitionImages', 'category', 'tags', 'categories'));
    }


    public function search(Request $request)
    {
        $categories = Category::all();
        $tags = Tag::all();

        // Arama sorgusunu al
        $search = $request->input('q');

        // Munition modelinden arama yap
        $munitions = Munition::where('name', 'like', '%' . $search . '%')
            ->orWhereHas('category', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orWhere('origin', 'like', '%' . $search . '%')
            ->orWhere('summary', 'like', '%' . $search . '%')
            ->orWhere('description', 'like', '%' . $search . '%')
            ->paginate(12); // Sayfalama ekleyerek 12 sonuç göster

        return view('Frontend.pages.home', compact('munitions', 'search', 'categories', 'tags'));
    }
}
