<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied: You do not have permission to view this page. <a href='login.html'>Go back</a>");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="content-box">
        <center>
            <h1>Admin Control Panel</h1>
            <p>Welcome, <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong>. You have administrator privileges.</p>
            <hr>
            <a href="logout.php" style="padding: 10px 20px; background-color: #c0392b; color: white; text-decoration: none; border-radius: 5px;">Secure Logout</a>
        </center>
    </div>
</body>
</html>