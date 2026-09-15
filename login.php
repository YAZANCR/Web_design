<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/app.php';

require_guest();

$errors = [];
$email = '';

if ($message = flash('error')) {
    $errors[] = $message;
}

if ($message = flash('success')) {
    $success = $message;
}

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>تسجيل الدخول - لوحة التحكم</title>

    <link rel="stylesheet" href="assets/style.css">
</head>

<body>

<section class="login">

    <div class="login-card">

        <div class="login-header">
            <h2>تسجيل الدخول</h2>
            <p>مرحباً بك، أدخل بياناتك للوصول للوحة التحكم</p>
        </div>

        <?php if (!empty($errors)): ?>

            <div class="alert">
                <ul>
                    <?php foreach ($errors as $error): ?>

                        <li>
                            <?= e($error) ?>
                        </li>

                    <?php endforeach; ?>
                </ul>
            </div>

        <?php endif; ?>


        <?php if (!empty($success)): ?>

            <div class="alert">
                <?= e($success) ?>
            </div>

        <?php endif; ?>


        <form method="POST" action="auth/authenticate.php">

            <?= csrf_field() ?>

            <div class="form-group">

                <label for="email">
                    البريد الإلكتروني
                </label>

                <input
                    id="email"
                    type="email"
                    name="email"
                    value="<?= e($email) ?>"
                    autocomplete="username"
                    placeholder="admin@ministore.test"
                    required
                >

            </div>


            <div class="form-group">

                <label for="password">
                    كلمة المرور
                </label>

                <input
                    id="password"
                    type="password"
                    name="password"
                    autocomplete="current-password"
                    placeholder="••••••••"
                    required
                >

            </div>


            <button class="btn" type="submit">
                دخول إلى لوحة التحكم
            </button>

        </form>

    </div>

</section>

</body>

</html>