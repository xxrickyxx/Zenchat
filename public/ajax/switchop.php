<?php

var_dump($_POST);

require_once '../conn/index.php'; 

//$valore=$_POST['value'];
$user=$_POST['idoperatore'];

$sql = "SELECT stato_chat FROM utenti WHERE username = :user_chats ";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_chats', $user, PDO::PARAM_STR); // :username è il placeholder per il parametro
$stmt->execute();
$stato_chat = $stmt->fetch();


if ($stato_chat['stato_chat']=='1') {
// Query di aggiornamento
$sql = "UPDATE utenti SET stato_chat = '0' WHERE username = :user";
$stmt = $conn->prepare($sql);
//$stmt->bindParam(':valore', $valore, PDO::PARAM_INT);
$stmt->bindParam(':user', $user, PDO::PARAM_STR);

} else {

$sql = "UPDATE utenti SET stato_chat = '1' WHERE username = :user";
$stmt = $conn->prepare($sql);
//$stmt->bindParam(':valore', $valore, PDO::PARAM_INT);
$stmt->bindParam(':user', $user, PDO::PARAM_STR);

}

// Esegue l'aggiornamento
if ($stmt->execute()) {
    echo "Aggiornamento riuscito.";
} else {
    echo "Errore nell'aggiornamento.";
}
