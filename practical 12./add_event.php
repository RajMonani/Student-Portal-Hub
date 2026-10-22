<?php
session_start();
require 'db_connect.php';

// Restrict to admin users
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied: You must be an administrator.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_name = $_POST['event_name'];
    $event_type = $_POST['event_type'];
    $event_date = $_POST['event_date'];
    $venue = $_POST['venue'];
    $poster_image = null; 

    // File Upload Validation Logic
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] == 0) {
        $allowed_exts = ['jpg', 'jpeg', 'png'];
        $max_size = 2 * 1024 * 1024; // 2MB limit

        $file_name = $_FILES['poster']['name'];
        $file_size = $_FILES['poster']['size'];
        $file_tmp = $_FILES['poster']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // 1. Validate File Type
        if (!in_array($file_ext, $allowed_exts)) {
            $error = "Upload Failed: Only JPG and PNG files are allowed.";
        } 
        // 2. Validate File Size
        elseif ($file_size > $max_size) {
            $error = "Upload Failed: Image size must be less than 2MB.";
        } 
        // 3. Process Upload
        else {
            $poster_image = uniqid('event_') . '.' . $file_ext;
            $upload_path = 'uploads/' . $poster_image;
            
            if (!move_uploaded_file($file_tmp, $upload_path)) {
                $error = "Upload Failed: Could not save the image to the server.";
            }
        }
    }

    // Insert into database if there are no validation errors
    if (!isset($error)) {
        $stmt = $conn->prepare("INSERT INTO events (event_name, event_type, event_date, venue, poster_image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $event_name, $event_type, $event_date, $venue, $poster_image);
        
        if ($stmt->execute()) {
            header("Location: manage_event.php?msg=Event added successfully");
            exit();
        } else {
            $error = "Database Error: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Event</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="content-box">
        <center><h2>Create New Event</h2></center>
        
        <?php if (isset($error)) echo "<div class='notification-banner' style='background-color: #e74c3c;'>$error</div>"; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <label>Event Name:</label><br>
            <input type="text" name="event_name" required style="width: 100%; padding: 8px;"><br><br>
            
            <label>Event Type:</label><br>
            <select name="event_type" required style="width: 100%; padding: 8px;">
                <option value="Technical">Technical</option>
                <option value="Non-Technical">Non-Technical</option>
                <option value="Cultural">Cultural</option>
                <option value="Sports">Sports</option>
            </select><br><br>
            
            <label>Event Date:</label><br>
            <input type="date" name="event_date" required style="width: 100%; padding: 8px;"><br><br>
            
            <label>Venue:</label><br>
            <input type="text" name="venue" required style="width: 100%; padding: 8px;"><br><br>
            
            <label>Event Poster (Optional, JPG/PNG, Max 2MB):</label><br>
            <input type="file" name="poster" accept=".jpg, .jpeg, .png" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;"><br><br>
            
            <button type="submit" style="padding: 10px 20px; background-color: #27ae60; color: white; border: none; border-radius: 5px;">Save Event</button>
            <a href="manage_event.php" style="margin-left: 10px;">Cancel</a>
        </form>
    </div>
</body>
</html>