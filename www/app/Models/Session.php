<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Session extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'sessions';

    /**
     * The primary key associated with the table.
     * 
     * @var string
     */
    protected $primaryKey = 'token_hash';
    protected $keyType = 'binary';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     * 
     * @var list<string>
     */
    protected $fillable = [
        'token_hash',
        'created_at',
        'ip_address',
        'user_agent',
        'user_id'
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

    /**
     * Get the user that owns the session
     * 
     * @return BelongsTo<User, Session>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the attributes that should be cast.
     * 
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // 'token_hash' => 'hashed'
            'created_at' => 'datetime'
        ];
    }
}
