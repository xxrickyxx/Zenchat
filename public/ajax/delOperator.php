<?php
session_start();
require_once '../conn/index.php';


$username = $_POST['adminUsername'];
$sql = "SELECT password, dominio, id FROM utenti WHERE username = :username";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':username', $username, PDO::PARAM_STR);
$stmt->execute();
$verify_user = $stmt->fetch();

$pass_verify = $_SESSION['pass'];
$id=$_POST['operatorId'];

if ($verify_user['password'] === hash('sha256', $pass_verify)) {

    $sql = "UPDATE utenti SET stato = '5', username='system' WHERE id= '".$id."' ";
    $results = $conn->query($sql);

if (!!$results){

	echo "success";
} else {

	echo "error";
}


}


