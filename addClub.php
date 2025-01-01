<?php
session_start();
$config=include('config.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = mysqli_connect("localhost", "root", $config["password"], "sutclubs");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $id=mysqli_real_escape_string($conn,$_POST["ID"]);

    // Handle file upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    // Check if image file is actual image or fake image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if($check !== false) {
        // File is an image
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = basename($_FILES["image"]["name"]);
            
            $query = mysqli_prepare($conn, "INSERT INTO clubs (name, description, image_path,id) VALUES (?, ?, ?,?)");
            mysqli_stmt_bind_param($query, "ssss", $name, $description, $image_path,$id);
            
            if (mysqli_stmt_execute($query)) {
                header("Location: CLUBS.php");
                exit();
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        echo "File is not an image.";
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Club - SUTCLUBS Admin</title>
    <link rel="stylesheet" href="./addClub.css">
</head>
<body>
<header>
        <div class="header-content">
            <h1>SUTCLUBS Admin</h1>
            <nav>
                <a href="CLUBS.php">Clubs</a>
                <a href="#">Events</a>
            </nav>
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
        </div>
    </header>

    <main>
        <div class="form-container">
            <h1 class="title">ADD CLUB</h1>
            <form id="addClubForm" action="addClub.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                    <label for="clubName">Club ID</label>
                    <input type="text" id="clubID" name="ID" required>
                </div>
                <div class="form-group">
                    <label for="clubName">Club Name</label>
                    <input type="text" id="clubName" name="name" required>
                </div>

                <div class="form-group">
                    <label for="clubDescription">Club Description</label>
                    <textarea id="clubDescription" name="description" required></textarea>
                </div>

                <div class="form-group upload-section">
                    <label for="clubImage">Upload Image</label>
                    <div class="upload-area" id="uploadArea">
                        <img src="upload-icon.svg" alt="Upload" id="uploadIcon">
                        <input type="file" id="clubImage" name="image" accept="image/*" required>
                    </div>
                    <div id="imagePreview" class="image-preview"></div>
                </div>

                <button type="submit">Add Club</button>
            </form>
        </div>
    </main>

    <script src="./addClub.js"></script>
</body>
</html>