<?php
// database.php - PDO Database Connection and Initialization

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        // Configuration: Change to 'mysql' to use MySQL instead of SQLite
        $driver = 'sqlite'; 
        
        // MySQL configuration parameters (only used if driver is 'mysql')
        $mysql_host = 'localhost';
        $mysql_db   = 'chevaux_arabes';
        $mysql_user = 'root';
        $mysql_pass = '';

        try {
            if ($driver === 'sqlite') {
                $db_file = __DIR__ . '/chevaux.sqlite';
                $is_new = !file_exists($db_file);
                
                $this->pdo = new PDO('sqlite:' . $db_file);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                
                // Enable foreign keys in SQLite
                $this->pdo->exec('PRAGMA foreign_keys = ON;');

                // If SQLite file was just created, initialize tables
                if ($is_new) {
                    $sql_file = __DIR__ . '/chevaux_arabes.sql';
                    if (file_exists($sql_file)) {
                        $sql = file_get_contents($sql_file);
                        $this->pdo->exec($sql);
                    }
                }
            } else {
                // MySQL PDO Connection
                $dsn = "mysql:host=$mysql_host;dbname=$mysql_db;charset=utf8mb4";
                $this->pdo = new PDO($dsn, $mysql_user, $mysql_pass);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getConnection() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->pdo;
    }
}
