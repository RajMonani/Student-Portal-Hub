<?php
session_start();
require 'db_connect.php';

// 1. Role-Based Access Control: Check login status
if (!isset($_SESSION['logged_in'])) {
    header("Location: login.html");
    exit();
}

$timeout_duration = 900; 
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: login.html?msg=timeout");
    exit();
}
$_SESSION['last_activity'] = time(); 

// Define if current user is an admin
$isAdmin = ($_SESSION['role'] === 'admin');

// 2. Fetch Aggregated Statistics if Admin
if ($isAdmin) {
    // Get Student Count
    $res = $conn->query("SELECT COUNT(student_id) AS total_students FROM students");
    $total_students = $res->fetch_assoc()['total_students'] ?? 0;

    // Get Event Count
    $res = $conn->query("SELECT COUNT(event_id) AS total_events FROM events");
    $total_events = $res->fetch_assoc()['total_events'] ?? 0;

    // Get Latest Registrations
    $recent_regs = $conn->query("
        SELECT s.full_name, e.event_name, r.registration_date 
        FROM registrations r
        JOIN students s ON r.student_id = s.student_id
        JOIN events e ON r.event_id = e.event_id
        ORDER BY r.registration_date DESC LIMIT 5
    ");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dynamic Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .stat-box { display: inline-block; width: 30%; background: var(--card-bg); padding: 20px; border: 1px solid var(--border-color); border-radius: 8px; margin: 10px; text-align: center; }
        .stat-box h3 { color: var(--heading-sub); margin: 0 0 10px 0; font-size: 20px; }
        .stat-box p { font-size: 32px; font-weight: bold; margin: 0; color: #27ae60; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .data-table th, .data-table td { padding: 10px; border: 1px solid var(--border-color); text-align: left; }
        .data-table th { background-color: var(--heading-sub); color: white; }
    </style>
</head>
<body>
    <div class="header-bar">
        <div id="hamburger" class="hamburger"><span></span><span></span><span></span></div>
        <button id="theme-toggle">🌙 Dark Mode</button>
    </div>
    
    <!-- 3. Dynamic Menu Based on Role -->
    <nav id="nav-menu" class="nav-menu active">
        <a href="index.html">Home</a>
        <a href="dashboard.php">Dashboard</a>
        <?php if ($isAdmin): ?>
            <a href="manage_event.php">Manage Events</a>
            <a href="ajax_students.php">Manage Students</a>
            <a href="view_logs.php">Audit Logs</a>
        <?php else: ?>
            <a href="FAQ.html">FAQ</a>
            <a href="profile.html">Profile</a>
            <a href="Event.html">Events</a>
        <?php endif; ?>
        <a href="logout.php">Logout</a>
    </nav>

    <div class="content-box" style="max-width: 1000px;">
        <center>
            <h1>Dashboard</h1>
            <strong>Welcome, <?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?>! (Role: <?php echo ucfirst($_SESSION['role']); ?>)</strong>
        </center>
        <hr>

        <?php if ($isAdmin): ?>
            <!-- ADMIN VIEW: Dashboard Statistics -->
            <h2>Admin Overview</h2>
            <div style="text-align: center;">
                <div class="stat-box">
                    <h3>Total Students</h3>
                    <p><?php echo $total_students; ?></p>
                </div>
                <div class="stat-box">
                    <h3>Total Events</h3>
                    <p><?php echo $total_events; ?></p>
                </div>
            </div>

            <h2>Latest Registrations</h2>
            <table class="data-table">
                <tr>
                    <th>Student Name</th>
                    <th>Event Name</th>
                    <th>Registration Date</th>
                </tr>
                <?php if ($recent_regs->num_rows > 0): ?>
                    <?php while ($row = $recent_regs->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['event_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['registration_date']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3" style="text-align: center;">No registrations found.</td></tr>
                <?php endif; ?>
            </table>
        <?php else: ?>
            <!-- STUDENT VIEW: Timetable -->
            <h2>Time Table for This Semester</h2><br>
            <table class="data-table">
                <tr>
                    <th>Time</th><th>Monday</th><th>Tuesday</th><th>Wednesday</th><th>Thursday</th><th>Friday</th>
                </tr>
                <tr><td>9:00 to 10:00 AM</td><td>Maths</td><td>Java</td><td>DSA</td><td>Network</td><td>Web Dev</td></tr>
                <tr><td>10:00 to 11:00 AM</td><td>Java</td><td>DSA</td><td>Network</td><td>Web Dev</td><td>Maths</td></tr>
                <tr><td>11:00 to 12:00 PM</td><td colspan="5" style="text-align:center;"><b>Lunch</b></td></tr>
                <tr><td>12:00 to 1:00 PM</td><td>DSA</td><td>Network</td><td>Web Dev</td><td>Maths</td><td>Java</td></tr>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>