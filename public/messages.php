<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
ensure_logged_in();

$chat_id = (int)($_GET['chat_id'] ?? 0);
if ($chat_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'chat_id inválido']);
    exit;
}

// Obtenemos los últimos 50 mensajes
$stmt = $pdo->prepare(
    'SELECT m.*, u.nombre AS username
     FROM mensaje m
     JOIN usuario u ON m.usuario_id = u.id
     WHERE m.chat_id = ?
     ORDER BY m.id DESC
     LIMIT 50'
);
$stmt->execute([$chat_id]);

$mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['messages' => array_reverse($mensajes)]);   // Revertir para orden ascendente
