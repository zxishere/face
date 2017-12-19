<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staffs';

    protected $guarded = ['_token'];

    public function subscribes()
    {
        return $this->morphMany('App\Models\Subscribe', 'subscribable');
    }

}