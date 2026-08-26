<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'accommodation_id',
        'transfer_type',
        'name',
        'description',
        'duration',
        'adult_price',
        'child_price',
        'infant_price',
        'currency',
        'mandatory',
    ];

    protected function casts(): array
    {
        return [
            'adult_price' => 'decimal:2',
            'child_price' => 'decimal:2',
            'infant_price' => 'decimal:2',
            'mandatory' => 'boolean',
        ];
    }

    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class);
    }
}
