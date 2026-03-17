@extends('layouts.pos')

@section('title', 'Appointment Management')

@push('styles')
    <link
        href="https://fonts.googleapis.com/css2?family=Geist:wght@300;400;500;600;700&family=Geist+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Geist', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        mono: ['Geist Mono', 'ui-monospace', 'monospace'],
                    },
                    colors: {
                        border: 'hsl(var(--border))',
                        input: 'hsl(var(--input))',
                        ring: 'hsl(var(--ring))',
                        background: 'hsl(var(--background))',
                        foreground: 'hsl(var(--foreground))',
                        primary: {
                            DEFAULT: 'hsl(var(--primary))',
                            foreground: 'hsl(var(--primary-foreground))',
                        },
                        secondary: {
                            DEFAULT: 'hsl(var(--secondary))',
                            foreground: 'hsl(var(--secondary-foreground))',
                        },
                        muted: {
                            DEFAULT: 'hsl(var(--muted))',
                            foreground: 'hsl(var(--muted-foreground))',
                        },
                        accent: {
                            DEFAULT: 'hsl(var(--accent))',
                            foreground: 'hsl(var(--accent-foreground))',
                        },
                        destructive: {
                            DEFAULT: 'hsl(var(--destructive))',
                            foreground: 'hsl(var(--destructive-foreground))',
                        },
                        card: {
                            DEFAULT: 'hsl(var(--card))',
                            foreground: 'hsl(var(--card-foreground))',
                        },
                        // status colours
                        status: {
                            amber: 'hsl(38 92% 50%)',
                            blue: 'hsl(221 83% 53%)',
                            purple: 'hsl(271 81% 56%)',
                            green: 'hsl(142 71% 45%)',
                            red: 'hsl(0 84% 60%)',
                            sky: 'hsl(199 89% 48%)',
                            teal: 'hsl(173 80% 40%)',
                            rose: 'hsl(346 87% 57%)',
                        },
                    },
                    borderRadius: {
                        DEFAULT: '0.5rem',
                        sm: 'calc(0.5rem - 2px)',
                        md: 'calc(0.5rem + 2px)',
                        full: '9999px',
                    },
                    keyframes: {
                        fadeIn: { from: { opacity: '0', transform: 'translateY(5px)' }, to: { opacity: '1', transform: 'translateY(0)' } },
                        popIn: { from: { transform: 'scale(0.85)', opacity: '0' }, to: { transform: 'scale(1)', opacity: '1' } },
                        slideInRight: { from: { transform: 'translateX(14px)', opacity: '0' }, to: { transform: 'translateX(0)', opacity: '1' } },
                        pulse2: { '0%,100%': { opacity: '1' }, '50%': { opacity: '0.4' } },
                    },
                    animation: {
                        fadeIn: 'fadeIn 0.2s ease',
                        popIn: 'popIn 0.15s ease',
                        slideInRight: 'slideInRight 0.2s ease',
                        pulse2: 'pulse2 2s infinite',
                    },
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer base {
                                                                                                                                                                                                                                                            :root {
                                                                                                                                                                                                                                                                --background: 210 40% 98%;
                                                                                                                                                                                                                                                                --foreground: 222.2 84% 4.9%;
                                                                                                                                                                                                                                                                --card: 0 0% 100%;
                                                                                                                                                                                                                                                                --card-foreground: 222.2 84% 4.9%;
                                                                                                                                                                                                                                                                --popover: 0 0% 100%;
                                                                                                                                                                                                                                                                --popover-foreground: 222.2 84% 4.9%;
                                                                                                                                                                                                                                                                --primary: 226 70% 55.5%; /* indigo-600 */
                                                                                                                                                                                                                                                                --primary-foreground: 210 40% 98%;
                                                                                                                                                                                                                                                                --secondary: 210 40% 96.1%;
                                                                                                                                                                                                                                                                --secondary-foreground: 222.2 47.4% 11.2%;
                                                                                                                                                                                                                                                                --muted: 210 40% 96.1%;
                                                                                                                                                                                                                                                                --muted-foreground: 215.4 16.3% 46.9%;
                                                                                                                                                                                                                                                                --accent: 210 40% 96.1%;
                                                                                                                                                                                                                                                                --accent-foreground: 222.2 47.4% 11.2%;
                                                                                                                                                                                                                                                                --destructive: 0 84.2% 60.2%;
                                                                                                                                                                                                                                                                --destructive-foreground: 210 40% 98%;
                                                                                                                                                                                                                                                                --border: 214.3 31.8% 91.4%;
                                                                                                                                                                                                                                                                --input: 214.3 31.8% 91.4%;
                                                                                                                                                                                                                                                                --ring: 226 70% 55.5%;
                                                                                                                                                                                                                                                            }

                                                                                                                                                                                                                                                            .dark {
                                                                                                                                                                                                                                                                --background: 222.2 84% 4.9%; /* gray-900 or similar */
                                                                                                                                                                                                                                                                --foreground: 210 40% 98%;
                                                                                                                                                                                                                                                                --card: 217.2 32.6% 17.5%; /* gray-800 or similar */
                                                                                                                                                                                                                                                                --card-foreground: 210 40% 98%;
                                                                                                                                                                                                                                                                --popover: 222.2 84% 4.9%;
                                                                                                                                                                                                                                                                --popover-foreground: 210 40% 98%;
                                                                                                                                                                                                                                                                --primary: 226 70% 55.5%;
                                                                                                                                                                                                                                                                --primary-foreground: 210 40% 98%;
                                                                                                                                                                                                                                                                --secondary: 217.2 32.6% 17.5%;
                                                                                                                                                                                                                                                                --secondary-foreground: 210 40% 98%;
                                                                                                                                                                                                                                                                --muted: 217.2 32.6% 17.5%;
                                                                                                                                                                                                                                                                --muted-foreground: 215 20.2% 65.1%;
                                                                                                                                                                                                                                                                --accent: 217.2 32.6% 17.5%;
                                                                                                                                                                                                                                                                --accent-foreground: 210 40% 98%;
                                                                                                                                                                                                                                                                --destructive: 0 62.8% 30.6%;
                                                                                                                                                                                                                                                                --destructive-foreground: 210 40% 98%;
                                                                                                                                                                                                                                                                --border: 217.2 32.6% 17.5%;
                                                                                                                                                                                                                                                                --input: 217.2 32.6% 17.5%;
                                                                                                                                                                                                                                                                --ring: 226 70% 55.5%;
                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                                        @layer utilities {
                                                                                                                                                                                                                                                            .scrollbar-thin::-webkit-scrollbar { width: 4px; height: 4px; }
                                                                                                                                                                                                                                                            .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
                                                                                                                                                                                                                                                            .scrollbar-thin::-webkit-scrollbar-thumb { @apply bg-border rounded-full; }
                                                                                                                                                                                                                                                            select { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24'%3E%3Cpath fill='%23888' d='M7 10l5 5 5-5z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 9px center; -webkit-appearance: none; appearance: none; }
                                                                                                                                                                                                                                                             .dark select { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24'%3E%3Cpath fill='%23ccc' d='M7 10l5 5 5-5z'/%3E%3C/svg%3E"); }
                                                                                                                                                                                                                                                             .dark ::-webkit-calendar-picker-indicator { filter: invert(1); }
                                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                                        @layer components {
                                                                                                                                                                                                                                                            .form-input {
                                                                                                                                                                                                                                                                @apply bg-background border border-input rounded text-foreground px-3 py-1.5 font-sans text-[0.83rem] outline-none transition-all w-full h-[34px] placeholder:text-muted-foreground focus:border-ring focus:ring-2 focus:ring-ring/15 dark:focus:ring-ring/30 transition-colors duration-300;
                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                            .filter-chip {
                                                                                                                                                                                                                                                                @apply inline-flex items-center px-2.5 py-[3px] rounded-full text-[0.72rem] font-medium border border-border bg-background text-muted-foreground cursor-pointer transition-all whitespace-nowrap hover:bg-accent hover:text-foreground hover:border-muted-foreground dark:hover:border-foreground/30 transition-colors duration-300;
                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                            .filter-chip.active {
                                                                                                                                                                                                                                                                @apply bg-primary text-primary-foreground border-primary;
                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                            .btn-primary-sm {
                                                                                                                                                                                                                                                                @apply inline-flex items-center justify-center gap-1.5 h-8 px-3 rounded bg-primary text-primary-foreground text-[0.8rem] font-medium border border-primary cursor-pointer transition-all hover:bg-primary/90 focus-visible:outline-2 focus-visible:outline-ring focus-visible:outline-offset-2 disabled:opacity-50 disabled:pointer-events-none transition-colors duration-300;
                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                            .btn-secondary-sm {
                                                                                                                                                                                                                                                                @apply inline-flex items-center justify-center gap-1.5 h-8 px-3 rounded bg-secondary text-secondary-foreground text-[0.8rem] font-medium border border-border cursor-pointer transition-all hover:bg-accent hover:border-muted-foreground dark:hover:border-foreground/30 disabled:opacity-50 transition-colors duration-300;
                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                            .btn-outline-sm {
                                                                                                                                                                                                                                                                @apply inline-flex items-center justify-center gap-1.5 h-[26px] px-2 rounded bg-transparent text-foreground text-[0.73rem] font-medium border border-border cursor-pointer transition-all hover:bg-accent dark:hover:border-foreground/30 disabled:opacity-50 transition-colors duration-300;
                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                            .btn-green-sm {
                                                                                                                                                                                                                                                                @apply inline-flex items-center justify-center gap-1.5 h-8 px-3 rounded bg-status-green text-white text-[0.8rem] font-medium cursor-pointer transition-all hover:bg-status-green/85;
                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                            .btn-sky-sm {
                                                                                                                                                                                                                                                                @apply inline-flex items-center justify-center gap-1.5 h-8 px-3 rounded bg-status-sky text-white text-[0.8rem] font-medium cursor-pointer transition-all hover:bg-status-sky/85;
                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                            .btn-amber-sm {
                                                                                                                                                                                                                                                                @apply inline-flex items-center justify-center gap-1.5 h-8 px-3 rounded bg-status-amber text-white text-[0.8rem] font-medium cursor-pointer transition-all hover:bg-status-amber/85;
                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                        }


                    </style>
@endpush

@section('content')
    <!-- ============================================================
                                                                                                                                                                                                                                                         3-COLUMN POS LAYOUT
                                                                                                                                                                                                                                                    ============================================================ -->
    <div id="posMainLayout"
        class="grid grid-cols-[280px_1fr_260px] h-full overflow-hidden max-lg:grid-cols-[240px_1fr_220px] max-md:flex max-md:flex-col bg-background text-foreground font-sans text-sm leading-relaxed antialiased transition-colors duration-300 max-md:pb-[60px]">

        <!-- ====================================================
                                                                                                                                                                                                                                                           PANEL 1 — APPOINTMENT LIST
                                                                                                                                                                                                                                                      ==================================================== -->
        <div id="panelApptList"
            class="flex flex-col overflow-hidden bg-background border-r border-border max-md:border-r-0 max-md:flex-1 transition-colors duration-300">

            <!-- Panel Header -->
            <div class="px-4 pt-3.5 pb-3 border-b border-border shrink-0">
                <div
                    class="text-[0.7rem] font-semibold uppercase tracking-[0.7px] text-muted-foreground mb-2.5 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('dashboard') }}" class="hover:text-foreground transition-colors p-1 -ml-1" title="{{ __('file.back_to_dashboard') ?? 'Back to Dashboard' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        <span>{{ __('file.appointments') }}</span>
                    </div>
                    <input type="date" id="dateSelector" value="{{ $selectedDate }}" onchange="changeDate(this.value)"
                        class="bg-transparent border-none text-[0.75rem] font-medium text-foreground p-0 cursor-pointer focus:ring-0">
                </div>

                <!-- Doctor Dropdown -->
                <div class="mb-2.5">
                    <div class="text-[0.68rem] font-semibold text-muted-foreground uppercase tracking-[0.6px] mb-1.5">
                        {{ __('file.doctor') }}
                    </div>
                    @if($isRestrictedDoctor && auth()->user()->doctor)
                        <div class="form-input bg-muted/20 flex items-center px-3 text-foreground font-medium border-border/50">
                            Dr. {{ auth()->user()->doctor->full_name }}
                        </div>
                        <input type="hidden" id="doctorDropdown" value="{{ auth()->user()->doctor->id }}">
                    @else
                        <select id="doctorDropdown" onchange="setDocFilterDropdown(this.value)"
                            class="form-input pr-7 cursor-pointer">
                            @if(!auth()->user()->hasRole('doctor') || auth()->user()->hasRole('admin'))
                                <option value="all">{{ __('file.all_doctors') }}</option>
                            @endif
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}">Dr. {{ $doc->full_name }} —
                                    {{ $doc->primarySpecialization?->name ?? __('file.general') }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <!-- Status Filter -->
                <div>
                    <div class="text-[0.68rem] font-semibold text-muted-foreground uppercase tracking-[0.6px] mb-1.5">
                        {{ __('file.status') }}
                    </div>
                    <div class="flex flex-wrap gap-1" id="statusFilters">
                        <button class="filter-chip active" data-status="all"
                            onclick="setStatusFilter(this,'all')">{{ __('file.all') }}</button>
                        <button class="filter-chip" data-status="pending"
                            onclick="setStatusFilter(this,'pending')">{{ __('file.pending') }}</button>
                        <button class="filter-chip" data-status="approved"
                            onclick="setStatusFilter(this,'approved')">{{ __('file.approved') }}</button>
                        <button class="filter-chip" data-status="paid"
                            onclick="setStatusFilter(this,'paid')">{{ __('file.paid') }}</button>
                        <button class="filter-chip" data-status="running"
                            onclick="setStatusFilter(this,'running')">{{ __('file.running') ?? 'Running' }}</button>
                        <button class="filter-chip" data-status="completed"
                            onclick="setStatusFilter(this,'completed')">{{ __('file.completed') }}</button>
                        <button class="filter-chip" data-status="cancelled"
                            onclick="setStatusFilter(this,'cancelled')">{{ __('file.cancelled') }}</button>
                        <button class="filter-chip" data-status="rejected"
                            onclick="setStatusFilter(this,'rejected')">{{ __('file.rejected') }}</button>
                    </div>
                </div>
            </div>

            <!-- Appointment List -->
            <div id="apptList" class="flex-1 overflow-y-auto scrollbar-thin p-3 max-md:max-h-[60vh]"></div>
        </div>

        <!-- ====================================================
                                                                                                                                                                                                                                                           PANEL 2 — APPOINTMENT EDIT
                                                                                                                                                                                                                                                      ==================================================== -->
        <div id="panelDetail"
            class="flex flex-col overflow-hidden bg-background border-l border-border max-md:border-l-0 max-md:border-t-0 max-md:hidden max-md:flex-1 transition-colors duration-300">

            <!-- Placeholder -->
            <div id="editPlaceholder"
                class="flex-1 flex flex-col items-center justify-center gap-2 text-muted-foreground p-10 text-center">
                <div
                    class="w-[52px] h-[52px] rounded bg-muted border border-border flex items-center justify-center text-[1.4rem]">
                    📋</div>
                <h3 class="text-[0.95rem] font-semibold text-foreground">{{ __('file.no_appointment_selected') }}</h3>
                <p class="text-[0.8rem] max-w-[240px] leading-relaxed">{{ __('file.select_appointment_to_view_and_edit') }}
                </p>

            </div>

            <!-- Edit Header -->
            <div id="editHeader"
                class="hidden bg-background border-b border-border px-5 py-3 shrink-0 flex items-center justify-between gap-3">
                <div>
                    <div id="editHeaderTitle" class="text-[0.9rem] font-semibold">—</div>
                    <div id="editHeaderSub" class="text-[0.75rem] text-muted-foreground mt-0.5">—</div>
                </div>
                <div id="editHeaderBadge"></div>
            </div>

            <!-- Edit Form -->
            <div id="editForm" class="hidden flex-1 overflow-y-auto scrollbar-thin p-5 max-md:overflow-visible">

                <!-- Patient & Visit -->
                <div class="mb-5 pb-5 border-b border-border">
                    <div class="flex items-center justify-between mb-3">
                        <div
                            class="text-[0.73rem] font-semibold text-foreground flex items-center gap-2 after:content-[''] after:w-12 after:h-px after:bg-border">
                            {{ __('file.patient_and_visit') }}
                        </div>
                        <a id="patientProfileLink" href="#" target="_blank"
                            class="text-[0.7rem] text-primary hover:underline font-medium">{{ __('file.view_profile') }}
                            ↗</a>
                    </div>

                    <!-- Patient Summary -->
                    <div id="patientSummary"
                        class="mb-4 grid grid-cols-2 gap-y-3 gap-x-4 bg-muted/20 p-3 rounded border border-border/50">
                        <div class="flex flex-col gap-0.5">
                            <label
                                class="text-[0.65rem] font-bold text-muted-foreground uppercase opacity-70">{{ __('file.patient_name') }}</label>
                            <div id="dispPatientName" class="text-[0.8rem] font-bold text-foreground truncate">—</div>
                        </div>
                        <div class="flex flex-col gap-0.5">
                            <label
                                class="text-[0.65rem] font-bold text-muted-foreground uppercase opacity-70">{{ __('file.mrn') }}</label>
                            <div id="dispPatientMRN" class="text-[0.8rem] font-mono text-foreground">—</div>
                        </div>
                        <div class="flex flex-col gap-0.5">
                            <label
                                class="text-[0.65rem] font-bold text-muted-foreground uppercase opacity-70">{{ __('file.phone') }}</label>
                            <div id="dispPatientPhone" class="text-[0.8rem] text-foreground">—</div>
                        </div>
                        <div class="flex flex-col gap-0.5">
                            <label
                                class="text-[0.65rem] font-bold text-muted-foreground uppercase opacity-70">{{ __('file.dob') }}</label>
                            <div id="dispPatientDOB" class="text-[0.8rem] text-foreground">—</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[0.77rem] font-medium text-foreground">{{ __('file.date') }}</label>
                            <input type="date" id="fDate" class="form-input">
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[0.77rem] font-medium text-foreground">{{ __('file.time_slot') }}</label>
                            <select id="fTime" class="form-input pr-7 cursor-pointer">
                                <option value="">{{ __('file.select_date_doctor_first') }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-5 pb-5 border-b border-border">
                    <div
                        class="text-[0.73rem] font-semibold text-foreground mb-3 flex items-center gap-2 after:content-[''] after:flex-1 after:h-px after:bg-border">
                        {{ __('file.doctor') }}
                    </div>
                    <div class="grid grid-cols-1 gap-3">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[0.77rem] font-medium text-foreground">{{ __('file.specialization') }} <span
                                    class="text-destructive">*</span></label>
                            <select id="fSpecialization" class="form-input pr-7 cursor-pointer" onchange="loadFilteredDoctors('f')">
                                <option value="">{{ __('file.select_specialization') }}</option>
                                @foreach($specializations as $spec)
                                    <option value="{{ $spec->id }}">{{ $spec->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[0.77rem] font-medium text-foreground">{{ __('file.doctor') }} <span
                                    class="text-destructive">*</span></label>
                            <select id="fDoctor" class="form-input pr-7 cursor-pointer" onchange="loadDoctorAttributes('f')">
                                @foreach($doctors as $doc)
                                    <option value="{{ $doc->id }}">Dr. {{ $doc->full_name }} —
                                        {{ $doc->primarySpecialization?->name ?? 'General' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Clinical Record (Treatments & Prescriptions) -->
                <div id="medicalRecordSection" class="hidden mb-5 pb-5 border-b border-border">
                    <div
                        class="text-[0.73rem] font-semibold text-foreground mb-3 flex items-center gap-2 after:content-[''] after:flex-1 after:h-px after:bg-border">
                        {{ __('file.medical_record') }}
                    </div>

                    <!-- Treatments -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <label
                                class="text-[0.68rem] font-semibold text-muted-foreground uppercase">{{ __('file.treatments') }}</label>
                            <button id="btnAddTreatment" onclick="openTreatmentModal()"
                                class="text-[0.68rem] text-primary hover:underline font-medium">+
                                {{ __('file.add_treatment') }}</button>
                        </div>
                        <div id="treatmentsTable" class="bg-muted/30 border border-border rounded overflow-hidden">
                            <div class="p-3 text-center text-xs text-muted-foreground italic">
                                {{ __('file.no_treatments_added_yet') }}
                            </div>
                        </div>
                    </div>

                    <!-- Prescription -->
                    <div>
                        <label
                            class="text-[0.75rem] font-semibold text-foreground border-b border-border pb-1">{{ __('file.medication_list') }}</label>
                        <div id="prescriptionSummary" class="bg-muted/30 border border-border rounded p-3">
                            <!-- JS injected summary -->
                        </div>
                    </div>
                </div>

                <!-- Clinical Notes -->
                <div class="mb-5 pb-5 border-b border-border">
                    <div
                        class="text-[0.73rem] font-semibold text-foreground mb-3 flex items-center gap-2 after:content-[''] after:flex-1 after:h-px after:bg-border">
                        {{ __('file.clinical_notes') }}
                    </div>
                    <div class="flex flex-col gap-1.5 mb-3">
                        <label class="text-[0.77rem] font-medium text-foreground">{{ __('file.chief_complaint') }}</label>
                        <textarea id="fComplaint" placeholder="{{ __('file.patient_complaint_placeholder') }}"
                            class="form-input min-h-[64px] resize-y py-2 h-auto"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[0.77rem] font-medium text-foreground">{{ __('file.doctor_notes') }}</label>
                            <textarea id="fDoctorNotes" placeholder="{{ __('file.medical_findings_placeholder') }}"
                                class="form-input min-h-[56px] resize-y py-2 h-auto"></textarea>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[0.77rem] font-medium text-foreground">{{ __('file.admin_notes') }}</label>
                            <textarea id="fAdminNotes" placeholder="{{ __('file.internal_remarks_placeholder') }}"
                                class="form-input min-h-[56px] resize-y py-2 h-auto"></textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mt-3">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[0.77rem] font-medium text-foreground">{{ __('file.age_group') }}</label>
                            <select id="fAgeGroup" class="form-input pr-7 cursor-pointer">
                                <option value="">{{ __('file.select_age_group') }}</option>
                                @foreach($ageGroups as $ag)
                                    <option value="{{ $ag->id }}">{{ $ag->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[0.77rem] font-medium text-foreground">{{ __('file.preferred_language') }}</label>
                            <select id="fLanguage" class="form-input pr-7 cursor-pointer">
                                <option value="">{{ __('file.select_language') }}</option>
                                @foreach($languages as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>


            </div><!-- /editForm -->

            <!-- Action Bar -->
            <div id="editActionBar"
                class="hidden items-center justify-between flex-wrap gap-2 px-5 py-3 border-t border-border bg-background shrink-0">
                <div class="flex gap-1.5">
                    <button id="btnStart" onclick="quickAction('start')" class="hidden btn-sky-sm">▶
                        {{ __('file.start_visit') }}</button>
                    <button id="btnComplete" onclick="completeAppt(selectedApptId)" class="hidden btn-green-sm">✓
                        {{ __('file.complete') }}</button>
                </div>
                <div class="flex gap-1.5">
                    <button onclick="discardEdit()" class="btn-secondary-sm">{{ __('file.discard') }}</button>
                    <button onclick="saveAppt()" class="btn-primary-sm">{{ __('file.save') }}</button>
                </div>
            </div>

        </div><!-- /panel2 -->

        <!-- Treatment Modal -->
        <div id="treatmentModal"
            class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-black/50 backdrop-blur-sm animate-fadeIn">
            <div
                class="bg-background border border-border rounded-xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh] animate-slideIn">
                <div class="px-5 py-4 border-b border-border flex items-center justify-between">
                    <h3 class="font-bold text-foreground">{{ __('file.apply_treatments') }}</h3>
                    <button onclick="closeTreatmentModal()" class="text-muted-foreground hover:text-foreground">✕</button>
                </div>
                <div class="p-5 overflow-y-auto flex-1 bg-muted/10">
                    <div id="modalTreatmentList" class="space-y-2">
                        @foreach($treatments as $t)
                            <label
                                class="flex items-center p-3 border border-border rounded-lg cursor-pointer bg-card hover:bg-accent transition-colors group">
                                <input type="checkbox" name="treatment_ids[]" value="{{ $t->id }}"
                                    class="w-4 h-4 rounded text-primary border-border focus:ring-primary">
                                <div class="ml-3 flex-1">
                                    <span class="text-sm font-semibold text-foreground">{{ $t->name }}</span>
                                    @if($t->code)<span
                                    class="text-[0.65rem] text-muted-foreground ml-2">{{ $t->code }}</span>@endif
                                </div>
                                <div class="text-sm font-bold text-primary treatment-price-display"
                                    data-treatment-id="{{ $t->id }}">
                                    {!! $currency_code !!} 0.00
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="px-5 py-4 border-t border-border bg-background flex justify-end gap-3">
                    <button onclick="closeTreatmentModal()" class="btn-secondary-sm">{{ __('file.cancel') }}</button>
                    <button id="btnSaveTreatments" onclick="saveTreatments()"
                        class="btn-primary-sm">{{ __('file.save_changes') }}</button>
                </div>
            </div>
        </div>

        <!-- Prescription Modal -->
        <div id="prescriptionModal"
            class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-black/50 backdrop-blur-sm animate-fadeIn">
            <div
                class="bg-background border border-border rounded-xl shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col max-h-[95vh] animate-slideIn">
                <div class="px-5 py-4 border-b border-border flex items-center justify-between">
                    <h3 class="font-bold text-foreground" id="prescriptionModalTitle">{{ __('file.prescription') }}</h3>
                    <button onclick="closePrescriptionModal()"
                        class="text-muted-foreground hover:text-foreground">✕</button>
                </div>

                <div class="border-b border-border bg-muted/5">
                    <nav class="flex overflow-x-auto no-scrollbar" aria-label="Tabs">
                        <button onclick="switchPrescriptionTab('details')" id="tab-p-details"
                            class="p-tab-btn flex-1 px-6 py-3 text-sm font-medium border-b-2 border-primary text-primary bg-accent/5">{{ __('file.details') }}</button>
                        <button onclick="switchPrescriptionTab('meds')" id="tab-p-meds"
                            class="p-tab-btn flex-1 px-6 py-3 text-sm font-medium border-b-2 border-transparent text-muted-foreground hover:text-foreground">{{ __('file.medications') }}</button>
                        <button onclick="switchPrescriptionTab('notes')" id="tab-p-notes"
                            class="p-tab-btn flex-1 px-6 py-3 text-sm font-medium border-b-2 border-transparent text-muted-foreground hover:text-foreground">{{ __('file.notes') }}</button>
                    </nav>
                </div>

                <div class="p-5 overflow-y-auto flex-1 bg-muted/10 space-y-4">
                    <input type="hidden" id="p_appt_id">
                    <input type="hidden" id="p_prescription_id">

                    <!-- Details Tab -->
                    <div id="p-content-details" class="p-tab-content space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="text-[0.7rem] font-medium text-foreground mb-1 block">{{ __('file.prescription_date') }}</label>
                                <input type="date" id="p_date" class="form-input text-sm">
                            </div>
                            <div>
                                <label
                                    class="text-[0.7rem] font-medium text-foreground mb-1 block">{{ __('file.type') }}</label>
                                <select id="p_type" class="form-input text-sm">
                                    <option value="Standard">{{ __('file.standard') }}</option>
                                    <option value="Emergency">{{ __('file.emergency') }}</option>
                                    <option value="Chronic">{{ __('file.chronic') }}</option>
                                    <option value="Follow-up">{{ __('file.follow_up') }}</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label
                                class="text-[0.7rem] font-medium text-foreground mb-1 block">{{ __('file.diagnosis_reason') }}</label>
                            <textarea id="p_diagnosis" rows="3" class="form-input text-sm resize-none"
                                placeholder="{{ __('file.enter_diagnosis') }}"></textarea>
                        </div>
                        <div>
                            <label
                                class="text-[0.7rem] font-medium text-foreground mb-1 block">{{ __('file.use_template') }}</label>
                            <select id="p_template" class="form-input text-sm"
                                onchange="applyPrescriptionTemplate(this.value)">
                                <option value="">-- {{ __('file.none') }} --</option>
                                @foreach($templates as $tpl)
                                    <option value="{{ $tpl->id }}">{{ $tpl->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Meds Tab -->
                    <div id="p-content-meds" class="p-tab-content hidden space-y-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-muted-foreground">{{ __('file.medication_list') }}</span>
                            <button onclick="addMedicationRow()" class="text-xs font-bold text-primary hover:underline">+
                                {{ __('file.add_medication') }}</button>
                        </div>
                        <div id="p_med_container" class="space-y-3">
                            <!-- Rows injected by JS -->
                        </div>
                    </div>

                    <!-- Notes Tab -->
                    <div id="p-content-notes" class="p-tab-content hidden">
                        <label
                            class="block text-[0.7rem] font-semibold uppercase tracking-wider text-muted-foreground mb-1">{{ __('file.additional_instructions') }}</label>
                        <textarea id="p_notes" rows="6" class="form-input text-sm resize-none"
                            placeholder="{{ __('file.enter_instructions') }}"></textarea>
                    </div>
                </div>

                <div class="px-5 py-4 border-t border-border bg-background flex justify-end gap-3">
                    <button onclick="closePrescriptionModal()" class="btn-secondary-sm">{{ __('file.cancel') }}</button>
                    <button id="btnSavePrescription" onclick="savePrescription()"
                        class="btn-primary-sm">{{ __('file.save_prescription') }}</button>
                </div>
            </div>
        </div>

        <!-- ====================================================
                                                                                                                                                                                                                                                           PANEL 3 — QUEUE
                                                                                                                                                                                                                                                      ==================================================== -->
        <div id="panelQueue"
            class="flex flex-col overflow-hidden bg-background border-l border-border max-md:border-l-0 max-md:border-t-0 max-md:hidden max-md:flex-1 transition-colors duration-300">

            <div class="px-4 pt-3.5 pb-3 border-b border-border shrink-0 flex items-center justify-between">
                <div class="text-[0.7rem] font-semibold uppercase tracking-[0.7px] text-muted-foreground">
                    {{ __('file.live_queue') }}
                </div>
                <div class="flex items-center gap-3">
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                            class="flex items-center space-x-1 text-sm font-medium text-muted-foreground hover:text-foreground focus:outline-none rounded-md px-1 transition active:scale-95"
                            title="{{ __('file.language') }}">
                            <span class="text-[0.65rem] font-bold">{{ strtoupper(app()->getLocale()) }}</span>
                            <svg class="w-3 h-3 transition-transform" :class="{ 'rotate-180': open }" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-cloak @click.away="open = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-32 origin-top-right bg-white dark:bg-gray-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50 overflow-hidden border border-border">
                            <form method="POST" action="{{ route('language.switch') }}">
                                @csrf
                                <input type="hidden" name="locale" value="en">
                                <button type="submit"
                                    class="w-full text-left px-3 py-2 text-[0.7rem] font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                    {{ __('English') }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('language.switch') }}">
                                @csrf
                                <input type="hidden" name="locale" value="es">
                                <button type="submit"
                                    class="w-full text-left px-3 py-2 text-[0.7rem] font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                    {{ __('Español') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <button id="theme-toggle-navbar" aria-label="Toggle dark mode"
                        class="text-muted-foreground hover:text-foreground transition-colors active:scale-95"
                        title="{{ __('file.theme') }}">
                        <svg id="sun-icon-navbar" class="w-3.5 h-3.5 hidden" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 9H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l.707.707M6.343 6.343l.707.707M12 7a5 5 0 100 10 5 5 0 000-10z" />
                        </svg>
                        <svg id="moon-icon-navbar" class="w-3.5 h-3.5 hidden" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>

                    <button id="fullscreen-toggle" aria-label="Toggle fullscreen"
                        class="text-muted-foreground hover:text-foreground transition-colors active:scale-95"
                        title="{{ __('file.fullscreen') }}">
                        <svg id="enter-fullscreen-icon" class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 3H5a2 2 0 00-2 2v3M16 3h3a2 2 0 012 2v3M8 21H5a2 2 0 01-2-2v-3M16 21h3a2 2 0 002-2v-3" />
                        </svg>
                        <svg id="exit-fullscreen-icon" class="w-3.5 h-3.5 hidden" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 9H5V5M15 9h4V5M9 15H5v4M15 15h4v4" />
                        </svg>
                    </button>
                </div>
            </div>

            <div id="queueList" class="flex-1 overflow-y-auto scrollbar-thin p-3 max-md:max-h-[60vh]"></div>
        </div>

        <datalist id="inventory-list">
            @foreach($inventoryItems as $item)
                <option value="{{ $item->name }}" data-id="{{ $item->id }}">
                    {{ $item->generic_name ? "({$item->generic_name})" : "" }}
                </option>
            @endforeach
        </datalist>

    </div><!-- /pos-wrapper -->

    <!-- MOBILE BOTTOM TAB BAR -->
    <div
        class="md:hidden fixed bottom-0 left-0 right-0 h-[60px] bg-background border-t border-border flex items-center justify-around z-40 pb-safe shadow-[0_-4px_6px_-1px_rgb(0,0,0,0.05)]">
        <button onclick="switchMobileTab('panelApptList')" id="tabApptList"
            class="flex flex-col items-center justify-center gap-1 text-primary w-1/3 h-full transition-colors active:bg-accent/50">
            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16">
                </path>
            </svg>
            <span class="text-[0.6rem] font-semibold tracking-wider uppercase">{{ __('file.appointments') }}</span>
        </button>
        <button onclick="switchMobileTab('panelDetail')" id="tabDetail"
            class="flex flex-col items-center justify-center gap-1 text-muted-foreground w-1/3 h-full transition-colors active:bg-accent/50">
            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                </path>
            </svg>
            <span class="text-[0.6rem] font-semibold tracking-wider uppercase">{{ __('file.details') }}</span>
        </button>
        <button onclick="switchMobileTab('panelQueue')" id="tabQueue"
            class="flex flex-col items-center justify-center gap-1 text-muted-foreground w-1/3 h-full transition-colors active:bg-accent/50">
            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                </path>
            </svg>
            <span class="text-[0.6rem] font-semibold tracking-wider uppercase">{{ __('file.live_queue') ?? 'Queue' }}</span>
        </button>
    </div>

    <!-- TOAST CONTAINER -->
    <div id="toastContainer" class="fixed bottom-20 md:bottom-5 right-5 z-[9999] flex flex-col gap-1.5"></div>


@endsection

@push('scripts')
    <script>
        /* ========== TOAST HELPER ========== */
        function toast(title, type = 'info', detail = '') {
            const container = document.getElementById('toastContainer');
            if (!container) return;
            const colors = {
                success: 'bg-green-500/90 text-white',
                error: 'bg-red-500/90 text-white',
                info: 'bg-primary/90 text-white',
                warning: 'bg-amber-500/90 text-white'
            };
            const icons = {
                success: '✓',
                error: '✕',
                info: 'ℹ',
                warning: '⚠'
            };
            const el = document.createElement('div');
            el.className = `flex items-start gap-2 px-4 py-3 rounded-lg shadow-lg text-sm font-medium backdrop-blur-sm min-w-[240px] max-w-[320px] animate-fadeIn transition-all ${colors[type] || colors.info}`;
            el.innerHTML = `
                                <span class="shrink-0 font-bold text-base leading-none mt-0.5">${icons[type] || icons.info}</span>
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold truncate">${title}</div>
                                    ${detail ? `<div class="text-[0.77rem] opacity-80 mt-0.5 line-clamp-2">${detail}</div>` : ''}
                                </div>
                                <button onclick="this.parentElement.remove()" class="shrink-0 opacity-70 hover:opacity-100 ml-1">✕</button>
                            `;
            container.appendChild(el);
            setTimeout(() => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(6px)';
                el.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                setTimeout(() => el.remove(), 300);
            }, 4000);
        }

        /* ========== DATA FROM CONTROLLER ========== */
        const DOCTORS = @json($doctorsJs);
        const DOCTOR_TREATMENT_PRICES = @json($doctorTreatmentPrices);
        const CURRENCY_CODE = @json($currency_code);
        const MEDICINE_TEMPLATES = @json($templates);
        const INVENTORY_ITEMS = @json($inventoryItems);
        const ALL_AGE_GROUPS = @json($ageGroups->map(fn($ag) => ['id' => $ag->id, 'name' => $ag->name]));
        const ALL_LANGUAGES = @json($languages);
        let appointments = @json($appointmentsJs);

        function switchMobileTab(targetPanelId) {
            if (window.innerWidth >= 768) return; // Only apply on mobile (md breakpoint)

            // Hide all panels
            document.getElementById('panelApptList').classList.add('max-md:hidden');
            document.getElementById('panelDetail').classList.add('max-md:hidden');
            document.getElementById('panelQueue').classList.add('max-md:hidden');

            // Show target
            document.getElementById(targetPanelId).classList.remove('max-md:hidden');

            // Reset styling on tab buttons
            ['tabApptList', 'tabDetail', 'tabQueue'].forEach(t => {
                const el = document.getElementById(t);
                if (el) {
                    el.classList.remove('text-primary');
                    el.classList.add('text-muted-foreground');
                }
            });

            // Set active tab styling
            const activeTabId = targetPanelId === 'panelApptList' ? 'tabApptList' : (targetPanelId === 'panelDetail' ? 'tabDetail' : 'tabQueue');
            const activeEl = document.getElementById(activeTabId);
            if (activeEl) {
                activeEl.classList.remove('text-muted-foreground');
                activeEl.classList.add('text-primary');
            }
        }

        const urlParams = new URLSearchParams(window.location.search);
        let selectedApptId = null;
        let activeDocFilter = urlParams.get('doctor_id') || "all";
        let activeStatusFilter = "all";
        const CURRENT_USER = @json($currentUser);
        let idCounter = 1000;
        const allowedStatuses = ["approved", "paid", "running", "completed"];

        // Global references to edit form elements
        let formInputs = [];
        window.addEventListener('DOMContentLoaded', () => {
            formInputs = [
                document.getElementById('fDate'),
                document.getElementById('fTime'),
                document.getElementById('fSpecialization'),
                document.getElementById('fDoctor'),
                document.getElementById('fDuration'),
                document.getElementById('fVisitType'),
                document.getElementById('fRoom'),
                document.getElementById('fComplaint'),
                document.getElementById('fDoctorNotes'),
                document.getElementById('fAdminNotes'),
                document.getElementById('fStatus'),
                document.getElementById('fFee'),
                document.getElementById('fAgeGroup'),
                document.getElementById('fLanguage')
            ].filter(el => el !== null);
        });

        async function loadFilteredDoctors(prefix = 'f') {
            const specId = document.getElementById(prefix + 'Specialization')?.value;

            const params = new URLSearchParams();
            if (specId) params.append('specialization_id', specId);
            if (ageGroupId) params.append('age_group_id', ageGroupId);
            if (languageId) params.append('preferred_language_id', languageId);

            try {
                const response = await fetch(`{{ route('appointments.doctors.filtered') }}?${params.toString()}`);
                const data = await response.json();

                const currentDoctorId = doctorSelect.value;
                doctorSelect.innerHTML = `<option value="">{{ __('file.select_doctor') }}</option>`;

                data.forEach(doc => {
                    const opt = document.createElement('option');
                    opt.value = doc.value; // API returns value/text
                    opt.textContent = doc.text;
                    doctorSelect.appendChild(opt);
                });

                if (currentDoctorId && Array.from(doctorSelect.options).some(o => o.value == currentDoctorId)) {
                    doctorSelect.value = currentDoctorId;
                }
            } catch (err) {
                console.error('Error loading filtered doctors:', err);
            }
        }


        async function loadDoctorAttributes(prefix = 'f') {
            const doctorId = document.getElementById(prefix + 'Doctor').value;
            if (!doctorId) {
                resetAttributes(prefix);
                return;
            }

            // Trigger slot loading
            const date = document.getElementById('fDate').value;
            if (date) loadSlots(doctorId, date);

            try {
                const response = await fetch(`{{ url('doctors') }}/${doctorId}/attributes`);
                const data = await response.json();

                const specSelect = document.getElementById(prefix + 'Specialization');
                if (specSelect) specSelect.value = data.specialization_id || "";

                // Filter Age Groups
                const ageGroupSelect = document.getElementById(prefix + 'AgeGroup');
                const supportedAgeGroups = data.age_groups || [];
                const currentAgeGroup = ageGroupSelect.value;
                ageGroupSelect.innerHTML = '<option value="">{{ __("file.select_age_group") }}</option>';
                
                // Strictly show only assigned
                const ageGroupsToShow = ALL_AGE_GROUPS.filter(ag => supportedAgeGroups.includes(ag.id));

                ageGroupsToShow.forEach(ag => {
                    const opt = new Option(ag.name, ag.id);
                    if (currentAgeGroup == ag.id) opt.selected = true;
                    ageGroupSelect.add(opt);
                });

                // Filter Languages
                const languageSelect = document.getElementById(prefix + 'Language');
                const supportedLanguages = data.languages || [];
                const currentLang = languageSelect.value;
                languageSelect.innerHTML = '<option value="">{{ __("file.select_language") }}</option>';
                
                // Strictly show only assigned
                const langsToShow = Object.entries(ALL_LANGUAGES).filter(([id, name]) => 
                    supportedLanguages.includes(parseInt(id))
                );

                langsToShow.forEach(([id, name]) => {
                    const opt = new Option(name, id);
                    if (currentLang == id) opt.selected = true;
                    languageSelect.add(opt);
                });

            } catch (err) {
                console.error('Error loading doctor attributes:', err);
                resetAttributes(prefix);
            }
        }

        function resetAttributes(prefix = 'f') {
            const ageGroupSelect = document.getElementById(prefix + 'AgeGroup');
            const languageSelect = document.getElementById(prefix + 'Language');
            const currentAgeGroup = ageGroupSelect.value;
            const currentLang = languageSelect.value;

            ageGroupSelect.innerHTML = '<option value="">{{ __("file.select_age_group") }}</option>';
            ALL_AGE_GROUPS.forEach(ag => {
                const opt = new Option(ag.name, ag.id);
                if (currentAgeGroup == ag.id) opt.selected = true;
                ageGroupSelect.add(opt);
            });

            languageSelect.innerHTML = '<option value="">{{ __("file.select_language") }}</option>';
            Object.entries(ALL_LANGUAGES).forEach(([id, name]) => {
                const opt = new Option(name, id);
                if (currentLang == id) opt.selected = true;
                languageSelect.add(opt);
            });
        }

        window.addEventListener('DOMContentLoaded', () => {
            const docDashed = document.getElementById("doctorDropdown");

            // Apply restrictions for doctors who are not admins or primary care providers
            const isRestrictedDoctor = !CURRENT_USER.roles.includes('admin') &&
                CURRENT_USER.roles.includes('doctor');

            if (CURRENT_USER.doctor_id) {
                // If user is a doctor, default to their own queue unless they requested another queue and are allowed to
                if (isRestrictedDoctor || !urlParams.get('doctor_id')) {
                    activeDocFilter = CURRENT_USER.doctor_id.toString();
                    if (window.history.replaceState) {
                        const url = new URL(window.location.href);
                        url.searchParams.set('doctor_id', activeDocFilter);
                        window.history.replaceState(null, '', url.toString());
                    }
                }

                if (docDashed) {
                    docDashed.value = activeDocFilter;
                }

                // Lock the doctor field in modals and forms
                const fDoctor = document.getElementById("fDoctor");
                if (fDoctor) {
                    fDoctor.value = CURRENT_USER.doctor_id;
                    if (isRestrictedDoctor) fDoctor.disabled = true;
                }
                const mDoctor = document.getElementById("mDoctor");
                if (mDoctor) {
                    mDoctor.value = CURRENT_USER.doctor_id;
                    if (isRestrictedDoctor) mDoctor.disabled = true;
                }
            } else {
                if (docDashed) docDashed.value = activeDocFilter;
            }

            // Initial render AFTER figuring out the correct activeDocFilter
            renderApptList();
            renderQueue();

            // Auto-refresh queue every 30 seconds
            setInterval(() => {
                const currentDate = document.getElementById("dateSelector")?.value;
                if (currentDate) {
                    refreshData(currentDate, activeDocFilter, true);
                }
            }, 30000);
        });

        async function refreshData(date, docId, silent = false) {
            const url = new URL(window.location.href);
            if (date) url.searchParams.set('date', date);
            if (docId) url.searchParams.set('doctor_id', docId);

            // Update URL without full reload
            history.replaceState(null, '', url.toString());

            try {
                const response = await fetch(url.toString(), {
                    headers: { 'Accept': 'application/json' }
                });
                if (!response.ok) throw new Error("Failed to fetch data");

                const data = await response.json();
                appointments = data.appointmentsJs;

                renderApptList();
                renderQueue();
                if (!silent) toast("Appointments updated", "success");
            } catch (err) {
                console.error(err);
                if (!silent) toast("Sync failed", "error", err.message);
            }
        }

        function changeDate(date) {
            refreshData(date, activeDocFilter);
        }

        function loadSlots(doctorId, date, callback = null) {
            const el = document.getElementById("fTime");
            if (!el || !doctorId || !date) return;

            el.innerHTML = '<option value="">Loading slots...</option>';
            el.disabled = true;

            const url = `{{ route("doctors.available-slots", ":doctor") }}`.replace(':doctor', doctorId) + '?date=' + date;

            fetch(url, { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    el.disabled = false;
                    if (data.slots && data.slots.length > 0) {
                        el.innerHTML = data.slots.map(s => `<option value="${s.start}|${s.end}">${s.label}</option>`).join('');
                    } else if (data.message) {
                        el.innerHTML = `<option value="">${data.message}</option>`;
                    } else {
                        el.innerHTML = `<option value="">{{ __('file.no_available_slots') }}</option>`;
                    }
                    if (callback) callback();
                })
                .catch(() => {
                    el.disabled = false;
                    el.innerHTML = `<option value="">{{ __('file.error_loading_slots') }}</option>`;
                    if (callback) callback();
                });
        }

        /* ========== HELPERS ========== */
        const statusLabel = s => ({
            pending: "Pending",
            approved: "Approved",
            paid: "Paid",
            rejected: "Rejected",
            cancelled: "Cancelled",
            completed: "Completed",
            running: "Running"
        })[s] || s;
        const visitLabel = v => ({ consultation: "Consultation", follow_up: "Follow-up", procedure: "Procedure", checkup: "Checkup", emergency: "Emergency" })[v] || v;
        const todayISO = () => new Date().toISOString().slice(0, 10);

        const BADGE_CLASSES = {
            pending: "bg-blue-50/10 text-blue-500 border-blue-200/20",
            approved: "bg-teal-50/10 text-teal-500 border-teal-200/20",
            paid: "bg-indigo-50/10 text-indigo-500 border-indigo-200/20",
            completed: "bg-green-50/10 text-green-500 border-green-200/20",
            cancelled: "bg-red-50/10 text-red-500 border-red-200/20",
            rejected: "bg-orange-50/10 text-orange-500 border-orange-200/20",
            running: "bg-amber-50/10 text-amber-600 border-amber-200/20",
        };
        function badge(status) {
            const cls = BADGE_CLASSES[status] || "bg-muted text-muted-foreground border-border";
            return `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.68rem] font-medium border ${cls} before:content-[''] before:w-1.5 before:h-1.5 before:rounded-full before:bg-current before:opacity-80">${statusLabel(status)}</span>`;
        }

        /* ========== FILTERS ========== */
        function setDocFilterDropdown(val) {
            activeDocFilter = val;
            refreshData(null, val);
        }
        function setStatusFilter(el, val) {
            document.querySelectorAll("#statusFilters .filter-chip").forEach(c => c.classList.remove("active"));
            el.classList.add("active"); activeStatusFilter = val; renderApptList();
        }

        function getFilteredAppts() {
            return appointments.filter(a => {
                if (activeDocFilter !== "all" && a.doctor_id !== +activeDocFilter) return false;
                if (activeStatusFilter !== "all" && a.status !== activeStatusFilter) return false;
                return true;
            });
        }

        /* ========== RENDER APPOINTMENT LIST ========== */
        function renderApptList() {
            const list = document.getElementById("apptList");
            const filtered = getFilteredAppts();
            if (!filtered.length) {
                list.innerHTML = `<div class="text-center py-8 text-muted-foreground text-[0.8rem]"><div class="text-3xl mb-2">🔍</div>{{ __('file.no_appointments_match_filters') }}</div>`;
                return;
            }
            const groups = {};
            filtered.forEach(a => { if (!groups[a.doctor_id]) groups[a.doctor_id] = []; groups[a.doctor_id].push(a); });

            let html = "";
            Object.keys(groups).forEach(dId => {
                const doc = DOCTORS[dId] || { name: 'Unknown', spec: '-', dotClass: 'bg-gray-400' };
                html += `<div class="text-[0.67rem] font-semibold uppercase tracking-[0.8px] text-muted-foreground pt-2.5 pb-1.5 flex items-center gap-1.5 after:content-[''] after:flex-1 after:h-px after:bg-border transition-colors duration-300">
                                                                                                                                                                                                                          <span class="w-1.5 h-1.5 rounded-full ${doc.dotClass} inline-block"></span>${doc.name}
                                                                                                                                                                                                                        </div>`;
                groups[dId].sort((a, b) => a.time.localeCompare(b.time)).forEach(a => {
                    const isSelected = a.id === selectedApptId;
                    const base = isSelected
                        ? "bg-primary border-primary text-primary-foreground shadow-sm"
                        : "bg-card border-border hover:bg-accent hover:border-muted-foreground/40 dark:hover:border-foreground/30 transition-colors duration-300";
                    html += `
                                            <div class="border rounded p-2.5 cursor-pointer mb-1.5 transition-all animate-fadeIn ${base}" id="appt-${a.id}" onclick="selectAppt(${a.id})">
                                                <div class="flex items-start justify-between gap-2 mb-1">
                                                    <div class="text-[0.84rem] font-semibold">${a.patient}</div>
                                                    <div class="shrink-0">
                                                        ${badge(a.status)}
                                                    </div>
                                                </div>
                                                <div class="flex flex-col gap-1.5 mt-0.5">
                                                    <div class="text-[0.73rem] ${isSelected ? 'text-primary-foreground/80' : 'text-muted-foreground'}">
                                                        <span>{{ __('file.time_slot') }}: ${a.slot_label || a.time}</span>
                                                    </div>
                                                    ${a.paid > 0 ? `<div class="text-[0.7rem] font-medium text-emerald-600 dark:text-emerald-400">{!! $currency_code !!} ${a.paid.toLocaleString()} {{ __('file.paid') }}</div>` : ''}
                                                </div>
                                            </div>`;
                });
            });
            list.innerHTML = html;
        }

        /* ========== SELECT APPOINTMENT ========== */
        function selectAppt(id) {
            selectedApptId = id;
            const a = appointments.find(x => x.id === id);
            if (!a) return;

            const doc = DOCTORS[a.doctor_id] || { name: 'Unknown' };

            document.getElementById("editPlaceholder").style.display = "none";
            document.getElementById("editHeader").classList.remove("hidden");
            document.getElementById("editHeader").classList.add("flex");
            document.getElementById("editForm").classList.remove("hidden");
            document.getElementById("editForm").classList.add("flex-1", "overflow-y-auto");
            document.getElementById("editActionBar").classList.remove("hidden");
            document.getElementById("editActionBar").classList.add("flex");

            document.getElementById("editHeaderTitle").textContent = a.patient;
            document.getElementById("editHeaderSub").textContent = `${doc.name} · ${a.date} · {{ __('file.time_slot') }}: ${a.slot_label || a.time}`;
            document.getElementById("editHeaderBadge").innerHTML = badge(a.status);

            // Patient Summary & Profile Link
            document.getElementById("dispPatientName").textContent = a.patient;
            document.getElementById("dispPatientMRN").textContent = a.patient_mrn;
            document.getElementById("dispPatientPhone").textContent = a.contact || '—';
            document.getElementById("dispPatientDOB").textContent = a.patient_dob;
            document.getElementById("patientProfileLink").href = `../patients/${a.patient_id}`;

            // Basic Fields
            const isRestrictedDoctor = CURRENT_USER.roles.includes('doctor') &&
                !CURRENT_USER.roles.includes('admin') &&
                !CURRENT_USER.roles.includes('primary_care_provider');

            const restrictedStatuses = ['approved', 'paid', 'running', 'completed'];
            const isApprovedOrBeyond = restrictedStatuses.includes(a.status);

            formInputs.forEach(input => {
                if (isRestrictedDoctor) {
                    input.disabled = (input.id !== 'fDoctorNotes');
                } else {
                    input.disabled = false;
                }

                // Extra restriction: once approved, doctor/date/time cannot be changed
                if (['fDoctor', 'fDate', 'fTime'].includes(input.id) && isApprovedOrBeyond) {
                    input.disabled = true;
                }
            });

            document.getElementById("fDate").value = a.date;
            if (document.getElementById("fSpecialization")) {
                document.getElementById("fSpecialization").value = a.specialization_id || "";
            }
            // Handle slot loading and population
            loadSlots(a.doctor_id, a.date, () => {
                const elTime = document.getElementById("fTime");
                if (elTime) {
                    // Try to match by the composite slot value first
                    const val = a.slot_val || (a.start_time && a.end_time ? `${a.start_time}|${a.end_time}` : '');

                    // If the slot isn't in the list (e.g. for past or approved appts where slot is "taken"), append it
                    if (val && !Array.from(elTime.options).some(o => o.value === val)) {
                        const opt = document.createElement('option');
                        opt.value = val;
                        opt.textContent = a.slot_label || a.time;
                        elTime.appendChild(opt);
                    }

                    if (val && Array.from(elTime.options).some(o => o.value === val)) {
                        elTime.value = val;
                    } else if (a.time && Array.from(elTime.options).some(o => o.value.startsWith(a.time))) {
                        // Fallback: match by start time
                        const match = Array.from(elTime.options).find(o => o.value.startsWith(a.time));
                        if (match) elTime.value = match.value;
                    }
                }
            });

            if (document.getElementById("fDuration")) document.getElementById("fDuration").value = a.duration;
            if (document.getElementById("fVisitType")) document.getElementById("fVisitType").value = a.visit_type;
            
            // Set doctor and specialization
            const fDoctor = document.getElementById("fDoctor");
            if (fDoctor) {
                // If the doctor isn't in the current filtered list, we might need to reload the list first or just append the doctor
                if (a.doctor_id && !Array.from(fDoctor.options).some(o => o.value == a.doctor_id)) {
                    loadFilteredDoctors('f').then(() => {
                        fDoctor.value = a.doctor_id || "";
                    });
                } else {
                    fDoctor.value = a.doctor_id || "";
                }
            }

            if (document.getElementById("fRoom")) document.getElementById("fRoom").value = (a.room && a.room !== 'Room —') ? a.room : "";
            document.getElementById("fComplaint").value = a.complaint;
            document.getElementById("fDoctorNotes").value = a.doctor_notes || "";
            document.getElementById("fAdminNotes").value = a.admin_notes || "";
            if (document.getElementById("fStatus")) document.getElementById("fStatus").value = a.status;
            if (document.getElementById("fFee")) document.getElementById("fFee").value = a.fee || 0;
            if (document.getElementById("fAgeGroup")) document.getElementById("fAgeGroup").value = a.age_group_id || "";
            if (document.getElementById("fLanguage")) document.getElementById("fLanguage").value = a.preferred_language_id || "";

            // Property Info
            if (document.getElementById("dispCreatedAt")) document.getElementById("dispCreatedAt").textContent = a.created_at;
            if (document.getElementById("dispApprovedBy")) document.getElementById("dispApprovedBy").textContent = a.approved_by;

            // Medical Record Section
            const medSec = document.getElementById("medicalRecordSection");
            const canModifyMedical = CURRENT_USER.roles.includes('admin') ||
                CURRENT_USER.roles.includes('primary_care_provider') ||
                (CURRENT_USER.doctor_id && String(CURRENT_USER.doctor_id) == String(a.doctor_id));

            if (allowedStatuses.includes(a.status) || a.doctor_id) {
                medSec.classList.remove("hidden");
                renderTreatments(a.treatments, a.total_treatments_cost);
                renderPrescription(a.prescription, a.create_prescription_url, a.doctor_id);

                const btnAddTreatment = document.getElementById("btnAddTreatment");
                if (btnAddTreatment) {
                    btnAddTreatment.classList.toggle("hidden", !canModifyMedical);
                }
            } else {
                medSec.classList.add("hidden");
            }

            updateQuickBtns(a.status, a.doctor_id);
            renderApptList();
            switchMobileTab('panelDetail');
        }

        function renderTreatments(treatments, total) {
            const container = document.getElementById("treatmentsTable");
            if (!treatments || treatments.length === 0) {
                container.innerHTML = '<div class="p-3 text-center text-[0.7rem] text-muted-foreground italic">{{ __('file.no_treatments_added_yet') }}</div>';
                return;
            }

            let html = `<table class="w-full text-[0.72rem]">
                                                                                                                                                                                                                <thead class="bg-muted border-b border-border">
                                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                                        <th class="px-2 py-1.5 text-left font-bold opacity-70">{{ __('file.treatment') }}</th>
                                                                                                                                                                                                                        <th class="px-2 py-1.5 text-right font-bold opacity-70">{{ __('file.total') }}</th>
                                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                                </thead>
                                                                                                                                                                                                                <tbody class="divide-y divide-border/50">`;

            treatments.forEach(t => {
                html += `<tr>
                                                                                                                                                                                                                    <td class="px-2 py-2">
                                                                                                                                                                                                                        <div class="font-bold text-foreground/90">${t.name}</div>
                                                                                                                                                                                                                        ${t.code ? `<div class="text-[0.65rem] text-muted-foreground">${t.code} • {{ __('file.qty') }} ${t.qty}</div>` : `<div class="text-[0.65rem] text-muted-foreground">{{ __('file.qty') }} ${t.qty}</div>`}
                                                                                                                                                                                                                    </td>
                                                                                                                                                                                                                    <td class="px-2 py-2 text-right font-bold text-foreground/90">{!! $currency_code !!} ${t.total.toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                                                                                                                                                                                                                </tr>`;
            });

            html += `</tbody>
                                                                                                                                                                                                                <tfoot class="bg-muted/30 border-t border-border">
                                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                                        <td class="px-2 py-1.5 font-bold text-muted-foreground uppercase text-[0.65rem]">{{ __('file.total') }}</td>
                                                                                                                                                                                                                        <td class="px-2 py-1.5 text-right font-bold text-primary">{!! $currency_code !!} ${total.toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                                </tfoot>
                                                                                                                                                                                                            </table>`;
            container.innerHTML = html;
        }

        function renderPrescription(p, createUrl, apptDoctorId) {
            const container = document.getElementById("prescriptionSummary");
            const canModifyMedical = CURRENT_USER.roles.includes('admin') ||
                CURRENT_USER.roles.includes('primary_care_provider') ||
                (CURRENT_USER.doctor_id && String(CURRENT_USER.doctor_id) === String(apptDoctorId));
            if (!p) {
                container.innerHTML = `
                                        <div class="text-center py-2">
                                            <p class="text-[0.7rem] text-muted-foreground mb-2">{{ __('file.no_medications_prescribed_yet') }}.</p>
                                            ${canModifyMedical ? `<button onclick="openPrescriptionModal(${selectedApptId})" class="btn-outline-sm h-[24px] px-3">{{ __('file.create_prescription') }}</button>` : ''}
                                        </div>`;
                return;
            }

            container.innerHTML = `
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="text-[0.75rem] font-bold text-foreground">#${p.id}</span>
                                                <span class="text-[0.68rem] text-muted-foreground">${p.date}</span>
                                            </div>
                                            <div class="text-[0.7rem] text-foreground/80 line-clamp-2 italic leading-relaxed">${p.diagnosis || ''}</div>
                                        </div>
                                        <div class="flex flex-col gap-1 shrink-0">
                                            ${canModifyMedical ? `<button onclick="openPrescriptionModal(${selectedApptId}, ${p.id})" class="btn-outline-sm h-[22px] px-2 w-full text-center">{{ __('file.edit') }}</button>` : ''}
                                        </div>
                                    </div>`;
        }

        function updateQuickBtns(status, doctorId = null) {
            const btnStart = document.getElementById("btnStart");
            const btnComplete = document.getElementById("btnComplete");

            if (btnStart) {
                const isApproved = status === "approved";
                btnStart.classList.toggle("hidden", !isApproved);
                btnStart.classList.toggle("inline-flex", isApproved);

                if (isApproved && doctorId) {
                    const hasRunning = appointments.some(x => x.doctor_id === +doctorId && x.status === 'running');
                    btnStart.disabled = hasRunning;
                    btnStart.classList.toggle("opacity-50", hasRunning);
                    btnStart.classList.toggle("cursor-not-allowed", hasRunning);
                    btnStart.title = hasRunning ? "{{ __('file.doctor_has_running_appointment') ?? 'Doctor already has a running appointment' }}" : "";
                } else {
                    btnStart.disabled = false;
                    btnStart.classList.remove("opacity-50", "cursor-not-allowed");
                    btnStart.title = "";
                }
            }

            if (btnComplete) {
                const allowed = ["approved", "paid", "running"];
                btnComplete.classList.toggle("hidden", !allowed.includes(status));
                btnComplete.classList.toggle("inline-flex", allowed.includes(status));
            }
        }

        function onStatusChange() {
            const el = document.getElementById("fStatus");
            if (!el) return;
            const s = el.value;
            document.getElementById("editHeaderBadge").innerHTML = badge(s);
            const a = appointments.find(x => x.id === selectedApptId);
            updateQuickBtns(s, a ? a.doctor_id : null);
        }

        /* ========== QUICK ACTIONS ========== */
        async function quickAction(action) {
            const a = appointments.find(x => x.id === selectedApptId);
            if (!a) return;

            const targetStatus = { start: "running" }[action];
            if (!targetStatus) return;

            if (!confirm(`{{ __('file.are_you_sure_change_status') }} ${statusLabel(targetStatus)}?`)) return;

            try {
                const url = `{{ route("queues.start", ":id") }}`.replace(':id', a.id);
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || "Failed to update status");

                a.status = targetStatus;
                const el = document.getElementById("fStatus");
                if (el) el.value = a.status;
                onStatusChange();
                renderApptList(); renderQueue();
                toast("{{ __('file.status_updated') }}", "success", `${a.patient} → ${statusLabel(a.status)}`);
            } catch (err) {
                console.error(err);
                toast("{{ __('file.error_updating_status') }}", "error", err.message);
            }
        }

        /* ========== SAVE ========== */
        async function saveAppt() {
            const a = appointments.find(x => x.id === selectedApptId);
            if (!a) return;

            if (!confirm("{{ __('file.are_you_sure_save_changes_appointment') }}")) return;

            const btnGroup = document.querySelector('#editActionBar .flex:last-child');
            const saveBtn = btnGroup.querySelector('button:last-child');
            const originalText = saveBtn.textContent;

            try {
                saveBtn.disabled = true;
                saveBtn.textContent = "{{ __('file.saving') }}...";

                const formData = {
                    date: document.getElementById("fDate").value,
                    slot: document.getElementById("fTime").value,
                    doctor_id: document.getElementById("fDoctor").value,
                    reason_for_visit: document.getElementById("fComplaint").value.trim(),
                    doctor_notes: document.getElementById("fDoctorNotes").value.trim(),
                    admin_notes: document.getElementById("fAdminNotes").value.trim(),
                    status: document.getElementById("fStatus") ? document.getElementById("fStatus").value : a.status,
                    duration_minutes: document.getElementById("fDuration") ? document.getElementById("fDuration").value : (a.duration_minutes || 15),
                    appointment_type: a.appointment_type || 'specific',
                    specialization_id: document.getElementById("fSpecialization") ? document.getElementById("fSpecialization").value : (a.specialization_id || null),
                    age_group_id: document.getElementById("fAgeGroup").value,
                    preferred_language_id: document.getElementById("fLanguage").value,
                    _token: '{{ csrf_token() }}'
                };

                const response = await fetch(`{{ url('appointments') }}/${a.id}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || "Failed to save appointment");
                }

                // Update local data
                Object.assign(a, data.appointment);

                renderApptList();
                renderQueue();
                onStatusChange(); // Update badge/buttons

                toast("{{ __('file.appointment_saved') }}", "success", `#${a.id} {{ __('file.updated_successfully') }}.`);
            } catch (err) {
                console.error(err);
                toast("{{ __('file.error_saving') }}", "error", err.message);
            } finally {
                saveBtn.disabled = false;
                saveBtn.textContent = originalText;
            }
        }

        function discardEdit() {
            if (!confirm("{{ __('file.confirm_discard_changes') }}")) return;
            const a = appointments.find(x => x.id === selectedApptId);
            if (a) selectAppt(a.id);
            toast("{{ __('file.changes_discarded') }}", "info");
        }

        /* ========== QUEUE ========== */
        function renderQueue() {
            const list = document.getElementById("queueList");
            let queue = appointments.filter(a => ["approved", "paid", "running"].includes(a.status));
            if (activeDocFilter !== "all") queue = queue.filter(a => a.doctor_id === +activeDocFilter);

            // Sort: Priority to running, then by queue number, then by time
            queue.sort((a, b) => {
                if (a.status === 'running' && b.status !== 'running') return -1;
                if (a.status !== 'running' && b.status === 'running') return 1;

                // Sort by queue number if present (treat missing as infinity)
                const qa = a.queue_number || Infinity;
                const qb = b.queue_number || Infinity;

                if (qa !== qb) {
                    return qa - qb;
                }

                // Fallback to time
                return a.time.localeCompare(b.time);
            });

            const waiting = queue.filter(a => a.status === "waiting").length;

            if (!queue.length) {
                list.innerHTML = `<div class="text-center py-16 text-muted-foreground">
                                                                                                                                                                                                            <div class="text-4xl mb-3 opacity-20">📋</div>
                                                                                                                                                                                                            <p class="text-sm font-medium">{{ __('file.queue_is_empty') }}</p>
                                                                                                                                                                                                        </div>`;
                return;
            }

            // Group by doctor
            const groups = {};
            queue.forEach(a => {
                if (!groups[a.doctor_id]) groups[a.doctor_id] = [];
                groups[a.doctor_id].push(a);
            });

            // Track if a doctor already has a "running" appointment
            const runningDocs = new Set();
            queue.forEach(a => {
                if (a.status === 'running') runningDocs.add(a.doctor_id);
            });

            let html = "";
            Object.keys(groups).forEach(dId => {
                const doc = DOCTORS[dId] || { name: 'Unknown', dotClass: 'bg-gray-400', room: '—' };
                const hasRunning = runningDocs.has(+dId);

                // Render Doctor Header
                html += `<div class="text-[0.67rem] font-semibold uppercase tracking-[0.8px] text-muted-foreground pt-2.5 pb-2 flex items-center gap-1.5 after:content-[''] after:flex-1 after:h-px after:bg-border transition-colors duration-300 mt-2 first:mt-0">
                                        <span class="w-1.5 h-1.5 rounded-full ${doc.dotClass} inline-block"></span>Dr. ${doc.name}
                                    </div>`;

                let pos = 1;
                html += groups[dId].map(a => {
                    const isCurrent = a.status === "running";
                    const qNum = a.queue_number || pos++;

                    const borderCls = isCurrent
                        ? "border-emerald-500/40 bg-emerald-500/[0.04] dark:bg-emerald-500/[0.08]"
                        : "border-border hover:border-muted-foreground/30 bg-card transition-colors";

                    const numCls = isCurrent
                        ? "text-emerald-600 dark:text-emerald-400"
                        : "text-muted-foreground";

                    let actionHtml = "";
                    if (isCurrent) {
                        actionHtml = `<span class="inline-flex items-center justify-center h-[26px] px-2.5 text-[0.6rem] font-bold uppercase text-amber-600 bg-amber-50 dark:bg-amber-500/10 rounded border border-amber-200/50 dark:border-amber-500/20"><span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span>{{ __('file.currently_serving') }}</span>`;
                    } else if (["approved", "paid"].includes(a.status)) {
                        const disabled = hasRunning ? 'disabled opacity-50 cursor-not-allowed' : '';
                        actionHtml = `<button onclick="event.stopPropagation(); if(!this.disabled) callInPatient(${a.id})"
                                            ${disabled}
                                            class="inline-flex items-center justify-center h-[26px] px-3 font-semibold text-[0.65rem] uppercase tracking-wider bg-primary/10 text-primary ${hasRunning ? '' : 'hover:bg-primary/20 hover:text-primary-foreground'} transition-colors rounded">
                                            {{ __('file.call_in') }}
                                        </button>`;
                    }

                    return `
                                            <div class="group border rounded-xl p-3.5 mb-2.5 cursor-pointer transition-all ${borderCls}" onclick="selectAppt(${a.id})">
                                                <div class="flex items-center gap-3">
                                                    <div class="text-3xl font-black ${numCls} w-12 shrink-0 font-mono tracking-tighter text-center">
                                                        #${qNum}
                                                    </div>
                                                    <div class="flex-1 min-w-0 flex flex-col justify-center">
                                                        <h4 class="text-[0.95rem] font-bold text-foreground leading-tight truncate mb-1.5">${a.patient}</h4>
                                                        <div class="mb-2.5">
                                                            <span class="text-[0.68rem] font-mono text-muted-foreground bg-muted px-1.5 py-0.5 rounded border border-border/50">${a.slot_label || a.time}</span>
                                                        </div>
                                                        <div class="flex items-center">
                                                            ${actionHtml}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>`;
                }).join("");
            });

            list.innerHTML = html;
        }

        async function callInPatient(id) {
            const a = appointments.find(x => x.id === id);
            if (!a) return;
            if (!confirm(`{{ __('file.confirm_call_in') }} ${a.patient}?`)) return;

            try {
                const url = `{{ route("queues.start", ":id") }}`.replace(':id', a.id);
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || "Failed to call in patient");

                a.status = "running";
                if (data.queue_number) {
                    a.queue_number = data.queue_number;
                }

                renderApptList(); renderQueue();
                if (selectedApptId === id) selectAppt(id);
                toast("{{ __('file.patient_called_in') }}", "success", `${a.patient} is now ${statusLabel('running')}.`);
            } catch (err) {
                console.error(err);
                toast("{{ __('file.error_updating_status') }}", "error", err.message);
            }
        }
        async function completeAppt(id) {
            const a = appointments.find(x => x.id === id);
            if (!a) return;

            if (!confirm(`{{ __('file.confirm_complete_visit') }} ${a.patient}?`)) return;

            // Check permissions
            const canComplete = CURRENT_USER.roles.includes('admin') ||
                CURRENT_USER.roles.includes('primary_care_provider') ||
                CURRENT_USER.doctor_id === a.doctor_id;

            if (!canComplete) {
                toast("{{ __('file.permission_denied') }}", "error", "{{ __('file.only_assigned_doctor_can_complete') }}");
                return;
            }

            try {
                const url = `{{ route("queues.complete", ":id") }}`.replace(':id', a.id);
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || "Failed to complete appointment");

                a.status = "completed";
                renderApptList(); renderQueue();
                if (selectedApptId === id) selectAppt(id);
                toast("{{ __('file.visit_completed') }}", "success", `${a.patient} marked as Completed.`);
            } catch (err) {
                console.error(err);
                toast("{{ __('file.error_updating_status') }}", "error", err.message);
            }
        }

        /* ========== TREATMENT MODAL ========== */
        function openTreatmentModal() {
            const a = appointments.find(x => x.id === selectedApptId);
            if (!a) return;

            // Update prices for this doctor and hide unassigned
            const prices = DOCTOR_TREATMENT_PRICES[a.doctor_id] || {};
            document.querySelectorAll('.treatment-price-display').forEach(el => {
                const trtId = el.getAttribute('data-treatment-id');
                const labelEl = el.closest('label');

                if (prices[trtId] !== undefined) {
                    const p = prices[trtId];
                    el.innerHTML = `${CURRENCY_CODE} ${parseFloat(p).toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
                    labelEl.style.display = 'flex';
                } else {
                    labelEl.style.display = 'none';
                    const checkbox = labelEl.querySelector('input[type="checkbox"]');
                    if (checkbox) checkbox.checked = false;
                }
            });

            // Pre-check existing treatments
            const checks = document.querySelectorAll('#modalTreatmentList input[type="checkbox"]');
            checks.forEach(c => {
                c.checked = a.treatments.some(at => at.id == c.value);
            });

            document.getElementById("treatmentModal").classList.remove("hidden");
            document.getElementById("treatmentModal").classList.add("flex");
        }

        function closeTreatmentModal() {
            const m = document.getElementById("treatmentModal");
            m.classList.add("hidden");
            m.classList.remove("flex");
        }

        async function saveTreatments() {
            const a = appointments.find(x => x.id === selectedApptId);
            if (!a) return;

            const selectedIds = Array.from(document.querySelectorAll('#modalTreatmentList input:checked')).map(c => c.value);
            const btn = document.getElementById("btnSaveTreatments");
            const originalText = btn.textContent;

            try {
                btn.disabled = true;
                btn.textContent = "{{ __('file.saving') }}...";

                const saveUrl = `{{ route("appointments.treatments.update", ":id") }}`.replace(':id', a.id);
                const response = await fetch(saveUrl, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ treatment_ids: selectedIds })
                });

                if (!response.ok) {
                    const data = await response.json();
                    throw new Error(data.message || "Failed to save treatments");
                }

                toast("{{ __('file.treatments_updated') }}", "success", "Reflecting medical records...");
                closeTreatmentModal();

                await refreshData(document.getElementById("dateSelector")?.value, activeDocFilter);
                if (selectedApptId) selectAppt(selectedApptId);
            } catch (err) {
                console.error(err);
                toast("{{ __('file.error_saving_treatments') }}", "error", err.message);
            } finally {
                btn.disabled = false;
                btn.textContent = originalText;
            }
        }

        /* ========== PRESCRIPTION MODAL ========== */
        function openPrescriptionModal(apptId, prescriptionId = null) {
            const appt = appointments.find(a => a.id === apptId);
            if (!appt) return;

            document.getElementById("p_appt_id").value = apptId;
            document.getElementById("p_prescription_id").value = prescriptionId || "";
            document.getElementById("prescriptionModalTitle").textContent = prescriptionId ? `{{ __('file.update_prescription') }} #${prescriptionId}` : "{{ __('file.create_prescription') }}";

            // Reset fields
            document.getElementById("p_date").value = prescriptionId && appt.prescription ? appt.prescription.date_iso : todayISO();
            document.getElementById("p_type").value = prescriptionId && appt.prescription ? appt.prescription.type : "Standard";
            document.getElementById("p_diagnosis").value = prescriptionId && appt.prescription ? appt.prescription.diagnosis : "";
            document.getElementById("p_notes").value = prescriptionId && appt.prescription ? appt.prescription.notes : "";
            document.getElementById("p_template").value = "";
            document.getElementById("p_med_container").innerHTML = "";

            if (prescriptionId && appt.prescription && appt.prescription.medications) {
                appt.prescription.medications.forEach(m => addMedicationRow(m));
            } else {
                addMedicationRow(); // Add one empty row by default
            }

            switchPrescriptionTab('details');
            document.getElementById("prescriptionModal").classList.remove("hidden");
            document.getElementById("prescriptionModal").classList.add("flex");
        }

        function closePrescriptionModal() {
            document.getElementById("prescriptionModal").classList.add("hidden");
            document.getElementById("prescriptionModal").classList.remove("flex");
        }

        function switchPrescriptionTab(tab) {
            document.querySelectorAll(".p-tab-btn").forEach(btn => {
                btn.classList.remove("border-primary", "text-primary", "bg-accent/5");
                btn.classList.add("border-transparent", "text-muted-foreground");
            });
            document.querySelectorAll(".p-tab-content").forEach(content => content.classList.add("hidden"));

            document.getElementById(`tab-p-${tab}`).classList.add("border-primary", "text-primary", "bg-accent/5");
            document.getElementById(`tab-p-${tab}`).classList.remove("border-transparent", "text-muted-foreground");
            document.getElementById(`p-content-${tab}`).classList.remove("hidden");
        }

        function updateMedId(input) {
            const list = document.getElementById("inventory-list");
            const opt = Array.from(list.options).find(o => o.value === input.value);
            const row = input.closest('.med-row');
            const idInput = row.querySelector('input[name*="[inventory_item_id]"]');
            if (opt) {
                idInput.value = opt.getAttribute('data-id');
            } else {
                idInput.value = "";
            }
        }

        let medRowIdx = 0;
        function addMedicationRow(data = null) {
            const container = document.getElementById("p_med_container");
            const idx = medRowIdx++;
            const div = document.createElement("div");
            div.className = "med-row p-3 border border-border rounded-lg bg-card/50 relative group animate-fadeIn";
            div.innerHTML = `
                <button onclick="this.closest('.med-row').remove()" class="absolute top-2 right-2 text-muted-foreground hover:text-destructive opacity-0 group-hover:opacity-100 transition-opacity">✕</button>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[0.65rem] font-bold uppercase text-muted-foreground mb-1">{{ __('file.medicine_name') }}</label>
                        <input type="text" name="meds[${idx}][name]" value="${(data && data.name) || ''}" oninput="updateMedId(this)" class="form-input text-sm" placeholder="{{ __('file.search_or_type_name') }}" list="inventory-list">
                    </div>
                    <div>
                        <label class="block text-[0.65rem] font-bold uppercase text-muted-foreground mb-1">{{ __('file.dosage') }}</label>
                        <input type="text" name="meds[${idx}][dosage]" value="${(data && data.dosage) || ''}" class="form-input text-sm" placeholder="{{ __('file.dosage_placeholder') }}">
                    </div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mt-3">
                    <div>
                        <label class="block text-[0.65rem] font-bold uppercase text-muted-foreground mb-1">{{ __('file.frequency') }}</label>
                        <input type="text" name="meds[${idx}][frequency]" value="${(data && data.frequency) || ''}" class="form-input text-sm" placeholder="{{ __('file.frequency_placeholder') }}">
                    </div>
                    <div>
                        <label class="block text-[0.65rem] font-bold uppercase text-muted-foreground mb-1">Route</label>
                        <select name="meds[${idx}][route]" class="form-input text-sm">
                            <option value="Oral" ${(data && data.route === 'Oral') ? 'selected' : ''}>Oral</option>
                            <option value="Injection" ${(data && data.route === 'Injection') ? 'selected' : ''}>Injection</option>
                            <option value="Topical" ${(data && data.route === 'Topical') ? 'selected' : ''}>Topical</option>
                            <option value="Inhalation" ${(data && data.route === 'Inhalation') ? 'selected' : ''}>Inhalation</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[0.65rem] font-bold uppercase text-muted-foreground mb-1">{{ __('file.per_day') }}</label>
                        <input type="number" name="meds[${idx}][per_day]" value="${(data && data.per_day) || '1'}" step="0.5" min="0.5" class="form-input text-sm">
                    </div>
                    <div>
                        <label class="block text-[0.65rem] font-bold uppercase text-muted-foreground mb-1">{{ __('file.days') }}</label>
                        <input type="number" name="meds[${idx}][duration_days]" value="${(data && data.duration_days) || ''}" class="form-input text-sm" placeholder="{{ __('file.qty') }}">
                    </div>
                    <div>
                        <label class="block text-[0.65rem] font-bold uppercase text-muted-foreground mb-1">{{ __('file.instructions') }}</label>
                        <input type="text" name="meds[${idx}][instructions]" value="${(data && data.instructions) || ''}" class="form-input text-sm" placeholder="{{ __('file.before_meal_etc') }}">
                    </div>
                </div>
                <input type="hidden" name="meds[${idx}][inventory_item_id]" value="${(data && data.inventory_item_id) || ''}">
            `;
            container.appendChild(div);
        }

        async function applyPrescriptionTemplate(templateId) {
            if (!templateId) return;

            try {
                const response = await fetch(`{{ url('medicine-templates') }}/${templateId}/medications`);
                if (!response.ok) throw new Error("Failed to fetch template medications");
                const medications = await response.json();

                // document.getElementById("p_med_container").innerHTML = ""; // Optional: clear existing
                medications.forEach(m => addMedicationRow(m));
                switchPrescriptionTab('meds');
                toast("{{ __('file.template_applied') }}", "success");
            } catch (err) {
                console.error(err);
                toast("{{ __('file.failed_to_load_template') }}", "error");
            }
        }

        async function savePrescription() {
            const apptId = document.getElementById("p_appt_id").value;
            const prescriptionId = document.getElementById("p_prescription_id").value;
            const btn = document.getElementById("btnSavePrescription");
            const originalText = btn.textContent;

            const meds = [];
            document.querySelectorAll(".med-row").forEach(row => {
                const name = row.querySelector('input[name*="[name]"]').value;
                if (!name) return;
                meds.push({
                    name: name,
                    dosage: row.querySelector('input[name*="[dosage]"]').value,
                    frequency: row.querySelector('input[name*="[frequency]"]').value,
                    route: row.querySelector('select[name*="[route]"]').value,
                    per_day: row.querySelector('input[name*="[per_day]"]').value,
                    duration_days: row.querySelector('input[name*="[duration_days]"]').value,
                    instructions: row.querySelector('input[name*="[instructions]"]').value,
                    inventory_item_id: row.querySelector('input[name*="[inventory_item_id]"]').value
                });
            });

            const payload = {
                appointment_id: apptId,
                prescription_date: document.getElementById("p_date").value,
                type: document.getElementById("p_type").value,
                diagnosis: document.getElementById("p_diagnosis").value,
                notes: document.getElementById("p_notes").value,
                medications: meds,
                _token: '{{ csrf_token() }}'
            };

            const url = prescriptionId
                ? `{{ url('prescriptions') }}/${prescriptionId}`
                : `{{ url('prescriptions') }}`;

            const method = prescriptionId ? 'PATCH' : 'POST';

            try {
                btn.disabled = true;
                btn.textContent = "{{ __('file.saving') }}...";

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();
                if (data.success) {
                    toast("{{ __('file.prescription_saved') }}", "success");
                    closePrescriptionModal();
                    await refreshData(document.getElementById("dateSelector")?.value, activeDocFilter);
                    if (selectedApptId) selectAppt(selectedApptId);
                } else {
                    toast("{{ __('file.error_saving') }}", "error", data.message || "{{ __('file.something_went_wrong') }}");
                }
            } catch (err) {
                console.error(err);
                toast("{{ __('file.network_error') }}", "error");
            } finally {
                btn.disabled = false;
                btn.textContent = originalText;
            }
        }


    </script>
@endpush