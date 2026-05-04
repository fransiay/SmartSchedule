<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

/**
 * Contrôleur d'authentification pour l'API REST (application mobile Android).
 * 
 * Gère l'inscription, la connexion, la déconnexion et la gestion du profil.
 * Utilise Laravel Sanctum pour générer des tokens Bearer.
 */
class AuthController extends Controller
{
    /**
     * Inscription d'un nouvel utilisateur.
     * 
     * Valide les données, crée l'utilisateur avec mot de passe haché,
     * puis retourne un token d'accès Bearer.
     */
    public function register(Request $request)
    {
        // Validation des champs requis
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // Création de l'utilisateur (mot de passe haché avec bcrypt)
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Génération du token Sanctum pour l'authentification mobile
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'         => $user,
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ], 201);
    }

    /**
     * Connexion d'un utilisateur existant.
     * 
     * Vérifie les identifiants avec Hash::check() et retourne un token si valides.
     * Retourne 401 si les identifiants sont incorrects.
     */
    public function login(Request $request)
    {
        // Validation de base
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Recherche de l'utilisateur par email
        $user = User::where('email', $request->email)->first();

        // Vérification du mot de passe haché
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Génération d'un nouveau token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'         => $user,
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ]);
    }

    /**
     * Déconnexion : suppression du token actuel côté serveur.
     * 
     * Cela invalide le token Bearer utilisé par l'application mobile.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Retourne les informations du profil de l'utilisateur connecté.
     */
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Mise à jour du profil utilisateur (nom et durée de pause).
     * 
     * Le champ 'break_duration' définit la pause entre chaque tâche planifiée (en minutes).
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'           => 'sometimes|string|max:255',
            'break_duration' => 'sometimes|integer|min:0|max:120',
        ]);

        $user->update($data);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user'    => $user
        ]);
    }
}
