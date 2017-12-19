<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class  Friend extends Model
{
    protected $table = 'friends';

    protected $guarded = ['_token'];
}
