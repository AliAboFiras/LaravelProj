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
        $av->save();
        $result = [
            'message' => "success",
            'added'=>$av
        ];
        return response()->json($result);
    }
    public function IncreaseAvailableDayDates(Request $request)
    {
        $daysNum = $request->number;
        $thisDay = Carbon::today();
        $thisStringDay = $thisDay->toFormattedDayDateString();
        $todayID = AvailableDay::where('todayDate', $thisStringDay)->value('id');
        $lastDay = AvailableDay::orderBy('id', 'desc')->first();
        $daysDifference = $lastDay->id - $todayID ;
        for ($i = 0; $i <= $daysDifference; $i++) {
            $thisDay = $thisDay->addDay();
        }
        $thisNewStringDay = $thisDay->toFormattedDayDateString();
        $avDay = new AvailableDay();
        $avDay->todayDate = $thisNewStringDay;
        $avDay->save();
        for ($i = 1; $i < $daysNum; $i++) {
            $thisDay = $thisDay->addDay();
            $thisNewStringDay = $thisDay->toFormattedDayDateString();
            $avDay = new AvailableDay();
            $avDay->todayDate = $thisNewStringDay;
            $avDay->save();
            $lastVaThisday = $thisDay->toFormattedDayDateString();
        }



        $result = [
            'message' => "success",
            'lastVaThisday'=>$lastVaThisday
        ];
        return response()->json($result);
    }

    public function GetLastAvailableDay()
    {
        $lastDay = AvailableDay::orderBy('id', 'desc')->first();
        $thisStringDay = "2023-03-20";
        $todayID = AvailableDay::where('todayDate', $thisStringDay)->value('id');
        $daysDifference =  $lastDay->id - $todayID;
        $thisDay = Carbon::today();
        for ($i = 0; $i <= $daysDifference; $i++) {
            $thisDay = $thisDay->addDay();
        }

        $thisNewStringDay = $thisDay->toDateString();

        $result = [
            'message' => "success",
            'last date in database' => $thisDay->toDateString(),
            'today date' => $now = Carbon::now(),
            'last date id in database' => $lastDay->id,
            'difference' => $daysDifference,
            'll' => $thisNewStringDay
        ];
        return response()->json($result);
    }
}
