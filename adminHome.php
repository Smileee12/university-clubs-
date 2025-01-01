






<?php
session_start();
// if (!isset($_SESSION['user'])) {
//     header("Location: LogInPage.html");
//     exit();
// }
// if($_SESSION['user']['role'] != 'admin'){
//     header("Location: LogInPage.html");
//     exit();
// }


$config = include('config.php');
$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
if ($id === 0) {
    die("Invalid club ID");
}

$conn = mysqli_connect("localhost", "root", $config['password'], "sutclubs");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$query = mysqli_prepare($conn, "SELECT * FROM clubs WHERE id = ?");
mysqli_stmt_bind_param($query, "i", $id);
mysqli_stmt_execute($query);
$result = mysqli_stmt_get_result($query);
$club = mysqli_fetch_assoc($result);

if (!$club) {
    die("Club not found");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Club - SUTCLUBS Admin</title>
    <link rel="stylesheet" href="./adminHomeSt.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">SUTCLUBS Admin</div>
        <div class="nav-links">
            <a href="CLUBS.php">Clubs</a>
        </div>
       
        <div class="nav-right">
                    <div class="profile-dropdown">
                        <img
                            src="img/placeholder-profile-icon-64037ijusubkr7gu.png"
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
    </nav>


    <main class="main-content">
        <div class="overlay"></div>
        <h1 class="title">Manage Club: <?= htmlspecialchars($club["name"]) ?></h1>
        
        <div class="button-container">
            <a href="edit.php?id=<?= $id ?>" class="admin-button">Edit Club</a>
            <a href="deleteClub.php?id=<?php echo $club['id']; ?>" class="admin-button">Delete Club</a>

            <a href="manageEvents.php?club_id=<?php echo $club['id']; ?>" class="admin-button">Manage Events</a>

            <a href="manage_members.php?id=<?= $id ?>" class="admin-button">Manage Members</a>


        </div>
    </main>

    <script src="adminHome.js">
     
    </script>
</body>
</html>



