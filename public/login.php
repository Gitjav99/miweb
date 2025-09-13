<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($nombre === '' || $password === '') {
        $errors[] = 'Todos los campos son obligatorios.';
    } else {
        // Buscamos el usuario
        $stmt = $pdo->prepare('SELECT * FROM usuario WHERE nombre = ?');
        $stmt->execute([$nombre]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['configuracion']->password ?? '')) {
            // Autenticar
            login_user($user);
            header('Location: chat_list.php');
            exit;
        } else {
            $errors[] = 'Credenciales incorrectas.';
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Login – Chat Demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Iniciar Sesión</h2>
    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $e): echo "<p>$e</p>"; endforeach; ?>
        </div>
    <?php endif; ?>
    <form method="post" class="mt-3">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre de usuario</label>
            <input type="text" id="nombre" name="nombre" class="form-control" value="<?= htmlspecialchars($nombre ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" id="password" name="password" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Entrar</button>
        <a href="register.php" class="btn btn-link">¿No tienes cuenta? Regístrate</a>
    </form>
</div>
</body>
</html>
