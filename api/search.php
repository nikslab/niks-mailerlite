<?php

require_once("config.php");

// Read input data from GET request
$name = isset($_GET['name']) ? sanitizeInput($_GET['name']) : null;
$email = isset($_GET['email']) ? strtolower(sanitizeInput($_GET['email'])) : null;
$limit = isset($_GET['limit']) ? min((int)$_GET['limit'], 100) : 100; // Max limit is 100
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1; // Default page is 1

// Calculate the offset for pagination
$offset = ($page - 1) * $limit;

// Build the SQL query based on the provided parameters
$query = "SELECT * FROM subscribers WHERE 1";
$params = [];

if (($name !== null) && ($name !== "")) {
    $query .= " AND name LIKE :name";
    $params[':name'] = "%$name%";
}

if (($email !== null) && ($email !== "")) {
    $query .= " AND email LIKE :email";
    $params[':email'] = "%$email%";
}

$query .= " LIMIT :limit OFFSET :offset";
$params[':limit'] = $limit;
$params[':offset'] = $offset;

// Execute the query
try {
    $stmt = $pdo->prepare($query);

    // Explicitly bind the parameters
    if (($name !== null) && ($name !== "")) {
        $stmt->bindParam(':name', $params[':name']);
    }

    if (($email !== null) && ($email !== "")) {
        $stmt->bindParam(':email', $params[':email']);
    }

    // Bind the LIMIT and OFFSET parameters
    $stmt->bindParam(':limit', $params[':limit'], PDO::PARAM_INT);
    $stmt->bindParam(':offset', $params[':offset'], PDO::PARAM_INT);

    $stmt->execute();

    // Fetch results as an associative array
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the results as JSON
    echo json_encode(['status' => 'success', 'data' => $results]);

} catch (PDOException $e) {
    // Return error message in case of a database error
    echo json_encode(['status' => 'error', 'message' => "Error querying the database"]);
}
