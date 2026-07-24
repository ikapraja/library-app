<?php
$servername = "localhost";
$username = "root";
$password = ""; // Default XAMPP password is empty

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS library_app_db";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully or already exists.<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select the database
$conn->select_db("library_app_db");

// Create table for posts (News & Highlights)
$sql = "CREATE TABLE IF NOT EXISTS posts (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'posts' created successfully or already exists.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Create table for users (Admin)
$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    role VARCHAR(20) DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql_users) === TRUE) {
    echo "Table 'users' created successfully or already exists.<br>";
    
    // Check if admin user exists, if not create one
    $check_admin = "SELECT id FROM users WHERE username = 'admin'";
    $result = $conn->query($check_admin);
    if ($result->num_rows == 0) {
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        $insert_admin = "INSERT INTO users (username, password, name, email) VALUES ('admin', '$hashed_password', 'Administrator', 'admin@pktj.ac.id')";
        if ($conn->query($insert_admin) === TRUE) {
            echo "Default admin user created. (Username: admin, Password: admin123)<br>";
        }
    }
} else {
    echo "Error creating table users: " . $conn->error . "<br>";
}

// Create uploads directory if it doesn't exist
$uploads_dir = __DIR__ . '/uploads';
if (!is_dir($uploads_dir)) {
    if (mkdir($uploads_dir, 0777, true)) {
        echo "Folder 'uploads' created successfully.<br>";
    } else {
        echo "Failed to create folder 'uploads'.<br>";
    }
} else {
    echo "Folder 'uploads' already exists.<br>";
}

$conn->close();
echo "<br><b>Setup complete!</b> You can now delete this file and start using the dashboard.";
?>
