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
        $groupName = $params['group_name'] ?? '';
        $username = $params['username'] ?? '';
        $content = $params['message'] ?? '';

        // error_log(var_export($groupName . $username . $content, true));

        if (empty($groupName) || empty($username) || empty($content)) {
            $response->withStatus(400)->getBody()->write(var_export(['flag' => 'error', 'message' => 'Group Name, username, and message are required.'], true));
            return $response;
        }

        list($result, $userId, $groupId) = $this->groupContainsUser($groupName, $username);

        if (empty($result)) { // => no such group_members exist
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
        $groupId = $args['group_id'];
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

        $stmt = $this->pdo->prepare('SELECT * FROM group_member WHERE group_id = :group_id AND user_id = :user_id');
        $stmt->bindParam(':group_id', $groupId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $result =  $stmt->fetchAll(PDO::FETCH_ASSOC);
        return [$result, $userId, $groupId];
    }
}
