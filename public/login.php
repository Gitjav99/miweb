<?php
// login.php
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido');
}

$usuario  = filter_input(INPUT_POST, 'usuario');
$password = $_POST['password'] ?? '';

if (!$usuario || !$password) {
    die('Usuario y contraseña requeridos.');
}

// 1️⃣ Obtener el hash de la BD
$stmt = $pdo->prepare("SELECT id, nombre, password_hash FROM usuario WHERE nombre = :nombre");
$stmt->execute(['nombre' => $usuario]);
$user = $stmt->fetch();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$user) {
    die("Credenciales inválidas. $result"); // Evita revelar si el usuario existe

}

// 2️⃣ Verificar la contraseña
if (!password_verify($password, $user['password_hash'])) {
    die("Contraseña inválida.  $result  $password");
}

// 3️⃣ Login correcto – puedes iniciar sesión (ej. $_SESSION)
session_start();
$_SESSION['user_id']  = $user['id'];
$_SESSION['usuario'] = $user['nombre'];

header('Location: dashboard.php'); // Página protegida
exit;
?>
