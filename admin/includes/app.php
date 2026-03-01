<?php

if (!defined('APP_BOOTSTRAPPED')) {
    define('APP_BOOTSTRAPPED', true);
}

function app_scheme(): string
{
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        return 'https';
    }

    return 'http';
}

function admin_base_url(): string
{
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    return app_scheme() . '://' . $host . '/SG/admin/';
}

function app_root_url(): string
{
    return substr(admin_base_url(), 0, -6);
}

function admin_url(string $path = ''): string
{
    return admin_base_url() . ltrim($path, '/');
}

function asset_url(string $path = ''): string
{
    return rtrim(app_root_url(), '/') . '/' . ltrim($path, '/');
}

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => app_scheme() === 'https',
    ]);

    session_start();
}

function redirect_to(string $path = ''): void
{
    $target = $path;

    if ($path === '') {
        $target = admin_base_url();
    } elseif (!preg_match('/^https?:\/\//i', $path)) {
        $target = admin_url($path);
    }

    header('Location: ' . $target);
    exit;
}

function require_auth(): void
{
    start_secure_session();

    if (empty($_SESSION['user_id']) || empty($_SESSION['apodo'])) {
        set_flash('danger', 'Debe iniciar sesion para continuar.');
        redirect_to('login.php');
    }
}

function sync_tournament_context_from_request(): void
{
    start_secure_session();

    if (!isset($_GET['torneoId'], $_GET['nombreTorneo'])) {
        return;
    }

    $torneoId = (int) $_GET['torneoId'];
    $nombreTorneo = trim((string) $_GET['nombreTorneo']);

    if ($torneoId <= 0 || $nombreTorneo === '') {
        return;
    }

    $_SESSION['torneoId'] = $torneoId;
    $_SESSION['nombreTorneo'] = $nombreTorneo;
}

function tournament_context(): array
{
    sync_tournament_context_from_request();

    return [
        'torneoId' => (int) ($_SESSION['torneoId'] ?? 0),
        'nombreTorneo' => (string) ($_SESSION['nombreTorneo'] ?? ''),
    ];
}

function require_tournament_context(string $message = 'Seleccione un torneo antes de continuar.'): array
{
    $context = tournament_context();

    if ($context['torneoId'] <= 0 || $context['nombreTorneo'] === '') {
        set_flash('warning', $message);
        redirect_to('secciones/torneos/');
    }

    return $context;
}

function login_user(array $usuario): void
{
    start_secure_session();
    session_regenerate_id(true);

    $_SESSION['user_id'] = (int) $usuario['ID'];
    $_SESSION['apodo'] = $usuario['apodo'];
    $_SESSION['nombre'] = $usuario['nombre'] ?? $usuario['apodo'];
    $_SESSION['logueado'] = true;
}

function current_user_id(): int
{
    start_secure_session();

    return (int) ($_SESSION['user_id'] ?? 0);
}

function logout_user(): void
{
    start_secure_session();
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    session_destroy();
}

function set_flash(string $type, string $message): void
{
    start_secure_session();
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function pull_flash(): ?array
{
    start_secure_session();

    if (empty($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function csrf_token(): string
{
    start_secure_session();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_input(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function require_csrf(): void
{
    start_secure_session();

    $token = $_POST['csrf_token'] ?? '';
    $expected = $_SESSION['csrf_token'] ?? '';

    if (!$token || !$expected || !hash_equals($expected, $token)) {
        http_response_code(419);
        exit('Solicitud invalida. Recargue la pagina e intente nuevamente.');
    }
}

function post(string $key, string $default = ''): string
{
    return isset($_POST[$key]) ? trim((string) $_POST[$key]) : $default;
}

function stored_password_is_hash(string $storedPassword): bool
{
    $info = password_get_info($storedPassword);

    return !empty($info['algo']);
}

function verify_stored_password(string $plainPassword, string $storedPassword): bool
{
    if ($storedPassword === '') {
        return false;
    }

    if (stored_password_is_hash($storedPassword)) {
        return password_verify($plainPassword, $storedPassword);
    }

    return hash_equals($storedPassword, $plainPassword);
}

function hash_user_password(string $plainPassword): string
{
    return password_hash($plainPassword, PASSWORD_DEFAULT);
}

function table_has_column(PDO $conexion, string $table, string $column): bool
{
    static $cache = [];

    $key = $table . '.' . $column;
    if (array_key_exists($key, $cache)) {
        return $cache[$key];
    }

    try {
        $consulta = $conexion->prepare("SHOW COLUMNS FROM `{$table}` LIKE :column");
        $consulta->bindValue(':column', $column);
        $consulta->execute();
        $cache[$key] = (bool) $consulta->fetch();
    } catch (Throwable $error) {
        $cache[$key] = false;
    }

    return $cache[$key];
}

function tournaments_support_state(PDO $conexion): bool
{
    return table_has_column($conexion, 'torneos', 'estado')
        && table_has_column($conexion, 'torneos', 'fecha_cierre_real');
}

function fetch_tournament_record(PDO $conexion, int $torneoId): ?array
{
    if ($torneoId <= 0) {
        return null;
    }

    $campos = [
        'ID',
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'tipoTorneo',
    ];

    $campos[] = table_has_column($conexion, 'torneos', 'estado')
        ? 'estado'
        : "'abierto' AS estado";

    $campos[] = table_has_column($conexion, 'torneos', 'fecha_cierre_real')
        ? 'fecha_cierre_real'
        : 'NULL AS fecha_cierre_real';

    $consulta = $conexion->prepare('SELECT ' . implode(', ', $campos) . ' FROM torneos WHERE ID = :id LIMIT 1');
    $consulta->bindValue(':id', $torneoId, PDO::PARAM_INT);
    $consulta->execute();
    $torneo = $consulta->fetch();

    return $torneo ?: null;
}

function tournament_state_label(?string $state): string
{
    return $state === 'cerrado' ? 'cerrado' : 'abierto';
}

function tournament_is_closed(PDO $conexion, int $torneoId): bool
{
    $torneo = fetch_tournament_record($conexion, $torneoId);

    return $torneo !== null && tournament_state_label($torneo['estado'] ?? null) === 'cerrado';
}

function ensure_open_tournament_or_redirect(
    PDO $conexion,
    int $torneoId,
    string $redirectPath,
    string $message = 'El torneo esta cerrado. Reabralo para volver a modificar su informacion.'
): void {
    if (!tournament_is_closed($conexion, $torneoId)) {
        return;
    }

    set_flash('warning', $message);
    redirect_to($redirectPath);
}

function get_int(string $key, int $default = 0): int
{
    $value = $_GET[$key] ?? $default;

    return (int) $value;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function render_flash(): void
{
    $flash = pull_flash();

    if (!$flash) {
        return;
    }

    echo '<div class="alert alert-' . e($flash['type']) . ' alert-dismissible fade show" role="alert">';
    echo e($flash['message']);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>';
    echo '</div>';
}

function render_delete_button(string $name, int $id, string $label = 'Eliminar', string $class = 'btn btn-danger btn-sm'): string
{
    return '<form method="post" class="d-inline">'
        . csrf_input()
        . '<input type="hidden" name="' . e($name) . '" value="' . $id . '">'
        . '<button type="submit" class="' . e($class) . '" data-confirm="Confirma esta accion?">' . e($label) . '</button>'
        . '</form>';
}
