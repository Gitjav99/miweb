<?php
// db.php
$host   = '172.18.0.2:3306';
$dbname = 'NeoVibra';
$user   = 'neoroot';
$pass   = 'NeoOllama';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    die('Error de conexión a la BD: ' . $e->getMessage());
}
?>
