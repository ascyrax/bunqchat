<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Helpers/TokenGenerator.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController
{
    private $UserModel, $tokenGenerator;
    private $secretKey = 'awesomeANDsecretKEY'; // Replace with a secure key


    public function __construct($pdo)
    {
        $this->UserModel = new User($pdo);
        $this->tokenGenerator = new TokenGenerator($pdo);
    }

    // Register a new user
    public function register(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $params = (array)$request->getParsedBody();

        if (empty($params['username']) || empty($params['password'])) {
            $response->getBody()->write(json_encode(['flag' => 'error', 'message' => 'Username and password are required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $username = $params['username'];
        $password = password_hash($params['password'], PASSWORD_BCRYPT);



        try {
            if ($this->UserModel->createUser($username, $password)) {
                $response
                    ->withStatus(201)
                    ->withHeader('Content-Type', 'application/json')
                    ->getBody()
                    ->write(var_export(['flag' => 'success', 'message' => 'New user created successfully.'], true));
            } else {
                $response
                    ->withStatus(500)
                    ->withHeader('Content-Type', 'application/json')
                    ->getBody()
                    ->write(var_export(['flag' => 'error', 'message' => 'Failed to create a new user.'], true));
            }
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) { // Unique constraint violation
                $response
                    ->withStatus(400)
                    ->withHeader('Content-Type', 'application/json')
                    ->getBody()
                    ->write(var_export(['flag' => 'error', 'message' => 'User already exists.'], true));
                return $response;
            }
            throw $e;
        }
        return $response;
    }

    // User login
    public function login(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $params = (array)$request->getParsedBody();

        if (empty($params['username']) || empty($params['password'])) {
            $response->getBody()->write(json_encode(['flag' => 'error', 'message' => 'Username and password are required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $username = $params['username'];
        $password = $params['password'];

        try {
            $user = $this->UserModel->getUserByName($username);
            if (!$user || !password_verify($password, $user['password'])) {
                $response
                    ->withStatus(401)
                    ->withHeader('Content-Type', 'application/json')
                    ->getBody()
                    ->write(var_export(['flag' => 'error', 'message' => 'Invalid username or password.'], true));
                return $response;
            }
        } catch (\PDOException $e) {
            throw $e;
        }

        try {
            // Generate JWT
            $payload = [
                'iss' => 'your_domain.com',    // Issuer
                'aud' => 'your_domain.com',    // Audience
                'iat' => time(),               // Issued at
                'nbf' => time(),               // Not before
                'exp' => time() + (60 * 60),   // Expiration time (e.g., 1 hour)
                'data' => [
                    'userId' => $user['id'],
                    'username' => $user['username']
                ]
            ];

            $jwt = JWT::encode($payload, $this->secretKey, 'HS256');

            // no need to do this :)
            // $result =  $this->UserModel->updateUser($user['id'], $token);

            $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->getBody()
                ->write(var_export(['flag' => 'success', 'message' => 'Login successful.', 'token' => $jwt], true));
            return $response;
        } catch (\PDOException $e) {
            $response
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json')
                ->getBody()
                ->write(var_export(['flag' => 'error', 'message' => 'Login failed.'], true));
            throw $e;
        }
    }
}
