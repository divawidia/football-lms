<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CoachController;
use App\Http\Controllers\Admin\CompetitionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventScheduleController;
use App\Http\Controllers\Admin\GroupDivisionController;
use App\Http\Controllers\Admin\OpponentTeamController;
use App\Http\Controllers\Admin\PlayerController;
use App\Http\Controllers\Admin\PlayerParentController;
use App\Http\Controllers\Admin\TeamController;
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
        Route::get('player-teams', [PlayerController::class, 'playerTeams'])->name('player-managements.playerTeams');
        Route::put('update-teams', [PlayerController::class, 'updateTeams'])->name('player-managements.updateTeams');
        Route::delete('remove-team/{team}', [PlayerController::class, 'removeTeam'])->name('player-managements.removeTeam');

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

        Route::prefix('our-teams')->group(function () {
            Route::get('create', [TeamController::class, 'create'])->name('team-managements.create');
            Route::post('store', [TeamController::class, 'store'])->name('team-managements.store');
            Route::post('api/store', [TeamController::class, 'apiStore'])->name('team-managements.apiStore');
            Route::prefix('{team}')->group(function () {
                Route::get('', [TeamController::class, 'show'])->name('team-managements.show');
                Route::get('edit', [TeamController::class, 'edit'])->name('team-managements.edit');
                Route::put('update', [TeamController::class, 'update'])->name('team-managements.update');
                Route::delete('destroy', [TeamController::class, 'destroy'])->name('team-managements.destroy');
                Route::patch('deactivate', [TeamController::class, 'deactivate'])->name('deactivate-team');
                Route::patch('activate', [TeamController::class, 'activate'])->name('activate-team');
                Route::get('players', [TeamController::class, 'teamPlayers'])->name('team-managements.teamPlayers');
                Route::get('coaches', [TeamController::class, 'teamCoaches'])->name('team-managements.teamCoaches');
                Route::get('edit-players', [TeamController::class, 'addPlayerTeam'])->name('team-managements.addPlayerTeam');
                Route::put('update-players', [TeamController::class, 'updatePlayerTeam'])->name('team-managements.updatePlayerTeam');
                Route::get('edit-coaches', [TeamController::class, 'addCoachesTeam'])->name('team-managements.addCoachesTeam');
                Route::put('update-coaches', [TeamController::class, 'updateCoachTeam'])->name('team-managements.updateCoachTeam');
                Route::put('remove-player/{player}', [TeamController::class, 'removePlayer'])->name('team-managements.removePlayer');
                Route::put('remove-coach/{coach}', [TeamController::class, 'removeCoach'])->name('team-managements.removeCoach');
            });
        });
        Route::prefix('opponent-teams')->group(function () {
            Route::get('', [OpponentTeamController::class, 'index'])->name('opponentTeam-managements.index');
            Route::get('create', [OpponentTeamController::class, 'create'])->name('opponentTeam-managements.create');
            Route::post('store', [OpponentTeamController::class, 'store'])->name('opponentTeam-managements.store');
            Route::post('api/store', [OpponentTeamController::class, 'apiStore'])->name('opponentTeam-managements.apiStore');
            Route::prefix('{team}')->group(function () {
                Route::get('', [OpponentTeamController::class, 'show'])->name('opponentTeam-managements.show');
                Route::get('edit', [OpponentTeamController::class, 'edit'])->name('opponentTeam-managements.edit');
                Route::put('update', [OpponentTeamController::class, 'update'])->name('opponentTeam-managements.update');
                Route::delete('destroy', [OpponentTeamController::class, 'destroy'])->name('opponentTeam-managements.destroy');
            });
        });
    });

    Route::prefix('competition-managements')->group(function () {
        Route::get('', [CompetitionController::class, 'index'])->name('competition-managements.index');
        Route::get('create', [CompetitionController::class, 'create'])->name('competition-managements.create');
        Route::post('store', [CompetitionController::class, 'store'])->name('competition-managements.store');
        Route::prefix('{competition}')->group(function () {
            Route::get('', [CompetitionController::class, 'show'])->name('competition-managements.show');
            Route::get('edit', [CompetitionController::class, 'edit'])->name('competition-managements.edit');
            Route::put('update', [CompetitionController::class, 'update'])->name('competition-managements.update');
            Route::delete('destroy', [CompetitionController::class, 'destroy'])->name('competition-managements.destroy');
            Route::patch('deactivate', [CompetitionController::class, 'deactivate'])->name('deactivate-competition');
            Route::patch('activate', [CompetitionController::class, 'activate'])->name('activate-competition');

            Route::prefix('group-division')->group(function () {
                Route::get('create', [GroupDivisionController::class, 'create'])->name('division-managements.create');
                Route::post('store', [GroupDivisionController::class, 'store'])->name('division-managements.store');
                Route::prefix('{group}')->group(function () {
                    Route::get('', [GroupDivisionController::class, 'index'])->name('division-managements.index');
                    Route::get('edit', [GroupDivisionController::class, 'edit'])->name('division-managements.edit');
                    Route::put('update', [GroupDivisionController::class, 'update'])->name('division-managements.update');
                    Route::delete('destroy', [GroupDivisionController::class, 'destroy'])->name('division-managements.destroy');
                    Route::get('add-team', [GroupDivisionController::class, 'addTeam'])->name('division-managements.addTeam');
                    Route::post('store-team', [GroupDivisionController::class, 'storeTeam'])->name('division-managements.storeTeam');
                    Route::put('remove-team/{team}', [GroupDivisionController::class, 'removeTeam'])->name('division-managements.removeTeam');
                });
            });
        });
    });

    Route::prefix('training-schedules')->group(function (){
        Route::get('', [EventScheduleController::class, 'indexTraining'])->name('training-schedules.index');
    });
});
//Route::group(['middleware' => ['role:coach,web']], function () {
//    Route::get('dashboard', [DashboardController::class, 'index'])->name('coach.dashboard');
//});
//Route::group(['middleware' => ['role:player,web']], function () {
//    Route::get('dashboard', [DashboardController::class, 'index'])->name('player.dashboard');
//});
