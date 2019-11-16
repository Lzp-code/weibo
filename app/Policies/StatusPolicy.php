<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;
use App\Models\Status;

class StatusPolicy
{
    use HandlesAuthorization;


    //User $user—系统自动获取登录的用户，Status $status—要删除的那条微博
    public function destroy(User $user,Status $status){
        return $status->user_id === $user->id;
    }

}