<?php
$host = "localhost";
$database = "";
$dsn = "mysql:host=$host;dbname=$database";
$user = "";
$pass = "";

try {
    $conn = new PDO($dsn, $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    } catch (PDOException $e) {
    echo "Errore di connessione al database: " . $e->getMessage();
}