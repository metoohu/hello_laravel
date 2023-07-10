<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    /**
     * 用户注册页面
     */
    public function create(){
        return view('users.create');
    }
    /**
     * 用户详情页面
     */
    public function show(User $user){
        return view('users.show',compact('user'));
    }
    /**
     * 用户注册提交数据
     */
    public function store(Request $request){
        //用户注册字段验证
        $this->validate($request,[
            'name'=>'required|unique:users|max:50',
            'email'=>'required|email|unique:users|max:255',
            'password'=>'required|confirmed|min:6'
        ]);
        /**
         * 添加用户信息到用户表
         */
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        //自动登录
        Auth::login($user);
        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('users.show', [$user]);
    }
}
