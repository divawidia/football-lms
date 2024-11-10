<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Subscription;
use App\Notifications\Invoices\InvoiceDueSoon;
use App\Notifications\Subscriptions\SubscriptionDueDateReminderAdmin;
use App\Notifications\Subscriptions\SubscriptionDueDateReminderPlayer;
use App\Repository\UserRepository;
use App\Services\InvoiceService;
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
        $datas = Subscription::whereBetween('nextDueDate', [Carbon::now(), Carbon::now()->addDays(3)])->where('status', 'Scheduled')->get();
        foreach ($datas as $data) {
            $playerName = $data->user->firstName.' '.$data->user->lastName;
            $data->user->notify(new SubscriptionDueDateReminderPlayer($data, $playerName));
            Notification::send($this->userRepository->getAllAdminUsers(), new SubscriptionDueDateReminderAdmin($data, $playerName));
        }

        $this->info('Player with due soon subscription successfully sent notification.');
    }
}
