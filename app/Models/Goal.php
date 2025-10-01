<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    protected $fillable = [
        'user_id',
        'daily_goal',
        'allowed_connects',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
