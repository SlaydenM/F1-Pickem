<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyRace extends Model
{
    protected $table = 'races';
    
    public $timestamps = false;

    // Assuming sessionKey is the unique identifier for a race
    protected $primaryKey = 'sessionKey';
    public $incrementing = false; // Set to true if sessionKey auto-increments in the DB

    protected $fillable = [
        'sessionKey',
        'dateStart',
        'name',
        'type'
    ];

    // Cast the timestamp to a Carbon date object
    protected $casts = [
        'dateStart' => 'datetime',
    ];
}
