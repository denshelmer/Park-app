<?php
/**
 * Configuración de la Base de Datos y Parámetros del Sistema
 * ParkApp - El Alto
 */

// Configuración de zona horaria oficial (Bolivia UTC-4)
date_default_timezone_set('America/La_Paz');

// Rutas base
define('BASE_PATH', dirname(__DIR__, 2));
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', BASE_PATH . '/public');

// Ruta física absoluta a la base de datos Microsoft Access
define('DB_FILE', BASE_PATH . '/ParkApp_DB.accdb');

// Configuración de Driver ODBC para Access
define('DB_DRIVER', '{Microsoft Access Driver (*.mdb, *.accdb)}');
define('DB_DSN', 'odbc:Driver=' . DB_DRIVER . ';Dbq=' . DB_FILE . ';Uid=;Pwd=;');

// URL base del sistema en XAMPP (ajustable según subdirectorio)
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
$protocol = $isHttps ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$scriptDir = str_replace('\\', '/', dirname($scriptName));
if (php_sapi_name() === 'cli' || strpos($scriptDir, ':') !== false) {
    $scriptDir = '/park-app';
}
$baseUrl = rtrim($protocol . $host . $scriptDir, '/');
define('BASE_URL', $baseUrl);

