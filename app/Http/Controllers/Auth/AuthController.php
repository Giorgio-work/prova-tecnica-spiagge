<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegistrationRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

class AuthController extends Controller
{

    public function register(RegistrationRequest $request): RedirectResponse
    {
        User::create($request->except(['confirmPassword']));

        return redirect()->route('view.login')
            ->with('title', 'Registrazione avvenuta con successo! Prova ad effettuare il login.')
            ->with('icon', 'success');
    }

    /**
     * @throws Exception
     */
    public function login(LoginRequest $request): Response|RedirectResponse
    {
        $user = User::whereEmail($request->email)->first();

        if ($user) {
            Auth::login($user);
            return redirect()->route('tasks.index')
                ->with('title', 'Benvenuto ' . $user->first_name)
                ->with('icon', 'success');
        }

        return redirect()->back()
            ->with('title', 'Le credenziali inserite non sono corrette, prova di nuovo.')
            ->with('icon', 'error');
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        return redirect()->route('view.login')
            ->with('title', 'Logout eseguito.')
            ->with('icon', 'success');
    }
}
