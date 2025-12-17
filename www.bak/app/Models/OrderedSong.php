<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderedSong extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'ordered_songs';

    /**
     * The primary key associated with the table.
     * 
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     * 
     * @var list<string>
     */
    protected $fillable = [
        'playlist_uuid',
        'song_uuid',
        'index'
    ];

    /**
     * The attributes that should be hidden for serialization.
     * 
     * @var list<string>
     */
    protected $hidden = [];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
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
