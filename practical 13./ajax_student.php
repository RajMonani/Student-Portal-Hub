<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AJAX Student Management</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .admin-table th, .admin-table td { padding: 10px; border: 1px solid var(--border-color); text-align: left; }
        .admin-table th { background-color: var(--heading-sub); color: white; }
        .action-btn { padding: 5px 10px; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 14px; }
        .btn-edit { background-color: #f39c12; }
        .btn-delete { background-color: #c0392b; }
        .form-container { background-color: var(--nav-bg); padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid var(--border-color); }
        .form-container input, .form-container select { padding: 8px; margin-bottom: 10px; width: 100%; box-sizing: border-box; }
        .form-container button { padding: 10px 15px; background-color: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer; width: 100%; font-weight: bold; }
        #statusMessage { padding: 10px; border-radius: 5px; margin-bottom: 15px; display: none; font-weight: bold; text-align: center; }
        .success-msg { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error-msg { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="content-box" style="max-width: 1000px;">
        <center>
            <h1>AJAX Student Management</h1>
            <p>CRUD operations via Fetch API (No Page Reload)</p>
        </center>

        <div id="statusMessage"></div>

        <!-- Student Form -->
        <div class="form-container">
            <h3 id="formTitle">Add New Student</h3>
            <form id="studentForm">
                <input type="hidden" id="student_id">
                <input type="text" id="full_name" placeholder="Full Name" required>
                <input type="email" id="email" placeholder="Email Address" required>
                <input type="text" id="mobile" placeholder="Mobile Number" required>
                <input type="text" id="course" placeholder="Course (e.g., BTech, BCA)" required>
                <input type="number" id="study_year" placeholder="Study Year (e.g., 1, 2)" required>
                <select id="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
                <button type="submit" id="saveBtn">Save Student</button>
                <button type="button" id="cancelBtn" style="background-color: #95a5a6; margin-top: 10px; display: none;" onclick="resetForm()">Cancel Edit</button>
            </form>
        </div>

        <!-- Students Data Table -->
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Course</th>
                    <th>Year</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="studentTableBody">
                <tr><td colspan="6" style="text-align: center;">Loading data...</td></tr>
            </tbody>
        </table>
    </div>

    <!-- JavaScript Fetch API Logic -->
    <script>
        const apiURL = 'api_students.php';

        // Load data when page loads
        document.addEventListener('DOMContentLoaded', fetchStudents);

        // Fetch all students (GET)
        function fetchStudents() {
            fetch(apiURL)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        renderTable(data.data);
                    } else {
                        showStatus(data.message, 'error');
                    }
                })
                .catch(error => showStatus('Error connecting to API', 'error'));
        }

        // Render table rows
        function renderTable(students) {
            const tbody = document.getElementById('studentTableBody');
            tbody.innerHTML = '';
            
            if (students.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">No students found.</td></tr>';
                return;
            }

            students.forEach(student => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${student.student_id}</td>
                    <td>${student.full_name}</td>
                    <td>${student.email}</td>
                    <td>${student.course}</td>
                    <td>${student.study_year}</td>
                    <td>
                        <button onclick='editStudent(${JSON.stringify(student)})' class="action-btn btn-edit">Edit</button>
                        <button onclick="deleteStudent(${student.student_id})" class="action-btn btn-delete">Delete</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        // Handle Form Submit (Create or Update)
        document.getElementById('studentForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent traditional form submission page reload

            const id = document.getElementById('student_id').value;
            const action = id ? 'update' : 'add';

            const payload = {
                action: action,
                student_id: id,
                full_name: document.getElementById('full_name').value,
                email: document.getElementById('email').value,
                mobile: document.getElementById('mobile').value,
                course: document.getElementById('course').value,
                study_year: document.getElementById('study_year').value,
                gender: document.getElementById('gender').value
            };

            fetch(apiURL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showStatus(data.message, 'success');
                    resetForm();
                    fetchStudents(); // Refresh table dynamically
                } else {
                    showStatus(data.message, 'error');
                }
            })
            .catch(error => showStatus('Error saving data', 'error'));
        });

        // Delete Student (POST)
        function deleteStudent(id) {
            if (!confirm("Are you sure you want to delete this student?")) return;

            fetch(apiURL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete', student_id: id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showStatus(data.message, 'success');
                    fetchStudents(); // Refresh table dynamically
                } else {
                    showStatus(data.message, 'error');
                }
            })
            .catch(error => showStatus('Error deleting data', 'error'));
        }

        // Populate form for editing
        function editStudent(student) {
            document.getElementById('formTitle').innerText = 'Edit Student';
            document.getElementById('student_id').value = student.student_id;
            document.getElementById('full_name').value = student.full_name;
            document.getElementById('email').value = student.email;
            document.getElementById('mobile').value = student.mobile;
            document.getElementById('course').value = student.course;
            document.getElementById('study_year').value = student.study_year;
            document.getElementById('gender').value = student.gender;
            
            document.getElementById('saveBtn').innerText = 'Update Student';
            document.getElementById('cancelBtn').style.display = 'block';
            window.scrollTo(0, 0); // Scroll to top
        }

        // Reset form to Add state
        function resetForm() {
            document.getElementById('studentForm').reset();
            document.getElementById('student_id').value = '';
            document.getElementById('formTitle').innerText = 'Add New Student';
            document.getElementById('saveBtn').innerText = 'Save Student';
            document.getElementById('cancelBtn').style.display = 'none';
        }

        // Display success or error banners
        function showStatus(message, type) {
            const statusDiv = document.getElementById('statusMessage');
            statusDiv.innerText = message;
            statusDiv.className = type === 'success' ? 'success-msg' : 'error-msg';
            statusDiv.style.display = 'block';
            
            // Hide after 3 seconds
            setTimeout(() => { statusDiv.style.display = 'none'; }, 3000);
        }
    </script>
</body>
</html>