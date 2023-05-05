<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvailableDay extends Model
{
    use HasFactory;

    protected $fillable =['todayDate'];

    public function slots()
    {
        return $this->hasMany('App\Model\Slot','AvD_id');
    }
}
