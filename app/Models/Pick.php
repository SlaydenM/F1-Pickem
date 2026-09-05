<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pick extends Model
{
    protected $fillable = ['user_id', 'score', 'd1_id', 'd2_id', 'd3_id', 'bonus', 'session_key'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function d1()
    {
        return $this->belongsTo(Driver::class, 'd1_id');
    }

    public function d2()
    {
        return $this->belongsTo(Driver::class, 'd2_id');
    }

    public function d3()
    {
        return $this->belongsTo(Driver::class, 'd3_id');
    }

    public function getPicks(): array
    {
        return [$this->d1, $this->d2, $this->d3];
    }
}
