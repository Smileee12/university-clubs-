<?php
// Database connection
$config = require 'config.php';
$conn = mysqli_connect("localhost", "root", $config['password'], "sutclubs");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Query to fetch events
$query = mysqli_query($conn, "SELECT * FROM events ORDER BY date_time DESC");
if (!$query) {
    die("Query failed: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SUTCLUBS - Explore Events</title>
    <link rel="stylesheet" href="EVENTS.css">
</head>
<body>
    <header class="navbar">
        <div class="logo">SUTCLUBS</div>
        <nav class="nav-center">
            <a href="homePage.php" class="nav-item">Clubs</a>
        </nav>
        <div class="nav-right">
            <a href="./LogInPage.html" class="signup-button">Log In</a>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <img src="uploads/webProject1.webp" alt="Hero Image" class="hero-image">
        <div class="hero-text">Explore and Join Events</div>
    </section>

    <!-- Event Cards -->
    <section class="event-cards-container">
        <?php 
        if (mysqli_num_rows($query) > 0):
            while ($row = mysqli_fetch_assoc($query)): 
                $remainingSpots = $row["max_registrations"] - $row["current_registrations"];
        ?>
            <div class="event-card">
                <div class="event-image-container">
                    <img src="<?= htmlspecialchars($row["photo_url"]) ?>" alt="<?= htmlspecialchars($row["title"]) ?> Event" class="event-image">
                </div>
                <div class="event-content">
                    <h2 class="event-title"><?= htmlspecialchars($row["title"]) ?></h2>
                    <p class="event-description"><?= htmlspecialchars($row["description"]) ?></p>
                    <p class="event-date-time">Date & Time: <?= date("F j, Y, g:i a", strtotime($row["date_time"])) ?></p>
                    <p class="event-registration-info">
                        <?php if ($remainingSpots > 0): ?>
                            <strong>Remaining Spots:</strong> <?= $remainingSpots ?>
                        <?php else: ?>
                            <strong>Event Full</strong>
                        <?php endif; ?>
                    </p>
                    <!-- Register Button -->
                    <?php if ($remainingSpots > 0): ?>
                        <button class="register-button" data-event-id="<?= htmlspecialchars($row["id"]) ?>">Register for Event</button>
                    <?php else: ?>
                        <button disabled class="register-button">Event Full</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php 
            endwhile;
        else: 
        ?>
            <p>No events found.</p>
        <?php 
        endif;
        mysqli_free_result($query);
        mysqli_close($conn);
        ?>
    </section>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Add click event listeners to all register buttons
    document.querySelectorAll('.register-button:not([disabled])').forEach(button => {
        button.addEventListener('click', function() {
            const eventId = this.dataset.eventId;

            // Send registration request
            fetch('register-event-handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'eventId=' + encodeURIComponent(eventId)
            })
            .then(response => {
                // First log the raw response for debugging
                response.text().then(text => {
                    console.log('Raw response:', text);
                    try {
                        const data = JSON.parse(text);
                        alert(data.message);
                        if (data.status === 'success') {
                            window.location.reload();
                        }
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        alert('Error processing response from server');
                    }
                });
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Connection error: ' + error.message);
            });
        });
    });
});
</script>
</body>
</html>