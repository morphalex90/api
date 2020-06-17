<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// $router->get('/','StarController@averageStar'); ##### Return average value
// $router->get('create','StarController@createStarr'); ##### Create new star

########
######## TOOLS

$router->group(['prefix' => 'api/v1'], function () use ($router) { // api/v1
    $router->group(['prefix' => 'tools'], function () use ($router) { // api/v1/tools

        $router->post('star','StarController@createStar'); ##### Create new star - POST api/v1/tools/star
        $router->get('average_star','StarController@averageStar'); ##### Return average value - GET api/v1/tools/average_star
    });
});


// $router->group(['prefix' => 'api/v1','namespace' => 'router\Http\Controllers'], function($router) {
//   $router->post('car','CarController@createCar');
//   $router->put('car/{id}','CarController@updateCar');
    
//   $router->delete('car/{id}','CarController@deleteCar');
//   $router->get('car','CarController@index');
// });
