<?php

declare(strict_types=1);

$app->get('/', app\controller\Home::class . ':home');
$app->get('/home', app\controller\Home::class . ':home');
$app->get('/login', app\controller\Login::class . ':login');

$app->group('/usuario', function (Slim\Routing\RouteCollectorProxy $group) {
    $group->get('/lista', app\controller\User::class . ':list');
    $group->get('/detalhes/{id}', app\controller\User::class . ':details');
    $group->get('/detalhes', app\controller\User::class . ':details');
    $group->post('/insert', app\controller\User::class . ':insert');
    $group->post('/update', app\controller\User::class . ':update');
    $group->post('/delete', app\controller\User::class . ':delete');
    $group->post('/listingdata', app\controller\User::class . ':listingdata');
});