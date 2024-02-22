<?php

require_once '../conn/index.php'; 

if(isset($_POST['username_chat'])) {

$username_chat=base64_encode($_POST['user_chats']);

$sql = "SELECT * FROM chat WHERE user_chats = :username_chat order by id ASC";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':username_chat', $username_chat, PDO::PARAM_STR); // :username è il placeholder per il parametro
$stmt->execute();
$chats = $stmt->fetchAll();


foreach ($chats as $key) {

	if (base64_decode($key['user_chats'])!='null'){

if ($key['keyy']==2) {
	//echo '<button class="open-chat" data-username="' . base64_decode($key['user_chats']) . '">Apri chat con ' . base64_decode($key['user_chats']) . '</button>';
	echo '<div class="user-msg" style="width:400px;">';
	echo "#".base64_decode($key['user_chats'])."<br>".$key['message'];
	echo "<br>";
	echo $key['created_at'];
	echo '</div><br>';
} else {

	echo '<div class="op-msg" style="width:400px;">';
	echo $key['message'];
	echo "<br>";
	echo $key['created_at'];
	echo '</div><br>';

}

} 

}

}