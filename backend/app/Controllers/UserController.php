<?php

// app/Controllers/UserController.php
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/GroupMember.php';
require_once __DIR__ . '/../Controllers/GroupController.php';

class UserController
{
    private $UserModel, $GroupController, $GroupMemberModel;

    public function __construct($pdo)
    {
        $this->UserModel = new User($pdo);
        $this->GroupMemberModel = new GroupMember($pdo);
        $this->GroupController = new GroupController($pdo);
    }

    // public function createUser(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    // {
    //     $username = $request->getAttribute('username');
    //     // $password = $request->getAttribute('password');
    //     $password = 'pass' . $username . 'word';
    //     // error_log(var_export($username, true));

    //     if ($this->UserModel->createUser($username, $password)) {
    //         $response
    //             ->withStatus(201)
    //             ->withHeader('Content-Type', 'application/json')
    //             ->getBody()
    //             ->write(var_export(['message' => 'New user created successfully.'], true));
    //     } else {
    //         $response
    //             ->withStatus(500)
    //             ->withHeader('Content-Type', 'application/json')
    //             ->getBody()
    //             ->write(var_export(['flag' => 'error', 'message' => 'Failed to create a new user.'], true));
    //     }
    //     return $response;
    // }

    public function joinGroup(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $user = $request->getAttribute('user');
        $username = $user['username'] ?? '';
        $userId = $user['userId'] ?? '';

        $params = (array)$request->getParsedBody();
        $groupName = $params['groupName'] ?? '';

        $groupId = $this->getGroupId($groupName);

        if ($this->GroupMemberModel->joinGroup($userId, $groupId)) {
            $response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json')
                ->getBody()
                ->write(var_export(['flag' => 'success', 'message' => 'User joined the Group successfully.'], true));
        } else {
            $response
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json')
                ->getBody()
                ->write(var_export(['flag' => 'error', 'message' => 'User failed to join the group.'], true));
        }
        return $response;
    }

    function getGroupId($groupName)
    {
        $groupId = $this->GroupController->getGroupId($groupName);
        return $groupId;
    }
}
