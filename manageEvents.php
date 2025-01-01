<?php
session_start();
$config = include('config.php');
$conn = mysqli_connect("localhost", $config['username'], $config['password'], "sutclubs");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error()); 
}

$club_id = isset($_GET['club_id']) ? intval($_GET['club_id']) : 0;

$club_query = mysqli_query($conn, "SELECT name FROM clubs WHERE id = $club_id");
$club = mysqli_fetch_assoc($club_query);

if (!$club) {
    die("Club not found");
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where_clause = $search ? "AND (title LIKE '%$search%' OR description LIKE '%$search%')" : '';

$events_query = mysqli_query($conn, "SELECT * FROM events WHERE club_id = $club_id $where_clause ORDER BY date_time DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events - <?php echo htmlspecialchars($club['name']); ?></title>
    <link rel="stylesheet" href="manageEvents.css">
</head>
<body>
    <!-- Navbar -->
    <header class="navbar">
        <a href="#" class="logo">SUTCLUBS Admin</a>
        <nav class="nav-center">
            <a href="CLUBS.php" class="nav-item">Clubs</a>
            <a href="#" class="nav-item">Events</a>
        </nav>
        <div class="profile-dropdown">
            <img src="./uploads/profile.webp" alt="Profile" class="profile-logo" id="profileIcon">
            <div class="dropdown-content" id="userDropdown">
                <a href="./logOut.php" id="logoutButton">Logout</a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <img src="uploads/webProject1.webp" alt="Hero Image" class="hero-image">
        <div class="hero-text">Manage  <?php echo htmlspecialchars($club['name']) ; ?>'s Events</div>
    </section>


    <main>
        
        <div class="actions">
            <form action="" method="GET" class="search-form">
                <input type="hidden" name="club_id" value="<?php echo $club_id; ?>">
                <input type="text" name="search" placeholder="Search events..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <div class="events-list">
        <div class="event-card add-event-card">
            <div class="event-content">
                <h2 class="event-name">Create a New event</h2>
                <p class="event-description">Start something amazing today!</p>
                <a href="addEvent.php?id=<?php echo $club_id; ?>" class="join-button">Add Event</a>
            </div>
        </div>

            <?php while ($event = mysqli_fetch_assoc($events_query)): ?>
                <div class="event-card">
                <img src="<?php echo htmlspecialchars($event['photo_url']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" class="event-image">
                <p></p>
                <h2><?php echo htmlspecialchars($event['title']); ?></h2>
                    <p>Date: <?php echo date('Y-m-d H:i', strtotime($event['date_time'])); ?></p>
                    <p><?php echo htmlspecialchars(substr($event['description'], 0, 100)) . '...'; ?></p>
                    <p>Registrations: <?php echo $event['current_registrations']; ?> / <?php echo $event['max_registrations']; ?></p>
                    <div class="event-actions">
                        <a href="editEvent.php?id=<?php echo $event['id']; ?>" class="admin-button">Edit</a>
                        <a href="viewRegistrations.php?event_id=<?php echo $event['id']; ?>" class="admin-button">View Registrations</a>
                        <a href="deleteEvent.php?id=<?php echo $event['id']; ?>" class="admin-button delete-button" onclick="return confirm('Are you sure you want to delete this event?')">Delete</a>
                    </div>
                    
                </div>
                <?php endwhile; ?>
        </div>
    </main>
</body>
</html>