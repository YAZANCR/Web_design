<?php

declare(strict_types=1);

const AUTH_IDLE_TIMEOUT = 20 * 60; // عشرون دقيقة من عدم النشاط.
const LOGIN_MAX_ATTEMPTS = 5;
const LOGIN_LOCK_SECONDS = 30;
function auth_user(): ?array
{
    $user = $_SESSION['auth_user'] ?? null;
    return is_array($user) ? $user : null;
}
function is_authenticated(): bool
{
    return auth_user() !== null;
}
function login_user(array $admin): void
{
    // يمنع Session Fixation بعد نجاح تسجيل الدخول.
    session_regenerate_id(true);

    $_SESSION['auth_user'] = [
        'id' => (int)$admin['id'],
        'name' => (string)$admin['name'],
        'email' => (string)$admin['email'],
        'role' => (string)$admin['role'],
    ];
    $_SESSION['last_activity_at'] = time();

    unset($_SESSION['login_attempts'], $_SESSION['login_lock_until']);
}

function logout_user(): void
{
    unset($_SESSION['auth_user'], $_SESSION['last_activity_at']);
    session_regenerate_id(true);
}

function enforce_idle_timeout(): void
{
    if (!is_authenticated()) {
        return;
    }

    $lastActivity = (int)($_SESSION['last_activity_at'] ?? 0);
    if ($lastActivity > 0 && time() - $lastActivity > AUTH_IDLE_TIMEOUT) {
        logout_user();
        flash('error', 'انتهت الجلسة بسبب عدم النشاط. سجّل الدخول من جديد.');
        redirect_to('auth/login.php');
    }

    $_SESSION['last_activity_at'] = time();
}

function require_auth(): void
{
    if (!is_authenticated()) {
        flash('error', 'يجب تسجيل الدخول للوصول إلى هذه الصفحة.');
        redirect_to('auth/login.php');
    }

    enforce_idle_timeout();
}

function require_guest(): void
{
    if (is_authenticated()) {
        redirect_to('index.php');
    }
}

function current_admin_id(): ?int
{
    return isset($_SESSION['auth_user']['id']) ? (int)$_SESSION['auth_user']['id'] : null;
}

function login_is_locked(): bool
{
    return (int)($_SESSION['login_lock_until'] ?? 0) > time();
}

function login_lock_remaining(): int
{
    return max(0, (int)($_SESSION['login_lock_until'] ?? 0) - time());
}

function record_failed_login(): void
{
    $attempts = (int)($_SESSION['login_attempts'] ?? 0) + 1;
    $_SESSION['login_attempts'] = $attempts;

    if ($attempts >= LOGIN_MAX_ATTEMPTS) {
        $_SESSION['login_lock_until'] = time() + LOGIN_LOCK_SECONDS;
        $_SESSION['login_attempts'] = 0;
    }
}

function clear_login_limits(): void
{
    unset($_SESSION['login_attempts'], $_SESSION['login_lock_until']);
}
