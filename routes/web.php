<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AttendanceReportController;
use App\Http\Controllers\Admin\CoachController;
use App\Http\Controllers\Admin\CompetitionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Coach\DashboardController as CoachDashboardController;
use App\Http\Controllers\Admin\EventScheduleController;
use App\Http\Controllers\Admin\GroupDivisionController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\LeaderboardController;
use App\Http\Controllers\Admin\OpponentTeamController;
use App\Http\Controllers\Admin\PerformanceReportController;
use App\Http\Controllers\Admin\PlayerController;
use App\Http\Controllers\Coach\PlayerController as CoachPlayerController;
use App\Http\Controllers\Admin\PlayerParentController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\TaxController;
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
Route::group(['middleware' => ['auth']], function () {

//    Route::group(['middleware' => ['role:admin|coach,web']], function () {
//        Route::prefix('player-managements')->group(function () {
//            Route::get('', [PlayerController::class, 'index'])->name('player-managements.index');
//            Route::prefix('{player}')->group(function () {
//                Route::get('', [PlayerController::class, 'index'])->name('player-managements.index');
//            });
//        });
//    });

    Route::group(['middleware' => ['role:admin,web']], function () {

        Route::prefix('admin')->group(function () {
            Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

            Route::resource('admin-managements', AdminController::class);
            Route::prefix('admin-managements/{admin}')->group(function () {
                Route::patch('deactivate', [AdminController::class, 'deactivate'])->name('deactivate-admin');
                Route::patch('activate', [AdminController::class, 'activate'])->name('activate-admin');
                Route::get('change-password', [AdminController::class, 'changePasswordPage'])->name('admin-managements.change-password-page');
                Route::patch('change-password', [AdminController::class, 'changePassword'])->name('admin-managements.change-password');
            });

            Route::prefix('player-managements')->group(function () {
                Route::get('', [PlayerController::class, 'index'])->name('player-managements.index');
                Route::post('', [PlayerController::class, 'store'])->name('player-managements.store');
                Route::get('create', [PlayerController::class, 'create'])->name('player-managements.create');

                Route::prefix('{player}')->group(function () {
                    Route::get('', [PlayerController::class, 'show'])->name('player-managements.show');
                    Route::get('edit', [PlayerController::class, 'edit'])->name('player-managements.edit');
                    Route::put('', [PlayerController::class, 'update'])->name('player-managements.update');
                    Route::delete('', [PlayerController::class, 'destroy'])->name('player-managements.destroy');

                    Route::patch('deactivate', [PlayerController::class, 'deactivate'])->name('deactivate-player');
                    Route::patch('activate', [PlayerController::class, 'activate'])->name('activate-player');
                    Route::get('change-password', [PlayerController::class, 'changePasswordPage'])->name('player-managements.change-password-page');
                    Route::patch('change-password', [PlayerController::class, 'changePassword'])->name('player-managements.change-password');
                    Route::get('player-teams', [PlayerController::class, 'playerTeams'])->name('player-managements.playerTeams');
                    Route::put('update-teams', [PlayerController::class, 'updateTeams'])->name('player-managements.updateTeams');
                    Route::delete('remove-team/{team}', [PlayerController::class, 'removeTeam'])->name('player-managements.removeTeam');

                    Route::prefix('parents')->group(function () {
                        Route::get('', [PlayerParentController::class, 'index'])->name('player-parents.index');
                        Route::get('create', [PlayerParentController::class, 'create'])->name('player-parents.create');
                        Route::post('store', [PlayerParentController::class, 'store'])->name('player-parents.store');
                        Route::prefix('{parent}')->group(function () {
                            Route::delete('destroy', [PlayerParentController::class, 'destroy'])->name('player-parents.destroy');
                            Route::get('edit', [PlayerParentController::class, 'edit'])->name('player-parents.edit');
                            Route::put('update', [PlayerParentController::class, 'update'])->name('player-parents.update');
                        });
                    });
                });
            });

            Route::prefix('coach-managements')->group(function () {
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

            Route::prefix('team-managements')->group(function () {
                Route::get('', [TeamController::class, 'index'])->name('team-managements.index');
                Route::get('admin-teams', [TeamController::class, 'adminTeamsData'])->name('team-managements.admin-teams');

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
                        Route::get('competitions', [TeamController::class, 'teamCompetitions'])->name('team-managements.teamCompetitions');
                        Route::get('training-histories', [TeamController::class, 'teamTrainingHistories'])->name('team-managements.training-histories');
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

            Route::prefix('training-schedules')->group(function () {
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
                    Route::patch('unpublish', [TrainingVideoController::class, 'unpublish'])->name('training-videos.unpublish');
                    Route::patch('publish', [TrainingVideoController::class, 'publish'])->name('training-videos.publish');
                    Route::get('assign-player', [TrainingVideoController::class, 'assignPlayer'])->name('training-videos.assign-player');
                    Route::put('update-player', [TrainingVideoController::class, 'updatePlayers'])->name('training-videos.update-player');
                    Route::delete('delete', [TrainingVideoController::class, 'destroy'])->name('training-videos.destroy');

                    Route::prefix('players')->group(function () {
                        Route::get('', [TrainingVideoController::class, 'players'])->name('training-videos.players');
                        Route::put('assign-players', [TrainingVideoController::class, 'assignPlayers'])->name('training-videos.assign-players');
                        Route::prefix('{player}')->group(function () {
                            Route::get('', [TrainingVideoController::class, 'showPlayer'])->name('training-videos.show-player');
                            Route::delete('remove', [TrainingVideoController::class, 'removePlayer'])->name('training-videos.remove-player');
                            Route::get('lessons', [TrainingVideoController::class, 'playerLessons'])->name('training-videos.player-lessons');
                        });
                    });

                    Route::prefix('lessons')->group(function () {
                        Route::get('', [TrainingVideoLessonController::class, 'index'])->name('training-videos.lessons-index');
                        Route::post('store', [TrainingVideoLessonController::class, 'store'])->name('training-videos.lessons-store');
                        Route::prefix('{lesson}')->group(function () {
                            Route::get('', [TrainingVideoLessonController::class, 'show'])->name('training-videos.lessons-show');
                            Route::get('players', [TrainingVideoLessonController::class, 'players'])->name('training-videos.lessons-players');
                            Route::get('edit', [TrainingVideoLessonController::class, 'edit'])->name('training-videos.lessons-edit');
                            Route::put('update', [TrainingVideoLessonController::class, 'update'])->name('training-videos.lessons-update');
                            Route::delete('destroy', [TrainingVideoLessonController::class, 'destroy'])->name('training-videos.lessons-destroy');
                            Route::patch('unpublish', [TrainingVideoLessonController::class, 'unpublish'])->name('training-videos.lessons-unpublish');
                            Route::patch('publish', [TrainingVideoLessonController::class, 'publish'])->name('training-videos.lessons-publish');
                        });
                    });

                });
            });

            Route::prefix('product-categories')->group(function () {
                Route::get('', [ProductCategoryController::class, 'index'])->name('product-categories.index');
                Route::post('store', [ProductCategoryController::class, 'store'])->name('product-categories.store');

                Route::prefix('{productCategory}')->group(function () {
                    Route::get('edit', [ProductCategoryController::class, 'edit'])->name('product-categories.edit');
                    Route::put('update', [ProductCategoryController::class, 'update'])->name('product-categories.update');
                    Route::patch('deactivate', [ProductCategoryController::class, 'deactivate'])->name('product-categories.deactivate');
                    Route::patch('activate', [ProductCategoryController::class, 'activate'])->name('product-categories.activate');
                    Route::delete('delete', [ProductCategoryController::class, 'destroy'])->name('product-categories.destroy');
                });
            });

            Route::prefix('taxes')->group(function () {
                Route::get('', [TaxController::class, 'index'])->name('taxes.index');
                Route::post('store', [TaxController::class, 'store'])->name('taxes.store');

                Route::prefix('{tax}')->group(function () {
                    Route::get('edit', [TaxController::class, 'edit'])->name('taxes.edit');
                    Route::put('update', [TaxController::class, 'update'])->name('taxes.update');
                    Route::patch('deactivate', [TaxController::class, 'deactivate'])->name('taxes.deactivate');
                    Route::patch('activate', [TaxController::class, 'activate'])->name('taxes.activate');
                    Route::delete('delete', [TaxController::class, 'destroy'])->name('taxes.destroy');
                });
            });

            Route::prefix('products')->group(function () {
                Route::get('', [ProductController::class, 'index'])->name('products.index');
                Route::post('store', [ProductController::class, 'store'])->name('products.store');

                Route::prefix('{product}')->group(function () {
                    Route::get('edit', [ProductController::class, 'edit'])->name('products.edit');
                    Route::put('update', [ProductController::class, 'update'])->name('products.update');
                    Route::patch('deactivate', [ProductController::class, 'deactivate'])->name('products.deactivate');
                    Route::patch('activate', [ProductController::class, 'activate'])->name('products.activate');
                    Route::delete('delete', [ProductController::class, 'destroy'])->name('products.destroy');
                });
            });

            Route::prefix('invoices')->group(function () {
                Route::get('', [InvoiceController::class, 'index'])->name('invoices.index');
                Route::get('calculate-product-amount', [InvoiceController::class, 'calculateProductAmount'])->name('invoices.calculate-product-amount');
                Route::post('calculate-invoice-total', [InvoiceController::class, 'calculateInvoiceTotal'])->name('invoices.calculate-invoice-total');
                Route::get('create', [InvoiceController::class, 'create'])->name('invoices.create');
                Route::post('store', [InvoiceController::class, 'store'])->name('invoices.store');

                Route::prefix('archived')->group(function () {
                    Route::get('', [InvoiceController::class, 'deletedData'])->name('invoices.archived');
                    Route::prefix('{invoice}')->group(function () {
                        Route::get('', [InvoiceController::class, 'showArchived'])->name('invoices.show-archived');
                        Route::post('restore', [InvoiceController::class, 'restoreData'])->name('invoices.restore');
                        Route::delete('permanent-delete', [InvoiceController::class, 'permanentDeleteData'])->name('invoices.permanent-delete');
                    });
                });

                Route::prefix('{invoice}')->group(function () {
                    Route::get('', [InvoiceController::class, 'show'])->name('invoices.show');
                    Route::get('edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
                    Route::put('update', [InvoiceController::class, 'update'])->name('invoices.update');
                    Route::patch('set-paid', [InvoiceController::class, 'setPaid'])->name('invoices.set-paid');
                    Route::patch('set-uncollectible', [InvoiceController::class, 'setUncollectible'])->name('invoices.set-uncollectible');
                    Route::patch('set-open', [InvoiceController::class, 'setOpen'])->name('invoices.set-open');
                    Route::patch('set-past-due', [InvoiceController::class, 'setPastDue'])->name('invoices.set-past-due');
                    Route::delete('delete', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
                });
            });

            Route::prefix('subscriptions')->group(function () {
                Route::get('', [SubscriptionController::class, 'index'])->name('subscriptions.index');

                Route::prefix('{subscription}')->group(function () {
                    Route::get('', [SubscriptionController::class, 'show'])->name('subscriptions.show');
                    Route::get('invoices', [SubscriptionController::class, 'invoices'])->name('subscriptions.invoices');
                    Route::patch('set-scheduled', [SubscriptionController::class, ''])->name('subscriptions.set-scheduled');
                    Route::patch('set-unsubscribed', [SubscriptionController::class, 'setUncollectible'])->name('subscriptions.set-unsubscribed');
                });

            });
        });
    });

    Route::group(['middleware' => ['role:coach,web']], function () {

        Route::prefix('coach')->group(function () {
            Route::get('dashboard', [CoachDashboardController::class, 'index'])->name('coach.dashboard');

            Route::prefix('player-managements')->group(function () {
                Route::get('', [CoachPlayerController::class, 'index'])->name('coach.player-managements.index');
                Route::prefix('{player}')->group(function () {
                    Route::get('', [CoachPlayerController::class, 'show'])->name('coach.player-managements.show');
                    Route::get('skill-stats', [CoachPlayerController::class, 'skillStatsDetail'])->name('coach.player-managements.skill-stats');
                    Route::get('player-teams', [PlayerController::class, 'playerTeams'])->name('coach.player-managements.playerTeams');
                    Route::get('parents', [PlayerParentController::class, 'index'])->name('coach.player-parents.index');
                    Route::get('upcoming-matches', [CoachPlayerController::class, 'upcomingMatches'])->name('coach.player-parents.upcoming-matches');
                    Route::get('upcoming-trainings', [CoachPlayerController::class, 'upcomingTrainings'])->name('coach.player-parents.upcoming-trainings');
                });
            });

            Route::prefix('team-managements')->group(function () {
                Route::get('', [TeamController::class, 'index'])->name('coach.team-managements.index');
                Route::get('coach-teams', [TeamController::class, 'coachTeamsData'])->name('coach.team-managements.coach-teams');

                Route::prefix('{team}')->group(function () {
                    Route::get('', [TeamController::class, 'show'])->name('coach.team-managements.show');
                    Route::get('players', [TeamController::class, 'teamPlayers'])->name('coach.team-managements.teamPlayers');
                    Route::get('coaches', [TeamController::class, 'teamCoaches'])->name('coach.team-managements.teamCoaches');
                    Route::get('competitions', [TeamController::class, 'teamCompetitions'])->name('coach.team-managements.teamCompetitions');
                    Route::get('training-histories', [TeamController::class, 'teamTrainingHistories'])->name('coach.team-managements.training-histories');
                });
            });

            Route::prefix('attendance-reports')->group(function () {
                Route::get('', [AttendanceReportController::class, 'index'])->name('coach.attendance-report.index');

                Route::prefix('{player}')->group(function () {
                    Route::get('', [AttendanceReportController::class, 'show'])->name('coach.attendance-report.show');
                    Route::get('training-history', [AttendanceReportController::class, 'trainingTable'])->name('coach.attendance-report.trainingTable');
                    Route::get('match-history', [AttendanceReportController::class, 'matchDatatable'])->name('coach.attendance-report.matchDatatable');
                });
            });
        });
    });
});
//Route::group(['middleware' => ['role:player,web']], function () {
//    Route::get('dashboard', [DashboardController::class, 'index'])->name('player.dashboard');
//});
