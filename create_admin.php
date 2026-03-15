<?php
require_once 'includes/config.php';

$name = 'Admin';
$email = 'admin@metro.com';
$mobile = '0000000000';
$password = password_hash('admin123', PASSWORD_DEFAULT);
$role = 'admin';

try {
    $stmt = $pdo->prepare("INSERT INTO users (name, email, mobile, password, role, is_verified) VALUES (?, ?, ?, ?, ?, 1)");
    $stmt->execute([$name, $email, $mobile, $password, $role]);
    echo "Admin user created successfully.\n";
    echo "Username/Email: admin@metro.com\n";
    echo "Password: admin123\n";
} catch (PDOException $e) {
    echo "Error creating admin user: " . $e->getMessage() . "\n";
}
?>
