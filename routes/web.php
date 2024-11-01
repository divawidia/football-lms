<?php

use App\Http\Controllers\AcademyController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AttendanceReportController;
use App\Http\Controllers\Admin\CoachController;
use App\Http\Controllers\Admin\CompetitionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventScheduleController;
use App\Http\Controllers\Admin\GroupDivisionController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\OpponentTeamController;
use App\Http\Controllers\Admin\PlayerParentController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\TaxController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\TrainingVideoController;
use App\Http\Controllers\Admin\TrainingVideoLessonController;
use App\Http\Controllers\Coach\DashboardController as CoachDashboardController;
use App\Http\Controllers\Coach\PlayerPerformanceReviewController;
use App\Http\Controllers\Player\DashboardController as PlayerDashboardController;
use App\Http\Controllers\Coach\SkillAssessmentController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\PerformanceReportController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\UserController;
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
});

Route::group(['middleware' => ['auth']], function () {

    Route::prefix('edit-account')->group(function () {
        Route::get('', [UserController::class, 'edit'])->name('edit-account.edit');
        Route::put('', [UserController::class, 'update'])->name('edit-account.update');
    });
    Route::prefix('reset-password')->group(function () {
        Route::get('', [UserController::class, 'resetPassword'])->name('reset-password.edit');
        Route::put('', [UserController::class, 'updatePassword'])->name('reset-password.update');
    });
    Route::prefix('edit-academy')->group(function () {
        Route::get('', [AcademyController::class, 'edit'])->name('edit-academy.edit');
        Route::put('', [AcademyController::class, 'update'])->name('edit-academy.update');
    });

    Route::group(['middleware' => ['role:Super-Admin,web']], function () {
//        Route::prefix('admin')->group(function () {
            Route::prefix('admin-managements')->group(function () {
                Route::post('', [AdminController::class, 'store'])->name('admin-managements.store');
                Route::get('create', [AdminController::class, 'create'])->name('admin-managements.create');

                Route::prefix('{admin}')->group(function () {
                    Route::get('', [AdminController::class, 'show'])->name('admin-managements.show');
                    Route::patch('deactivate', [AdminController::class, 'deactivate'])->name('deactivate-admin');
                    Route::patch('activate', [AdminController::class, 'activate'])->name('activate-admin');
                    Route::patch('change-password', [AdminController::class, 'changePassword'])->name('admin-managements.change-password');
                    Route::get('edit', [AdminController::class, 'edit'])->name('admin-managements.edit');
                    Route::put('', [AdminController::class, 'update'])->name('admin-managements.update');
                    Route::delete('', [AdminController::class, 'destroy'])->name('admin-managements.destroy');
                });
            });
//        });
    });

    Route::group(['middleware' => ['role:admin|Super-Admin,web']], function () {

//        Route::prefix('admin')->group(function () {

            Route::get('admin-dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

            Route::prefix('admin-managements')->group(function () {
                Route::get('', [AdminController::class, 'index'])->name('admin-managements.index');
                Route::prefix('{admin}')->group(function () {
                    Route::get('', [AdminController::class, 'show'])->name('admin-managements.show');
                });
            });

            Route::prefix('player-managements')->group(function () {
                Route::get('admins-players', [PlayerController::class, 'adminIndex'])->name('admin.player-managements.index');
                Route::get('create', [PlayerController::class, 'create'])->name('player-managements.create');
                Route::post('', [PlayerController::class, 'store'])->name('player-managements.store');

                Route::prefix('{player}')->group(function () {
                    Route::get('edit', [PlayerController::class, 'edit'])->name('player-managements.edit');
                    Route::put('', [PlayerController::class, 'update'])->name('player-managements.update');
                    Route::delete('', [PlayerController::class, 'destroy'])->name('player-managements.destroy');

                    Route::patch('deactivate', [PlayerController::class, 'deactivate'])->name('deactivate-player');
                    Route::patch('activate', [PlayerController::class, 'activate'])->name('activate-player');
                    Route::get('change-password', [PlayerController::class, 'changePasswordPage'])->name('player-managements.change-password-page');
                    Route::patch('change-password', [PlayerController::class, 'changePassword'])->name('player-managements.change-password');
                    Route::put('update-teams', [PlayerController::class, 'updateTeams'])->name('player-managements.updateTeams');
                    Route::delete('remove-team/{team}', [PlayerController::class, 'removeTeam'])->name('player-managements.removeTeam');

                    Route::prefix('parents')->group(function () {
//                        Route::get('', [PlayerParentController::class, 'index'])->name('player-parents.index');
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
                Route::get('admin-teams', [TeamController::class, 'adminTeamsData'])->name('team-managements.admin-teams');

                Route::prefix('our-teams')->group(function () {
                    Route::get('create', [TeamController::class, 'create'])->name('team-managements.create');
                    Route::post('store', [TeamController::class, 'store'])->name('team-managements.store');
                    Route::post('api/store', [TeamController::class, 'apiStore'])->name('team-managements.apiStore');

                    Route::prefix('{team}')->group(function () {
                        Route::get('edit', [TeamController::class, 'edit'])->name('team-managements.edit');
                        Route::put('update', [TeamController::class, 'update'])->name('team-managements.update');
                        Route::delete('destroy', [TeamController::class, 'destroy'])->name('team-managements.destroy');
                        Route::patch('deactivate', [TeamController::class, 'deactivate'])->name('deactivate-team');
                        Route::patch('activate', [TeamController::class, 'activate'])->name('activate-team');
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
                Route::get('create', [CompetitionController::class, 'create'])->name('competition-managements.create');
                Route::post('store', [CompetitionController::class, 'store'])->name('competition-managements.store');
                Route::prefix('{competition}')->group(function () {
                    Route::get('edit', [CompetitionController::class, 'edit'])->name('competition-managements.edit');
                    Route::put('update', [CompetitionController::class, 'update'])->name('competition-managements.update');
                    Route::delete('destroy', [CompetitionController::class, 'destroy'])->name('competition-managements.destroy');
                    Route::patch('deactivate', [CompetitionController::class, 'deactivate'])->name('deactivate-competition');
                    Route::patch('activate', [CompetitionController::class, 'activate'])->name('activate-competition');

                    Route::prefix('group-division')->group(function () {
                        Route::get('create', [GroupDivisionController::class, 'create'])->name('division-managements.create');
                        Route::post('store', [GroupDivisionController::class, 'store'])->name('division-managements.store');
                        Route::prefix('{group}')->group(function () {
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
                Route::get('admins-trainings', [EventScheduleController::class, 'adminIndexTraining'])->name('admin.training-schedules.index');
            });

            Route::prefix('match-schedules')->group(function () {
                Route::get('admins-matches', [EventScheduleController::class, 'adminIndexMatch'])->name('admin.match-schedules.index');
                Route::get('create', [EventScheduleController::class, 'createMatch'])->name('match-schedules.create');
                Route::get('get-competition-teams/{competition}', [EventScheduleController::class, 'getCompetitionTeam'])->name('match-schedules.get-competition-team');
                Route::get('get-friendlymatch-teams', [EventScheduleController::class, 'getFriendlyMatchTeam'])->name('match-schedules.get-friendlymatch-team');
                Route::post('store', [EventScheduleController::class, 'storeMatch'])->name('match-schedules.store');

                Route::prefix('{schedule}')->group(function () {
                    Route::get('edit', [EventScheduleController::class, 'editMatch'])->name('match-schedules.edit');
                    Route::put('update', [EventScheduleController::class, 'updateMatch'])->name('match-schedules.update');
                    Route::delete('destroy', [EventScheduleController::class, 'destroy'])->name('match-schedules.destroy');
                    Route::patch('end-match', [EventScheduleController::class, 'endMatch'])->name('end-match');
                    Route::patch('activate', [EventScheduleController::class, 'activateMatch'])->name('activate-match');
                });
            });

            Route::prefix('attendance-reports')->group(function () {
                Route::get('admin', [AttendanceReportController::class, 'adminIndex'])->name('admin.attendance-report.index');
            });

            Route::prefix('performance-reports')->group(function () {
                Route::get('admin', [PerformanceReportController::class, 'adminIndex'])->name('admin.performance-report.index');
            });

            Route::prefix('leaderboards')->group(function () {
                Route::get('admin-teams', [LeaderboardController::class, 'teamLeaderboard'])->name('leaderboards.teams');
                Route::get('admin-players', [LeaderboardController::class, 'playerLeaderboard'])->name('leaderboards.players');
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
//        });
    });

    Route::group(['middleware' => ['role:coach,web']], function () {

//        Route::prefix('coach')->group(function () {
            Route::get('coach-dashboard', [CoachDashboardController::class, 'index'])->name('coach.dashboard');

            Route::prefix('player-managements')->group(function () {
                Route::get('coaches-players', [PlayerController::class, 'coachIndex'])->name('coach.player-managements.index');

                Route::prefix('{player}')->group(function () {
                    Route::prefix('performance-reviews')->group(function () {
                        Route::get('', [PlayerPerformanceReviewController::class, 'indexPlayer'])->name('coach.player-managements.performance-reviews');
                        Route::post('store', [PlayerPerformanceReviewController::class, 'store'])->name('coach.performance-reviews.store');
                    });

                });

                Route::prefix('performance-reviews/{review}')->group(function () {
                    Route::get('', [PlayerPerformanceReviewController::class, 'edit'])->name('coach.performance-reviews.edit');
                    Route::put('update', [PlayerPerformanceReviewController::class, 'update'])->name('coach.performance-reviews.update');
                    Route::delete('destroy', [PlayerPerformanceReviewController::class, 'destroy'])->name('coach.performance-reviews.destroy');
                });
            });

            Route::prefix('team-managements')->group(function () {
                Route::get('coach-teams', [TeamController::class, 'coachTeamsData'])->name('coach.team-managements.coach-teams');
            });

            Route::prefix('skill-assessments')->group(function () {
                Route::get('', [SkillAssessmentController::class, 'index'])->name('skill-assessments.index');
                Route::prefix('{player}')->group(function () {
                    Route::get('', [PlayerController::class, 'skillStatsDetail'])->name('skill-assessments.skill-stats');
                    Route::get('create', [SkillAssessmentController::class, 'create'])->name('skill-assessments.create');
                    Route::post('store', [SkillAssessmentController::class, 'store'])->name('skill-assessments.store');
                });

                Route::prefix('skill-stats/{skillStats}')->group(function () {
                    Route::get('', [SkillAssessmentController::class, 'edit'])->name('skill-assessments.edit');
                    Route::put('update', [SkillAssessmentController::class, 'update'])->name('skill-assessments.update');
                    Route::delete('destroy', [SkillAssessmentController::class, 'destroy'])->name('skill-assessments.destroy');
                });
            });

            Route::prefix('training-schedules')->group(function () {
                Route::get('coaches-trainings', [EventScheduleController::class, 'coachIndexTraining'])->name('coach.training-schedules.index');
            });

            Route::prefix('match-schedules')->group(function () {
                Route::get('coaches-matches', [EventScheduleController::class, 'coachIndexMatch'])->name('coach.match-schedules.index');
            });

            Route::prefix('attendance-reports')->group(function () {
                Route::get('coach', [AttendanceReportController::class, 'coachIndex'])->name('coach.attendance-report.index');
            });

            Route::prefix('performance-reports')->group(function () {
                Route::get('coach', [PerformanceReportController::class, 'coachIndex'])->name('coach.performance-report.index');
            });

            Route::prefix('leaderboards')->group(function () {
                Route::get('coach-teams', [LeaderboardController::class, 'coachTeamLeaderboard'])->name('coach.leaderboards.teams');
                Route::get('coach-players', [LeaderboardController::class, 'coachPlayerLeaderboard'])->name('coach.leaderboards.players');
            });
//        });
    });

    Route::group(['middleware' => ['role:player,web']], function () {
        Route::get('player-dashboard', [PlayerDashboardController::class, 'index'])->name('player.dashboard');

        Route::get('skill-stats', [PlayerController::class, 'skillStatsDetailPlayer'])->name('player.skill-stats');

        Route::get('performance-reviews', [PlayerPerformanceReviewController::class, 'playerPerformancePage'])->name('player.performance-reviews');

        Route::prefix('training-schedules')->group(function () {
            Route::get('players-trainings', [EventScheduleController::class, 'playerIndexTraining'])->name('player.training-schedules.index');
        });

        Route::prefix('match-schedules')->group(function () {
            Route::get('players-matches', [EventScheduleController::class, 'playerIndexMatch'])->name('player.match-schedules.index');
        });

        Route::prefix('attendance-reports')->group(function () {
            Route::get('training-history', [AttendanceReportController::class, 'playerTrainingHistories'])->name('player.attendance-report.trainingTable');
            Route::get('match-history', [AttendanceReportController::class, 'playerMatchHistories'])->name('player.attendance-report.matchDatatable');
        });
        Route::prefix('performance-reports')->group(function () {
            Route::get('player-match-histories', [PerformanceReportController::class, 'playerIndex'])->name('player.performance-report.index');
        });

        Route::prefix('leaderboards')->group(function () {
            Route::get('player-teams', [LeaderboardController::class, 'playerTeamLeaderboard'])->name('player.leaderboards.teams');
            Route::get('players-teammate', [LeaderboardController::class, 'playersTeammateLeaderboard'])->name('player.leaderboards.teammate');
        });
    });

    Route::group(['middleware' => ['role:coach|admin|Super-Admin,web']], function () {
        Route::prefix('player-managements')->group(function () {
            Route::get('', [PlayerController::class, 'index'])->name('player-managements.index');

            Route::prefix('{player}')->group(function () {
                Route::get('', [PlayerController::class, 'show'])->name('player-managements.show');
                Route::get('skill-stats', [PlayerController::class, 'skillStatsDetail'])->name('player-managements.skill-stats');
                Route::get('player-teams', [PlayerController::class, 'playerTeams'])->name('player-managements.playerTeams');

                Route::get('upcoming-matches', [PlayerController::class, 'upcomingMatches'])->name('player-managements.upcoming-matches');
                Route::get('upcoming-trainings', [PlayerController::class, 'upcomingTrainings'])->name('player-managements.upcoming-trainings');

                Route::get('performance-reviews', [PlayerPerformanceReviewController::class, 'playerPerformancePage'])->name('player-managements.performance-reviews-page');
            });
        });

        Route::prefix('training-schedules')->group(function () {
            Route::get('create', [EventScheduleController::class, 'createTraining'])->name('training-schedules.create');
            Route::post('store', [EventScheduleController::class, 'storeTraining'])->name('training-schedules.store');

            Route::prefix('{schedule}')->group(function () {
                Route::get('', [EventScheduleController::class, 'showTraining'])->name('training-schedules.show');
                Route::get('edit', [EventScheduleController::class, 'editTraining'])->name('training-schedules.edit');
                Route::put('update', [EventScheduleController::class, 'updateTraining'])->name('training-schedules.update');
                Route::delete('destroy', [EventScheduleController::class, 'destroy'])->name('training-schedules.destroy');
                Route::patch('deactivate', [EventScheduleController::class, 'deactivateTraining'])->name('deactivate-training');
                Route::patch('activate', [EventScheduleController::class, 'activateTraining'])->name('activate-training');

                Route::get('player-skills', [SkillAssessmentController::class, 'indexAllPlayerInEvent'])->name('training-schedules.player-skills');
                Route::get('player-performance-review', [PlayerPerformanceReviewController::class, 'indexAllPlayerInEvent'])->name('training-schedules.player-performance-review');

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
            Route::prefix('{schedule}')->group(function () {
                Route::get('', [EventScheduleController::class, 'showMatch'])->name('match-schedules.show');

                Route::get('get-assisted-player/{player}', [EventScheduleController::class, 'getAssistPlayer'])->name('get-assist-player');

                Route::get('player-skills', [SkillAssessmentController::class, 'indexAllPlayerInEvent'])->name('match-schedules.player-skills');
                Route::get('player-performance-review', [PlayerPerformanceReviewController::class, 'indexAllPlayerInEvent'])->name('match-schedules.player-performance-review');

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
                    Route::get('{player}', [EventScheduleController::class, 'getPlayerStats'])->name('match-schedules.show-player-match-stats');
                    Route::put('{player}/update', [EventScheduleController::class, 'updatePlayerStats'])->name('match-schedules.update-player-match-stats');
                });
            });
        });

        Route::prefix('attendance-reports')->group(function () {
            Route::prefix('{player}')->group(function () {
                Route::get('', [AttendanceReportController::class, 'show'])->name('attendance-report.show');
            });
        });

        Route::prefix('performance-reports')->group(function () {
            Route::get('', [PerformanceReportController::class, 'index'])->name('performance-report.index');
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
    });

    Route::group(['middleware' => ['role:player|coach|admin|Super-Admin,web']], function () {
        Route::prefix('player-managements')->group(function () {
            Route::prefix('{player}')->group(function () {
                Route::get('parents', [PlayerParentController::class, 'index'])->name('player-parents.index');

                Route::get('performance-reviews-table', [PlayerPerformanceReviewController::class, 'indexPlayer'])->name('player-managements.performance-reviews');
            });
        });

        Route::prefix('training-schedules')->group(function () {
            Route::get('', [EventScheduleController::class, 'indexTraining'])->name('training-schedules.index');

            Route::prefix('{schedule}')->group(function () {
                Route::get('', [EventScheduleController::class, 'showTraining'])->name('training-schedules.show');
            });
        });

        Route::prefix('match-schedules')->group(function () {
            Route::get('', [EventScheduleController::class, 'indexMatch'])->name('match-schedules.index');

            Route::prefix('{schedule}')->group(function () {
                Route::get('', [EventScheduleController::class, 'showMatch'])->name('match-schedules.show');
                Route::prefix('player-match-stats')->group(function () {
                    Route::get('', [EventScheduleController::class, 'indexPlayerMatchStats'])->name('match-schedules.index-player-match-stats');
                });
            });
        });

        Route::prefix('attendance-reports')->group(function () {
            Route::get('', [AttendanceReportController::class, 'index'])->name('attendance-report.index');

            Route::prefix('{player}')->group(function () {
                Route::get('training-history', [AttendanceReportController::class, 'trainingTable'])->name('attendance-report.trainingTable');
                Route::get('match-history', [AttendanceReportController::class, 'matchDatatable'])->name('attendance-report.matchDatatable');
            });
        });

        Route::prefix('team-managements')->group(function () {
            Route::get('', [TeamController::class, 'index'])->name('team-managements.index');

            Route::prefix('{team}')->group(function () {
                Route::get('', [TeamController::class, 'show'])->name('team-managements.show');
                Route::get('players', [TeamController::class, 'teamPlayers'])->name('team-managements.teamPlayers');
                Route::get('coaches', [TeamController::class, 'teamCoaches'])->name('team-managements.teamCoaches');
                Route::get('competitions', [TeamController::class, 'teamCompetitions'])->name('team-managements.teamCompetitions');
                Route::get('training-histories', [TeamController::class, 'teamTrainingHistories'])->name('team-managements.training-histories');
                Route::get('match-histories', [TeamController::class, 'teamMatchHistories'])->name('team-managements.match-histories');
            });
        });

        Route::prefix('competition-managements')->group(function () {
            Route::get('', [CompetitionController::class, 'index'])->name('competition-managements.index');

            Route::prefix('{competition}')->group(function () {
                Route::get('', [CompetitionController::class, 'show'])->name('competition-managements.show');

                Route::prefix('group-division')->group(function () {
                    Route::prefix('{group}')->group(function () {
                        Route::get('', [GroupDivisionController::class, 'index'])->name('division-managements.index');
                    });
                });
            });
        });

        Route::prefix('performance-reports')->group(function () {
            Route::get('', [PerformanceReportController::class, 'index'])->name('performance-report.index');
        });

        Route::prefix('leaderboards')->group(function () {
            Route::get('', [LeaderboardController::class, 'index'])->name('leaderboards.index');
        });
    });
});
