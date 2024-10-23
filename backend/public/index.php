<?php

// /app/public/index.php

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/db.php';  // Include the database setup
require __DIR__ . '/../app/Routes/Routes.php';  // Include chat routes

$app = AppFactory::create();
$pdo = createDatabase();  // Create or connect to the SQLite database

// Register the routes
chatRoutes($app, $pdo);

$app->run();
