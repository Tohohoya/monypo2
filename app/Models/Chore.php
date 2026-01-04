<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chore extends Model
{
    protected $fillable = [
        'family_id',
        'title',
        'description',
        'points',
        'child_id',
        'created_by',
        'status',
        'completed_at',
        'approved_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'approved_at' => 'datetime',
    ];
    
    public function assignedToUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function child()
    {
        return $this->belongsTo(User::class, 'child_id');
    }


}
