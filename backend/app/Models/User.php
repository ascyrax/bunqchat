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
        $stmt = $this->pdo->prepare('INSERT INTO users (username, password) VALUES (:username, :password)');
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        return $stmt->execute();
    }

    public function getUserByName($username)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE username = :username');
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result; // This will return null if no group is found
    }

    public function getUserByToken($token)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE token = :token');
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result; // This will return null if no group is found
    }

    public function updateUser($userId, $token)
    {
        $stmt = $this->pdo->prepare("UPDATE users SET token = :token WHERE id = :userId");
        $stmt->bindParam(":userId", $userId);
        $stmt->bindParam(":token", $token);
        return $stmt->execute();
        // $result = $stmt->fetch(PDO::FETCH_ASSOC);
        // return $result;
    }
}
