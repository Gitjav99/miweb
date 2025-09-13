<?php
session_start();

/**
 * Redirige a la página indicada si el usuario NO está autenticado.
 */
function ensure_logged_in(string $redirect = '../public/login.php'): void
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: $redirect");
        exit;
    }
}

/**
 * Salva la sesión de usuario (login).
 */
function login_user(array $user): void
{
    $_SESSION['user_id']  = $user['id'];
    $_SESSION['nombre']   = $user['nombre'];
    // Actualizamos la última conexión
    global $pdo;
    $stmt = $pdo->prepare("UPDATE usuario SET ultima_conexion = NOW() WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
}

/**
 * Cierra la sesión.
 */
function logout_user(): void
{
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}
