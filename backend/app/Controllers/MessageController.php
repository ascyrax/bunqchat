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
        $body = $request->getParsedBody();
        $groupName = $body['group_name'] ?? '';
        $username = $body['username'] ?? '';
        $message = $body['message'] ?? '';

        error_log(var_export($groupName . $username . $message, true));

        if (empty($groupName) || empty($username) || empty($message)) {
            $response->withStatus(400)->getBody()->write(var_export(['error' => 'Group Name, username, and message are required.'], true));
            return $response;
        }

        list($result, $userId, $groupId) = $this->groupContainsUser($groupName, $username);

        if (empty($result)) { // => no such group_members exist
            $response->withStatus(401)->getBody()->write(var_export(['success' => false, 'message' => 'User is not a member of the group'], true));
            return $response;
        }

        if ($this->messageModel->sendMessage($groupId, $userId, $message)) {
            $response->withStatus(201)->getBody()->write(var_export(['success' => true, 'message' => 'Message sent successfully.'], true));
        } else {
            $response->withStatus(500)->getBody()->write(var_export(['success' => false, 'error' => 'Failed to send message.'], true));
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
        $stmt = $this->pdo->prepare('SELECT * FROM group_members WHERE group_id = :group_id AND user_id = :user_id');
        $stmt->bindParam(':group_id', $groupId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $result =  $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log('yooooooooooo------------' . var_export($result, true));
        return [$result, $userId, $groupId];
    }
}
