<?php

require_once '../conn/index.php'; 

if(isset($_POST['username_chat'])) {

$username_chat=$_POST['username_chat'];

$sql = "SELECT user_chats FROM chat WHERE username = :username_chat order by id DESC";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':username_chat', $username_chat, PDO::PARAM_STR); // :username è il placeholder per il parametro
$stmt->execute();
$chats = $stmt->fetchAll();



	echo $chats[0]['user_chats'];


}