@extends('layouts.app')

@section('title', 'My Calendar - Doctor Panel')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 pb-6 sm:py-12 pt-20">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 sm:mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">My Appointment Calendar</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">View and manage your scheduled patient appointments</p>
        </div>

    </div>

    <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Loading Overlay – matching admin style -->
        <div id="calendar-loading" class="absolute inset-0 bg-white dark:bg-gray-800/95 backdrop-blur-sm flex items-center justify-center z-10 hidden">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 border-4 border-gray-300 dark:border-gray-600 border-t-indigo-600 dark:border-t-indigo-500 rounded-full animate-spin"></div>
                <p class="mt-4 text-sm font-medium text-gray-700 dark:text-gray-300">Loading your appointments...</p>
            </div>
        </div>

        <div class="p-4 sm:p-6 bg-gray-50 dark:bg-gray-900/50">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Dependencies -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<!-- Styles – matched to your admin calendar -->
<style>
    #calendar {
        max-width: 100%;
        margin: 0 auto;
        font-size: 0.9375rem;
        background: transparent;
    }

    @media (max-width: 640px) {
        .fc .fc-toolbar.fc-header-toolbar {
            flex-direction: column;
            gap: 12px;
        }
        .fc .fc-toolbar-title {
            font-size: 1.25rem !important;
        }
    }

    .dark .fc {
        --fc-border-color: #374151;
        --fc-daygrid-event-dot-opacity: 1;
        --fc-bg-event-opacity: 0.95;
        --fc-today-bg-color: rgba(99, 102, 241, 0.15);
        color: #e5e7eb;
        background-color: #111827;
    }

    .dark .fc .fc-col-header-cell,
    .dark .fc .fc-daygrid-day-top,
    .dark .fc .fc-timegrid-axis,
    .dark .fc .fc-timegrid-slot-label,
    .dark .fc .fc-scrollgrid-sync-table {
        background: #1f2937 !important;
        border-color: #374151 !important;
    }

    .dark .fc .fc-daygrid-day-number,
    .dark .fc .fc-timegrid-slot-label,
    .dark .fc .fc-toolbar-title,
    .dark .fc .fc-timegrid-axis-cushion {
        color: #d1d5db !important;
    }
</style>

<script>
// Doctor Personal Calendar – matched layout & behavior to admin version
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const loadingOverlay = document.getElementById('calendar-loading');
    let picker = null;

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'datePickerButton dayGridMonth,timeGridDay'
        },
        customButtons: {
            datePickerButton: {
                text: 'Pick Date',
                click: function () {
                    if (!picker) {
                        const button = document.querySelector('.fc-datePickerButton-button');
                        picker = flatpickr(button, {
                            inline: false,
                            dateFormat: "Y-m-d",
                            defaultDate: calendar.getDate(),
                            theme: document.documentElement.classList.contains('dark') ? "dark" : "light",
                            appendTo: document.body,
                            onChange: function(selectedDates, dateStr) {
                                calendar.gotoDate(dateStr);
                            },
                            onClose: function() {
                                document.activeElement.blur();
                            }
                        });
                    }
                    picker.open();
                }
            }
        },
        height: 'auto',
        slotDuration: '00:15:00',
        slotMinTime: '06:00:00',
        slotMaxTime: '21:00:00',
        timeZone: 'local',
        displayEventTime: true,
        eventTimeFormat: { hour: 'numeric', minute: '2-digit', meridiem: 'short' },

        events: function(fetchInfo, successCallback, failureCallback) {
            const params = new URLSearchParams({
                start: fetchInfo.start.toISOString(),
                end: fetchInfo.end.toISOString()
            });

            fetch('{{ route("doctor-panel.calendar.events") }}?' + params.toString())
                .then(r => r.ok ? r.json() : Promise.reject(r))
                .then(data => successCallback(data))
                .catch(err => {
                    console.error('Events fetch error:', err);
                    failureCallback(err);
                });
        },

        loading: function(isLoading) {
            loadingOverlay.classList.toggle('hidden', !isLoading);
        },

        eventContent: function(arg) {
            const p = arg.event.extendedProps;
            const timeText = arg.timeText || '';

            const bgColor = arg.event.backgroundColor || '#3b82f6';
            const textColor = arg.event.textColor || '#ffffff';

            const commonHtml = `
                <div class="fc-event-title fc-sticky text-xs leading-tight p-1 rounded"
                     style="background-color: ${bgColor}; color: ${textColor};">
                    <div class="font-semibold text-xs opacity-90">${timeText}</div>
                    <div class="font-medium truncate">${p.patient}</div>
                    ${p.status ? `<div class="text-xs font-medium mt-0.5">${p.status}</div>` : ''}
                </div>
            `;

            if (arg.view.type === 'dayGridMonth') {
                return { html: commonHtml };
            }

            return { html: `
                <div class="flex flex-col justify-center h-full px-2 py-1.5 text-left rounded"
                     style="background-color: ${bgColor}; color: ${textColor};">
                    <div class="text-sm leading-tight">${timeText ? timeText + ' - ' : ''}${p.patient}</div>
                    ${p.status ? `<div class="text-xs font-medium opacity-90">${p.status}</div>` : ''}
                </div>
            `};
        },

        eventDidMount: function(info) {
            const p = info.event.extendedProps;
            const duration = p.duration ? ` (${p.duration})` : '';
            const time = info.event.start
                ? new Date(info.event.start).toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' })
                : '';

            info.el.title = `${p.patient}\n${time}${duration}\nStatus: ${p.status || 'Scheduled'}`;
        }
    });

    calendar.render();
});
</script>
@endsection