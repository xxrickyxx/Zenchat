<?php

// controllers/BotController.php

require_once '../models/BotModel.php';

class BotController {
    private $botModel;

    public function __construct(BotModel $botModel) {
        $this->botModel = $botModel;
    }

    public function respondToUser($userInput, $user_id, $username) {
        // Ottieni la risposta del bot
        $botResponse = $this->botModel->getBotResponse($userInput, $user_id, $username);
        return $botResponse;
    }
}

