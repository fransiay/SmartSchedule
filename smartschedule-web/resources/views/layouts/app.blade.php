<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>SmartSchedule — {{ config('app.name', 'SmartSchedule') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            *, *::before, *::after { box-sizing: border-box; }
            html { scroll-behavior: smooth; }
            body {
                font-family: 'Inter', sans-serif;
                background: #f7f7f8;
                color: #0f0f10;
                min-height: 100vh;
                margin: 0;
            }

            /* ---- App wrapper ---- */
            .app-bg {
                min-height: 100vh;
                background: #f7f7f8;
            }

            /* ---- Navigation ---- */
            .nav-bar {
                background: #ffffff;
                border-bottom: 1px solid #e5e5e7;
                position: sticky;
                top: 0;
                z-index: 100;
                box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            }

            /* ---- Cards ---- */
            .card {
                background: #ffffff;
                border: 1px solid #e5e5e7;
                border-radius: 14px;
                transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 1px 4px rgba(0,0,0,0.05);
            }
            .card:hover {
                border-color: #c7c7cc;
                box-shadow: 0 6px 24px rgba(0,0,0,0.08);
                transform: translateY(-1px);
            }

            /* ---- Buttons ---- */
            .btn-primary {
                background: #0f0f10;
                color: #ffffff;
                border: none;
                padding: 10px 20px;
                border-radius: 10px;
                font-weight: 600;
                font-size: 0.875rem;
                cursor: pointer;
                transition: all 0.2s ease;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            }
            .btn-primary:hover {
                background: #2a2a2e;
                transform: translateY(-1px);
                box-shadow: 0 4px 16px rgba(0,0,0,0.2);
            }
            .btn-primary:disabled {
                opacity: 0.5;
                cursor: not-allowed;
                transform: none;
            }
            .btn-secondary {
                background: #f3f3f4;
                color: #3a3a3c;
                border: 1px solid #e5e5e7;
                padding: 10px 20px;
                border-radius: 10px;
                font-weight: 600;
                font-size: 0.875rem;
                cursor: pointer;
                transition: all 0.2s ease;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }
            .btn-secondary:hover {
                background: #e9e9eb;
                border-color: #c7c7cc;
            }
            .btn-danger {
                background: #fff1f2;
                color: #e11d48;
                border: 1px solid #fecdd3;
                padding: 8px 14px;
                border-radius: 8px;
                font-weight: 600;
                font-size: 0.8rem;
                cursor: pointer;
                transition: all 0.2s ease;
            }
            .btn-danger:hover { background: #ffe4e6; border-color: #fda4af; }

            /* ---- Stat cards ---- */
            .stat-card {
                border-radius: 16px;
                padding: 28px;
                position: relative;
                overflow: hidden;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                border: 1px solid #e5e5e7;
                box-shadow: 0 1px 4px rgba(0,0,0,0.05);
            }
            .stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 32px rgba(0,0,0,0.1); }
            .stat-card.dark { background: #0f0f10; color: #ffffff; border-color: #0f0f10; }
            .stat-card.mid  { background: #3a3a3c; color: #ffffff; border-color: #3a3a3c; }
            .stat-card.light{ background: #ffffff; color: #0f0f10; }

            /* ---- Form inputs ---- */
            .form-input {
                width: 100%;
                background: #ffffff;
                border: 1px solid #d1d1d6;
                border-radius: 10px;
                padding: 10px 16px;
                color: #0f0f10;
                font-size: 0.9rem;
                font-family: 'Inter', sans-serif;
                transition: border-color 0.2s ease, box-shadow 0.2s ease;
                outline: none;
            }
            .form-input:focus {
                border-color: #0f0f10;
                box-shadow: 0 0 0 3px rgba(15,15,16,0.08);
            }
            .form-input::placeholder { color: #aeaeb2; }
            select.form-input { cursor: pointer; background-color: #ffffff; }

            /* ---- Labels ---- */
            .form-label {
                display: block;
                font-size: 0.78rem;
                font-weight: 600;
                color: #6c6c70;
                text-transform: uppercase;
                letter-spacing: 0.07em;
                margin-bottom: 6px;
            }

            /* ---- Modal ---- */
            .modal-backdrop {
                background: rgba(0, 0, 0, 0.45);
                backdrop-filter: blur(4px);
            }
            .modal-box {
                background: #ffffff;
                border: 1px solid #e5e5e7;
                border-radius: 18px;
                box-shadow: 0 24px 64px rgba(0,0,0,0.15);
            }

            /* ---- Table ---- */
            .data-table th {
                background: #f7f7f8;
                color: #6c6c70;
                font-size: 0.7rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                padding: 12px 20px;
                border-bottom: 1px solid #e5e5e7;
            }
            .data-table td {
                padding: 14px 20px;
                border-bottom: 1px solid #f2f2f3;
                color: #3a3a3c;
                font-size: 0.88rem;
            }
            .data-table tr:hover td { background: #fafafa; }
            .data-table tr:last-child td { border-bottom: none; }

            /* ---- Badges ---- */
            .badge { border-radius: 20px; padding: 3px 10px; font-size: 0.72rem; font-weight: 700; }
            .badge-red    { background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; }
            .badge-yellow { background: #fefce8; color: #a16207; border: 1px solid #fde68a; }
            .badge-green  { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
            .badge-blue   { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
            .badge-gray   { background: #f3f3f4; color: #6c6c70; border: 1px solid #e5e5e7; }

            /* ---- Nav links ---- */
            .nav-link {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 14px;
                border-radius: 8px;
                color: #6c6c70;
                font-size: 0.875rem;
                font-weight: 500;
                text-decoration: none;
                transition: all 0.2s ease;
                border: 1px solid transparent;
            }
            .nav-link:hover { color: #0f0f10; background: #f3f3f4; }
            .nav-link.active { color: #0f0f10; background: #f3f3f4; border-color: #e5e5e7; font-weight: 600; }

            /* ---- Page header ---- */
            .page-header {
                padding: 32px 0 0;
                margin-bottom: 28px;
            }
            .page-title {
                font-size: 2rem;
                font-weight: 800;
                color: #0f0f10;
                margin: 0 0 6px;
                letter-spacing: -0.02em;
            }
            .page-subtitle {
                color: #6c6c70;
                font-size: 0.9rem;
                margin: 0;
            }

            /* ---- Schedule item ---- */
            .schedule-item {
                display: flex;
                align-items: center;
                padding: 14px 18px;
                border-radius: 12px;
                background: #f7f7f8;
                border: 1px solid #e5e5e7;
                transition: all 0.2s ease;
                gap: 16px;
            }
            .schedule-item:hover {
                background: #f3f3f4;
                border-color: #c7c7cc;
            }

            /* ---- Dividers ---- */
            .accent-bar {
                width: 3px;
                height: 36px;
                border-radius: 3px;
                background: #0f0f10;
                flex-shrink: 0;
            }

            /* ---- Toast ---- */
            .toast {
                position: fixed;
                bottom: 24px;
                right: 24px;
                background: #0f0f10;
                color: #ffffff;
                padding: 12px 20px;
                border-radius: 10px;
                font-size: 0.875rem;
                font-weight: 500;
                z-index: 9999;
                box-shadow: 0 8px 24px rgba(0,0,0,0.2);
                animation: slideUp 0.3s ease;
            }
            .toast.success { background: #0f0f10; }
            .toast.error   { background: #e11d48; }
            @keyframes slideUp {
                from { transform: translateY(20px); opacity: 0; }
                to   { transform: translateY(0);    opacity: 1; }
            }

            /* ---- Chip/pill input addon ---- */
            .chip {
                display: inline-flex; align-items: center; gap: 6px;
                background: #f3f3f4; border: 1px solid #e5e5e7;
                border-radius: 20px; padding: 4px 12px;
                font-size: 0.78rem; font-weight: 600; color: #3a3a3c;
            }
        </style>
    </head>
    <body>
        <div class="app-bg">
            @include('layouts.navigation')

            @if (isset($header))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="page-header">
                        {{ $header }}
                    </div>
                </div>
            @endif

            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- Global Notification Controller -->
        <script>
            function notifController() {
                const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                return {
                    open: false,
                    count: 0,
                    notifications: [],
                    loading: false,
                    _interval: null,

                    init() {
                        // Vérifie les échéances au chargement de la page
                        fetch('/web-api/notifications/check-deadlines', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
                        }).then(() => this.fetchNotifications()).catch(() => this.fetchNotifications());
                        // Poll toutes les 30 secondes
                        this._interval = setInterval(() => this.fetchNotifications(), 30000);
                    },

                    toggle() {
                        this.open = !this.open;
                        if (this.open) this.fetchNotifications();
                    },

                    fetchNotifications() {
                        if (this.notifications.length === 0) this.loading = true;
                        fetch('/web-api/notifications', {
                            credentials: 'same-origin',
                            headers: { 'Accept': 'application/json' }
                        })
                        .then(r => r.json())
                        .then(data => {
                            this.notifications = Array.isArray(data) ? data : [];
                            this.count = this.notifications.filter(n => n.read_at === null).length;
                        })
                        .catch(() => {})
                        .finally(() => { this.loading = false; });
                    },

                    handleClick(n) {
                        // Marquer comme lu
                        if (!n.read_at) {
                            fetch(`/web-api/notifications/${n.id}/read`, {
                                method: 'POST',
                                credentials: 'same-origin',
                                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
                            });
                            n.read_at = new Date().toISOString();
                            this.count = Math.max(0, this.count - 1);
                        }
                        // Naviguer vers la page concernée
                        this.open = false;
                        const action = n.data?.action || '/tasks';
                        window.location.href = action;
                    },

                    markAllRead() {
                        fetch('/web-api/notifications/mark-read', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
                        });
                        this.notifications.forEach(n => { if (!n.read_at) n.read_at = new Date().toISOString(); });
                        this.count = 0;
                    },

                    typeEmoji(type) {
                        return { success: '✅', info: 'ℹ️', warning: '⚠️', error: '❌' }[type] || '🔔';
                    },

                    typeColor(type, opacity) {
                        const colors = { success: '22,163,74', info: '59,130,246', warning: '217,119,6', error: '225,29,72' };
                        const c = colors[type] || '107,114,128';
                        return `rgba(${c},${opacity})`;
                    },

                    timeAgo(dateStr) {
                        if (!dateStr) return '';
                        const now = new Date();
                        const d = new Date(dateStr);
                        const diffS = Math.floor((now - d) / 1000);
                        if (diffS < 60)   return "à l'instant";
                        if (diffS < 3600) return Math.floor(diffS / 60) + ' min';
                        if (diffS < 86400) return Math.floor(diffS / 3600) + ' h';
                        const days = Math.floor(diffS / 86400);
                        if (days === 1) return 'hier';
                        if (days < 7) return days + ' j';
                        return d.toLocaleDateString('fr-FR', { day:'numeric', month:'short' });
                    }
                };
            }
        </script>
    </body>
</html>
