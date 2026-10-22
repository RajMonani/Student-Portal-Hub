<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$student) {
    die("Student not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $course = $_POST['course'];
    
    $update_stmt = $conn->prepare("UPDATE students SET full_name = ?, email = ?, mobile = ?, course = ? WHERE student_id = ?");
    $update_stmt->bind_param("ssssi", $full_name, $email, $mobile, $course, $id);
    
    if ($update_stmt->execute()) {
        header("Location: manage_students.php?msg=Student updated successfully");
        exit();
    } else {
        $error = "Error updating record.";
    }
    $update_stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="content-box">
        <center><h2>Edit Student: <?php echo htmlspecialchars($student['full_name']); ?></h2></center>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="POST">
            <label>Full Name:</label><br>
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($student['full_name']); ?>" required style="width: 100%; padding: 8px;"><br><br>
            <label>Email:</label><br>
            <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required style="width: 100%; padding: 8px;"><br><br>
            <label>Mobile:</label><br>
            <input type="text" name="mobile" value="<?php echo htmlspecialchars($student['mobile']); ?>" required style="width: 100%; padding: 8px;"><br><br>
            <label>Course:</label><br>
            <select name="course" style="width: 100%; padding: 8px;">
                <option value="BTech" <?php if($student['course'] == 'BTech') echo 'selected'; ?>>BTech</option>
                <option value="MTech" <?php if($student['course'] == 'MTech') echo 'selected'; ?>>MTech</option>
                <option value="BCA" <?php if($student['course'] == 'BCA') echo 'selected'; ?>>BCA</option>
            </select><br><br>
            <button type="submit" style="padding: 10px 20px; background-color: #f39c12; color: white; border: none; border-radius: 5px;">Update Student</button>
            <a href="manage_students.php" style="margin-left: 10px;">Cancel</a>
        </form>
    </div>
</body>
</html>