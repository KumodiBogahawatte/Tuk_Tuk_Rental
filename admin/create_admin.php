<?php
require_once '../config/db_connect.php';

// Check if admin user already exists
$stmt = $pdo->query("SELECT COUNT(*) FROM admin_users");
$count = $stmt->fetchColumn();

if ($count > 0) {
    die("Admin user already exists!");
}

// Create admin user
$username = "admin";
$password = password_hash("admin123", PASSWORD_DEFAULT);
$email = "admin@tuktuk.com";

$stmt = $pdo->prepare("INSERT INTO admin_users (username, password, email) VALUES (?, ?, ?)");
$stmt->execute([$username, $password, $email]);

echo "Admin user created successfully!<br>";
echo "Username: admin<br>";
echo "Password: admin123<br>";
echo "<a href='index.php'>Go to login page</a>";
?> 