<?php
$config=include('config.php');
session_start();
$servername = "localhost";
$username =$config['username'];
$password = $config['password'];
$dbname = "sutclubs";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, password FROM users";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $hashed_password = password_hash($row['password'], PASSWORD_DEFAULT);
        $update_sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("si", $hashed_password, $row['id']);
        $stmt->execute();
    }
    echo "Passwords rehashed successfully.";
} else {
    echo "No users found.";
}

$conn->close();
?>
