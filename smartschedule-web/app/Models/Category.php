<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Category — Représente une catégorie de tâches.
 * 
 * Les catégories permettent d'organiser les tâches par thème
 * (ex : "Informatique", "Mathématiques", "Sport").
 * Chaque catégorie est propre à un utilisateur.
 * Si une catégorie est supprimée, les tâches liées conservent leur valeur
 * mais category_id est mis à NULL (ON DELETE SET NULL en base de données).
 */
class Category extends Model
{
    use HasFactory;

    /**
     * Champs remplissables.
     */
    protected $fillable = [
        'user_id',
        'name',  // Nom de la catégorie
    ];

    // ────────────────────────────────────────────────
    // Relations Eloquent (ORM)
    // ────────────────────────────────────────────────

    /**
     * Une catégorie appartient à un utilisateur.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Une catégorie peut contenir plusieurs tâches.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
