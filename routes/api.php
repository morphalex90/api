<?php

use App\Http\Controllers\ThreadController;
use App\Http\Controllers\ToolsScanController;
use App\Http\Controllers\ToolsStarController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

########
######## TOOLS

Route::group(['prefix' => 'v1'], function () {
    Route::group(['prefix' => 'tools'], function () {

        Route::post('star', [ToolsStarController::class, 'store']); ##### Create new star - POST api/v1/tools/star
        Route::get('average_star', [ToolsStarController::class, 'averageStar']); ##### Return average value - GET api/v1/tools/average_star

        Route::post('scans', [ToolsScanController::class, 'store']);
        Route::get('scans/{scan_uuid}', [ToolsScanController::class, 'show']);
        Route::get('scans/{scan_uuid}/step_links', [ToolsScanController::class, 'stepLinks']);
        Route::get('scans/{scan_uuid}/step_images', [ToolsScanController::class, 'stepImages']);
        Route::get('scans/{scan_uuid}/step_headings', [ToolsScanController::class, 'stepHeadings']);
        Route::get('scans/{scan_uuid}/step_meta', [ToolsScanController::class, 'stepMeta']);
        Route::get('scans/{scan_uuid}/step_robots', [ToolsScanController::class, 'stepRobots']);
        Route::get('scans/{scan_uuid}/step_sitemap', [ToolsScanController::class, 'stepSitemap']);
        Route::get('scans/{scan_uuid}/step_others', [ToolsScanController::class, 'stepOthers']);
        Route::get('scans/{scan_uuid}/step_structured_data', [ToolsScanController::class, 'stepStructuredData']);
    });

    Route::group(['prefix' => 'chat'], function () {
        Route::get('threads', [ThreadController::class, 'index']);
        Route::get('threads/{thread_id}', [ThreadController::class, 'show']);
        Route::get('threads/{thread_id}/messages', [ThreadController::class, 'messages']);
    });
});
