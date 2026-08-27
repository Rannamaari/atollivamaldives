<?php

namespace App\Models;

use App\Enums\ActivityEventType;
use App\Enums\DocumentType;
use App\Services\OperationsHub\ActivityLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DocumentRecord extends Model
{
    use HasFactory;

    protected $fillable = ['documentable_type', 'documentable_id', 'document_type', 'original_filename', 'stored_filename', 'stored_path', 'mime_type', 'file_size', 'issue_date', 'effective_date', 'expiry_date', 'is_confidential', 'notes', 'uploaded_by'];

    protected function casts(): array
    {
        return [
            'document_type' => DocumentType::class,
            'issue_date' => 'date',
            'effective_date' => 'date',
            'expiry_date' => 'date',
            'is_confidential' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (DocumentRecord $document): void {
            if (blank($document->uploaded_by) && auth()->check()) {
                $document->uploaded_by = auth()->id();
            }
        });

        static::created(function (DocumentRecord $document): void {
            app(ActivityLogger::class)->log(
                ActivityEventType::DocumentUploaded,
                'Document uploaded',
                $document->original_filename,
                supplier: $document->documentable instanceof Supplier ? $document->documentable : null,
                agencyPartner: $document->documentable instanceof AgencyPartner ? $document->documentable : null,
                rateRequest: $document->documentable instanceof RateRequest ? $document->documentable : null,
                document: $document,
            );
        });
    }

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
