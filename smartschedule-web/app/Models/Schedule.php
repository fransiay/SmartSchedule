<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Schedule — Représente un créneau du planning généré.
 * 
 * Un Schedule est créé par l'algorithme SmartScheduleService.
 * Il associe une tâche à un utilisateur pour une plage horaire précise.
 * Le planning peut être entièrement recréé via le bouton "Générer".
 */
class Schedule extends Model
{
    use HasFactory;

    /**
     * Champs remplissables.
     */
    protected $fillable = [
        'task_id',    // Référence vers la tâche planifiée
        'user_id',    // Référence vers l'utilisateur propriétaire
        'start_time', // Heure de début du créneau
        'end_time',   // Heure de fin du créneau
    ];

    /**
     * Conversions automatiques de types.
     * Les dates sont automatiquement converties en objets Carbon.
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];

    // ────────────────────────────────────────────────
    // Relations Eloquent (ORM)
    // ────────────────────────────────────────────────

    /**
     * Un créneau appartient à une tâche.
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Un créneau appartient à un utilisateur.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
