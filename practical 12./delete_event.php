<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}

$id = $_GET['id'] ?? 0;

if ($id) {
    // First, fetch the poster filename
    $stmt = $conn->prepare("SELECT poster_image FROM events WHERE event_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();
    $stmt->close();

    if ($event) {
        // Delete the physical file from the folder
        if ($event['poster_image'] && file_exists('uploads/' . $event['poster_image'])) {
            unlink('uploads/' . $event['poster_image']);
        }

        // Delete the database record
        $del_stmt = $conn->prepare("DELETE FROM events WHERE event_id = ?");
        $del_stmt->bind_param("i", $id);
        $del_stmt->execute();
        $del_stmt->close();
        
        header("Location: manage_event.php?msg=Event and poster deleted");
    } else {
        header("Location: manage_event.php?msg=Event not found");
    }
} else {
    header("Location: manage_event.php");
}
exit();
?>