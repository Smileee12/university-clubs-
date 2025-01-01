<?php
// Database connection (update with your credentials)
$config=require 'config.php';
$servername = "localhost";
$username = "root";
$password = $config['password'];
$dbname = "sutclubs";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch statistics
$total_clubs = $conn->query("SELECT COUNT(*) as total FROM clubs")->fetch_assoc()['total'];
$total_events = $conn->query("SELECT COUNT(*) as total FROM events")->fetch_assoc()['total'];
$total_users = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];

// Fetch top clubs
$top_clubs_query = "
    SELECT club_name, COUNT(*) as members_count 
    FROM club_members 
    GROUP BY club_name 
    ORDER BY members_count DESC 
    LIMIT 5";
$top_clubs_result = $conn->query($top_clubs_query);

// Fetch top events
$top_events_query = "
    SELECT title, current_registrations as registrations 
    FROM events 
    ORDER BY registrations DESC 
    LIMIT 5";
$top_events_result = $conn->query($top_events_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SUTCLUBS</title>
    <style>
        :root {
            --primary-green: #1a3d3d;
            --dark-overlay: rgba(0, 0, 0, 0.6);
            --background-gray: #f4f4f4;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: var(--background-gray);
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 10;
        }

        .logo {
            font-weight: bold;
            font-size: 1.5rem;
            color: var(--primary-green);
        }

        .nav-links a {
            margin: 0 1rem;
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }

        .dashboard-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 4rem;
            padding: 2rem;
        }

        .stats-summary {
            display: flex;
            justify-content: space-around;
            width: 100%;
            max-width: 1200px;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-box {
            background-color: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            flex: 1;
        }

        .stat-box h3 {
            font-size: 1.5rem;
            color: var(--primary-green);
            margin-bottom: 1rem;
        }

        .stat-box p {
            font-size: 1.2rem;
            color: #555;
        }

        section {
            width: 100%;
            max-width: 1200px;
            margin-bottom: 2rem;
            background-color: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        section h2 {
            margin-bottom: 1rem;
            font-size: 1.8rem;
            color: var(--primary-green);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 1rem;
            border: 1px solid #ddd;
            text-align: left;
        }

        table th {
            background-color: var(--primary-green);
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <header class="navbar">
        <div class="logo">SUTCLUBS</div>
        <nav class="nav-links">
            <a href="CLUBS.php">Clubs</a>
            <a href="logOut.php">Logout</a>
        </nav>
    </header>
    <div class="dashboard-container">
        <div class="stats-summary">
            <div class="stat-box">
                <h3>Total Clubs</h3>
                <p><?php echo $total_clubs; ?></p>
            </div>
            <div class="stat-box">
                <h3>Total Events</h3>
                <p><?php echo $total_events; ?></p>
            </div>
            <div class="stat-box">
                <h3>Total Users</h3>
                <p><?php echo $total_users; ?></p>
            </div>
        </div>
        <section class="top-clubs">
            <h2>Top Clubs (Most Members)</h2>
            <table>
                <thead>
                    <tr>
                        <th>Club Name</th>
                        <th>Members</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($club = $top_clubs_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $club['club_name']; ?></td>
                        <td><?php echo $club['members_count']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
        <section class="top-events">
            <h2>Top Events (Most Registrations)</h2>
            <table>
                <thead>
                    <tr>
                        <th>Event Title</th>
                        <th>Registrations</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($event = $top_events_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $event['title']; ?></td>
                        <td><?php echo $event['registrations']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>
<?php
$conn->close();
?>
