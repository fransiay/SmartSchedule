<x-app-layout>
    <x-slot name="header">
        <p class="page-title">Calendrier</p>
        <p class="page-subtitle">Visualisez votre planning optimisé en vue Jour, Semaine ou Mois.</p>
    </x-slot>

    <div class="pb-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Info banner -->
            <div id="calendarBanner"
                style="display:none;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:12px 18px;margin-bottom:16px;font-size:0.85rem;color:#166534;font-weight:500;">
                Planning chargé — <span id="calendarBannerCount"></span> créneau(x) affiché(s).
            </div>
            <div id="calendarError"
                style="display:none;background:#fff1f2;border:1px solid #fecdd3;border-radius:10px;padding:12px 18px;margin-bottom:16px;font-size:0.85rem;color:#e11d48;font-weight:500;">
            </div>

            <div class="legend-container">
                <div class="legend-item">
                    <div class="legend-color" style="background:#ef4444;"></div> P1 Urgent
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background:#f97316;"></div> P2 Élevé
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background:#eab308;"></div> P3 Moyen
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background:#3b82f6;"></div> P4 Normal
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background:#10b981;"></div> P5 Bas
                </div>
                <div class="legend-item" style="margin-left:auto;">
                    <div class="legend-color" style="background:rgba(15, 15, 16, 0.04);border:1px dashed #d1d1d6;">
                    </div> Disponibilité
                </div>
            </div>

            <div class="card" style="padding:12px; overflow: hidden;">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>

    <style>
        /* FullCalendar — light/grey override */
        :root {
            --fc-border-color: #e5e5e7;
            --fc-daygrid-event-dot-width: 8px;
            --fc-event-bg-color: #ffffff;
            --fc-event-border-color: #e5e5e7;
            --fc-event-text-color: #0f0f10;
            --fc-page-bg-color: transparent;
            --fc-neutral-bg-color: #f7f7f8;
            --fc-list-event-hover-bg-color: #f3f3f4;
            --fc-today-bg-color: rgba(15, 15, 16, 0.02);
            --fc-highlight-color: rgba(15, 15, 16, 0.06);
            --fc-now-indicator-color: #ef4444;
        }

        .fc {
            color: #0f0f10;
            font-family: 'Inter', sans-serif;
        }

        .fc-toolbar-title {
            font-size: 1.25rem !important;
            font-weight: 800 !important;
            color: #0f0f10 !important;
            letter-spacing: -0.02em;
        }

        .fc-button-primary {
            background: #ffffff !important;
            border: 1px solid #e5e5e7 !important;
            color: #3a3a3c !important;
            font-weight: 600 !important;
            font-size: 0.82rem !important;
            border-radius: 10px !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            padding: 8px 16px !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
        }

        .fc-button-primary:hover {
            background: #f7f7f8 !important;
            border-color: #d1d1d6 !important;
            color: #0f0f10 !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
        }

        .fc-button-primary:not(:disabled).fc-button-active,
        .fc-button-primary:not(:disabled):active {
            background: #0f0f10 !important;
            border-color: #0f0f10 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;
        }

        .fc-col-header-cell {
            background: #f7f7f8 !important;
            border-color: #e5e5e7 !important;
        }

        .fc-col-header-cell-cushion {
            color: #6c6c70 !important;
            font-size: 0.75rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.07em !important;
            padding: 12px 6px !important;
            text-decoration: none !important;
        }

        .fc-timegrid-slot-label {
            color: #aeaeb2 !important;
            font-size: 0.75rem !important;
            font-weight: 500;
        }

        .fc-timegrid-axis {
            color: #d1d1d6 !important;
        }

        .fc-event {
            border-radius: 6px !important;
            padding: 4px 6px !important;
            border: none !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
            transition: all 0.2s ease !important;
            cursor: pointer !important;
            display: block !important;
        }

        .fc-event:hover {
            transform: scale(1.02) translateY(-1px) !important;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12) !important;
            z-index: 50 !important;
        }

        .fc-event-main {
            padding: 2px 4px !important;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .fc-event-title {
            font-weight: 700 !important;
            font-size: 0.78rem !important;
            color: inherit;
            line-height: 1.1 !important;
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            word-break: break-word;
        }

        /* Ensure time is always visible */
        .fc-event-time {
            font-weight: 700 !important;
            font-size: 0.65rem !important;
            color: inherit;
            opacity: 0.9;
            line-height: 1;
            margin-bottom: 2px;
        }

        .fc-timegrid-event-short .fc-event-time {
            display: block !important;
        }

        .fc-daygrid-day-number {
            color: #6c6c70 !important;
            font-size: 0.85rem !important;
            font-weight: 600;
            padding: 8px !important;
            text-decoration: none !important;
        }

        .fc-day-today .fc-daygrid-day-number {
            color: #0f0f10 !important;
            font-weight: 800 !important;
        }

        .fc-day-today {
            background: rgba(15, 15, 16, 0.02) !important;
        }

        .fc-scrollgrid {
            border-radius: 12px;
            border-color: #e5e5e7 !important;
        }

        .fc-scrollgrid-sync-table td,
        .fc-scrollgrid-sync-table th {
            border-color: #f2f2f3 !important;
        }

        /* Fix Saturday clipping */
        .fc-view-harness {
            overflow: visible !important;
        }

        .fc-scroller-harness {
            overflow: visible !important;
        }

        .fc-timegrid-now-indicator-line {
            border-color: #ef4444 !important;
            border-width: 2px !important;
        }

        .fc-timegrid-now-indicator-arrow {
            border-top-color: #ef4444 !important;
            border-bottom-color: #ef4444 !important;
        }

        .fc-toolbar {
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px !important;
        }

        .fc-toolbar-chunk {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .fc-button-group {
            gap: 2px;
            display: flex;
            background: #f3f3f4;
            padding: 2px;
            border-radius: 12px;
        }

        .fc-button-group .fc-button {
            border: none !important;
        }

        /* Priority Legend */
        .legend-container {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 20px;
            padding: 12px 20px;
            background: #ffffff;
            border: 1px solid #e5e5e7;
            border-radius: 12px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.78rem;
            font-weight: 600;
            color: #6c6c70;
        }

        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 3px;
        }

        /* Event popup */
        #eventPopup {
            position: fixed;
            background: #ffffff;
            border: 1px solid #e5e5e7;
            border-radius: 16px;
            padding: 0;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
            z-index: 9999;
            min-width: 260px;
            max-width: 320px;
            display: none;
            overflow: hidden;
            animation: popupFade 0.2s ease;
        }

        @keyframes popupFade {
            from {
                opacity: 0;
                transform: translateY(10px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .popup-header {
            padding: 16px 20px;
            border-bottom: 1px solid #f2f2f3;
            position: relative;
        }

        .popup-body {
            padding: 16px 20px;
        }

        .priority-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 6px;
        }
    </style>

    <!-- Event popup DOM -->
    <div id="eventPopup">
        <button onclick="document.getElementById('eventPopup').style.display='none'"
            style="position:absolute;top:12px;right:12px;background:#f3f3f4;border:none;width:24px;height:24px;border-radius:50%;cursor:pointer;color:#6c6c70;font-size:0.8rem;display:flex;align-items:center;justify-content:center;z-index:10;">✕</button>
        <div class="popup-header" id="popupHeader">
            <div id="eventPopupTitle"
                style="font-weight:800;color:#0f0f10;font-size:1rem;margin-bottom:4px;padding-right:20px;line-height:1.3;">
            </div>
            <div id="eventPopupPriority"
                style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.05em;font-weight:700;"></div>
        </div>
        <div class="popup-body">
            <div id="eventPopupTime"
                style="font-size:0.85rem;color:#3a3a3c;margin-bottom:8px;display:flex;align-items:center;gap:8px;">
            </div>
            <div id="eventPopupDuration"
                style="font-size:0.85rem;color:#6c6c70;display:flex;align-items:center;gap:8px;"></div>
            <div id="eventPopupDesc"
                style="font-size:0.82rem;color:#aeaeb2;margin-top:12px;padding-top:12px;border-top:1px solid #f2f2f3;display:none;">
            </div>
        </div>
    </div>

    <script>
        const CSRF_CAL = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');
            const banner = document.getElementById('calendarBanner');
            const bannerCnt = document.getElementById('calendarBannerCount');
            const errorEl = document.getElementById('calendarError');
            const popup = document.getElementById('eventPopup');

            // Close popup when clicking outside
            document.addEventListener('click', function (e) {
                if (!popup.contains(e.target) && !e.target.closest('.fc-event')) {
                    popup.style.display = 'none';
                }
            });

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                locale: 'fr',
                height: 'auto',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'timeGridWeek,timeGridDay,dayGridMonth'
                },
                buttonText: { today: "Aujourd'hui", month: 'Mois', week: 'Semaine', day: 'Jour' },
                allDaySlot: false,
                scrollTime: '08:00:00',
                slotMinTime: '06:00:00',
                slotMaxTime: '24:00:00',
                slotDuration: '00:30:00',
                nowIndicator: true,
                eventClick: function (info) {
                    info.jsEvent.stopPropagation();
                    const task = info.event.extendedProps.task;
                    const start = info.event.start?.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
                    const end = info.event.end?.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });

                    const priorityColors = {
                        1: { text: 'Urgent', color: '#ef4444', bg: '#fef2f2' },
                        2: { text: 'Élevé', color: '#f97316', bg: '#fff7ed' },
                        3: { text: 'Moyen', color: '#eab308', bg: '#fefce8' },
                        4: { text: 'Normal', color: '#3b82f6', bg: '#eff6ff' },
                        5: { text: 'Bas', color: '#10b981', bg: '#f0fdf4' }
                    };

                    const p = priorityColors[task?.priority || 3];

                    document.getElementById('eventPopupTitle').textContent = info.event.title;
                    document.getElementById('eventPopupPriority').innerHTML = `<span class="priority-dot" style="background:${p.color}"></span> Priorité ${task?.priority || 3} : ${p.text}`;
                    document.getElementById('eventPopupPriority').style.color = p.color;
                    document.getElementById('popupHeader').style.background = p.bg;

                    document.getElementById('eventPopupTime').innerHTML = `<span>🕒</span> ${start} — ${end}`;
                    document.getElementById('eventPopupDuration').innerHTML = task?.duration_minutes ? `<span>⏳</span> Durée : ${task.duration_minutes} min` : '';

                    const descEl = document.getElementById('eventPopupDesc');
                    if (task?.description) {
                        descEl.textContent = task.description;
                        descEl.style.display = 'block';
                    } else {
                        descEl.style.display = 'none';
                    }

                    const rect = info.el.getBoundingClientRect();
                    popup.style.display = 'block';

                    // Position popup
                    let top = rect.bottom + window.scrollY + 8;
                    let left = rect.left + window.scrollX;

                    // Prevent overflow
                    if (left + 320 > window.innerWidth) left = window.innerWidth - 340;
                    if (top + 200 > window.innerHeight + window.scrollY) top = rect.top + window.scrollY - 210;

                    popup.style.top = top + 'px';
                    popup.style.left = Math.max(20, left) + 'px';
                },
                events: function (info, successCallback, failureCallback) {
                    errorEl.style.display = 'none';

                    Promise.all([
                        fetch('/web-api/schedule', {
                            credentials: 'same-origin',
                            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_CAL }
                        }).then(response => {
                            if (!response.ok) throw new Error('Erreur HTTP API Schedule ' + response.status);
                            return response.json();
                        }),
                        fetch('/web-api/availabilities', {
                            credentials: 'same-origin',
                            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_CAL }
                        }).then(response => {
                            if (!response.ok) throw new Error('Erreur HTTP API Availabilities ' + response.status);
                            return response.json();
                        })
                    ])
                        .then(([schedulesData, availabilitiesData]) => {
                            let allEvents = [];

                            // Map Availabilities to Background Events
                            if (Array.isArray(availabilitiesData)) {
                                availabilitiesData.forEach(av => {
                                    if (av.is_active && av.day_of_week !== null && av.day_of_week !== undefined) {
                                        allEvents.push({
                                            groupId: 'availableForWork',
                                            daysOfWeek: [av.day_of_week],
                                            startTime: av.start_time,
                                            endTime: av.end_time,
                                            display: 'background',
                                            color: 'rgba(15, 15, 16, 0.04)' // subtle shading for available blocks
                                        });
                                    }
                                });
                            }

                            // Map Scheduled Tasks to Normal Events
                            let taskEvents = [];
                            if (Array.isArray(schedulesData) && schedulesData.length > 0) {
                                const priorityStyles = {
                                    1: { bg: '#ef4444', border: '#ef4444', text: '#ffffff' }, // Urgent: Solid Red
                                    2: { bg: '#f97316', border: '#f97316', text: '#ffffff' }, // High: Solid Orange
                                    3: { bg: '#eab308', border: '#eab308', text: '#0f0f10' }, // Medium: Solid Yellow (black text)
                                    4: { bg: '#3b82f6', border: '#3b82f6', text: '#ffffff' }, // Normal: Solid Blue
                                    5: { bg: '#10b981', border: '#10b981', text: '#ffffff' }  // Low: Solid Green
                                };

                                taskEvents = schedulesData
                                    .filter(s => s.start_time && s.end_time)
                                    .map(schedule => {
                                        const priority = schedule.task?.priority || 3;
                                        const style = priorityStyles[priority] || priorityStyles[3];
                                        return {
                                            id: schedule.id,
                                            title: schedule.task?.title || 'Tâche',
                                            start: schedule.start_time,
                                            end: schedule.end_time,
                                            backgroundColor: style.bg,
                                            borderColor: style.border,
                                            textColor: style.text,
                                            extendedProps: { task: schedule.task }
                                        };
                                    });
                                allEvents = allEvents.concat(taskEvents);

                                banner.style.display = 'block';
                                bannerCnt.textContent = taskEvents.length;
                            } else {
                                banner.style.display = 'none';
                            }

                            successCallback(allEvents);

                            // Auto focus on the first generated event date
                            if (taskEvents.length > 0) {
                                calendar.gotoDate(taskEvents[0].start);
                            }
                        })
                        .catch(error => {
                            console.error(error);
                            errorEl.style.display = 'block';
                            errorEl.textContent = '⚠️ Impossible de charger le calendrier : ' + error.message + '. Assurez-vous d\'être connecté.';
                            failureCallback(error);
                        });
                }

            });
            calendar.render();
        });
    </script>
</x-app-layout>