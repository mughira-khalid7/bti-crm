<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;

class UpworkProfile extends Model
{
    protected $fillable = [
        'profile_name',
        'country',
        'username',
        'password',
        'assigned_bd_ids'
    ];

    protected $casts = [
        'assigned_bd_ids' => 'array',
    ];

    /**
     * Get the encrypted username attribute
     */
    public function getUsernameAttribute($value)
    {
        if ($value) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                return $value; // Return as-is if decryption fails
            }
        }
        return $value;
    }

    /**
     * Set the encrypted username attribute
     */
    public function setUsernameAttribute($value)
    {
        if ($value) {
            $this->attributes['username'] = Crypt::encryptString($value);
        }
    }

    /**
     * Get the encrypted password attribute
     */
    public function getPasswordAttribute($value)
    {
        if ($value) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                return $value; // Return as-is if decryption fails
            }
        }
        return $value;
    }

    /**
     * Set the encrypted password attribute
     */
    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = Crypt::encryptString($value);
        }
    }

    /**
     * Get the assigned BD users
     */
    public function assignedBds()
    {
        return $this->belongsToMany(User::class, 'upwork_profile_user', 'upwork_profile_id', 'user_id')
                    ->where('role', 'bd');
    }

    /**
     * Get all users assigned to this profile (alias for assignedBds for backward compatibility)
     */
    public function users()
    {
        return $this->assignedBds();
    }
}
