<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AvailableDay;
use App\Models\Service;
use App\Models\Slot;
use App\Models\User;
use App\Models\UserService;
use Illuminate\Support\Carbon;

class SlotController extends Controller
{
    public function AddMasterAvailableSlots(Request $request)
    {
        //Get User and Service
        $email = $request->email;
        $serviceName = $request->serviceName;
        $user = User::where('email', $email)->get()->first();
        $service = Service::where('name', $serviceName)->get()->first();
        $userService = UserService::where('user_id', $user->id)
            ->where('service_id', $service->id)
            ->get()->first();

        //Get Data From API
        $recievedDays = $request->dates;
        $recievedTimes = $request->times;

        //make Times Columns
        foreach ($recievedTimes as $slotTime) {
            $availableSlots[] = ['time' => $slotTime, 'client_id' => ''];
        }

        //Make Days Records
        foreach ($recievedDays as $rDay) {
            $date = $rDay;
            $day = AvailableDay::where('todayDate', $date)->get()->first();
            $slot = new Slot();
            $slot->US_id = $userService->id;
            $slot->AvD_id = $day->id;
            $slot->AvailableSlots = json_encode($availableSlots);
            $slot->save();
            $slots[] = $slot;
        }
        $result = [
            'status' => 200,
            'message' => "success",
            'data' => $slots
        ];
        return response()->json($result);
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

    public function GetMasterTodaySlots(Request $request)
    {
        //get Request Parameters
        $email = $request->email;
        $serviceName = $request->ServiceName;
        $today = $request->date;
        //get user and service
        $user = User::where('email', $email)->get()->first();
        $service = Service::where('name', $serviceName)->get()->first();
        //get userServisce
        $userService = UserService::where('user_id', $user->id)->where('service_id', $service->id)->get()->first();
        //get today
        $todayDate = AvailableDay::where('todayDate', $today)->get()->first();
        //get Today slots
        $slots = Slot::where('US_id', $userService->id)
            ->where('AvD_id', $todayDate->id)
            ->get()->first();
        $result = [
            'status' => 200,
            'message' => "this is your slots for today",
            'data' => $slots->AvailableSlots,
        ];
        return response()->json($result);
    }

    public function CheckTodayClientSlot(Request $request)
    {
        $email = $request->email;
        $reqToday = $request->today;
        $user = User::where('email', $email)->get()->first();
        $today = AvailableDay::where('todayDate', $reqToday)->get()->first();
        $slots = Slot::where('AvD_id', $today->id)->get();
        if (!empty($slots)) {
            foreach ($slots as $slot) {
                $Av_slots = json_decode($slot->AvailableSlots);
                foreach ($Av_slots as $Av_slot) {
                    if (!empty($Av_slot->client_id)) {
                        if ($Av_slot->client_id = $user->id) {
                            $userService = UserService::where('id', $slot->US_id)->get()->first();
                            $service = Service::where('id', $userService->service_id)->get()->first();
                            $serviceProvider = User::where('id', $userService->user_id)->get()->first();
                            $res[] = [
                                'time' => $Av_slot->time,
                                'doctor' => $service->name . " : " . $serviceProvider->first_name . " " . $serviceProvider->last_name,
                                'location' => $userService->location,
                            ];
                        }
                    }
                }
            }
        }
        if (!empty($res)) {
            $result = [
                'status' => 200,
                'message' => "this is your slots for today",
                'data' => $res,
            ];
            return response()->json($result);
        } else {
            $result = [
                'status' => 400,
                'message' => "we dont find any slot for today",
            ];
            return response()->json($result);
        }
    }

    public function VisitDoctorProfile(Request $request)
    {
        $docServiceID = $request->docID;
        $docService = UserService::where('id', $docServiceID)->get()->first();
        $doctor = User::where('id', $docService->user_id)->get()->first();
        $service = Service::where('id', $docService->service_id)->get()->first();
        $res = [
            'fullName' => $doctor->first_name . ' ' . $doctor->last_name,
            'serviceName' => $service->name,
            'description' => $docService->description,
            'location' => $docService->location,
        ];
        $result = [
            'status' => 200,
            'message' => "Welcome to my profile",
            'data' => $res,
        ];
        return response()->json($result);
    }

    public function viewTodaySlot(Request $request)
    {
        $email = $request->email;
        $client = User::where('email', $email)->get()->first();
        $docServiceID = $request->docID;
        $todayDate = $request->date;
        $docService = UserService::where('id', $docServiceID)->get()->first();
        $date = AvailableDay::where('todayDate', $todayDate)->get()->first();
        $slot = Slot::where('US_id', $docService->id)->where('AvD_id', $date->id)->get()->first();
        if (!empty($slot)) {
            $Av_slots = json_decode($slot->AvailableSlots);
            foreach ($Av_slots as $Av_slot) {
                if (!empty($Av_slot->client_id)) {
                    $res[] = [
                        'time' => $Av_slot->time,
                        'state' => false
                    ];
                } else {
                    $res[] = [
                        'time' => $Av_slot->time,
                        'state' => true
                    ];
                }
            }
            $result = [
                'status' => 200,
                'message' => "this is slmot state for your date",
                'data' => $res,
                'dateID' => $slot->id,
            ];
            return response()->json($result);
        } else {
            $result = [
                'status' => 400,
                'message' => "لا توجد خانات للتاريخ الذي اخترته",
            ];
            return response()->json($result);
        }
    }


    public function addClientSlot(Request $request)
    {
        $dateID = $request->dateID;
        $time = $request->time;
        $email = $request->email;
        $user = User::where('email', $email)->get()->first();
        $slot = Slot::where('id', $dateID)->get()->first();
        if (!empty($slot)) {
            $Av_slots = json_decode($slot->AvailableSlots);
            foreach ($Av_slots as $Av_slot) {
                if ($Av_slot->time = $time) {
                    $Av_slot->client_id = $user->id;
                    break;
                }
            }
            $slot->AvailableSlots = $Av_slots;
            $slot->save();
            $result = [
                'status' => 200,
                'message' => "Booking Done",
            ];
            return response()->json($result);
        }
        $result = [
            'status' => 400,
            'message' => "Sorry, an error has occurred. Please wait for a while and then resend the request after verifying the accuracy of the information you have submitted.",
        ];
        return response()->json($result);
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
