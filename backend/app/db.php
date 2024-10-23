<?php

// /app/db.php

function createDatabase($dsn = null)
{
    if ($dsn === null) {
        $dbFile = __DIR__ . '/../database/database.sqlite';

        // Create the database file if it does not exist
        if (!file_exists($dbFile)) {
            touch($dbFile);
        }

        $dsn = 'sqlite:' . $dbFile;
    }

    // Create a new PDO instance
    try {
        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('PRAGMA foreign_keys = ON;');  // Enable foreign keys
    } catch (PDOException $e) {
        error_log("Database connection error: " . $e->getMessage());
        throw new Exception("Unable to connect to the database.");
    }

    createTables($pdo);

    return $pdo;
}

function createTables($pdo)
{
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL, -- Store hashed passwords
        createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
        updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP
        )"
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS groups (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL UNIQUE,
        createdBy INTEGER, -- user id who created this group
        createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
        updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP
        )"
    );


    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS messages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        groupId INTEGER NOT NULL,
        userId INTEGER NOT NULL, -- this is the id of the user who sent the messages
        message TEXT NOT NULL,
        createdBy TEXT NOT NULL, -- username corresponding to the userId
        createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (groupId) REFERENCES groups(id) ON DELETE CASCADE,
        FOREIGN KEY (userId) REFERENCES users(id) ON DELETE CASCADE
        )"
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS groupMembers (
            groupId INTEGER NOT NULL,
            userId INTEGER NOT NULL,
            joinedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (groupId, userId),
            FOREIGN KEY (groupId) REFERENCES groups(id) ON DELETE CASCADE,
            FOREIGN KEY (userId) REFERENCES users(id) ON DELETE CASCADE
        )"
    );
}
