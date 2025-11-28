<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Playlist extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'playlists';

    /**
     * The primary key associated with the table.
     * 
     * @var string
     */
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     * 
     * @var list<string>
     */
    protected $fillable = [
        'creator_id',
        'name',
        'description',
        'is_album'
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
     * @return BelongsTo<User, Playlist>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all songs in the playlist
     * 
     * @return HasMany<Song, Playlist>
     */
    public function songs(): HasMany
    {
        return $this->hasMany(OrderedSong::class);
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
        return [];
    }
}
