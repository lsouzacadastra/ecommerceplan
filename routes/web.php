<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/painel', 'PainelController@index');
$router->get('/painel/painel', 'PainelController@painel');
$router->get('/painel/painel', 'PainelController@painel');
$router->get('/painel/migracao', 'PainelController@migracao');
$router->get('/painel/multiplicador', 'PainelController@multiplicador');
$router->get('/painel/multiplo', 'PainelController@graficoMultiplo');

$router->post('/tracker/collect', 'TrackerController@collect');
$router->get('/tracker/getatag', 'TrackerController@geraTag');

$router->get('/tracker/collect', 'TrackerController@collect');
$router->get('/tracker', 'TrackerController@index');