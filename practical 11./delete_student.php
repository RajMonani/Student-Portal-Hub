<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}

$id = $_GET['id'] ?? 0;

if ($id) {
    $stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: manage_students.php?msg=Student deleted successfully");
    } else {
        header("Location: manage_students.php?msg=Error deleting student");
    }
    $stmt->close();
} else {
    header("Location: manage_students.php");
}
exit();
?>