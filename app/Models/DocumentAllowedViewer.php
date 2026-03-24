<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentAllowedViewer extends Model
{
    use HasFactory;

    protected $table = 'document_allowed_viewers';
    protected $fillable = [
        'doc_id',
        'user_id',
        'office_id',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class, 'doc_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }
}
