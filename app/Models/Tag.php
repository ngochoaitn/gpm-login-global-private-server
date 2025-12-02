<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    protected $table = 'tags';

    public $incrementing = false; // Disable auto-increment
    protected $keyType = 'string'; // ID is string (UUID)

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'color',
        'description',
        'category',
        'created_by',
    ];

    protected $hidden = ['pivot'];

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
     * Profiles with this tag
     */
    public function profiles()
    {
        return $this->belongsToMany(Profile::class, 'profile_tags')
            ->using(ProfileTag::class)
            ->withTimestamps();
    }

    /**
     * Proxies with this tag
     */
    public function proxies()
    {
        return $this->belongsToMany(Proxy::class, 'proxy_tags')
            ->using(ProxyTag::class)
            ->withTimestamps();
    }

    /**
     * Get the tag's display name with color if available
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name . ($this->color ? " ({$this->color})" : '');
    }
}
