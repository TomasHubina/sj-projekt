<?php
require_once __DIR__ . '/../database.php';

class Pouzivatel {
    private $id;
    private $meno;
    private $priezvisko;
    private $email;
    private $heslo; 
    private $ulica;
    private $cislo;
    private $mesto;
    private $psc;
    private $telefon;
    private $je_admin = 0;
    private $datum_vytvorenia;
    
    private $db;
    
    public function __construct($data = null) {
        $this->db = Database::getInstance();
        
        if ($data) {
            $this->id = $data['id'] ?? null;
            $this->meno = $data['meno'] ?? '';
            $this->priezvisko = $data['priezvisko'] ?? '';
            $this->email = $data['email'] ?? '';
            $this->heslo = $data['heslo'] ?? '';
            $this->je_admin = $data['je_admin'] ?? 0;
            $this->datum_vytvorenia = $data['datum_vytvorenia'] ?? null;
            $this->ulica = $data['ulica'] ?? null;
            $this->cislo = $data['cislo'] ?? null;
            $this->mesto = $data['mesto'] ?? null;
            $this->psc = $data['psc'] ?? null;
            $this->telefon = $data['telefon'] ?? null;
        }
    }
    
    public function getId() { return $this->id; }
    public function getMeno() { return $this->meno; }
    public function getPriezvisko() { return $this->priezvisko; }
    public function getEmail() { return $this->email; }
    public function getPassword() { return $this->heslo; }
    public function isAdmin() { return $this->je_admin == 1; }
    public function getDatum() { return $this->datum_vytvorenia; }
    public function getUlica() { return $this->ulica; }
    public function getCislo() { return $this->cislo; }
    public function getMesto() { return $this->mesto; }
    public function getPsc() { return $this->psc; }
    public function getTelefon() { return $this->telefon; }
    
    //Settery

    public function setMeno($meno) { $this->meno = $meno; }
    public function setPriezvisko($priezvisko) { $this->priezvisko = $priezvisko; }
    public function setEmail($email) { $this->email = $email; }
    public function setPassword($heslo) { 
        $this->heslo = password_hash($heslo, PASSWORD_DEFAULT); 
    }
    public function setUlica($ulica) { $this->ulica = $ulica; }
    public function setCislo($cislo) { $this->cislo = $cislo; }
    public function setMesto($mesto) { $this->mesto = $mesto; }
    public function setPsc($psc) { $this->psc = $psc; }
    public function setTelefon($telefon) { $this->telefon = $telefon; }
    
    public static function findById($id) {
        $db = Database::getInstance();
        $data = $db->fetchOne("SELECT * FROM pouzivatelia WHERE id = ?", [$id]);
        
        return $data ? new Pouzivatel($data) : null;
    }
    
    public static function findByEmail($email) {
        $db = Database::getInstance();
        $data = $db->fetchOne("SELECT * FROM pouzivatelia WHERE email = ?", [$email]);
        
        return $data ? new Pouzivatel($data) : null;
    }
    
    public static function verifyLogin($email, $heslo) {
        $user = self::findByEmail($email);
        
        if ($user && password_verify($heslo, $user->getPassword())) {
            return $user;
        }
        
        return null;
    }
    
    public function save() {
        if ($this->id) {
            return $this->db->query(
                "UPDATE pouzivatelia SET meno = ?, priezvisko = ?, email = ?, ulica = ?, cislo = ?, mesto = ?, psc = ?, telefon = ? WHERE id = ?",
                [$this->meno, $this->priezvisko, $this->email, $this->ulica, $this->cislo, $this->mesto, $this->psc, $this->telefon, $this->id]
            );
        } else {
            $this->db->query(
                "INSERT INTO pouzivatelia (meno, priezvisko, email, heslo, je_admin, ulica, cislo, mesto, psc, telefon) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$this->meno, $this->priezvisko, $this->email, $this->heslo, $this->je_admin, $this->ulica, $this->cislo, $this->mesto, $this->psc, $this->telefon]
            );
            $this->id = $this->db->lastInsertId();
            return $this->id;
        }
    }
    
    public static function getAll() {
        $db = Database::getInstance();
        $data = $db->fetchAll("SELECT * FROM pouzivatelia ORDER BY id");
        
        $users = [];
        foreach ($data as $user_data) {
            $users[] = new Pouzivatel($user_data);
        }
        
        return $users;
    }
}
?>