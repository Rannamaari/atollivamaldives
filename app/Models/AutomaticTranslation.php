<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomaticTranslation extends Model
{
    protected $fillable = [
        'source_hash',
        'source_locale',
        'target_locale',
        'source_text',
        'translated_text',
        'provider',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return ['reviewed_at' => 'datetime'];
    }
}
