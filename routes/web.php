<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserActivationController;
use App\Http\Controllers\Auth\UserPasswordResetController;

Route::get(
    '/activate/{token}',
    [UserActivationController::class, 'show']
)
    ->where('token', '[a-f0-9]{64}')
    ->middleware('throttle:30,1')
    ->name('user.activation.show');


Route::post(
    '/activate/{token}',
    [UserActivationController::class, 'activate']
)
    ->where('token', '[a-f0-9]{64}')
    ->middleware('throttle:10,1')
    ->name('user.activation.activate');

Route::get(
    '/reset-access/{token}',
    [UserPasswordResetController::class, 'show']
)
    ->where('token', '[a-f0-9]{64}')
    ->middleware('throttle:30,1')
    ->name('user.password-reset.show');


Route::post(
    '/reset-access/{token}',
    [UserPasswordResetController::class, 'reset']
)
    ->where('token', '[a-f0-9]{64}')
    ->middleware('throttle:10,1')
    ->name('user.password-reset.reset');
