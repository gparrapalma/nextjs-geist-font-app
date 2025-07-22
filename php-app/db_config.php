<?php
// db_config.php - Configuración de conexión a la base de datos
$host = 'localhost';
$db   = 'vehicle_db';
$user = 'root';  // Cambiar por tu usuario de MySQL
$pass = '';      // Cambiar por tu contraseña de MySQL
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // habilitar excepciones
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Registrar error (no mostrar información sensible en producción)
    exit('Error de conexión a la base de datos: ' . $e->getMessage());
}
?>
