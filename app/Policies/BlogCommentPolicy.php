<?php

namespace App\Policies;

use App\Models\BlogComment;
use App\Models\User;

class BlogCommentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BlogComment $blogComment): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role->isAdmin() || $user->role->isModerator() || $user->role->isUser();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BlogComment $blogComment): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     * Allow a user delete it's own comment or an admin to delete any comment.
     */
    public function delete(User $user, BlogComment $blogComment): bool
    {
        return $blogComment->user_id === $user->id || $user->role->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BlogComment $blogComment): bool
    {
        return $user->role->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BlogComment $blogComment): bool
    {
        return $user->role->isAdmin();
    }
}
