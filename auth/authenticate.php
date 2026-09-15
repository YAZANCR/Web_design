<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('login.php');
}

require_guest();

if (login_is_locked()) {

    flash(
        'error',
        'عدد المحاولات كبير. انتظر قليلًا ثم حاول مرة أخرى.'
    );

    redirect_to('login.php');
}

verify_csrf_or_abort();


$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';


// التحقق من البيانات
if ($email === '' || $password === '') {

    flash(
        'error',
        'يرجى إدخال البريد الإلكتروني وكلمة المرور.'
    );

    redirect_to('login.php');
}


// البحث عن المستخدم
$stmt = $pdo->prepare(
    "SELECT * FROM admins
     WHERE email = ?
     AND status = 1
     LIMIT 1"
);

$stmt->execute([$email]);

$admin = $stmt->fetch();


// التحقق من المستخدم وكلمة المرور
if (!$admin || !password_verify($password, $admin['password'])) {

    record_failed_login();

    flash(
        'error',
        'البريد الإلكتروني أو كلمة المرور غير صحيحة.'
    );

    redirect_to('login.php');
}


// تسجيل الدخول
login_user($admin);


// تحديث آخر تسجيل دخول
$update = $pdo->prepare(
    "UPDATE admins
     SET last_login_at = NOW()
     WHERE id = ?"
);

$update->execute([$admin['id']]);


flash(
    'success',
    'تم تسجيل الدخول بنجاح.'
);


// الانتقال للصفحة الرئيسية
redirect_to('index.php');