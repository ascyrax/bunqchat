<?php


class Message
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function sendMessage($groupId, $userId, $content)
    {
        try {
            $stmt = $this->pdo->prepare('INSERT INTO messages (groupId, userId, content) VALUES (:groupId, :userId, :content)');
            $stmt->bindParam(':groupId', $groupId);
            $stmt->bindParam(':userId', $userId);
            $stmt->bindParam(':content', $content);
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
