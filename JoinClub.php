<?php
session_start();
$config = include('config.php');

// Redirect if not logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: LogInPage.html");
    exit;
}

// Database connection
$conn = new mysqli("localhost", $config['username'], $config['password'], "sutclubs");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the club ID from the URL
$clubId = $_GET['id'] ?? null;
if (!$clubId) {
    die("Invalid club specified.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = $_POST["fullName"];
    $studentId = $_POST["studentId"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $role = $_POST["role"];
    $userId = $_SESSION['id'];

    // Check if the club exists
    $clubCheckQuery = "SELECT * FROM clubs WHERE id = ?";
    $stmt = $conn->prepare($clubCheckQuery);
    $stmt->bind_param("i", $clubId);
    $stmt->execute();
    $clubResult = $stmt->get_result();

    if ($clubResult->num_rows == 0) {
        $errorMessage = "Invalid club specified.";
    } else {
        // Fetch the club details
        $clubRow = $clubResult->fetch_assoc();
        $clubName = $clubRow['name'];

        // Check if the user is already a member of this club
        $memberCheckQuery = "SELECT * FROM club_members WHERE club_name = ? AND email = ?";
        $stmt = $conn->prepare($memberCheckQuery);
        $stmt->bind_param("ss", $clubName, $email);
        $stmt->execute();
        $memberResult = $stmt->get_result();

        if ($memberResult->num_rows > 0) {
            $errorMessage = "You are already a member of this club.";
        } else {
            // Check if the user has reached their club limit
            $clubLimit = 3; // Example limit of 3 clubs
            $userClubsQuery = "SELECT COUNT(*) as club_count FROM club_members WHERE email = ?";
            $stmt = $conn->prepare($userClubsQuery);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $userClubsResult = $stmt->get_result();
            $clubCountRow = $userClubsResult->fetch_assoc();

            if ($clubCountRow['club_count'] >= $clubLimit) {
                $errorMessage = "You have reached the maximum number of clubs you can join.";
            } else {
                // Insert the user into the club_members table
                $sql = "INSERT INTO club_members (club_name, full_name, student_id, email, phone, role) VALUES (?, ?, ?, ?, ?, ?)";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("ssssss", $clubName, $fullName, $studentId, $email, $phone, $role);
                    if ($stmt->execute()) {
                        $successMessage = "Successfully joined the club!";
                        header("Location: userClubs.php");
                        exit;
                    } else {
                        $errorMessage = "Something went wrong. Please try again later.";
                    }
                    $stmt->close();
                }
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join <?= htmlspecialchars($clubName ?? 'Club') ?> Club</title>
    <link rel="stylesheet" href="JoinClubstyle.css">
</head>
<body>
    <nav>
        <div class="logo">SUTCLUBS</div>
        <div class="nav-links">
            <a href="./userDashboard.php">Dashboard</a>
            <a href="./userEvents.php">Event</a>
            <a href="./userClubs.php">Clubs</a>
        </div>
        <div class="user-icon">
            <div class="profile-dropdown">
                <img src="img/placeholder-profile-icon-64037ijusubkr7gu.png" alt="User Account" id="profileIcon">
                <div class="dropdown-content" id="userDropdown">
                    <div id="userInfo"></div>
                    <a href="#" id="logoutButton">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <h1>Join <?= htmlspecialchars($clubName ?? 'the Club') ?> Club</h1>
    <main class="form-container">
        <?php if (isset($successMessage)): ?>
            <p style="color: green;"><?= htmlspecialchars($successMessage) ?></p>
        <?php elseif (isset($errorMessage)): ?>
            <p style="color: red;"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>

        <form id="clubJoinForm" class="join-form" action="joinClub.php?id=<?= urlencode($clubId) ?>" method="POST">
            <div class="form-grid">
                <div class="form-left">
                    <input type="hidden" id="clubName" name="clubName" value="<?= htmlspecialchars($clubName ?? '') ?>">
                    <div class="form-group">
                        <label for="fullName">Full Name</label>
                        <input type="text" id="fullName" name="fullName" value="<?= htmlspecialchars($_SESSION['full_name'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="studentId">Student ID</label>
                        <input type="text" id="studentId" name="studentId" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>

                    <div class="form-group">
                        <label for="role">Role Select</label>
                        <select id="role" name="role" required>
                            <option value="" disabled selected>select a role from the following</option>
                            <option value="marketing">Marketing</option>
                            <option value="pr-logistics">PR and Logistics</option>
                            <option value="photography">Photography</option>
                            <option value="graphic-designer">Graphic designer</option>
                        </select>
                        <button type="submit" class="submit-btn">Submit</button>
                    </div>
                </div>

                
            </div>
        </form>
    </main>
</body>
</html>












<!-- <div class="form-group">
                        <label for="role">Role Select</label>
                        <select id="role" name="role" required>
                            <option value="" disabled selected>select a role from the following</option>
                            <option value="marketing">Marketing</option>
                            <option value="pr-logistics">PR and Logistics</option>
                            <option value="photography">Photography</option>
                            <option value="graphic-designer">Graphic designer</option>
                        </select>
                        <button type="submit" class="submit-btn">Submit</button>
                    </div> -->