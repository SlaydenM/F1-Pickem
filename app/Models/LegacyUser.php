<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // Extending Authenticatable in case you use this for auth
use Illuminate\Database\Eloquent\Model;

class LegacyUser extends Authenticatable
{
    protected $table = 'users';
    
    public $timestamps = false;

    // Override the default 'id' primary key
    protected $primaryKey = 'userID';

    protected $fillable = [
        'userID',
        'username',
        'password'
    ];
}