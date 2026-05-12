<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute les champs de récurrence sur la table tasks.
     * - is_recurring    : booléen indiquant si la tâche est récurrente
     * - recurrence_type : fréquence (daily, weekly, monthly)
     * - recurrence_days : jours de la semaine pour weekly (JSON, ex: [1,3,5])
     * - recurrence_end  : date de fin de la récurrence (nullable)
     * - parent_task_id  : clé vers la tâche "parent" (pour les occurrences générées)
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->boolean('is_recurring')->default(false)->after('status');
            $table->enum('recurrence_type', ['daily', 'weekly', 'monthly'])->nullable()->after('is_recurring');
            $table->json('recurrence_days')->nullable()->after('recurrence_type'); // ex: [1,3,5] pour lun/mer/ven
            $table->date('recurrence_end')->nullable()->after('recurrence_days');
            $table->foreignId('parent_task_id')->nullable()->constrained('tasks')->onDelete('cascade')->after('recurrence_end');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['parent_task_id']);
            $table->dropColumn(['is_recurring', 'recurrence_type', 'recurrence_days', 'recurrence_end', 'parent_task_id']);
        });
    }
};
