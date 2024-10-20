<?php

// app/Models/Message.php
require_once __DIR__ . "/../Controllers/GroupController.php";

class Message
{
    private $pdo;
    private $GroupController;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->GroupController = new GroupController($pdo);
    }

    public function sendMessage($groupId, $userId, $message)
    {
        $stmt = $this->pdo->prepare('INSERT INTO messages (group_id, user_id, message) VALUES (:group_id, :user_id, :message)');
        $stmt->bindParam(':group_id', $groupId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':message', $message);
        return $stmt->execute();
    }

    public function getMessagesByGroup($groupName)
    {
        $groupId = $this->GroupController->getGroupId($groupName);
        $stmt = $this->pdo->prepare('SELECT * FROM messages WHERE group_id = :group_id ORDER BY created_at ASC');
        $stmt->bindParam(':group_id', $groupId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
