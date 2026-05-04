<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Availability — Représente un créneau de disponibilité récurrent.
 * 
 * Chaque disponibilité définit un jour de la semaine et une plage horaire
 * pendant laquelle l'utilisateur peut travailler.
 * Ces données sont lues par l'algorithme SmartScheduleService pour générer le planning.
 * 
 * Le jour est stocké en abréviation anglaise (Mon, Tue, Wed...) en base de données,
 * mais converti en numéro (0-6) via l'attribut calculé 'day_of_week'.
 */
class Availability extends Model
{
    use HasFactory;

    /**
     * Champs remplissables.
     */
    protected $fillable = [
        'user_id',
        'day',        // Abréviation anglaise : 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'
        'start_time', // Heure de début (format H:i, ex: "09:00")
        'end_time',   // Heure de fin (format H:i, ex: "17:00")
    ];

    /**
     * Attributs calculés ajoutés automatiquement aux réponses JSON.
     * 'day_of_week' et 'is_active' sont générés à la volée.
     */
    protected $appends = ['day_of_week', 'is_active'];

    // ────────────────────────────────────────────────
    // Relations Eloquent (ORM)
    // ────────────────────────────────────────────────

    /**
     * Une disponibilité appartient à un utilisateur.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ────────────────────────────────────────────────
    // Attributs calculés (Accessors)
    // ────────────────────────────────────────────────

    /**
     * Convertit l'abréviation du jour ('Mon', 'Tue'...) en numéro (0-6).
     * Utilisé par l'algorithme et l'app mobile pour identifier le jour de la semaine.
     * Convention : 0 = Dimanche, 1 = Lundi, ..., 6 = Samedi.
     */
    public function getDayOfWeekAttribute()
    {
        $map = ['Sun' => 0, 'Mon' => 1, 'Tue' => 2, 'Wed' => 3, 'Thu' => 4, 'Fri' => 5, 'Sat' => 6];
        return $map[$this->day] ?? 0;
    }

    /**
     * Indique si ce créneau est actif.
     * Par défaut toujours true (tous les créneaux sont actifs).
     */
    public function getIsActiveAttribute()
    {
        return true;
    }
}
