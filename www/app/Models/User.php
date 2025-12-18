<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
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
        'username',
        'email',
        'password',
        'is_admin',
        'is_searchable',
        'description'
    ];

    protected $hidden = [
        'is_admin',
        'is_searchable',
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
    public function uploadedSongs(): HasMany
    {
        return $this->hasMany(Song::class);
    }

    /**
     * Get all songs in the playlist
     * 
     * @return BelongsToMany<Song, User, Pivot>
     */
    public function likedSongs(): BelongsToMany
    {
        return $this->belongsToMany(
            Song::class,
            '_user_likes',
            'user_id',
            'song_uuid',
            'id',
            'uuid'
        );
    }

    /**
     * Get all songs in the playlist
     * 
     * @return BelongsToMany<Playlist, User, Pivot>
     */
    public function savedPlaylists(): BelongsToMany
    {
        return $this->belongsToMany(
            Playlist::class,
            '_user_saved_playlists',
            'user_id',
            'playlist_uuid',
            'id',
            'uuid'
        );
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
            'is_admin' => 'boolean',
            'is_searchable' => 'boolean',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime'
        ];
    }
}
