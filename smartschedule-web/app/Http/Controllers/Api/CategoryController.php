<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

/**
 * Contrôleur CRUD des catégories pour l'API REST.
 * 
 * Les catégories permettent d'organiser les tâches par thème
 * (ex : "Informatique", "Mathématiques", "Personnel").
 * Chaque catégorie est propre à un utilisateur.
 */
class CategoryController extends Controller
{
    /**
     * Retourne toutes les catégories de l'utilisateur connecté.
     */
    public function index(Request $request)
    {
        return response()->json($request->user()->categories);
    }

    /**
     * Crée une nouvelle catégorie.
     * Le champ 'color' est optionnel (code hexadécimal, ex : #FF5733).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'color' => 'nullable|string|max:7', // Format couleur hex (#RRGGBB)
        ]);

        $category = $request->user()->categories()->create($validated);
        return response()->json($category, 201);
    }

    /**
     * Retourne les détails d'une catégorie.
     * Retourne 403 si elle n'appartient pas à l'utilisateur.
     */
    public function show(Request $request, Category $category)
    {
        if ($category->user_id !== $request->user()->id) abort(403);
        return response()->json($category);
    }

    /**
     * Met à jour une catégorie existante.
     */
    public function update(Request $request, Category $category)
    {
        if ($category->user_id !== $request->user()->id) abort(403);

        $validated = $request->validate([
            'name'  => 'sometimes|required|string|max:255',
            'color' => 'nullable|string|max:7',
        ]);
        
        $category->update($validated);
        return response()->json($category);
    }

    /**
     * Supprime une catégorie.
     * Les tâches liées voient leur category_id mis à NULL (ON DELETE SET NULL en BDD).
     */
    public function destroy(Request $request, Category $category)
    {
        if ($category->user_id !== $request->user()->id) abort(403);
        $category->delete();
        return response()->json(null, 204);
    }
}
