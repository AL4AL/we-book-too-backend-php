<?php

namespace App\Policies;

use App\Domain\Auth\Entities\User;
use App\Domain\Chat\Entities\Conversation;

class ConversationPolicy
{
    public function view(User $user, Conversation $conversation): bool
    {
        return $conversation->created_by_user_id === $user->id || 
               $conversation->representative_user_id === $user->id;
    }

    public function message(User $user, Conversation $conversation): bool
    {
        return $this->view($user, $conversation) && $conversation->status === 'accepted';
    }
}
