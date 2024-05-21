<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;

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

// Route::get('/', function () {
//     return view('home');
// });

Route::get('/', [WeatherController::class, 'getWeather']);
Route::post('/subscribe', [WeatherController::class, 'subscribe']);
// Route::get('/unsubscribe/{email}', [WeatherController::class, 'unsubscribe']);
Route::get('/unsubscribe/{email}/{location}', [WeatherController::class, 'unsubscribe'])->name('unsubscribe');


