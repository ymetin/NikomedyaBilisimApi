<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = ['title', 'slug', 'author', 'content', 'image', 'status'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:d M, Y',
        ];
    }
}
