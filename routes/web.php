<?php

use App\Http\Controllers\CandidateController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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
Route::get('/signup', function () {
    return view ('signup');
})->name('signup');

Route::post('/registration', [UserController::class, 'store'])->name('registration');

Route::get('/', [CandidateController::class, 'index'])->middleware('voter')->name('main');
Route::get('/details/{id}', [CandidateController::class, 'show'])->middleware('voter');
Route::put('/details/update/{id}', [CandidateController::class, 'update'])->middleware('voter')->name('candidate.vote');
Route::get('/stats', [CandidateController::class, 'stats'])->middleware('admin')->name('stats');

Route::get('/attendanceList', [UserController::class, 'attendanceList'])->middleware('admin')->name('attendanceList');
Route::get('/voterSearch', [UserController::class, 'voterSearch'])->middleware('admin')->name('voterSearch');
Route::post('/attendanceCheck', [UserController::class, 'check'])->middleware('admin')->name('attendanceCheck');
Route::get('/manualCheck/{id}', [UserController::class, 'attendance' ])->middleware('admin')->name('manualCheck');
Route::get('/uncheck/{id}', [UserController::class, 'uncheck' ])->middleware('admin')->name('uncheck');

/**
 * Google Login
 */
Route::controller(UserController::class)->group(function() {
    Route::get('auth/google','googleLogin')->name('auth.google');
Route::get('auth/google-callback', 'googleAuthentication')->name('auth.google-callback');
});



// Route::get('/main2', function () {
//     return view('main2');
// });

Route::get('/finish', function () {
    return view ('finish');
})->middleware('voter');

Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});
