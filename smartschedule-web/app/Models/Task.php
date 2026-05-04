<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Task — Représente une tâche à planifier.
 * 
 * Une tâche appartient à un utilisateur et peut être assignée à une catégorie.
 * Elle génère au maximum un créneau dans le planning (relation hasOne Schedule).
 * 
 * Priorités : 1 = Urgent (rouge), 2 = Élevé, 3 = Moyen, 4 = Normal, 5 = Bas (vert)
 * Statuts   : todo → in_progress → done
 */
class Task extends Model
{
    use HasFactory;

    /**
     * Champs remplissables (protection contre le mass assignment).
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'duration_minutes', // Durée totale de la tâche en minutes
        'priority',         // Entier de 1 (Urgent) à 5 (Bas)
        'deadline',         // Date limite de réalisation
        'status',
        'modifier',   // todo / in_progress / done
    ];

    /**
     * Conversions automatiques de types.
     */
    protected $casts = [
        'deadline' => 'datetime', // Convertit automatiquement en objet Carbon
    ];

    // ────────────────────────────────────────────────
    // Relations Eloquent (ORM)
    // ────────────────────────────────────────────────

    /**
     * Une tâche appartient à un utilisateur.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Une tâche appartient à une catégorie (optionnel).
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Une tâche génère au maximum un créneau dans le planning.
     */
    public function schedule()
    {
        return $this->hasOne(Schedule::class);
    }
}
