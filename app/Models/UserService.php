<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserService extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'service_id'];

    public function services()
    {
        return $this->belongsTo('App\Model\Service', 'service_id');
    }


    public function users()
    {
        return $this->belongsTo('App\Model\User', 'user_id');
    }


    public function slots()
    {
        return $this->hasMany('App\Model\Slot', 'US_id');
    }
}


