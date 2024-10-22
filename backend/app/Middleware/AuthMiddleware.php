<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . "/../Models/User.php";

class AuthMiddleware
{
    private $UserModel;

    public function __construct($pdo)
    {
        $this->UserModel = new User($pdo);
    }

    public function __invoke(Request $request, $handler): Response
    {
        $token = $request->getHeaderLine('Authorization');

        $params = (array)$request->getParsedBody();
        $username = $params['username'];

        if (!$token) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['flag' => 'error', 'message' => 'Unauthorized. Missing Token. Login First.']));
            return $response->withStatus(401)->withHeader('Content-Type', value: 'application/json');
        }

        // $db = Database::getInstance();
        $loggedUser = $this->UserModel->getUserByToken($token);
        // error_log("^^^^^^^^^^^^^^^^^^^^^".var_export($username, true));
        // error_log("^^^^^^^^^^^^^^^^^^^^^".var_export($loggedUser, true));

        if ($loggedUser['username'] != $username) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['flag' => 'error', 'message' => 'Unauthorized. Login First.']));
            return $response->withStatus(401)->withHeader('Content-Type', value: 'application/json');
        }

        if (!$loggedUser) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['flag' => 'error', 'message' => 'Invalid Token. Login Again.']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $request = $request->withAttribute('loggedUser', $loggedUser);

        return $handler->handle($request);
    }
}
