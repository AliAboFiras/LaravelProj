<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AvailableDay;
use App\Models\Service;
use App\Models\Slot;
use App\Models\User;
use App\Models\UserService;

class SlotController extends Controller
{
    public function AddMasterAvailableSlots(Request $request)
    // {
    //     $slots = [];
    //     $email = $request->email;
    //     $serviceName = $request->serviceName;
    //     $user = User::where('email', $email)->get()->first();
    //     $service = Service::where('name', $serviceName)->get()->first();
    //     $userService = UserService::where('user_id', $user->id)
    //         ->where('service_id', $service->id)
    //         ->get()->first();
    //     $year = $request->year;
    //     $r = 0;
    //     $yearSlot = AvailableDay::where('todayDate', 'LIKE', $year)->get();
    //     $month = $request->months;
    //     $day = $request->days;
    //     $times = $request->times;
    //     $dayID = '';
    //     $wh = '';
    //     $i = 0;
    //     foreach ($month as $m) {

    //         if ($m['state'] == "true") {

    //             foreach ($day as $d) {

    //                 if ($d['state'] == "true") {
    //                     $slots[] = "in day " . $d['name'];
    //                     $name = '';
    //                     $monthw = '';
    //                     $name = $d["name"];
    //                     $monthw = $m['name'];
    //                     $wh = $name . "%" . $monthw . "%" . $year;
    //                     $dayID = AvailableDay::where('todayDate', 'like', $wh)->get('id');
    //                     foreach ($dayID as $dID) {
    //                         $availableSlots = $request->times;
    //                         $slot = new Slot();
    //                         $slot->AvD_id = $dID['id'];
    //                         $slot->US_id = $user['id'];
    //                         $slot->AvailableSlots = json_encode($availableSlots);
    //                         $slot->save();
    //                     }
    //                 }
    //             }
    //         }
    //     }
    //     $result = [
    //         'status' => 200,
    //         'message' => $slots,
    //     ];
    //     return response()->json($result);
    // }
    {
        $email = $request->email;
        $serviceName = $request->serviceName;
        $user = User::where('email', $email)->get()->first();
        $service = Service::where('name', $serviceName)->get()->first();
        $userService = UserService::where('user_id', $user->id)
            ->where('service_id', $service->id)
            ->get()->first();
    }

    public function GetSlot()
    {
        $slots = Slot::get()->all();
        return response()->json($slots, 200);
    }

    public function GetAllSlots()
    {
        $slots = Slot::get()->all();
        return response()->json($slots);
    }

    public function GetAllUserSlots(Request $request)
    {
        $email = $request->email;
        $user = User::where('email', $email)->get()->first();
        $serviceName = $request->ServiceName;
        $service = Service::where('name', $serviceName)->get()->first();
        $userService = UserService::where('user_id', $user->id)->where('service_id', $service->id)->get()->first();
        $userServiceID = $userService->id;
        $slots = Slot::where('US_id', $userServiceID)->get();


        return $slots;
    }

    public function AddClientSlot(Request $request)
    {
        $serviceProviderEmail = $request->serviceProviderEmail;
        $year = $request->year;
        $month = $request->month;
        $day = $request->day;
        $service = $request->service;
        $time = $request->time;
        $clientEmail = $request->clientEmail;
        $client = User::where('email', $clientEmail)->get()->first();
        $service = Service::where('name', $service)->get()->first();
        $user = User::where('email', $serviceProviderEmail)->get()->first();
        $userService = UserService::where('user_id', $user->id)->where('service_id', $service->id)->get()->first();
        $dayString = $day . '%' . $month . '%' . $year;
        $availableDays = AvailableDay::where('todayDate', 'like', $dayString)->get();
        foreach ($availableDays as $availableDay) {
            $slot = Slot::where('US_id', $userService->id)->where('AvD_id', $availableDay->id)->get()->first();
            $AvSlots = json_decode($slot->AvailableSlots);
            foreach ($AvSlots as $AvSlot) {
                if ($AvSlot->time == $time) {
                    if (empty($AvSlot->client_id)) {
                        $AvSlot->client_id = $client->id;
                    } else {
                        $result = [
                            'message' => "the slot time is not empty"
                        ];
                        return response()->json($result);
                    }
                }
            }
        }
        $slot->AvailableSlots = json_encode($AvSlots);
        $slot->save();


        return $slot;
    }

    public function DeleteClientSlot(Request $request)
    {
        $serviceProviderEmail = $request->serviceProviderEmail;
        $year = $request->year;
        $month = $request->month;
        $day = $request->day;
        $service = $request->service;
        $time = $request->time;
        $clientEmail = $request->clientEmail;
        $client = User::where('email', $clientEmail)->get()->first();
        $service = Service::where('name', $service)->get()->first();
        $user = User::where('email', $serviceProviderEmail)->get()->first();
        $userService = UserService::where('user_id', $user->id)->where('service_id', $service->id)->get()->first();
        $dayString = $day . '%' . $month . '%' . $year;
        $availableDays = AvailableDay::where('todayDate', 'like', $dayString)->get();
        foreach ($availableDays as $availableDay) {
            $slot = Slot::where('US_id', $userService->id)->where('AvD_id', $availableDay->id)->get()->first();
            $AvSlots = json_decode($slot->AvailableSlots);
            foreach ($AvSlots as $AvSlot) {
                if ($AvSlot->time == $time) {
                    $AvSlot->client_id = '';
                }
            }
        }
        $slot->AvailableSlots = json_encode($AvSlots);
        $slot->save();
        return $slot;
    }
}
