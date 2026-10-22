<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied: You must be an administrator to manage students.");
}

$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$query = "SELECT * FROM students WHERE full_name LIKE ? OR email LIKE ? OR course LIKE ? ORDER BY student_id DESC";
$stmt = $conn->prepare($query);
$search_param = "%" . $search . "%";
$stmt->bind_param("sss", $search_param, $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Students</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .admin-table th, .admin-table td { padding: 10px; border: 1px solid var(--border-color); text-align: left; }
        .admin-table th { background-color: var(--heading-sub); color: white; }
        .action-btn { padding: 5px 10px; color: white; text-decoration: none; border-radius: 3px; font-size: 14px; }
        .btn-edit { background-color: #f39c12; }
        .btn-delete { background-color: #c0392b; }
        .add-btn { background-color: #27ae60; padding: 10px 15px; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; margin-bottom: 20px; display: inline-block; }
    </style>
</head>
<body>
    <div class="content-box" style="max-width: 1000px;">
        <center>
            <h1>Student Management</h1>
            <a href="admin_dashboard.php" style="margin-bottom: 20px;">Back to Dashboard</a>
        </center>
        
        <?php if (isset($_GET['msg'])) echo "<div class='notification-banner'>" . htmlspecialchars($_GET['msg']) . "</div>"; ?>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <a href="add_student.php" class="add-btn">+ Add New Student</a>
            
            <form method="GET" action="manage_students.php" style="display: flex; gap: 10px;">
                <input type="text" name="search" placeholder="Search by name, email, course..." value="<?php echo htmlspecialchars($search); ?>" style="padding: 8px; width: 250px; border-radius: 4px; border: 1px solid #ccc;">
                <button type="submit" style="padding: 8px 15px; background-color: #2980b9; color: white; border: none; border-radius: 4px;">Search</button>
                <a href="manage_students.php" style="padding: 8px 15px; background-color: #95a5a6; color: white; text-decoration: none; border-radius: 4px; font-size: 14px;">Clear</a>
            </form>
        </div>

        <table class="admin-table">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Course</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['course']); ?></td>
                <td>
                    <a href="edit_student.php?id=<?php echo $row['student_id']; ?>" class="action-btn btn-edit">Edit</a>
                    <a href="delete_student.php?id=<?php echo $row['student_id']; ?>" class="action-btn btn-delete" onclick="return confirm('Are you sure you want to permanently delete this student record?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>