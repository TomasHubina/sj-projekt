<?php
require_once __DIR__ . '/../database.php';

class Produkt {
    private $produkt_id;
    private $nazov;
    private $popis;
    private $cena;
    private $dostupne_mnozstvo;
    private $obrazok;
    
    private $db;
    
    public function __construct($data = null) {
        $this->db = Database::getInstance();
        
        if ($data) {
            $this->produkt_id = $data['produkt_id'] ?? null;
            $this->nazov = $data['nazov'] ?? '';
            $this->popis = $data['popis'] ?? '';
            $this->cena = $data['cena'] ?? 0;
            $this->dostupne_mnozstvo = $data['dostupne_mnozstvo'] ?? 0;
            $this->obrazok = $data['obrazok'] ?? '';
        }
    }
    
    public function getId() { return $this->produkt_id; }
    public function getNazov() { return $this->nazov; }
    public function getPopis() { return $this->popis; }
    public function getCena() { return $this->cena; }
    public function getDostupneMnozstvo() { return $this->dostupne_mnozstvo; }
    public function getObrazok() { return $this->obrazok; }
    
    public function setNazov($nazov) { $this->nazov = $nazov; }
    public function setPopis($popis) { $this->popis = $popis; }
    public function setCena($cena) { $this->cena = $cena; }
    public function setDostupneMnozstvo($mnozstvo) { $this->dostupne_mnozstvo = $mnozstvo; }
    public function setObrazok($obrazok) { $this->obrazok = $obrazok; }
    
    public static function findById($id) {
        $db = Database::getInstance();
        $data = $db->fetchOne("SELECT * FROM produkty WHERE produkt_id = ?", [$id]);
        
        return $data ? new Produkt($data) : null;
    }
    
    public function save() {
        if ($this->produkt_id) {
            return $this->db->query(
                "UPDATE produkty SET nazov = ?, popis = ?, cena = ?, dostupne_mnozstvo = ?, obrazok = ? WHERE produkt_id = ?",
                [$this->nazov, $this->popis, $this->cena, $this->dostupne_mnozstvo, $this->obrazok, $this->produkt_id]
            );
        } else {
            $this->db->query(
                "INSERT INTO produkty (nazov, popis, cena, dostupne_mnozstvo, obrazok) VALUES (?, ?, ?, ?, ?)",
                [$this->nazov, $this->popis, $this->cena, $this->dostupne_mnozstvo, $this->obrazok]
            );
            $this->produkt_id = $this->db->lastInsertId();
            return $this->produkt_id;
        }
    }
    
    public static function getAll() {
        $db = Database::getInstance();
        $data = $db->fetchAll("SELECT * FROM produkty ORDER BY nazov");
        
        $produkty = [];
        foreach ($data as $produkt_data) {
            $produkty[] = new Produkt($produkt_data);
        }
        
        return $produkty;
    }

    public static function getRandomProducts($excludeId, $limit = 3) {
    $db = Database::getInstance();
    $data = $db->fetchAll(
        "SELECT * FROM produkty WHERE produkt_id != ? ORDER BY RAND() LIMIT ?", 
        [$excludeId, $limit]
    );
    
    $produkty = [];
    foreach ($data as $row) {
        $produkty[] = new Produkt($row);
    }
    
    return $produkty;
}
    
    public function znizitMnozstvo($mnozstvo) {
        if ($this->dostupne_mnozstvo >= $mnozstvo) {
            $this->dostupne_mnozstvo -= $mnozstvo;
            return $this->db->query(
                "UPDATE produkty SET dostupne_mnozstvo = ? WHERE produkt_id = ?",
                [$this->dostupne_mnozstvo, $this->produkt_id]
            );
        }
        return false;
    }
    
    public function delete() {
        if ($this->produkt_id) {
            return $this->db->query("DELETE FROM produkty WHERE produkt_id = ?", [$this->produkt_id]);
        }
        return false;
    }
}
?>