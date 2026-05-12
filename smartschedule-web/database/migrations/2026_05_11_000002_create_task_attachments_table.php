<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table task_attachments pour stocker les pièces jointes.
     * - task_id      : clé vers la tâche
     * - type         : 'image', 'audio', 'pdf', 'other'
     * - original_name: nom original du fichier uploadé
     * - path         : chemin de stockage dans storage/
     * - mime_type    : type MIME réel du fichier
     * - size         : taille en octets
     */
    public function up(): void
    {
        Schema::create('task_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['image', 'audio', 'pdf', 'other'])->default('other');
            $table->string('original_name');
            $table->string('path');
            $table->string('mime_type');
            $table->unsignedBigInteger('size')->default(0); // en octets
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_attachments');
    }
};
