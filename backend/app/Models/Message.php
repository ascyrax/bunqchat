<?php

// app/Models/Message.php

class Message {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function sendMessage($groupId, $userId, $message) {
        $stmt = $this->pdo->prepare('INSERT INTO messages (group_id, userId, message) VALUES (:group_id, :userId, :message)');
        $stmt->bindParam(':group_id', $groupId);
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':message', $message);
        return $stmt->execute();
    }

    public function getMessagesByGroup($groupId) {
        $stmt = $this->pdo->prepare('SELECT * FROM messages WHERE group_id = :group_id ORDER BY created_at ASC');
        $stmt->bindParam(':group_id', $groupId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}