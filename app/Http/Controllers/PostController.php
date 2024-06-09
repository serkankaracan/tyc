<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        $posts = Post::where('status', '!=', -1)->get();
        return view('Backend.pages.post', compact('categories', 'posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('Backend.pages.post_add_edit', compact('categories', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Doğrulama kurallarının belirlenmesi
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'postImage' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'status' => 'required|integer',
            'tags' => 'nullable|array',
        ]);

        // Verinin doğrulamasının geçip geçmediğinin kontrol edilmesi
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Kategori modelinin oluşturulması
        $post = new Post();
        $post->title = $request->input('title');
        $post->slug = Str::slug($request->input('title'));
        $post->category_id = $request->input('category_id');
        $post->summary = $request->input('summary');
        $post->content = $request->input('content');
        $post->status = $request->input('status');

        // Kullanıcı resim yüklediyse işlemler yapılır
        if ($request->hasFile('postImage')) {
            // Get base64 image data
            $croppedImageData = $request->input('croppedPostImage');

            if ($croppedImageData) {
                $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $croppedImageData));

                // Base64 kodunu dosyaya yaz
                $tempImagePath = tempnam(sys_get_temp_dir(), 'cropped_post_image');
                file_put_contents($tempImagePath, $image);

                $imageData = $request->file('postImage');
                // Generate unique image name
                $imageName = Str::slug(pathinfo($imageData->getClientOriginalName(), PATHINFO_FILENAME), '_') . '_' . uniqid();
                $imageExtension = $imageData->getClientOriginalExtension();
                $fullImageName = $imageName . '.' . $imageExtension;

                // Save image to storage
                $storagePath = Storage::putFileAs('public/post_images', new File($tempImagePath), $fullImageName);

                // Dosyayı sildikten sonra
                unlink($tempImagePath);

                $post->image = 'post_images/' . $fullImageName;
            } else {
                $image = $request->file('postImage');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->storeAs('public/post_images', $imageName);
                $post->image = 'post_images/' . $imageName;
            }
        }

        // Makale kaydedilir ve başarılı mesajı döndürülür
        if ($post->save()) {
            if ($request->has('tags')) {
                $tags = Tag::find($request->input('tags'));
                $post->tags()->attach($tags);
            }
            return redirect()->route('makale.index')->with('success', $post->title . ' veritabanına eklendi.');
        } else {
            // Kayıt başarısız olursa hata mesajı döndürülür
            return redirect()->route('makale.index')->with('fail', $post->title . 'veritabanına eklenirken bir hata oluştu.');
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $categories = Category::all();
        $tags = Tag::all();

        return view('Backend.pages.post_add_edit', compact('post', 'categories', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $data = [
            'title' => $request->input('title'),
            'category_id' => $request->input('category_id'),
            'image' => $request->hasFile('postImage') ? $request->file('postImage')->getClientOriginalName() : null,
            'summary' => $request->input('summary'),
            'content' => $request->input('content'),
            'status' => $request->input('status'),
        ];

        // Doğrulama kurallarının belirlenmesi
        $rules = [
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|string',
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'status' => 'required|string',
        ];

        // Veri doğrulamasının yapılması
        $validator = Validator::make($data, $rules);

        // Verinin doğrulamasının geçip geçmediğinin kontrol edilmesi
        if ($validator->passes()) {
            $post->title = $data['title'];
            $post->slug = Str::slug($data['title']);
            $post->category_id = $data['category_id'];
            $post->summary = $data['summary'];
            $post->content = $data['content'];
            $post->status = $data['status'];

            // Kullanıcı resim yüklediyse işlemler yapılır
            if ($request->hasFile('postImage')) {

                Storage::delete('public/' . $post->image);

                // Resmin base64 verisi alınır ve resim dosyası oluşturulur
                $croppedImageData = $request->input('croppedImage');
                $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $croppedImageData));

                // Geçici bir dosya oluşturulur ve resim verisi yazılır
                $tempFilePath = tempnam(sys_get_temp_dir(), 'post_image_');
                file_put_contents($tempFilePath, $image);

                // Resmin dosya bilgileri alınır ve dosya adı oluşturulur
                $imageData = $request->file('postImage');
                $imageName = Str::slug(pathinfo($imageData->getClientOriginalName(), PATHINFO_FILENAME), '_') . '_' . uniqid();
                $imageExtension = $imageData->getClientOriginalExtension();
                $fullImageName = $imageName . '.' . $imageExtension;

                // Geçici dosya Storage'a yüklenir ve dosya yolu kaydedilir
                $storagePath = Storage::putFileAs('public/post_images', new File($tempFilePath), $fullImageName);
                $post->image = 'post_images/' . $fullImageName;
            }


            // Makale kaydedilir ve başarılı mesajı döndürülür
            if ($post->update()) {
                if ($request->has('tags')) {
                    $post->tags()->sync($request->input('tags'));
                }
                return redirect()->route('makale.index')->with('success', $post->title . ' güncellendi.');
            } else {
                // Kayıt başarısız olursa hata mesajı döndürülür
                return redirect()->route('makale.index')->with('fail', $post->title . ' veritabanına eklenirken bir hata oluştu.');
            }
        } else {
            // Doğrulama başarısız olursa hatalar kullanıcıya gösterilir
            $errors = $validator->errors()->all();
            $errorMessage = implode("\n", $errors);
            return redirect()->route('makale.index')->with('fail', $errorMessage);
        }
    }

    public function changeStatus($id)
    {
        $post = Post::findOrFail($id);
        $newStatus = $post->status === 1 ? 0 : 1;
        $post->update(['status' => $newStatus]);

        $message = $newStatus === 1 ? 'aktif' : 'pasif';
        return redirect()->route('makale.index')->with('success', $post->title . ' durumu başarıyla ' . $message . ' hale getirildi.');
    }

    public function remove($id)
    {
        $post = Post::findOrFail($id);
        $post->update(['status' => -1]);
        return redirect()->route('makale.index')->with('success', $post->title . ' arşivlendi.');
    }

    public function restore($id)
    {
        $post = Post::findOrFail($id);
        $post->update(['status' => 1]);
        return redirect()->route('makale.index')->with('success', $post->title . ' arşivden kurtarıldı.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $post = Post::find($id);
        if (!$post) {
            return redirect()->back()->with('error', 'Özellik bulunamadı.');
        }

        if ($post->delete()) {
            Storage::delete('public/' . $post->image);
            return redirect()->route('makale.index')->with('success', $post->title . ' veritabanından silindi.');
        } else {
            return redirect()->route('makale.index')->with('fail', $post->title . ' veritabanından silinirken bir hata oluştu.');
        }
    }
}
