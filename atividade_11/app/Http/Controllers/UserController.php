<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
        // Apenas admins podem editar usuários
        $this->middleware(function ($request, $next) {
            if (in_array($request->route()->getName(), ['users.edit', 'users.update']) &&
                Auth::user()->role !== 'admin') {
                abort(403, 'Acesso negado.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->only('name', 'email');

        if (Auth::user()->role === 'admin') {
            $data['role'] = $request->input('role');
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso.');
    }
}
