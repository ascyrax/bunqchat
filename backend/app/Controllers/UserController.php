<?php

// app/Controllers/UserController.php
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

require_once __DIR__ . '/../Models/User.php';

class UserController
{
    private $UserModel;

    public function __construct($pdo)
    {
        $this->UserModel = new User($pdo);
    }

    public function createUser(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $username = $request->getAttribute('username');
        // $password = $request->getAttribute('password');
        $password = 'pass' . $username . 'word';
        // error_log(var_export($username, true));

        if ($this->UserModel->createUser($username, $password)) {
            $response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json')
                ->getBody()
                ->write(var_export(['message' => 'New user created successfully.'], true));
        } else {
            $response
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json')
                ->getBody()
                ->write(var_export(['error' => 'Failed to create a new user.'], true));
        }
        return $response;
    }
}
