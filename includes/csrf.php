<?php

declare(strict_types=1);

/** إنشاء رمز CSRF واحد للجلسة وإعادة استخدامه في النماذج. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return (string)$_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/** مقارنة آمنة للرمز القادم من النموذج مع رمز Session. */
function verify_csrf_or_abort(): void
{
    $submitted = (string)($_POST['csrf_token'] ?? '');
    $stored = (string)($_SESSION['csrf_token'] ?? '');

    if ($submitted === '' || $stored === '' || !hash_equals($stored, $submitted)) {
        render_errors(['انتهت صلاحية النموذج أو أن الطلب غير موثوق. أعد تحميل الصفحة ثم حاول مرة أخرى.']);
    }
}
