<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UsersController extends Controller
{
    public function __construct(){
        $this->middleware('auth',[
            'except' => ['show','create','store','index','confirmEmail']
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
        $statuses = $user->statuses()->orderBy('created_at', 'desc')->paginate(30);
        return view('users.show',compact('user','statuses'));
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
        $this->sendEmailConfirmationTo($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');

        //自动登录
       // Auth::login($user);
        //session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
       // return redirect()->route('users.show', [$user]);
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
    /**
     * 删除用户
     *  * 删除授权策略 destroy 我们已经在上面创建了，
         * 这里我们在用户控制器中使用 authorize 方法来对删除操作进行授权验证即可。
         * 在删除动作的授权中，我们规定只有当前用户为管理员，且被删除用户不是自己时，
         * 授权才能通过。
     */
    public function destroy(User $user){
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success','成功删除用户');
        return back();
    }
    /**
     * 发送注册邮箱
     */
    protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $from = 'metoohu@126.com';
        $name = 'Edwin';
        $to = $user->email;
        $subject = "感谢注册 Weibo 应用！请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
            $message->from($from, $name)->to($to)->subject($subject);
        });
    }

    /**
     * 激活账号
     */
    public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }
    /**
     * 显示用户关注人列表视图的 followings 方法
     */
    public function followings(User $user){
        $users = $user->followings()->paginate(30);
        $title = $user->name .'关注的人';
        return view('users.show_follow',compact('users','title'));
    }

    /**
     * 显示粉丝列表的 followers 方法
     */
    public function followers(User $user){
        $users = $user->followers()->paginate(30);
        $title = $user->name.'的粉丝';
        return view('users.show_follow',compact('users','title'));
    }
}
