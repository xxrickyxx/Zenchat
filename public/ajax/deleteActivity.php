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

$sql = "SELECT COUNT(id) as count FROM bot_responses WHERE user_id = :username";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':username', $username, PDO::PARAM_STR);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$conta_record = $result['count'];

// Utilizzo di $conta_record per ottenere il numero di record
echo "Numero di record: " . $conta_record;


if ($conta_record >= '1'){
    $sql = "DELETE FROM bot_responses WHERE id= '".$id."' ";
    $results = $conn->query($sql);
}


}
