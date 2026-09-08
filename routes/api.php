<?php

use App\Http\Controllers\Api\HeaderMenuController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HomeHeroController;
use App\Http\Controllers\Api\HomeIntroController;
use App\Http\Controllers\Api\HomeFeaturedProjectsController;
use App\Http\Controllers\Api\HomeCapabilitiesController;
use App\Http\Controllers\Api\HomeEngineeringApproachController;
use App\Http\Controllers\Api\HomeContactSectionController;
use App\Http\Controllers\Api\ContactInquiryController;
use App\Http\Controllers\Api\SiteFooterController;
use App\Http\Controllers\Api\HomeImageShowcaseController;
use App\Http\Controllers\Api\MailAuthController;
use App\Http\Controllers\Api\MailInboxController;
Route::get('/header/menu', [HeaderMenuController::class, 'index'])
    ->name('header.menu');


Route::get('/home/hero', HomeHeroController::class);

Route::get('/home/intro', [HomeIntroController::class, 'show']);

Route::get('/home/featured-projects', HomeFeaturedProjectsController::class);

Route::get('/home/capabilities', HomeCapabilitiesController::class);



Route::get(
    '/home/engineering-approach',
    [HomeEngineeringApproachController::class, 'index']
);

Route::get(
    '/home/contact',
    [HomeContactSectionController::class, 'index']
);

Route::post(
    '/contact/inquiries',
    [ContactInquiryController::class, 'store']
)->middleware('throttle:5,1');

Route::get(
    '/footer',
    [SiteFooterController::class, 'index']
);

Route::get('/home/image-showcase', [HomeImageShowcaseController::class, 'index']);

Route::prefix('mail/auth')->group(function () {
    Route::post('/login', [MailAuthController::class, 'login'])
        ->middleware('throttle:5,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [MailAuthController::class, 'me']);

        Route::post('/logout', [MailAuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/mail/inbox', MailInboxController::class);
});
