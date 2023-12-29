<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VeritMiddleware;
use App\Http\Controllers\MedicineController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::group(['middleware' => ['jwt.verify']], function() {
   // Route::get('user', 'UserController@getAuthenticatedUser');
    //Route::get('closed', 'DataController@closed');
    Route::post('/Create',[MedicineController::class,'Create']);
    Route::get('/GetAll',[MedicineController::class,'getall']);
    Route::post('/Update/{$id}',[MedicineController::class,'Updata']);
    Route::delete('/Delete',[MedicineController::class,'Delete']);
    Route::get('/Getid/{$id}',[MedicineController::class,'Getid']);



});
