<?php

namespace App\Observers;

use App\Models\UserModel;
use Illuminate\Support\Facades\Log;

// 只支持单条数据操作，批量操作无法记录
class UserObserver
{
    /**
     * Handle the UserModel "created" event.
     *
     * @param  \App\Models\UserModel  $user
     * @return void
     */
    public function created(UserModel $user)
    {
        //
    }

    /**
     * Handle the UserModel "updated" event.
     *
     * @param  \App\Models\UserModel  $user
     * @return void
     */
    public function updated(UserModel $user)
    {
        $context = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email']
        ];
        Log::info('更新: ' , $context);
    }

    /**
     * Handle the UserModel "deleted" event.
     *
     * @param  \App\Models\UserModel  $user
     * @return void
     */
    public function deleted(UserModel $user)
    {
        //
    }

    /**
     * Handle the UserModel "restored" event.
     *
     * @param  \App\Models\UserModel  $user
     * @return void
     */
    public function restored(UserModel $user)
    {
        //
    }

    /**
     * Handle the UserModel "force deleted" event.
     *
     * @param  \App\Models\UserModel  $user
     * @return void
     */
    public function forceDeleted(UserModel $user)
    {
        //
    }
}
