<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if ($nombre === '' || $password === '' || $confirm === '') {
        $errors[] = 'Todos los campos son obligatorios.';
    } elseif ($password !== $confirm) {
        $errors[] = 'Las contraseñas no coinciden.';
    } else {
        // Hash de la contraseña
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Intentamos insertar
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO usuario (nombre, configuracion) VALUES (?, ?)'
            );
            // Guardamos la contraseña dentro de un objeto JSON
            $config = json_encode(['password' => $hash], JSON_THROW_ON_ERROR);
            $stmt->execute([$nombre, $config]);

            // Login inmediato
            $id = $pdo->lastInsertId();
            $user = ['id' => $id, 'nombre' => $nombre, 'configuracion' => json_decode($config)];
            login_user($user);
            header('Location: chat_list.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {   // Viola UNIQUE
                $errors[] = 'Ese nombre ya está en uso.';
            } else {
                $errors[] = 'Error interno, intenta más tarde.';
            }
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Registro – Chat Demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Registro</h2>
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
        <div class="mb-3">
            <label for="confirm" class="form-label">Confirmar contraseña</label>
            <input type="password" id="confirm" name="confirm" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Registrarme</button>
        <a href="login.php" class="btn btn-link">Ya tengo cuenta</a>
    </form>
</div>
</body>
</html>
