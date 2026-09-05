<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Race extends Model
{
    // The columns in your NEW f1db2 schema
    protected $fillable = ['session_key', 'date_start', 'name', 'type'];
}
