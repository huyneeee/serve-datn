<?php

namespace App\Policies;

use App\Models\CommentNews;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentNewsPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->checkPermissionAccess('list_comment_new');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CommentNews  $commentNews
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user)
    {
        return $user->checkPermissionAccess('show_comment_new');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CommentNews  $commentNews
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user)
    {
        return $user->checkPermissionAccess('update_comment_new');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CommentNews  $commentNews
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function deleteChecked(User $user)
    {
        return $user->checkPermissionAccess('deleteChecked_comment_new');
    }
    public function delete(User $user)
    {
        return $user->checkPermissionAccess('delete_comment_new');
    }
    public function viewDelete(User $user)
    {
        return $user->checkPermissionAccess('viewDelete_comment_new');
    }
    public function restoreAll(User $user)
    {
        return $user->checkPermissionAccess('restoreAll_comment_new');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CommentNews  $commentNews
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user)
    {
        return $user->checkPermissionAccess('restore_comment_new');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CommentNews  $commentNews
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user)
    {
        return $user->checkPermissionAccess('forceDelete_comment_new');
    }
}
