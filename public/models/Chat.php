<?php

// models/Chat.php

class Chat {
    private $conn;

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    public function sendMessage($username, $message, $keyy, $user_chats) {
        // Check if $user_chats is 'null'
        if (base64_decode($user_chats) !== 'null') {
            // Salva il messaggio nel database, insieme al mittente e alla data
            $query = "INSERT INTO chat (username, message, created_at, keyy, user_chats) VALUES (:username, :message, NOW(), :keyy, :user_chats)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':username', $username, PDO::PARAM_STR);
            $stmt->bindValue(':message', $message, PDO::PARAM_STR);
            $stmt->bindValue(':keyy', $keyy, PDO::PARAM_STR);
            $stmt->bindValue(':user_chats', $user_chats, PDO::PARAM_STR);
            $stmt->execute();
        }
    }
}
