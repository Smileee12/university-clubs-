<?php
// Database connection
$config = require 'config.php';
$conn = mysqli_connect("localhost", "root", $config["password"], "sutclubs");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Query to fetch clubs
$query = mysqli_query($conn, "SELECT * FROM clubs");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SUTCLUBS - Join a Club</title>
    <link rel="stylesheet" href="CLUBS.css">
    
</head>
<body>

<header class="navbar">
      <div class="logo">SUTCLUBS</div>
      <nav class="nav-center">
      </nav>
      <div class="nav-right">
        <a href="./LogInPage.html" class="signup-button">Log In</a>
      </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <img src="uploads/webProject1.webp" alt="Hero Image" class="hero-image">
        <div class="hero-text">Explore and Join Clubs</div>
    </section>

    <!-- Club Cards -->
    <section class="club-cards-container">
        <?php while ($row = mysqli_fetch_assoc($query)): ?>
            <div class="club-card">
                <img src="uploads/<?= htmlspecialchars($row["image_path"]) ?>" alt="<?= htmlspecialchars($row["name"]) ?> Club" class="club-image">
                <div class="club-content">
                    <h2 class="club-name"><?= htmlspecialchars($row["name"]) ?></h2>
                    <p class="club-description"><?= htmlspecialchars($row["description"]) ?></p>
                    <!-- Join Button -->
                    <a href="./LogInPage.html" class="join-button">Join Club</a>
                </div>
            </div>
        <?php endwhile; ?>
    </section>

    <script src="CLUBS.js"></script>
</body>
</html>
