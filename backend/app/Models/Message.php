<?php

use SebastianBergmann\Environment\Console;

class Message
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function sendMessage($groupId, $userId, $message, $createdAt, $createdBy)
    {
        try {
            $stmt = $this->pdo->prepare('INSERT INTO messages (groupId, userId, message, createdAt, createdBy) VALUES (:groupId, :userId, :message, :createdAt, :createdBy)');
            $stmt->bindParam(':groupId', $groupId);
            $stmt->bindParam(':userId', $userId);
            $stmt->bindParam(':message', $message);
            $stmt->bindParam(':createdAt', $createdAt);
            $stmt->bindParam(':createdBy', $createdBy);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('failed to create a new message:' . $e->getMessage());
            return false;
        }
    }

    public function getMessagesByGroup($groupId)
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM messages WHERE groupId = :groupId ORDER BY createdAt ASC');
            $stmt->bindParam(':groupId', $groupId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('failed to get messages of the group:' . $e->getMessage());
            return [];
        }
    }
}
