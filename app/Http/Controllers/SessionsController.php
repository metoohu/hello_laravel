<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionsController extends Controller
{
    //登录页面
    public function create()
    {
        return view('sessions.create');
    }

    public function store(Request $request){
        $credentials = $this->validate($request,[
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);

        if(Auth::attempt($credentials,$request->has('remember'))){
            session()->flash('success','欢迎回来！');
            return redirect()->route('users.show',[Auth::user()]);
            //登录成功
        }else{
            //登录失败
            session()->flash('danger','很抱歉，你的邮箱和密码不匹配！');
            return redirect()->back()->withInput();
        }
    }
    /**
     * 退出登录
     */
    public function destroy(){
        Auth::logout();
        session()->flash('success','您已经成功退出！');
        return redirect('login');
    }
}
