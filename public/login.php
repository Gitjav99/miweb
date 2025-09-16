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
$filas = [];
foreach ($user as $fila) {
    $filas[] = $fila["id"] . " - " . $fila["nombre"]. " - " . $fila["password_hash"];
}

$string = implode(" | ", $filas);

if (!$user) {
    die("Credenciales inválidas. $string"); // Evita revelar si el usuario existe

}

// 2️⃣ Verificar la contraseña
if (!password_verify($password, $user['password_hash'])) {
    die("Contraseña inválida. $string -- $password");
}

// 3️⃣ Login correcto – puedes iniciar sesión (ej. $_SESSION)
session_start();
$_SESSION['user_id']  = $user['id'];
$_SESSION['usuario'] = $user['nombre'];

header('Location: dashboard.php'); // Página protegida
exit;
?>
