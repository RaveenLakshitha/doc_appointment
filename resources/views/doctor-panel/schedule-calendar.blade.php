@extends('layouts.app')

@section('title', 'My Schedule - Doctor Panel')

@section('content')
    <div class="px-4 sm:px-6 lg:px-8 pb-6 sm:py-12 pt-20">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 sm:mb-8">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">My Schedule Calendar</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Your recurring availability, assigned rooms, and
                    active days</p>
            </div>
        </div>

        <div
            class="relative bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Loading Overlay – matching admin style -->
            <div id="calendar-loading"
                class="absolute inset-0 bg-white dark:bg-gray-800/95 backdrop-blur-sm flex items-center justify-center z-10 hidden">
                <div class="text-center">
                    <div
                        class="inline-flex items-center justify-center w-12 h-12 border-4 border-gray-300 dark:border-gray-600 border-t-indigo-600 dark:border-t-indigo-500 rounded-full animate-spin">
                    </div>
                    <p class="mt-4 text-sm font-medium text-gray-700 dark:text-gray-300">Loading your schedule...</p>
                </div>
            </div>

            <div class="p-4 sm:p-6 bg-gray-50 dark:bg-gray-900/50">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <!-- Dependencies – same as admin -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- Styles – matched to admin calendar -->
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
            --fc-today-bg-color: rgba(99, 102, 241, 0.18);
            --fc-highlight-color: rgba(99, 102, 241, 0.25);
            --fc-page-bg-color: #111827;
            --fc-neutral-bg-color: #1f2937;
            color: #e5e7eb;
            background-color: #111827;
        }

        .dark .fc .fc-col-header-cell {
            background: #1f2937 !important;
            border-color: #374151 !important;
        }

        .dark .fc .fc-col-header-cell-cushion {
            color: #d1d5db !important;
            font-weight: 500;
        }

        .dark .fc .fc-daygrid-day-top {
            background: transparent !important;
            color: #9ca3af !important;
            padding-top: 8px;
            padding-bottom: 4px;
        }

        .dark .fc .fc-daygrid-day-number {
            color: #9ca3af !important;
            opacity: 1 !important;
        }

        .dark .fc .fc-timegrid-axis,
        .dark .fc .fc-timegrid-slot-label,
        .dark .fc .fc-scrollgrid-sync-table {
            background: transparent !important;
            border-color: #374151 !important;
        }

        .dark .fc .fc-daygrid-day.fc-day-today {
            background-color: var(--fc-today-bg-color) !important;
        }

        .fc .fc-highlight,
        .fc .fc-daygrid-day.fc-day-selected,
        .fc .fc-timegrid-col.fc-day-selected,
        .fc .fc-daygrid-day:active,
        .fc .fc-daygrid-day:focus,
        .fc .fc-daygrid-day:focus-visible {
            background-color: rgba(99, 102, 241, 0.25) !important;
            color: inherit !important;
            outline: none !important;
        }

        .fc .fc-daygrid-day,
        .fc .fc-timegrid-col {
            -webkit-tap-highlight-color: transparent !important;
        }

        @media (pointer: coarse) {

            .fc .fc-daygrid-day:active,
            .fc .fc-daygrid-day:focus,
            .fc .fc-highlight {
                background: rgba(99, 102, 241, 0.22) !important;
                transition: background 0.08s ease-out;
            }
        }

        .dark .fc .fc-daygrid-day:hover {
            background-color: rgba(55, 65, 81, 0.4) !important;
        }

        /* Hide weekday names in month view */
        .fc-view-harness.fc-dayGridMonth .fc-col-header-cell-cushion {
            display: none !important;
        }

        .fc-view-harness.fc-dayGridMonth .fc-col-header-cell {
            height: 0 !important;
            padding: 0 !important;
            border-bottom: none !important;
            overflow: hidden;
        }

        .fc .fc-daygrid-day-top {
            padding-top: 6px;
        }
    </style>

    <script>
        // Doctor's Personal Schedule Calendar
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');
            const loadingOverlay = document.getElementById('calendar-loading');
            let picker = null;

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: window.innerWidth < 640 ? 'listWeek' : 'dayGridMonth',
                headerToolbar: window.innerWidth < 640 ? {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'datePickerButton'
                } : {
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
                                    onChange: function (selectedDates, dateStr) {
                                        calendar.gotoDate(dateStr);
                                    },
                                    onClose: function () {
                                        document.activeElement.blur();
                                    }
                                });
                            }
                            picker.open();
                        }
                    }
                },
                windowResize: function (arg) {
                    if (window.innerWidth < 640) {
                        calendar.changeView('listWeek');
                        calendar.setOption('headerToolbar', {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'datePickerButton'
                        });
                    } else {
                        calendar.changeView('dayGridMonth');
                        calendar.setOption('headerToolbar', {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'datePickerButton dayGridMonth,timeGridDay'
                        });
                    }
                },
                height: 'auto',
                slotDuration: '00:15:00',
                slotMinTime: '06:00:00',
                slotMaxTime: '22:00:00',
                allDaySlot: false,
                displayEventTime: true,
                eventTimeFormat: { hour: 'numeric', minute: '2-digit', meridiem: 'short' },

                events: function (fetchInfo, successCallback, failureCallback) {
                    const params = new URLSearchParams({
                        start: fetchInfo.start.toISOString(),
                        end: fetchInfo.end.toISOString()
                    });

                    fetch('{{ route("doctor-panel.schedule-events") }}?' + params.toString())
                        .then(r => r.ok ? r.json() : Promise.reject(r))
                        .then(data => {
                            console.log('Doctor personal schedule events loaded:', data);
                            successCallback(data);
                        })
                        .catch(err => {
                            console.error('Error loading personal schedule:', err);
                            failureCallback(err);
                        });
                },

                loading: function (isLoading) {
                    loadingOverlay.classList.toggle('hidden', !isLoading);
                },

                eventContent: function (arg) {
                    const p = arg.event.extendedProps;

                    if (arg.view.type === 'dayGridMonth') {
                        // Month view: compact block
                        return {
                            html: `
                                    <div class="fc-event-title fc-sticky text-xs leading-tight p-1 rounded">
                                        <div class="font-semibold text-xs opacity-90">${p.time || ''}</div>
                                        <div class="font-medium truncate">Room: ${p.room}</div>
                                    </div>
                                `};
                    }

                    // timeGridDay / timeGridWeek: full timed block
                    return {
                        html: `
                                <div class="text-white text-xs leading-tight px-2 py-1.5 h-full flex flex-col justify-center">
                                    <div class="font-semibold">${p.time}</div>
                                    <div class="opacity-90 text-xs">Room: ${p.room}</div>
                                </div>
                            `};
                },

                eventDidMount: function (info) {
                    const p = info.event.extendedProps;
                    info.el.title = `Room: ${p.room}\nTime: ${p.time}\nDays: ${p.days}\nActive: ${p.is_active ? 'Yes' : 'No'}`;
                }
            });

            calendar.render();
        });
    </script>
@endsection