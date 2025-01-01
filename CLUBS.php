<?php
session_start(); // Ensure the session is started before accessing $_SESSION

// if (!isset($_SESSION['loggedin']) || !isset($_SESSION['loggedin']['user_type']) || $_SESSION['loggedin']['user_type'] !== 'student') {
//     // Redirect to login page if the user is not logged in or user_type is not 'admin'
//     header('Location: LogInPage.html');
//     exit; // Always exit after a redirect to stop further script execution
// }

// For debugging: Check the session variable
// var_dump($_SESSION['loggedin']['user_type']);

// // For debugging: Check the session variable
// var_dump($_SESSION['loggedin']['user_type']);

$config = require 'config.php';
$conn = mysqli_connect("localhost", "root", $config['password'], "sutclubs");

// Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch all clubs
$query = mysqli_query($conn, "SELECT * FROM clubs");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SUTCLUBS Admin - Manage Clubs</title>
    <link rel="stylesheet" href="CLUBS.css">
</head>
<body>
<header class="navbar">
        <div class="logo">SUTCLUBS</div>
        <nav class="nav-center">
            <a href="./manageEvents.php" class="nav-item">Events</a>
        </nav>
        <div class="nav-right">
            <div class="profile-dropdown">
                <img
                    src="./uploads/profile.webp"
                    alt="Profile"
                    class="profile-logo"
                    id="profileIcon"
                />
                <div class="dropdown-content" id="userDropdown">
                    <div id="userInfo"></div>
                    <a href="./logOut.php" id="logoutButton">Logout</a>
                </div>
            </div>
        </div>
    </header>
    <!-- Hero Section -->
    <section class="hero">
        <img src="uploads/clubs.webp" alt="Hero Image" class="hero-image">
        <div class="hero-text">Manage Our Clubs</div>
    </section>

    <!-- Club Cards -->
    <section class="club-cards-container">
        <!-- Add Club Card -->
        <div class="club-card add-club-card">
            <div class="club-content">
                <h2 class="club-name">Create a New Club</h2>
                <p class="club-description">Start something amazing today!</p>
                <a href="addClub.php" class="join-button">Add Club</a>
            </div>
        </div>
        <?php while ($row = mysqli_fetch_assoc($query)): ?>
            <div class="club-card">
                <img src="uploads/<?= htmlspecialchars($row["image_path"]) ?>" alt="<?= htmlspecialchars($row["name"]) ?> Club" class="club-image">
                <div class="club-content">
                    <h2 class="club-name"><?= htmlspecialchars($row["name"]) ?></h2>
                    <p class="club-description"><?= htmlspecialchars($row["description"]) ?></p>
                    <a href="adminHome.php?id=<?= urlencode($row["id"]) ?>" class="join-button">Manage Club</a>
                </div>
            </div>
        <?php endwhile; ?>
    </section>

    <script src="CLUBS.js"></script>
</body>
</html>
<?php
// Close the database connection
mysqli_close($conn);
?>
