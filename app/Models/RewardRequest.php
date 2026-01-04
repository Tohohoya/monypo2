<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardRequest extends Model
{
    protected $fillable = [
        'reward_id',
        'child_id',
        'status',
        'approved_at',
    ];

    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }

    public function child()
    {
        return $this->belongsTo(User::class);
    }
}