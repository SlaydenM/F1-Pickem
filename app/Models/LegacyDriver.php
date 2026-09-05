<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyDriver extends Model
{
    protected $table = 'drivers';
    
    public $timestamps = false;

    // No standard primary key
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'number',
        'name',
        'team',
        'position',
        'sessionKey'
    ];
    
    protected $casts = [
        'number' => 'integer',
        'position' => 'integer',
        'sessionKey' => 'integer',
    ];
}
