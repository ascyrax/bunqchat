<?php

// app/Controllers/GroupController.php
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

require_once __DIR__ . '/../Models/Group.php';
require_once __DIR__ . '/../Models/GroupMember.php';


class GroupController
{
    private $GroupModel, $UserModel, $GroupMemberModel;

    public function __construct($pdo)
    {
        $this->UserModel = new User($pdo);
        $this->GroupModel = new Group($pdo);
        $this->GroupMemberModel = new GroupMember($pdo);
    }

    public function createGroup(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $user = $request->getAttribute('user');
        $username = $user['username'] ?? '';
        $userId = $user['userId'] ?? '';

        $params = (array)$request->getParsedBody();
        $groupName = $params['groupName'] ?? '';

        // middleware handles all the cases where groupName does not exits
        // todo handle in middleware -> similar groupname already exists.
        // $groupName = $request->getAttribute('groupName');
        error_log(var_export($groupName, true));

        if ($this->GroupModel->createGroup($userId, $groupName)) {
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
            $response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json')
                ->getBody()
                ->write(var_export(['flag' => 'success', 'message' => 'Chat group created successfully.'], true));
        } else {
            $response
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json')
                ->getBody()
                ->write(var_export(['flag' => 'error', 'message' => 'Failed to create chat group.'], true));
        }
        return $response;
    }

    public function getAllGroups($request, $response)
    {
        $groups = $this->GroupModel->getAllGroups();
        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(var_export($groups, true));
        return $response;
    }

    public function getGroupId($groupName)
    {
        try {
            $group = $this->GroupModel->getGroupByName($groupName);
            return $group['id'];
        } catch (\Exception $e) {
            error_log("Could not find group: " . $e->getMessage());
            return throw new Exception("Group could not be found in the database.");
        }
    }
}
