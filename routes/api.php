<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TripController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Models\Trip;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);


// Protected routes
Route::group(['middleware'=> ['auth:sanctum']], function () {
    Route::get('/trips', [TripController::class, 'getAllTripsFilteredByStartEndStations']);
    Route::post('/book', [TripController::class, 'bookTrip']);
});

