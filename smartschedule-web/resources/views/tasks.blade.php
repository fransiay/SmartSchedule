<x-app-layout>
    <x-slot name="header">
        <p class="page-title">Gestion des Tâches</p>
        <p class="page-subtitle">Créez, modifiez et organisez vos tâches par priorité et deadline.</p>
    </x-slot>

    <div class="pb-16" x-data="tasksController()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="card" style="padding:28px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
                    <div>
                        <h2 style="font-size:1.1rem;font-weight:700;color:#0f0f10;margin:0 0 4px;">Mes Tâches</h2>
                        <p style="font-size:0.82rem;color:#aeaeb2;margin:0;"
                            x-text="tasks.length + ' tâche(s) au total'"></p>
                    </div>
                    <button @click="openModal()" class="btn-primary">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        Nouvelle Tâche
                    </button>
                </div>

                <div x-show="tasks.length > 0" class="overflow-hidden rounded-2xl border border-gray-100 shadow-sm bg-white">
                    <table class="data-table w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="text-left py-4 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Titre</th>
                                <th class="text-left py-4 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Catégorie</th>
                                <th class="text-left py-4 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Priorité</th>
                                <th class="text-left py-4 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Durée</th>
                                <th class="text-left py-4 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Deadline</th>
                                <th class="text-left py-4 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Statut</th>
                                <th class="text-right py-4 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <template x-for="task in tasks" :key="task.id">
                                <tr class="hover:bg-gray-50/30 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold text-gray-900" x-text="task.title"></span>
                                            <template x-if="task.is_recurring">
                                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-blue-50 text-blue-600 border border-blue-100" title="Tâche récurrente">
                                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="display:inline"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                                                </span>
                                            </template>
                                            <template x-if="task.attachments && task.attachments.length > 0">
                                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-gray-50 text-gray-500 border border-gray-100">
                                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="display:inline"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                                                    <span x-text="task.attachments.length"></span>
                                                </span>
                                            </template>
                                        </div>
                                        <template x-if="task.description">
                                            <span class="text-[11px] text-gray-400 mt-0.5 block truncate max-w-[220px]" x-text="task.description"></span>
                                        </template>
                                    </td>
                                    <td class="py-4 px-6">
                                        <template x-if="task.category">
                                            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-gray-50 border border-gray-100">
                                                <div :style="'width:6px;height:6px;border-radius:50%;background-color:' + (task.category.color || '#6366f1')"></div>
                                                <span class="text-[12px] font-medium text-gray-600" x-text="task.category.name"></span>
                                            </div>
                                        </template>
                                        <template x-if="!task.category">
                                            <span class="text-[12px] text-gray-300 italic">Aucune</span>
                                        </template>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="badge"
                                            :class="task.priority<=2?'badge-red':(task.priority==3?'badge-yellow':'badge-green')"
                                            x-text="'P'+task.priority"></span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-1.5 text-gray-500">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                            <span class="text-[13px]" x-text="task.duration_minutes+' min'"></span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="text-[13px] font-medium"
                                            :style="new Date(task.deadline)<new Date()&&task.status!='done'?'color:#e11d48;':'color:#6b7280;'"
                                            x-text="new Date(task.deadline).toLocaleDateString('fr-FR')"></span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="relative inline-block">
                                            <select x-model="task.status" @change="updateStatus(task)"
                                                class="appearance-none bg-gray-50/50 border border-gray-200 rounded-lg px-3 py-1.5 pr-8 text-[12px] font-semibold text-gray-700 cursor-pointer focus:outline-none focus:ring-2 focus:ring-black/5 transition-all">
                                                <option value="todo">À faire</option>
                                                <option value="in_progress">En cours</option>
                                                <option value="done">Terminé</option>
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                                <svg class="h-3 w-3 fill-current" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex gap-2 justify-end">
                                            <button @click="openModal(task)"
                                                class="p-2 text-gray-400 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-all" title="Éditer">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                            </button>
                                            <button @click="deleteTask(task.id)"
                                                class="p-2 text-rose-300 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all" title="Supprimer">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div x-show="isModalOpen" style="display:none;" x-cloak>
            <div style="position:fixed;inset:0;z-index:999;overflow-y:auto;padding:20px;display:flex;align-items:flex-start;justify-content:center;">
                <div style="position:fixed;inset:0;background:rgba(0,0,0,0.45);backdrop-filter:blur(4px);" @click="isModalOpen=false"></div>
                <div class="modal-box" style="position:relative;z-index:10;width:100%;max-width:580px;padding:32px;margin:auto;">
                    <h3 style="font-size:1.25rem;font-weight:700;color:#0f0f10;margin:0 0 24px;" x-text="currentTask.id?'Modifier la Tâche':'Nouvelle Tâche'"></h3>

                    <div style="display:flex;flex-direction:column;gap:16px;">
                        <div>
                            <label class="form-label">Titre *</label>
                            <input type="text" x-model="currentTask.title" class="form-input" placeholder="Ex: Préparer la présentation">
                        </div>
                        <div>
                            <label class="form-label">Description</label>
                            <textarea x-model="currentTask.description" class="form-input" rows="2" placeholder="Description optionnelle..." style="resize:vertical;"></textarea>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                            <div>
                                <label class="form-label">Durée (min) *</label>
                                <input type="number" x-model="currentTask.duration_minutes" class="form-input" min="1">
                            </div>
                            <div>
                                <label class="form-label">Priorité (1–5) *</label>
                                <input type="number" x-model="currentTask.priority" class="form-input" min="1" max="5">
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                            <div>
                                <label class="form-label">Catégorie</label>
                                <select x-model="currentTask.category_id" class="form-input">
                                    <option value="">Sans catégorie</option>
                                    <template x-for="cat in categories" :key="cat.id">
                                        <option :value="cat.id" x-text="cat.name"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Statut</label>
                                <select x-model="currentTask.status" class="form-input">
                                    <option value="todo">À faire</option>
                                    <option value="in_progress">En cours</option>
                                    <option value="done">Terminé</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Deadline *</label>
                            <input type="date" x-model="currentTask.deadline" class="form-input">
                        </div>

                        <!-- ── Récurrence ── -->
                        <div style="border:1px solid #e5e5e7;border-radius:12px;padding:16px;">
                            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:0.875rem;font-weight:600;color:#0f0f10;margin-bottom:0;">
                                <input type="checkbox" x-model="currentTask.is_recurring" style="width:16px;height:16px;accent-color:#0f0f10;">
                                Tâche récurrente
                            </label>
                            <div x-show="currentTask.is_recurring" class="mt-3" style="display:flex;flex-direction:column;gap:12px;">
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                                    <div>
                                        <label class="form-label">Fréquence</label>
                                        <select x-model="currentTask.recurrence_type" class="form-input">
                                            <option value="daily">Quotidien</option>
                                            <option value="weekly">Hebdomadaire</option>
                                            <option value="monthly">Mensuel</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="form-label">Date de fin</label>
                                        <input type="date" x-model="currentTask.recurrence_end" class="form-input">
                                    </div>
                                </div>
                                <div x-show="currentTask.recurrence_type === 'weekly'">
                                    <label class="form-label">Jours de répétition</label>
                                    <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:6px;">
                                        <template x-for="day in [{l:'Lun',v:1},{l:'Mar',v:2},{l:'Mer',v:3},{l:'Jeu',v:4},{l:'Ven',v:5},{l:'Sam',v:6},{l:'Dim',v:0}]" :key="day.v">
                                            <button type="button"
                                                @click="toggleDay(day.v)"
                                                :style="isDaySelected(day.v) ? 'background:#0f0f10;color:#fff;' : 'background:#f3f3f4;color:#3a3a3c;'"
                                                style="width:44px;height:36px;border-radius:8px;border:none;font-size:0.75rem;font-weight:700;cursor:pointer;transition:all 0.15s;" x-text="day.l">
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ── Pièces jointes ── -->
                        <div x-show="currentTask.id" style="border:1px solid #e5e5e7;border-radius:12px;padding:16px;">
                            <p style="font-size:0.875rem;font-weight:600;color:#0f0f10;margin:0 0 12px;">Pièces jointes</p>
                            <div x-show="currentTask.attachments && currentTask.attachments.length > 0" class="mb-3 space-y-2">
                                <template x-for="att in (currentTask.attachments || [])" :key="att.id">
                                    <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:#f7f7f8;border-radius:8px;">
                                        <div style="display:flex;align-items:center;gap:8px;">
                                            <span x-text="attIcon(att.type)" style="font-size:1rem;"></span>
                                            <span style="font-size:0.8rem;color:#3a3a3c;" x-text="att.original_name"></span>
                                            <span style="font-size:0.72rem;color:#aeaeb2;" x-text="formatSize(att.size)"></span>
                                        </div>
                                        <div style="display:flex;gap:6px;">
                                            <a :href="att.url" target="_blank" style="padding:4px 8px;background:#fff;border:1px solid #e5e5e7;border-radius:6px;font-size:0.75rem;font-weight:600;color:#0f0f10;text-decoration:none;">Voir</a>
                                            <button @click="deleteAttachment(att)" style="padding:4px 8px;background:#fff1f2;border:1px solid #fecdd3;border-radius:6px;font-size:0.75rem;font-weight:600;color:#e11d48;cursor:pointer;">✕</button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <label style="display:flex;align-items:center;justify-content:center;gap:8px;padding:12px;border:2px dashed #e5e5e7;border-radius:8px;cursor:pointer;font-size:0.82rem;color:#6c6c70;transition:all 0.2s;" @dragover.prevent @drop.prevent="uploadFile($event.dataTransfer.files[0])" :style="uploading?'opacity:0.6;pointer-events:none;':''">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                                <span x-text="uploading ? 'Upload en cours...' : 'Glisser ou cliquer pour ajouter (image, audio, PDF — max 20 Mo)'"></span>
                                <input type="file" class="hidden" accept="image/*,audio/*,.pdf" @change="uploadFile($event.target.files[0])" :disabled="uploading">
                            </label>
                        </div>
                        <div x-show="!currentTask.id" style="padding:10px 14px;background:#f7f7f8;border-radius:8px;font-size:0.8rem;color:#6c6c70;">
                            💡 Sauvegardez d'abord la tâche pour pouvoir ajouter des pièces jointes.
                        </div>
                    </div>

                    <p x-show="errorMsg" x-text="errorMsg" style="color:#e11d48;font-size:0.82rem;margin:12px 0 0;background:#fff1f2;border:1px solid #fecdd3;padding:8px 12px;border-radius:8px;"></p>

                    <div style="display:flex;gap:12px;margin-top:24px;justify-content:flex-end;">
                        <button @click="isModalOpen=false" class="btn-secondary">Annuler</button>
                        <button @click="saveTask" class="btn-primary">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                            Sauvegarder
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const apiFetch = (url, opts = {}) => fetch(url, {
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', ...(opts.headers || {}) },
            ...opts
        });

        function tasksController() {
            return {
                tasks: [], categories: [], isModalOpen: false, currentTask: {}, errorMsg: '', uploading: false,
                init() { this.fetchTasks(); this.fetchCategories(); },
                emptyTask() {
                    return { id: null, title: '', description: '', duration_minutes: 60, priority: 3,
                        deadline: new Date().toISOString().split('T')[0], status: 'todo', category_id: '',
                        is_recurring: false, recurrence_type: 'weekly', recurrence_days: [], recurrence_end: '',
                        attachments: [] };
                },
                fetchCategories() { apiFetch('/web-api/categories').then(r => r.json()).then(d => { this.categories = Array.isArray(d) ? d : []; }); },
                fetchTasks()     { apiFetch('/web-api/tasks').then(r => r.json()).then(d => { this.tasks = Array.isArray(d) ? d : []; }); },
                openModal(task = null) {
                    this.errorMsg = '';
                    if (task) {
                        this.currentTask = JSON.parse(JSON.stringify(task));
                        if (this.currentTask.deadline) this.currentTask.deadline = this.currentTask.deadline.split('T')[0].split(' ')[0];
                        if (!this.currentTask.recurrence_days) this.currentTask.recurrence_days = [];
                        if (!this.currentTask.attachments)     this.currentTask.attachments = [];
                    } else {
                        this.currentTask = this.emptyTask();
                    }
                    this.isModalOpen = true;
                },
                toggleDay(v) {
                    const d = this.currentTask.recurrence_days || [];
                    const i = d.indexOf(v);
                    if (i === -1) d.push(v); else d.splice(i, 1);
                    this.currentTask.recurrence_days = [...d];
                },
                isDaySelected(v) { return (this.currentTask.recurrence_days || []).includes(v); },
                saveTask() {
                    this.errorMsg = '';
                    if (!this.currentTask.title) { this.errorMsg = 'Le titre est obligatoire.'; return; }
                    const isNew = !this.currentTask.id;
                    const url = isNew ? '/web-api/tasks' : `/web-api/tasks/${this.currentTask.id}`;
                    const payload = {
                        title: this.currentTask.title,
                        description: this.currentTask.description || null,
                        duration_minutes: parseInt(this.currentTask.duration_minutes),
                        priority: parseInt(this.currentTask.priority),
                        deadline: this.currentTask.deadline + ' 00:00:00',
                        category_id: this.currentTask.category_id || null,
                        status: this.currentTask.status,
                        is_recurring: !!this.currentTask.is_recurring,
                        recurrence_type: this.currentTask.is_recurring ? this.currentTask.recurrence_type : null,
                        recurrence_days: this.currentTask.is_recurring && this.currentTask.recurrence_type === 'weekly' ? this.currentTask.recurrence_days : null,
                        recurrence_end: this.currentTask.recurrence_end || null,
                    };
                    apiFetch(url, { method: isNew ? 'POST' : 'PUT', body: JSON.stringify(payload) })
                        .then(r => r.json())
                        .then(data => {
                            if (data.errors || data.message === 'The given data was invalid.') {
                                this.errorMsg = Object.values(data.errors || {}).flat().join(' ');
                            } else {
                                this.isModalOpen = false;
                                this.fetchTasks();
                            }
                        })
                        .catch(() => { this.errorMsg = 'Erreur réseau.'; });
                },
                updateStatus(task) { apiFetch(`/web-api/tasks/${task.id}`, { method: 'PUT', body: JSON.stringify({ status: task.status }) }); },
                deleteTask(id) {
                    if (confirm('Supprimer cette tâche définitivement ?')) {
                        apiFetch(`/web-api/tasks/${id}`, { method: 'DELETE' }).then(() => this.fetchTasks());
                    }
                },
                uploadFile(file) {
                    if (!file || !this.currentTask.id) return;
                    this.uploading = true;
                    const fd = new FormData();
                    fd.append('file', file);
                    fetch(`/web-api/tasks/${this.currentTask.id}/attachments`, {
                        method: 'POST', credentials: 'same-origin',
                        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                        body: fd
                    })
                    .then(r => r.json())
                    .then(att => {
                        if (att.id) {
                            if (!this.currentTask.attachments) this.currentTask.attachments = [];
                            this.currentTask.attachments.push(att);
                            const idx = this.tasks.findIndex(t => t.id === this.currentTask.id);
                            if (idx !== -1) this.tasks[idx].attachments = [...this.currentTask.attachments];
                        } else { alert(att.message || 'Erreur upload'); }
                    })
                    .catch(() => alert('Erreur réseau.'))
                    .finally(() => { this.uploading = false; });
                },
                deleteAttachment(att) {
                    if (!confirm('Supprimer cette pièce jointe ?')) return;
                    apiFetch(`/web-api/tasks/${this.currentTask.id}/attachments/${att.id}`, { method: 'DELETE' })
                        .then(() => {
                            this.currentTask.attachments = this.currentTask.attachments.filter(a => a.id !== att.id);
                        });
                },
                attIcon(type) { return {image:'🖼️', audio:'🎙️', pdf:'📄', other:'📎'}[type] || '📎'; },
                formatSize(b) { return b < 1024*1024 ? Math.round(b/1024)+'Ko' : (b/1024/1024).toFixed(1)+'Mo'; },
            }
        }
    </script>
</x-app-layout>