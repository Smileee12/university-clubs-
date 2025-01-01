<?php
session_start();
// if($_SESSION['user']['user_type']!='admin'){
//     header("location:LogInPage.html");
//     exit();
// }
$config=include('config.php');
$conn = mysqli_connect("localhost", "root",$config["password"], "sutclubs");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$club_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    $update_query = "UPDATE clubs SET name = '$name', description = '$description' WHERE id = $club_id";
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_file = $_FILES["image"]["name"];

        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = mysqli_real_escape_string($conn, $target_file);
            $update_query = "UPDATE clubs SET name = '$name', description = '$description', image_path = '$image_path' WHERE id = $club_id";
        }
    }
    
    if (mysqli_query($conn, $update_query)) {
        // Redirect using club_id
        header("Location: CLUBS.php" );
        exit();
    } else {
        $error = "Error updating club: " . mysqli_error($conn);
    }
}

$query = mysqli_query($conn, "SELECT * FROM clubs WHERE id = $club_id");
$club = mysqli_fetch_assoc($query);

if (!$club) {
    die("Club not found");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Club - SUTCLUBS</title>
    <link rel="stylesheet" href="edit.css">
</head>
<body>
<header>
    <div class="header-content">
        <h1>SUTCLUBS</h1>
        <nav>
            <a href="CLUBS.php">Home</a>
            <a href="adminHome.php?id=<?php echo htmlspecialchars($club['id']); ?>">Back to Club</a>
        </nav>
        <div class="nav-right">
                <div class="profile-dropdown">
                    <img
                        src="img/placeholder-profile-icon-64037ijusubkr7gu.png"
                        alt="Profile Icon"
                        class="profile-logo"
                        id="profileIcon"
                        tabindex="0"
                        aria-haspopup="true"
                        aria-expanded="false"
                    />
                    <div class="dropdown-content" id="userDropdown" aria-label="User Menu">
                        <div id="userInfo">Loading user info...</div>
                        <a href="./logOut.php" id="logoutButton">Logout</a>
                    </div>
                </div>
            </div>
    </div>
</header>

    <main>
    <h2 class="title">Edit Club: <?php echo htmlspecialchars($club['name']); ?></h2>
    <div class="form-container">
        <form id="editClubForm" action="" method="POST" enctype="multipart/form-data">
            <div class="form-left">
                <div class="form-group">
                    <label for="name">Edit Club Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($club['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="description">Edit Club Description</label>
                    <textarea id="description" name="description" required><?php echo htmlspecialchars($club['description']); ?></textarea>
                </div>
                
                <button type="submit">Update Club</button>
            </div>
            <div class="form-right">
                <label>Update Image</label>
                <div class="upload-section">
                    <div class="upload-area" id="uploadArea">
                    <img id="uploadIcon" src="<?php echo htmlspecialchars($club['image_path']); ?>" alt="Upload Icon" style="width: 200px; height:100px">
                        <input type="file" id="clubImage" name="image" accept="image/*">
                    </div>
                   
                </div> 
            </div>
        </form>
    </div>
</main>
    <script src="edit.js"></script>
</body>
</html>
