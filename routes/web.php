<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (config('app.env') == 'local') {
        return 'API v1, Laravel ' . app()->version();
    } else {
        return redirect('https://www.pieronanni.me');
    }
});
