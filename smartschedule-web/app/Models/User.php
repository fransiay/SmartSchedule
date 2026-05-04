<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Modèle User — Représente un utilisateur de l'application.
 * 
 * Hérite de Authenticatable pour la gestion de l'authentification.
 * Utilise HasApiTokens (Sanctum) pour les tokens Bearer de l'API mobile.
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Champs remplissables via le mass assignment (protection contre les injections).
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'preferred_hours', // Heures de travail préférées par jour
        'break_duration',  // Durée de pause entre tâches (en minutes)
    ];

    /**
     * Champs masqués lors de la sérialisation JSON (jamais exposés dans les réponses API).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Conversions automatiques de types (casts).
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed', // Hash automatique à l'affectation
    ];

    // ────────────────────────────────────────────────
    // Relations Eloquent (ORM)
    // ────────────────────────────────────────────────

    /**
     * Un utilisateur possède plusieurs catégories.
     */
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    /**
     * Un utilisateur possède plusieurs tâches.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Un utilisateur possède plusieurs créneaux de disponibilité.
     */
    public function availabilities()
    {
        return $this->hasMany(Availability::class);
    }

    /**
     * Un utilisateur possède plusieurs créneaux dans son planning généré.
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
