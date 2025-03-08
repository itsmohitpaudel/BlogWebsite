<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    protected $guarded = [];

    public function setTagNameAttribute($value)
    {
        $this->attributes['tag_name'] = $value;
        $this->attributes['tag_slug'] = Str::slug($value);
    }

    public function posts()
    {
        return $this->morphedByMany(Post::class, 'taggable');
    }
}
