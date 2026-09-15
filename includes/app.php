<?php
function base_url(string $path = ''): string
{
    // يفترض أن اسم المجلد داخل htdocs هو mini_store
    $base = '/mini_store/';
    return $base . ltrim($path, '/');
    //تقوم بإزالة أي علامة شرطة مائلة (/) قد تكون موجودة في بداية النص المُمرر ($path) ltrim
}
function redirect_to(string $path): never
{
    header('Location: ' . base_url($path));
    exit;
}
function render_errors(array $errors): void
{
    if (empty($errors)) {
        return;
    }
    echo '<div class="alert alert-error"><strong>تحقق من الأخطاء التالية:</strong><ul>';
    foreach ($errors as $error) {
        echo '<li>' . e($error) . '</li>';    }
    echo '</ul></div>';
}

/** Flash Message تُعرض مرة واحدة في الطلب التالي. */
function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['_flash'][$key] = $message;
        return null;
    }

    $value = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return is_string($value) ? $value : null;
}
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/auth.php';
