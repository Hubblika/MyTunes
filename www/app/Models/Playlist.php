<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Playlist extends Model
{
    protected $table = 'playlists';

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'uuid',
        'user_id',
        'name',
        'description',
        'public',
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

    public $timestamps = true;


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function songs()
    {
        return $this->belongsToMany(
            Song::class,
            'playlist_songs',
            'playlist_id',
            'song_id'
        )->withPivot('position')
         ->withTimestamps();
    }

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'name' => 'string',
            'description' => 'string',
            'public' => 'boolean',
        ];
    }
}

