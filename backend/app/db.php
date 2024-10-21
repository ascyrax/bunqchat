<?php
// app/db.php

function createDatabase()
{
    $dbFile = __DIR__ . '/../database/database.sqlite';

    // Create the database file if it does not exist
    if (!file_exists($dbFile)) {
        touch($dbFile);
    }

    // Create a new PDO instance
    try {
        $pdo = new PDO('sqlite:' . $dbFile);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        error_log("Database connection error: " . $e->getMessage());
        throw new Exception("Unable to connect to the database.");
    }

    createTables($pdo);

    return $pdo;
}

function createTables($pdo)
{
    // Create tables if they do not exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS chat_groups (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    group_name TEXT NOT NULL,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
)");

    $pdo->exec("CREATE TABLE IF NOT EXISTS messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    group_id INTEGER,
    username TEXT NOT NULL,
    message TEXT NOT NULL,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES chat_groups (id)
)");
}
