<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

/**
 * Contrôleur d'authentification pour l'interface web (sessions Laravel).
 * 
 * Contrairement au AuthController de l'API (qui utilise des tokens Sanctum),
 * ce contrôleur utilise le système de sessions Laravel standard pour
 * l'authentification dans le navigateur.
 */
class WebAuthController extends Controller
{
    /**
     * Affiche la page de connexion.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Traite le formulaire de connexion.
     * 
     * Auth::attempt() vérifie automatiquement le mot de passe haché
     * et crée une session si les identifiants sont corrects.
     */
    public function login(Request $request)
    {
        // Validation des champs du formulaire
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Tentative de connexion (compare le mot de passe avec le hash en BDD)
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Régénération de l'ID de session pour éviter les attaques de fixation de session
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        // Identifiants incorrects : retour au formulaire avec message d'erreur
        return back()->withErrors([
            'email' => 'Les identifiants ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    }

    /**
     * Affiche la page d'inscription.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Traite le formulaire d'inscription.
     * 
     * Le mot de passe est haché avec bcrypt via Hash::make()
     * avant d'être stocké en base de données. Il n'est jamais stocké en clair.
     */
    public function register(Request $request)
    {
        // Validation stricte : email unique, mot de passe confirmé
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Création de l'utilisateur avec mot de passe haché (bcrypt)
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Connexion automatique après inscription
        Auth::login($user);

        return redirect(route('dashboard'));
    }

    /**
     * Déconnexion de l'utilisateur.
     * 
     * Invalide la session et régénère le token CSRF pour la sécurité.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidation et régénération de la session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
