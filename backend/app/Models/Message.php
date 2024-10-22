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

    public function sendMessage($groupId, $userId, $content)
    {
        // error_log(var_export($groupId, true) . "" . var_export($userId, true) . "" . var_export($message, true));
        try {
            $stmt = $this->pdo->prepare('INSERT INTO messages (group_id, user_id, content) VALUES (:group_id, :user_id, :content)');
            $stmt->bindParam(':group_id', $groupId);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':content', $content);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('failed to create a new message:' . $e->getMessage());
            return false;
        }
    }

    public function getMessagesByGroup($groupName)
    {
        try {
            $groupId = $this->GroupController->getGroupId($groupName);
            $stmt = $this->pdo->prepare('SELECT * FROM messages WHERE group_id = :group_id ORDER BY created_at ASC');
            $stmt->bindParam(':group_id', $groupId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('failed to get messages of the group:' . $e->getMessage());
            return [];
        }
    }
}
