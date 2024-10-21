<?php
// app/Routes/chatRoutes.php

require_once __DIR__ . '/../Controllers/GroupController.php';
require_once __DIR__ . '/../Controllers/MessageController.php';
require_once __DIR__ . '/../Controllers/UserController.php';

use App\Middleware\JsonBodyParserMiddleware;


function chatRoutes($app, $pdo)
{
    $GroupController = new GroupController($pdo);
    $MessageController = new MessageController($pdo);
    $UserController = new UserController($pdo);

    $app->get('/', function ($request, $response) {
        $response->getBody()->write('home page');
        return $response;
    });

    // Chat group routes
    $app->post('/groups', function ($request, $response) use ($GroupController) {
        return $GroupController->createGroup($request, $response);
    })->add(new JsonBodyParserMiddleware('group_name'));

    // create new user
    $app->post('/users', function ($request, $response) use ($UserController) {
        return $UserController->createUser($request, $response);
    })->add(new JsonBodyParserMiddleware('username'));

    // create new user
    $app->post('/join', function ($request, $response) use ($UserController) {
        return $UserController->joinGroup($request, $response);
    })->add(new JsonBodyParserMiddleware('username'));

    $app->get('/groups', function ($request, $response) use ($GroupController) {
        return $GroupController->getAllGroups($request, $response);
    });

    // Message routes
    // send a message
    $app->post('/messages', function ($request, $response) use ($MessageController) {
        return $MessageController->sendMessage($request, $response);
    });

    $app->get('/messages/{group_id}', function ($request, $response, $args) use ($MessageController) {
        return $MessageController->getMessages($request, $response, $args);
    });



    $app->addBodyParsingMiddleware();
}
