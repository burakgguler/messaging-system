<?php

use App\Http\Controllers\Api\MessageController;
use Illuminate\Support\Facades\Route;

Route::get('/messages/sent', [MessageController::class, 'sent']);
