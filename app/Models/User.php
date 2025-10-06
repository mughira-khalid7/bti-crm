<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    public function goal()
    {
        return $this->hasOne(Goal::class);
    }

    public function setting()
    {
        return $this->hasOne(UserSetting::class);
    }

    public function actionLogs()
    {
        return $this->hasMany(ActionLog::class);
    }

    public function bdNotes()
    {
        return $this->hasMany(BDNote::class);
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->avatar) {
            return null;
        }
        return asset('avatars/' . ltrim($this->avatar, '/'));
    }

    public function getHasAvatarAttribute(): bool
    {
        return !empty($this->avatar);
    }

    public function getInitialsAttribute(): string
    {
        $name = trim((string) $this->name);
        if ($name === '') {
            return '';
        }

        $parts = preg_split('/\s+/', $name) ?: [];
        if (count($parts) === 0) {
            return '';
        }

        $firstInitial = mb_strtoupper(mb_substr($parts[0], 0, 1));
        $lastInitial = $firstInitial;
        if (count($parts) > 1) {
            $lastInitial = mb_strtoupper(mb_substr($parts[count($parts) - 1], 0, 1));
        }

        return $firstInitial . $lastInitial;
    }
}
