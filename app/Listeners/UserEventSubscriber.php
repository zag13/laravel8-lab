<?php

namespace App\Listeners;

use App\Events\UserLoginEvent;
use App\Utils\Z\ZLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UserEventSubscriber
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
     *  为订阅者注册监听器
     * @param $events
     */
    public function subscribe($events)
    {
        $events->listen(
            UserLoginEvent::class,
            UserEventSubscriber::class . '@onUserLogin'
        );
    }

    public function onUserLogin(UserLoginEvent $event)
    {
        /*$user = $event->user;
        $context = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email']
        ];
        ZLog::channel('login')->info('登录', $context);*/
        return $event->user;
    }
}
