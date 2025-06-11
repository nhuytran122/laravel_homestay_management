<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    protected $fillable = ['title', 'description', 'is_hidden'];

    protected $casts = [
        'is_hidden' => 'boolean',
    ];
}