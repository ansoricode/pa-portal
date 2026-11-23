<?php

namespace App\Models;

use App\Models\Concerns\HasFileUrl;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFileUrl;

    protected $guarded = [];

    protected $casts = ['is_active' => 'boolean'];

    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category)->where('is_active', true)
            ->orderBy('sort_order')->orderByDesc('year')->orderBy('id');
    }
}
