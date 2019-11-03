<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{

    public function create(){
        return view('users.create');
    }

    public function show(User $user){
        return view('users.show',compact('user'));
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

        Auth::login($user);
        session()->flash('success','欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('users.show',[$user]);

    }


}
