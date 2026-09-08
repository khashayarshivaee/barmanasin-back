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
use App\Http\Controllers\Api\MailMessageController;
use App\Http\Controllers\Api\MailMessageSeenController;
use App\Http\Controllers\Api\MailMessageStarredController;
use App\Http\Controllers\Api\MailStarredController;
use App\Http\Controllers\Api\MailArchiveController;
use App\Http\Controllers\Api\MailMessageArchiveController;
use App\Http\Controllers\Api\MailTrashController;
use App\Http\Controllers\Api\MailMessageTrashController;
use App\Http\Controllers\Api\MailSendController;
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

Route::get('/mail/messages/{uid}', MailMessageController::class)
    ->whereNumber('uid');

Route::patch(
    '/mail/messages/{uid}/seen',
    MailMessageSeenController::class,
)->whereNumber('uid');

Route::patch(
    '/mail/messages/{uid}/starred',
    MailMessageStarredController::class,
)->whereNumber('uid');

Route::get('/mail/starred', MailStarredController::class);


Route::get('/mail/archive', MailArchiveController::class);

Route::patch(
    '/mail/messages/{uid}/archive',
    MailMessageArchiveController::class,
)->whereNumber('uid');

Route::get(
    '/mail/trash',
    MailTrashController::class,
);

Route::patch(
    '/mail/messages/{uid}/trash',
    MailMessageTrashController::class,
)->whereNumber('uid');

Route::post(
    '/mail/send',
    MailSendController::class,
)->middleware('throttle:10,1');
