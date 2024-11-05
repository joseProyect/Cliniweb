<?php
class Database {
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $dbName = "clinicabd"; // Nombre de la base de datos que vas a usar
    private $charset = "utf8";
    private $pdo;
    private $error;

    public function __construct() {
        $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            echo "Error de conexión: " . $this->error;
        }
    }

    // Método para obtener la conexión
    public function getConnection() {
        return $this->pdo;
    }
}
?>
