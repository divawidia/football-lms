<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AttendanceReportController;
use App\Http\Controllers\Admin\CoachController;
use App\Http\Controllers\Admin\CompetitionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventScheduleController;
use App\Http\Controllers\Admin\GroupDivisionController;
use App\Http\Controllers\Admin\LeaderboardController;
use App\Http\Controllers\Admin\OpponentTeamController;
use App\Http\Controllers\Admin\PerformanceReportController;
use App\Http\Controllers\Admin\PlayerController;
use App\Http\Controllers\Admin\PlayerParentController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\TrainingVideoController;
use App\Http\Controllers\Admin\TrainingVideoLessonController;
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
            Route::get('coach-teams', [CoachController::class, 'coachTeams'])->name('coach-managements.coach-teams');
            Route::put('update-teams', [CoachController::class, 'updateTeams'])->name('coach-managements.updateTeams');
            Route::delete('remove-team/{team}', [CoachController::class, 'removeTeam'])->name('coach-managements.removeTeam');
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
        Route::get('create', [EventScheduleController::class, 'createTraining'])->name('training-schedules.create');
        Route::post('store', [EventScheduleController::class, 'storeTraining'])->name('training-schedules.store');

        Route::prefix('{schedule}')->group(function () {
            Route::get('', [EventScheduleController::class, 'showTraining'])->name('training-schedules.show');
            Route::get('edit', [EventScheduleController::class, 'editTraining'])->name('training-schedules.edit');
            Route::put('update', [EventScheduleController::class, 'updateTraining'])->name('training-schedules.update');
            Route::delete('destroy', [EventScheduleController::class, 'destroy'])->name('training-schedules.destroy');
            Route::patch('deactivate', [EventScheduleController::class, 'deactivateTraining'])->name('deactivate-training');
            Route::patch('activate', [EventScheduleController::class, 'activateTraining'])->name('activate-training');

            Route::get('edit-player-attendance/{player}', [EventScheduleController::class, 'getPlayerAttendance'])->name('training-schedules.player');
            Route::put('update-player-attendance/{player}', [EventScheduleController::class, 'updatePlayerAttendance'])->name('training-schedules.update-player');

            Route::get('edit-coach-attendance/{coach}', [EventScheduleController::class, 'getCoachAttendance'])->name('training-schedules.coach');
            Route::put('update-coach-attendance/{coach}', [EventScheduleController::class, 'updateCoachAttendance'])->name('training-schedules.update-coach');

            Route::post('create-note', [EventScheduleController::class, 'createNote'])->name('training-schedules.create-note');
            Route::get('edit-note/{note}', [EventScheduleController::class, 'editNote'])->name('training-schedules.edit-note');
            Route::put('update-note/{note}', [EventScheduleController::class, 'updateNote'])->name('training-schedules.update-note');
            Route::delete('delete-note/{note}', [EventScheduleController::class, 'destroyNote'])->name('training-schedules.destroy-note');
        });
    });

    Route::prefix('match-schedules')->group(function () {
        Route::get('', [EventScheduleController::class, 'indexMatch'])->name('match-schedules.index');
        Route::get('create', [EventScheduleController::class, 'createMatch'])->name('match-schedules.create');
        Route::get('get-competition-teams/{competition}', [EventScheduleController::class, 'getCompetitionTeam'])->name('match-schedules.get-competition-team');
        Route::get('get-friendlymatch-teams', [EventScheduleController::class, 'getFriendlyMatchTeam'])->name('match-schedules.get-friendlymatch-team');
        Route::post('store', [EventScheduleController::class, 'storeMatch'])->name('match-schedules.store');

        Route::prefix('{schedule}')->group(function () {
            Route::get('', [EventScheduleController::class, 'showMatch'])->name('match-schedules.show');
            Route::get('edit', [EventScheduleController::class, 'editMatch'])->name('match-schedules.edit');
            Route::put('update', [EventScheduleController::class, 'updateMatch'])->name('match-schedules.update');
            Route::delete('destroy', [EventScheduleController::class, 'destroy'])->name('match-schedules.destroy');
            Route::patch('end-match', [EventScheduleController::class, 'endMatch'])->name('end-match');
            Route::patch('activate', [EventScheduleController::class, 'activateMatch'])->name('activate-match');
            Route::get('get-assisted-player/{player}', [EventScheduleController::class, 'getAssistPlayer'])->name('get-assist-player');

            Route::get('edit-player-attendance/{player}', [EventScheduleController::class, 'getPlayerAttendance'])->name('match-schedules.player');
            Route::put('update-player-attendance/{player}', [EventScheduleController::class, 'updatePlayerAttendance'])->name('match-schedules.update-player');

            Route::get('edit-coach-attendance/{coach}', [EventScheduleController::class, 'getCoachAttendance'])->name('match-schedules.coach');
            Route::put('update-coach-attendance/{coach}', [EventScheduleController::class, 'updateCoachAttendance'])->name('match-schedules.update-coach');

            Route::post('match-scorer', [EventScheduleController::class, 'storeMatchScorer'])->name('match-schedules.store-match-scorer');
            Route::delete('match-scorer/{scorer}/destroy', [EventScheduleController::class, 'destroyMatchScorer'])->name('match-schedules.destroy-match-scorer');

            Route::post('create-note', [EventScheduleController::class, 'createNote'])->name('match-schedules.create-note');
            Route::get('edit-note/{note}', [EventScheduleController::class, 'editNote'])->name('match-schedules.edit-note');
            Route::put('update-note/{note}', [EventScheduleController::class, 'updateNote'])->name('match-schedules.update-note');
            Route::delete('delete-note/{note}', [EventScheduleController::class, 'destroyNote'])->name('match-schedules.destroy-note');

            Route::post('own-goal', [EventScheduleController::class, 'storeOwnGoal'])->name('match-schedules.store-own-goal');
            Route::delete('own-goal/{scorer}/destroy', [EventScheduleController::class, 'destroyOwnGoal'])->name('match-schedules.destroy-own-goal');

            Route::put('update-match-stats', [EventScheduleController::class, 'updateMatchStats'])->name('match-schedules.update-match-stats');

            Route::prefix('player-match-stats')->group(function () {
                Route::get('', [EventScheduleController::class, 'indexPlayerMatchStats'])->name('match-schedules.index-player-match-stats');
                Route::get('{player}', [EventScheduleController::class, 'getPlayerStats'])->name('match-schedules.show-player-match-stats');
                Route::put('{player}/update', [EventScheduleController::class, 'updatePlayerStats'])->name('match-schedules.update-player-match-stats');
            });
        });
    });

    Route::prefix('attendance-reports')->group(function () {
        Route::get('', [AttendanceReportController::class, 'index'])->name('attendance-report.index');

        Route::prefix('{player}')->group(function () {
            Route::get('', [AttendanceReportController::class, 'show'])->name('attendance-report.show');
            Route::get('training-history', [AttendanceReportController::class, 'trainingTable'])->name('attendance-report.trainingTable');
            Route::get('match-history', [AttendanceReportController::class, 'matchDatatable'])->name('attendance-report.matchDatatable');
        });
    });

    Route::prefix('performance-reports')->group(function () {
        Route::get('', [PerformanceReportController::class, 'index'])->name('performance-report.index');
    });

    Route::prefix('leaderboards')->group(function () {
        Route::get('', [LeaderboardController::class, 'index'])->name('leaderboards.index');
        Route::get('teams', [LeaderboardController::class, 'teamLeaderboard'])->name('leaderboards.teams');
        Route::get('players', [LeaderboardController::class, 'playerLeaderboard'])->name('leaderboards.players');
    });

    Route::prefix('training-videos')->group(function () {
        Route::get('', [TrainingVideoController::class, 'index'])->name('training-videos.index');
        Route::get('create', [TrainingVideoController::class, 'create'])->name('training-videos.create');
        Route::post('store', [TrainingVideoController::class, 'store'])->name('training-videos.store');

        Route::prefix('{trainingVideo}')->group(function () {
            Route::get('', [TrainingVideoController::class, 'show'])->name('training-videos.show');
            Route::get('edit', [TrainingVideoController::class, 'edit'])->name('training-videos.edit');
            Route::put('update', [TrainingVideoController::class, 'update'])->name('training-videos.update');

            Route::get('players', [TrainingVideoController::class, 'players'])->name('training-videos.players');
            Route::put('assign-players', [TrainingVideoController::class, 'assignPlayers'])->name('training-videos.assign-players');
            Route::put('remove-player/{player}', [TrainingVideoController::class, 'removePlayer'])->name('training-videos.remove-player');

            Route::prefix('lessons')->group(function () {
            Route::get('', [TrainingVideoLessonController::class, 'index'])->name('training-videos.lessons-index');
            Route::post('store', [TrainingVideoLessonController::class, 'store'])->name('training-videos.lessons-store');
                Route::prefix('{lesson}')->group(function () {
                    Route::get('', [TrainingVideoLessonController::class, 'show'])->name('training-videos.lessons-show');
                    Route::get('update', [TrainingVideoLessonController::class, 'update'])->name('training-videos.lessons-update');
                    Route::delete('destroy', [TrainingVideoLessonController::class, 'destroy'])->name('training-videos.lessons-destroy');
                    Route::patch('unpublish', [TrainingVideoLessonController::class, 'unpublish'])->name('training-videos.lessons-unpublish');
                    Route::patch('publish', [TrainingVideoLessonController::class, 'publish'])->name('training-videos.lessons-publish');
                });
            });
        });
    });
});
//Route::group(['middleware' => ['role:coach,web']], function () {
//    Route::get('dashboard', [DashboardController::class, 'index'])->name('coach.dashboard');
//});
//Route::group(['middleware' => ['role:player,web']], function () {
//    Route::get('dashboard', [DashboardController::class, 'index'])->name('player.dashboard');
//});
