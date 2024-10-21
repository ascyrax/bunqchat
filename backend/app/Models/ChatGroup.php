<?php
// app/Models/ChatGroup.php

class ChatGroup
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function createGroup($groupName)
    {
        $stmt = $this->pdo->prepare('INSERT INTO chat_groups (group_name) VALUES (:group_name)');
        $stmt->bindParam(':group_name', $groupName);
        error_log("createGroup -> ");
        return $stmt->execute();
    }

    public function getAllGroups()
    {
        $stmt = $this->pdo->query('SELECT * FROM chat_groups');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
