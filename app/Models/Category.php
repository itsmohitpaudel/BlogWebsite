<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $guarded = [];

    public function setCategoryNameAttribute($value)
    {
        $this->attributes['category_name'] = $value;
        $this->attributes['category_slug'] = Str::slug($value);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
