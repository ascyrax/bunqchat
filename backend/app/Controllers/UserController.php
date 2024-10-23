<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

require_once __DIR__ . '/../Models/GroupMember.php';

class UserController
{
    private  $GroupModel, $GroupMemberModel;

    public function __construct($pdo)
    {
        $this->GroupModel = new Group($pdo);
        $this->GroupMemberModel = new GroupMember($pdo);
    }

    public function joinGroup(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $user = $request->getAttribute('user');
        $username = $user['username'] ?? '';
        $userId = $user['userId'] ?? '';

        $params = (array)$request->getParsedBody();
        $groupName = $params['groupName'] ?? '';

        try {
            $groupId = $this->getGroupId($groupName);
            if ($this->GroupMemberModel->joinGroup($userId, $groupId)) {
                $response
                    ->getBody()
                    ->write(json_encode(['flag' => 'success', 'message' => 'User joined the group successfully.']));
                return $response->withStatus(201)
                    ->withHeader('Content-Type', 'application/json');
            } else {
                $response
                    ->getBody()
                    ->write(json_encode(['flag' => 'error', 'message' => 'User failed to join the group.']));
                return $response->withStatus(500)
                    ->withHeader('Content-Type', 'application/json');
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $response
                ->getBody()
                ->write(json_encode(['flag' => 'error', 'message' => 'Group not found.']));
            return $response->withStatus(404)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    function getGroupId($groupName)
    {
        try {
            $group = $this->GroupModel->getGroupByName($groupName);
            return $group['id'];
        } catch (\Exception $e) {
            error_log("Could not find group: " . $e->getMessage());
            throw new Exception("Group not found.");
        }
    }
}
