<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Status;
use Auth;

class StatusesController extends Controller
{
        public function __construct(){

            //借助中间件来添加过滤请求：使用本控制器的方程的都必须是已登录用户
            $this->middleware('auth');
        }

    public function store(Request $request){
        $this->validate($request,[
            'content'=>'required|max:140'
        ]);

        //Aust::user()获取当前登录的用户
        Auth::user()->statuses()->create([
            'content'=>$request['content']
        ]);
        session()->flash('success','发布成功');
        return redirect()->back();
    }

    public function destroy(Status $status){
        //调用StatusPolicy里面的destroy方法，判断该用户是否可以删除此$status
        $this->authorize('destroy',$status);
        $status->delete();
        session()->flash('success', '微博已被成功删除！');
        return redirect()->back();
    }
}