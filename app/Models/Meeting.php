<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Meeting extends Model
{
    protected $fillable = [
        'proposal_id',
        'bd_user_id',
        'title',
        'description',
        'scheduled_at',
        'meeting_type',
        'meeting_link',
        'location',
        'status',
        'notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    /**
     * Get the proposal that owns the meeting.
     */
    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    /**
     * Get the BD user assigned to the meeting.
     */
    public function bdUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'bd_user_id');
    }

    /**
     * BD participants invited to this meeting (including owner optionally).
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'meeting_user')->withTimestamps();
    }
}
