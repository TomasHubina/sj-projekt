<?php
require_once __DIR__ . '/../Database.php';

class Pouzivatel {
    private $id;
    private $meno;
    private $priezvisko;
    private $email;
    private $heslo; 
    private $je_admin;
    
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
        }
    }
    
    public function getId() { return $this->id; }
    public function getMeno() { return $this->meno; }
    public function getPriezvisko() { return $this->priezvisko; }
    public function getEmail() { return $this->email; }
    public function getPassword() { return $this->heslo; }
    public function isAdmin() { return $this->je_admin == 1; }
    
    public function setMeno($meno) { $this->meno = $meno; }
    public function setPriezvisko($priezvisko) { $this->priezvisko = $priezvisko; }
    public function setEmail($email) { $this->email = $email; }
    public function setPassword($heslo) { 
        $this->heslo = password_hash($heslo, PASSWORD_DEFAULT); 
    }
    
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
                "UPDATE pouzivatelia SET meno = ?, priezvisko = ?, email = ? WHERE id = ?",
                [$this->meno, $this->priezvisko, $this->email, $this->id]
            );
        } else {
            $this->db->query(
                "INSERT INTO pouzivatelia (meno, priezvisko, email, heslo, je_admin) VALUES (?, ?, ?, ?, ?)",
                [$this->meno, $this->priezvisko, $this->email, password_hash($this->heslo, PASSWORD_DEFAULT), $this->je_admin]
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