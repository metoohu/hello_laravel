<?php

namespace App\Policies;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;
class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    //public function __construct()
    //{
        //
    //}

    public function update(User $currentUser,User $user)
    {

        return $currentUser->id===$user->id;
    }

    public function destroy(User $currentUser, User $user)
    {
        return $currentUser->is_admin && $currentUser->id !== $user->id;
    }
    /**
     * 在开发一个功能前，需仔细考虑下此功能的授权策略。在我们的场景中，
     * 什么人不能关注用户？是的，自己不能关注自己。
     * 故新增授权策略方法取名 follow()
     */
    public function follow(User $currentUser, User $user)
    {
        return $currentUser->id !== $user->id;
    }
}
