<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.UserModel.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});

Broadcast::channel('wechat.group.{$id}', function ($user, $id) {
    // 模拟用户属于哪个微信群
    $group_users = [
        [
            'group_id' => 1,
            'user_id' => 1,
        ],
        [
            'group_id' => 1,
            'user_id' => 2
        ]
    ];

    $result = collect($group_users)->groupBy('group_id')
        ->first(function ($group, $groupId) use ($user, $id) {
            return $id == $groupId && $group->contains('user_id', $user->id);
        });

    return $result == null ? false : true;
});
