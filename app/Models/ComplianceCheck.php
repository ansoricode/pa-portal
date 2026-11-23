<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplianceCheck extends Model
{
    protected $fillable = [
        'code',
        'status',
        'target_url',
        'notes',
        'checked_at',
        'checked_by',
        'link_status',
        'link_http_status',
        'link_message',
        'link_checked_at',
        'link_checked_by',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
        'link_checked_at' => 'datetime',
    ];
}
