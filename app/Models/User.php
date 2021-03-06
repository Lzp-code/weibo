<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function gravatar($size = '100'){
//        $hash = md5(strtolower(trim($this->attributes['email'])));
//        return "http://www.gravatar.com/avatar/$hash?s=$size";

        return "http://weibo.test/headimg.jpeg";

    }

    //boot会在模型类完成初始化之后进行加载
    public static function boot(){
        parent::boot();
//        Eloquent 模型触发几个事件，允许你挂接到模型生命周期的如下节点
//        ： retrieved、creating、created、updating、updated、saving、saved、deleting、deleted、restoring 和 restored。
//        事件允许你每当特定模型保存或更新数据库时执行代码。每个事件通过其构造器接受模型实例。
//        retrieved 事件在现有模型从数据库中查找数据时触发。当新模型每一次保存时，creating 和 created 事件被触发。
//        如果数据库中已经存在模型并且调用了 save 方法，updating / updated 事件被触发。这些情况下，saving / saved 事件也被触发。
        static::creating(function($user){
           $user->activation_token  = Str::random(10); //在模型被创建前给用户生成激活令牌（给用户数据库字段一个activation_token的值）
        });


        //比如在控制器调用save的方法之前可以触发一个updating方法，给$user的name设置为AAAAA，则执行save时可以把$user的name设置为AAAAA：
//        static::updating(function($user){
//            $user->name  = 'AAAAA'; //在模型被创建前给用户生成激活令牌（给用户数据库字段一个activation_token的值）
//        });

    }



    public function statuses(){
        //hasMany表示一对多的关系
        return $this->hasMany(Status::class);
    }

    public function feed(){
        //获取自己和已关注用户的微博
        $user_ids = $this->followings->pluck('id')->toArray();
        array_push($user_ids,$this->id);
        return Status::whereIn('user_id',$user_ids)->with('user')->orderBy('created_at','desc');
    }

    //获取粉丝列表
    public function followers(){
        //belongsToMany表示多对多的关系
        //laravel会将两个关联模型的名称进行合并，并按照字母排序。
        //如这里的：在user模型中关联user模型：$this->belongsToMany(User::Class);
        //上次的关联关系表名为user_user，我们自定义把关联表名改为'followers'
        //并且通过传递额外参数至belongsToMany方法来自定义'followers'表里面的字段名称
        //'user_id'是定义在关联中的模型外键名，（这里是指用户自己）
        //'follower_id'的要合并的模型外键名（这里指关注我们的人）
        return $this->belongsToMany(User::class,'followers','user_id','follower_id');
    }

    //获取我关注的
    public function followings(){
        return $this->belongsToMany(User::class,'followers','follower_id','user_id');
    }

    public function follow($user_ids){
        if(!is_array($user_ids)){
            $user_ids = compact('user_ids');
        }
        //sync方法接收两个参数
        //一个是要进行添加的id，一个是是否移除其他不包含在要添加的关联的id数组中的id，false表示不移除
        $this->followings()->sync($user_ids,false);
    }

    public function unfollow($user_ids){
        if(!is_array($user_ids)){
            $user_ids = compact('user_ids');
        }
        $this->followings()->detach($user_ids);
    }


    //判断当前登录的用户是否有关注用户$user_id
    public function isFollowing($user_id){
        //实现的方法是判断用户$user_id是否在当前用户的关注人列表上
        return $this->followings->contains($user_id);
    }




















}
