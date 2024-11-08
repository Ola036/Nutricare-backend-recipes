<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Recipe extends Model
{
    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'mongodb';

    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = ['user_id', 'ingredients', 'calories', 'response', 'favorite'];

    /**
     * The attributes that should be cast.
     * 
     * @var array
     */
    protected $casts = [
        'calories' => 'integer',
        'favorite' => 'boolean',
    ];
}
