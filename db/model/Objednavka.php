<?php
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/ObjednavkaPolozka.php';
require_once __DIR__ . '/Pouzivatel.php';

class Objednavka {
    private $objednavka_id;
    private $pouzivatel_id;
    private $meno;
    private $priezvisko;
    private $email;
    private $ulica;
    private $cislo;
    private $mesto;
    private $psc;
    private $telefon;
    private $celkova_suma;
    private $stav;
    private $sposob_platby;
    private $sposob_dorucenia;
    private $poznamka;
    private $datum_vytvorenia;
    
    private $db;
    private $polozky = [];
    
    public function __construct($data = null) {
        $this->db = Database::getInstance();
        
        if ($data) {
            $this->objednavka_id = $data['objednavka_id'] ?? null;
            $this->pouzivatel_id = $data['pouzivatel_id'] ?? null;
            $this->meno = $data['meno'] ?? '';
            $this->priezvisko = $data['priezvisko'] ?? '';
            $this->email = $data['email'] ?? '';
            $this->ulica = $data['ulica'] ?? '';
            $this->cislo = $data['cislo'] ?? '';
            $this->mesto = $data['mesto'] ?? '';
            $this->psc = $data['psc'] ?? '';
            $this->telefon = $data['telefon'] ?? '';
            $this->celkova_suma = $data['celkova_suma'] ?? 0;
            $this->stav = $data['stav'] ?? 'Nová';
            $this->sposob_platby = $data['sposob_platby'] ?? '';
            $this->sposob_dorucenia = $data['sposob_dorucenia'] ?? '';
            $this->poznamka = $data['poznamka'] ?? '';
            $this->datum_vytvorenia = $data['datum_vytvorenia'] ?? date('Y-m-d H:i:s');
        }
    }
    
    // Gettery
    public function getId() { return $this->objednavka_id; }
    public function getPouzivatelId() { return $this->pouzivatel_id; }
    public function getMeno() { return $this->meno; }
    public function getPriezvisko() { return $this->priezvisko; }
    public function getEmail() { return $this->email; }
    public function getUlica() { return $this->ulica; }
    public function getCislo() { return $this->cislo; }
    public function getMesto() { return $this->mesto; }
    public function getPSC() { return $this->psc; }
    public function getTelefon() { return $this->telefon; }
    public function getCelkovaSuma() { return $this->celkova_suma; }
    public function getStav() { return $this->stav; }
    public function getSposobPlatby() { return $this->sposob_platby; }
    public function getSposobDorucenia() { return $this->sposob_dorucenia; }
    public function getPoznamka() { return $this->poznamka; }
    public function getDatumVytvorenia() { return $this->datum_vytvorenia; }
    
    // Settery
    public function setPouzivatelId($id) { $this->pouzivatel_id = $id; }
    public function setMeno($meno) { $this->meno = $meno; }
    public function setPriezvisko($priezvisko) { $this->priezvisko = $priezvisko; }
    public function setEmail($email) { $this->email = $email; }
    public function setUlica($ulica) { $this->ulica = $ulica; }
    public function setCislo($cislo) { $this->cislo = $cislo; }
    public function setMesto($mesto) { $this->mesto = $mesto; }
    public function setPSC($psc) { $this->psc = $psc; }
    public function setTelefon($telefon) { $this->telefon = $telefon; }
    public function setCelkovaSuma($suma) { $this->celkova_suma = $suma; }
    public function setStav($stav) { $this->stav = $stav; }
    public function setSposobPlatby($platba) { $this->sposob_platby = $platba; }
    public function setSposobDorucenia($dorucenie) { $this->sposob_dorucenia = $dorucenie; }
    public function setPoznamka($poznamka) { $this->poznamka = $poznamka; }
    
    public static function findById($id) {
        $db = Database::getInstance();
        $data = $db->fetchOne("SELECT * FROM objednavky WHERE objednavka_id = ?", [$id]);
        
        return $data ? new Objednavka($data) : null;
    }

    public static function findByStav($stav) {
        $db = Database::getInstance();
        $data = $db->fetchAll("SELECT * FROM objednavky WHERE stav = ?", [$stav]);
        
        $objednavky = [];
        foreach ($data as $row) {
            $objednavky[] = new Objednavka($row);
        }
        
        return $objednavky;
    }

    public static function countByStav() {
        $db = Database::getInstance();
        $data = $db->fetchAll("SELECT stav, COUNT(*) as pocet FROM objednavky GROUP BY stav");
        
        $stavy_pocty = [];
        foreach ($data as $row) {
            $stavy_pocty[$row['stav']] = $row['pocet'];
        }
        
        return $stavy_pocty;
    }
    
    public function save() {
        if ($this->objednavka_id) {
            return $this->db->query(
                "UPDATE objednavky SET 
                    pouzivatel_id = ?, meno = ?, priezvisko = ?, email = ?, 
                    ulica = ?, cislo = ?, mesto = ?, psc = ?, telefon = ?,
                    celkova_suma = ?, stav = ?, sposob_platby = ?, sposob_dorucenia = ?, poznamka = ?
                WHERE objednavka_id = ?",
                [
                    $this->pouzivatel_id, $this->meno, $this->priezvisko, $this->email,
                    $this->ulica, $this->cislo, $this->mesto, $this->psc, $this->telefon,
                    $this->celkova_suma, $this->stav, $this->sposob_platby, $this->sposob_dorucenia, $this->poznamka,
                    $this->objednavka_id
                ]
            );
        } else {
            $this->db->query(
                "INSERT INTO objednavky (
                    pouzivatel_id, meno, priezvisko, email, 
                    ulica, cislo, mesto, psc, telefon,
                    celkova_suma, stav, sposob_platby, sposob_dorucenia, poznamka, datum_vytvorenia
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())",
                [
                    $this->pouzivatel_id, $this->meno, $this->priezvisko, $this->email,
                    $this->ulica, $this->cislo, $this->mesto, $this->psc, $this->telefon,
                    $this->celkova_suma, $this->stav, $this->sposob_platby, $this->sposob_dorucenia, $this->poznamka
                ]
            );
            $this->objednavka_id = $this->db->lastInsertId();
            return $this->objednavka_id;
        }
    }
    
    public static function getAll() {
        $db = Database::getInstance();
        $data = $db->fetchAll("SELECT * FROM objednavky ORDER BY objednavka_id DESC");
        
        $objednavky = [];
        foreach ($data as $objednavka_data) {
            $objednavky[] = new Objednavka($objednavka_data);
        }
        
        return $objednavky;
    }
    
    public function getPolozky() {
        if (empty($this->polozky) && $this->objednavka_id) {
            $data = $this->db->fetchAll(
                "SELECT op.*, p.nazov, p.obrazok 
                FROM objednavka_produkty op
                JOIN produkty p ON op.produkt_id = p.produkt_id
                WHERE op.objednavka_id = ?",
                [$this->objednavka_id]
            );
            
            foreach ($data as $polozka_data) {
                $this->polozky[] = new ObjednavkaPolozka($polozka_data);
            }
        }
        
        return $this->polozky;
    }
    
    public function getPouzivatel() {
        if ($this->pouzivatel_id) {
            return Pouzivatel::findById($this->pouzivatel_id);
        }
        return null;
    }
}
?>