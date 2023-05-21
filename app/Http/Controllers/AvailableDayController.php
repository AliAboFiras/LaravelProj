<?php

namespace App\Http\Controllers;

use App\Models\AvailableDay;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AvailableDayController extends Controller
{

    public function InsertTodayDate(){
        $today = Carbon::today();
        $t = $today->toFormattedDayDateString();
        $av = new AvailableDay();
        $av->todayDate = $t ;
        // $av->save();
        $result = [
            'message' => "success",
            'added'=>$today
        ];
        return response()->json($result);
    }

    public function IncreaseAvailableDayDates(Request $request)
    {
        $dates = $request->dates;
        foreach ($dates as $date) {
            $newDay = new AvailableDay();
            $newDay->todayDate = $date ;
            $newDay->save();
        }
        $result = [
            'status' => 200,
            'message' => "success",
            'data' => "this is your last available day in Database " . $newDay->todayDate
        ];
        return response()->json($result);
    }

    public function GetLastAvailableDay()
    {
        $lastDay = AvailableDay::orderBy('id', 'desc')->first();
        $result = [
            'status' => 200,
            'message' => "this is your last available day in Database",
            'data' => $lastDay->todayDate
        ];
        return response()->json($result);
    }
}
