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
$dbHost = 'localhost';
$dbName = '**************';
$dbUser = '**************';
$dbPassword = '**************';

// Connect to database
try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
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