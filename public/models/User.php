<?php

// models/User.php

class User {
    private $conn;

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    public function verifyUser($username, $password) {
        $query = "SELECT password FROM utenti WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);  

        if (!$result) {
            return false; // Utente non trovato
        }
        
        $hashedPasswordFromDatabase = $result['password'];  

        if (hash('sha256', $password) === $hashedPasswordFromDatabase) {
            return true; // Accesso consentito
        }

        return false; // Password errata
    }

   public function getUserStatus($username) {
        $query = "SELECT stato FROM utenti WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return 'non_trovato'; // Utente non trovato
        }

        return $result['stato'];
    }
}


