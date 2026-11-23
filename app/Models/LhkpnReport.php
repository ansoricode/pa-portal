<?php

namespace App\Models;

use App\Models\Concerns\HasFileUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LhkpnReport extends Model
{
    use HasFileUrl;

    protected $guarded = [];

    public function official(): BelongsTo
    {
        return $this->belongsTo(Official::class);
    }
}
