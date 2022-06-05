<?php

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    if (config('app.env') == 'local') {
        return 'API v1, Laravel ' . app()->version();
    } else {
        return redirect('https://www.pieronanni.com');
    }
});


########
######## Yahtzee

// $router->group(['prefix' => 'api/v1'], function () use ($router) { // api/v1
//     $router->group(['prefix' => 'yahtzee'], function () use ($router) { // api/v1/yahtzee

//         $router->post('session', 'YahtzeeSessionController@store'); ##### Create new session - POST api/v1/yahtzee/session
//         $router->get('sessions', 'YahtzeeSessionController@index'); ##### Get all sessions - GET api/v1/yahtzee/sessions
//         $router->get('session/{id}/players', 'YahtzeeSessionController@players'); ##### Get all players of a session - GET api/v1/yahtzee/session/players

//         $router->post('session/player', 'YahtzeePlayerController@store'); ##### Create new player - POST api/v1/yahtzee/session/player
//         $router->post('session/{session_id}/player/{player_id}/leave', 'YahtzeePlayerController@player_leave'); ##### Leave player from a session - GET api/v1/yahtzee/session/players

//     });
// });
