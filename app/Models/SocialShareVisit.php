<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialShareVisit extends Model
{
    protected $fillable = [
        'visit_key',
        'session_id',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'landing_page',
        'path',
        'user_id',
        'ip_hash',
        'user_agent',
        'referrer',
    ];
}
