<?php
declare(strict_types=1);

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function login_page_url(): string
{
    $d = trim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
    $depth = $d === '' ? 0 : count(explode('/', $d));
    return str_repeat('../', $depth) . 'login.php';
}

function require_login(): void
{
    if (current_user() === null) {
        $next = $_SERVER['REQUEST_URI'] ?? '';
        header('Location: ' . login_page_url() . '?next=' . rawurlencode($next));
        exit;
    }
}

function require_admin(): void
{
    require_login();
    $u = current_user();
    if (empty($u['is_admin'])) {
        header('HTTP/1.1 403 Forbidden');
        echo 'Forbidden';
        exit;
    }
}

function login_user(array $row): void
{
    $_SESSION['user'] = [
        'id' => (int) $row['id'],
        'name' => $row['name'],
        'email' => $row['email'],
        'is_admin' => (int) $row['is_admin'],
    ];
}

function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}
