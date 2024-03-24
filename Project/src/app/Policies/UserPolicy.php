<?php

namespace App\Policies;

use App\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class UserPolicy {
    use HandlesAuthorization;

    public function show(User $user, User $user2) {
      // Only an authenticated user can see it
      return ($user->id == $user2->id) || $user->is_admin;
    }

    public function showAdmin(User $user) {
      // Only an authenticated user can see it
      return $user->is_admin === True;
    }

    public function edit(User $user, User $user2) {
      return $user->id == $user2->id;
    }

    public function editAdmin(User $user) {
      return $user->is_admin === True;
    }
}