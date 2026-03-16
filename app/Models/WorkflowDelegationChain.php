<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowDelegationChain extends Model
{
    protected $table = 'workflow_delegation_chain';

    protected $fillable = [
        'workflow_id',
        'delegator_workflow_id',
        'delegator_user_id',
        'delegate_user_id',
        'delegation_type',
        'depth_level',
    ];

    public function workflow()
    {
        return $this->belongsTo(DocumentWorkflow::class, 'workflow_id');
    }

    public function delegatorWorkflow()
    {
        return $this->belongsTo(DocumentWorkflow::class, 'delegator_workflow_id');
    }

    public function delegator()
    {
        return $this->belongsTo(User::class, 'delegator_user_id');
    }

    public function delegate()
    {
        return $this->belongsTo(User::class, 'delegate_user_id');
    }
}
