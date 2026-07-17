@extends('layouts.app')

@section('title', __('file.edit_invoice'))

@section('content')
<div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('file.edit_invoice') }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $invoice->invoice_number }}</p>
            </div>
            <a href="{{ route('invoices.show', $invoice) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('file.back_to_invoice') }}
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden">
            <form action="{{ route('invoices.update', $invoice) }}" method="POST" class="p-6 sm:p-8">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label for="invoice_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.invoice_date') }}
                        </label>
                        <input type="date" name="invoice_date" id="invoice_date" 
                               value="{{ $invoice->invoice_date->format('Y-m-d') }}"
                               class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-white transition shadow-sm">
                        @error('invoice_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.due_date') }}
                        </label>
                        <input type="date" name="due_date" id="due_date" 
                               value="{{ $invoice->due_date->format('Y-m-d') }}"
                               class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-white transition shadow-sm">
                        @error('due_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="doctor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.therapist') }}
                        </label>
                        <select name="doctor_id" id="doctor_id" 
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-white transition shadow-sm">
                            <option value="">{{ __('file.select_therapist') }}</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" @selected($invoice->doctor_id == $doctor->id)>
                                    {{ $doctor->full_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('doctor_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="consulate_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.consulate_status') ?? 'Consulate Status' }}
                        </label>
                        <select name="consulate_status" id="consulate_status" 
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-white transition shadow-sm">
                            <option value="pending" @selected($invoice->consulate_status == 'pending')>{{ __('file.pending') }}</option>
                            <option value="consulate" @selected($invoice->consulate_status == 'consulate')>{{ __('file.consulate') ?? 'Consulate' }}</option>
                        </select>
                        @error('consulate_status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-8">
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('file.notes') }}
                    </label>
                    <textarea name="notes" id="notes" rows="4" 
                              class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-white transition shadow-sm resize-none">{{ $invoice->notes }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="reset" class="px-6 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition">
                        {{ __('file.reset') }}
                    </button>
                    <button type="submit" class="px-8 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                        {{ __('file.update_invoice') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
