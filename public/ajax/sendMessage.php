<?php
// sendMessage.php


// Include il file di connessione al database
require_once '../conn/index.php'; 

// Include la classe Chat
require_once '../models/Chat.php';

// Ottieni i dati inviati tramite POST
$message = $_POST['message_chat'];
$username = $_POST['username_chat']; // Assumi che l'username sia memorizzato nella sessione
$user_chats = base64_encode($_POST['user_chats']); // il cookie cliente front

// Verifica se è stata inviata la chiave keyy
$keyy = isset($_POST['keyy']) ? $_POST['keyy'] : "1";

// Ottieni il dominio dell'utente attuale
$sql = "SELECT dominio FROM utenti WHERE username = :username ";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':username', $username, PDO::PARAM_STR);
$stmt->execute();
$dominio = $stmt->fetch();

// Ottieni gli operatori online ordinati per il numero minore di chat assegnate nell'arco di 10 minuti
$sql = "SELECT username FROM utenti WHERE dominio = :dominio AND stato_chat = 1";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':dominio', $dominio['dominio'], PDO::PARAM_STR);
$stmt->execute();
$operators = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Trova l'operatore con meno chat assegnate nell'arco di 10 minuti
$minOperator = null;
$minChatCount = PHP_INT_MAX;

foreach ($operators as $operator) {
    $sql = "SELECT COUNT(*) as chatCount FROM chat WHERE username = :operator AND created_at >= NOW() - INTERVAL 10 MINUTE";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':operator', $operator['username'], PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch();

    $chatCount = $result['chatCount'];

    if ($chatCount < $minChatCount) {
        $minChatCount = $chatCount;
        $minOperator = $operator['username'];
    }
}

// Invia il messaggio
$chat = new Chat($conn);
$chat->sendMessage($minOperator, $message, $keyy, $user_chats);

// Restituisci una risposta JSON (puoi personalizzarla)
$response = ['status' => 'success', 'message' => 'Messaggio inviato con successo'];
echo json_encode($response);
?>
