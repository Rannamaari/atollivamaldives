<?php

namespace App\Models;

use App\Enums\ContactDepartment;
use App\Enums\ContactMethod;
use App\Models\Concerns\TracksUserstamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgencyContact extends Model
{
    use HasFactory, TracksUserstamps;

    protected $fillable = ['agency_partner_id', 'full_name', 'position', 'department', 'email', 'telephone', 'whatsapp_number', 'preferred_contact_method', 'is_primary', 'is_active', 'notes', 'created_by', 'updated_by'];

    protected function casts(): array
    {
        return [
            'department' => ContactDepartment::class,
            'preferred_contact_method' => ContactMethod::class,
            'is_primary' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (AgencyContact $contact): void {
            if ($contact->is_primary && $contact->agency_partner_id) {
                static::query()
                    ->where('agency_partner_id', $contact->agency_partner_id)
                    ->whereKeyNot($contact->getKey())
                    ->update(['is_primary' => false]);
            }
        });
    }

    public function agencyPartner(): BelongsTo
    {
        return $this->belongsTo(AgencyPartner::class);
    }
}
