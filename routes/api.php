<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GatewayReadingController;

Route::post('/gateway', [GatewayReadingController::class, 'store']);
