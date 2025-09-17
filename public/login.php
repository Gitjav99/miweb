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

if (!$user) {
    die('
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Error</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f4f4f4;
            margin-top: 100px;
        }
        h1 {
            font-size: 2.5em;
            color: #c0392b;
        }
        .buttons {
            margin-top: 30px;
        }
        button {
            padding: 10px 20px;
            margin: 10px;
            font-size: 1em;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
        }
        .back {
            background-color: #3498db;
            color: white;
        }
        .back:hover {
            background-color: #2980b9;
        }
        .register {
            background-color: #2ecc71;
            color: white;
        }
        .register:hover {
            background-color: #27ae60;
        }
    </style>
</head>
<body>
    <h1>El usuario no existe</h1>
    <div class="buttons">
        <button class="back" onclick="history.back()">Volver Atrás</button>
        <button class="register" onclick="window.location.href=\'../register.html\'">Registrarse</button>
    </div>
</body>
</html>
');
}

// 2️⃣ Verificar la contraseña
if (!password_verify($password, $user['password_hash'])) {
    die('
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Error</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f4f4f4;
            margin-top: 100px;
        }
        h1 {
            font-size: 2.5em;
            color: #c0392b;
        }
        .buttons {
            margin-top: 30px;
        }
        button {
            padding: 10px 20px;
            margin: 10px;
            font-size: 1em;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
        }
        .back {
            background-color: #3498db;
            color: white;
        }
        .back:hover {
            background-color: #2980b9;
        }
        .register {
            background-color: #2ecc71;
            color: white;
        }
        .register:hover {
            background-color: #27ae60;
        }
    </style>
</head>
<body>
    <h1>El usuario no existe</h1>
    <div class="buttons">
        <button class="back" onclick="history.back()">Volver Atrás</button>
    </div>
</body>
</html>
');
}

// 3️⃣ Login correcto – puedes iniciar sesión (ej. $_SESSION)
session_start();
$_SESSION['nombre_usuario']= $user["nombre"];

header('Location: dashboard.php'); // Página protegida
exit;
?>
