<?php
session_start();
$config = include('config.php');
$servername = "localhost";
$username = "root";
$password = $config['password'];
$dbname = "sutclubs";

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Trim inputs to avoid accidental spaces
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Email and password are required"]);
        exit;
    }

    // Prepare SQL statement
    $sql = "SELECT id, full_name, email, password, user_type FROM users WHERE email = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $param_email);
        $param_email = $email;

        if ($stmt->execute()) {
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                // Bind results to variables
                $stmt->bind_result($id, $full_name, $email, $hashed_password, $user_type);
                if ($stmt->fetch()) {
                    // Verify the password
                    if (password_verify($password, $hashed_password)) {
                        // Set session variables
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["email"] = $email;
                        $_SESSION["full_name"] = $full_name;
                        $_SESSION["user_type"] = $user_type;

                        // Redirect based on user type
                        $redirect_url = ($user_type === "admin") ? "admin-dashboard.php" : "userDashboard.php";

                        echo json_encode([
                            "status" => "success",
                            "redirect" => $redirect_url,
                            "user" => [
                                "id" => $id,
                                "email" => $email,
                                "full_name" => $full_name,
                                "user_type" => $user_type
                            ]
                        ]);
                        exit;
                    } else {
                        // Incorrect password
                        echo json_encode(["status" => "error", "message" => "Invalid password"]);
                        exit;
                    }
                }
            } else {
                // Email not found
                echo json_encode(["status" => "error", "message" => "Email not found"]);
                exit;
            }
        } else {
            // Execution error
            echo json_encode(["status" => "error", "message" => "Something went wrong. Please try again later."]);
            exit;
        }

        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to prepare the SQL statement"]);
        exit;
    }

    $conn->close();
}
?>
