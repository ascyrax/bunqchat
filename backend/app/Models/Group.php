<?php
// app/Models/Group.php

class Group
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function createGroup($groupName)
    {
        $stmt = $this->pdo->prepare('INSERT INTO groups (group_name) VALUES (:group_name)');
        $stmt->bindParam(':group_name', $groupName);
        return $stmt->execute();
    }

    public function getAllGroups()
    {
        $stmt = $this->pdo->query('SELECT * FROM groups');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGroupByName($groupName)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM groups WHERE group_name = :group_name');
        $stmt->bindParam(':group_name', $groupName);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result; // This will return null if no group is found
    }
}
