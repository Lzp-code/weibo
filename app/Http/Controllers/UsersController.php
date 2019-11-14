<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UsersController extends Controller
{

    public function __construct(){

        //构建函数时，要求必须是登录用户才能操作本控制器的方程，除了except
        $this->middleware('auth',[
            'except'=>['show','create','store','index','confirmEmail']
        ]);

        //只允许未登录用户访问的方程
        $this->middleware('guest',[
            'only'=>['create']
        ]);

    }

    public function index(){
//        $users = User::all();     //获得所有用户
        $users = User::paginate(2);     //获得用户列表，每页两个
//        compact('users')——创建一个键名为users，键值为$users的变量，赋值给'users.index界面
        return view('users.index',compact('users'));
    }

    public function create(){
        return view('users.create');
    }



    public function show(User $user){
        //User $user是获取所要查看的人的信息，User是自动加载User_Model
        $statuses = $user->statuses()
            ->orderBy('created_at','desc')
            ->paginate(10);
        return view('users.show',compact('user','statuses'));
    }

    public function store(Request $request){
        //validate就类似于thinkPHP的自动验证方法
        $this->validate($request,[
           'name'=>'required|max:50',
            'email'=>'required|email|unique:users|max:255',//针对数据表users的唯一email验证
            'password'=>'required|confirmed|min:6'//最少六位，要求两次输入的密码要一致
        ]);
        $user = User::create([
           'name'=>$request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password),
        ]);

        //如果要发邮件：
        $this->sendEmailConfirmationTo($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');


        //如果不要发邮件：
//        Auth::login($user);
//        session()->flash('success','欢迎，您将在这里开启一段新的旅程~');
//        return redirect()->route('users.show',[$user]);
    }


    protected function sendEmailConfirmationTo($user){
        $view = 'emails.confirm';
        $data = compact('user');
//        $from = '763752090@qq.com';
        $name = 'admin';
        $to = $user->email;
        $subject = "感谢注册 Weibo 应用！请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
            $message->from($from, $name)->to($to)->subject($subject);
        });
    }

    public function confirmEmail($token){
        $user = User::where('activation_token',$token)->firstOrFail();

        $user->activated  = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success','恭喜你，激活成功！');
        return redirect()->route('users.show',[$user]);
    }







    public function edit(User $user){   //用Laravel的隐性路由模型绑定，直接读取对应id的用户实例
        $this->authorize('update',$user);
        return view('users.edit',compact('user'));
    }


    //update参数接收两个参数，一个是自动解析用户ID对应的用户实例，一个是用户表单提交的数据
    public function update(User $user,Request $request){
        $this->authorize('update',$user);
        $this->validate($request,[
            'name'=>'required|max:50',
            'password'=>'nullable|confirmed|min:6'
        ]);

        $data = [];
        $data['name'] = $request->name;
        if($request->password){
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

       session()->flash('success','个人资料更新成功！');

        //$user是User模型的对象实例，route方法自动获取User模型的主键，以下代码相当于redirect()->route('users.show', [$user->id]);
        return redirect()->route('users.show',$user);
    }

    public function destroy(User $user){
        $this->authorize('destroy',$user);
        $user->delete();
        session()->flash('success','成功删除用户！');
        return back();
    }

    public function followings(User $user)
    {
        $users = $user->followings()->paginate(30);
        $title = $user->name . '关注的人';
        return view('users.show_follow', compact('users', 'title'));
    }

    public function followers(User $user)
    {
        $users = $user->followers()->paginate(30);
        $title = $user->name . '的粉丝';
        return view('users.show_follow', compact('users', 'title'));
    }


}
