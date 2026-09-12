<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    //
    protected $fillable = ['notify_picks', 'notify_races', 'notify_results', 'notify_others'];
}
