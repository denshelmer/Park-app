<?php
/**
 * Clase Database (Patrón Singleton)
 * Gestiona la conexión PDO ODBC hacia Microsoft Access (ParkApp_DB.accdb)
 */
class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            if (!extension_loaded('pdo_odbc')) {
                throw new Exception("La extensión 'pdo_odbc' no está habilitada en el php.ini de XAMPP.");
            }

            if (!file_exists(DB_FILE)) {
                throw new Exception("No se encontró el archivo de base de datos Access en: " . DB_FILE);
            }

            try {
                self::$instance = new PDO(DB_DSN, '', '', [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                throw new Exception("Error de conexión a Microsoft Access: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
