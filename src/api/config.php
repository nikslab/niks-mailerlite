<?php

/**
 * PDO connection to database and some functions
 * 
 * PHP Version 8.0
 * 
 * @author Nik Stankovic <niks.work.goog@gmail.com>
 * @link   https://github.com/nikslab/niks-mailerlite
 */

// Database
$dbHost = getenv('MYSQL_HOSTNAME');
$dbName = getenv('MYSQL_DATABASE');
$dbUser = getenv('MYSQL_USER');
$dbPassword = getenv('MYSQL_PASSWORD');

// Connect to database
try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $error = $e->getMessage();
    echo json_encode(
        ['status' => 'error', 
        'message' => 'Database connection failed: ' . $error]
    );
    exit(0);
}

// Function to sanitize input and prevent SQL injection
/**
 * PDO connection to database and some functions
 * 
 * PHP Version 8.0
 * 
 * @param $input string that needs to be sanitized
 * 
 * @return string sanitized $input
 */
function sanitizeInput($input)
{
    return htmlspecialchars(strip_tags(trim($input)));
}