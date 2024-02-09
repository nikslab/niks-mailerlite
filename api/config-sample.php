<?php

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

$apiBase = "https://localhost/api";

/*************/
/* FUNCTIONS */
/*************/

// Function to sanitize input and prevent SQL injection
function sanitizeInput($input)
{
    return htmlspecialchars(strip_tags(trim($input)));
}