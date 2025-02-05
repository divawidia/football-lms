<?php

namespace App\Services;

use App\Helpers\DatatablesHelper;
use App\Models\Coach;
use App\Models\Player;
use App\Models\TrainingNote;
use App\Models\Team;
use App\Models\Training;
use App\Notifications\TrainingSchedules\AdminCoach\TrainingCanceledForAdminCoachNotification;
use App\Notifications\TrainingSchedules\AdminCoach\TrainingCreatedForAdminCoachNotification;
use App\Notifications\TrainingSchedules\AdminCoach\TrainingDeletedForAdminCoachNotification;
use App\Notifications\TrainingSchedules\AdminCoach\TrainingUpdatedForAdminCoachNotification;
use App\Notifications\TrainingSchedules\Player\TrainingCanceledForPlayerNotification;
use App\Notifications\TrainingSchedules\Player\TrainingCreatedForPlayerNotification;
use App\Notifications\TrainingSchedules\Player\TrainingDeletedForPlayerNotification;
use App\Notifications\TrainingSchedules\Player\TrainingUpdatedForPlayerNotification;
use App\Notifications\TrainingSchedules\TrainingCompletedNotification;
use App\Notifications\TrainingSchedules\TrainingNoteCreatedNotification;
use App\Notifications\TrainingSchedules\TrainingNoteDeletedNotification;
use App\Notifications\TrainingSchedules\TrainingNoteUpdatedNotification;
use App\Notifications\TrainingSchedules\TrainingOngoingNotification;
use App\Notifications\TrainingSchedules\TrainingScheduleAttendance;
use App\Repository\Interface\TeamRepositoryInterface;
use App\Repository\Interface\TrainingRepositoryInterface;
use App\Repository\Interface\UserRepositoryInterface;
use App\Repository\PlayerPerformanceReviewRepository;
use App\Repository\PlayerSkillStatsRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Yajra\DataTables\Facades\DataTables;

class TrainingService extends Service
{
    private TrainingRepositoryInterface $trainingRepository;
    private TeamRepositoryInterface $teamRepository;
    private TeamService $teamService;
    private PlayerSkillStatsRepository $playerSkillStatsRepository;
    private PlayerPerformanceReviewRepository $playerPerformanceReviewRepository;
    private DatatablesHelper $datatablesHelper;
    public function __construct(
        TrainingRepositoryInterface $trainingRepository,
        TeamRepositoryInterface $teamRepository,
        TeamService $teamService,
        PlayerSkillStatsRepository $playerSkillStatsRepository,
        PlayerPerformanceReviewRepository $playerPerformanceReviewRepository,
        DatatablesHelper                  $datatablesHelper)
    {
        $this->trainingRepository = $trainingRepository;
        $this->teamRepository = $teamRepository;
        $this->teamService = $teamService;
        $this->playerSkillStatsRepository = $playerSkillStatsRepository;
        $this->playerPerformanceReviewRepository = $playerPerformanceReviewRepository;
        $this->datatablesHelper = $datatablesHelper;
    }

    public function indexTraining(Team $team = null, $startDate = null, $endDate = null): Collection
    {
        return $this->trainingRepository->getAll(['team'], teams: $team, status: ['Scheduled', 'Ongoing'], startDate: $startDate, endDate: $endDate);
    }
    public function coachTeamsIndexTraining(Coach $coach, $startDate = null, $endDate = null): Collection
    {
        return $this->trainingRepository->getByRelation($coach, ['team'], ['Scheduled', 'Ongoing'], startDate: $startDate, endDate: $endDate);
    }
    public function playerTeamsIndexTraining(Player $player, $startDate = null, $endDate = null): Collection
    {
        return $this->trainingRepository->getByRelation($player, ['team'],  ['Scheduled', 'Ongoing'], startDate: $startDate, endDate: $endDate);
    }


    public function indexTrainingHistories(Team $team = null, $startDate = null, $endDate = null): Collection
    {
        return $this->trainingRepository->getAll(['team'], teams: $team, startDate: $startDate, endDate: $endDate);
    }
    public function coachTeamsIndexTrainingHistories(Coach $coach, $startDate = null, $endDate = null): Collection
    {
        return $this->trainingRepository->getByRelation($coach, ['team'], startDate: $startDate, endDate: $endDate);
    }
    public function playerTeamsIndexTrainingHistories(Player $player, $startDate = null, $endDate = null): Collection
    {
        return $this->trainingRepository->getByRelation($player, ['team'], startDate: $startDate, endDate: $endDate);
    }


