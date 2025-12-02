<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Group extends Model
{
    use HasFactory;

    protected $table = 'groups';

    public $incrementing = false;  // Disable auto-increment
    protected $keyType = 'string'; // ID is string (UUID)

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id',
        'name',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'sort_order' => 'integer',
    ];

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
     * Profiles in this group
     */
    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }

    /**
     * User who created this group
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User who last updated this group
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Group shares
     */
    public function shares()
    {
        return $this->hasMany(GroupShare::class);
    }

    /**
     * Users who have access to this group through shares
     */
    public function sharedUsers()
    {
        return $this->belongsToMany(User::class, 'group_shares')
            ->withPivot(['role'])
            ->withTimestamps();
    }
}
