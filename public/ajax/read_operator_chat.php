<?php

require_once '../conn/index.php'; 

if(isset($_POST['username_chat'])) {

$username_chat=$_POST['username_chat'];
/*
$sql = "SELECT * FROM chat WHERE username = :username_chat order by id ASC";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':username_chat', $username_chat, PDO::PARAM_STR); // :username è il placeholder per il parametro
$stmt->execute();
$chats = $stmt->fetchAll();*/
// Data corrente
$currentDate = date('Y-m-d H:i:s');

// Data di un mese fa
$oneMonthAgo = date('Y-m-d H:i:s', strtotime('-1 month'));

$sql = "SELECT DISTINCT *
        FROM chat
        WHERE username = :username_chat
          AND created_at >= :oneMonthAgo
        ORDER BY id DESC";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':username_chat', $username_chat, PDO::PARAM_STR);
$stmt->bindParam(':oneMonthAgo', $oneMonthAgo, PDO::PARAM_STR);
$stmt->execute();
$chats = $stmt->fetchAll();
$verify="";

foreach ($chats as $key) {
if ($verify!=$key['user_chats']){
if ($key['keyy']==2) {
$sql = "SELECT id FROM chat WHERE username = :username AND :user_chats = user_chats";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':username', $key['username'], PDO::PARAM_STR);
$stmt->bindParam(':user_chats', $key['user_chats'], PDO::PARAM_STR);
$stmt->execute();
$contak = $stmt->fetchAll();
$conta_keyy=count($contak);
if ($conta_keyy!='1'){ 
	//echo '<button  id="open-chat-button" onclick="sendMessagechatsx()">Apri chat con ' . base64_decode($key['user_chats']) . '</button>';
	echo '<button id="open-chat-button" class="ghghghg" style="font-size:12px;" onclick="sendMessagechatsx(\'' . base64_decode($key['user_chats']) . '\')">Open chat ' . base64_decode($key['user_chats']) . '</button>'; 
} else { 

	echo '<div id="suona"><button id="open-chat-button" class="ghghghg" style="font-size:12px; background-color:red !important;" onclick="sendMessagechatsx(\'' . base64_decode($key['user_chats']) . '\')">New chat ' . base64_decode($key['user_chats']) . '</button></div>'; 

}
	echo '<div class="op-msg" style="width:400px; font-size:10px;">';
	echo $key['message'];
	echo "<br>";
	echo $key['created_at'];
	echo '</div><br>';
}

$verify=$key['user_chats']; } 

}

}