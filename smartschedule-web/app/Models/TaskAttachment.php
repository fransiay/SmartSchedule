<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle TaskAttachment — Pièce jointe associée à une tâche.
 *
 * Types supportés :
 *   - image : JPEG, PNG, WebP, GIF
 *   - audio : MP3, WAV, OGG, M4A (notes vocales)
 *   - pdf   : Documents PDF
 *   - other : Tout autre fichier
 */
class TaskAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'type',
        'original_name',
        'path',
        'mime_type',
        'size',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    // ── Accesseur : URL publique via le storage ──
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }

    protected $appends = ['url'];

    // ── Relations ──

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // ── Helper : détermine le type à partir du MIME ──
    public static function typeFromMime(string $mime): string
    {
        if (str_starts_with($mime, 'image/')) return 'image';
        if (str_starts_with($mime, 'audio/')) return 'audio';
        if ($mime === 'application/pdf')       return 'pdf';
        return 'other';
    }
}
