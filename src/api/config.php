<?php

/**
 * PDO connection to database and some functions
 * 
 * PHP Version 8.0
 * 
 * @author Nik Stankovic <niks.work.goog@gmail.com>
 * @link   https://github.com/nikslab/niks-mailerlite
 */

// Load environment variables if they are not loaded already
if (!getenv('MYSQL_DATABASE')) {
    loadEnv('../../.env');
}

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

/**
 * Loads environmental variables if they are not already loaded
 * 
 * PHP Version 8.0
 * 
 * @param $filePath file path to .env file
 * 
 * @return true
 */
function loadEnv($filePath = '.env') {
    if (!file_exists($filePath)) {
        return false; // .env file not found
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        // Ignore comments
        if (strpos($line, '#') !== false) {
            continue;
        }

        list($key, $value) = explode('=', $line, 2);

        // Trim leading/trailing whitespaces and quotes
        $key = trim($key);
        $value = trim($value, " \t\n\r\0\x0B'\"");

        // Set the environment variable
        putenv("$key=$value");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }

    return true;
}


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