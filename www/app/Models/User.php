<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    use HasFactory;

    protected $fillable = [
        'username', 'email', 'password', 'is_admin', 'is_searchable', 'description',
    ];

    protected $hidden = [
        'is_searchable',
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token'
    ];

    public $timestamps = true;

    // Relationships
    public function playlists()
    {
        return $this->hasMany(Playlist::class);
    }

    protected function casts(): array
    {
        return [
            'username' => 'string',
            'email' => 'string',
            'email_verified_at' => 'datetime',
            'is_admin' => 'boolean',
            'is_searchable' => 'boolean',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime'
        ];
    }
}
