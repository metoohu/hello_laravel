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
}
