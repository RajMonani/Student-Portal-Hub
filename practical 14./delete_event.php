<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}

$id = $_GET['id'] ?? 0;
// Fallback to student_id 1 (Raj Monani) if your login script doesn't store the ID in the session
$admin_id = $_SESSION['student_id'] ?? 1; 

if ($id) {
    $stmt = $conn->prepare("SELECT event_name, poster_image FROM events WHERE event_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();
    $stmt->close();

    if ($event) {
        if ($event['poster_image'] && file_exists('uploads/' . $event['poster_image'])) {
            unlink('uploads/' . $event['poster_image']);
        }

        $del_stmt = $conn->prepare("DELETE FROM events WHERE event_id = ?");
        $del_stmt->bind_param("i", $id);
        $del_stmt->execute();
        $del_stmt->close();
        
        // --- AUDIT LOG INSERTION ---
        $action = "DELETE EVENT";
        $desc = "Deleted event: " . $event['event_name'];
        $log_stmt = $conn->prepare("INSERT INTO audit_logs (admin_id, action_type, description) VALUES (?, ?, ?)");
        $log_stmt->bind_param("iss", $admin_id, $action, $desc);
        $log_stmt->execute();
        $log_stmt->close();
        // ---------------------------
        
        header("Location: manage_event.php?msg=Event and poster deleted");
    } else {
        header("Location: manage_event.php?msg=Event not found");
    }
} else {
    header("Location: manage_event.php");
}
exit();
?>