<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function __construct(){
        $this->middleware('auth',[
            'except' => ['show','create','store']
        ]);
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }
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
    /**
     * 编辑页面
     */
    public function edit(User $user){
        $this->authorize('update', $user);
        return view('users.edit',compact('user'));
    }
    /**
     * 编辑用户资料
     */
    public function update(User $user,Request $request){
        $this->authorize('update', $user);
        $this->validate($request,[
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);
        $date = [];
        $date['name'] = $request->name;
        if($request->passward){
            $date['password'] = bcrtpt($request->password);
        }
        $user->update($date);
        session()->flash('success','个人资料更新成功！');
        return redirect()->route('users.show',$user->id);
    }
    /**
     * 用户列表
     */
    public function index(){
        $users = User::paginate(6);
        return view('users.index',compact('users'));
    }
}
