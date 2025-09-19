<?php
// dashboard.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

require '../includes/db.php';

// 1️⃣  Obtener los chats del usuario (o todos si lo prefieres)
$user_id = $_SESSION['user_id'];
$sql = "SELECT u.id AS usuario_id, 
    u.nombre AS nombre_usuario, 
    c.id AS chat_id, 
    c.titulo AS titulo_chat, 
    cp.rol AS rol_en_chat, 
    cp.invitado_en AS fecha_invitacion, 
    c.creado_en AS creado_en,
    m.contenido as contenido
    FROM usuario u INNER JOIN chat_participante cp ON u.id = cp.usuario_id INNER JOIN chat c ON c.id = cp.chat_id INNER JOIN mensaje m on c.id = m.chat_id
    WHERE u.id = :owner_id and m.usuario_id!= u.id
    ORDER BY `creado_en` DESC;";
$stmt = $pdo->prepare($sql);
$stmt->execute(['owner_id' => $user_id]);

$chats = $stmt->fetchAll();   // array de chats
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard – Seleccionar Chat</title>
    <style>
        body{font-family:Arial,Helvetica,sans-serif;background:#f4f4f4;padding:2rem;}
        .wrapper{max-width:700px;margin:auto;background:#fff;padding:2rem;border-radius:8px;box-shadow:0 2px 12px rgba(0,0,0,.1);}
        h1{margin-top:0;}
        .chat-list{margin-top:1.5rem;}
        .chat-item{padding:.8rem;border-bottom:1px solid #ddd;}
        .chat-item:last-child{border-bottom:none;}
        .chat-link{color:#007bff;text-decoration:none;}
        .chat-link:hover{text-decoration:underline;}
        .btn-new{display:inline-block;padding:.5rem 1rem;background:#28a745;color:#fff;border:none;border-radius:4px;text-decoration:none;}
        .btn-new:hover{background:#218838;}
        .logout{margin-top:2rem;font-size:.9rem;}
    </style>
</head>
<body>
<div class="wrapper">
    <h1>Hola, <?= htmlspecialchars($_SESSION['nombre_usuario']); ?>!</h1>
    <p>Aquí puedes elegir el chat que quieres ver.</p>

    <!-- Botón para crear nueva sala (opcional) -->
    <p><a href="create_chat.php" class="btn-new">Crear nueva sala</a></p>

    <div class="chat-list">
        <?php if (empty($chats)): ?>
            <p>No tienes salas creadas todavía.</p>
        <?php else: ?>
            <?php foreach ($chats as $chat): ?>
                <div class="chat-item">
                    <strong><?= htmlspecialchars($chat['titulo_chat']); ?></strong>
                    <br>
                    <small>Creado el <?= date('d/m/Y H:i', strtotime($chat['creado_en'])); ?></small>
                    <small><?= htmlspecialchars($chat['contenido']); ?></small>
                    <br>
                    <a href="chat.php?chat_id=<?= (int)$chat['chat_id']; ?>" class="chat-link">Ver chat →</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="logout"><a href="logout.php">Cerrar sesión</a></div>
</div>
</body>
</html>
