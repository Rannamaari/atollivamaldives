<?php

namespace App\Models;

use App\Enums\PartnerCollectionScope;
use App\Models\Concerns\TracksUserstamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PartnerCollection extends Model
{
    use HasFactory, TracksUserstamps;

    protected $fillable = [
        'name',
        'slug',
        'scope',
        'description',
        'color',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'scope' => PartnerCollectionScope::class,
            'is_active' => 'boolean',
        ];
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class, 'partner_collection_supplier');
    }

    public function agencyPartners(): BelongsToMany
    {
        return $this->belongsToMany(AgencyPartner::class, 'agency_partner_partner_collection');
    }
}
