<?php
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/Produkt.php';

class ObjednavkaPolozka {
    private $id;
    private $objednavka_id;
    private $produkt_id;
    private $mnozstvo;
    private $cena_za_kus;
    private $celkova_suma;
    
    private $nazov;
    private $obrazok;
    
    private $db;
    
    public function __construct($data = null) {
        $this->db = Database::getInstance();
        
        if ($data) {
            $this->id = $data['id'] ?? null;
            $this->objednavka_id = $data['objednavka_id'] ?? null;
            $this->produkt_id = $data['produkt_id'] ?? null;
            $this->mnozstvo = $data['mnozstvo'] ?? 0;
            $this->cena_za_kus = $data['cena_za_kus'] ?? 0;
            $this->celkova_suma = $data['celkova_suma'] ?? 0;
            $this->nazov = $data['nazov'] ?? '';
            $this->obrazok = $data['obrazok'] ?? '';
        }
    }
    
    public function getId() { return $this->id; }
    public function getObjednavkaId() { return $this->objednavka_id; }
    public function getProduktId() { return $this->produkt_id; }
    public function getMnozstvo() { return $this->mnozstvo; }
    public function getCenaZaKus() { return $this->cena_za_kus; }
    public function getCelkovaSuma() { return $this->celkova_suma; }
    public function getNazov() { return $this->nazov; }
    public function getObrazok() { return $this->obrazok; }
    
    public function setObjednavkaId($id) { $this->objednavka_id = $id; }
    public function setProduktId($id) { $this->produkt_id = $id; }
    public function setMnozstvo($mnozstvo) { 
        $this->mnozstvo = $mnozstvo; 
        $this->updateCelkovaSuma();
    }
    public function setCenaZaKus($cena) { 
        $this->cena_za_kus = $cena; 
        $this->updateCelkovaSuma();
    }

    private function updateCelkovaSuma() {
        $this->celkova_suma = $this->mnozstvo * $this->cena_za_kus;
    }
    
    public static function findById($id) {
        $db = Database::getInstance();
        $data = $db->fetchOne(
            "SELECT op.*, p.nazov, p.obrazok 
            FROM objednavka_produkty op
            LEFT JOIN produkty p ON op.produkt_id = p.produkt_id
            WHERE op.id = ?", 
            [$id]
        );
        
        return $data ? new ObjednavkaPolozka($data) : null;
    }
    
    public static function findByObjednavkaId($objednavka_id) {
        $db = Database::getInstance();
        $data = $db->fetchAll(
            "SELECT op.*, p.nazov, p.obrazok 
            FROM objednavka_produkty op
            LEFT JOIN produkty p ON op.produkt_id = p.produkt_id
            WHERE op.objednavka_id = ?", 
            [$objednavka_id]
        );
        
        $polozky = [];
        foreach ($data as $polozka_data) {
            $polozky[] = new ObjednavkaPolozka($polozka_data);
        }
        
        return $polozky;
    }
    
    public function save() {
        $this->updateCelkovaSuma();
        
        if ($this->id) {
            return $this->db->query(
                "UPDATE objednavka_produkty 
                SET objednavka_id = ?, produkt_id = ?, mnozstvo = ?, cena_za_kus = ?, celkova_suma = ?
                WHERE id = ?",
                [
                    $this->objednavka_id, $this->produkt_id, $this->mnozstvo, 
                    $this->cena_za_kus, $this->celkova_suma, $this->id
                ]
            );
        } else {
            $this->db->query(
                "INSERT INTO objednavka_produkty 
                (objednavka_id, produkt_id, mnozstvo, cena_za_kus, celkova_suma) 
                VALUES (?, ?, ?, ?, ?)",
                [
                    $this->objednavka_id, $this->produkt_id, $this->mnozstvo, 
                    $this->cena_za_kus, $this->celkova_suma
                ]
            );
            $this->id = $this->db->lastInsertId();
            return $this->id;
        }
    }
    
    public function delete() {
        if ($this->id) {
            return $this->db->query("DELETE FROM objednavka_produkty WHERE id = ?", [$this->id]);
        }
        return false;
    }
    
    public function getProdukt() {
        if ($this->produkt_id) {
            return Produkt::findById($this->produkt_id);
        }
        return null;
    }
    
    public function aktualizovatMnozstvo($nove_mnozstvo) {
        $rozdiel = $nove_mnozstvo - $this->mnozstvo;
        $this->mnozstvo = $nove_mnozstvo;
        $this->updateCelkovaSuma();
        
        $this->db->query(
            "UPDATE objednavka_produkty SET mnozstvo = ?, celkova_suma = ? WHERE id = ?",
            [$this->mnozstvo, $this->celkova_suma, $this->id]
        );
        
        $this->db->query(
            "UPDATE objednavky 
            SET celkova_suma = (
                SELECT SUM(celkova_suma) FROM objednavka_produkty WHERE objednavka_id = ?
            )
            WHERE objednavka_id = ?",
            [$this->objednavka_id, $this->objednavka_id]
        );
        
        if ($rozdiel != 0) {
            $this->db->query(
                "UPDATE produkty SET dostupne_mnozstvo = dostupne_mnozstvo - ? WHERE produkt_id = ?",
                [$rozdiel, $this->produkt_id]
            );
        }
        
        return true;
    }
}
?>