    public function makeTrainingCalendar($trainingsData): array
    {
        $events = [];
        foreach ($trainingsData as $training) {
            $events[] = [
                'id' => $training->id,
                'title' => $training->team->teamName.' - '.$training->topic,
                'start' => $training->date.' '.$training->startTime,
                'end' => $training->date.' '.$training->endTime,
                'className' => 'bg-warning'
            ];
        }
        return $events;
    }

    public function trainingCalendar(): array
    {
        $trainings = $this->indexTraining();
        return $this->makeTrainingCalendar($trainings);
    }
    public function coachTeamsTrainingCalendar(Coach $coach): array
    {
        $trainings = $this->coachTeamsIndexTraining($coach);
        return $this->makeTrainingCalendar($trainings);
    }
    public function playerTeamsTrainingCalendar(Player $player): array
    {
        $trainings = $this->playerTeamsIndexTraining($player);
        return $this->makeTrainingCalendar($trainings);
    }


    public function trainingHistoriesCalendar(): array
    {
        $trainings = $this->indexTrainingHistories();
        return $this->makeTrainingCalendar($trainings);
    }

    public function coachTeamsTrainingHistoriesCalendar(Coach $coach): array
    {
        $trainings = $this->coachTeamsIndexTrainingHistories($coach);
        return $this->makeTrainingCalendar($trainings);
    }

    public function playerTeamsTrainingHistoriesCalendar(Player $player): array
    {
        $trainings = $this->playerTeamsIndexTrainingHistories($player);
        return $this->makeTrainingCalendar($trainings);
    }


    public function makeDataTablesTraining($trainingData): JsonResponse
    {
        return Datatables::of($trainingData)
            ->addColumn('action', function ($item) {
                $dropdownItem = $this->datatablesHelper->linkDropdownItem(route: route('training-schedules.show', $item->hash), icon: 'visibility', btnText: 'View training session');
                if (isAllAdmin()) {
                    if ($item->status == 'Scheduled'){
                        $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('cancelBtn', $item->id, 'danger', icon: 'block', btnText: 'Cancel training');
                        $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('edit-training-btn', $item->id,  icon: 'edit', btnText: 'Edit Training');
                    } elseif ($item->status == 'Cancelled') {
                        $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('scheduled-btn', $item->id, 'warning', icon: 'check_circle', btnText: 'Set training to Scheduled');
                        $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('edit-training-btn', $item->id,  icon: 'edit', btnText: 'Edit Training');
                    }
                    $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('delete', $item->id, iconColor: 'danger', icon: 'delete', btnText: 'Delete training');
                }
                return $this->datatablesHelper->dropdown(function () use ($dropdownItem) {
                    return $dropdownItem;
                });
            })
            ->editColumn('team', function ($item) {
                return $this->datatablesHelper->name($item->team->logo, $item->team->teamName, $item->team->ageGroup, route('team-managements.show', $item->team->hash));
            })
            ->editColumn('date', function ($item) {
                return $this->datatablesHelper->startEndDate($item);
            })
            ->editColumn('status', function ($item) {
                return $this->datatablesHelper->eventStatus($item->status);
            })
            ->rawColumns(['action','team','status'])
            ->addIndexColumn()
            ->make();
    }

    public function adminDataTablesTraining(): JsonResponse
    {
        $data = $this->indexTraining();
        return $this->makeDataTablesTraining($data);
    }
    public function coachTeamsDataTablesTraining(Coach $coach): JsonResponse
    {
        $data = $this->coachTeamsIndexTraining($coach);
        return $this->makeDataTablesTraining($data);
    }
    public function playerTeamsDataTablesTraining(Player $player): JsonResponse
    {
        $data = $this->playerTeamsIndexTraining($player);
        return $this->makeDataTablesTraining($data);
    }


