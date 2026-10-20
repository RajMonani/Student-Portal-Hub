<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['username']); 
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $course = $_POST['course'];
    $study_year = $_POST['year'];          
    $gender = $_POST['gender'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        die("Error: Passwords do not match. <a href='registration form_2.html'>Go back</a>");
    }

    $check_stmt = $conn->prepare("SELECT student_id FROM students WHERE email = ? OR full_name = ?");
    $check_stmt->bind_param("ss", $email, $full_name);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        die("Error: An account with this email or name already exists. <a href='registration form_2.html'>Go back</a>");
    }
    $check_stmt->close();

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $insert_stmt = $conn->prepare("INSERT INTO students (full_name, email, mobile, course, study_year, gender, password_hash) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    $insert_stmt->bind_param("ssssiss", $full_name, $email, $mobile, $course, $study_year, $gender, $hashed_password);

    if ($insert_stmt->execute()) {
        echo "Registration successful! <a href='login.html'>Click here to login</a>";
    } else {
        echo "Error during registration: " . $conn->error;
    }

    $insert_stmt->close();
}

$conn->close();
?>