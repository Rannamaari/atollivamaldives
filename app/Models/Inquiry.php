<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inquiry extends Model
{
    use HasFactory;

    protected $fillable = ['reference', 'customer_id', 'accommodation_id', 'room_id', 'name', 'first_name', 'last_name', 'email', 'phone', 'nationality', 'travel_type', 'arrival_date', 'departure_date', 'check_in', 'check_out', 'number_of_nights', 'travellers', 'adults', 'children', 'children_ages', 'infants', 'budget', 'preferred_atoll', 'transfer_preference', 'honeymoon', 'family_trip', 'diving_trip', 'surfing_trip', 'preferred_room', 'meal_plan', 'message', 'notes', 'status', 'source', 'assigned_to'];

    protected function casts(): array
    {
        return ['arrival_date' => 'date', 'departure_date' => 'date', 'check_in' => 'date', 'check_out' => 'date', 'travellers' => 'integer', 'number_of_nights' => 'integer', 'adults' => 'integer', 'children' => 'integer', 'infants' => 'integer', 'honeymoon' => 'boolean', 'family_trip' => 'boolean', 'diving_trip' => 'boolean', 'surfing_trip' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::creating(function (Inquiry $inquiry) {
            if (! $inquiry->reference) {
                $nextId = (static::max('id') ?? 0) + 1;
                $inquiry->reference = 'ATL-'.str_pad((string) $nextId, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }
}
