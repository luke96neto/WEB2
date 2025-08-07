<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

class BookPolicy
{
    public function viewAny(User $user)
    {
        return true; // todos podem ver a lista
    }

    public function view(User $user, Book $book)
    {
        return in_array($user->role, ['admin', 'bibliotecario', 'cliente']);
    }

    public function create(User $user)
    {
        return in_array($user->role, ['admin', 'bibliotecario']);
    }

    public function update(User $user, Book $book)
    {
        return in_array($user->role, ['admin', 'bibliotecario']);
    }

    public function delete(User $user, Book $book)
    {
        return $user->role === 'admin';
    }
}
