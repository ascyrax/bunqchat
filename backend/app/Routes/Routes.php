<?php
// app/Routes/chatRoutes.php

require_once __DIR__ . '/../Controllers/GroupController.php';
require_once __DIR__ . '/../Controllers/MessageController.php';
require_once __DIR__ . '/../Controllers/UserController.php';
require_once __DIR__ . '/../Controllers/AuthController.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../Middleware/JsonBodyParserMiddleware.php';



function chatRoutes($app, $pdo)
{
    $AuthController = new AuthController($pdo);
    $GroupController = new GroupController($pdo);
    $MessageController = new MessageController($pdo);
    $UserController = new UserController($pdo);

    // Routes
    $app->post('/register', function ($request, $response, $args) use ($AuthController) {
        return $AuthController->register($request, $response, $args);
    });
    $app->post('/login', function ($request, $response, $args) use ($AuthController) {
        return $AuthController->login($request, $response, $args);
    });

    // Protected Routes
    $app->group('/api', function ($group) use ($GroupController, $UserController, $MessageController) {

        $group->get('/', function ($request, $response) {
            $response->getBody()->write('home page');
            return $response;
        });

        // Chat group routes
        $group->post('/groups', function ($request, $response) use ($GroupController) {
            return $GroupController->createGroup($request, $response);
        })->add(new JsonBodyParserMiddleware('groupName'));

        // create new user
        $group->post('/users', function ($request, $response) use ($UserController) {
            return $UserController->createUser($request, $response);
        })->add(new JsonBodyParserMiddleware('username'));

        // create new user
        $group->post('/join', function ($request, $response) use ($UserController) {
            return $UserController->joinGroup($request, $response);
        })->add(new JsonBodyParserMiddleware('username'));

        $group->get('/groups', function ($request, $response) use ($GroupController) {
            return $GroupController->getAllGroups($request, $response);
        });

        // Message routes
        // send a message
        $group->post('/messages', function ($request, $response) use ($MessageController) {
            return $MessageController->sendMessage($request, $response);
        });

        $group->get('/messages/{group_id}', function ($request, $response, $args) use ($MessageController) {
            return $MessageController->getMessages($request, $response, $args);
        });
    })->add(new AuthMiddleware($pdo));

    $app->addBodyParsingMiddleware();

    // $errorMiddleware = $app->addErrorMiddleware(true, true, true);

    // // Optional: Customize the error handler
    // $errorMiddleware->setDefaultErrorHandler(function (
    //     Psr\Http\Message\ServerRequestInterface $request,
    //     Throwable $exception,
    //     bool $displayErrorDetails,
    //     bool $logErrors,
    //     bool $logErrorDetails
    // ) use ($app) {
    //     $response = new Slim\Psr7\Response();
    //     $response->getBody()->write(json_encode([
    //         'error' => $exception->getMessage()
    //     ]));
    //     return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    // });
}
