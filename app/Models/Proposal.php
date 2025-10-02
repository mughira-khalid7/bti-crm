<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proposal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'job_description',
        'connects_used',
        'url',
        'notes',
        'status',
        'submitted_at',
        'is_copy',
        'original_proposal_id',
    ];

    /**
     * Get the user that owns the proposal.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Get all versions for this proposal.
     */
    public function versions(): HasMany
    {
        return $this->hasMany(ProposalVersion::class)->orderBy('version_number', 'desc');
    }

    /**
     * Get the latest version.
     */
    public function latestVersion()
    {
        return $this->hasOne(ProposalVersion::class)->latestOfMany('version_number');
    }

    /**
     * Get the original proposal if this is a copy.
     */
    public function originalProposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class, 'original_proposal_id');
    }

    /**
     * Get all copies of this proposal.
     */
    public function copies(): HasMany
    {
        return $this->hasMany(Proposal::class, 'original_proposal_id');
    }

    /**
     * Get the tracked fields for version comparison.
     */
    public static function getTrackedFields(): array
    {
        return [
            'title',
            'job_description',
            'connects_used',
            'url',
            'notes',
            'submitted_at',
        ];
    }
}
