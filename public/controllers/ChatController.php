<?php

// controllers/ChatController.php

require_once '../models/Chat.php';

class ChatController {
    private $chat;

    public function __construct(Chat $chat) {
        $this->chat = $chat;
    }

    public function sendMessage($username, $message, $keyy) {
        $this->chat->sendMessage($username, $message, $keyy);
    }

    public function getChatMessages() {
        return $this->chat->getChatMessages();
    }
}
