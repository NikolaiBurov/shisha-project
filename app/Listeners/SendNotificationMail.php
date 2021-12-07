<?php

namespace App\Listeners;

use App\Events\NewUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
class SendNotificationMail
{
    private const EMAILS = ['nikiburov7@gmail.com'];
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NewUser  $event
     * @return void
     */
    public function handle(NewUser $event)
    {
        $admin_mails = self::EMAILS;
        try {
            Mail::raw("User {$event->user} has signed up", function ($message) use ($admin_mails) {
                $message->to(implode(",",$admin_mails));
            });
        } catch (\Exception $e){
            Log::error($e->getMessage());
        }

    }
}
