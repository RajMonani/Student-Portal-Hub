<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require 'db_connect.php';

$input = json_decode(file_get_contents('php://input'), true);
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $result = $conn->query("SELECT student_id, full_name, email, mobile, course, study_year, gender FROM students ORDER BY student_id DESC");
    
    if ($result) {
        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
        echo json_encode(['status' => 'success', 'data' => $students]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch students.']);
    }
} 
elseif ($method === 'POST') {
    $action = $input['action'] ?? '';

    if ($action === 'add') {
        $stmt = $conn->prepare("INSERT INTO students (full_name, email, mobile, course, study_year, gender, password_hash) VALUES (?, ?, ?, ?, ?, ?, ?)");
        // Dummy hash for testing since this is a basic CRUD demo
        $dummy_hash = password_hash('default123', PASSWORD_DEFAULT); 
        $stmt->bind_param("ssssiss", $input['full_name'], $input['email'], $input['mobile'], $input['course'], $input['study_year'], $input['gender'], $dummy_hash);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Student added successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $conn->error]);
        }
        $stmt->close();
    } 
    elseif ($action === 'update') {
        $stmt = $conn->prepare("UPDATE students SET full_name=?, email=?, mobile=?, course=?, study_year=?, gender=? WHERE student_id=?");
        $stmt->bind_param("ssssisi", $input['full_name'], $input['email'], $input['mobile'], $input['course'], $input['study_year'], $input['gender'], $input['student_id']);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Student updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $conn->error]);
        }
        $stmt->close();
    } 
    elseif ($action === 'delete') {
        $stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
        $stmt->bind_param("i", $input['student_id']);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Student deleted successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $conn->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action provided.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

$conn->close();
?>