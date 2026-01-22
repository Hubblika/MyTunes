<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Song extends Model
{
    protected $table = 'songs';

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'uuid',
        'title',
        'artist',
        'album',
        'url',
        'cover_url',
        'date',
        'duration',
        'genre',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->uuid) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function playlists()
    {
        return $this->belongsToMany(
            Playlist::class,
            'playlist_songs',
            'song_id',
            'playlist_id'
        )->withPivot('position')
         ->withTimestamps();
    }
}
