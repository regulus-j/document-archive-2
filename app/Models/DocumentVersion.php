<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentVersion extends Model
{
    use HasFactory;

    protected $table = 'document_versions';

    protected $fillable = [
        'doc_id',
        'version_number',
        'file_path',
        'original_filename',
        'mime_type',
        'file_size',
        'uploaded_by',
        'change_notes',
    ];

    /**
     * Get the document this version belongs to.
     */
    public function document()
    {
        return $this->belongsTo(Document::class, 'doc_id');
    }

    /**
     * Get the user who uploaded this version.
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get formatted file size.
     */
    public function getFormattedSizeAttribute()
    {
        if (!$this->file_size) {
            return '0 KB';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $factor = floor((strlen((string)$size) - 1) / 3);

        return sprintf("%.1f %s", $size / pow(1024, $factor), $units[$factor] ?? 'GB');
    }
}
