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

    $id = $_POST['id'];
    $username = $_POST['username'];
    $response = $_POST['response'];
    $buttonId = $_POST['buttonId'];

    function sanitizeInputForSQL($input) {
    // Rimuovi gli accenti
    $input = iconv('UTF-8', 'ASCII//TRANSLIT', $input);

    // Rimuovi virgolette singole e doppie
    $input = str_replace(array("'", '"'), ' ', $input);



    return $input;
}

$conta_response = strlen($response);

if ($buttonId == 'delsolve') {

    $sql = "DELETE FROM user_bot_interactions WHERE id = '".$id."'";
    $results = $conn->query($sql);

} else {

    $sql = "UPDATE user_bot_interactions SET bot_response = '".sanitizeInputForSQL($response)."' WHERE id = '".$id."'";
    $results = $conn->query($sql);

}


}
?>
