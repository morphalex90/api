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

        $router->post('step_link','StarController@stepLink'); ##### POST api/v1/tools/step_link
        $router->post('step_image','StarController@stepImage'); ##### POST api/v1/tools/step_image
        $router->post('step_heading','StarController@stepHeading'); ##### POST api/v1/tools/step_heading
        $router->post('step_meta','StarController@stepMeta'); ##### POST api/v1/tools/step_meta
        $router->post('step_robots','StarController@stepRobots'); ##### POST api/v1/tools/step_robots
        $router->post('step_sitemap','StarController@stepSitemap'); ##### POST api/v1/tools/step_sitemap
        $router->post('step_others','StarController@stepOthers'); ##### POST api/v1/tools/step_others
        $router->post('step_structured_data','StarController@stepStructuredData'); ##### POST api/v1/tools/step_structured_data
    });
});
