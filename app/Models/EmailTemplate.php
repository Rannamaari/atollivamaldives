<?php

namespace App\Models;

use App\Enums\EmailTemplateType;
use App\Models\Concerns\TracksUserstamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory, TracksUserstamps;

    protected $fillable = ['name', 'slug', 'template_type', 'subject_template', 'body_template', 'description', 'is_active', 'created_by', 'updated_by'];

    protected function casts(): array
    {
        return [
            'template_type' => EmailTemplateType::class,
            'is_active' => 'boolean',
        ];
    }
}
