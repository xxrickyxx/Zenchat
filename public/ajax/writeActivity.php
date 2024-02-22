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
    $response = $_POST['edit'];
    $keyword = $_POST['keyword'];

    function sanitizeInputForSQL($input) {
    // Rimuovi gli accenti
    $input = iconv('UTF-8', 'ASCII//TRANSLIT', $input);

    // Rimuovi virgolette singole e doppie
    $input = str_replace(array("'", '"'), ' ', $input);



    return $input;
}

$conta_response = strlen($response);



    $sql = "UPDATE bot_responses SET response = '".sanitizeInputForSQL($response)."' WHERE user_id = '".$username."' AND keyword = '".sanitizeInputForSQL($keyword)."'";
    $results = $conn->query($sql);


    // Verifica se ci sono errori nell'esecuzione della query di aggiornamento
    if ($stmt->errorCode() == 0) {
        // Nessun errore, restituisci una risposta JSON con successo
        echo json_encode(['success' => true, 'message' => 'Aggiornato con successo']);
    } else {
        // Ci sono stati errori, restituisci una risposta JSON con l'errore
        echo json_encode(['success' => false, 'error' => $stmt->errorInfo()]);
    }
}
?>
