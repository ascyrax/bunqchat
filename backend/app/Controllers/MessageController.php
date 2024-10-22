<?php
// app/Controllers/MessageController.php

require_once __DIR__ . '/../Models/Message.php';
require_once __DIR__ . '/../Controllers/GroupController.php';
require_once __DIR__ . '/../Controllers/UserController.php';

class MessageController
{
    private $messageModel;
    private $pdo;
    private $GroupController, $UserController;

    public function __construct($pdo)
    {
        $this->messageModel = new Message($pdo);
        $this->pdo = $pdo;
        $this->GroupController = new GroupController($pdo);
        $this->UserController = new UserController($pdo);
    }

    public function sendMessage($request, $response)
    {
        $params = (array)$request->getParsedBody();
        $groupName = $params['groupName'] ?? '';
        $username = $params['username'] ?? '';
        $content = $params['message'] ?? '';

        // error_log(var_export($groupName . $username . $content, true));

        if (empty($groupName) || empty($username) || empty($content)) {
            $response->withStatus(400)->getBody()->write(var_export(['flag' => 'error', 'message' => 'Group Name, username, and message are required.'], true));
            return $response;
        }

        list($result, $userId, $groupId) = $this->groupContainsUser($groupName, $username);

        if (empty($result)) { // => no such groupMemberss exist
            $response->withStatus(401)->getBody()->write(var_export(['flag' => 'error', 'message' => 'User is not a member of the group'], true));
            return $response;
        }

        if ($this->messageModel->sendMessage($groupId, $userId, $content)) {
            $response->withStatus(201)->getBody()->write(var_export(['flag' => 'success', 'message' => 'Message sent successfully.'], true));
        } else {
            $response->withStatus(500)->getBody()->write(var_export(['flag' => 'error', 'message' => 'Failed to send message.'], true));
        }
        return $response;
    }

    public function getMessages($request, $response, $args)
    {
        $groupId = $args['groupId'];
        // error_log(var_export($groupId, true));

        $messages = $this->messageModel->getMessagesByGroup($groupId);
        // error_log(var_export($messages, true));
        $response->getBody()->write(var_export($messages, true));
        // $response->getBody()->write(var_export('surdaj', true));
        return $response;
    }

    public function groupContainsUser($groupName, $username)
    {
        $groupId = $this->GroupController->getGroupId($groupName);
        $userId = $this->UserController->getUserId($username);

        $stmt = $this->pdo->prepare('SELECT * FROM groupMembers WHERE groupId = :groupId AND userId = :userId');
        $stmt->bindParam(':groupId', $groupId);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        $result =  $stmt->fetchAll(PDO::FETCH_ASSOC);
        return [$result, $userId, $groupId];
    }
}
