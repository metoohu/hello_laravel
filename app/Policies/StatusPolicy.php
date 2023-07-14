<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Status;

class StatusPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * 我们需要在该授权策略中引入用户模型和微博模型，
     * 并添加 destroy 方法定义微博删除动作相关的授权。
     * 如果当前用户的 id 与要删除的微博作者 id 相同时，验证才能通过。
     */
    public function destroy(User $user, Status $status)
    {
        return $user->id === $status->user_id;
    }
    /**
     * 关注
     * 当 id 为 1 的用户去关注 id 为 2 和 id 为 3 的用户时，可使用 attach 方法来进行关注，如下所示：
     * $user = App\Models\User::find(1)
     * $user->followings()->attach([2, 3])
     * 可以看到 id 2 和 3 已被成功添加到关联 id 数组中，但 attach 方法有个问题，在我们对同一个 id 进行添加时，则会出现 id 重复的情况。
     * 为了解决这种问题，我们可以使用 sync 方法。sync 方法会接收两个参数，第一个参数为要进行添加的 id，
     * 第二个参数则指明是否要移除其它不包含在关联的 id 数组中的 id，true 表示移除，false 表示不移除，
     * 默认值为 true。由于我们在关注一个新用户的时候，仍然要保持之前已关注用户的关注关系，
     * 因此不能对其进行移除，所以在这里我们选用 false
     *
     */
    public function follow($userIds){
        if(! is_array($userIds)){
            $userIds = compact('user_ids');
        }
        $this->followings()->sync($userIds,false);
    }
    /**
     * 取消关注
     * detach 来对用户进行取消关注的操作，取消关注 2 号和 3 号用户
     * $user->followings()->detach([2,3])
     */
    public function unfollow($userIds){
        if(! is_array($userIds)){
            $userIds = compact('user_ids');
        }
        $this->followings()->detach($userIds);
    }
    /**
     * 我们还需要一个方法用于判断当前登录的用户 A 是否关注了用户
     * B，代码实现逻辑很简单，我们只需判断用户 B 是否包含在用户 A 的关注人列表上即可。
     * 这里我们将用到 contains 方法来做判断。
     */
    public function isFollowing($userId){
        return $this->followings->contains($userId);
    }
}
