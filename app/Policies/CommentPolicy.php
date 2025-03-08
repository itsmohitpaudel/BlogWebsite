<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Comment $comment): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Comment $comment): bool
    {
        //Authors can edit their own comments, Admins can edit any comment
        return $user->id === $comment->user_id || $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Comment $comment): bool
    {
        // Users can only delete their own comments
        // Post owner can delete comments on their post
        //Admins can delete any comment
        // Post owner can delete comments on their post
        $isPostOwner = $comment->commentable_type === Post::class && $comment->commentable->user_id === $user->id;

        // Users can delete their own comments
        $isCommentOwner = $user->id === $comment->user_id;

        // Admins can delete any comment
        $isAdmin = $user->role === 'admin';

        return $isPostOwner || $isCommentOwner || $isAdmin;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Comment $comment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Comment $comment): bool
    {
        return false;
    }
}
