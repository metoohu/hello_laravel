<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    /**
     * 在用户模型中，指明一个用户拥有多条微博。
     */
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    }

    public function followings()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function gravatar($size = '100')
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "https://cdn.v2ex.com/gravatar/$hash?s=$size";
    }
    /**
     * 生成的用户激活令牌
     * boot 方法会在用户模型类完成初始化之后进行加载，
     * 因此我们对事件的监听需要放在该方法中。
     */
    public static function boot(){
        parent::boot();
        static::creating(function($user){
            $user->activation_token = str::random(10);
        });
    }
    /**
     * 现在网站主页已经拥有微博的发布表单和当前登录用户的个人信息展示了，
     * 接下来让我们接着完善该页面，
     * 在微博发布表单下面增加一个局部视图用于展示微博列表。在开始之前，
     * 我们需要在用户模型中定义一个 feed 方法，
     * 该方法将当前用户发布过的所有微博从数据库中取出，
     * 并根据创建时间来倒序排序。在后面我们为用户增加关注人的功能之后，
     * 将使用该方法来获取当前用户关注的人发布过的所有微博动态。
     * 现在的 feed 方法定义如下：
     */
    public function feed(){
        return $this->statuses()->orderBy('created_at','desc');
    }
    /**
     * 借助这两个方法可以让我们非常简单的实现用户的「关注」和「取消关注」的相关逻辑，
     * 具体在用户模型中定义关注（follow）和取消关注（unfollow）的方法如下：
     *
     */
    public function follow($user_ids)
    {
        if ( ! is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->sync($user_ids, false);
    }

    public function unfollow($user_ids)
    {
        if ( ! is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->detach($user_ids);
    }
}
