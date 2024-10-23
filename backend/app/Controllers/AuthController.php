<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Firebase\JWT\JWT;

require_once __DIR__ . '/../Models/User.php';

class AuthController
{
    private $UserModel, $secretKey = 'awesomeANDsecretKEY';


    public function __construct($pdo)
    {
        $this->UserModel = new User($pdo);
    }

    // Register a new user
    public function register(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $params = (array)$request->getParsedBody();

        if (empty($params['username']) || empty($params['password'])) {
            $response->getBody()->write(json_encode(['flag' => 'error', 'message' => 'username and password are required.']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $username = $params['username'];
        $password = password_hash($params['password'], PASSWORD_BCRYPT);

        list($flag, $e) = $this->UserModel->createUser($username, $password);

        if ($flag) {
            $response->getBody()
                ->write(json_encode(['flag' => 'success', 'message' => 'registration successful.']));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } else {
            // Check if the error is due to a unique constraint violation
            if ($e && $e->getCode() == '23000') {
                $response->getBody()
                    ->write(json_encode(['flag' => 'error', 'message' => 'user already registered.']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            // handle other errors
            $response
                ->getBody()
                ->write(json_encode(['flag' => 'error', 'message' => 'Failed to create a new user.']));
            return $response->withStatus(500)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    // User login
    public function login(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $params = (array)$request->getParsedBody();

        if (empty($params['username']) || empty($params['password'])) {
            $response->getBody()->write(json_encode(['flag' => 'error', 'message' => 'both username and password are required.']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $username = $params['username'];
        $password = $params['password'];

        try {
            $user = $this->UserModel->getUserByName($username);
            if (!$user || !password_verify($password, $user['password'])) {
                $response
                    ->getBody()
                    ->write(json_encode(['flag' => 'error', 'message' => 'Invalid username or password.']));
                return $response
                    ->withStatus(401)
                    ->withHeader('Content-Type', 'application/json');
            }
        } catch (\PDOException $e) {
            throw $e;
        }

        try {
            // Generate JWT
            $payload = [
                'iss' => 'bunqchat.com',    // Issuer
                'aud' => 'audience.com',    // Audience
                'iat' => time(),               // Issued at
                'nbf' => time(),               // Not before
                'exp' => time() + (60 * 60),   // Expiration time (e.g., 1 hour)
                'data' => [
                    'userId' => $user['id'],
                    'username' => $user['username']
                ]
            ];

            $jwt = JWT::encode($payload, $this->secretKey, 'HS256');

            $response
                ->getBody()
                ->write(json_encode([
                    'flag' => 'success',
                    'message' => 'Login successful.',
                    'token' => $jwt,
                    'username' => $user['username'],
                    'userId' => $user['id']
                ]));
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
        } catch (\PDOException $e) {
            $response
                ->getBody()
                ->write(json_encode(['flag' => 'error', 'message' => 'Login failed.'], true));
            return $response->withStatus(500)
                ->withHeader('Content-Type', 'application/json');
        }
    }
}
