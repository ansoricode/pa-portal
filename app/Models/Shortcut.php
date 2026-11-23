<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shortcut extends Model
{
    protected $guarded = [];

    protected $casts = ['is_active' => 'boolean', 'is_external' => 'boolean'];

    public function getLinkAttribute(): string
    {
        return preg_match('#^https?://#i', $this->url) ? $this->url : url($this->url);
    }
}
