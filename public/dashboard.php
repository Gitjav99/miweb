<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Dashboard</title></head>
<body>
<h1>¡Bienvenido, <?= htmlspecialchars($_SESSION['user_usuario']); ?>!</h1>
<p>Esta es una página protegida.</p>
<a href="logout.php">Cerrar sesión</a>
</body>
</html>
