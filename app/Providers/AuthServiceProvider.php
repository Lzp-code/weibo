<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     * 自定义自动授权的规则，修改boot方法
     */
    //编辑此授权策略定义后在用户控制器中可以使用authorize方法来验证用户授权策略,比
    //如User控制器的edit和update方法：$this->authorize('update',$user);
    //在上面一行，authorize方法里面的第一个参数update指的是app/Policies/UserPolicy文件
    //里面的update方法，$user是app/Policies/UserPolicy文件里面的update方法的第二个参数（框架会默认加载第一个参数：即当前登录的用户）
    public function boot()
    {
        $this->registerPolicies();//编辑策略自动发现的逻辑

        Gate::guessPolicyNamesUsing(function($modelClass){
            // 动态返回模型对应的策略名称，如：// 'App\Models\User' => 'App\Policies\UserPolicy',
            return 'App\Policies\\'.class_basename($modelClass).'Policy';
        });

        //
    }
}
