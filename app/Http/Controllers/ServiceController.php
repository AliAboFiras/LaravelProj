<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    public function AddGeneralService(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:services,name'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->messages()
            ]);
        }
        else{
            $service = Service::create([
                'name'=>$request->name,
            ]);
        }
        return response()->json([
            'status'=> 200,
            'username'=>$service->name,
            'message'=>'service added successfuly'
        ]);
    }

    public function ShowGeneralServices(){
        $service = Service::all('id','name');
        $result = [$service];
        return response()->json($service);
    }
}
