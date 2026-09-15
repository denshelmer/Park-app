<?php
/**
 * Clase Base Model
 * Maneja la interacción con las tablas de Microsoft Access a través de PDO.
 */
class Model {
    protected PDO $db;
    protected string $table = '';

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Obtiene todos los registros de la tabla
     */
    public function getAll(): array {
        $sql = "SELECT * FROM [{$this->table}]";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Ejecuta una consulta preparada de lectura
     */
    protected function query(string $sql, array $params = []): array {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Ejecuta una consulta preparada que retorna un único registro
     */
    protected function queryOne(string $sql, array $params = []): ?array {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ? $result : null;
    }

    /**
     * Ejecuta una sentencia de inserción, actualización o eliminación
     */
    protected function execute(string $sql, array $params = []): bool {
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Obtiene el siguiente ID autoincremental para tablas de Microsoft Access
     */
    public function getNextId(string $primaryKey): int {
        $sql = "SELECT MAX([{$primaryKey}]) AS max_id FROM [{$this->table}]";
        $row = $this->queryOne($sql);
        return ($row && isset($row['max_id']) && $row['max_id'] !== null) ? ((int)$row['max_id'] + 1) : 1;
    }
}
