<?php

namespace App\Models;

use App\Models\Concerns\HasFileUrl;
use Illuminate\Database\Eloquent\Model;

class PanggilanGhaib extends Model
{
    use HasFileUrl;

    protected $table = 'panggilan_ghaib';

    protected $guarded = [];

    protected $casts = ['hearing_date' => 'date'];
}
