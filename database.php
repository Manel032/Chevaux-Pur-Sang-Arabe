<?php
class Database {
    private string $host = "localhost";
    private string $db_name = "chevaux_tunisiens";
    private string $username = "root";
    private string $password = "";
    private ?PDO $conn = null;

    // Singleton pour éviter plusieurs connexions
    private static ?Database $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function connect(): PDO {
        if ($this->conn === null) {
            try {
                $this->conn = new PDO(
                    "mysql:host={$this->host};dbname={$this->db_name};charset=utf8",
                    $this->username,
                    $this->password
                );
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch(PDOException $e) {
                echo json_encode([
                    "success" => false,
                    "error" => "Connection error: ".$e->getMessage()
                ]);
                exit;
            }
        }
        return $this->conn;
    }

    // Optionnel : fermer la connexion
    public function disconnect(): void {
        $this->conn = null;
    }
}
?>