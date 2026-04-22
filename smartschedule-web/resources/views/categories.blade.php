<x-app-layout>
    <x-slot name="header">
        <p class="page-title">Catégories</p>
        <p class="page-subtitle">Organisez vos tâches par thématiques (Travail, Personnel, Loisirs, etc.)</p>
    </x-slot>

    <div class="pb-16" x-data="categoriesController()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card" style="padding:28px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
                    <div>
                        <h2 style="font-size:1.1rem;font-weight:700;color:#0f0f10;margin:0 0 4px;">Mes Catégories</h2>
                        <p style="font-size:0.82rem;color:#aeaeb2;margin:0;" x-text="categories.length + ' catégorie(s) au total'"></p>
                    </div>
                    <button @click="openModal()" class="btn-primary">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Nouvelle Catégorie
                    </button>
                </div>

                <div x-show="categories.length === 0" style="text-align:center;padding:50px 0;color:#aeaeb2;">
                    <svg style="margin:0 auto 12px;opacity:0.4;" width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                    <p>Aucune catégorie définie.</p>
                </div>

                <div x-show="categories.length > 0" style="overflow-x:auto;border-radius:12px;border:1px solid #e5e5e7;">
                    <table class="data-table" style="width:100%;border-collapse:collapse;">
                        <thead>
                            <tr>
                                <th style="text-align:left;">Nom</th>
                                <th style="text-align:left;">Couleur</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="category in categories" :key="category.id">
                                <tr>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:12px;">
                                            <div :style="'width:12px;height:12px;border-radius:50%;background-color:' + (category.color || '#6366f1')"></div>
                                            <span style="font-weight:600;color:#0f0f10;" x-text="category.name"></span>
                                        </div>
                                    </td>
                                    <td><span style="font-family:monospace;color:#6c6c70;" x-text="category.color || '#6366f1'"></span></td>
                                    <td style="text-align:right;">
                                        <div style="display:flex;gap:8px;justify-content:flex-end;">
                                            <button @click="openModal(category)"
                                                    style="background:#f3f3f4;border:1px solid #e5e5e7;color:#0f0f10;padding:5px 12px;border-radius:7px;font-size:0.8rem;font-weight:600;cursor:pointer;">Éditer</button>
                                            <button @click="deleteCategory(category.id)"
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
                <div class="modal-box" style="position:relative;z-index:10;width:100%;max-width:400px;padding:32px;margin:20px;">
                    <h3 style="font-size:1.25rem;font-weight:700;color:#0f0f10;margin:0 0 24px;" x-text="currentCategory.id?'Modifier la Catégorie':'Nouvelle Catégorie'"></h3>

                    <div style="display:flex;flex-direction:column;gap:16px;">
                        <div>
                            <label class="form-label">Nom de la catégorie *</label>
                            <input type="text" x-model="currentCategory.name" class="form-input" placeholder="Ex: Travail">
                        </div>
                        <div>
                            <label class="form-label">Couleur</label>
                            <div style="display:flex;gap:10px;align-items:center;">
                                <input type="color" x-model="currentCategory.color" style="width:40px;height:40px;border:none;border-radius:8px;background:none;cursor:pointer;">
                                <input type="text" x-model="currentCategory.color" class="form-input" style="flex:1;" placeholder="#6366f1">
                            </div>
                        </div>
                    </div>

                    <p x-show="errorMsg" x-text="errorMsg" style="color:#e11d48;font-size:0.82rem;margin:12px 0 0;background:#fff1f2;border:1px solid #fecdd3;padding:8px 12px;border-radius:8px;"></p>

                    <div style="display:flex;gap:12px;margin-top:24px;justify-content:flex-end;">
                        <button @click="isModalOpen=false" class="btn-secondary">
                            Annuler
                        </button>
                        <button @click="saveCategory" class="btn-primary">
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

        function categoriesController() {
            return {
                categories: [],
                isModalOpen: false,
                currentCategory: {},
                errorMsg: '',
                init() { this.fetchCategories(); },
                emptyCategory() {
                    return { id: null, name: '', color: '#3a3a3c' };
                },
                fetchCategories() {
                    apiFetch('/web-api/categories').then(r => r.json()).then(d => { this.categories = Array.isArray(d) ? d : []; });
                },
                openModal(category = null) {
                    this.errorMsg = '';
                    if (category) {
                        this.currentCategory = JSON.parse(JSON.stringify(category));
                    } else {
                        this.currentCategory = this.emptyCategory();
                    }
                    this.isModalOpen = true;
                },
                saveCategory() {
                    this.errorMsg = '';
                    if (!this.currentCategory.name) { this.errorMsg = 'Le nom est obligatoire.'; return; }
                    const isNew = !this.currentCategory.id;
                    const url = isNew ? '/web-api/categories' : `/web-api/categories/${this.currentCategory.id}`;
                    apiFetch(url, { method: isNew ? 'POST' : 'PUT', body: JSON.stringify(this.currentCategory) })
                        .then(r => r.json())
                        .then(data => {
                            if (data.errors) {
                                this.errorMsg = Object.values(data.errors).flat().join(' ');
                            } else {
                                this.isModalOpen = false;
                                this.fetchCategories();
                            }
                        })
                        .catch(() => { this.errorMsg = 'Erreur réseau.'; });
                },
                deleteCategory(id) {
                    if (confirm('Supprimer cette catégorie ? Les tâches associées perdront leur catégorie.')) {
                        apiFetch(`/web-api/categories/${id}`, { method: 'DELETE' }).then(() => this.fetchCategories());
                    }
                }
            }
        }
    </script>
</x-app-layout>
