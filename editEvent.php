<?php
session_start();
$config = include('config.php');
$conn = mysqli_connect("localhost", "root",$config[ "password"], "sutclubs");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$event_query = mysqli_query($conn, "SELECT e.*, c.name AS club_name, c.id AS club_id FROM events e JOIN clubs c ON e.club_id = c.id WHERE e.id = $event_id");
$event = mysqli_fetch_assoc($event_query);

if (!$event) {
    die("Event not found");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = isset($_POST['name']) ? mysqli_real_escape_string($conn, $_POST['name']) : $event['title']; // Use existing title if not provided
    $description = isset($_POST['description']) ? mysqli_real_escape_string($conn, $_POST['description']) : $event['description']; // Use existing description if not provided
    $date_time = isset($_POST['date_time']) ? mysqli_real_escape_string($conn, $_POST['date_time']) : $event['date_time']; // Use existing date_time if not provided
    $photo_url = $event['photo_url']; // retain existing image path as default
    $max_registrations = intval($_POST['max_registrations']);

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $fileType = $_FILES['image']['type'];

        // Validate file type and size
        $allowedTypes = ['image/jpeg', 'image/png'];
        if (!in_array($fileType, $allowedTypes)) {
            $error = "Invalid file type. Only JPG and PNG are allowed.";
        } elseif ($fileSize > 2 * 1024 * 1024) {
            $error = "File size exceeds 2MB.";
        } else {
            $uploadFileDir = 'uploads/';
            $destPath = $uploadFileDir . basename($fileName);

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $photo_url = $destPath; // Update photo URL
            } else {
                $error = "Error moving uploaded file.";
            }
        }
    }

    if (!empty($error)) {
        echo "<p class='error'>$error</p>";
    } else {
        $update_query = "UPDATE events SET title = '$title', description = '$description', date_time = '$date_time', photo_url = '$photo_url', max_registrations = $max_registrations WHERE id = $event_id";
        
        if (mysqli_query($conn, $update_query)) {
            header("Location: manageEvents.php?club_id=" . $event['club_id']);
            exit();
        } else {
            echo "<p class='error'>Error updating event: " . mysqli_error($conn) . "</p>"; // Display SQL error
        }
    }
}
?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - <?php echo htmlspecialchars($event['title']); ?></title>
    <link rel="stylesheet" href="editEvent.css">
</head>
<body>
    <header>
        <div class="header-content">
            <h1>SUTCLUBS Admin</h1>
            <nav>
                <a href="CLUBS.php">Home</a>
                <a href="manageEvents.php?club_id=<?php echo $event['club_id']; ?>">Back to Events</a>
                <a href="logout.php">Logout</a>
            </nav>
            <div class="user-icon">
                <img src="img/placeholder-profile-icon-64037ijusubkr7gu.png" alt="User Icon">
            </div>
        </div>
    </header>

    <main>


    <!-- <h2 class="title">Edit Events: <?php echo htmlspecialchars($event['title']); ?></h2> -->
    <div class="form-container">
        <form id="editClubForm" action="" method="POST" enctype="multipart/form-data">
            <div class="form-left">
                <div class="form-group">
                    <label for="name">Edit Event Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($event['title']); ?>" >
                </div>
                <div class="form-group">
                    <label for="description">Edit Event Description</label>
                    <textarea id="description" name="description" ><?php echo htmlspecialchars($event['description']); ?></textarea>
                </div>
                <div class="form-group">
                <label for="date_time">Date and Time:</label>
                <input type="datetime-local" id="date_time" name="date_time" value="<?php echo date('Y-m-d\TH:i', strtotime($event['date_time'])); ?>" required>
            </div>
                <div class="form-group">
                    <label for="max_registrations">Maximum Registrations:</label>
                    <input type="number" id="max_registrations" name="max_registrations" min="1" value="<?php echo $event['max_registrations']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="current_registrations">Current Registrations:</label>
                    <input type="number" id="current_registrations" name="current_registrations" value="<?php echo $event['current_registrations']; ?>" readonly>
                </div>
                <button type="submit">Update Event</button>
            </div>
            <div class="form-right">
                <label>Update Image</label>
                <div class="upload-section">
                    <div class="upload-area" id="uploadArea">
                    <img id="uploadIcon" src="<?php echo htmlspecialchars($event['photo_url']); ?>" alt="Upload Icon" style="width: 200px; height:100px">
                        <input type="file" id="clubImage" name="image" accept="image/*">
                    </div>
                   
                </div>
            </div>
        </form>
    </div>

    </main>

    <script src="editEvent.js"></script>
</body>
</html>
