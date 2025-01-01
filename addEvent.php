<?php
session_start();
$conn = mysqli_connect("localhost", "root", "1234", "sutclubs");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$club_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$club_query = mysqli_query($conn, "SELECT name FROM clubs WHERE id = $club_id");
$club = mysqli_fetch_assoc($club_query);
$event_query = mysqli_query($conn, "SELECT * FROM events ");
$event = mysqli_fetch_assoc($event_query);


if (!$club) {
    die("Club not found");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $date_time = mysqli_real_escape_string($conn, $_POST['date_time']);
    $max_registrations = intval($_POST['max_registrations']);
    $event_id =$_POST['event_id'];


    // Handle file upload
    if (isset($_FILES['event_photo']) && $_FILES['event_photo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['event_photo']['tmp_name'];
        $fileName = $_FILES['event_photo']['name'];
        $fileSize = $_FILES['event_photo']['size'];
        $fileType = $_FILES['event_photo']['type'];

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
                $photo_url = $destPath;

                $insert_query = "INSERT INTO events (club_id, title, description, date_time, photo_url, max_registrations,id) VALUES (?, ?, ?, ?, ?, ?,?)";
                $stmt = mysqli_prepare($conn, $insert_query);
                mysqli_stmt_bind_param($stmt, 'isssssi', $club_id, $title, $description, $date_time, $photo_url, $max_registrations,$event_id);

                if (mysqli_stmt_execute($stmt)) {
                    header("Location: manageEvents.php?club_id=" . urlencode($club_id));
                    exit();
                } else {
                    $error = "Error adding event: " . mysqli_error($conn);
                }
            } else {
                $error = "Error moving uploaded file.";
            }
        }
    } else {
        $error = "File upload error.";
    }

    if (!empty($error)) {
        echo "<p class='error'>$error</p>";
    }
}
?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="addEvent.css">
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

    <main>


    <h2 class="title">Add Events: <?php echo htmlspecialchars($club['name']); ?></h2>
    <div class="form-container">
        <form id="editClubForm" action="" method="POST" enctype="multipart/form-data">
            <div class="form-left">
            <div class="form-group">
                    <label for="name"> Event ID</label>
                    <input type="text" id="event_id" name="event_id"  >
                </div>
                <div class="form-group">
                    <label for="name"> Event Name</label>
                    <input type="text" id="name" name="name"  >
                </div>
                <div class="form-group">
                    <label for="description"> Event Description</label>
                    <textarea id="description" name="description" ></textarea>
                </div>
                <div class="form-group">
                <label for="date_time">Date and Time:</label>
                <input type="datetime-local" id="date_time" name="date_time"  required>
            </div>
                <div class="form-group">
                    <label for="max_registrations">Maximum Registrations:</label>
                    <input type="number" id="max_registrations" name="max_registrations" min="1"  required>
                </div>
                <label>Upload Image</label>
                <div class="upload-section">
                    <div class="upload-area" id="uploadArea">
                    <img id="uploadIcon" >
                    <input type="file" id="event_photo" name="event_photo" accept="image/*" required>
                    </div>
                   
                </div>
               
                <button type="submit">Add Event</button>
            </div>
            
            </div>
        </form>
    </div>

    </main>

    <script src="editEvent.js"></script>
</body>
</html>
