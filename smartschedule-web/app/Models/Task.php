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
        'duration_minutes',
        'priority',
        'deadline',
        'status',
        'modifier',
        // Récurrence
        'is_recurring',
        'recurrence_type',  // daily | weekly | monthly
        'recurrence_days',  // JSON : [0,1,2,...6] (0=dim, 1=lun, ...)
        'recurrence_end',   // Date de fin de répétition
        'parent_task_id',   // Clé vers la tâche parente (occurrences)
    ];

    protected $casts = [
        'deadline'        => 'datetime',
        'recurrence_end'  => 'date',
        'recurrence_days' => 'array',
        'is_recurring'    => 'boolean',
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

    /**
     * Pièces jointes de la tâche.
     */
    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }

    /**
     * Occurrences générées par la récurrence (tâches enfants).
     */
    public function children()
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    /**
     * Tâche parente (pour les occurrences).
     */
    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }
}
