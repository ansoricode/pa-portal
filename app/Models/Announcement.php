<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $guarded = [];

    protected $casts = [
        'published_at' => 'date',
        'is_active' => 'boolean',
        'view_count' => 'integer',
    ];

    public function getLinkAttribute(): string
    {
        if (! $this->url) {
            return '#';
        }

        return preg_match('#^https?://#i', $this->url) ? $this->url : url($this->url);
    }
}
