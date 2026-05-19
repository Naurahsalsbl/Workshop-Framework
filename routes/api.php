<?php

use App\Http\Controllers\Antrian\AntrianController;
use Illuminate\Support\Facades\Route;

Route::get('/stream', [AntrianController::class, 'stream']);