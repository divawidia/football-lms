<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
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

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('prevent.back.history')->group(function (){
    Auth::routes();

//    Route::group(['middleware' => ['auth', 'web']], function () {
//        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
//
//        Route::group(['prefix' => 'akun', 'as' => 'profile.'], function () {
//            Route::get('/profile', [ProfileController::class, 'index'])->name('index');
//            Route::post('/update-photo', [ProfileController::class, 'updatePhoto'])->name('update.photo');
//            Route::put('/update-password', [ProfileController::class, 'updatePassword'])->name('update.password');
//            Route::put('/delete-photo', [ProfileController::class, 'deletePhoto'])->name('delete.photo');
//        });
//    });
});

//Route::get('/dashboard', function () {
//    return view('pages.admins.dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');
//
//Route::middleware('auth')->group(function () {
//    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
//});
//
//require __DIR__.'/auth.php';
Route::group(['middleware' => ['role:admin,web']], function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::resource('admin-managements', AdminController::class);
    Route::prefix('admin-managements/{admin}')->group(function (){
        Route::patch('deactivate', [AdminController::class, 'deactivate'])->name('deactivate-admin');
        Route::patch('activate', [AdminController::class, 'activate'])->name('activate-admin');
    });
});
//Route::group(['middleware' => ['role:coach,web']], function () {
//    Route::get('dashboard', [DashboardController::class, 'index'])->name('coach.dashboard');
//});
//Route::group(['middleware' => ['role:player,web']], function () {
//    Route::get('dashboard', [DashboardController::class, 'index'])->name('player.dashboard');
//});
