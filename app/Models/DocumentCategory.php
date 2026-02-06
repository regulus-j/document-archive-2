<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentCategory extends Model
{
    protected $table = 'document_categories';
    
    protected $fillable = [
        'category',
        'company_id',
        'is_global',
    ];

    protected $casts = [
        'is_global' => 'boolean',
    ];

    public function documents()
    {
        return $this->belongsToMany(Document::class, 'document_category', 'category_id', 'doc_id');
    }

    public function company()
    {
        return $this->belongsTo(CompanyAccount::class, 'company_id');
    }
}
