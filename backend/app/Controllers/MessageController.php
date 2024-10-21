<?php
// app/Controllers/MessageController.php

require_once __DIR__ . '/../Models/Message.php';

class MessageController
{
    private $messageModel;

    public function __construct($pdo)
    {
        $this->messageModel = new Message($pdo);
    }

    public function sendMessage($request, $response)
    {
        $body = $request->getParsedBody();
        $groupId = $body['group_id'] ?? '';
        $username = $body['username'] ?? '';
        $message = $body['message'] ?? '';

        if (empty($groupId) || empty($username) || empty($message)) {
            $response->withStatus(400)->getBody()->write(var_export(['error' => 'Group ID, username, and message are required.'], true));
            return $response;
        }

        if ($this->messageModel->sendMessage($groupId, $username, $message)) {
            $response->withStatus(201)->getBody()->write(var_export(['message' => 'Message sent successfully.'], true));
        } else {
            $response->withStatus(500)->getBody()->write(var_export(['error' => 'Failed to send message.'], true));
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
}
