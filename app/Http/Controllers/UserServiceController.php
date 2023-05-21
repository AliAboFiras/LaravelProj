<?php

namespace App\Http\Controllers;

use App\Models\AvailableDay;
use App\Models\Service;
use App\Models\User;
use App\Models\UserService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserServiceController extends Controller
{

    public function AddUserService(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'serviceName' => 'required',
            'userEmail' => 'required|email',
            'location' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->messages()
            ]);
        } else {
            $sname = $request->serviceName;
            $uemail = $request->userEmail;
            $serviceId = Service::where('name', $sname)->value('id');
            $userId = User::where('email', $uemail)->value('id');
            $user_service = new UserService();
            $user_service->user_id = $userId;
            $user_service->service_id = $serviceId;
            $user_service->location = $request->location;
            $user_service->save();
        }
        $result = [
            'status' => 200,
            'userServiceName' => $sname,
            'message' => 'the service added successfuly ',
        ];
        return response()->json($result);
    }

    public function ShowAllUsersServices()
    {
        $IDs = UserService::get('id');
        $result = [];
        foreach ($IDs as $ID) {
            $uID = UserService::where('id', $ID->id)->get()->value('user_id');
            $sID = UserService::where('id', $ID->id)->get()->value('service_id');
            $service = Service::where('id', $sID)->get()->value('name');
            $user_firstN = User::where('id', $uID)->get()->value('first_name');
            $user_lastN = User::where('id', $uID)->get()->value('last_name');
            $user = $user_firstN . " " . $user_lastN;
            $result[] = [
                'user name' => $user,
                'service' => $service
            ];
        }

        $results = [
            'message' => 'successful',
            'result' => $result
        ];

        return response()->json($results);
    }



    public function ShowUserServices(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->messages()
            ]);
        } else {
            $userID = User::where('email', $request->email)->get()->value('id');
            $IDs = UserService::get('id');
            $result = [];
            foreach ($IDs as $ID) {
                $userService = UserService::where('user_id', $userID)->where('id', $ID->id)->get()->value('service_id');
                if ($userService) {
                    $result[] = [Service::where('id', $userService)->get('name')];
                }
            }

            $results = [
                'message' => 'successful',
                'result' => $result
            ];

            return response()->json($results);
        }
    }


    public function DeleteUserService(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service' => 'required',
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->messages()
            ]);
        } else {

            $user = User::where('email', $request->email)->get()->first();
            $service = Service::where('name', $request->service)->get()->first();
            $userID = $user->id;
            $serviceID = $service->id;
            $userService = UserService::where('user_id', $userID)->where('service_id', $serviceID)->get()->first();
            $userService->delete();
            return response()->json('success');
        }
    }

    public function Show()
    {
        $day = "Thu%2023";
        $dayS = AvailableDay::where('todaydate', 'LIKE', $day)->get();
        return $dayS;
    }


    public function SearchByLocation(Request $request)
    {
        $location = $request->location;
        $userServices = UserService::where('location', "like", $location)->get();
        $res = [];
        if (count($userServices) > 0) {
            foreach ($userServices as $userService) {
                $user = User::where('id', $userService->user_id)->get()->first();
                $service = Service::where('id', $userService->service_id)->get()->first();
                $res[] = [
                    'service' => $service->name,
                    'user' => $user->first_name . " " . $user->last_name,
                    'id' => $userService->id,
                ];
            }
            $result = [
                'status' => 200,
                'message' => "this is your slots for today",
                'data' => $res,
            ];
            return response()->json($result);
        } else {

            $result = [
                'status' => 400,
                'message' => "we dont find what you looking for",
            ];
            return response()->json($result);
        }
    }

    public function SearchByName(Request $request)
    {
        $name = $request->name;
        $res = [];
        $users = User::where('first_name', $name)->orwhere('last_name', $name)->get();
        if (empty($users)) {
        } else {
            foreach ($users as $user) {
                $userservice = null;
                $userservice = UserService::where('user_id', $user->id)->get()->first();
                if (empty($userservice)) {
                    null;
                } else {
                    $service = Service::where('id', $userservice->service_id)->get()->first();
                    $res[] = [
                        'service' => $service->name,
                        'user' => $user->first_name . " " . $user->last_name,
                        'docID' => $userservice->id,
                    ];
                }
            }
        }
        if (count($res) > 0) {
            $result = [
                'status' => 200,
                'message' => "this is your slots for today",
                'data' => $res,
            ];
            return response()->json($result);
        } else {

            $result = [
                'status' => 400,
                'message' => "we dont find what you looking for",
            ];
            return response()->json($users);
        }
    }
}
