<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class News extends Model
{
    protected $table = 'news';

    protected $guarded = [];

    protected $casts = ['published_at' => 'datetime', 'is_published' => 'boolean'];

    protected static function booted(): void
    {
        static::saving(function (News $news) {
            if (blank($news->slug)) {
                $base = Str::slug($news->title) ?: 'berita';
                $slug = $base;
                $i = 2;
                while (static::where('slug', $slug)->where('id', '!=', $news->id ?? 0)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $news->slug = $slug;
            }
            if (! $news->published_at) {
                $news->published_at = now();
            }
        });
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->where('published_at', '<=', now());
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : 'https://placehold.co/600x360/056e4f/ffffff?text=' . urlencode($this->category);
    }

    public function getBadgeClassAttribute(): string
    {
        return $this->category === 'Inovasi Layanan' ? 'bg-pa-primary' : 'bg-pa-secondary text-dark';
    }
}