    public function adminTrainingHistories(): JsonResponse
    {
        $data = $this->indexTrainingHistories();
        return $this->makeDataTablesTraining($data);
    }
    public function coachTeamsDataTablesTrainingHistories(Coach $coach): JsonResponse
    {
        $data = $this->coachTeamsIndexTrainingHistories($coach);
        return $this->makeDataTablesTraining($data);
    }
    public function playerTeamsDataTablesTrainingHistories(Player $player): JsonResponse
    {
        $data = $this->playerTeamsIndexTrainingHistories($player);
        return $this->makeDataTablesTraining($data);
    }



    public function dataTablesPlayerSkills(Training $training): JsonResponse
    {
        $data = $training->players;
        return Datatables::of($data)
            ->addColumn('action', function ($item) use ($training){
                $stats = $this->playerSkillStatsRepository->getByPlayer($item, training: $training, retrievalMethod: 'first');
                $review = $this->playerPerformanceReviewRepository->getByPlayer($item, training: $training, retrievalMethod: 'first');

                $dropdownItem = $this->datatablesHelper->linkDropdownItem(route: route('player-managements.skill-stats', $item->hash), icon: 'visibility', btnText: 'View Player Skill Stats');
                $statsBtn = '';
                $reviewBtn = '';
                if ( isCoach() ){
                    (!$stats) ? $statsBtn = '<a class="dropdown-item addSkills" id="'.$item->id.'" data-trainingId="'.$training->id.'"><span class="material-icons">edit</span> Evaluate Player Skills Stats</a>'
                        : $statsBtn = '<a class="dropdown-item editSkills" id="'.$item->id.'" data-trainingId="'.$training->id.'" data-statsId="'.$stats->id.'"><span class="material-icons">edit</span> Edit Player Skills Stats</a>';

                    (!$review) ? $reviewBtn = '<a class="dropdown-item addPerformanceReview" id="'.$item->id.'" data-trainingId="'.$training->id.'"><span class="material-icons">add</span> Add Player Performance Review</a>'
                        : $reviewBtn = '<a class="dropdown-item editPerformanceReview" id="'.$item->id.'" data-trainingId="'.$training->id.'"  data-reviewId="'.$review->id.'"><span class="material-icons">edit</span> Edit Player Performance Review</a>';
                }
                return $this->datatablesHelper->dropdown(function () use ($dropdownItem, $statsBtn, $reviewBtn) {
                    return $dropdownItem . $statsBtn . $reviewBtn;
                });
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesHelper->name($item->user->foto, $this->getUserFullName($item->user), $item->position->name,route('player-managements.show', $item->hash));
            })
            ->editColumn('stats_status', function ($item) use ($training){
                $stats = $this->playerSkillStatsRepository->getByPlayer($item, training: $training, retrievalMethod: 'first');
                return ($stats) ? 'Skill stats have been added' : 'Skill stats still not added yet';
            })
            ->editColumn('stats_created', function ($item) use ($training){
                $stats = $this->playerSkillStatsRepository->getByPlayer($item, training: $training, retrievalMethod: 'first');
                return ($stats) ? $this->convertToDatetime($stats->created_at) : '-';
            })
            ->editColumn('stats_updated', function ($item) use ($training){
                $stats = $this->playerSkillStatsRepository->getByPlayer($item, training: $training, retrievalMethod: 'first');
                return ($stats) ? $this->convertToDatetime($stats->updated_at) : '-';
            })
            ->editColumn('performance_review', function ($item) use ($training){
                $review = $this->playerPerformanceReviewRepository->getByPlayer($item, training: $training, retrievalMethod: 'first');
                return ($review) ? $review->performanceReview : 'Performance review still not added yet';
            })
            ->editColumn('performance_review_created', function ($item) use ($training){
                $review = $this->playerPerformanceReviewRepository->getByPlayer($item, training: $training, retrievalMethod: 'first');
                return ($review) ? $this->convertToDatetime($review->created_at) : '-';
            })
            ->editColumn('performance_review_last_updated', function ($item) use ($training){
                $review = $this->playerPerformanceReviewRepository->getByPlayer($item, training: $training, retrievalMethod: 'first');
                return ($review) ? $this->convertToDatetime($review->updated_at) : '-';
            })
            ->rawColumns(['action','name', 'stats_status', 'stats_created', 'stats_updated', 'performance_review', 'performance_review_created','performance_review_last_updated'])
            ->addIndexColumn()
            ->make();
    }

