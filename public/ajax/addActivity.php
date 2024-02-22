<?php
session_start();
require_once '../conn/index.php';

$username = $_POST['username'];
$sql = "SELECT password, dominio, id FROM utenti WHERE username = :username";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':username', $username, PDO::PARAM_STR);
$stmt->execute();
$verify_user = $stmt->fetch();

$pass_verify = $_SESSION['pass'];

if ($verify_user['password'] === hash('sha256', $pass_verify)) {
    $id = $verify_user['id'];
    $response = $_POST['response'];

    // Esempio di estrazione della keyword dalla response
    $words = explode(' ', $response);
    $keyword = $words[0]; // Prendi la prima parola come keyword

    function sanitizeInputForSQL($input) {
        // Rimuovi gli accenti
        $input = iconv('UTF-8', 'ASCII//TRANSLIT', $input);

        // Rimuovi virgolette singole e doppie
        $input = str_replace(array("'", '"'), ' ', $input);

        return $input;
    }

    // Modifica: Cambiato da UPDATE a INSERT
    $sql = "INSERT INTO bot_responses (user_id, keyword, response) VALUES (:username, :keyword, :response)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':keyword', sanitizeInputForSQL($keyword), PDO::PARAM_STR);
    $stmt->bindParam(':response', sanitizeInputForSQL($response), PDO::PARAM_STR);
    $stmt->execute();

    // Verifica se ci sono errori nell'esecuzione della query di inserimento
    if ($stmt->errorCode() == 0) {
        // Nessun errore, restituisci una risposta JSON con successo
        echo json_encode(['success' => true, 'message' => 'Inserito con successo']);
    } else {
        // Ci sono stati errori, restituisci una risposta JSON con l'errore
        echo json_encode(['success' => false, 'error' => $stmt->errorInfo()]);
    }
}
?>
