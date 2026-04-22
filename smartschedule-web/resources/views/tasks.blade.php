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
                        <p style="font-size:0.82rem;color:#aeaeb2;margin:0;" x-text="tasks.length + ' tâche(s) au total'"></p>
                    </div>
                    <button @click="openModal()" class="btn-primary">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Nouvelle Tâche
                    </button>
                </div>

                <div x-show="tasks.length === 0" style="text-align:center;padding:50px 0;color:#aeaeb2;">
                    <svg style="margin:0 auto 12px;opacity:0.4;" width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><polyline points="3,6 4,7 6,5"/><polyline points="3,12 4,13 6,11"/><polyline points="3,18 4,19 6,17"/></svg>
                    <p>Aucune tâche. Cliquez sur "Nouvelle Tâche" pour commencer.</p>
                </div>

                <div x-show="tasks.length > 0" style="overflow-x:auto;border-radius:12px;border:1px solid #e5e5e7;">
                    <table class="data-table" style="width:100%;border-collapse:collapse;">
                        <thead>
                            <tr>
                                <th style="text-align:left;">Titre</th>
                                <th style="text-align:left;">Catégorie</th>
                                <th style="text-align:left;">Priorité</th>
                                <th style="text-align:left;">Durée</th>
                                <th style="text-align:left;">Deadline</th>
                                <th style="text-align:left;">Statut</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="task in tasks" :key="task.id">
                                <tr>
                                    <td><span style="font-weight:600;color:#0f0f10;" x-text="task.title"></span></td>
                                    <td>
                                        <template x-if="task.category">
                                            <div style="display:flex;align-items:center;gap:8px;">
                                                <div :style="'width:8px;height:8px;border-radius:50%;background-color:' + (task.category.color || '#6366f1')"></div>
                                                <span style="font-size:0.82rem;color:#3a3a3c;" x-text="task.category.name"></span>
                                            </div>
                                        </template>
                                        <template x-if="!task.category">
                                            <span style="font-size:0.82rem;color:#d1d1d6;font-style:italic;">Aucune</span>
                                        </template>
                                    </td>
                                    <td>
                                        <span class="badge"
                                            :class="task.priority>=4?'badge-red':(task.priority==3?'badge-yellow':'badge-green')"
                                            x-text="'P'+task.priority"></span>
                                    </td>
                                    <td><span style="color:#6c6c70;" x-text="task.duration_minutes+' min'"></span></td>
                                    <td>
                                        <span :style="new Date(task.deadline)<new Date()&&task.status!='done'?'color:#e11d48;font-weight:600;':'color:#6c6c70;'"
                                              x-text="new Date(task.deadline).toLocaleDateString('fr-FR')"></span>
                                    </td>
                                    <td>
                                        <select x-model="task.status" @change="updateStatus(task)"
                                                style="background:#f7f7f8;border:1px solid #d1d1d6;border-radius:8px;padding:5px 10px;color:#0f0f10;font-size:0.82rem;cursor:pointer;outline:none;">
                                            <option value="todo">À faire</option>
                                            <option value="in_progress">En cours</option>
                                            <option value="done">Terminé</option>
                                        </select>
                                    </td>
                                    <td style="text-align:right;">
                                        <div style="display:flex;gap:8px;justify-content:flex-end;">
                                            <button @click="openModal(task)"
                                                    style="background:#f3f3f4;border:1px solid #e5e5e7;color:#0f0f10;padding:5px 12px;border-radius:7px;font-size:0.8rem;font-weight:600;cursor:pointer;">Éditer</button>
                                            <button @click="deleteTask(task.id)"
                                                    style="background:#fff1f2;border:1px solid #fecdd3;color:#e11d48;padding:5px 12px;border-radius:7px;font-size:0.8rem;font-weight:600;cursor:pointer;">Suppr.</button>
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
            <div style="position:fixed;inset:0;z-index:999;display:flex;align-items:center;justify-content:center;">
                <div style="position:absolute;inset:0;background:rgba(0,0,0,0.45);backdrop-filter:blur(4px);" @click="isModalOpen=false"></div>
                <div class="modal-box" style="position:relative;z-index:10;width:100%;max-width:500px;padding:32px;margin:20px;">
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
                            <label class="form-label">Deadline *</label>
                            <input type="date" x-model="currentTask.deadline" class="form-input">
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

                    <p x-show="errorMsg" x-text="errorMsg" style="color:#e11d48;font-size:0.82rem;margin:12px 0 0;background:#fff1f2;border:1px solid #fecdd3;padding:8px 12px;border-radius:8px;"></p>

                    <div style="display:flex;gap:12px;margin-top:24px;justify-content:flex-end;">
                        <button @click="isModalOpen=false" class="btn-secondary">
                            Annuler
                        </button>
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
                tasks: [],
                categories: [],
                isModalOpen: false,
                currentTask: {},
                errorMsg: '',
                init() { 
                    this.fetchTasks(); 
                    this.fetchCategories();
                },
                emptyTask() {
                    return { id: null, title: '', description: '', duration_minutes: 60, priority: 3, deadline: new Date().toISOString().split('T')[0], status: 'todo', category_id: '' };
                },
                fetchCategories() {
                    apiFetch('/web-api/categories').then(r => r.json()).then(d => { this.categories = Array.isArray(d) ? d : []; });
                },
                fetchTasks() {
                    apiFetch('/web-api/tasks').then(r => r.json()).then(d => { this.tasks = Array.isArray(d) ? d : []; });
                },
                openModal(task = null) {
                    this.errorMsg = '';
                    if (task) {
                        this.currentTask = JSON.parse(JSON.stringify(task));
                        // normalise deadline to YYYY-MM-DD
                        if (this.currentTask.deadline) {
                            this.currentTask.deadline = this.currentTask.deadline.split('T')[0].split(' ')[0];
                        }
                    } else {
                        this.currentTask = this.emptyTask();
                    }
                    this.isModalOpen = true;
                },
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
                updateStatus(task) {
                    apiFetch(`/web-api/tasks/${task.id}`, { method: 'PUT', body: JSON.stringify({ status: task.status }) });
                },
                deleteTask(id) {
                    if (confirm('Supprimer cette tâche définitivement ?')) {
                        apiFetch(`/web-api/tasks/${id}`, { method: 'DELETE' }).then(() => this.fetchTasks());
                    }
                }
            }
        }
    </script>
</x-app-layout>
