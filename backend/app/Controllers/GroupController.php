<?php

// app/Controllers/GroupController.php
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

require_once __DIR__ . '/../Models/Group.php';
require_once __DIR__ . '/../Models/GroupMember.php';


class GroupController
{
    private $GroupModel, $GroupMemberModel;

    public function __construct($pdo)
    {
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

        if (empty($groupName)) {
            $response->getBody()->write(json_encode(['flag' => 'error', 'message' => 'Group name is required.']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        list($flagCreate, $eCreate) = $this->GroupModel->createGroup($userId, $groupName);

        if ($flagCreate) {
            $groupId = $this->getGroupId($groupName);
            list($flagJoin, $eJoin) = $this->GroupMemberModel->joinGroup($userId, $groupId);
            if ($flagJoin) {
                $response
                    ->getBody()
                    ->write(json_encode(['flag' => 'success', 'message' => 'group created + user joined the group successfully.']));
                return $response->withStatus(201)
                    ->withHeader('Content-Type', 'application/json');
            } else {
                // Check if the error is due to a unique constraint violation
                if ($eJoin && $eJoin->getCode() == '23000') {
                    $response->getBody()
                        ->write(json_encode(['flag' => 'error', 'message' => 'user has already joined the group.']));
                    return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
                } else {
                    $response->getBody()
                        ->write(json_encode(['flag' => 'error', 'message' => 'User failed to join the group.']));
                    return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
                }
            }
        } else {
            // Check if the error is due to a unique constraint violation
            if ($eCreate && $eCreate->getCode() == '23000') {
                $response->getBody()
                    ->write(json_encode(['flag' => 'error', 'message' => 'group already exists.']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()
                    ->write(json_encode(['flag' => 'error', 'message' => 'failed to create the group.']));
                return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
            }
        }
    }

    public function getAllGroups($request, $response)
    {
        $groups = $this->GroupModel->getAllGroups();
        $response->getBody()->write(json_encode(['flag' => 'success', 'message' => $groups]));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    public function getGroupId($groupName)
    {
        try {
            $group = $this->GroupModel->getGroupByName($groupName);
            return $group['id'];
        } catch (\Exception $e) {
            error_log("\n" . "Could not find group: " . $e->getMessage());
            return throw new Exception("Group not found.");
        }
    }
}
