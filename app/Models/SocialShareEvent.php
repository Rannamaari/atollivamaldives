<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SocialShareEvent extends Model
{
    protected $fillable = [
        'shareable_type',
        'shareable_id',
        'platform',
        'url',
        'session_id',
        'user_id',
        'ip_hash',
        'user_agent',
        'referrer',
    ];

    public function shareable(): MorphTo
    {
        return $this->morphTo();
    }
}
