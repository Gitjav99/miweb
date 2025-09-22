<?php
require '../includes/db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.html');
    exit;
}
$user_id = $_SESSION['user_id'];
$chatID= $_SESSION['chatID'];
$sql = "SELECT id, chat_id, usuario_id, contenido, enviado_en
 FROM mensaje WHERE chat_id = :chat_id and usuario_id != :owner_id ORDER BY enviado_en DESC;";
$stmt = $pdo->prepare($sql);
$stmt->execute(['chat_id' => $chatID, 'owner_id' => $user_id]);

$chats = $stmt->fetchAll();   // array de chats
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear chat</title>
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
    <h1>Pronto estará disponible la visualización del chat</h1>
    <small><?= htmlspecialchars($chat['contenido']); ?></small>
                    <br>
    <div class="logout"><a href="dashboard.php">Volver atras</a></div>
</div>
</body>
</html>