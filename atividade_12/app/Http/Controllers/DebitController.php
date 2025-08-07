<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class DebitController extends Controller
{
    public function index()
    {
        $users = User::where('debit', '>', 0)->get();
        return view('debits.index', compact('users'));
    }

    public function pay(User $user)
    {
        $user->debit = 0;
        $user->save();

        return redirect()->route('debitos.index')->with('success', "Débito do usuário {$user->name} zerado com sucesso.");
    }
}
