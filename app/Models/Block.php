<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Block extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'blocker_id',
        'blocked_id',
    ];

    /**
     * Get the user that blocked the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function blocker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocker_id');
    }

    /**
     * Get the user that blocked the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function blocked(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocked_id');
    }

}
