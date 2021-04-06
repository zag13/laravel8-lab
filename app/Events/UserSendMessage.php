<?php

namespace App\Events;

use App\Models\UserModel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserSendMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $connection = 'redis';
    public $queue = 'broadcast-message';

    public $user, $message, $groupId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(UserModel $user, $message, $groupId = 0)
    {
        $this->user = $user;
        $this->message = $message;
        $this->groupId = $groupId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        if ($this->groupId == 0) {
            return new Channel('public');
        }
        return new PrivateChannel('wechat.group.' . $this->groupId);
    }

    public function broadcastAs()
    {
        return 'user.message';
    }

    public function broadcastWith()
    {
        return [
            'username' => $this->user->name,
            'message' => $this->message
        ];
    }
}
