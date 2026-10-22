<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $course = $_POST['course'];
    $study_year = $_POST['study_year'];
    $gender = $_POST['gender'];
    $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO students (full_name, email, mobile, course, study_year, gender, password_hash, role) VALUES (?, ?, ?, ?, ?, ?, ?, 'student')");
    $stmt->bind_param("ssssiss", $full_name, $email, $mobile, $course, $study_year, gender, $password_hash);
    
    if ($stmt->execute()) {
        header("Location: manage_students.php?msg=Student added successfully");
        exit();
    } else {
        $error = "Error adding student: " . $conn->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="content-box">
        <center><h2>Add New Student</h2></center>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="POST">
            <label>Full Name:</label><br>
            <input type="text" name="full_name" required style="width: 100%; padding: 8px;"><br><br>
            <label>Email:</label><br>
            <input type="email" name="email" required style="width: 100%; padding: 8px;"><br><br>
            <label>Mobile:</label><br>
            <input type="text" name="mobile" required style="width: 100%; padding: 8px;"><br><br>
            <label>Course:</label><br>
            <select name="course" required style="width: 100%; padding: 8px;">
                <option value="BTech">BTech</option>
                <option value="MTech">MTech</option>
                <option value="BCA">BCA</option>
            </select><br><br>
            <label>Study Year:</label><br>
            <input type="number" name="study_year" min="1" max="4" required style="width: 100%; padding: 8px;"><br><br>
            <label>Gender:</label><br>
            <input type="radio" name="gender" value="Male" required> Male
            <input type="radio" name="gender" value="Female"> Female<br><br>
            <label>Temporary Password:</label><br>
            <input type="password" name="password" required style="width: 100%; padding: 8px;"><br><br>
            <button type="submit" style="padding: 10px 20px; background-color: #27ae60; color: white; border: none; border-radius: 5px;">Save Student</button>
            <a href="manage_students.php" style="margin-left: 10px;">Cancel</a>
        </form>
    </div>
</body>
</html>