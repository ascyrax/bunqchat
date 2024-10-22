<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Models\Database;

// Initialize the database
$db = Database::getInstance();

// Create a temporary in-memory database for testing
$db->exec('PRAGMA foreign_keys = ON;');

// Load the schema
$schema = file_get_contents(__DIR__ . '/../schema.sql');
$db->exec($schema);
