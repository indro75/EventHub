<?php
require_once 'config.php';

// Generate a new hash for 'admin123'
$newPassword = 'admin123';
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

// Update the admin password in the database
$stmt = $conn->prepare("UPDATE admins SET password = ? WHERE email = 'admin@ems.com'");
$stmt->bind_param("s", $hashedPassword);

if ($stmt->execute()) {
    echo "✅ <strong>Success!</strong> Admin password has been reset to: <b>admin123</b><br>";
    echo "👉 <a href='login.php'>Click here to Login</a>";
} else {
    echo "❌ Error: " . $conn->error;
}
?>