<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$config = include('config.php');

// Fetch user data
$id = $_SESSION["id"];
$full_name = $_SESSION["full_name"];
$email = $_SESSION["email"];

// Database connection
$servername = "localhost";
$username = $config['username'];
$password = $config['password'];
$dbname = "sutclubs";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user's clubs with status
$user_clubs_query = "
    SELECT c.name AS club_name, cm.status AS membership_status
    FROM club_members cm
    JOIN clubs c ON cm.club_name = c.name
    WHERE cm.email = ?";

$stmt = $conn->prepare($user_clubs_query);
$stmt->bind_param("s", $email);
$stmt->execute();
$clubs_result = $stmt->get_result();

$user_clubs = [];
while ($row = $clubs_result->fetch_assoc()) {
    $user_clubs[] = ['club_name' => $row['club_name'], 'membership_status' => $row['membership_status']];
}

// Fetch user's events
$user_events_query = "
    SELECT e.title AS event_title, e.date_time
    FROM event_registrations er
    JOIN events e ON er.event_id = e.id
    JOIN users u ON er.user_id = u.id
    WHERE u.email = ?";

$stmt = $conn->prepare($user_events_query);
$stmt->bind_param("s", $email);
$stmt->execute();
$events_result = $stmt->get_result();

$user_events = [];
while ($row = $events_result->fetch_assoc()) {
    $user_events[] = ['title' => $row['event_title'], 'date_time' => $row['date_time']];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
        :root {
            --primary-green: rgba(34, 197, 94, 0.9);
            --dark-overlay: rgba(0, 0, 0, 0.6);
            --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: url('./uploads/webProject1.webp') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            color: var(--white);
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background-color: var(--white);
        }

        .nav-right {
  display: flex;
  gap: 10px;
  align-items: center;
  text-decoration: none; /* Remove underline from text */
  color: black; /* Set color to black */
}

        .logo {
            font-weight: bold;
            font-size: 1.2rem;
            color: black;
        }

        .nav-right {
  display: flex;
  gap: 10px;
  align-items: center;
  text-decoration: none; /* Remove underline from text */
  color: black; /* Set color to black */
}

.signup-button {
  padding: 10px 20px;
  background-color: black;
  color: white;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 500;
  cursor: pointer;
  text-decoration: none;
}


        .main-content {
            position: relative;
            min-height: calc(100vh - 4rem);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            text-align: center;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: var(--dark-overlay);
            z-index: 1;
        }

        .content {
            z-index: 2;
            max-width: 900px;
            width: 100%;
        }

        .welcome {
            font-size: 2rem;
            margin-bottom: 1.5rem;
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            margin-top: 2rem;
        }

        .card {
            background: var(--white);
            color: black;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: left;
        }

        .card h3 {
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        .card ul {
            list-style: none;
            padding: 0;
        }

        .card li {
            margin-bottom: 0.5rem;
        }

        .button-container {
            margin-top: 2rem;
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .action-button, .logout-button {
            padding: 1rem;
            font-size: 1rem;
            border: none;
            border-radius: 8px;
            background-color: var(--primary-green);
            color: var(--white);
            cursor: pointer;
            transition: transform 0.2s;
        }

        .action-button:hover, .logout-button:hover {
            transform: scale(1.02);
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">SUTCLUBS</div>

        <div class="nav-right">
            <a href="logOut.php" class="signup-button">Log Out</a>
        </div>
    </div>
    <div class="main-content">
        <div class="overlay"></div>
        <div class="content">
            <div class="welcome">
                Welcome, <?php echo htmlspecialchars($full_name); ?>!
            </div>

            <div class="cards-container">
                <!-- Clubs Card -->
                <div class="card">
                    <h3>Your Clubs:</h3>
                    <ul>
                        <?php if (count($user_clubs) > 0): ?>
                            <?php foreach ($user_clubs as $club): ?>
                                <?php if ($club['membership_status'] === 'approved'): ?>
                                    <li><?php echo htmlspecialchars($club['club_name']); ?></li>
                                <?php elseif ($club['membership_status'] === 'Pending'): ?>
                                    <li style="color: black;"><?php echo htmlspecialchars($club['club_name']); ?> - Pending Approval</li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>You are not a member of any clubs yet.</li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Events Card -->
                <div class="card">
                    <h3>Your Registered Events:</h3>
                    <ul>
                        <?php if (count($user_events) > 0): ?>
                            <?php foreach ($user_events as $event): ?>
                                <li>
                                    <?php echo htmlspecialchars($event['title']); ?> 
                                    <br>
                                    <small>On: <?php echo date('F j, Y, g:i a', strtotime($event['date_time'])); ?></small>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>You are not registered for any events yet.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <div class="button-container">
                <button class="action-button" onclick="window.location.href='userClubs.php';">Explore Clubs</button>
                <button class="action-button" onclick="window.location.href='userEvents.php';">Explore Events</button>
            </div>
        </div>
    </div>

    <!-- Debugging: Output user clubs and membership statuses -->
    <script>
        console.log("User Clubs and Status:", <?php echo json_encode($user_clubs); ?>);
    </script>
</body>
</html>
