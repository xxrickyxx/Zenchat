<?php
include('../ajax/lang.php');



class BotModel {
    private $conn;

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    public function getBotResponse($userInput, $user_id, $username) {
        $userInput = strtolower($userInput);

        $query = "SELECT * FROM setting WHERE id_user = :username ";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
           die("Errore nella query: " . print_r($this->conn->errorInfo(), true)); 

        }
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        if (!$stmt->execute()) {
            die("Errore nell'esecuzione della query: " . print_r($stmt->errorInfo(), true));
        }
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $onoff = $row['onoffgpt'];

        if (!$onoff) { $onoff="off"; }

        // Verifica se GPT è spento
        if ($onoff == 'on' && !!$row['apigpt']) {
            // API Key e URL del servizio
            $api_key = $row['apigpt'];
            $url = 'https://api.openai.com/v1/chat/completions';
            $modello = "gpt-3.5-turbo";
            $headers = array(
                "Authorization: Bearer {$api_key}",
                "Content-Type: application/json"
            );


$query = "SELECT response FROM bot_responses WHERE user_id = :user_id ";
$stmt = $this->conn->prepare($query);

if (!$stmt) {
    die("Errore nella query: " . print_r($this->conn->errorInfo(), true));
}

$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);

if (!$stmt->execute()) {
    die("Errore nell'esecuzione della query: " . print_r($stmt->errorInfo(), true));
}

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Estrai i valori della colonna "response" in un array
$responses = array_column($rows, 'response');

// Unisci gli elementi dell'array in una stringa, separati da virgole
$responseString = implode(', ', $responses);


            // Configurazione dei messaggi
            $assistant_key = $row['assistantgpt'];
            $synt=($row['synt']." Engine Site Web:".$responseString);

            $messages[] = array("role" => "assistant", "content" => $synt);
            $messages[] = array("role" => "user", "content" => $userInput);

            // Configurazione della richiesta
            $data = array();
            $data["model"] = $modello;
            $data["messages"] = $messages;
            $data["max_tokens"] = 150;
            $data["temperature"] = 0.7;

            // Inizializzazione della sessione cURL
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            // Esecuzione della richiesta HTTP e restituzione del risultato
            $result = curl_exec($curl);
            if (curl_errno($curl)) {
                // Messaggio di errore
                echo "Attenzione: " . curl_error($curl);
            }
            curl_close($curl);

            // Decodifica della risposta JSON
            $json_response = json_decode($result, true);

            // Estrazione della risposta
            $risposta = $json_response["choices"][0]["message"]["content"];
            if (!$risposta) { $risposta="zero count"; }

            // Report sui token utilizzati
           // $risposta= "In questa chat sono stati utilizzati " . $json_response["usage"]["prompt_tokens"] . " token per il prompt.<br />";
           // echo $json_response["usage"]["completion_tokens"] . " token per l'output.<br />";
           // echo $json_response["usage"]["total_tokens"] . " token totali utilizzati.<br />";

            // Stampa la risposta del modello di chat
            //echo $risposta;

            return $risposta;
        }

        // Query per recuperare la risposta dal database
        $query = "SELECT response FROM bot_responses WHERE user_id = :user_id AND (keyword LIKE CONCAT('%', :userInput, '%') OR response LIKE CONCAT('%', :userInput, '%'))";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            die("Errore nella query: " . print_r($this->conn->errorInfo(), true));
        }
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->bindValue(':userInput', $userInput, PDO::PARAM_STR);
        if (!$stmt->execute()) {
            die("Errore nell'esecuzione della query: " . print_r($stmt->errorInfo(), true));
        }
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $response = $row['response'];
        } else {
            // Se non ci sono corrispondenze, restituisci una risposta predefinita
            $defaultResponse = 'I didn t understand how to formulate the question';
            $timestamp = date('Y-m-d H:i:s');
            $this->storeInteraction("TU: $userInput", "KLOE: $defaultResponse", $user_id);
            return $defaultResponse;
        }

        // Memorizza l'interazione nel database
        $timestamp = date('Y-m-d H:i:s');
        $this->storeInteraction("TU: $userInput", "KLOE: $response", $user_id);
        return $response;
    }

    private function storeInteraction($userInput, $botResponse, $user_id) {
        $query = "INSERT INTO user_bot_interactions (user_id, user_input, bot_response) VALUES (:user_id, :user_input, :bot_response)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':user_input', $userInput);
        $stmt->bindParam(':bot_response', $botResponse);
        $stmt->execute();
    }
}
?>
