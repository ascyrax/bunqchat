<?php
// app/Models/Group.php

class Group
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function createGroup($username, $groupName)
    {
        try {
            $stmt = $this->pdo->prepare('INSERT INTO groups (name, created_by) VALUES (:name, :created_by)');
            $stmt->bindParam(':name', $groupName);
            $stmt->bindParam(':created_by', $username);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("failed to create a new group:" . $e->getMessage());
            return false;
        }
    }

    public function getAllGroups()
    {
        try {
            $stmt = $this->pdo->query('SELECT * FROM groups');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("failed to get all groups:" . $e->getMessage());
            return [];
        }
    }

    public function getGroupByName($groupName)
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM groups WHERE name = :group_name');
            $stmt->bindParam(':group_name', $groupName);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result; // This will return null if no group is found
        } catch (PDOException $e) {
            error_log("failed to get the group:" . $e->getMessage());
            return [];
        }
    }
}
