<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    protected $table = 'users';

    public $incrementing = false;       // Không tự tăng
    protected $keyType = 'string';      // ID là kiểu string (UUID)
    
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'email',
        'system_role',
        'is_active',
        'display_name',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        // 'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'system_role' => 'string',
        'is_active' => 'boolean'
    ];

    /**
     * System role constants
     */
    const ROLE_ADMIN = 'ADMIN';
    const ROLE_MOD = 'MOD';
    const ROLE_USER = 'USER';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->system_role === self::ROLE_ADMIN;
    }

    /**
     * Check if user is moderator
     */
    public function isModerator(): bool
    {
        return $this->system_role === self::ROLE_MOD;
    }

    /**
     * Check if user has admin or moderator privileges
     */
    public function hasModeratorAccess(): bool
    {
        return in_array($this->system_role, [self::ROLE_ADMIN, self::ROLE_MOD]);
    }

    /**
     * Groups created by this user
     */
    public function createdGroups()
    {
        return $this->hasMany(Group::class, 'created_by');
    }

    /**
     * Groups last updated by this user
     */
    public function updatedGroups()
    {
        return $this->hasMany(Group::class, 'updated_by');
    }

    /**
     * Profiles created by this user
     */
    public function createdProfiles()
    {
        return $this->hasMany(Profile::class, 'created_by');
    }

    /**
     * Profiles currently being used by this user
     */
    public function usingProfiles()
    {
        return $this->hasMany(Profile::class, 'using_by');
    }

    /**
     * Profiles last run by this user
     */
    public function lastRunProfiles()
    {
        return $this->hasMany(Profile::class, 'last_run_by');
    }

    /**
     * Profiles deleted by this user
     */
    public function deletedProfiles()
    {
        return $this->hasMany(Profile::class, 'deleted_by');
    }

    /**
     * Group shares for this user
     */
    public function groupShares()
    {
        return $this->hasMany(GroupShare::class);
    }

    /**
     * Profile shares for this user
     */
    public function profileShares()
    {
        return $this->hasMany(ProfileShare::class);
    }

    /**
     * Proxy shares for this user
     */
    public function proxyShares()
    {
        return $this->hasMany(ProxyShare::class);
    }

    /**
     * Proxies created by this user
     */
    public function createdProxies()
    {
        return $this->hasMany(Proxy::class, 'created_by');
    }

    /**
     * Groups this user has access to through shares
     */
    public function sharedGroups()
    {
        return $this->belongsToMany(Group::class, 'group_shares')
            ->withPivot(['role'])
            ->withTimestamps();
    }

    /**
     * Profiles this user has access to through shares
     */
    public function sharedProfiles()
    {
        return $this->belongsToMany(Profile::class, 'profile_shares')
            ->withPivot(['role'])
            ->withTimestamps();
    }

    /**
     * Proxies this user has access to through shares
     */
    public function sharedProxies()
    {
        return $this->belongsToMany(Proxy::class, 'proxy_shares')
            ->withPivot(['role'])
            ->withTimestamps();
    }
}
