<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Carbon\Carbon;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'genero' => ['required', 'string', 'max:50'],
            'cpf' => ['required', 'string', 'size:14', 'unique:users'],
            'tipo_profissional' => ['required', 'in:psicologo,psiquiatra,psicanalista'],
            'registro_profissional' => [
                'nullable',
                'string',
                'max:50',
                function ($attribute, $value, $fail) use ($request) {
                    if (in_array($request->tipo_profissional, ['psicologo', 'psiquiatra']) && empty($value)) {
                        $fail('O campo Registro Profissional é obrigatório para Psicólogos(as) e Psiquiatras.');
                    }
                },
            ],
            'data_nascimento' => ['required', 'date', 'before:-18 years'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'genero' => $request->genero,
            'cpf' => $request->cpf,
            'tipo_profissional' => $request->tipo_profissional,
            'registro_profissional' => $request->registro_profissional,
            'data_nascimento' => $request->data_nascimento,
            'password' => Hash::make($request->password),
            'trial_ends_at' => Carbon::now()->addDays(10),
        ]);

        $user->sendEmailVerificationNotification();

        Auth::login($user);
    
        return redirect()->route('verification.notice');
    }
}
