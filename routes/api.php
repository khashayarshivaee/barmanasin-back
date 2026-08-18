<?php

use App\Http\Controllers\Api\HeaderMenuController;
use Illuminate\Support\Facades\Route;

Route::get('/header/menu', [HeaderMenuController::class, 'index'])
    ->name('header.menu');
