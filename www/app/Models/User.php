<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $fillable = [
        'liked_songs_playlist_uuid',
        'username',
        'email',
        'password_hash',
        'role',
        'description'
    ];

    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token'
    ];

    public $timestamps = true;

    /**
     * Get all playlists the user has created
     * 
     * @return HasMany<Playlist, User>
     */
    public function playlists(): HasMany
    {
        return $this->hasMany(Playlist::class);
    }

    /**
     * Get all songs published by the user
     * 
     * @return HasMany<Song, User>
     */
    public function songs(): HasMany
    {
        return $this->hasMany(Song::class);
    }

    /**
     * Get the attributes that should be cast.
     * 
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'username' => 'string',
            'email' => 'string',
            'email_verified_at' => 'datetime',
            'password_hash' => 'hashed',
            'two_factor_confirmed_at' => 'datetime'
        ];
    }
}
