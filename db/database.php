<?php
class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        try {
            require_once 'config.php';
            $this->conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD, PDO_OPTIONS);
        } catch (PDOException $e) {
            die("CHYBA: Nepodarilo sa pripojiť do databázy: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->conn;
    }

    public function beginTransaction() {
    return $this->conn->beginTransaction();
    }

    public function commit() {
    return $this->conn->commit();
    }

    public function rollback() {
    return $this->conn->rollback();
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            die("CHYBA: SQL príkaz zlyhal: " . $e->getMessage());
        }
    }
    
    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }

    public function createTables() {
        $this->query("CREATE TABLE IF NOT EXISTS pouzivatelia (
            id INT AUTO_INCREMENT PRIMARY KEY,
            meno VARCHAR(100) NOT NULL,
            priezvisko VARCHAR(100) NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            heslo VARCHAR(255) NOT NULL,
            datum_vytvorenia TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            ulica VARCHAR(50) NULL,
            cislo VARCHAR(10) NULL,
            mesto VARCHAR(100) NULL,
            psc VARCHAR(10) NULL,
            telefon VARCHAR(20) NULL,
            je_admin TINYINT(1) NOT NULL DEFAULT 0
        )"); 

        $this->query("CREATE TABLE IF NOT EXISTS produkty (
            produkt_id INT AUTO_INCREMENT PRIMARY KEY,
            nazov VARCHAR(255) NOT NULL,
            popis TEXT,
            cena DECIMAL(10,2) NOT NULL,
            dostupne_mnozstvo INT NOT NULL DEFAULT 0,
            obrazok VARCHAR(255)
        )");

        $this->query("CREATE TABLE IF NOT EXISTS objednavky (
            objednavka_id INT AUTO_INCREMENT PRIMARY KEY,
            pouzivatel_id INT NOT NULL,
            meno VARCHAR(100) NOT NULL,
            priezvisko VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            ulica VARCHAR(50) NOT NULL,
            cislo VARCHAR(10) NOT NULL,
            mesto VARCHAR(100) NOT NULL,
            psc VARCHAR(10) NOT NULL,
            telefon VARCHAR(20) NOT NULL,
            celkova_suma DECIMAL(10,2) NOT NULL,
            stav VARCHAR(50) NOT NULL DEFAULT 'Nová',
            sposob_platby VARCHAR(50) NOT NULL,
            sposob_dorucenia VARCHAR(50) NOT NULL,
            poznamka TEXT,
            FOREIGN KEY (pouzivatel_id) REFERENCES pouzivatelia(id)
        )");

        $this->query("CREATE TABLE IF NOT EXISTS objednavka_produkty (
            id INT AUTO_INCREMENT PRIMARY KEY,
            objednavka_id INT NOT NULL,
            produkt_id INT NOT NULL,
            mnozstvo INT NOT NULL,
            cena_za_kus DECIMAL(10,2) NOT NULL,
            celkova_suma DECIMAL(10,2) NOT NULL,
            FOREIGN KEY (objednavka_id) REFERENCES objednavky(objednavka_id),
            FOREIGN KEY (produkt_id) REFERENCES produkty(produkt_id)
        )");

        return true;
    }

        public function createAdminUser() {
        $admin_exists = $this->fetchOne("SELECT id FROM pouzivatelia WHERE meno = ? AND je_admin = 1", ['Admin']);
        
        if (!$admin_exists) {
            $admin_heslo = password_hash("admin123", PASSWORD_DEFAULT);
            $this->query("INSERT INTO pouzivatelia (meno, email, heslo, je_admin) 
                          VALUES (?, ?, ?, ?)", ['Admin', 'admin@a.sk', $admin_heslo, 1]);
            return true;
        }
        
        return false;
    }
}
?>