<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Str;

class Song extends Model
{
    protected $table = 'songs';
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'title',
        'created_at',
        'duration',
        'is_explicit'
    ];


    protected $hidden = [];

    public $timestamps = false;

    public static function boot() {
        parent::boot();

        static::creating(function (Model $model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Get all songs in the playlist
     * 
     * @return BelongsToMany<Song, User, Pivot>
     */
    public function likedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            '_user_likes',
            'song_uuid',
            'user_id',
            'uuid',
            'id'
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
            'created_at' => 'date'
        ];
    }
}
