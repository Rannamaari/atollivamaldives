<?php

namespace App\Models;

use App\Enums\ContactDepartment;
use App\Enums\ContactMethod;
use App\Models\Concerns\TracksUserstamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierContact extends Model
{
    use HasFactory, TracksUserstamps;

    protected $fillable = ['supplier_id', 'full_name', 'job_title', 'department', 'email', 'telephone', 'whatsapp_number', 'preferred_contact_method', 'is_primary', 'is_active', 'notes', 'created_by', 'updated_by'];

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
        static::saved(function (SupplierContact $contact): void {
            if ($contact->is_primary && $contact->supplier_id) {
                static::query()
                    ->where('supplier_id', $contact->supplier_id)
                    ->whereKeyNot($contact->getKey())
                    ->update(['is_primary' => false]);
            }
        });
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
