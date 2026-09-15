<?php

require_once 'config.php';

$name = "Shamel";
$email = "shamel@gmail.com";
$password = "123456";
$role = "admin";

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO admins (name, email, password, role)
        VALUES (:name, :email, :password, :role)";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':name' => $name,
    ':email' => $email,
    ':password' => $hashedPassword,
    ':role' => $role
]);

echo "تم إنشاء المستخدم بنجاح";