<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use Illuminate\Http\Request;

/**
 * Contrôleur CRUD des disponibilités pour l'API REST.
 * 
 * Une disponibilité représente un créneau horaire récurrent dans la semaine
 * (ex : "Lundi de 09h à 12h") pendant lequel l'utilisateur peut travailler.
 * Ces créneaux sont utilisés par l'algorithme SmartSchedule pour planifier les tâches.
 */
class AvailabilityController extends Controller
{
    /**
     * Retourne toutes les disponibilités de l'utilisateur connecté.
     */
    public function index(Request $request)
    {
        return response()->json($request->user()->availabilities);
    }

    /**
     * Crée un nouveau créneau de disponibilité.
     * 
     * L'application mobile envoie day_of_week (0=Dimanche à 6=Samedi).
     * On convertit ce numéro en abréviation textuelle (Mon, Tue, etc.)
     * pour le stockage en base de données.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'day_of_week' => 'required|integer|between:0,6', // 0=Dim, 1=Lun, ..., 6=Sam
            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'required|date_format:H:i',
            'is_active'   => 'boolean',
        ]);

        // Conversion du numéro de jour en abréviation anglaise (stockage BDD)
        $map = [0 => 'Sun', 1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat'];

        $availability = $request->user()->availabilities()->create([
            'day'        => $map[$validated['day_of_week']],
            'start_time' => $validated['start_time'],
            'end_time'   => $validated['end_time'],
        ]);
        return response()->json($availability, 201);
    }

    /**
     * Retourne les détails d'une disponibilité spécifique.
     * Retourne 403 si elle n'appartient pas à l'utilisateur.
     */
    public function show(Request $request, Availability $availability)
    {
        if ($availability->user_id !== $request->user()->id) abort(403);
        return response()->json($availability);
    }

    /**
     * Met à jour un créneau de disponibilité existant.
     * Seuls les champs envoyés sont modifiés (mise à jour partielle).
     */
    public function update(Request $request, Availability $availability)
    {
        if ($availability->user_id !== $request->user()->id) abort(403);

        $validated = $request->validate([
            'day_of_week' => 'sometimes|required|integer|between:0,6',
            'start_time'  => 'sometimes|required|date_format:H:i',
            'end_time'    => 'sometimes|required|date_format:H:i',
            'is_active'   => 'boolean',
        ]);

        // Construction du tableau de mise à jour
        $updateData = [];
        if (isset($validated['day_of_week'])) {
            // Reconversion du numéro en abréviation si le jour a changé
            $map = [0 => 'Sun', 1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat'];
            $updateData['day'] = $map[$validated['day_of_week']];
        }
        if (isset($validated['start_time'])) $updateData['start_time'] = $validated['start_time'];
        if (isset($validated['end_time']))   $updateData['end_time']   = $validated['end_time'];

        $availability->update($updateData);
        return response()->json($availability);
    }

    /**
     * Supprime un créneau de disponibilité.
     * Retourne 204 No Content en cas de succès.
     */
    public function destroy(Request $request, Availability $availability)
    {
        if ($availability->user_id !== $request->user()->id) abort(403);
        $availability->delete();
        return response()->json(null, 204);
    }
}
