<?php
// app/Controllers/MessageController.php

require_once __DIR__ . '/../Models/Message.php';

class MessageController
{
    private $messageModel;
    private $pdo;

    public function __construct($pdo)
    {
        $this->messageModel = new Message($pdo);
        $this->pdo = $pdo;
    }

    public function sendMessage($request, $response)
    {
        $body = $request->getParsedBody();
        $groupId = $body['group_id'] ?? '';
        $userId = $body['userId'] ?? '';
        $message = $body['message'] ?? '';

        if (empty($groupId) || empty($userId) || empty($message)) {
            $response->withStatus(400)->getBody()->write(var_export(['error' => 'Group ID, userId, and message are required.'], true));
            return $response;
        }

        // check if the user belongs to the group
        if (!$this->groupContainsUser($groupId, $userId)) {
            $response->withStatus(401)->getBody()->write(var_export(['success' => false, 'message' => 'User is not a member of the group'], true));
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

    public function groupContainsUser($groupId, $userId)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM group_members WHERE group_id = :group_id AND user_id = :user_id');
        $stmt->bindParam(':group_id', $groupId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $result =  $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log(var_export($result, true));
    }
}
