<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyBettor extends Model
{
    protected $table = 'bettors';
    
    // Disable default timestamps (created_at, updated_at)
    public $timestamps = false;

    // No standard auto-incrementing primary key exists
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'userID',
        'score',
        'bets',
        'bonus',
        'sessionKey'
    ];
    
    // Optional: Cast bets to an array if it stores JSON data
    protected $casts = [
        'score' => 'float',
        'bonus' => 'float',
    ];
}
