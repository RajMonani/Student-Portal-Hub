<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied: You must be an administrator.");
}

$query = "SELECT * FROM events ORDER BY event_date DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Events</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .admin-table th, .admin-table td { padding: 10px; border: 1px solid var(--border-color); text-align: left; vertical-align: middle; }
        .admin-table th { background-color: var(--heading-sub); color: white; }
        .action-btn { padding: 5px 10px; color: white; text-decoration: none; border-radius: 3px; font-size: 14px; }
        .btn-edit { background-color: #f39c12; }
        .btn-delete { background-color: #c0392b; }
        .add-btn { background-color: #27ae60; padding: 10px 15px; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block; margin-bottom: 20px; }
        .poster-thumb { width: 80px; height: auto; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="content-box" style="max-width: 1000px;">
        <center>
            <h1>Event Management</h1>
            <a href="admin_dashboard.php" style="margin-bottom: 20px;">Back to Dashboard</a>
        </center>
        
        <?php if (isset($_GET['msg'])) echo "<div class='notification-banner'>" . htmlspecialchars($_GET['msg']) . "</div>"; ?>

        <a href="add_event.php" class="add-btn">+ Add New Event</a>

        <table class="admin-table">
            <tr>
                <th>Poster</th>
                <th>Event Name</th>
                <th>Type</th>
                <th>Date</th>
                <th>Venue</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td>
                    <?php if ($row['poster_image']): ?>
                        <img src="uploads/<?php echo htmlspecialchars($row['poster_image']); ?>" class="poster-thumb" alt="Poster">
                    <?php else: ?>
                        <span style="color: #999; font-size: 12px;">No Image</span>
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($row['event_name']); ?></td>
                <td><?php echo htmlspecialchars($row['event_type']); ?></td>
                <td><?php echo htmlspecialchars($row['event_date']); ?></td>
                <td><?php echo htmlspecialchars($row['venue']); ?></td>
                <td>
                    <a href="edit_event.php?id=<?php echo $row['event_id']; ?>" class="action-btn btn-edit">Edit</a>
                    <a href="delete_event.php?id=<?php echo $row['event_id']; ?>" class="action-btn btn-delete" onclick="return confirm('Delete this event? The poster file will also be removed.');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>