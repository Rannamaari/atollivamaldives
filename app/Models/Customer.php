<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'company_name',
        'email',
        'phone',
        'whatsapp',
        'country',
        'address',
        'passport_number',
        'work_permit_number',
        'national_id_number',
        'dependents',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'dependents' => 'array',
        ];
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim(collect([$this->first_name, $this->last_name])->filter()->implode(' '));
    }
}
