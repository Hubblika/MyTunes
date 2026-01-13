<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable;

    protected $keyType = 'string';       // UUID
    public $incrementing = false;        // Not auto-incrementing
    protected $fillable = [
        'username', 'email', 'password', 'is_admin', 'is_searchable', 'description'
    ];

    // Boot method to auto-generate UUIDs
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // Relationships
    public function playlists()
    {
        return $this->hasMany(Playlist::class);
    }
}
