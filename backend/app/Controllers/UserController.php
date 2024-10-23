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
        } catch (Exception $e) {
            error_log($e->getMessage());
            $response
                ->withStatus(404)
                ->withHeader('Content-Type', 'application/json')
                ->getBody()
                ->write(var_export(['flag' => 'error', 'message' => 'Group could not be found in the database.'], true));
        }
        return $response;
    }

    function getGroupId($groupName)
    {
        try {
            $group = $this->GroupModel->getGroupByName($groupName);
            return $group['id'];
        } catch (\Exception $e) {
            error_log("Could not find group: " . $e->getMessage());
            throw new Exception("Group could not be found in the database.");
        }
    }
}
