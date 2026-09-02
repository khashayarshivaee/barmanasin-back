<?php

use App\Http\Controllers\Api\HeaderMenuController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HomeHeroController;
use App\Http\Controllers\Api\HomeIntroController;
use App\Http\Controllers\Api\HomeFeaturedProjectsController;
use App\Http\Controllers\Api\HomeCapabilitiesController;
use App\Http\Controllers\Api\HomeEngineeringApproachController;
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
