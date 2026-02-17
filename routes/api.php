<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsAppFlowController;

Route::post('/whatsapp/flows/endpoint', [WhatsAppFlowController::class, 'handle'])
    ->name('whatsapp.flows.endpoint');
