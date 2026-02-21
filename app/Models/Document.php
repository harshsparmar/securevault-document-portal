<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'original_name',
        'mime_type',
        'storage_path',
        'preview_path',
    ];

    /**
     * Get the user that uploaded the document.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the file extension from the original name.
     */
    public function getExtensionAttribute(): string
    {
        return strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));
    }

    /**
     * Determine if this document requires conversion for preview.
     */
    public function requiresConversion(): bool
    {
        return in_array($this->extension, config('documents.convertible_types', []));
    }

    /**
     * Determine if a preview is available.
     */
    public function hasPreview(): bool
    {
        return $this->preview_path !== null;
    }

    /**
     * Determine if the document is a plain text file.
     */
    public function isText(): bool
    {
        return $this->extension === 'txt';
    }

    /**
     * Determine if the document is a PDF.
     */
    public function isPdf(): bool
    {
        return $this->extension === 'pdf';
    }
}
