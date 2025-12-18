<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Str;

class Playlist extends Model
{
    protected $table = 'playlists';
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'creator_id',
        'name',
        'description',
        'public',
        'is_album'
    ];

    protected $hidden = [];

    public $timestamps = true;

    /**
     * Get the user that created the playlist
     * 
     * @return BelongsTo<User, Playlist>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all ordered_songs in the playlist
     * 
     * @return HasMany<OrderedSong, Playlist>
     */
    public function orderedSongs(): HasMany
    {
        return $this->hasMany(OrderedSong::class);
    }

    /**
     * Get all songs in the playlist
     * 
     * @return BelongsToMany<Song, Playlist, Pivot>
     */
    public function songs(): BelongsToMany
    {
        return $this
            ->belongsToMany(Song::class, 'ordered_songs')
            ->using(OrderedSong::class)
            ->withPivot(['id', 'index'])
            ->orderBy('pivot_index');
    }

    public static function boot() {
        parent::boot();

        static::creating(function (Model $model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     * 
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'creator_id' => 'integer',
            'name' => 'string',
            'description' => 'string',
            'public' => 'boolean',
            'is_album' => 'boolean'
        ];
    }
}
