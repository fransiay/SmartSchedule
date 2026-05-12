<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Contrôleur pour les pièces jointes des tâches.
 * 
 * Gère l'upload, la liste et la suppression des fichiers attachés.
 * Les fichiers sont stockés dans storage/app/public/attachments/{user_id}/
 * et accessibles via une URL publique (lien symbolique : php artisan storage:link).
 */
class TaskAttachmentController extends Controller
{
    // Taille maximale autorisée : 20 Mo
    const MAX_SIZE_KB = 20480;

    // Types MIME autorisés
    const ALLOWED_MIMES = [
        'image/jpeg', 'image/png', 'image/webp', 'image/gif',
        'audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/mp4',
        'application/pdf',
    ];

    /**
     * Liste toutes les pièces jointes d'une tâche.
     */
    public function index(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($task->attachments()->latest()->get());
    }

    /**
     * Upload une nouvelle pièce jointe pour une tâche.
     * Accepte les champs multipart : file (required)
     */
    public function store(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'file' => [
                'required',
                'file',
                'max:' . self::MAX_SIZE_KB,
                'mimes:jpeg,jpg,png,webp,gif,mp3,wav,ogg,m4a,pdf',
            ],
        ], [
            'file.max'   => 'Le fichier ne doit pas dépasser 20 Mo.',
            'file.mimes' => 'Format non supporté. Formats acceptés : images (JPG, PNG, WebP), audio (MP3, WAV, OGG, M4A) ou PDF.',
        ]);

        $file = $request->file('file');
        $mime = $file->getMimeType();

        // Stockage dans storage/app/public/attachments/{user_id}/
        $path = $file->store("attachments/{$request->user()->id}", 'public');

        $attachment = TaskAttachment::create([
            'task_id'       => $task->id,
            'type'          => TaskAttachment::typeFromMime($mime),
            'original_name' => $file->getClientOriginalName(),
            'path'          => $path,
            'mime_type'     => $mime,
            'size'          => $file->getSize(),
        ]);

        return response()->json($attachment, 201);
    }

    /**
     * Supprime une pièce jointe et son fichier physique.
     */
    public function destroy(Request $request, Task $task, TaskAttachment $attachment)
    {
        if ($task->user_id !== $request->user()->id || $attachment->task_id !== $task->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Suppression du fichier physique du disque
        Storage::disk('public')->delete($attachment->path);
        $attachment->delete();

        return response()->json(null, 204);
    }
}
