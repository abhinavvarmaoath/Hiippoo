<?php
// Single place to open the app's DB connection. Credentials come from the
// environment (see .env.example) instead of being hardcoded per page.
function db_connect(): mysqli {
    $host = getenv('DB_HOST') ?: 'localhost';
    $name = getenv('DB_NAME') ?: 'hiippoo';
    $user = getenv('DB_USER');
    $pass = getenv('DB_PASSWORD');
    if ($user === false || $pass === false) {
        die('Database credentials are not configured. Set DB_USER and DB_PASSWORD.');
    }
    $conn = new mysqli($host, $user, $pass, $name);
    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }
    return $conn;
}
