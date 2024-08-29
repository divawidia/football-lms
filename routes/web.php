<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PlayerController;
use App\Http\Controllers\Admin\PlayerParentController;
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
Route::group(['middleware' => ['role:admin,web', 'auth']], function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::resource('admin-managements', AdminController::class);
    Route::prefix('admin-managements/{admin}')->group(function (){
        Route::patch('deactivate', [AdminController::class, 'deactivate'])->name('deactivate-admin');
        Route::patch('activate', [AdminController::class, 'activate'])->name('activate-admin');
        Route::get('change-password', [AdminController::class, 'changePasswordPage'])->name('admin-managements.change-password-page');
        Route::patch('change-password', [AdminController::class, 'changePassword'])->name('admin-managements.change-password');
    });

    Route::resource('player-managements', PlayerController::class);
    Route::prefix('player-managements/{player}')->group(function (){
        Route::patch('deactivate', [PlayerController::class, 'deactivate'])->name('deactivate-player');
        Route::patch('activate', [PlayerController::class, 'activate'])->name('activate-player');
        Route::get('change-password', [PlayerController::class, 'changePasswordPage'])->name('player-managements.change-password-page');
        Route::patch('change-password', [PlayerController::class, 'changePassword'])->name('player-managements.change-password');

        Route::get('parents', [PlayerParentController::class, 'index'])->name('player-parents.index');
        Route::get('parents/create', [PlayerParentController::class, 'create'])->name('player-parents.create');
        Route::post('parents/store', [PlayerParentController::class, 'store'])->name('player-parents.store');
        Route::get('parents/{parent}/edit', [PlayerParentController::class, 'edit'])->name('player-parents.edit');
        Route::put('parents/{parent}/update', [PlayerParentController::class, 'update'])->name('player-parents.update');
        Route::delete('parents/{parent}/destroy', [PlayerParentController::class, 'destroy'])->name('player-parents.destroy');
    });
});
//Route::group(['middleware' => ['role:coach,web']], function () {
//    Route::get('dashboard', [DashboardController::class, 'index'])->name('coach.dashboard');
//});
//Route::group(['middleware' => ['role:player,web']], function () {
//    Route::get('dashboard', [DashboardController::class, 'index'])->name('player.dashboard');
//});
