<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . "/../Models/User.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware
{
    private $UserModel;
    private $secretKey = 'awesomeANDsecretKEY'; // Replace with a secure key



    public function __construct($pdo)
    {
        $this->UserModel = new User($pdo);
    }

    public function __invoke(Request $request, $handler): Response
    {
        // $token = $request->getHeaderLine('Authorization');
        $authHeader = $request->getHeader('Authorization');

        if (!$authHeader) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['flag' => 'error', 'message' => 'Unauthorized. Missing Token. Login First.']));
            return $response->withStatus(401)->withHeader('Content-Type', value: 'application/json');
        }

        $arr = explode(" ", $authHeader[0]);

        if (!(isset($arr[0]) && isset($arr[1]) && $arr[0] === 'Bearer')) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'Invalid Authorization header format']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $jwt = $arr[1];
        try {
            $decoded = JWT::decode($jwt, new Key($this->secretKey, 'HS256'));

            // Attach user data to request as attribute
            $request = $request->withAttribute('user', [
                'userId' => $decoded->data->userId,
                'username' => $decoded->data->username
            ]);

            // check if the logged in user is same as the one mentioned in the json data
            $params = (array)$request->getParsedBody();
            $paramUsername = $params['username'] ?? '';
            // error_log("&&&&&&&&&&&&&&&&&&&&&&" . var_export($paramUsername, true) . "******" . var_export($decoded->data->username, true));
            // if its empty => no username value has been sent with the http => :)
            if ($paramUsername && $paramUsername != $decoded->data->username) {
                // security alert 
                // logged in user is different from the one trying to join the group
                $response = new \Slim\Psr7\Response();
                $response->getBody()->write(json_encode(['flag' => 'error', 'message' => 'Unauthorized. Only the logged in user can is authorized for this.']));
                return $response->withStatus(401)->withHeader('Content-Type', value: 'application/json');
            }

            return $handler->handle($request);
        } catch (\Firebase\JWT\ExpiredException $e) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'Token expired']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        // $loggedUser = $this->UserModel->getUserByToken($token);

        // if ($loggedUser['username'] != $username) {
        //     $response = new \Slim\Psr7\Response();
        //     $response->getBody()->write(json_encode(['flag' => 'error', 'message' => 'Unauthorized. Login First.']));
        //     return $response->withStatus(401)->withHeader('Content-Type', value: 'application/json');
        // }

        // if (!$loggedUser) {
        //     $response = new \Slim\Psr7\Response();
        //     $response->getBody()->write(json_encode(['flag' => 'error', 'message' => 'Invalid Token. Login Again.']));
        //     return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        // }

        // $request = $request->withAttribute('loggedUser', $loggedUser);

        // return $handler->handle($request);
    }
}
