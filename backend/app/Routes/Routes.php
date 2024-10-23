<?php

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

    // create new user
    $app->post('/register', function ($request, $response, $args) use ($AuthController) {
        return $AuthController->register($request, $response, $args);
    });

    $app->post('/login', function ($request, $response, $args) use ($AuthController) {
        return $AuthController->login($request, $response, $args);
    });

    // Protected Routes
    $app->group('', function ($group) use ($GroupController, $UserController, $MessageController) {

        $group->get('/', function ($request, $response) {
            $response->getBody()->write('home page');
            return $response;
        });

        // groups
        $group->post('/groups', function ($request, $response) use ($GroupController) {
            return $GroupController->createGroup($request, $response);
        })->add(new JsonBodyParserMiddleware('groupName'));

        $group->post('/join', function ($request, $response) use ($UserController) {
            return $UserController->joinGroup($request, $response);
        });

        // todo
        $group->get('/groups', function ($request, $response) use ($GroupController) {
            return $GroupController->getAllGroups($request, $response);
        });

        // Messages
        $group->post('/messages', function ($request, $response) use ($MessageController) {
            return $MessageController->sendMessage($request, $response);
        });

        $group->get('/messages/{groupName}', function ($request, $response, $args) use ($MessageController) {
            return $MessageController->getMessages($request, $response, $args);
        });
    })->add(new AuthMiddleware());

    $app->addBodyParsingMiddleware();

    // Note: Make sure to add the CORS middleware before the exception handling middleware and after body parsing middleware.
    $app->add(new \App\Middleware\CorsMiddleware());


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
