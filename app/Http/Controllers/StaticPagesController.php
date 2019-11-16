<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Status;
use Auth;

class StaticPagesController extends Controller
{
    //home页面，即本项目的weibo.test的初始页面
    public function home()
    {
        $feed_items = [];
        if (Auth::check()) {
            //如果用户已登录，Auth::user()获取登录用户的信息，调用User模型的feed方法，获取已关注用户的微博
            $feed_items = Auth::user()->feed()->paginate(30);
        }

        return view('static_pages/home', compact('feed_items'));
    }

    public function help()
    {
        return view('static_pages/help');
    }

    public function about()
    {
        return view('static_pages/about');
    }
}