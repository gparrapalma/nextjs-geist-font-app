<?php
// config.php - Configuración general de la aplicación

// Configuración de la aplicación
define('APP_NAME', 'Sistema de Verificación de Patentes Vehiculares');
define('APP_VERSION', '1.0');
define('APP_AUTHOR', 'Sistema Web PHP');

// Configuración de archivos
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB en bytes
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/jpg', 'image/png', 'image/gif']);
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Configuración de paginación
define('EVENTS_PER_PAGE', 10);

// Configuración de validación
define('MIN_PLATE_LENGTH', 3);
define('MAX_PLATE_LENGTH', 20);
define('MAX_DESCRIPTION_LENGTH', 1000);

// Configuración de zona horaria
date_default_timezone_set('America/Santiago'); // Cambiar según tu zona horaria

// Función para formatear fechas
function formatDate($date, $format = 'd/m/Y') {
    return date($format, strtotime($date));
}

// Función para formatear fecha y hora
function formatDateTime($datetime, $format = 'd/m/Y H:i') {
    return date($format, strtotime($datetime));
}

// Función para validar formato de patente
function validatePlateFormat($plate) {
    $plate = trim($plate);
    return strlen($plate) >= MIN_PLATE_LENGTH && strlen($plate) <= MAX_PLATE_LENGTH && preg_match('/^[A-Z0-9]+$/', $plate);
}

// Función para limpiar nombre de archivo
function sanitizeFileName($filename) {
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
    return substr($filename, 0, 100); // Limitar longitud
}

// Función para generar nombre único de archivo
function generateUniqueFileName($originalName) {
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if ($extension === 'jpeg') $extension = 'jpg';
    
    return 'veh_' . date('Ymd_His') . '_' . uniqid() . '.' . $extension;
}

// Función para validar tipo de imagen
function isValidImageType($mimeType) {
    return in_array($mimeType, ALLOWED_IMAGE_TYPES);
}

// Función para crear directorio si no existe
function ensureDirectoryExists($dir) {
    if (!is_dir($dir)) {
        return mkdir($dir, 0755, true);
    }
    return true;
}

// Función para obtener tamaño de archivo legible
function getReadableFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= pow(1024, $pow);
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

// Función para log de errores personalizado
function logError($message, $context = []) {
    $logMessage = date('Y-m-d H:i:s') . ' - ' . $message;
    if (!empty($context)) {
        $logMessage .= ' - Context: ' . json_encode($context);
    }
    error_log($logMessage);
}

// Función para escapar HTML
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Función para redireccionar
function redirect($url) {
    header("Location: $url");
    exit;
}

// Función para mostrar mensaje flash (requiere sesión)
function setFlashMessage($type, $message) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash_message'] = ['type' => $type, 'message' => $message];
}

function getFlashMessage() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    
    return null;
}

// Configuración de manejo de errores
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    $errorTypes = [
        E_ERROR => 'ERROR',
        E_WARNING => 'WARNING',
        E_PARSE => 'PARSE',
        E_NOTICE => 'NOTICE'
    ];
    
    $errorType = $errorTypes[$errno] ?? 'UNKNOWN';
    $message = "[$errorType] $errstr in $errfile on line $errline";
    
    logError($message);
    
    // En producción, no mostrar errores al usuario
    if (ini_get('display_errors')) {
        echo "<div style='color: red; padding: 10px; border: 1px solid red; margin: 10px;'>$message</div>";
    }
    
    return true;
}

// Configurar manejador de errores personalizado
set_error_handler('customErrorHandler');

// Configuración de headers de seguridad
function setSecurityHeaders() {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

// Aplicar headers de seguridad
setSecurityHeaders();
?>
