<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CoachController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PlayerController;
use App\Http\Controllers\Admin\PlayerParentController;
use App\Http\Controllers\Admin\TeamController;
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
    Route::delete('parents/{parent}/destroy', [PlayerParentController::class, 'destroy'])->name('player-parents.destroy');
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
    });

    Route::prefix('coach-managements')->group(function (){
       Route::get('', [CoachController::class, 'index'])->name('coach-managements.index');
        Route::get('create', [CoachController::class, 'create'])->name('coach-managements.create');
        Route::post('store', [CoachController::class, 'store'])->name('coach-managements.store');
        Route::prefix('{coach}')->group(function () {
            Route::get('', [CoachController::class, 'show'])->name('coach-managements.show');
            Route::get('edit', [CoachController::class, 'edit'])->name('coach-managements.edit');
            Route::put('update', [CoachController::class, 'update'])->name('coach-managements.update');
            Route::delete('destroy', [CoachController::class, 'destroy'])->name('coach-managements.destroy');
            Route::patch('deactivate', [CoachController::class, 'deactivate'])->name('deactivate-coach');
            Route::patch('activate', [CoachController::class, 'activate'])->name('activate-coach');
            Route::get('change-password', [CoachController::class, 'changePasswordPage'])->name('coach-managements.change-password-page');
            Route::patch('change-password', [CoachController::class, 'changePassword'])->name('coach-managements.change-password');
        });
    });

    Route::prefix('team-managements')->group(function (){
        Route::get('', [TeamController::class, 'index'])->name('team-managements.index');
        Route::get('create', [TeamController::class, 'create'])->name('team-managements.create');
        Route::post('store', [TeamController::class, 'store'])->name('team-managements.store');
        Route::prefix('{team}')->group(function () {
            Route::get('', [TeamController::class, 'show'])->name('team-managements.show');
            Route::get('edit', [TeamController::class, 'edit'])->name('team-managements.edit');
            Route::put('update', [TeamController::class, 'update'])->name('team-managements.update');
            Route::delete('destroy', [TeamController::class, 'destroy'])->name('team-managements.destroy');
            Route::patch('deactivate', [TeamController::class, 'deactivate'])->name('deactivate-team');
            Route::patch('activate', [TeamController::class, 'activate'])->name('activate-team');
            Route::get('players', [TeamController::class, 'teamPlayers'])->name('team-managements.teamPlayers');
            Route::get('coaches', [TeamController::class, 'teamCoaches'])->name('team-managements.teamCoaches');
        });
    });
});
//Route::group(['middleware' => ['role:coach,web']], function () {
//    Route::get('dashboard', [DashboardController::class, 'index'])->name('coach.dashboard');
//});
//Route::group(['middleware' => ['role:player,web']], function () {
//    Route::get('dashboard', [DashboardController::class, 'index'])->name('player.dashboard');
//});
