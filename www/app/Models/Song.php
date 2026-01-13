<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Song extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'title', 'artist', 'url', 'cover_url', 'duration', 'is_explicit', 'genre'
    ];

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
        return $this->belongsToMany(Playlist::class, 'playlist_songs')
                    ->withPivot('position')
                    ->withTimestamps();
    }
}
