<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    protected $fillable = [
        'family_id',
        'title',
        'description',
        'cost',
    ];
}