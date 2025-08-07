<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::orderBy('name')->paginate(20);

        return view('usuarios.index', compact('usuarios'));
    }

    public function toggleAdmin(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Você não pode alterar seu próprio status de admin.');
        }

        $user->is_admin = !$user->is_admin;
        $user->save();

        return back()->with('success', 'Permissão atualizada com sucesso!');
    }
}
