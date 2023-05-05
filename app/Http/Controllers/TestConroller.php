<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TestConroller extends Controller
{
    public function index(){
        $now = Carbon::now();
        $today = $now->toDateString();
        $result=[
            'time' => $now,
            'day' => $today
        ];

        return response()->json($result);
    }
}
