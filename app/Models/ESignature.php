<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ESignature extends Model
{
    use HasFactory;

    protected $table = 'e_signatures';

    protected $fillable = [
        'document_id',
        'workflow_id',
        'user_id',
        'action',
        'signature_path',
        'full_name',
        'position',
        'ip_address',
        'signed_at',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function workflow()
    {
        return $this->belongsTo(DocumentWorkflow::class, 'workflow_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
