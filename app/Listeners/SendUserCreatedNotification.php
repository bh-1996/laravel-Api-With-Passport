<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Models\User;
use App\Notifications\UserCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendUserCreatedNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserCreated $event)
    {
        $admin = User::where('id', 1)->first(); // Or however you define your admin
        Log::info("message succeee= ".$admin);

        Notification::send($admin, new UserCreatedNotification($event->user));
    }
}
