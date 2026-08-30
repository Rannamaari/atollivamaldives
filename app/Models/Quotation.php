<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quotation extends Model
{
    protected $fillable = [
        'inquiry_id',
        'customer_id',
        'accommodation_id',
        'quotation_number',
        'status',
        'quotation_date',
        'valid_until',
        'reference',
        'currency',
        'title',
        'customer_name',
        'company_name',
        'customer_address',
        'customer_phone',
        'customer_email',
        'property_name',
        'check_in',
        'check_out',
        'nights',
        'adults',
        'children',
        'infants',
        'chargeable_pax',
        'itinerary',
        'items',
        'taxes',
        'subtotal',
        'tax_total',
        'grand_total',
        'payment_notes',
        'notes',
        'signature_name',
        'signature_title',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'quotation_date' => 'date',
            'valid_until' => 'date',
            'check_in' => 'date',
            'check_out' => 'date',
            'items' => 'array',
            'taxes' => 'array',
            'subtotal' => 'decimal:2',
            'tax_total' => 'decimal:2',
            'grand_total' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Quotation $quotation): void {
            if (blank($quotation->quotation_number)) {
                $nextId = (static::max('id') ?? 0) + 1;
                $quotation->quotation_number = 'QT-'.str_pad((string) $nextId, 6, '0', STR_PAD_LEFT);
            }

            if (auth()->check() && blank($quotation->created_by)) {
                $quotation->created_by = auth()->id();
            }
        });

        static::saving(function (Quotation $quotation): void {
            if (auth()->check()) {
                $quotation->updated_by = auth()->id();
            }
        });
    }

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class);
    }
}
