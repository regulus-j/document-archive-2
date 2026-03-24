<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Office extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'parent_office_id',
        'office_lead',
        'archive_schedule_days',
        'archive_last_run_at',
    ];

    protected $casts = [
        'archive_last_run_at' => 'datetime',
    ];

    public function childOffices()
    {
        return $this->hasMany(Office::class, 'parent_office_id');
    }

    public function parentOffice()
    {
        return $this->belongsTo(Office::class, 'parent_office_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function sentTransactions()
    {
        return $this->hasMany(DocumentTransaction::class, 'from_office');
    }

    public function receivedTransactions()
    {
        return $this->hasMany(DocumentTransaction::class, 'to_office');
    }

    public function company()
    {
        return $this->belongsTo(CompanyAccount::class);
    }

    public function lead()
    {
        return $this->belongsTo(User::class, 'office_lead');
    }
}
