<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Winner extends Model
{
    // The columns in your NEW f1db2 schema
    protected $fillable = ['driver_id', 'position', 'session_key'];

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
}
