<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourtSchedule extends Model
{
    protected $guarded = [];

    protected $casts = ['hearing_date' => 'date'];
}
