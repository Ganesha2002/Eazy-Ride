<?php
include 'db.php';

// Create an admin account: admin@eazyride.com / admin123
$pass = password_hash('admin123', PASSWORD_DEFAULT);
$sql = "INSERT INTO users (full_name, email, phone, password, role) 
        VALUES ('System Admin', 'admin@eazyride.com', '1234567890', '$pass', 'admin')";

if ($conn->query($sql) === TRUE) {
    echo "Admin created successfully! <br>Email: <strong>admin@eazyride.com</strong> <br>Password: <strong>admin123</strong>";
} else {
    echo "Error: " . $conn->error;
}
?>