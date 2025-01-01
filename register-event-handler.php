<?php
session_start();
header('Content-Type: application/json');

// Database connection
$config = require 'config.php';
$conn = new mysqli("localhost", $config['username'], $config['password'], "sutclubs");

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['eventId'])) {
        echo json_encode(["status" => "error", "message" => "No event ID provided"]);
        exit;
    }

    $eventId = intval($_POST['eventId']);
    $userId = $_SESSION['id'] ?? null;  // Get the user_id from session
    $username = $_SESSION['full_name'] ?? $_SESSION['user_name'] ?? null;  // Get username (or full name)
    $email = $_SESSION['email'] ?? null;  // Get the email from session

    if (!$userId) {
        echo json_encode(["status" => "error", "message" => "User is not logged in."]);
        exit;
    }

    if (!$username || !$email) {
        echo json_encode(["status" => "error", "message" => "User details are incomplete."]);
        exit;
    }

    try {
        // Check if the event exists and has available spots
        $sql = "SELECT current_registrations, max_registrations FROM events WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            echo json_encode(["status" => "error", "message" => "Event not found"]);
            exit;
        }

        $event = $result->fetch_assoc();

        if ($event['current_registrations'] >= $event['max_registrations']) {
            echo json_encode(["status" => "error", "message" => "Event is full"]);
            exit;
        }

        // Check if user is already registered
        $sql = "SELECT id FROM event_registrations WHERE event_id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $eventId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo json_encode(["status" => "error", "message" => "You are already registered for this event"]);
            exit;
        }

        // Begin transaction
        $conn->begin_transaction();

        // Register the user for the event (including username and email)
        $sql = "INSERT INTO event_registrations (event_id, user_id, username, email) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiss", $eventId, $userId, $username, $email);  // Insert event_id, user_id, username, email

        if (!$stmt->execute()) {
            throw new Exception("Failed to insert registration: " . $stmt->error);
        }

        // Update the current_registrations count
        $sql = "UPDATE events SET current_registrations = current_registrations + 1 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $eventId);

        if (!$stmt->execute()) {
            throw new Exception("Failed to update registration count: " . $stmt->error);
        }

        // Commit the transaction
        $conn->commit();
        echo json_encode(["status" => "success", "message" => "Registration successful"]);

    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => "Registration failed: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}

$conn->close();
?>
