<?php
session_start();
require 'db_connect.php';

// Restrict access to admin users only
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied: You must be an administrator.");
}

// Fetch logs and join with the students table to get the admin's name
$query = "SELECT a.log_id, s.full_name AS admin_name, a.action_type, a.description, a.created_at 
          FROM audit_logs a 
          JOIN students s ON a.admin_id = s.student_id 
          ORDER BY a.created_at DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Audit Logs</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .admin-table th, .admin-table td { padding: 10px; border: 1px solid var(--border-color); text-align: left; }
        .admin-table th { background-color: var(--heading-sub); color: white; }
    </style>
</head>
<body>
    <div class="content-box" style="max-width: 1000px;">
        <center>
            <h1>System Audit Logs</h1>
            <a href="dashboard.php" style="margin-bottom: 20px; display: inline-block;">Back to Dashboard</a>
        </center>
        
        <table class="admin-table">
            <tr>
                <th>Log ID</th>
                <th>Admin Name</th>
                <th>Action</th>
                <th>Description</th>
                <th>Timestamp</th>
            </tr>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['log_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['admin_name']); ?></td>
                    <td><strong><?php echo htmlspecialchars($row['action_type']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align: center;">No audit logs found.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>