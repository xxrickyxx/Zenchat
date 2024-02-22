<?php


require_once '../conn/index.php'; 

if(isset($_POST['user_chats'])) {

$user_chats=base64_encode($_POST['user_chats']);

$sql = "SELECT message, created_at, keyy FROM chat WHERE user_chats = :user_chats order by id ASC";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_chats', $user_chats, PDO::PARAM_STR); // :username è il placeholder per il parametro
$stmt->execute();
$chats = $stmt->fetchAll();

foreach ($chats as $key){

if ($key['keyy']=='2') {

	echo '<div class="user-msg" style=" white-space: pre-wrap !important;
  font-weight: 400 !important;
  text-align: left !important;
  margin-right: auto !important;
  background: #eceff6 !important;
  color: #2c2c2c !important;
  padding: 8px 20px !important;
  max-width: 100% !important;
  border-radius: 15px 15px 15px 0 !important;
  word-wrap: break-word !important;">';
	echo $key['message'];
	echo "<p style='font-size:10px;'>";
	echo $key['created_at'];
	echo "</p>";
	echo "</div><br>";

} else {

	echo '<div class="op-msg" style="  white-space: pre-wrap !important;
  margin-left: auto !important;
  padding: 8px 20px !important;
  border-radius: 15px 15px 0 15px !important;
  font-weight: 400 !important;
  max-width: 100% !important;
  word-wrap: break-word !important;
  text-align: left !important;
  background: #1e88e5 !important;
  color: white !important;">'; 
	echo $key['message'];
	echo "<p style='font-size:10px;'>";
	echo $key['created_at'];
	echo "</p>";
	echo "</div><br>";

}
}
}