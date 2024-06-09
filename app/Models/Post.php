<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'category_id', 'summary', 'content', 'slug', 'status', 'image'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            $post->slug = Str::slug($post->title);
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tags')->withPivot('id');
    }

    /*
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
        public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
    */

}
