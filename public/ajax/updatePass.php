<?php
session_start();
require_once '../conn/index.php';


$passwords=$_POST['password'];
$username=$_POST['username'];
$stato=2;
$password = hash('sha256', $passwords);
if (isset($_POST['username'])) {


$sqlCheckAttempts = "UPDATE lostpass SET stato = :stato WHERE email = :email ";
$stmt = $conn->prepare($sqlCheckAttempts);
$stmt->bindParam(':email', $username, PDO::PARAM_STR);
$stmt->bindParam(':stato', $stato, PDO::PARAM_STR);
$stmt->execute();

       // Verifica se ci sono errori nell'esecuzione della query di aggiornamento
    if ($stmt->errorCode() == 0) {
        // Nessun errore, restituisci una risposta JSON con successo
        echo json_encode(['success' => true, 'message' => 'success token']);
    } else {
        // Ci sono stati errori, restituisci una risposta JSON con l'errore
        echo json_encode(['success' => false, 'error' => $stmt->errorInfo()]);
    }

$sql = "UPDATE utenti SET password = :password WHERE username = :username ";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':password', $password, PDO::PARAM_STR);
$stmt->bindParam(':username', $username, PDO::PARAM_STR);
$stmt->execute();


    // Verifica se ci sono errori nell'esecuzione della query di aggiornamento
    if ($stmt->errorCode() == 0) {
        // Nessun errore, restituisci una risposta JSON con successo
        echo json_encode(['success' => true, 'message' => 'success user']);
    } else {
        // Ci sono stati errori, restituisci una risposta JSON con l'errore
        echo json_encode(['success' => false, 'error' => $stmt->errorInfo()]);
    }


}