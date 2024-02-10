<?php

/**
 * API endpoint adds a new subscriber
 * 
 * PHP Version 8.0
 * 
 * @author Nik Stankovic <niks.work.goog@gmail.com>
 * @link   https://github.com/nikslab/niks-mailerlite
 */

require_once "config.php";

// Read input data from POST request
$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true);

// Check if the "email" field is present
if (isset($data['email'])) {
    // Sanitize input, better safe than sorry
    foreach ($data as $key => $value) {
        $data[$key] = sanitizeInput($value);
    }
    $email = strtolower($data['email']);

    // Insert data into the table with a unique constraint on the "email" column
    try {
        $stmt = $pdo->prepare(
            "INSERT INTO subscribers (
                 name, 
                 last_name, 
                 email, 
                 status
             ) 
             VALUES (
                :name, 
                :last_name, 
                :email, 
                :status
            )"
        );
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':last_name', $data['lastName']);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':status', $data['status']);
        $stmt->execute();

        // Return success message
        echo json_encode(
            ['status' => 'success', 
             'message' => 'Data inserted successfully']
        );
    } catch (PDOException $e) {
        // Check if the error is due to a duplicate entry
        if ($e->errorInfo[1] == 1062) { // 1062 is the MySQL error code for duplicate entry
            // Return message if the email already exists
            echo json_encode(
                ['status' => 'subscriber_exists', 
                 'message' => 'Subscriber with this email already exists']
            );
        } else {
            // Return error message for other database errors
            echo json_encode(
                ['status' => 'error', 
                 'message' => 'Error inserting data into the database']
            );
        }
    }
} else {
    // Return error message if the "email" field is missing
    echo json_encode(
        ['status' => 'error', 
        'message' => 'Missing required email field']
    );
}