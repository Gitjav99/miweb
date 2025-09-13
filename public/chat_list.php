<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
ensure_logged_in();

$pdo = $pdo;   // para evitar “undefined variable”

// Obtenemos los chats de este usuario
$stmt = $pdo->prepare(
    'SELECT c.*, COUNT(mp.usuario_id) AS total_participantes
     FROM chat c
     JOIN chat_participante mp ON c.id = mp.chat_id
     WHERE mp.usuario_id = ?
     GROUP BY c.id
     ORDER BY c.actualizado_en DESC'
);
$stmt->execute([$_SESSION['user_id']]);

$chats = $stmt->fetchAll();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Mis chats – Chat Demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>
<div class="d-flex h-100">
    <!-- Sidebar con la lista de chats -->
    <nav class="sidebar bg-dark text-white p-3">
        <h4>Chats</h4>
        <ul class="list-unstyled">
            <?php foreach ($chats as $chat): ?>
                <li>
                    <a href="chat_room.php?id=<?= $chat['id'] ?>"
                       class="text-white text-decoration-none">
                        <?= $chat['titulo'] ?? "Chat #{$chat['id']}" ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <hr class="text-white">
        <a href="#" id="btn-new-chat" class="btn btn-primary w-100 mb-2">+ Nuevo chat</a>
        <a href="logout.php" class="btn btn-danger w-100">Cerrar sesión</a>
    </nav>

    <!-- Área del chat -->
    <div id="chat-container" class="flex-fill p-4">
        <h3>Elige un chat en la barra izquierda.</h3>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/chat.js"></script>
</body>
</html>
