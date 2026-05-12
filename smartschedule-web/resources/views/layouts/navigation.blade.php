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
                <a href="{{ route('analytics') }}" class="nav-link {{ request()->routeIs('analytics') ? 'active' : '' }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    Analytics
                </a>
            </div>
            
            <div class="flex items-center gap-4">
                <!-- Notifications Bell -->
                <div x-data="notifController()" class="relative">
                    <button @click="toggle()" class="relative flex items-center justify-center w-9 h-9 rounded-xl hover:bg-gray-100 transition-all" style="background:none;border:none;cursor:pointer;color:#6c6c70;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                        <template x-if="count > 0">
                            <span x-text="count > 9 ? '9+' : count" style="position:absolute;top:0;right:0;min-width:16px;height:16px;padding:0 4px;display:flex;align-items:center;justify-content:center;border-radius:50%;background:#e11d48;color:#fff;font-size:9px;font-weight:800;line-height:1;"></span>
                        </template>
                    </button>

                    <!-- Dropdown -->
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click.away="open = false" style="display:none;position:absolute;right:0;margin-top:8px;width:370px;z-index:50;background:#ffffff;border:1px solid #e5e5e7;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,0.12);overflow:hidden;">

                        <!-- Header -->
                        <div style="padding:14px 16px;border-bottom:1px solid #f2f2f3;display:flex;align-items:center;justify-content:space-between;">
                            <div style="display:flex;align-items:center;gap:8px;">
                                <span style="font-size:0.875rem;font-weight:800;color:#0f0f10;">Notifications</span>
                                <template x-if="count > 0"><span x-text="count" style="font-size:10px;font-weight:800;background:#fee2e2;color:#e11d48;padding:2px 7px;border-radius:20px;"></span></template>
                            </div>
                            <button x-show="count > 0" @click="markAllRead()" style="font-size:11px;font-weight:700;color:#3b82f6;background:none;border:none;cursor:pointer;padding:4px 10px;border-radius:6px;transition:background 0.15s;" onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background='transparent'">Tout lire</button>
                        </div>

                        <!-- List -->
                        <div style="max-height:380px;overflow-y:auto;">
                            <template x-if="loading">
                                <div style="padding:32px;text-align:center;color:#aeaeb2;font-size:13px;">Chargement...</div>
                            </template>
                            <template x-if="!loading && notifications.length === 0">
                                <div style="padding:40px 24px;text-align:center;">
                                    <div style="width:44px;height:44px;background:#f7f7f8;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d1d1d6" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                                    </div>
                                    <p style="font-size:13px;font-weight:700;color:#0f0f10;margin:0 0 4px;">Tout est à jour !</p>
                                    <p style="font-size:12px;color:#aeaeb2;margin:0;">Aucune notification pour le moment.</p>
                                </div>
                            </template>
                            <template x-if="!loading && notifications.length > 0">
                                <div>
                                    <template x-for="n in notifications" :key="n.id">
                                        <a :href="n.data?.action || '/tasks'" @click.prevent="handleClick(n)" style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;cursor:pointer;border-bottom:1px solid #f7f7f8;transition:background 0.15s;text-decoration:none;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='transparent'">
                                            <div :style="'background:' + typeColor(n.data?.type, 0.1) + ';color:' + typeColor(n.data?.type, 1)" style="width:34px;height:34px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                                <span x-text="typeEmoji(n.data?.type)" style="font-size:16px;"></span>
                                            </div>
                                            <div style="flex:1;min-width:0;">
                                                <p x-text="n.data?.message || 'Notification'" :style="!n.read_at ? 'font-weight:700;color:#0f0f10;' : 'font-weight:500;color:#6c6c70;'" style="font-size:13px;margin:0 0 4px;line-height:1.4;"></p>
                                                <div style="display:flex;align-items:center;gap:6px;">
                                                    <span x-text="timeAgo(n.created_at)" style="font-size:11px;color:#aeaeb2;font-weight:500;"></span>
                                                    <span style="font-size:10px;font-weight:700;color:#3b82f6;">→ Voir</span>
                                                </div>
                                            </div>
                                            <div x-show="!n.read_at" style="width:8px;height:8px;border-radius:50%;background:#3b82f6;flex-shrink:0;margin-top:6px;"></div>
                                        </a>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <!-- Footer -->
                        <div style="padding:10px 16px;border-top:1px solid #f2f2f3;text-align:center;">
                            <a href="{{ route('tasks') }}" style="font-size:11px;font-weight:700;color:#6c6c70;text-decoration:none;text-transform:uppercase;letter-spacing:0.05em;" onmouseover="this.style.color='#0f0f10'" onmouseout="this.style.color='#6c6c70'">Voir toutes les tâches</a>
                        </div>
                    </div>
                </div>

            <!-- User Menu -->
            <div class="hidden sm:flex items-center gap-4">
                <div class="flex items-center gap-2.5 pl-4 border-l border-gray-100">
                    <div class="w-8 h-8 rounded-full bg-gray-900 flex items-center justify-center font-bold text-[11px] text-white shadow-sm ring-2 ring-white">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[13px] font-bold text-gray-900 leading-none">{{ Auth::user()->name }}</span>
                        <span class="text-[10px] font-medium text-gray-400 mt-0.5">Membre</span>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="group flex items-center gap-2 px-3 py-2 rounded-xl text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition-all duration-200">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="group-hover:rotate-12 transition-transform"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        <span class="text-[12px] font-bold">Quitter</span>
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
