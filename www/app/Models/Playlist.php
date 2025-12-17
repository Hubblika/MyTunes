<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
     * Get all songs in the playlist
     * 
     * @return HasMany<OrderedSong, Playlist>
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
        return [
            'creator_id' => 'integer',
            'name' => 'string',
            'description' => 'string',
            'is_album' => 'boolean'
        ];
    }
}
