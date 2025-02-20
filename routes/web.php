<?php

use App\Http\Controllers\AcademyController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AttendanceReportController;
use App\Http\Controllers\Admin\CoachController;
use App\Http\Controllers\Admin\CompetitionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MatchController;
use App\Http\Controllers\Admin\FinancialReportController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\LeagueStandingController;
use App\Http\Controllers\Admin\PlayerParentController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\TaxController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\TrainingController;
use App\Http\Controllers\Admin\TrainingVideoController;
use App\Http\Controllers\Admin\TrainingVideoLessonController;
use App\Http\Controllers\Coach\DashboardController as CoachDashboardController;
use App\Http\Controllers\Coach\PlayerPerformanceReviewController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Player\BillingPaymentsController;
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

Route::group(['middleware' => ['auth', 'web']], function () {
    Route::prefix('edit-account')->group(function () {
        Route::get('', [UserController::class, 'edit'])->name('edit-account.edit');
        Route::put('', [UserController::class, 'update'])->name('edit-account.update');
    });
    Route::prefix('reset-password')->group(function () {
        Route::get('', [UserController::class, 'resetPassword'])->name('reset-password.edit');
        Route::put('', [UserController::class, 'updatePassword'])->name('reset-password.update');
    });

    Route::patch('/notifications/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::get('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

    Route::get('admin-dashboard', [DashboardController::class, 'index'])->middleware('role:Super-Admin|admin')->name('admin.dashboard');
    Route::get('coach-dashboard', [CoachDashboardController::class, 'index'])->middleware('role:coach')->name('coach.dashboard');
    Route::get('player-dashboard', [PlayerDashboardController::class, 'index'])->middleware('role:player')->name('player.dashboard');

    Route::prefix('edit-academy')->middleware('role:Super-Admin|admin')->group(function () {
        Route::get('', [AcademyController::class, 'edit'])->name('edit-academy.edit');
        Route::put('', [AcademyController::class, 'update'])->name('edit-academy.update');
    });

    Route::prefix('admin-management')->name('admin-managements.')->group(function () {
        Route::middleware('role:Super-Admin')->group(function () {
            Route::get('create', [AdminController::class, 'create'])->name('create');
            Route::post('', [AdminController::class, 'store'])->name('store');

            Route::prefix('{admin}')->group(function () {
                Route::patch('deactivate', [AdminController::class, 'deactivate'])->name('deactivate');
                Route::patch('activate', [AdminController::class, 'activate'])->name('activate');
                Route::patch('change-password', [AdminController::class, 'changePassword'])->name('change-password');
                Route::get('edit', [AdminController::class, 'edit'])->name('edit');
                Route::put('', [AdminController::class, 'update'])->name('update');
                Route::delete('', [AdminController::class, 'destroy'])->name('destroy');
            });
        });

        Route::middleware('role:Super-Admin|admin')->group(function () {
            Route::get('', [AdminController::class, 'index'])->name('index');
            Route::get('{admin}', [AdminController::class, 'show'])->name('show');
        });
    });

    Route::prefix('player-management')->name('player-managements.')->group(function () {
        Route::middleware('role:Super-Admin|admin')->group(function () {
            Route::get('admins-players', [PlayerController::class, 'adminIndex'])->name('admin-index');
            Route::get('create', [PlayerController::class, 'create'])->name('create');
            Route::post('', [PlayerController::class, 'store'])->name('store');

        });

        Route::get('', [PlayerController::class, 'index'])->middleware('role:Super-Admin|admin|coach')->name('index');
        Route::get('coaches-players', [PlayerController::class, 'coachIndex'])->middleware('role:coach')->name('coach-index');

        Route::prefix('{player}')->group(function () {
            Route::middleware('role:Super-Admin|admin')->group(function () {
                Route::get('edit', [PlayerController::class, 'edit'])->name('edit');
                Route::put('', [PlayerController::class, 'update'])->name('update');
                Route::delete('', [PlayerController::class, 'destroy'])->name('destroy');

                Route::patch('deactivate', [PlayerController::class, 'deactivate'])->name('deactivate');
                Route::patch('activate', [PlayerController::class, 'activate'])->name('activate');

                Route::patch('change-password', [PlayerController::class, 'changePassword'])->name('change-password');

                Route::put('update-teams', [PlayerController::class, 'updateTeams'])->name('update-teams');
                Route::delete('remove-team/{team}', [PlayerController::class, 'removeTeam'])->name('remove-team');
            });

            Route::middleware('role:Super-Admin|admin|coach')->group(function () {
                Route::get('', [PlayerController::class, 'show'])->name('show');
                Route::get('skill-stats', [PlayerController::class, 'skillStatsDetail'])->name('skill-stats');
                Route::get('player-teams', [PlayerController::class, 'playerTeams'])->name('player-teams');

                Route::get('upcoming-matches', [PlayerController::class, 'upcomingMatches'])->name('upcoming-matches');
                Route::get('upcoming-trainings', [PlayerController::class, 'upcomingTrainings'])->name('upcoming-trainings');

            });

            Route::get('skill-stats-history', [PlayerController::class, 'skillStatsHistory'])->middleware('role:Super-Admin|admin|coach|player')->name('skill-stats-history');

            Route::prefix('performance-reviews')->name('performance-reviews.')->group(function () {
                Route::get('table', [PlayerPerformanceReviewController::class, 'indexPlayer'])->middleware('role:Super-Admin|admin|coach|player')->name('index-tables');
                Route::get('match-training', [PlayerPerformanceReviewController::class, 'indexPlayer'])->middleware('role:Super-Admin|admin|coach')->name('match-training');
                Route::get('', [PlayerPerformanceReviewController::class, 'playerPerformancePage'])->middleware('role:Super-Admin|admin|coach')->name('index-page');
                Route::post('store', [PlayerPerformanceReviewController::class, 'store'])->middleware('role:coach')->name('store');
            });

            Route::prefix('parents')->name('player-parents.')->group(function () {
                Route::get('', [PlayerParentController::class, 'index'])->middleware('role:Super-Admin|admin|coach|player')->name('index');

                Route::middleware('role:Super-Admin|admin')->group(function () {
                    Route::get('create', [PlayerParentController::class, 'create'])->name('create');
                    Route::post('store', [PlayerParentController::class, 'store'])->name('store');
                    Route::prefix('{parent}')->group(function () {
                        Route::delete('destroy', [PlayerParentController::class, 'destroy'])->name('destroy');
                        Route::get('edit', [PlayerParentController::class, 'edit'])->name('edit');
                        Route::put('update', [PlayerParentController::class, 'update'])->name('update');
                    });
                });
            });
        });

        Route::prefix('performance-reviews/{review}')->name('performance-reviews.')->middleware('role:coach')->group(function () {
            Route::get('', [PlayerPerformanceReviewController::class, 'edit'])->name('edit');
            Route::put('update', [PlayerPerformanceReviewController::class, 'update'])->name('update');
            Route::delete('destroy', [PlayerPerformanceReviewController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('coach-managements')->middleware('role:Super-Admin|admin')->name('coach-managements.')->group(function () {
        Route::get('', [CoachController::class, 'index'])->name('index');
        Route::get('tables', [CoachController::class, 'indexTables'])->name('tables');
        Route::get('create', [CoachController::class, 'create'])->name('create');
        Route::post('store', [CoachController::class, 'store'])->name('store');
        Route::prefix('{coach}')->group(function () {
            Route::get('', [CoachController::class, 'show'])->name('show');
            Route::get('edit', [CoachController::class, 'edit'])->name('edit');
            Route::put('update', [CoachController::class, 'update'])->name('update');
            Route::delete('destroy', [CoachController::class, 'destroy'])->name('destroy');
            Route::patch('deactivate', [CoachController::class, 'deactivate'])->name('deactivate');
            Route::patch('activate', [CoachController::class, 'activate'])->name('activate');
            Route::patch('change-password', [CoachController::class, 'changePassword'])->name('change-password');
            Route::get('coach-teams', [CoachController::class, 'coachTeams'])->name('coach-teams');
            Route::put('update-teams', [CoachController::class, 'updateTeams'])->name('update-team');
            Route::delete('remove-team/{team}', [CoachController::class, 'removeTeam'])->name('remove-team');
        });
    });

    Route::prefix('team-managements')->name('team-managements.')->group(function () {
        Route::get('', [TeamController::class, 'index'])->middleware('role:player|coach|admin|Super-Admin')->name('index');

        Route::get('coach-teams', [TeamController::class, 'coachTeamsData'])->middleware('role:coach')->name('coach-teams');
        Route::get('player-teams', [TeamController::class, 'playerTeamsData'])->middleware('role:player')->name('player-teams');

        Route::middleware('role:Super-Admin|admin')->group(function () {
            Route::get('admin-teams', [TeamController::class, 'adminTeamsData'])->name('admin-teams');
            Route::get('all-teams', [TeamController::class, 'allTeams'])->name('all-teams');
            Route::get('create', [TeamController::class, 'create'])->name('create');
            Route::post('store', [TeamController::class, 'store'])->name('store');
            Route::post('api-store', [TeamController::class, 'apiStore'])->name('api-store');
        });

        Route::prefix('{team}')->group(function () {
            Route::middleware('role:player|coach|admin|Super-Admin')->group(function () {
                Route::get('', [TeamController::class, 'show'])->name('show');
                Route::get('players', [TeamController::class, 'teamPlayers'])->name('team-players');
                Route::get('coaches', [TeamController::class, 'teamCoaches'])->name('team-coaches');
                Route::get('competitions', [TeamController::class, 'teamCompetitions'])->name('team-competitions');
                Route::get('training-histories', [TeamController::class, 'teamTrainingHistories'])->name('training-histories');
                Route::get('match-histories', [TeamController::class, 'teamMatchHistories'])->name('match-histories');
            });

            Route::middleware('role:Super-Admin|admin')->group(function () {
                Route::get('edit', [TeamController::class, 'edit'])->name('edit');
                Route::put('update', [TeamController::class, 'update'])->name('update');
                Route::delete('destroy', [TeamController::class, 'destroy'])->name('destroy');
                Route::patch('deactivate', [TeamController::class, 'deactivate'])->name('deactivate');
                Route::patch('activate', [TeamController::class, 'activate'])->name('activate');
                Route::put('update-players', [TeamController::class, 'updatePlayerTeam'])->name('update-player');
                Route::put('update-coaches', [TeamController::class, 'updateCoachTeam'])->name('update-coach');
                Route::put('remove-player/{player}', [TeamController::class, 'removePlayer'])->name('remove-player');
                Route::put('remove-coach/{coach}', [TeamController::class, 'removeCoach'])->name('remove-coach');
            });
        });
    });

    Route::prefix('competition-managements')->name('competition-managements.')->group(function () {
        Route::get('', [CompetitionController::class, 'index'])->middleware('role:player|coach|admin|Super-Admin')->name('index');

        Route::middleware('role:Super-Admin|admin')->group(function () {
            Route::get('create', [CompetitionController::class, 'create'])->name('create');
            Route::post('store', [CompetitionController::class, 'store'])->name('store');
        });

        Route::prefix('{competition}')->group(function () {
            Route::middleware('role:player|coach|admin|Super-Admin')->group(function () {
                Route::get('', [CompetitionController::class, 'show'])->name('show');
                Route::get('matches', [CompetitionController::class, 'competitionMatches'])->name('matches');
            });

            Route::middleware('role:Super-Admin|admin')->group(function () {
                Route::get('edit', [CompetitionController::class, 'edit'])->name('edit');
                Route::put('update', [CompetitionController::class, 'update'])->name('update');
                Route::post('store-match', [CompetitionController::class, 'storeMatch'])->name('store-match');
                Route::delete('destroy', [CompetitionController::class, 'destroy'])->name('destroy');
                Route::patch('scheduled', [CompetitionController::class, 'scheduled'])->name('scheduled');
                Route::patch('ongoing', [CompetitionController::class, 'ongoing'])->name('ongoing');
                Route::patch('completed', [CompetitionController::class, 'completed'])->name('completed');
                Route::patch('cancelled', [CompetitionController::class, 'cancelled'])->name('cancelled');
            });

            Route::prefix('league-standings')->name('league-standings.')->group(function () {
                Route::get('', [LeagueStandingController::class, 'index'])->middleware('role:player|coach|admin|Super-Admin')->name('index');
                Route::post('store', [LeagueStandingController::class, 'store'])->middleware('role:admin|Super-Admin')->name('store');

                Route::prefix('{leagueStanding}')->middleware('role:Super-Admin|admin')->group(function () {
                    Route::get('', [LeagueStandingController::class, 'show'])->middleware('role:player|coach|admin|Super-Admin')->name('show');
                    Route::put('update', [LeagueStandingController::class, 'update'])->name('update');
                    Route::delete('destroy', [LeagueStandingController::class, 'destroy'])->name('destroy');
                });
            });
        });
    });

    Route::prefix('match-schedules')->name('match-schedules.')->group(function () {
        Route::get('', [MatchController::class, 'indexMatch'])->middleware('role:Super-Admin|admin|coach|player')->name('index');

        Route::middleware('role:Super-Admin|admin')->group(function () {
            Route::get('admins-matches', [MatchController::class, 'adminIndexMatch'])->name('admin-index');
            Route::get('create', [MatchController::class, 'createMatch'])->name('create');
            Route::post('store', [MatchController::class, 'storeMatch'])->name('store');
        });
        Route::get('coaches-matches', [MatchController::class, 'coachIndexMatch'])->middleware('role:coach')->name('coach-index');
        Route::get('players-matches', [MatchController::class, 'playerIndexMatch'])->middleware('role:player')->name('player-index');

        Route::prefix('{match}')->group(function () {
            Route::get('', [MatchController::class, 'showMatch'])->middleware('role:Super-Admin|admin|coach|player')->name('show');

            Route::middleware('role:Super-Admin|admin')->group(function () {
                Route::get('match-detail', [MatchController::class, 'getMatchDetail'])->name('match-detail');
                Route::get('match-stats', [MatchController::class, 'getTeamMatchStats'])->name('match-stats');
                Route::get('edit', [MatchController::class, 'editMatch'])->name('edit');
                Route::put('update', [MatchController::class, 'updateMatch'])->name('update');
                Route::delete('destroy', [MatchController::class, 'destroy'])->name('destroy');
                Route::patch('cancel', [MatchController::class, 'cancelled'])->name('cancel');
                Route::patch('scheduled', [MatchController::class, 'scheduled'])->name('scheduled');
            });

            Route::middleware('role:coach|Super-Admin|admin')->group(function () {
                Route::get('players', [MatchController::class, 'getEventPLayers'])->name('players');

                Route::get('player-skills', [SkillAssessmentController::class, 'indexAllPlayerInEvent'])->name('player-skills');
                Route::get('player-performance-review', [PlayerPerformanceReviewController::class, 'indexAllPlayerInMatch'])->name('player-performance-review');

                Route::get('edit-player-attendance/{player}', [MatchController::class, 'getPlayerAttendance'])->name('player');
                Route::put('update-player-attendance/{player}', [MatchController::class, 'updatePlayerAttendance'])->name('update-player');

                Route::get('edit-coach-attendance/{coach}', [MatchController::class, 'getCoachAttendance'])->name('coach');
                Route::put('update-coach-attendance/{coach}', [MatchController::class, 'updateCoachAttendance'])->name('update-coach');

                Route::post('match-scorer', [MatchController::class, 'storeMatchScorer'])->name('store-match-scorer');
                Route::delete('match-scorer/{scorer}/destroy', [MatchController::class, 'destroyMatchScorer'])->name('destroy-match-scorer');

                Route::post('create-note', [MatchController::class, 'createNote'])->name('create-note');
                Route::get('edit-note/{note}', [MatchController::class, 'editNote'])->name('edit-note');
                Route::put('update-note/{note}', [MatchController::class, 'updateNote'])->name('update-note');
                Route::delete('delete-note/{note}', [MatchController::class, 'destroyNote'])->name('destroy-note');

                Route::post('own-goal', [MatchController::class, 'storeOwnGoal'])->name('store-own-goal');
                Route::delete('own-goal/{scorer}/destroy', [MatchController::class, 'destroyOwnGoal'])->name('destroy-own-goal');

                Route::put('update-match-stats', [MatchController::class, 'updateMatchStats'])->name('update-match-stats');
                Route::put('update-external-team-score', [MatchController::class, 'updateExternalTeamScore'])->name('update-external-team-score');
            });

            Route::prefix('player-match-stats')->name('player-match-stats.')->group(function () {
                Route::get('', [MatchController::class, 'indexPlayerMatchStats'])->middleware('role:Super-Admin|admin|coach|player')->name('index');

                Route::middleware('role:Super-Admin|admin|coach')->group(function () {
                    Route::get('{player}', [MatchController::class, 'getPlayerStats'])->name('show');
                    Route::put('{player}/update', [MatchController::class, 'updatePlayerStats'])->name('update');
                });
            });
        });
    });

    Route::prefix('training-schedules')->name('training-schedules.')->group(function () {
        Route::get('', [TrainingController::class, 'index'])->middleware('role:player|coach|admin|Super-Admin')->name('index');
        Route::get('admins-trainings', [TrainingController::class, 'adminIndexTraining'])->middleware('role:admin|Super-Admin')->name('admin-index');
        Route::get('coaches-trainings', [TrainingController::class, 'coachIndexTraining'])->middleware('role:coach')->name('coach-index');
        Route::get('players-trainings', [TrainingController::class, 'playerIndexTraining'])->middleware('role:player')->name('player-index');

        Route::middleware('role:coach|admin|Super-Admin')->group(function () {
            Route::get('create', [TrainingController::class, 'create'])->name('create');
            Route::post('store', [TrainingController::class, 'store'])->name('store');
        });

        Route::prefix('{training}')->group(function () {
            Route::get('', [TrainingController::class, 'show'])->middleware('role:player|coach|admin|Super-Admin')->name('show');

            Route::middleware('role:coach|admin|Super-Admin')->group(function () {
                Route::get('edit', [TrainingController::class, 'edit'])->name('edit');
                Route::put('update', [TrainingController::class, 'update'])->name('update');
                Route::delete('destroy', [TrainingController::class, 'destroy'])->name('destroy');
                Route::patch('cancel', [TrainingController::class, 'cancelled'])->name('cancel');
                Route::patch('scheduled', [TrainingController::class, 'scheduled'])->name('scheduled');

                Route::get('player-skills', [SkillAssessmentController::class, 'indexAllPlayerInTraining'])->name('player-skills');
                Route::get('player-performance-review', [PlayerPerformanceReviewController::class, 'indexAllPlayerInTraining'])->name('player-performance-review');

                Route::get('edit-player-attendance/{player}', [TrainingController::class, 'getPlayerAttendance'])->name('player');
                Route::put('update-player-attendance/{player}', [TrainingController::class, 'updatePlayerAttendance'])->name('update-player');

                Route::get('edit-coach-attendance/{coach}', [TrainingController::class, 'getCoachAttendance'])->name('coach');
                Route::put('update-coach-attendance/{coach}', [TrainingController::class, 'updateCoachAttendance'])->name('update-coach');

                Route::post('create-note', [TrainingController::class, 'createNote'])->name('create-note');
                Route::get('edit-note/{note}', [TrainingController::class, 'editNote'])->name('edit-note');
                Route::put('update-note/{note}', [TrainingController::class, 'updateNote'])->name('update-note');
                Route::delete('delete-note/{note}', [TrainingController::class, 'destroyNote'])->name('destroy-note');
            });
        });
    });

    Route::prefix('training-histories')->name('training-histories.')->group(function () {
        Route::get('', [TrainingController::class, 'historiesIndex'])->middleware('role:player|coach|admin|Super-Admin')->name('index');
        Route::get('admins-trainings', [TrainingController::class, 'adminIndexTrainingHistories'])->middleware('role:admin|Super-Admin')->name('admin-index');
        Route::get('coaches-trainings', [TrainingController::class, 'coachIndexTrainingHistories'])->middleware('role:coach')->name('coach-index');
        Route::get('players-trainings', [TrainingController::class, 'playerIndexTrainingHistories'])->middleware('role:player')->name('player-index');
    });

    Route::prefix('match-histories')->name('match-histories.')->group(function () {
        Route::get('', [MatchController::class, 'indexMatchHistories'])->middleware('role:player|coach|admin|Super-Admin')->name('index');
        Route::get('admins-trainings', [MatchController::class, 'adminIndexMatchHistories'])->middleware('role:admin|Super-Admin')->name('admin-index');
        Route::get('coaches-trainings', [MatchController::class, 'coachIndexMatchHistories'])->middleware('role:coach')->name('coach-index');
        Route::get('players-trainings', [MatchController::class, 'playerIndexMatchHistories'])->middleware('role:player')->name('player-index');
    });

    Route::prefix('attendance-reports')->name('attendance-report.')->group(function () {
        Route::middleware('role:coach|admin|Super-Admin')->group(function () {
            Route::get('', [AttendanceReportController::class, 'adminCoachIndex'])->name('admin-coach-index');
            Route::get('match-players', [AttendanceReportController::class, 'matchPlayersAttendanceIndex'])->name('match-players');
            Route::get('training-players', [AttendanceReportController::class, 'trainingPlayersAttendanceIndex'])->name('training-players');
            Route::get('matches-attendance', [AttendanceReportController::class, 'matchesAttendanceIndex'])->name('matches');
            Route::get('trainings-attendance', [AttendanceReportController::class, 'trainingAttendanceIndex'])->name('trainings');
            Route::get('attendance', [AttendanceReportController::class, 'attendanceData'])->name('attendance');
        });

        Route::prefix('{player}')->middleware('role:player|coach|admin|Super-Admin')->group(function () {
                Route::get('', [AttendanceReportController::class, 'show'])->name('show');
                Route::get('attendance-data', [AttendanceReportController::class, 'playerAttendanceData'])->name('player-attendance-data');
                Route::get('training-history', [AttendanceReportController::class, 'playerTrainingIndex'])->name('player-training-index');
                Route::get('match-history', [AttendanceReportController::class, 'playerMatchIndex'])->name('player-match-index');
        });
    });

    Route::prefix('performance-reports')->name('performance-report.')->group(function () {
        Route::get('admin', [PerformanceReportController::class, 'adminIndex'])->middleware('role:admin|Super-Admin')->name('admin-index');
        Route::get('coach', [PerformanceReportController::class, 'coachIndex'])->middleware('role:coach')->name('coach-index');
        Route::get('player', [PerformanceReportController::class, 'playerIndex'])->middleware('role:player')->name('player-index');
    });

    Route::prefix('financial-reports')->name('financial-report.')->group(function () {
        Route::middleware('role:admin|Super-Admin')->group(function () {
            Route::get('', [FinancialReportController::class, 'index'])->name('index');
            Route::get('revenue', [FinancialReportController::class, 'revenueChartData'])->name('revenue-chart-data');
            Route::get('subscription', [FinancialReportController::class, 'subscriptionChartData'])->name('subscription-chart-data');
        });
    });

    Route::prefix('leaderboards')->name('leaderboards.')->group(function () {
        Route::get('', [LeaderboardController::class, 'index'])->middleware('role:player|coach|admin|Super-Admin')->name('index');

        Route::middleware('role:admin|Super-Admin')->group(function () {
            Route::get('admin-teams', [LeaderboardController::class, 'teamLeaderboard'])->name('teams');
            Route::get('admin-players', [LeaderboardController::class, 'playerLeaderboard'])->name('players');
        });

        Route::middleware('role:coach')->group(function () {
            Route::get('coach-teams', [LeaderboardController::class, 'coachTeamLeaderboard'])->name('coach-teams');
            Route::get('coach-players', [LeaderboardController::class, 'coachPlayerLeaderboard'])->name('coach-players');
        });

        Route::middleware('role:player')->group(function () {
            Route::get('player-teams', [LeaderboardController::class, 'playerTeamLeaderboard'])->name('player-teams');
            Route::get('players-teammate', [LeaderboardController::class, 'playersTeammateLeaderboard'])->name('player-teammate');
        });
    });

    Route::prefix('product-categories')->middleware('role:admin|Super-Admin')->name('product-categories.')->group(function () {
        Route::get('', [ProductCategoryController::class, 'index'])->name('index');
        Route::post('store', [ProductCategoryController::class, 'store'])->name('store');

        Route::prefix('{productCategory}')->group(function () {
            Route::get('edit', [ProductCategoryController::class, 'edit'])->name('edit');
            Route::put('update', [ProductCategoryController::class, 'update'])->name('update');
            Route::patch('deactivate', [ProductCategoryController::class, 'deactivate'])->name('deactivate');
            Route::patch('activate', [ProductCategoryController::class, 'activate'])->name('activate');
            Route::delete('delete', [ProductCategoryController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('taxes')->middleware('role:admin|Super-Admin')->name('taxes.')->group(function () {
        Route::get('', [TaxController::class, 'index'])->name('index');
        Route::post('store', [TaxController::class, 'store'])->name('store');

        Route::prefix('{tax}')->group(function () {
            Route::get('edit', [TaxController::class, 'edit'])->name('edit');
            Route::put('update', [TaxController::class, 'update'])->name('update');
            Route::patch('deactivate', [TaxController::class, 'deactivate'])->name('deactivate');
            Route::patch('activate', [TaxController::class, 'activate'])->name('activate');
            Route::delete('delete', [TaxController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('products')->middleware('role:admin|Super-Admin')->name('products.')->group(function () {
        Route::get('', [ProductController::class, 'index'])->name('index');
        Route::post('store', [ProductController::class, 'store'])->name('store');

        Route::prefix('{product}')->group(function () {
            Route::get('edit', [ProductController::class, 'edit'])->name('edit');
            Route::put('update', [ProductController::class, 'update'])->name('update');
            Route::patch('deactivate', [ProductController::class, 'deactivate'])->name('deactivate');
            Route::patch('activate', [ProductController::class, 'activate'])->name('activate');
            Route::delete('delete', [ProductController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::middleware('role:admin|Super-Admin')->group(function () {
            Route::get('', [InvoiceController::class, 'index'])->name('index');
            Route::get('calculate-product-amount', [InvoiceController::class, 'calculateProductAmount'])->name('calculate-product-amount');
            Route::post('calculate-invoice-total', [InvoiceController::class, 'calculateInvoiceTotal'])->name('calculate-invoice-total');
            Route::get('create', [InvoiceController::class, 'create'])->name('create');
            Route::post('store', [InvoiceController::class, 'store'])->name('store');

            Route::prefix('archived')->group(function () {
                Route::get('', [InvoiceController::class, 'deletedData'])->name('archived');
                Route::prefix('{invoice}')->group(function () {
                    Route::get('', [InvoiceController::class, 'showArchived'])->name('show-archived');
                    Route::post('restore', [InvoiceController::class, 'restoreData'])->name('restore');
                    Route::delete('permanent-delete', [InvoiceController::class, 'permanentDeleteData'])->name('permanent-delete');
                });
            });
        });

        Route::prefix('{invoice}')->group(function () {
            Route::middleware('role:admin|Super-Admin')->group(function () {
                Route::get('', [InvoiceController::class, 'show'])->name('show');
                Route::patch('set-open', [InvoiceController::class, 'setOpen'])->name('set-open');
                Route::patch('set-past-due', [InvoiceController::class, 'setPastDue'])->name('set-past-due');
                Route::delete('delete', [InvoiceController::class, 'destroy'])->name('destroy');
            });
            Route::middleware('role:player|coach|admin|Super-Admin')->group(function () {
                Route::patch('set-paid', [InvoiceController::class, 'setPaid'])->name('set-paid');
                Route::patch('set-uncollectible', [InvoiceController::class, 'setUncollectible'])->name('set-uncollectible');
            });
        });
    });

    Route::prefix('subscriptions')->middleware('role:admin|Super-Admin')->name('subscriptions.')->group(function () {
        Route::get('', [SubscriptionController::class, 'index'])->name('index');
        Route::get('available-product', [SubscriptionController::class, 'getAvailablePlayerSubscriptionProduct'])->name('available-product');

        Route::prefix('{subscription}')->group(function () {
            Route::get('', [SubscriptionController::class, 'show'])->name('show');
            Route::get('edit', [SubscriptionController::class, 'edit'])->name('edit');
            Route::put('', [SubscriptionController::class, 'update'])->name('update-tax');
            Route::delete('', [SubscriptionController::class, 'destroy'])->name('destroy');
            Route::post('create-new-invoice', [SubscriptionController::class, 'createNewInvoice'])->name('create-new-invoice');
            Route::get('invoices', [SubscriptionController::class, 'invoices'])->name('invoices');
            Route::patch('set-scheduled', [SubscriptionController::class, 'setScheduled'])->name('set-scheduled');
            Route::patch('set-unsubscribed', [SubscriptionController::class, 'setUnsubscribed'])->name('set-unsubscribed');
            Route::patch('renew-subscription', [SubscriptionController::class, 'renewSubscription'])->name('renew-subscription');
        });
    });

    Route::prefix('skill-assessments')->middleware('role:coach')->name('skill-assessments.')->group(function () {
        Route::get('', [SkillAssessmentController::class, 'index'])->name('index');
        Route::prefix('{player}')->group(function () {
            Route::get('', [PlayerController::class, 'skillStatsDetail'])->name('skill-stats');
            Route::get('create', [SkillAssessmentController::class, 'create'])->name('create');
            Route::post('store', [SkillAssessmentController::class, 'store'])->name('store');
        });

        Route::prefix('skill-stats/{skillStats}')->group(function () {
            Route::get('', [SkillAssessmentController::class, 'edit'])->name('edit');
            Route::put('update', [SkillAssessmentController::class, 'update'])->name('update');
            Route::delete('destroy', [SkillAssessmentController::class, 'destroy'])->name('destroy');
        });
    });

    Route::get('skill-stats', [PlayerController::class, 'skillStatsDetailPlayer'])->middleware('role:player')->name('skill-stats');

    Route::get('performance-reviews', [PlayerPerformanceReviewController::class, 'playerPerformancePage'])->name('performance-reviews');

    Route::prefix('billing-and-payments')->middleware('role:player')->name('billing-and-payments.')->group(function () {
        Route::get('', [BillingPaymentsController::class, 'index'])->name('index');
        Route::get('subscriptions', [SubscriptionController::class, 'playerIndex'])->name('subscriptions');

        Route::prefix('{invoice}')->group(function () {
            Route::get('', [BillingPaymentsController::class, 'show'])->name('show');
        });
    });

    Route::prefix('training-courses')->name('training-videos.')->group(function () {
        Route::get('', [TrainingVideoController::class, 'index'])->middleware('role:player|coach|admin|Super-Admin')->name('index');

        Route::middleware('role:admin|Super-Admin')->group(function () {
            Route::get('create', [TrainingVideoController::class, 'create'])->name('create');
            Route::post('store', [TrainingVideoController::class, 'store'])->name('store');
        });

        Route::prefix('{trainingVideo}')->group(function () {
            Route::middleware('role:player|coach|admin|Super-Admin')->group(function () {
                Route::get('', [TrainingVideoController::class, 'show'])->name('show');
                Route::get('training-completed', [TrainingVideoLessonController::class, 'trainingVideoCompleted'])->name('completed');

                Route::prefix('player-lessons')->group(function () {
                    Route::prefix('{lesson}')->group(function () {
                        Route::get('', [TrainingVideoLessonController::class, 'showPlayerLesson'])->name('show-player-lesson');
                        Route::post('mark-as-complete', [TrainingVideoLessonController::class, 'markAsComplete'])->name('mark-as-complete');
                    });
                });
            });

            Route::middleware('role:coach|admin|Super-Admin')->group(function () {
                Route::get('edit', [TrainingVideoController::class, 'edit'])->name('edit');
                Route::put('update', [TrainingVideoController::class, 'update'])->name('update');
                Route::patch('unpublish', [TrainingVideoController::class, 'unpublish'])->name('unpublish');
                Route::patch('publish', [TrainingVideoController::class, 'publish'])->name('publish');
                Route::get('assigned-player', [TrainingVideoController::class, 'assignPlayer'])->name('assign-player');
                Route::put('update-player', [TrainingVideoController::class, 'updatePlayers'])->name('update-player');
                Route::delete('delete', [TrainingVideoController::class, 'destroy'])->name('destroy');
            });

            Route::prefix('players')->group(function () {
                Route::get('', [TrainingVideoController::class, 'players'])->middleware('role:coach|admin|Super-Admin')->name('players');
                Route::put('assign-players', [TrainingVideoController::class, 'assignPlayers'])->middleware('role:admin|Super-Admin')->name('assign-players');
                Route::prefix('{player}')->group(function () {
                    Route::middleware('role:coach|admin|Super-Admin')->group(function () {
                        Route::get('', [TrainingVideoController::class, 'showPlayer'])->middleware('role:coach|admin|Super-Admin')->name('show-player');
                        Route::get('lessons', [TrainingVideoController::class, 'playerLessons'])->name('player-lessons');
                    });
                    Route::middleware('role:admin|Super-Admin')->group(function () {
                        Route::delete('remove', [TrainingVideoController::class, 'removePlayer'])->name('remove-player');
                    });
                });
            });

            Route::prefix('lessons')->group(function () {
                Route::get('', [TrainingVideoLessonController::class, 'index'])->middleware('role:coach|admin|Super-Admin')->name('lessons-index');
                Route::post('store', [TrainingVideoLessonController::class, 'store'])->middleware('role:admin|Super-Admin')->name('lessons-store');
                Route::prefix('{lesson}')->group(function () {
                    Route::middleware('role:coach|admin|Super-Admin')->group(function () {
                        Route::get('', [TrainingVideoLessonController::class, 'show'])->name('lessons-show');
                        Route::get('players', [TrainingVideoLessonController::class, 'players'])->name('lessons-players');
                    });
                    Route::middleware('role:admin|Super-Admin')->group(function () {
                        Route::get('edit', [TrainingVideoLessonController::class, 'edit'])->name('lessons-edit');
                        Route::put('update', [TrainingVideoLessonController::class, 'update'])->name('lessons-update');
                        Route::delete('destroy', [TrainingVideoLessonController::class, 'destroy'])->name('lessons-destroy');
                        Route::patch('unpublish', [TrainingVideoLessonController::class, 'unpublish'])->name('lessons-unpublish');
                        Route::patch('publish', [TrainingVideoLessonController::class, 'publish'])->name('lessons-publish');
                    });
                });
            });
        });
    });
});
