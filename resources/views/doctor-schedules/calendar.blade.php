{{-- resources/views/doctor-schedules/calendar.blade.php --}}
@extends('layouts.app')

@section('title', 'Doctor Schedule Calendar')

@section('content')
<div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 sm:mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                Doctor Schedule Calendar
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                View recurring doctor availability and assigned rooms
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <div class="flex items-center gap-3">
                <label for="doctor_filter" class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                    Filter by Doctor:
                </label>
                <select id="doctor_filter"
                    class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2">
                    <option value="">All Doctors</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" {{ $doctorId == $doctor->id ? 'selected' : '' }}>
                            Dr. {{ $doctor->getFullNameAttribute() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <a href="{{ route('doctor-schedules.create') }}"
               class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors duration-200 shadow-sm whitespace-nowrap">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Schedule
            </a>
        </div>
    </div>

    <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div id="calendar-loading" class="absolute inset-0 bg-white dark:bg-gray-800/95 backdrop-blur-sm flex items-center justify-center z-10 hidden">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 border-4 border-gray-300 dark:border-gray-600 border-t-indigo-600 dark:border-t-indigo-500 rounded-full animate-spin"></div>
                <p class="mt-4 text-sm font-medium text-gray-700 dark:text-gray-300">Loading schedules...</p>
            </div>
        </div>

        <div class="p-4 sm:p-6 bg-gray-50 dark:bg-gray-900/50">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<style>
    #calendar { max-width: 100%; margin: 0 auto; font-size: 0.9375rem; background: transparent; }
    @media (max-width: 640px) {
        .fc .fc-toolbar.fc-header-toolbar { flex-direction: column; gap: 12px; }
        .fc .fc-toolbar-title { font-size: 1.25rem !important; }
    }
    .dark .fc { --fc-border-color: #374151; --fc-daygrid-event-dot-opacity: 1; --fc-bg-event-opacity: 0.95; --fc-today-bg-color: rgba(99, 102, 241, 0.15); color: #e5e7eb; background-color: #111827; }
    .dark .fc .fc-col-header-cell,
    .dark .fc .fc-daygrid-day-top,
    .dark .fc .fc-timegrid-axis,
    .dark .fc .fc-timegrid-slot-label,
    .dark .fc .fc-scrollgrid-sync-table { background: #1f2937 !important; border-color: #374151 !important; }
    .dark .fc .fc-daygrid-day-number,
    .dark .fc .fc-timegrid-slot-label,
    .dark .fc .fc-toolbar-title,
    .dark .fc .fc-timegrid-axis-cushion { color: #d1d5db !important; }

    /* Hide weekday names (Mon, Tue, etc.) in month view */
    .fc-view-harness.fc-dayGridMonth .fc-col-header-cell-cushion {
        display: none !important;
    }
    .fc-view-harness.fc-dayGridMonth .fc-col-header-cell {
        height: 0 !important;
        padding: 0 !important;
        border-bottom: none !important;
        overflow: hidden;
    }
    /* Give day numbers some breathing room */
    .fc .fc-daygrid-day-top {
        padding-top: 6px;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const doctorFilter = document.getElementById('doctor_filter');
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

        // Critical for timed recurring events to appear in timeGridDay view
        slotDuration: '00:15:00',
        slotMinTime: '06:00:00',
        slotMaxTime: '22:00:00',
        allDaySlot: false,
        displayEventTime: true,

        events: function(fetchInfo, successCallback, failureCallback) {
    const params = new URLSearchParams({
        start: fetchInfo.start.toISOString(),
        end: fetchInfo.end.toISOString(),
        doctor_id: doctorFilter.value || ''
    });

    console.log('Fetching events for range:', fetchInfo.start.toISOString(), 'to', fetchInfo.end.toISOString());
    console.log('Full URL:', '{{ route("doctor-schedules.calendar-events") }}?' + params);

    fetch('{{ route("doctor-schedules.calendar-events") }}?' + params)
        .then(r => r.ok ? r.json() : Promise.reject(r))
        .then(data => {
            console.log('Received events data:', data);
            successCallback(data);
        })
        .catch(err => {
            console.error('Error loading schedules:', err);
            failureCallback(err);
        });
},

        loading: function(isLoading) {
            loadingOverlay.classList.toggle('hidden', !isLoading);
        },

        eventContent: function(arg) {
            const p = arg.event.extendedProps;

            if (arg.view.type === 'dayGridMonth') {
                // Month view: show full time range + doctor + room
                return { html: `
                    <div class="fc-event-title fc-sticky text-xs leading-tight p-1">
                        <div class="font-semibold text-xs opacity-90">${p.time || ''}</div>
                        <div class="font-medium truncate">Dr. ${p.doctor}</div>
                        <div class="text-xs opacity-80">Room: ${p.room}</div>
                    </div>
                `};
            }

            // timeGridDay view: clean timed block
            return { html: `
                <div class="text-white text-xs leading-tight px-2 py-1.5">
                    <div class="font-semibold">${p.time}</div>
                    <div class="opacity-90 text-xs">Dr. ${p.doctor}</div>
                    <div class="text-xs">Room: ${p.room}</div>
                </div>
            `};
        },

        eventDidMount: function(info) {
            const p = info.event.extendedProps;
            info.el.title = `Dr. ${p.doctor}\nRoom: ${p.room}\nTime: ${p.time}\nDays: ${p.days}\nActive: ${p.is_active ? 'Yes' : 'No'}`;
        }
    });

    calendar.render();

    doctorFilter.addEventListener('change', () => calendar.refetchEvents());
});
</script>
@endsection