<?php
// app/Routes/chatRoutes.php

require_once __DIR__ . '/../Controllers/ChatGroupController.php';
require_once __DIR__ . '/../Controllers/MessageController.php';

use App\Middleware\JsonBodyParserMiddleware;


function chatRoutes($app, $pdo)
{
    $chatGroupController = new ChatGroupController($pdo);
    $messageController = new MessageController($pdo);

    $app->get('/', function ($request, $response) {
        $response->getBody()->write('home page');
        return $response;
    });

    // Chat group routes
    $app->post('/groups', function ($request, $response) use ($chatGroupController) {
        return $chatGroupController->createGroup($request, $response);
    })->add(new JsonBodyParserMiddleware());

    $app->get('/groups', function ($request, $response) use ($chatGroupController) {
        return $chatGroupController->getAllGroups($request, $response);
    });

    // Message routes
    $app->post('/messages', function ($request, $response) use ($messageController) {
        return $messageController->sendMessage($request, $response);
    });

    $app->get('/messages/{group_id}', function ($request, $response, $args) use ($messageController) {
        return $messageController->getMessages($request, $response, $args);
    });

    $app->addBodyParsingMiddleware();
}
