<?php

namespace App\Policies;


use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    //要使用User的授权策略，先创建一个名为UserPolicy的授权策略类文件
    //所有生成的授权策略都会被放置在app/Policies下

    use HandlesAuthorization;


    //创建update方法，用于用户更新时的权限验证（当前登录的用户实例，要进行授权的用户实例）
    //调用是，第二个参数（User $user）不需要传参，框架会自动获取
    public function update(User $currentUser,User $user){
        return $currentUser->id === $user->id;
    }

    //创建destroy方法，用于删除用户时的权限验证（当前登录的用户实例，要进行授权的用户实例）
    //调用是，第二个参数（User $user）不需要传参，框架会自动获取
    public function destroy(User $currentUser,User $user){
        return $currentUser->is_admin && $currentUser->id !== $user->id;

    }

    //创建follow方法，用于关注用户时的权限验证（当前登录的用户实例，要进行授权的用户实例）
    //调用是，第二个参数（User $user）不需要传参，框架会自动获取
    public function follow(User $currentUser,User $user){
        return $currentUser->id !== $user->id;
    }


}
