<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    use HasFactory;

    protected $fillable =['US_id','AvD_id','AvailableSlots->time','AvailableSlots->state','AvailableSlots->client_id'];

    // protected $casts =['AvailableSlots'=>'json'];

    public function user_services(){
        return $this->belongsTo('App\Model\UserService','id');
    }


    public function available_days(){
        return $this->belongsTo('App\Model\AvailableDay','id');
    }
}
