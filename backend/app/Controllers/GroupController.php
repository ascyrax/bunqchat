<?php

// app/Controllers/GroupController.php
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

require_once __DIR__ . '/../Models/Group.php';

class GroupController
{
    private $GroupModel;

    public function __construct($pdo)
    {
        $this->GroupModel = new Group($pdo);
    }

    public function createGroup(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // middleware handles all the cases where groupName does not exits
        // todo handle in middleware -> similar groupname already exists.
        $groupName = $request->getAttribute('group_name');
        error_log(var_export($groupName, true));

        if ($this->GroupModel->createGroup($groupName)) {
            $response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json')
                ->getBody()
                ->write(var_export(['message' => 'Chat group created successfully.'], true));
        } else {
            $response
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json')
                ->getBody()
                ->write(var_export(['error' => 'Failed to create chat group.'], true));
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
