<?php
$config = include('config.php');
$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
if ($id === 0) {
    die("Invalid club ID");
}

$conn = mysqli_connect("localhost", $config['username'], $config['password'], "sutclubs");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$query = mysqli_prepare($conn, "DELETE FROM clubs WHERE id = ?");
mysqli_stmt_bind_param($query, "i", $id);

if (mysqli_stmt_execute($query)) {
    header("Location: CLUBS.php");
    exit();
} else {
    echo "Error deleting club: " . mysqli_error($conn);
}

mysqli_close($conn);
?>