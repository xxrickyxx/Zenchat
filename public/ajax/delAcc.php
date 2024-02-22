<?php

session_start();
require_once '../conn/index.php';


$username = $_POST['confirmAccount'];
$sql = "SELECT password, dominio, id FROM utenti WHERE username = :username";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':username', $username, PDO::PARAM_STR);
$stmt->execute();
$verify_user = $stmt->fetch();

$pass_verify = $_SESSION['pass'];
$dominio=$_POST['confirmDom'];
$pass=$_POST['confirmPassword'];


if ($verify_user['password'] === hash('sha256', $pass)) {

    $sql = "UPDATE utenti SET stato = '6', username='system' WHERE dominio= '".$dominio."' ";
    $results = $conn->query($sql);



if (!!$results){

	echo "success";
} else {

	echo "error";
}



}

