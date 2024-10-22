<?php

class TokenGenerator
{
    private $pdo;
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    public static function generateToken($length = 32)
    {
        return bin2hex(random_bytes($length));
    }
}
