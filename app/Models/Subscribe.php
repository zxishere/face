<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Subscribe extends Model
{
    protected $table = 'subscribes';

    protected $guarded = ['_token'];

    public function subscribable()
    {
        return $this->morphTo();
    }

    public function subscriber()
    {
        return $this->hasOne('App\Models\Friend', 'id', 'friend_id');
    }

    public function scopeNeedPush($query, $type)
    {
        return $query->where('subscribable_type', $type)->whereDate('latest_push', '<', Carbon::today()->toDateString())->orWhereNull('latest_push')->get();
    }

}
