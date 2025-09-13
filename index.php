<?php
/* ---------- 1. Parámetros de conexión ----------
   Ajusta estos valores con los de tu entorno.  */
$host   = 'localhost';
$dbname = 'NeoVibra';
$user   = 'tu_usuario';
$pass   = 'tu_contraseña';
$charset = 'utf8mb4';

/* ---------- 2. Conexión PDO ----------
   PDO nos permite usar sentencias preparadas de forma sencilla. */
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo "<h1>Error de conexión</h1>";
    exit;
}

/* ---------- 3. Consulta ----------
   Un JOIN simple para obtener el chat y el usuario.  */
$sql = "
    SELECT
        m.id,
        m.contenido,
        m.enviado_en,
        u.nombre AS usuario,
        c.titulo AS chat
    FROM mensaje m
    JOIN usuario u ON m.usuario_id = u.id
    JOIN chat c ON m.chat_id = c.id
    ORDER BY m.enviado_en ASC
";
$stm = $pdo->query($sql);
$mensajes = $stm->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Mensajes de NeoVibra</title>
    <style>
        body {font-family: Arial, sans-serif; margin: 20px; background:#f4f4f4;}
        h1   {text-align:center;}
        .chat {margin-top:30px; padding:15px; background:#fff; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,.1);}
        .mensaje {border-bottom:1px solid #ddd; padding:10px 0;}
        .mensaje:last-child {border-bottom:none;}
        .meta   {color:#555; font-size:0.9em;}
        .contenido {margin-top:5px;}
    </style>
</head>
<body>
<h1>Mensajes de NeoVibra</h1>

<?php if (empty($mensajes)): ?>
    <p>No hay mensajes disponibles.</p>
<?php else: ?>
    <?php
    /* Agrupar mensajes por chat para mostrar un encabezado de chat */
    $chatActual = null;
    foreach ($mensajes as $msg):
        if ($msg['chat'] !== $chatActual):
            if ($chatActual !== null): /* cerrar div del chat anterior */
                echo '</div>';
            endif;
            $chatActual = $msg['chat'];
            echo "<div class='chat'><h2>Chat: ".htmlspecialchars($chatActual)."</h2>";
        endif;
    ?>
        <div class="mensaje">
            <div class="meta">
                <strong><?=htmlspecialchars($msg['usuario'])?></strong>
                en <?=htmlspecialchars($msg['enviado_en'])?>
            </div>
            <div class="contenido">
                <?=nl2br(htmlspecialchars($msg['contenido']))?>
            </div>
        </div>
    <?php endforeach; ?>
    </div> <!-- cerrar último chat -->
<?php endif; ?>

</body>
</html>
