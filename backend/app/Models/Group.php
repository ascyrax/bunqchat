<?php
// app/Models/Group.php

class Group
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function createGroup($userId, $groupName)
    {
        try {
            $stmt = $this->pdo->prepare('INSERT INTO groups (name, createdBy) VALUES (:name, :createdBy)');
            $stmt->bindParam(':name', $groupName);
            $stmt->bindParam(':createdBy', $userId);
            return [true, $stmt->execute()];
        } catch (PDOException $e) {
            error_log("\n" . "failed to create a new group:" . $e->getMessage());
            return [false, $e];
        }
    }

    public function getAllGroups()
    {
        try {
            $stmt = $this->pdo->query('SELECT * FROM groups');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("\n" . "failed to get all groups:" . $e->getMessage());
            return [];
        }
    }

    public function getGroupByName($groupName)
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM groups WHERE name = :groupName');
            $stmt->bindParam(':groupName', $groupName);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result; // This will return false if no group is found
        } catch (PDOException $e) {
            error_log("\n" . "failed to get the group:" . $e->getMessage());
            return [];
        }
    }
}
