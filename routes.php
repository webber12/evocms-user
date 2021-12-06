<?php

use EvolutionCMS\EvoUser\Controller;
use Illuminate\Support\Facades\Route;

Route::post('/evocms-user/auth', [Controller::class, 'Auth']);

Route::post('/evocms-user/register', [Controller::class, 'Register']);

Route::post('/evocms-user/profile/{user}', [Controller::class, 'ProfileEdit'])
    ->where('user', '[0-9]+')
    ->middleware('evocms-user-access:ProfileEdit');

Route::get('/evocms-user/profile/{user}', [Controller::class, 'ProfileInfo'])
    ->where('user', '[0-9]+')
    ->middleware('evocms-user-access:ProfileInfo');

