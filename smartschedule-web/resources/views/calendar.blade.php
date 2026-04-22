<x-app-layout>
    <x-slot name="header">
        <p class="page-title">Calendrier</p>
        <p class="page-subtitle">Visualisez votre planning optimisé en vue Jour, Semaine ou Mois.</p>
    </x-slot>

    <div class="pb-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Info banner -->
            <div id="calendarBanner" style="display:none;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:12px 18px;margin-bottom:16px;font-size:0.85rem;color:#166534;font-weight:500;">
                ✅ Planning chargé — <span id="calendarBannerCount"></span> créneau(x) affiché(s).
            </div>
            <div id="calendarError" style="display:none;background:#fff1f2;border:1px solid #fecdd3;border-radius:10px;padding:12px 18px;margin-bottom:16px;font-size:0.85rem;color:#e11d48;font-weight:500;"></div>

            <div class="card" style="padding:24px;overflow:hidden;">
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
            --fc-event-bg-color: #0f0f10;
            --fc-event-border-color: #0f0f10;
            --fc-event-text-color: #fff;
            --fc-page-bg-color: transparent;
            --fc-neutral-bg-color: #f7f7f8;
            --fc-list-event-hover-bg-color: #f3f3f4;
            --fc-today-bg-color: #f7f7f8;
            --fc-highlight-color: rgba(15,15,16,0.06);
            --fc-now-indicator-color: #0f0f10;
        }

        .fc { color: #0f0f10; font-family: 'Inter', sans-serif; }
        .fc-toolbar-title { font-size: 1.15rem !important; font-weight: 700 !important; color: #0f0f10 !important; }

        .fc-button-primary {
            background: #f3f3f4 !important;
            border: 1px solid #e5e5e7 !important;
            color: #3a3a3c !important;
            font-weight: 600 !important;
            font-size: 0.82rem !important;
            border-radius: 8px !important;
            transition: all 0.2s !important;
            padding: 6px 14px !important;
            box-shadow: none !important;
        }
        .fc-button-primary:hover {
            background: #e9e9eb !important;
            color: #0f0f10 !important;
        }
        .fc-button-primary:not(:disabled).fc-button-active,
        .fc-button-primary:not(:disabled):active {
            background: #0f0f10 !important;
            border-color: #0f0f10 !important;
            color: #ffffff !important;
            box-shadow: none !important;
        }
        .fc-col-header-cell { background: #f7f7f8 !important; border-color: #e5e5e7 !important; }
        .fc-col-header-cell-cushion {
            color: #6c6c70 !important;
            font-size: 0.75rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.07em !important;
            padding: 10px 6px !important;
            text-decoration: none !important;
        }
        .fc-timegrid-slot-label { color: #aeaeb2 !important; font-size: 0.75rem !important; }
        .fc-timegrid-axis { color: #d1d1d6 !important; }

        .fc-event {
            border-radius: 7px !important;
            padding: 3px 8px !important;
            background: #0f0f10 !important;
            border: none !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.12) !important;
            font-size: 0.78rem !important;
            font-weight: 600 !important;
            cursor: pointer !important;
        }
        .fc-event:hover { filter: brightness(1.3) !important; transform: translateY(-1px) !important; }
        .fc-event-title { color: #ffffff !important; }

        .fc-daygrid-day-number {
            color: #6c6c70 !important;
            font-size: 0.85rem !important;
            text-decoration: none !important;
        }
        .fc-day-today .fc-daygrid-day-number { color: #0f0f10 !important; font-weight: 800 !important; }
        .fc-day-today { background: #f7f7f8 !important; }

        .fc-scrollgrid { border-color: #e5e5e7 !important; }
        .fc-scrollgrid-sync-table td,
        .fc-scrollgrid-sync-table th { border-color: #f2f2f3 !important; }

        .fc-timegrid-now-indicator-line { border-color: #0f0f10 !important; border-width: 2px !important; }
        .fc-timegrid-now-indicator-arrow { border-top-color: #0f0f10 !important; border-bottom-color: #0f0f10 !important; }

        .fc-toolbar { flex-wrap: wrap; gap: 12px; margin-bottom: 20px !important; }
        .fc-toolbar-chunk { display: flex; gap: 6px; }
        .fc-button-group { gap: 4px; display: flex; }

        /* Event popup */
        #eventPopup {
            position: fixed;
            background: #ffffff;
            border: 1px solid #e5e5e7;
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 12px 40px rgba(0,0,0,0.12);
            z-index: 9999;
            min-width: 240px;
            max-width: 300px;
            display: none;
        }
    </style>

    <!-- Event popup DOM -->
    <div id="eventPopup">
        <button onclick="document.getElementById('eventPopup').style.display='none'"
                style="position:absolute;top:10px;right:12px;background:none;border:none;cursor:pointer;color:#aeaeb2;font-size:1rem;">✕</button>
        <div id="eventPopupTitle" style="font-weight:700;color:#0f0f10;font-size:0.95rem;margin-bottom:10px;padding-right:16px;"></div>
        <div id="eventPopupTime" style="font-size:0.82rem;color:#6c6c70;margin-bottom:6px;"></div>
        <div id="eventPopupDuration" style="font-size:0.82rem;color:#aeaeb2;"></div>
    </div>

    <script>
      const CSRF_CAL = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

      document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const banner     = document.getElementById('calendarBanner');
        const bannerCnt  = document.getElementById('calendarBannerCount');
        const errorEl    = document.getElementById('calendarError');
        const popup      = document.getElementById('eventPopup');

        // Close popup when clicking outside
        document.addEventListener('click', function(e) {
            if (!popup.contains(e.target) && !e.target.closest('.fc-event')) {
                popup.style.display = 'none';
            }
        });

        var calendar = new FullCalendar.Calendar(calendarEl, {
          initialView: 'timeGridWeek',
          locale: 'fr',
          height: 700,
          headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'timeGridWeek,timeGridDay,dayGridMonth'
          },
          buttonText: { today: "Aujourd'hui", month: 'Mois', week: 'Semaine', day: 'Jour' },
          allDaySlot: false,
          slotMinTime: '06:00:00',
          slotMaxTime: '23:00:00',
          slotDuration: '00:30:00',
          nowIndicator: true,
          eventClick: function(info) {
              info.jsEvent.stopPropagation();
              const task  = info.event.extendedProps.task;
              const start = info.event.start?.toLocaleTimeString('fr-FR', {hour:'2-digit', minute:'2-digit'});
              const end   = info.event.end?.toLocaleTimeString('fr-FR', {hour:'2-digit', minute:'2-digit'});

              document.getElementById('eventPopupTitle').textContent = info.event.title;
              document.getElementById('eventPopupTime').textContent  = `🕐 ${start} → ${end}`;
              document.getElementById('eventPopupDuration').textContent = task?.duration_minutes ? `⏳ Durée totale : ${task.duration_minutes} min` : '';

              const rect = info.el.getBoundingClientRect();
              popup.style.display = 'block';
              popup.style.top  = (rect.bottom + window.scrollY + 8) + 'px';
              popup.style.left = Math.min(rect.left + window.scrollX, window.innerWidth - 320) + 'px';
          },
          events: function(info, successCallback, failureCallback) {
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
                                  daysOfWeek: [ av.day_of_week ],
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
                      taskEvents = schedulesData
                          .filter(s => s.start_time && s.end_time)
                          .map(schedule => ({
                              id:    schedule.id,
                              title: schedule.task?.title || 'Tâche',
                              start: schedule.start_time,
                              end:   schedule.end_time,
                              extendedProps: { task: schedule.task }
                          }));
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
                  errorEl.textContent   = '⚠️ Impossible de charger le calendrier : ' + error.message + '. Assurez-vous d\'être connecté.';
                  failureCallback(error);
              });
          }
        });
        calendar.render();
      });
    </script>
</x-app-layout>
