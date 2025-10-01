<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
