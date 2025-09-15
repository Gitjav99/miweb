<?php
/* ---------- 1. Parámetros de conexión ----------
   Cambia estos valores con los datos de tu entorno. */
$host   = '192.168.1.134:8081';
$dbname = 'NeoVibra';
$user   = 'root';
$pass   = 'rootpassword';
$charset = 'utf8mb4';

/* ---------- 2. Conexión PDO ----------
   Con PDO podemos capturar el error con una excepción. */
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,      // ¡¡importante!!
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    /* ---------- 3. Error de conexión ----------
       Ahora mostramos el mensaje real de MySQL. */
    http_response_code(500);                       // Código HTTP 500 – Internal Server Error
    echo '<!DOCTYPE html><html><head><meta charset="utf‑8"><title>¡Error de conexión!</title></head>';
    echo '<body style="font-family:Arial,Helvetica,sans-serif;margin:2rem;">';
    echo '<h1>❌ Error al conectar con la base de datos</h1>';
    echo '<p><strong>Mensaje de MySQL:</strong> '.htmlspecialchars($e->getMessage()).'</p>';
    echo '<p>Si ves “host not found” o “No such file or directory” intenta revisar la configuración del servidor MySQL (puerto, host, permisos).<br>';
    echo 'Para solucionar el problema, puedes comprobar lo siguiente:</p>';
    echo '<ul><li>El host es correcto (ej.: localhost o 127.0.0.1).</li>';
    echo '<li>El usuario y contraseña existen y tienen permisos en la BD.</li>';
    echo '<li>El puerto está abierto (el predeterminado es 3306).</li>';
    echo '<li>El servicio MySQL está corriendo.</li></ul>';
    echo '</body></html>';
    exit;   // Termina el script – no seguimos intentando consultar.
}

/* ---------- 4. Consulta ----------
   Si llegamos aquí, la conexión fue exitosa y seguimos con el resto del script. */
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
    $chatActual = null;
    foreach ($mensajes as $msg):
        if ($msg['chat'] !== $chatActual):
            if ($chatActual !== null): echo '</div>'; endif;
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
    </div>
<?php endif; ?>

</body>
</html>
