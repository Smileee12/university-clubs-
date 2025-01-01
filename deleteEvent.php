<?php
session_start();
$conn = mysqli_connect("localhost", $config['username'], $config['password'], "sutclubs");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$event_query = mysqli_query($conn, "SELECT club_id FROM events WHERE id = $event_id");
$event = mysqli_fetch_assoc($event_query);

if (!$event) {
    die("Event not found");
}

$delete_query = "DELETE FROM events WHERE id = $event_id";

if (mysqli_query($conn, $delete_query)) {
    header("Location: manageEvents.php?club_id=" . $event['club_id']);
    exit();
} else {
    die("Error deleting event: " . mysqli_error($conn));
}
?>

