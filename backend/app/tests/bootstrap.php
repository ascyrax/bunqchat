<?php
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../app/db.php';  // Include the database setup

// For testing, we can use an in-memory SQLite database
$pdo = createDatabase('sqlite::memory:');  // Pass DSN for in-memory database
