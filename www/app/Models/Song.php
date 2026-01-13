<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Song extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'title',
        'artist',
        'url',
        'cover_url',
        'date',
        'duration',
        'is_explicit',
        'genre'
    ];
    public $timestamps = true;

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

    protected function casts(): array
    {
        return [
            'title' => 'string',
            'artist' => 'string',
            'url' => 'string',
            'cover_url' => 'string',
            'date' => 'date',
            'duration' => 'integer',
            'is_explicit' => 'boolean',
            'genre' => 'string'
        ];
    }
}
