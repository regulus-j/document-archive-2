<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentOfficePermission extends Model
{
    use HasFactory;

    protected $table = 'document_office_permissions';
    
    protected $fillable = [
        'document_id',
        'office_id',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }
}
