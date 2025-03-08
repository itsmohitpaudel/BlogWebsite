<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $guarded = [];

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // Filters posts using tag_name
    public function scopeTag($query, $tagName)
    {
        return $query->whereHas('tags', function ($query) use ($tagName) {
            $query->where('tag_name', 'like', '%' . $tagName . '%');
        });
    }

    // Filters posts using category_name
    public function scopeCategory($query, $categoryName)
    {
        return $query->whereHas('category', function ($query) use ($categoryName) {
            $query->where('category_name', 'like', '%' . $categoryName . '%');
        });
    }

    // Filters posts using author name
    public function scopeAuthor($query, $authorName)
    {
        return $query->whereHas('author', function ($query) use ($authorName) {
            $query->where('name', 'like', '%' . $authorName . '%');
        });
    }

    // Using polymorphic relationship
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    //  This allows to filter posts based on their associated tags.
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
