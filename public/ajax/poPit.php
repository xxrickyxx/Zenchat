<?php 


require_once '../conn/index.php';
if (!!$_POST['url']){
$url=$_POST['url'];
$user_chats=$_POST['token'];


$sql = "SELECT id FROM urlchat WHERE user_chats = :user_chats";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_chats', $user_chats, PDO::PARAM_STR); // :username è il placeholder per il parametro
$stmt->execute();
$verify = $stmt->fetch();

if (!$verify) {

$query = "INSERT INTO urlchat (user_chats, url) VALUES (:user_chats, :url)";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_chats', $user_chats, PDO::PARAM_STR);
$stmt->bindParam(':url', $url, PDO::PARAM_STR);
if ($url !== null) {
    $stmt->execute();
} else {
    // Puoi gestire questa situazione come preferisci, ad esempio restituendo un messaggio di errore o facendo altro
    echo "Il valore di 'url' non può essere nullo.";
}


} else {


$updateQuery = "UPDATE urlchat SET url = :url WHERE user_chats = :user_chats";
$updateStmt = $conn->prepare($updateQuery);
$updateStmt->bindParam(':url', $url, PDO::PARAM_STR);
$updateStmt->bindParam(':user_chats', $user_chats, PDO::PARAM_STR);
$updateStmt->execute();



} }