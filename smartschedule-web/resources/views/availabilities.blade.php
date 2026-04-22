<x-app-layout>
    <x-slot name="header">
        <p class="page-title">Disponibilités</p>
        <p class="page-subtitle">Définissez vos plages horaires de travail pour chaque jour de la semaine.</p>
    </x-slot>

    <div class="pb-16" x-data="availabilitiesController()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Toast -->
            <div x-show="toast" x-text="toast"
                 style="position:fixed;bottom:24px;right:24px;background:#0f0f10;color:#fff;padding:12px 20px;border-radius:10px;font-size:0.875rem;font-weight:500;z-index:9999;box-shadow:0 8px 24px rgba(0,0,0,0.15);">
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px;">
                <template x-for="(dayName, dayIndex) in daysOfWeek" :key="dayIndex">
                    <div class="card" style="padding:24px;">
                        <!-- Day header -->
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:36px;height:36px;border-radius:10px;background:#0f0f10;display:flex;align-items:center;justify-content:center;">
                                    <span style="font-size:0.75rem;font-weight:800;color:#ffffff;" x-text="dayName.charAt(0)"></span>
                                </div>
                                <span style="font-weight:700;color:#0f0f10;font-size:0.95rem;" x-text="dayName"></span>
                            </div>
                            <button @click="addBlock(dayIndex)"
                                    style="background:#f3f3f4;border:1px solid #e5e5e7;color:#0f0f10;padding:5px 12px;border-radius:7px;font-size:0.78rem;font-weight:700;cursor:pointer;transition:all 0.2s;"
                                    onmouseover="this.style.background='#e9e9eb'" onmouseout="this.style.background='#f3f3f4'">
                                + Ajouter
                            </button>
                        </div>

                        <!-- Saved blocks -->
                        <div style="display:flex;flex-direction:column;gap:10px;">
                            <template x-for="block in getSavedBlocksForDay(dayIndex)" :key="block.id">
                                <div style="background:#f7f7f8;border:1px solid #e5e5e7;border-radius:10px;padding:12px;display:flex;align-items:center;gap:10px;">
                                    <div style="display:flex;align-items:center;gap:6px;flex:1;">
                                        <input type="time" x-model="block.start_time" @change="updateBlock(block)"
                                               style="background:#fff;border:1px solid #d1d1d6;border-radius:6px;padding:4px 8px;color:#0f0f10;font-size:0.82rem;width:90px;outline:none;cursor:pointer;">
                                        <span style="color:#aeaeb2;font-size:0.8rem;">→</span>
                                        <input type="time" x-model="block.end_time" @change="updateBlock(block)"
                                               style="background:#fff;border:1px solid #d1d1d6;border-radius:6px;padding:4px 8px;color:#0f0f10;font-size:0.82rem;width:90px;outline:none;cursor:pointer;">
                                    </div>
                                    <button @click="deleteBlock(block)"
                                            style="background:#fff1f2;border:1px solid #fecdd3;border-radius:7px;padding:5px 8px;cursor:pointer;display:flex;align-items:center;">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                    </button>
                                </div>
                            </template>

                            <!-- Pending (unsaved) blocks -->
                            <template x-for="block in getTempBlocksForDay(dayIndex)" :key="'tmp-'+block.tempId">
                                <div style="background:#fafafa;border:2px dashed #d1d1d6;border-radius:10px;padding:12px;">
                                    <div style="display:flex;align-items:center;gap:6px;margin-bottom:10px;">
                                        <input type="time" x-model="block.start_time"
                                               style="background:#fff;border:1px solid #d1d1d6;border-radius:6px;padding:4px 8px;color:#0f0f10;font-size:0.82rem;width:90px;outline:none;cursor:pointer;">
                                        <span style="color:#aeaeb2;font-size:0.8rem;">→</span>
                                        <input type="time" x-model="block.end_time"
                                               style="background:#fff;border:1px solid #d1d1d6;border-radius:6px;padding:4px 8px;color:#0f0f10;font-size:0.82rem;width:90px;outline:none;cursor:pointer;">
                                    </div>
                                    <div style="display:flex;gap:8px;">
                                        <button @click="saveTempBlock(block)"
                                                style="flex:1;background:#0f0f10;color:#fff;border:none;border-radius:7px;padding:7px 12px;font-size:0.78rem;font-weight:700;cursor:pointer;transition:opacity 0.2s;"
                                                :disabled="savingId === block.tempId"
                                                x-text="savingId === block.tempId ? 'Sauvegarde...' : 'Sauvegarder'">
                                        </button>
                                        <button @click="removeTempBlock(block)"
                                                style="background:#fff1f2;border:1px solid #fecdd3;color:#e11d48;border-radius:7px;padding:7px 10px;font-size:0.78rem;font-weight:600;cursor:pointer;">
                                            Annuler
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <!-- Empty state -->
                            <div x-show="getSavedBlocksForDay(dayIndex).length === 0 && getTempBlocksForDay(dayIndex).length === 0"
                                 style="text-align:center;padding:18px;color:#aeaeb2;font-size:0.82rem;font-style:italic;border:1px dashed #e5e5e7;border-radius:8px;">
                                Jour de repos
                            </div>
                        </div>
                    </div>
                </template>
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

        function availabilitiesController() {
            return {
                daysOfWeek: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
                availabilities: [],
                tempBlocks: [],
                savingId: null,
                toast: '',

                init() { this.fetchAvailabilities(); },

                fetchAvailabilities() {
                    apiFetch('/web-api/availabilities').then(r => r.json()).then(d => {
                        this.availabilities = Array.isArray(d) ? d : [];
                    }).catch(() => this.showToast('Erreur lors du chargement.', true));
                },

                getSavedBlocksForDay(dayIndex) {
                    return this.availabilities.filter(a => a.day_of_week === dayIndex);
                },

                getTempBlocksForDay(dayIndex) {
                    return this.tempBlocks.filter(t => t.day_of_week === dayIndex);
                },

                addBlock(dayIndex) {
                    this.tempBlocks.push({
                        tempId: Date.now() + Math.random(),
                        day_of_week: dayIndex,
                        start_time: '09:00',
                        end_time: '17:00',
                    });
                },

                removeTempBlock(block) {
                    this.tempBlocks = this.tempBlocks.filter(t => t.tempId !== block.tempId);
                },

                saveTempBlock(block) {
                    if (!block.start_time || !block.end_time) return;
                    if (block.start_time >= block.end_time) {
                        this.showToast('L\'heure de fin doit être après l\'heure de début.', true);
                        return;
                    }
                    this.savingId = block.tempId;
                    apiFetch('/web-api/availabilities', {
                        method: 'POST',
                        body: JSON.stringify({
                            day_of_week: block.day_of_week,
                            start_time: block.start_time,
                            end_time: block.end_time,
                            is_active: true
                        })
                    })
                    .then(r => {
                        if (!r.ok) return r.json().then(e => Promise.reject(e));
                        return r.json();
                    })
                    .then(() => {
                        this.tempBlocks = this.tempBlocks.filter(t => t.tempId !== block.tempId);
                        this.savingId = null;
                        this.fetchAvailabilities();
                        this.showToast('Disponibilité sauvegardée !');
                    })
                    .catch(err => {
                        this.savingId = null;
                        const msg = err?.message || Object.values(err?.errors || {}).flat().join(' ') || 'Erreur lors de la sauvegarde.';
                        this.showToast(msg, true);
                    });
                },

                updateBlock(block) {
                    if (!block.id || !block.start_time || !block.end_time) return;
                    apiFetch(`/web-api/availabilities/${block.id}`, {
                        method: 'PUT',
                        body: JSON.stringify({
                            start_time: block.start_time,
                            end_time: block.end_time,
                            is_active: true
                        })
                    }).then(r => r.json()).then(() => {
                        this.showToast('Horaire mis à jour.');
                    }).catch(() => this.showToast('Erreur lors de la mise à jour.', true));
                },

                deleteBlock(block) {
                    if (!confirm('Supprimer cette plage horaire ?')) return;
                    apiFetch(`/web-api/availabilities/${block.id}`, { method: 'DELETE' })
                        .then(() => {
                            this.fetchAvailabilities();
                            this.showToast('Plage supprimée.');
                        })
                        .catch(() => this.showToast('Erreur lors de la suppression.', true));
                },

                showToast(msg, isError = false) {
                    this.toast = msg;
                    setTimeout(() => this.toast = '', 3000);
                }
            }
        }
    </script>
</x-app-layout>
