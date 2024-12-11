<?php

namespace App\Console\Commands\Subscriptions;

use App\Models\Subscription;
use App\Notifications\Subscriptions\SubscriptionDueDateReminderAdmin;
use App\Notifications\Subscriptions\SubscriptionDueDateReminderPlayer;
use App\Repository\UserRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SubscriptionDueSoonNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:subscription-due-soon-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sent reminder notification to players with due soon subscription';

    private UserRepository $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $datas = Subscription::whereBetween('nextDueDate', [Carbon::now(), Carbon::now()->addDays(3)])->where('status', 'Scheduled')->where('isReminderNotified', '0')->get();
        foreach ($datas as $data) {
            $data->update(['isReminderNotified' => '1']);
            $playerName = $data->user->firstName.' '.$data->user->lastName;
            $data->user->notify(new SubscriptionDueDateReminderPlayer($data, $playerName));
            Notification::send($this->userRepository->getAllAdminUsers(), new SubscriptionDueDateReminderAdmin($data, $playerName));
        }

        $this->info('Player with due soon subscription successfully sent notification.');
    }
}
