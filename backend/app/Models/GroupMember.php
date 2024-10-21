<?php

class GroupMember
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function joinGroup($userId, $groupId)
    {
        $stmt = $this->pdo->prepare('INSERT INTO group_members (group_id, user_id) VALUES (:groupId, :userId)');
        $stmt->bindParam(':groupId', $groupId);
        $stmt->bindParam(':userId', $userId);
        return $stmt->execute();
    }

    public function getAllGroupMembers()
    {
        $stmt = $this->pdo->query('SELECT * FROM group_members');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGroupMemberByName($groupName, $username)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM group_members WHERE group_name = :groupName AND username = :username');
        $stmt->bindParam(':group_name', $groupName);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result; // This will return null if no group is found
    }
}
