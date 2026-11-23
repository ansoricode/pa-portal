<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Official extends Model
{
    protected $guarded = [];

    protected $casts = ['tmt' => 'date', 'is_active' => 'boolean', 'is_lhkpn' => 'boolean'];

    public function lhkpnReports(): HasMany
    {
        return $this->hasMany(LhkpnReport::class);
    }

    public function getPhotoUrlAttribute(): string
    {
        return $this->photo
            ? asset('storage/' . $this->photo)
            : 'https://placehold.co/150x150/056e4f/ffffff?text=' . urlencode(mb_substr($this->position, 0, 12));
    }
}
