<?php

class User
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function createUser($username, $password)
    {
        try {
            $stmt = $this->pdo->prepare('INSERT INTO users (username, password) VALUES (:username, :password)');
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            return [true, $stmt->execute()];
        } catch (PDOException $e) {
            error_log("\n" . "failed to create a new user:" . $e->getMessage());
            return [false, $e];
        }
    }

    public function getUserByName($username)
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM users WHERE username = :username');
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result; // This will return null if no group is found
        } catch (PDOException $e) {
            error_log("\n" . 'failed to get user:' . $e->getMessage());
            return [];
        }
    }

    public function getUserByToken($token)
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM users WHERE token = :token');
            $stmt->bindParam(':token', $token);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result; // This will return null if no group is found
        } catch (PDOException $e) {
            error_log("\n" . 'failed to get user:' . $e->getMessage());
            return null;
        }
    }

    public function updateUser($userId, $token)
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET token = :token WHERE id = :userId");
            $stmt->bindParam(":userId", $userId);
            $stmt->bindParam(":token", $token);
            return $stmt->execute();
            // $result = $stmt->fetch(PDO::FETCH_ASSOC);
            // return $result;
        } catch (PDOException $e) {
            error_log("\n" . 'failed to update user' . $e->getMessage());
            return null;
        }
    }
}
