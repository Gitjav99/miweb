<?php
// register.php
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido');
}

// 1️⃣ Validar datos
$usuario    = filter_input(INPUT_POST, 'usuario');
$password = $_POST['password'] ?? '';
$pwd_conf = $_POST['password_confirm'] ?? '';

$errors = [];

if (!$usuario) {
    $errors[] = 'Correo electrónico inválido.';
}
if (strlen($password) < 6) {
    $errors[] = 'La contraseña debe tener al menos 6 caracteres.';
}
if ($password !== $pwd_conf) {
    $errors[] = 'Las contraseñas no coinciden.';
}

if (!empty($errors)) {
    // Mostrar errores en la misma página de registro (puedes redirigir a register.html con session flash)
    foreach ($errors as $err) {
        echo '<div style="color:#dc3545;margin-bottom:.8rem;">' . htmlspecialchars($err) . '</div>';
    }
    echo '<p><a href="register.html">Volver al registro</a></p>';
    exit;
}

// 2️⃣ Comprobar si ya existe el usuario
$stmt = $pdo->prepare("SELECT id FROM usuario WHERE nombre = :nombre");
$stmt->execute(['nombre' => $usuario]);
if ($stmt->fetch()) {
    echo '<div style="color:#dc3545;margin-bottom:.8rem;">El usuario ya está registrado.</div>';
    echo '<p><a href="register.html">Volver al registro</a></p>';
    exit;
}

// 3️⃣ Hash de la contraseña (bcrypt)
$hash = password_hash($password, PASSWORD_DEFAULT);   // Incluye salt aleatorio

// 4️⃣ Guardar en la BD
try {
    $stmt = $pdo->prepare("INSERT INTO usuario (nombre, password_hash) VALUES (:usuario, :pw)");
    $stmt->execute(['usuario' => $usuario, 'pw' => $hash]);

    // Registro exitoso: opcional iniciar sesión directamente
    session_start();
    $_SESSION['user_id']   = $pdo->lastInsertId();
    $_SESSION['user_usuario']= $usuario;

    // Redirigir a la página de bienvenida/protegida
    header('Location: dashboard.php');
    exit;
} catch (PDOException $e) {
    // Por seguridad no revelamos detalles internos
    echo '<div style="color:#dc3545;margin-bottom:.8rem;">Error interno. Por favor intenta de nuevo.</div>';
    echo '<p><a href="register.html">Volver al registro</a></p>';
    exit;
}
?>
