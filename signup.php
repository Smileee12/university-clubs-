<?php
// Configuration
session_start();
$config = include('config.php');
$servername = "localhost";
$username = "root";
$password = $config["password"];
$dbname = "sutclubs";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize input data
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input data
    $full_name = sanitize_input($_POST['full_name']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $username = $_POST['User_name'];


    // Validate input
    $errors = [];

    if (empty($full_name)) {
        $errors[] = "Full name is required";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }

    // if (empty($password) || strlen($password) < 8) {
    //     $errors[] = "Password must be at least 8 characters long";
    // }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }

   

    // If no errors, proceed with registration
    if (empty($errors)) {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "Error: Email already exists";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Prepare SQL statement
            $sql = "INSERT INTO users (full_name, email, password,username) VALUES (?, ?, ?,?)";
            $stmt = $conn->prepare($sql);

            // Bind parameters
            $stmt->bind_param("ssss", $full_name, $email, $hashed_password,$username);

            // Execute statement
            if ($stmt->execute()) {
                echo "Registration successful!";
                header("location:userDashboard.php");
                exit();
            } else {
                echo "Error: " . $conn->error;
            }

            // Close statement
            $stmt->close();
        }
    } else {
        // Display errors
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
    }
}

// Close connection
$conn->close();

