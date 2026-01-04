<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'invite_code',
    ];

    // users とのリレーション
    public function users()
    {
        return $this->hasMany(User::class);
    }
}