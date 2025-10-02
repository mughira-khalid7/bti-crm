<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProposalVersion extends Model
{
    protected $fillable = [
        'proposal_id',
        'user_id',
        'version_number',
        'changes',
        'snapshot',
    ];

    protected $casts = [
        'changes' => 'array',
        'snapshot' => 'array',
    ];

    /**
     * Get the proposal that owns this version.
     */
    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    /**
     * Get the user who made this change.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
