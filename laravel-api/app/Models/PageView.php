<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    protected $fillable = [
        'url',
        'path',
        'route_name',
        'method',
        'ip_address',
        'user_agent',
        'referer',
        'session_id',
    ];
}
