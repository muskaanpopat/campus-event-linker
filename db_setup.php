
<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS kjconnect_db";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select the database
$conn->select_db("kjconnect_db");

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'committee_head', 'exam_cell') NOT NULL,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'users' created successfully<br>";
} else {
    echo "Error creating table 'users': " . $conn->error . "<br>";
}

// Create events table
$sql = "CREATE TABLE IF NOT EXISTS events (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    event_type ENUM('hackathon', 'workshop', 'internship') NOT NULL,
    committee_id INT(6) UNSIGNED,
    location VARCHAR(100) NOT NULL,
    event_date DATETIME NOT NULL,
    registration_link VARCHAR(255),
    image_path VARCHAR(255),
    created_by INT(6) UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'events' created successfully<br>";
} else {
    echo "Error creating table 'events': " . $conn->error . "<br>";
}

// Create committees table
$sql = "CREATE TABLE IF NOT EXISTS committees (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    logo_path VARCHAR(255),
    head_id INT(6) UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (head_id) REFERENCES users(id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'committees' created successfully<br>";
} else {
    echo "Error creating table 'committees': " . $conn->error . "<br>";
}

// Create exam_posts table
$sql = "CREATE TABLE IF NOT EXISTS exam_posts (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    post_type ENUM('timetable', 'result') NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    posted_by INT(6) UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (posted_by) REFERENCES users(id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'exam_posts' created successfully<br>";
} else {
    echo "Error creating table 'exam_posts': " . $conn->error . "<br>";
}

// Create event_attachments table
$sql = "CREATE TABLE IF NOT EXISTS event_attachments (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_id INT(6) UNSIGNED NOT NULL,
    file_name VARCHAR(100) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'event_attachments' created successfully<br>";
} else {
    echo "Error creating table 'event_attachments': " . $conn->error . "<br>";
}

// Insert sample users
$sql = "INSERT INTO users (name, email, password, role) VALUES
('Student User', 'student@somaiya.edu', 'password', 'student'),
('Committee Head', 'committee@somaiya.edu', 'password', 'committee_head'),
('Exam Cell Staff', 'examcell@somaiya.edu', 'password', 'exam_cell')";

if ($conn->query($sql) === TRUE) {
    echo "Sample users created successfully<br>";
} else {
    echo "Error creating sample users: " . $conn->error . "<br>";
}

// Insert sample committees
$sql = "INSERT INTO committees (name, description, head_id) VALUES
('Technical Committee', 'Organizing technical events, workshops, and coding competitions', 2),
('Cultural Committee', 'Managing cultural festivals, performances, and art exhibitions', 2),
('Sports Committee', 'Coordinating sports events, tournaments, and athletic activities', 2)";

if ($conn->query($sql) === TRUE) {
    echo "Sample committees created successfully<br>";
} else {
    echo "Error creating sample committees: " . $conn->error . "<br>";
}

// Insert sample events
$sql = "INSERT INTO events (title, description, event_type, committee_id, location, event_date, registration_link, created_by) VALUES
('CodeWars 2023', '24-hour coding competition with amazing prizes', 'hackathon', 1, 'Main Auditorium', '2023-10-15 09:00:00', 'https://forms.google.com/codewars', 2),
('AI/ML Workshop', 'Learn the basics of AI and Machine Learning', 'workshop', 1, 'Room 302', '2023-10-20 14:00:00', 'https://forms.google.com/aiml-workshop', 2),
('Summer Internship Program', '3-month paid internship for CS students', 'internship', 1, 'TechSolutions Inc.', '2023-11-30 23:59:59', 'https://forms.google.com/summer-internship', 2)";

if ($conn->query($sql) === TRUE) {
    echo "Sample events created successfully<br>";
} else {
    echo "Error creating sample events: " . $conn->error . "<br>";
}

// Insert sample exam posts
$sql = "INSERT INTO exam_posts (title, description, post_type, file_path, posted_by) VALUES
('Mid-Term Exam Schedule', 'The mid-term examination schedule for all departments has been released.', 'timetable', 'uploads/exams/midterm_timetable.pdf', 3),
('Semester Results', 'Results for the previous semester have been published.', 'result', 'uploads/exams/semester_results.pdf', 3)";

if ($conn->query($sql) === TRUE) {
    echo "Sample exam posts created successfully<br>";
} else {
    echo "Error creating sample exam posts: " . $conn->error . "<br>";
}

// Close connection
$conn->close();

echo "<p>Database setup completed successfully. <a href='index.html'>Go to Homepage</a></p>";
?>
