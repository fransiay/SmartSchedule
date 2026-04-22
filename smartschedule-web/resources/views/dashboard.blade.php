<x-app-layout>
    <x-slot name="header">
        <p class="page-title">Dashboard</p>
        <p class="page-subtitle">Bienvenue, {{ Auth::user()->name }} — voici votre journée en un coup d'œil.</p>
    </x-slot>

    <div class="pb-16" x-data="dashboardData()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- KPI Cards -->


            <!-- Main content grid -->
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                <!-- Planning du jour (left, wider) -->
                <div class="lg:col-span-3">
                    <div class="card" style="padding:28px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
                            <div>
                                <h2 style="font-size:1.1rem;font-weight:700;color:#0f0f10;margin:0 0 4px;">Planning du
                                    jour</h2>
                                <p style="font-size:0.82rem;color:#aeaeb2;margin:0;">
                                    {{ now()->translatedFormat('l d F Y') }}
                                </p>
                            </div>
                            <button @click="generateSchedule" class="btn-primary" :disabled="generating">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path
                                        d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4" />
                                </svg>
                                <span x-show="!generating">Générer le Planning</span>
                                <span x-show="generating">Génération...</span>
                            </button>
                        </div>

                        <!-- Error message -->
                        <div x-show="errorMsg" x-text="errorMsg"
                            style="background:#fff1f2;border:1px solid #fecdd3;color:#e11d48;padding:10px 14px;border-radius:8px;font-size:0.82rem;margin-bottom:16px;">
                        </div>

                        <!-- Success message -->
                        <div x-show="successMsg" x-text="successMsg"
                            style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;padding:10px 14px;border-radius:8px;font-size:0.82rem;margin-bottom:16px;">
                        </div>

                        <div style="display:flex;flex-direction:column;gap:8px;">
                            <template x-for="schedule in todaySchedules" :key="schedule.id">
                                <div class="schedule-item">
                                    <div style="width:90px;flex-shrink:0;">
                                        <div style="font-size:0.85rem;font-weight:700;color:#0f0f10;"
                                            x-text="formatTime(schedule.start_time)"></div>
                                        <div style="font-size:0.75rem;color:#aeaeb2;">→ <span
                                                x-text="formatTime(schedule.end_time)"></span></div>
                                    </div>
                                    <div class="accent-bar"></div>
                                    <div style="flex:1;min-width:0;">
                                        <div style="font-weight:600;color:#0f0f10;" x-text="schedule.task?.title"></div>
                                        <div style="font-size:0.78rem;color:#aeaeb2;margin-top:2px;"
                                            x-text="(schedule.task?.duration_minutes || '') + ' min'"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Chart (right) -->
                <div class="lg:col-span-2">
                    <div class="card" style="padding:28px;">
                        <h2 style="font-size:1.1rem;font-weight:700;color:#0f0f10;margin:0 0 20px;">Répartition des
                            tâches</h2>
                        <div style="position:relative;height:220px;">
                            <canvas id="tasksChart"></canvas>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:8px;margin-top:20px;">
                            <div style="display:flex;align-items:center;justify-content:space-between;">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <div style="width:10px;height:10px;border-radius:2px;background:#0f0f10;"></div>
                                    <span style="font-size:0.82rem;color:#6c6c70;">À faire</span>
                                </div>
                                <span style="font-size:0.82rem;font-weight:700;color:#0f0f10;"
                                    x-text="stats.todo"></span>
                            </div>
                            <div style="display:flex;align-items:center;justify-content:space-between;">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <div style="width:10px;height:10px;border-radius:2px;background:#6c6c70;"></div>
                                    <span style="font-size:0.82rem;color:#6c6c70;">En cours</span>
                                </div>
                                <span style="font-size:0.82rem;font-weight:700;color:#0f0f10;"
                                    x-text="stats.inProgress"></span>
                            </div>
                            <div style="display:flex;align-items:center;justify-content:space-between;">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <div style="width:10px;height:10px;border-radius:2px;background:#d1d1d6;"></div>
                                    <span style="font-size:0.82rem;color:#6c6c70;">Terminées</span>
                                </div>
                                <span style="font-size:0.82rem;font-weight:700;color:#0f0f10;"
                                    x-text="stats.completed"></span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const apiFetch = (url, opts = {}) => fetch(url, {
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', ...(opts.headers || {}) },
            ...opts
        });

        let chartInstance = null;

        function dashboardData() {
            return {
                tasks: [],
                schedules: [],
                generating: false,
                errorMsg: '',
                successMsg: '',
                get stats() {
                    const todo = this.tasks.filter(t => t.status === 'todo').length;
                    const inProgress = this.tasks.filter(t => t.status === 'in_progress').length;
                    const completed = this.tasks.filter(t => t.status === 'done').length;
                    return { total: this.tasks.length, completed, pending: this.tasks.length - completed, todo, inProgress };
                },
                get todaySchedules() {
                    const today = new Date().toISOString().split('T')[0];
                    return this.schedules
                        .filter(s => s.start_time && s.start_time.startsWith(today))
                        .sort((a, b) => new Date(a.start_time) - new Date(b.start_time));
                },
                formatTime(datetime) {
                    if (!datetime) return '';
                    return new Date(datetime).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
                },
                init() {
                    this.fetchData();
                    this.$watch('tasks', () => this.renderChart());
                },
                fetchData() {
                    apiFetch('/web-api/tasks').then(r => r.json()).then(d => {
                        this.tasks = Array.isArray(d) ? d : [];
                        this.renderChart();
                    });
                    apiFetch('/web-api/schedule').then(r => r.json()).then(d => {
                        this.schedules = Array.isArray(d) ? d : [];
                    });
                },
                renderChart() {
                    const ctx = document.getElementById('tasksChart');
                    if (!ctx) return;
                    const todo = this.tasks.filter(t => t.status === 'todo').length;
                    const inProgress = this.tasks.filter(t => t.status === 'in_progress').length;
                    const done = this.tasks.filter(t => t.status === 'done').length;

                    if (chartInstance) chartInstance.destroy();
                    chartInstance = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['À faire', 'En cours', 'Terminées'],
                            datasets: [{
                                data: [todo, inProgress, done],
                                backgroundColor: ['#0f0f10', '#6c6c70', '#d1d1d6'],
                                borderColor: ['#ffffff', '#ffffff', '#ffffff'],
                                borderWidth: 3,
                                hoverOffset: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '68%',
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: ctx => ` ${ctx.label} : ${ctx.parsed}`
                                    }
                                }
                            }
                        }
                    });
                },
                generateSchedule() {
                    this.generating = true;
                    this.errorMsg = '';
                    this.successMsg = '';
                    apiFetch('/web-api/schedule/generate', { method: 'POST' })
                        .then(r => {
                            if (!r.ok) return r.json().then(e => Promise.reject(e));
                            return r.json();
                        })
                        .then(data => {
                            this.schedules = data.schedules || [];
                            this.generating = false;
                            this.successMsg = `Planning généré : ${data.schedules?.length || 0} créneau(x) planifié(s).`;
                            setTimeout(() => this.successMsg = '', 4000);
                        })
                        .catch(err => {
                            this.generating = false;
                            this.errorMsg = err?.message || 'Erreur lors de la génération du planning.';
                            setTimeout(() => this.errorMsg = '', 5000);
                        });
                }
            }
        }
    </script>
</x-app-layout>