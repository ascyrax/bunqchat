<?php

// app/Controllers/ChatGroupController.php
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

require_once __DIR__ . '/../Models/ChatGroup.php';

class ChatGroupController
{
    private $chatGroupModel;

    public function __construct($pdo)
    {
        $this->chatGroupModel = new ChatGroup($pdo);
    }

    public function createGroup(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // middleware handles all the cases where groupName does not exits
        // todo handle in middleware -> similar groupname already exists.
        $groupName = $request->getAttribute('group_name');
        error_log(var_export($groupName, true));

        if ($this->chatGroupModel->createGroup($groupName)) {
            error_log("if");
            $response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json')
                ->getBody()
                ->write(var_export(['message' => 'Chat group created successfully.'], true));
        } else {
            error_log("else");
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
        $groups = $this->chatGroupModel->getAllGroups();
        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(var_export($groups, true));
        return $response;
    }
}
