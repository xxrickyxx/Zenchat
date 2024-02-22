<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../controllers/BotController.php';
require_once '../models/BotModel.php';
require_once '../conn/index.php';

try {
    $botModel = new BotModel($conn);
    $botController = new BotController($botModel);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $postData = json_decode(file_get_contents("php://input"), true);
        if (isset($postData["user_input"]) && isset($postData["user_id"]) && !!$postData["user_input"]) {
            $userInput = $postData["user_input"];
            $user_id = $postData["user_id"];
            $username= $postData["username"];
            $botResponse = $botController->respondToUser($userInput, $user_id, $username); // Utilizza respondToUser invece di getBotResponse
            header('Content-Type: application/json');
            echo json_encode(['bot_response' => $botResponse]);
        }
    }
} catch (Exception $e) {
    echo json_encode(['errore' => $e->getMessage()]);
}
?>
