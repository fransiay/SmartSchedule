<nav class="nav-bar">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            <!-- Logo -->
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 no-underline" style="text-decoration:none;">
                <div style="width:32px;height:32px;background:#0f0f10;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
                <span style="font-size:1.05rem;font-weight:800;color:#0f0f10;letter-spacing:-0.01em;">SmartSchedule</span>
            </a>

            <!-- Desktop Nav Links -->
            <div class="hidden sm:flex items-center gap-1">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('tasks') }}" class="nav-link {{ request()->routeIs('tasks') ? 'active' : '' }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><polyline points="3,6 4,7 6,5"/><polyline points="3,12 4,13 6,11"/><polyline points="3,18 4,19 6,17"/></svg>
                    Tâches
                </a>
                <a href="{{ route('availabilities') }}" class="nav-link {{ request()->routeIs('availabilities') ? 'active' : '' }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
                    Disponibilités
                </a>
                <a href="{{ route('categories') }}" class="nav-link {{ request()->routeIs('categories') ? 'active' : '' }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                    Catégories
                </a>
                <a href="{{ route('calendar') }}" class="nav-link {{ request()->routeIs('calendar') ? 'active' : '' }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Calendrier
                </a>
            </div>
            
            <div class="flex items-center gap-4">
                <!-- Notifications Bell -->
                <div x-data="{ 
                    open: false, 
                    count: {{ Auth::user()->unreadNotifications->count() }},
                    init() {
                        // Vérifie les nouvelles notifications toutes les 30 secondes
                        setInterval(() => {
                            fetch('/web-api/notifications')
                                .then(r => r.json())
                                .then(data => {
                                    this.count = data.filter(n => n.read_at === null).length;
                                });
                        }, 30000);
                    }
                }" class="relative">
                    <button @click="open = !open" class="flex items-center text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out relative" style="background:none;border:none;cursor:pointer;padding:8px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                        <template x-if="count > 0">
                            <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">
                                <span x-text="count"></span>
                            </span>
                        </template>
                    </button>

                    <!-- Dropdown Content (Simple implementation) -->
                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-xl border border-gray-100 z-50 p-2 overflow-hidden" style="display:none;">
                        <div class="p-3 border-bottom border-gray-50 flex justify-between items-center">
                            <span class="font-bold text-sm">Notifications</span>
                            <button @click="fetch('/web-api/notifications/mark-read', {method:'POST', headers:{'X-CSRF-TOKEN': '{{ csrf_token() }}'}}).then(() => count = 0)" class="text-[11px] text-blue-600 hover:underline">Tout marquer comme lu</button>
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            @forelse(Auth::user()->unreadNotifications as $notification)
                                <div class="p-3 hover:bg-gray-50 rounded-lg transition-colors border-bottom border-gray-50 last:border-0">
                                    <p class="text-xs font-semibold text-gray-900">{{ $notification->data['message'] }}</p>
                                    <p class="text-[10px] text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                            @empty
                                <div class="p-6 text-center text-gray-400 text-xs">
                                    Aucune nouvelle notification
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            <!-- User Menu -->
            <div class="hidden sm:flex items-center gap-3">
                <div class="flex items-center gap-2" style="color:#6c6c70;font-size:0.85rem;">
                    <div style="width:30px;height:30px;border-radius:50%;background:#0f0f10;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.78rem;color:white;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <span style="color:#3a3a3c;font-weight:500;">{{ Auth::user()->name }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-danger" style="font-size:0.8rem;padding:6px 14px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        Déconnexion
                    </button>
                </form>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden" x-data="{ open: false }">
                <button @click="open = !open" style="color:#6c6c70;background:none;border:none;cursor:pointer;padding:8px;">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <!-- Mobile menu -->
                <div x-show="open" class="absolute top-16 left-0 right-0 p-4" style="background:#ffffff;border-bottom:1px solid #e5e5e7;box-shadow:0 4px 16px rgba(0,0,0,0.08);">
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                        <a href="{{ route('tasks') }}" class="nav-link {{ request()->routeIs('tasks') ? 'active' : '' }}">Tâches</a>
                        <a href="{{ route('availabilities') }}" class="nav-link {{ request()->routeIs('availabilities') ? 'active' : '' }}">Disponibilités</a>
                        <a href="{{ route('categories') }}" class="nav-link {{ request()->routeIs('categories') ? 'active' : '' }}">Catégories</a>
                        <a href="{{ route('calendar') }}" class="nav-link {{ request()->routeIs('calendar') ? 'active' : '' }}">Calendrier</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="nav-link btn-danger" style="width:100%;justify-content:center;">Déconnexion</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
