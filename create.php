<?php

require_once __DIR__ . '/config.php';

$name = 'مستخدم جديد';
$email = 'user@ministore.test';
$password = 'User@123';
$role = 'admin';

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO admins (name, email, password, role, status)
        VALUES (?, ?, ?, ?, 1)";

$stmt = $pdo->prepare($sql);

try {
    $stmt->execute([
        $name,
        $email,
        $hashedPassword,
        $role
    ]);

    echo "تم إنشاء المستخدم بنجاح<br>";
    echo "البريد الإلكتروني: " . htmlspecialchars($email) . "<br>";
    echo "كلمة المرور: " . htmlspecialchars($password);

} catch (PDOException $e) {
    echo "حدث خطأ: " . htmlspecialchars($e->getMessage());
}