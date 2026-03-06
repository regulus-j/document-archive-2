<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentPrint extends Model
{
    use HasFactory;

    protected $table = 'document_prints';

    protected $fillable = [
        'document_id',
        'version_id',
        'printed_by',
        'copies',
        'print_reason',
    ];

    protected $casts = [
        'copies' => 'integer',
    ];

    /**
     * Get the document that was printed.
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the specific version that was printed (nullable = current version).
     */
    public function version()
    {
        return $this->belongsTo(DocumentVersion::class, 'version_id');
    }

    /**
     * Get the user who printed the document.
     */
    public function printer()
    {
        return $this->belongsTo(User::class, 'printed_by');
    }

    /**
     * Get the total number of copies printed for a document.
     */
    public static function totalCopiesForDocument(int $documentId): int
    {
        return static::where('document_id', $documentId)->sum('copies');
    }

    /**
     * Get the total number of print events for a document.
     */
    public static function printCountForDocument(int $documentId): int
    {
        return static::where('document_id', $documentId)->count();
    }
}
