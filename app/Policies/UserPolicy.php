<?php

namespace App\Policies;

use App\User;
use App\Model\StartService;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function create_users(User $user){
         return $user->admin == User::ADMIN;
    }
}
