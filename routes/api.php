<?php

use App\Http\Controllers\Tools\ScanController;
use App\Http\Controllers\Tools\StarController;
use Illuminate\Support\Facades\Route;

//////////// TOOLS

Route::group(['prefix' => 'v1'], function () {
    Route::group(['prefix' => 'tools'], function () {

        Route::post('star', [StarController::class, 'store']); // Create new star - POST api/v1/tools/star
        Route::get('average_star', [StarController::class, 'averageStar']); // Return average value - GET api/v1/tools/average_star

        Route::post('scans', [ScanController::class, 'store']);
        Route::get('scans/{scan_uuid}', [ScanController::class, 'show']);
        Route::get('scans/{scan_uuid}/step_links', [ScanController::class, 'stepLinks']);
        Route::get('scans/{scan_uuid}/step_images', [ScanController::class, 'stepImages']);
        Route::get('scans/{scan_uuid}/step_headings', [ScanController::class, 'stepHeadings']);
        Route::get('scans/{scan_uuid}/step_meta', [ScanController::class, 'stepMeta']);
        Route::get('scans/{scan_uuid}/step_robots', [ScanController::class, 'stepRobots']);
        Route::get('scans/{scan_uuid}/step_sitemap', [ScanController::class, 'stepSitemap']);
        Route::get('scans/{scan_uuid}/step_others', [ScanController::class, 'stepOthers']);
        Route::get('scans/{scan_uuid}/step_structured_data', [ScanController::class, 'stepStructuredData']);
    });
});
