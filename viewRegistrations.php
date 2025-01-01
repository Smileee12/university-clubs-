<?php
session_start();
$config = include('config.php');

$conn = mysqli_connect("localhost", "root", $config["password"], "sutclubs");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

$event_query = mysqli_query($conn, "SELECT e.*, c.name AS club_name FROM events e JOIN clubs c ON e.club_id = c.id WHERE e.id = $event_id");
$event = mysqli_fetch_assoc($event_query);

if (!$event) {
    die("Event not found");
}

$registrations_query = mysqli_query($conn, "
    SELECT u.id, u.full_name, u.email, er.registration_date 
    FROM event_registrations er 
    JOIN users u ON er.user_id = u.id 
    WHERE er.event_id = $event_id 
    ORDER BY er.registration_date DESC
");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Registrations - <?php echo htmlspecialchars($event['title']); ?></title>
    <link rel="stylesheet" href="viewRegistrations.css">
</head>
<body>
    <header>
        <div class="header-content">
            <h1>SUTCLUBS Admin</h1>
            <nav>
                <a href="CLUBS.php">Home</a>
                <a href="manageEvents.php?club_id=<?php echo $event['club_id']; ?>">Back to Events</a>
            </nav>
            
        </div>
    </header>
    <section class="hero">
        <img src="uploads/webProject1.webp" alt="Hero Image" class="hero-image">
        <div class="hero-text">Users</div>
    </section>

    <main>
        <h2 class="title">Registrations for: <?php echo htmlspecialchars($event['title']); ?></h2>
        <!-- <p class="event-info">
            Club: <?php echo htmlspecialchars($event['club_name']); ?><br>
            Date: <?php echo date('Y-m-d H:i', strtotime($event['date_time'])); ?><br>
            Registrations: <?php echo $event['current_registrations']; ?> / <?php echo $event['max_registrations']; ?>
        </p> -->

        <table class="registrations-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Registration Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($registration = mysqli_fetch_assoc($registrations_query)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($registration['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($registration['email']); ?></td>
                        <td><?php echo date('Y-m-d H:i', strtotime($registration['registration_date'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
</body>
</html>

