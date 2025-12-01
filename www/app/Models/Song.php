<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Song extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'songs';

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
        'title',
        'created_at',
        'duration',
        'is_explicit'
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
     * Get the attributes that should be cast.
     * 
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'title' => 'string',
            'created_at' => 'date',
            'duration' => 'integer',
            'is_explicit' => 'boolean'
        ];
    }
}
