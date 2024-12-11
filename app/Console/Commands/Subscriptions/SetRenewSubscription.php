<?php

namespace App\Console\Commands\Subscriptions;

use App\Models\Subscription;
use App\Notifications\Subscriptions\SubscriptionDueDateReminderAdmin;
use App\Notifications\Subscriptions\SubscriptionDueDateReminderPlayer;
use App\Repository\UserRepository;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SetRenewSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:subscription-renew';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Renew subscription at due date';

    private SubscriptionService $subscriptionService;
    public function __construct(SubscriptionService $subscriptionService)
    {
        parent::__construct();
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        // Update records where end_date is less than the current date
        $datas = Subscription::where('nextDueDate', '<=', $now)->where('status', 'Scheduled')->get();
        foreach ($datas as $data) {
            $this->subscriptionService->renewSubscription($data);
        }

        $this->info('Subscription that reaching due date are successfully renewed.');
    }
}
