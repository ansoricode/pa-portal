<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $guarded = [];

    protected $casts = ['is_active' => 'boolean'];

    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : 'https://placehold.co/1600x700/034d37/ffffff?text=' . urlencode($this->title);
    }

    /** Tautan internal ("/layanan-hukum/ecourt") atau eksternal ("https://..."). */
    public function getLinkAttribute(): ?string
    {
        if (! $this->button_url) {
            return null;
        }

        return preg_match('#^https?://#i', $this->button_url) ? $this->button_url : url($this->button_url);
    }
}
