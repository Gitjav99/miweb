<?php
// test_db.php
// Muestra info de diagnóstico para conectar a MySQL desde PHP (mysqli y PDO).
// Coloca este archivo en ./www y ábrelo desde el navegador.

error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- Configuración por defecto (se pueden pasar parámetros GET para probar otras opciones) ---
// Ej: http://.../test_db.php?host=db&port=3306&user=admin&pass=admin123&db=mi_base
$host = isset($_GET['host']) ? $_GET['host'] : (getenv('DB_HOST') ?: (getenv('MYSQL_HOST') ?: 'db'));
$port = isset($_GET['port']) ? (int)$_GET['port'] : (getenv('DB_PORT') ?: 8081);
$db   = isset($_GET['db'])   ? $_GET['db'] : (getenv('MYSQL_DATABASE') ?: (getenv('DB_NAME') ?: 'NeoVibra'));
$user = isset($_GET['user']) ? $_GET['user'] : (getenv('MYSQL_USER') ?: (getenv('DB_USER') ?: 'root') );
$pass = isset($_GET['pass']) ? $_GET['pass'] : (getenv('MYSQL_PASSWORD') ?: getenv('MYSQL_ROOT_PASSWORD') ?: getenv('DB_PASS') ?: 'rootpassword');

function mask($s){
    if ($s === '' || $s === null) return '(empty)';
    return substr($s,0,1) . str_repeat('*', max(0, strlen($s)-2)) . substr($s,-1);
}

?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Diagnóstico conexión MySQL</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
body{font-family:system-ui, -apple-system, "Segoe UI", Roboto, Arial; padding:18px; background:#f7f7fb}
h1{margin-top:0}
pre{background:#111; color:#e6ffda; padding:12px; overflow:auto}
.box{background:#fff;border-radius:8px;padding:12px;margin:8px 0;box-shadow:0 1px 4px rgba(0,0,0,.08)}
.bad{color:#a33}
.good{color:#2a7}
.kv{font-weight:600}
.small{font-size:0.9em;color:#666}
</style>
</head>
<body>
<h1>Diagnóstico conexión MySQL / MariaDB (PHP)</h1>
<p class="small">Edita parámetros mediante la query string o modifica las variables en el script.</p>

<div class="box">
  <div><span class="kv">Host:</span> <?php echo htmlspecialchars($host) ?></div>
  <div><span class="kv">Puerto:</span> <?php echo htmlspecialchars($port) ?></div>
  <div><span class="kv">Base de datos:</span> <?php echo htmlspecialchars($db) ?></div>
  <div><span class="kv">Usuario:</span> <?php echo htmlspecialchars($user) ?></div>
  <div><span class="kv">Contraseña:</span> <?php echo htmlspecialchars(mask($pass)) ?></div>
</div>

<div class="box">
  <h2>1) Comprobaciones de PHP</h2>
  <div><strong>Extensión mysqli:</strong>
    <?php if (extension_loaded('mysqli')): ?><span class="good"> disponible</span>
    <?php else: ?><span class="bad"> NO disponible</span><?php endif; ?></div>

  <div><strong>Extensión pdo_mysql:</strong>
    <?php if (extension_loaded('pdo_mysql')): ?><span class="good"> disponible</span>
    <?php else: ?><span class="bad"> NO disponible</span><?php endif; ?></div>
</div>

<div class="box">
  <h2>2) Resolución DNS / IP</h2>
  <?php
    $resolved = gethostbyname($host);
    echo "<div><strong>gethostbyname('$host'):</strong> " . htmlspecialchars($resolved) . "</div>";
    if ($resolved === $host) {
        echo "<div class=\"small\">(Si devuelve el mismo nombre, puede que no haya resolución DNS dentro del contenedor)</div>";
    }
  ?>
</div>

<div class="box">
  <h2>3) Prueba de conexión TCP al puerto (fsockopen)</h2>
  <?php
  $timeout = 3;
  $fp = @fsockopen($host, $port, $errno, $errstr, $timeout);
  if ($fp) {
      echo "<div class=\"good\">Conexión TCP exitosa a $host:$port (timeout {$timeout}s)</div>";
      fclose($fp);
  } else {
      echo "<div class=\"bad\">No se pudo conectar TCP a $host:$port — Error: " . htmlspecialchars("[$errno] $errstr") . "</div>";
      // Si host es localhost, indicar socket
      if ($host === 'localhost' || $host === '127.0.0.1') {
          $sockets = ['/var/run/mysqld/mysqld.sock','/tmp/mysql.sock'];
          foreach ($sockets as $s) {
              echo "<div class=\"small\">Comprobando socket $s: " . (file_exists($s) ? "<span class='good'>existe</span>" : "<span class='bad'>no existe</span>") . "</div>";
          }
      }
  }
  ?>
</div>

<div class="box">
  <h2>4) Intento con <code>mysqli</code></h2>
  <div class="small">Se mostrará el error que devuelve <code>mysqli_connect</code>.</div>
  <?php
  $mysqli = @new mysqli($host, $user, $pass, $db, $port);
  if ($mysqli->connect_errno) {
      echo "<pre class='bad'>mysqli_connect_error(): (" . $mysqli->connect_errno . ") " . htmlspecialchars($mysqli->connect_error) . "</pre>";
  } else {
      echo "<div class='good'>Conexión mysqli OK ✅</div>";
      // Opcional: lista de bases de datos
      $res = $mysqli->query("SHOW DATABASES");
      echo "<div class='small'><strong>Bases visibles:</strong></div><pre>";
      while ($row = $res->fetch_assoc()) { echo htmlspecialchars($row['Database']) . "\n"; }
      echo "</pre>";
      $mysqli->close();
  }
  ?>
</div>

<div class="box">
  <h2>5) Intento con <code>PDO</code> (PDO_MYSQL)</h2>
  <div class="small">Se mostrará la excepción si falla.</div>
  <?php
  try {
      $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8";
      $opt = [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT=>3];
      $pdo = new PDO($dsn, $user, $pass, $opt);
      echo "<div class='good'>Conexión PDO OK ✅</div>";
  } catch (PDOException $e) {
      echo "<pre class='bad'>PDOException: " . htmlspecialchars($e->getMessage()) . "</pre>";
      // Si el mensaje es genérico (2002), damos pistas:
      if (strpos($e->getMessage(),'2002') !== false) {
          echo "<div class='small'>El error 2002 suele indicar: host incorrecto o servicio MySQL no accesible (socket vs TCP). Revisa que uses el nombre del servicio Docker (ej. 'db') si PHP corre en un contenedor.</div>";
      }
  }
  ?>
</div>

<div class="box">
  <h2>6) Sugerencias / Pasos siguientes</h2>
  <ul>
    <li>Si PHP corre <strong>dentro del contenedor</strong>, usa el <strong>nombre del servicio Docker</strong> (p. ej. <code>db</code>) como host y <strong>no</strong> uses <code>localhost</code>.</li>
    <li>Si conectas desde tu <strong>Windows</strong> al MySQL del contenedor, usa la IP de la VM (<code>192.168.1.134</code>) y el puerto publicado (<code>3306</code> si lo expusiste).</li>
    <li>Si ves que <code>pdo_mysql</code> no está disponible, instala la extensión (reconstruyendo la imagen o con <code>docker-php-ext-install pdo_mysql</code>).</li>
    <li>Pega aquí (o revisa) la salida completa de este script para más ayuda.</li>
  </ul>
</div>

</body>
</html>
