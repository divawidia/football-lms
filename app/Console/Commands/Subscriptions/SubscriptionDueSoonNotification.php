<?php

namespace App\Console\Commands\Subscriptions;

use App\Models\Subscription;
use App\Notifications\Subscriptions\Admin\SubscriptionDueDateReminderForAdmin;
use App\Notifications\Subscriptions\Player\SubscriptionDueDateReminderForPlayer;
use App\Repository\UserRepository;
use App\Services\SubscriptionService;
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
    private SubscriptionService $subscriptionService;
    public function __construct(UserRepository $userRepository, SubscriptionService $subscriptionService)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $datas = Subscription::whereBetween('nextDueDate', [Carbon::now(), Carbon::now()->addDay()])->where('status', 'Scheduled')->where('isReminderNotified', '0')->get();
        foreach ($datas as $data) {
            $data->update(['isReminderNotified' => '1']);
            $data->user->notify(new SubscriptionDueDateReminderForPlayer($data));
            Notification::send($this->userRepository->getAllAdminUsers(), new SubscriptionDueDateReminderForAdmin($data));
            $this->subscriptionService->renewSubscription($data);
        }

        $this->info('Player with due soon subscription successfully sent notification.');
    }
}
