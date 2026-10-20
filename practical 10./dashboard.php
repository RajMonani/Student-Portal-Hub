<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'student') {
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="notification-banner" class="notification-banner">
        📢 Welcome to the Student Dashboard! View timetable, mark attendance, and track submissions.
        <span id="close-banner" class="close-banner">&times;</span>
    </div>
    <div class="header-bar">
        <div id="hamburger" class="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <button id="theme-toggle">🌙 Dark Mode</button>
    </div>
    <nav id="nav-menu" class="nav-menu">
        <a href="index.html">Home</a>
        <a href="logout.php">Logout</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="FAQ.html">FAQ</a>
        <a href="profile.html">Profile</a>
        <a href="register.html">Register</a>
        <a href="feedback.html">Feedback</a>
        <a href="contact.html">Contact</a>
        <a href="Event.html">Events</a>
    </nav>
    <div class="content-box">
        <center>
            <h1>Dashboard</h1>
            <strong>Welcome <?php echo htmlspecialchars($_SESSION['name']); ?>..</strong>
            <hr>
            
            <h2>Time Table for This Semester</h2><br>
            <table border="1" cellpadding="10" cellspacing="0">
                <tr>
                    <th>Time</th>
                    <th>Monday</th>
                    <th>Tuesday</th>
                    <th>Wednesday</th>
                    <th>Thursday</th>
                    <th>Friday</th>
                </tr>
                <tr>
                    <td>9:00 to 10:00 AM</td>
                    <td>Maths</td>
                    <td>Java</td>
                    <td>DSA</td>
                    <td>Network</td>
                    <td>Web Dev</td>
                </tr>
                <tr>
                    <td>10:00 to 11:00 AM</td>
                    <td>Java</td>
                    <td>DSA</td>
                    <td>Network</td>
                    <td>Web Dev</td>
                    <td>Maths</td>
                </tr>
                <tr>
                    <td>11:00 to 12:00 PM</td>
                    <td><b>Lunch</b></td>
                    <td><b>Lunch</b></td>
                    <td><b>Lunch</b></td>
                    <td><b>Lunch</b></td>
                    <td><b>Lunch</b></td>
                </tr>
                <tr>
                    <td>12:00 to 1:00 PM</td>
                    <td>DSA</td>
                    <td>Network</td>
                    <td>Web Dev</td>
                    <td>Maths</td>
                    <td>Java</td>
                </tr>
                <tr>
                    <td>1:00 to 2:00 PM</td>
                    <td>Network</td>
                    <td>Web Dev</td>
                    <td>Maths</td>
                    <td>Java</td>
                    <td>DSA</td>
                </tr>
                <tr>
                    <td>2:00 to 3:00 PM</td>
                    <td>Web Dev</td>
                    <td>Maths</td>
                    <td>Java</td>
                    <td>DSA</td>
                    <td>Network</td>
                </tr>
            </table>

            <hr>
            <h2>Attendance Checker</h2>
            <form id="attendance-form">
                <div>
                    <p><b>Name:</b> Raj Monani | <b>Date:</b> 17-07-2026 | <b>ID NO:</b> 25DCE098</p>
                    <label><input type="radio" name="att_25DCE098" value="present" required> Present</label>
                    <label><input type="radio" name="att_25DCE098" value="absent"> Absent</label>
                </div>
                <br>
                <button type="submit" id="save-attendance-btn">Save Attendance</button>
            </form>

            <hr>
            <h2>Assignment Submission Status</h2>
            <form id="assignment-form">
                <div>
                    <p><b>Name:</b> Raj Monani | <b>Date:</b> 17-07-2026 | <b>ID NO:</b> 25DCE098</p>
                    <label><input type="radio" name="ass_25DCE098" value="submitted" required> Submitted</label>
                    <label><input type="radio" name="ass_25DCE098" value="pending"> Pending</label>
                </div>
                <br>
                <button type="submit" id="save-assignment-btn">Save Assignment Status</button>
            </form>

            <br><br>
            <a href="Event.html">Event Page</a>
            <hr>
        </center>
    </div>

    <script src="script.js"></script>
</body>
</html>