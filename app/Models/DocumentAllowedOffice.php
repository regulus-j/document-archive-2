<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentAllowedOffice extends Model
{
    protected $fillable = [
        'document_id',
        'office_id',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }
}
