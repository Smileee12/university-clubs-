<?php
$config = require 'config.php';

// Retrieve credentials
$host = 'localhost';
$db_name = 'sutclubs';
$username = $config['username'];
$password = $config['password'];

// Create a MySQLi connection
$mysqli = new mysqli($host, $username, $password, $db_name);

// Check for connection errors
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

$eventId = $_GET['eventId'];

$sql = "SELECT current_registrations, max_registrations FROM events WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $eventId);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

echo json_encode(['count' => $event['current_registrations'], 'max' => $event['max_registrations']]);

$stmt->close(); // Close the statement
$mysqli->close(); // Close the connection
?>
