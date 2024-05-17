<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (config('app.env') == 'local' || config('app.env') == 'staging') {
        return ['Laravel' => app()->version()];
    } else {
        return ['Oi!' => 'What you looking at?!'];
    }
});
