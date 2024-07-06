<?php

namespace App\Policies;

use App\Models\Form;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FormPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Form $form)
    {
        return $user->id === $form->creator_id;
    }

    // Add other policy methods as needed
    public function create(User $user)
    {
        return true; // Assuming all authenticated users can create forms
    }

    public function update(User $user, Form $form)
    {
        return $user->id === $form->creator_id;
    }

    public function delete(User $user, Form $form)
    {
        return $user->id === $form->creator_id;
    }
}
