<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller
{

    public function __construct(){
        $this->middleware('guest',[
            'only'=>['create']
        ]);
    }

    public function create(){
        return view('sessions.create');
    }

    public function store(Request $request)
    {
        //获得用户在登录页面输入的信息并验证
        $credentials = $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);

        //attempt方法会接收一个数组作为参数，在数据库中寻找相应数据，$request->has('remember')——用户在页面上是否有点击‘记住我’
        if (Auth::attempt($credentials, $request->has('remember'))) {

            //Auth::user()方法获取当前登录用户的信息
            //Auth::user()->activated ：用户是否已被激活（数据库的activated字段）
            if(Auth::user()->activated){
                session()->flash('success', '欢迎回来！');
//            return route('users.show', Auth::user());
                $fallback = route('users.show', Auth::user());
                return redirect()->intended($fallback);
            }else{
                Auth::logout();
                session()->flash('warning','你的账号未激活，请检查邮箱中的注册邮件进行激活。');
                return redirect('/');

            }



        } else {
            session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
            return redirect()->back()->withInput();
        }
    }

    public function destory(){
        Auth::logout();
        session()->flash('success','您已成功退出！');
        return redirect('help');
    }



}
