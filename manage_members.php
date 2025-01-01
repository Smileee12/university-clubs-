<?php
session_start();
$config = include('config.php');

$club_id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
if ($club_id === 0) {
    die("Invalid club ID");
}

$conn = mysqli_connect("localhost", "root", $config['password'], "sutclubs");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch club details
$club_query = mysqli_prepare($conn, "SELECT id, name FROM clubs WHERE id = ?");
mysqli_stmt_bind_param($club_query, "i", $club_id);
mysqli_stmt_execute($club_query);
$club_result = mysqli_stmt_get_result($club_query);
$club = mysqli_fetch_assoc($club_result);

if (!$club) {
    die("Club not found");
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["action"])) {
        $member_id = isset($_POST["member_id"]) ? intval($_POST["member_id"]) : 0;

        switch ($_POST["action"]) {
            case "update":
                $full_name = mysqli_real_escape_string($conn, $_POST["full_name"]);
                $email = mysqli_real_escape_string($conn, $_POST["email"]);
                $phone = mysqli_real_escape_string($conn, $_POST["phone"]);

                $update_query = mysqli_prepare($conn, "UPDATE club_members SET full_name = ?, email = ?, phone = ? WHERE id = ? AND club_name = ?");
                mysqli_stmt_bind_param($update_query, "sssis", $full_name, $email, $phone, $member_id, $club['name']);
                mysqli_stmt_execute($update_query);
                break;

            case "approve":
                $approve_query = mysqli_prepare($conn, "UPDATE club_members SET status = 'approved' WHERE id = ? AND club_name = ?");
                mysqli_stmt_bind_param($approve_query, "is", $member_id, $club['name']);
                mysqli_stmt_execute($approve_query);
                break;

            case "assign_role":
                $role = mysqli_real_escape_string($conn, $_POST["role"]);

                $assign_role_query = mysqli_prepare($conn, "UPDATE club_members SET role = ? WHERE id = ? AND club_name = ?");
                mysqli_stmt_bind_param($assign_role_query, "sis", $role, $member_id, $club['name']);
                mysqli_stmt_execute($assign_role_query);
                break;

            case "delete":
                $delete_query = mysqli_prepare($conn, "DELETE FROM club_members WHERE id = ? AND club_name = ?");
                mysqli_stmt_bind_param($delete_query, "is", $member_id, $club['name']);
                mysqli_stmt_execute($delete_query);
                break;
        }
    }
}

// Fetch club members
$members_query = mysqli_prepare($conn, "SELECT * FROM club_members WHERE club_name = ?");
mysqli_stmt_bind_param($members_query, "s", $club['name']);
mysqli_stmt_execute($members_query);
$members_result = mysqli_stmt_get_result($members_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Members - <?= htmlspecialchars($club["name"]) ?> - SUTCLUBS Admin</title>
    <link rel="stylesheet" href="./adminHomeSt.css">
    <link rel="stylesheet" href="./members.css">
    

</head>
<body>
    <nav class="navbar">
        <div class="logo">SUTCLUBS Admin</div>
        <div class="nav-links">
            <a href="CLUBS.php">Clubs</a>
            <a href="#">Events</a>
        </div>
        <div class="user-profile">
            <img src="user-icon.png" alt="User Profile" class="profile-icon">
        </div>
    </nav>

    <div class="search-bar">
    <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search by name, ID, email, or phone">
</div>

    <main class="main-content">
        <h1 class="title">Manage Members: <?= htmlspecialchars($club["name"]) ?></h1>
        <div class="member-list">
            <h2>Club Members</h2>
           
        </div>

            <table>
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Student ID</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($member = mysqli_fetch_assoc($members_result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($member["full_name"]) ?></td>
                            <td><?= htmlspecialchars($member["student_id"]) ?></td>
                            <td><?= htmlspecialchars($member["email"]) ?></td>
                            <td><?= htmlspecialchars($member["phone"]) ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="action" value="assign_role">
                                    <input type="hidden" name="member_id" value="<?= $member['id'] ?>">
                                    <select name="role" onchange="this.form.submit()">
                                        <option value="member" <?= $member['role'] == 'member' ? 'selected' : '' ?>>Member</option>
                                        <option value="admin" <?= $member['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                        <option value="vice_president" <?= $member['role'] == 'vice_president' ? 'selected' : '' ?>>Vice President</option>
                                        <option value="secretary" <?= $member['role'] == 'secretary' ? 'selected' : '' ?>>Secretary</option>
                                    </select>
                                </form>
                            </td>
                            <td><?= htmlspecialchars($member["status"]) ?></td>
                            <td>
                                <button onclick="showEditForm(<?= $member['id'] ?>)" class="admin-button">Edit</button>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="approve">
                                    <input type="hidden" name="member_id" value="<?= $member['id'] ?>">
                                    <button type="submit" class="admin-button">Approve</button>
                                </form>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="member_id" value="<?= $member['id'] ?>">
                                    <button type="submit" class="delete-button">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <tr id="edit-form-<?= $member['id'] ?>" class="edit-form">
                            <td colspan="7">
                                <form method="POST">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="member_id" value="<?= $member['id'] ?>">
                                    <label>Full Name: <input type="text" name="full_name" value="<?= htmlspecialchars($member['full_name']) ?>"></label>
                                    <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($member['email']) ?>"></label>
                                    <label>Phone: <input type="tel" name="phone" value="<?= htmlspecialchars($member['phone']) ?>"></label>
                                    <button type="submit" class="admin-button">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        function showEditForm(memberId) {
            const editForm = document.getElementById(`edit-form-${memberId}`);
            if (editForm) {
                editForm.classList.toggle('active');
            }
        }

        
    function filterTable() {
        const input = document.getElementById("searchInput");
        const filter = input.value.toLowerCase();
        const table = document.querySelector("table tbody");
        const rows = table.querySelectorAll("tr");

        rows.forEach(row => {
            const columns = row.querySelectorAll("td");
            let match = false;

            // Check all columns except the Actions column
            columns.forEach((column, index) => {
                if (index < columns.length - 1 && column.textContent.toLowerCase().includes(filter)) {
                    match = true;
                }
            });

            row.style.display = match ? "" : "none";
        });
    }


    </script>
</body>
</html>
