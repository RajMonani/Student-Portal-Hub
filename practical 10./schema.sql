DROP DATABASE IF EXISTS StudentHub;
CREATE DATABASE StudentHub;
USE StudentHub;

CREATE TABLE students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mobile VARCHAR(15) NOT NULL,
    course VARCHAR(50) NOT NULL,
    study_year INT NOT NULL,
    gender VARCHAR(10) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'student' NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(100) NOT NULL,
    event_type VARCHAR(50) NOT NULL,
    event_date DATE NOT NULL,
    venue VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE registrations (
    registration_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    event_id INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE
);

INSERT INTO students (full_name, email, mobile, course, study_year, gender, password_hash, role) VALUES
('Raj Monani', 'raj.monani@studenthub.com', '9876543210', 'BTech', 2, 'Male', '$2y$10$DummyHashForTestingPurposesOnly1234567890123456789012', 'admin'),
('Aarav Patel', 'aarav.p@studenthub.com', '9876543211', 'BCA', 1, 'Male', '$2y$10$DummyHashForTestingPurposesOnly1234567890123456789012', 'student'),
('Priya Sharma', 'priya.s@studenthub.com', '9876543212', 'MTech', 1, 'Female', '$2y$10$DummyHashForTestingPurposesOnly1234567890123456789012', 'student');

INSERT INTO events (event_name, event_type, event_date, venue) VALUES
('Smart India Hackathon', 'Technical', '2026-11-01', 'Main Lab'),
('Annual Sports Meet', 'Non-Technical', '2026-11-05', 'University Ground'),
('Code Debugging', 'Technical', '2026-11-18', 'Lab 1');

INSERT INTO registrations (student_id, event_id) VALUES
(1, 1), 
(1, 3), 
(2, 2), 
(3, 1);
