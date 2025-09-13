<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
ensure_logged_in();

$chat_id = (int)($_GET['id'] ?? 0);
if ($chat_id <= 0) {
    header('Location: chat_list.php');
    exit;
}

// Comprobamos que el usuario pertenece al chat
$stmt = $pdo->prepare('SELECT * FROM chat_participante WHERE chat_id = ? AND usuario_id = ?');
$stmt->execute([$chat_id, $_SESSION['user_id']]);
if (!$stmt->fetch()) {
    die('No tienes acceso a este chat.');
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Chat #<?= $chat_id ?> – Chat Demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid h-100 d-flex flex-column">

    <!-- Cabecera con título del chat -->
    <div class="bg-primary text-white p-3 d-flex justify-content-between align-items-center">
        <h4 id="chat-title"></h4>
        <button id="btn-close-chat" class="btn btn-outline-light">↵</button>
    </div>

    <!-- Historial -->
    <div id="messages" class="flex-fill overflow-auto p-3 bg-white border"></div>

    <!-- Input -->
    <form id="message-form" class="d-flex p-2 border-top">
        <input type="text" id="msg-input" class="form-control me-2" placeholder="Escribe algo...">
        <button type="submit" class="btn btn-primary">Enviar</button>
    </form>
</div>

<script src="../js/chat.js"></script>
<script>
    const chatId = <?= $chat_id ?>;
    initChat(chatId);   // Función de chat.js
</script>
</body>
</html>
