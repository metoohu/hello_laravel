<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionsController extends Controller
{
    public function __Construct(){
        $this->middleware('guest',[
            'only' => ['create']
        ]);
    }
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


            if(Auth::user()->activated) {
                session()->flash('success', '欢迎回来！');
                $fallback = route('users.show', Auth::user());
                return redirect()->intended($fallback);
            } else {
                Auth::logout();
                session()->flash('warning', '你的账号未激活，请检查邮箱中的注册邮件进行激活。');
                return redirect('/');
            }
            //当一个未登录的用户尝试访问自己的资料编辑页面时，
            //将会自动跳转到登录页面，这时候如果用户再进行登录，
            //则会重定向到其个人中心页面上，这种方式的用户体验并不好。
            //更好的做法是，将用户重定向到他之前尝试访问的页面，
            //即自己的个人编辑页面。redirect() 实例提供了一个 intended 方法，
            //该方法可将页面重定向到上一次请求尝试访问的页面上，
            //并接收一个默认跳转地址参数，当上一次请求记录为空时，
            //跳转到默认地址上。
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
