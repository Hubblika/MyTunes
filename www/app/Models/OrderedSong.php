<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderedSong extends Pivot
{
    protected $table = 'ordered_songs';
    protected $primaryKey = 'id';

    protected $fillable = [
        'playlist_uuid',
        'song_uuid',
        'index'
    ];

    protected $hidden = [];

    public $timestamps = true;

    /**
     * Get the user that created the playlist
     * 
     * @return BelongsTo<Song, Playlist>
     */
    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }

    /**
     * Get the playlist this ordered song is in
     * 
     * @return BelongsTo<Playlist, OrderedSong>
     */
    public function playlist(): BelongsTo
    {
        return $this->belongsTo(Playlist::class);
    }

    /**
     * Get the attributes that should be cast.
     * 
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'playlist_uuid' => 'string',
            'song_uuid' => 'string',
            'index' => 'integer'
        ];
    }
}
