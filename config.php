<?php
// config.php - Credenciales y Constantes del Sistema
date_default_timezone_set('America/Mexico_City');

// Credenciales de Base de Datos (Hardcoded para MVP según solicitud)
define('DB_HOST', 'localhost');
define('DB_NAME', 'DEMO');
define('DB_USER', 'DEMO');
define('DB_PASS', 'DEMO');

// Configuración de Correo (Ajustar con credenciales reales de SMTP si difieren del host)
define('SMTP_HOST', 'DEMO); // Ejemplo, ajustar según cPanel
define('SMTP_USER', 'DEMO'); // Asumido como sender
define('SMTP_PASS', 'DEMO'); // ¡PENDIENTE DEFINIR!
define('SMTP_PORT', 465);
define('SMTP_SECURE', 'ssl');

// Rutas
define('BASE_URL', 'AQUI VA TU DIRECTORIO GENERAL/');
define('UPLOAD_DIR', __DIR__ . '/uploads/');

// Conexión PDO Global
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error de Conexión BD: " . $e->getMessage());
}
?>