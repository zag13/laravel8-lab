<?php

namespace App\Listeners;

use App\Events\LoginEvent;
use App\Services\Utils\ZLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LoginListener
{
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
     * @param object $event
     * @return void
     */
    public function handle(LoginEvent $event)
    {
        $user = $event->user;
        $context = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email']
        ];
        ZLog::channel('login')->info('登录', $context);
    }
}