    public function allSkills(Training $training, Player $player = null)
    {
        return ($player) ? $this->playerSkillStatsRepository->getByPlayer($player, training:  $training, retrievalMethod: 'single') : null;
    }

    public function playerPerformanceReviews(Training $training, Player $player = null)
    {
        return ($player) ? $this->playerPerformanceReviewRepository->getByPlayer($player, training:  $training, retrievalMethod: 'single') : null;
    }

    public function totalParticipant(Training $training, Team $team = null): int
    {
        $players = $training->players()->count();
        $coaches = $training->coaches()->count();
        return $players + $coaches;
    }

    public function playerTotalTraining(Player $player, $startDate = null, $endDate = null, $status = null)
    {
        return $this->trainingRepository->getAll(player: $player, status: $status, startDate: $startDate, endDate: $endDate, retrievalMethod: 'count');
    }

    public function playerAttended(Training $training)
    {
        return $this->trainingRepository->getRelationData($training, 'players', attendanceStatus: 'Attended', retrieveType: 'count');
    }

    public function playerIllness(Training $training)
    {
        return $this->trainingRepository->getRelationData($training, 'players', attendanceStatus: 'Illness', retrieveType: 'count');
    }

    public function playerInjured(Training $training)
    {
        return $this->trainingRepository->getRelationData($training, 'players', attendanceStatus: 'Injured', retrieveType: 'count');
    }

    public function playerOther(Training $training)
    {
        return $this->trainingRepository->getRelationData($training, 'players', attendanceStatus: 'Other', retrieveType: 'count');
    }
    public function playerRequiredAction(Training $training)
    {
        return $this->trainingRepository->getRelationData($training, 'players', attendanceStatus: 'Required Action', retrieveType: 'count');
    }

    public function playerDidntAttend(Training $training)
    {
        return $this->playerIllness($training) + $this->playerInjured($training) + $this->playerOther($training);
    }

    public function coachAttended(Training $training)
    {
        return $this->trainingRepository->getRelationData($training, 'coaches', attendanceStatus: 'Attended', retrieveType: 'count');
    }

    public function coachIllness(Training $training)
    {
        return $this->trainingRepository->getRelationData($training, 'coaches', attendanceStatus: 'Illness', retrieveType: 'count');
    }

    public function coachInjured(Training $training)
    {
        return $this->trainingRepository->getRelationData($training, 'coaches', attendanceStatus: 'Injured', retrieveType: 'count');
    }

    public function coachOther(Training $training)
    {
        return $this->trainingRepository->getRelationData($training, 'coaches', attendanceStatus: 'Other', retrieveType: 'count');
    }

    public function coachDidntAttend(Training $training)
    {
        return $this->coachIllness($training) + $this->coachInjured($training) + $this->coachOther($training);
    }

    public function totalAttend(Training $training)
    {
        return $this->playerAttended($training) + $this->coachAttended($training);
    }

    public function totalDidntAttend(Training $training)
    {
        return $this->playerDidntAttend($training) + $this->coachDidntAttend($training);
    }

    public function totalIllness(Training $training)
    {
        return $this->playerIllness($training) + $this->coachIllness($training);
    }

    public function totalInjured(Training $training)
    {
        return $this->playerInjured($training) + $this->coachInjured($training);
    }

    public function totalOther(Training $training)
    {
        return $this->playerOther($training) + $this->coachOther($training);
    }

    public function createTraining(Coach $coach = null)
    {
        return ($coach) ? $coach->teams : $this->teamRepository->getAll();
    }

    public function storeTraining(array $data, $loggedUser){
        $data['userId'] = $loggedUser->id;
        $data['startDatetime'] = $this->convertToTimestamp($data['date'], $data['startTime']);
        $data['endDatetime'] = $this->convertToTimestamp($data['date'], $data['endTime']);
        $training = $this->trainingRepository->create($data);

        Notification::send($this->teamService->teamsCoachesAdmins($training->team), new TrainingCreatedForAdminCoachNotification($loggedUser, $training, $training->team, $this->getUserRoleName($loggedUser)));
        Notification::send($this->teamService->teamsPlayers($training->team), new TrainingCreatedForPlayerNotification($loggedUser, $training, $training->team));

        return $training;
    }

