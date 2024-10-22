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

        if (!$token) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['flag' => 'error', 'message' => 'Unauthorized. Missing Token. Login First.']));
            return $response->withStatus(401)->withHeader('Content-Type', value: 'application/json');
        }

        // $db = Database::getInstance();
        $user = $this->UserModel->getUserByToken($token);

        if (!$user) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['flag' => 'error', 'message' => 'Invalid Token. Login Again.']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $request = $request->withAttribute('user', $user);

        return $handler->handle($request);
    }
}
