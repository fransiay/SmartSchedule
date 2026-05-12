<x-app-layout>
    <x-slot name="header">
        <p class="page-title">Analytics</p>
        <p class="page-subtitle">Visualisez vos performances, le temps passé et votre taux de complétion.</p>
    </x-slot>

    <div class="pb-16" x-data="analyticsController()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- KPI Overview -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="card p-6">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Total tâches</p>
                    <p class="text-3xl font-black text-gray-900" x-text="stats.overview?.total ?? '—'"></p>
                </div>
                <div class="card p-6">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Taux de complétion</p>
                    <p class="text-3xl font-black text-green-600" x-text="(stats.overview?.completion_rate ?? '—') + '%'"></p>
                </div>
                <div class="card p-6">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">En retard</p>
                    <p class="text-3xl font-black text-rose-500" x-text="stats.overview?.overdue ?? '—'"></p>
                </div>
                <div class="card p-6">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Temps total planifié</p>
                    <p class="text-3xl font-black text-gray-900" x-text="formatMinutes(stats.overview?.total_minutes)"></p>
                </div>
            </div>

            <div class="mb-6">

                <!-- Tendance 30 jours -->
                <div class="card p-6">
                    <h2 class="text-[15px] font-bold text-gray-900 mb-1">Tâches complétées — 30 derniers jours</h2>
                    <p class="text-[12px] text-gray-400 mb-5">Nombre de tâches terminées par jour.</p>
                    <div style="position:relative;height:200px;">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Temps par catégorie -->
                <div class="card p-6">
                    <h2 class="text-[15px] font-bold text-gray-900 mb-1">Temps par catégorie</h2>
                    <p class="text-[12px] text-gray-400 mb-5">Durée cumulée planifiée (en heures).</p>
                    <div class="space-y-4">
                        <template x-for="cat in stats.by_category ?? []" :key="cat.category_id">
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2.5 h-2.5 rounded-full" :style="'background:' + cat.category_color"></div>
                                        <span class="text-[13px] font-semibold text-gray-700" x-text="cat.category_name"></span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-[11px] font-medium text-gray-400" x-text="formatMinutes(cat.total_minutes)"></span>
                                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full"
                                              :style="'background:' + cat.category_color + '22;color:' + cat.category_color"
                                              x-text="cat.completion_rate + '%'"></span>
                                    </div>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-700"
                                         :style="'width:' + cat.completion_rate + '%;background:' + cat.category_color">
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template x-if="(stats.by_category ?? []).length === 0">
                            <div class="py-8 text-center text-gray-400 text-sm">Aucune donnée disponible.</div>
                        </template>
                    </div>
                </div>

                <!-- Répartition par priorité -->
                <div class="card p-6">
                    <h2 class="text-[15px] font-bold text-gray-900 mb-1">Taux de complétion par priorité</h2>
                    <p class="text-[12px] text-gray-400 mb-5">Performance selon le niveau d'urgence.</p>
                    <div class="space-y-3">
                        <template x-for="p in stats.priority_breakdown ?? []" :key="p.priority">
                            <div class="flex items-center gap-4">
                                <div class="w-16 flex-shrink-0">
                                    <span class="text-[11px] font-bold px-2 py-1 rounded-lg"
                                          :class="{
                                            'bg-red-50 text-red-600': p.priority <= 2,
                                            'bg-yellow-50 text-yellow-700': p.priority === 3,
                                            'bg-green-50 text-green-700': p.priority >= 4,
                                          }"
                                          x-text="p.label"></span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between mb-1">
                                        <span class="text-[11px] text-gray-400" x-text="p.done + '/' + p.total + ' tâches'"></span>
                                        <span class="text-[11px] font-bold text-gray-700" x-text="p.completion_rate + '%'"></span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-700"
                                             :class="{
                                                'bg-red-500': p.priority <= 2,
                                                'bg-yellow-500': p.priority === 3,
                                                'bg-green-500': p.priority >= 4,
                                             }"
                                             :style="'width:' + p.completion_rate + '%'">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const apiFetch = (url, opts = {}) => fetch(url, {
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', ...(opts.headers || {}) },
            ...opts
        });

        let trendChart = null;

        function analyticsController() {
            return {
                stats: {},
                init() { this.load(); },
                load() {
                    apiFetch('/web-api/analytics').then(r => r.json()).then(data => {
                        this.stats = data;
                        this.$nextTick(() => {
                            this.renderTrend(data.completion_trend || []);
                        });
                    });
                },
                formatMinutes(min) {
                    if (!min) return '0 min';
                    if (min < 60) return min + ' min';
                    const h = Math.floor(min / 60);
                    const m = min % 60;
                    return m > 0 ? h + 'h ' + m + 'min' : h + 'h';
                },
                renderTrend(trend) {
                    const ctx = document.getElementById('trendChart');
                    if (!ctx) return;
                    if (trendChart) trendChart.destroy();
                    const labels = trend.map(d => d.label);
                    const data   = trend.map(d => d.count);
                    trendChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels,
                            datasets: [{
                                data,
                                backgroundColor: data.map(v => v > 0 ? '#0f0f10' : '#f3f3f4'),
                                borderRadius: 6,
                                borderSkipped: false,
                            }]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => c.parsed.y + ' tâche(s)' } } },
                            scales: {
                                x: { grid: { display: false }, ticks: { maxTicksLimit: 10, font: { size: 10 } } },
                                y: { grid: { color: '#f3f3f4' }, ticks: { stepSize: 1, font: { size: 10 } }, beginAtZero: true }
                            }
                        }
                    });
                }
            }
        }
    </script>
</x-app-layout>