    public function updateTraining(array $data, Training $training, $loggedUser){
        $data['startDatetime'] = $this->convertToTimestamp($data['date'], $data['startTime']);
        $data['endDatetime'] = $this->convertToTimestamp($data['date'], $data['endTime']);
        $training->update($data);

        if (array_key_exists('teamId', $data)){
            $training->players()->sync($training->team->players);
            $training->coaches()->sync($training->team->coaches);

            Notification::send($this->teamService->teamsCoachesAdmins($training->team), new TrainingUpdatedForAdminCoachNotification($loggedUser, $training, $training->team, $this->getUserRoleName($loggedUser)));
            Notification::send($this->teamService->teamsPlayers($training->team), new TrainingUpdatedForPlayerNotification($loggedUser, $training, $training->team));
        }
        return $training;
    }

    public function setStatus(Training $training, $status, $loggedUser = null): bool
    {
        $this->sendNotificationByStatus($training, $status, $loggedUser);
        return $training->update(['status' => $status]);
    }

    public function sendNotificationByStatus(Training $training, $status, $loggedUser = null)
    {
        if ($status == 'Ongoing') {
            Notification::send($this->teamService->teamsAllParticipants($training->team), new TrainingOngoingNotification($training, $training->team));
        } elseif ($status == 'Completed') {
            Notification::send($this->teamService->teamsAllParticipants($training->team), new TrainingCompletedNotification($training, $training->team));
        } elseif ($status == 'Cancelled') {
            Notification::send($this->teamService->teamsCoachesAdmins($training->team), new TrainingCanceledForAdminCoachNotification($loggedUser, $training, $training->team, $this->getUserRoleName($loggedUser)));
            Notification::send($this->teamService->teamsPlayers($training->team), new TrainingCanceledForPlayerNotification($training, $training->team));
        } elseif ($status == 'Scheduled') {
            Notification::send($this->teamService->teamsCoachesAdmins($training->team), new TrainingCanceledForAdminCoachNotification($loggedUser, $training, $training->team, $this->getUserRoleName($loggedUser)));
            Notification::send($this->teamService->teamsPlayers($training->team), new TrainingCanceledForPlayerNotification($training, $training->team));
        }
    }



    public function getPlayerAttendance(Training $training, Player $player)
    {
        return $training->players()->find($player->id);
    }
    public function getCoachAttendance(Training $training, Coach $coach)
    {
        return $training->coaches()->find($coach->id);
    }

    public function updatePlayerAttendanceStatus($data, Training $training, Player $player): int
    {
        $player->user->notify(new TrainingScheduleAttendance($training, $data['attendanceStatus']));

        return $training->players()->updateExistingPivot($player->id, ['attendanceStatus'=> $data['attendanceStatus'], 'note' => $data['note']]);
    }
    public function updateCoachAttendanceStatus($data, Training $training, Coach $coach): int
    {
        $coach->user->notify(new TrainingScheduleAttendance($training, $data['attendanceStatus']));

        return $training->coaches()->updateExistingPivot($coach->id, ['attendanceStatus'=> $data['attendanceStatus'], 'note' => $data['note']]);
    }

    public function createNote($data, Training $training)
    {
        Notification::send($this->teamService->teamsAllParticipants($training->team), new TrainingNoteCreatedNotification($training, $training->team));
        $training->notes()->create($data);
        return $training;
    }
    public function updateNote($data, Training $training, TrainingNote $note): bool
    {
        Notification::send($this->teamService->teamsAllParticipants($training->team), new TrainingNoteUpdatedNotification($training, $training->team));
        return $note->update($data);
    }
    public function destroyNote(Training $training, TrainingNote $note): ?bool
    {
        Notification::send($this->teamService->teamsAllParticipants($training->team), new TrainingNoteDeletedNotification($training, $training->team));
        return $note->delete();
    }

    public function destroy(Training $training, $loggedUser)
    {
        Notification::send($this->teamService->teamsCoachesAdmins($training->team), new TrainingDeletedForAdminCoachNotification($loggedUser, $training, $training->team, $this->getUserRoleName($loggedUser)));
        Notification::send($this->teamService->teamsPlayers($training->team), new TrainingDeletedForPlayerNotification($training, $training->team));
        return $training->delete();
    }
}
