<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AvailableDayController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SlotController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestConroller;
use App\Http\Controllers\UserServiceController;
use App\Models\Appointment;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});




Route::post('register', [UserController::class, 'Register']);
Route::post('login', [UserController::class, 'Login']);
Route::middleware('auth:sanctum')->post('upgrade', [UserController::class, 'Upgrade']);
Route::middleware('auth:sanctum')->post('upgradeee', [UserController::class, 'Upgradeee']);
Route::middleware('auth:sanctum')->post('upgradee', [UserController::class, 'Upgradee']);

Route::get('time', [TestConroller::class, 'index']);

Route::get('insertTodayDate', [AvailableDayController::class, 'InsertTodayDate']);
Route::post('increaseAvailableDayDates', [AvailableDayController::class, 'IncreaseAvailableDayDates']);
Route::get('getLastAvailableDay', [AvailableDayController::class, 'GetLastAvailableDay']);

Route::post('addGeneralService', [ServiceController::class, 'AddGeneralService']);
Route::get('showGeneralServices', [ServiceController::class, 'ShowGeneralServices']);

Route::middleware('auth:sanctum')->post('addUserService', [UserServiceController::class, 'AddUserService']);
Route::get('showAllUsersServices', [UserServiceController::class, 'ShowAllUsersServices']);
Route::post('deleteUserService', [UserServiceController::class, 'DeleteUserService']);
Route::post('showUserServices', [UserServiceController::class, 'ShowUserServices']);

Route::middleware('auth:sanctum')->post('addMasterAvailableSlots',[SlotController::class , 'AddMasterAvailableSlots']);
Route::middleware('auth:sanctum')->get('getSlot',[SlotController::class , 'GetSlot']);
Route::get('getAllSlots',[SlotController::class , 'GetAllSlots']);
Route::post('getAllUserSlots',[SlotController::class , 'GetAllUserSlots']);
Route::post('addClientSlot',[SlotController::class , 'AddClientSlot']);
Route::post('deleteClientSlot',[SlotController::class , 'DeleteClientSlot']);
