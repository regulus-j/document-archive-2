
    use HasFactory;
    
    protected $fillable = [
        'company_id',
        'retention_days',
        'storage_limit_mb',
        'is_active',
        'criteria',
        'last_executed_at',
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'last_executed_at' => 'datetime',
    ];
    
    /**
     * Get the company that owns this deletion schedule
     */
    public function company()
    {
        return $this->belongsTo(CompanyAccount::class);
    }
    
    /**
     * Check if the schedule should be triggered based on storage
     */
    public function shouldTriggerByStorage($currentStorageMb)
    {
        if (!$this->is_active) {
            return false;
        }
        
        if ($this->criteria === 'storage' || $this->criteria === 'both') {
            return $this->storage_limit_mb && $currentStorageMb >= $this->storage_limit_mb;
        }
        
        return false;
    }
    
    /**
     * Get documents eligible for deletion based on age
     */
    public function getDocumentsForDeletion()
    {
        if (!$this->is_active) {
            return collect();
        }
        
        if ($this->criteria === 'age' || $this->criteria === 'both') {
            $cutoffDate = now()->subDays($this->retention_days);
            
            return Document::where('company_id', $this->company_id)
                ->where('is_archived', false)
                ->where('created_at', '<', $cutoffDate)
                ->get();
        }
        
        return collect();
    }
}
