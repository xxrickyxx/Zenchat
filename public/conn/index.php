<?php
$host = "localhost";
$database = "kloe2";
$dsn = "mysql:host=$host;dbname=$database";
$user = "root";
$pass = "leo@2018";

try {
    $conn = new PDO($dsn, $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    } catch (PDOException $e) {
    echo "Errore di connessione al database: " . $e->getMessage();
}