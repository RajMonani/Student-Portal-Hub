<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM events WHERE event_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$event) die("Event not found.");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_name = $_POST['event_name'];
    $event_type = $_POST['event_type'];
    $event_date = $_POST['event_date'];
    $venue = $_POST['venue'];
    $poster_image = $event['poster_image']; 

    if (isset($_FILES['poster']) && $_FILES['poster']['error'] == 0) {
        $allowed_exts = ['jpg', 'jpeg', 'png'];
        $file_name = $_FILES['poster']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        if (!in_array($file_ext, $allowed_exts)) {
            $error = "Upload Failed: Only JPG/PNG allowed.";
        } elseif ($_FILES['poster']['size'] > 2097152) {
            $error = "Upload Failed: Image exceeds 2MB.";
        } else {
            // Delete old poster if exists
            if ($poster_image && file_exists('uploads/' . $poster_image)) {
                unlink('uploads/' . $poster_image);
            }
            $poster_image = uniqid('event_') . '.' . $file_ext;
            move_uploaded_file($_FILES['poster']['tmp_name'], 'uploads/' . $poster_image);
        }
    }

    if (!isset($error)) {
        $update_stmt = $conn->prepare("UPDATE events SET event_name=?, event_type=?, event_date=?, venue=?, poster_image=? WHERE event_id=?");
        $update_stmt->bind_param("sssssi", $event_name, $event_type, $event_date, $venue, $poster_image, $id);
        
        if ($update_stmt->execute()) {
            header("Location: manage_event.php?msg=Event updated successfully");
            exit();
        } else {
            $error = "Database Error.";
        }
        $update_stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Event</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="content-box">
        <center><h2>Edit Event</h2></center>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="POST" enctype="multipart/form-data">
            <label>Event Name:</label><br>
            <input type="text" name="event_name" value="<?php echo htmlspecialchars($event['event_name']); ?>" required style="width: 100%; padding: 8px;"><br><br>
            
            <label>Event Type:</label><br>
            <select name="event_type" style="width: 100%; padding: 8px;">
                <option value="Technical" <?php if($event['event_type'] == 'Technical') echo 'selected'; ?>>Technical</option>
                <option value="Non-Technical" <?php if($event['event_type'] == 'Non-Technical') echo 'selected'; ?>>Non-Technical</option>
            </select><br><br>
            
            <label>Event Date:</label><br>
            <input type="date" name="event_date" value="<?php echo htmlspecialchars($event['event_date']); ?>" required style="width: 100%; padding: 8px;"><br><br>
            
            <label>Venue:</label><br>
            <input type="text" name="venue" value="<?php echo htmlspecialchars($event['venue']); ?>" required style="width: 100%; padding: 8px;"><br><br>
            
            <label>Replace Poster (Optional):</label><br>
            <input type="file" name="poster" accept=".jpg, .jpeg, .png" style="width: 100%; padding: 8px;"><br><br>
            
            <button type="submit" style="padding: 10px 20px; background-color: #f39c12; color: white; border: none;">Update Event</button>
            <a href="manage_event.php" style="margin-left: 10px;">Cancel</a>
        </form>
    </div>
</body>
</html>