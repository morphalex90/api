<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! app()->isProduction()) {
        return ['Laravel' => app()->version()];
    }

    return ['Oi!' => 'What you looking at?!'];

})->name('home');
