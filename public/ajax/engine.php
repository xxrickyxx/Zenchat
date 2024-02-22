<?php
session_start();
require_once '../conn/index.php';

// Imposta il numero di elementi per pagina
$items_per_page = 10;

$username = $_GET['username'];

$sql = "SELECT password, dominio, username FROM utenti WHERE username = :username";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':username', $username, PDO::PARAM_STR); // :username è il placeholder per il parametro
$stmt->execute();
$verify_user = $stmt->fetch();

$pass_verify=$_SESSION['pass']; 


$sql = "SELECT id, username FROM utenti WHERE dominio = :dominio AND stato=1";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':dominio', $verify_user['dominio'], PDO::PARAM_STR); // :username è il placeholder per il parametro
$stmt->execute();
$bot_response = $stmt->fetch();

// Includi la libreria Simple HTML DOM Parser
include('htmldom.php');
if (isset($_POST['url'])) {
// URL del sito da analizzare
 $url = $_POST['url'];

// Effettua una richiesta al sito
$html = file_get_html($url);

// Verifica se la richiesta ha avuto successo
if ($html) {
    // Inizializza una lista per le keyword e una per i testi adattati
    $keywords = [];
    $adaptedTexts = [];

// Estrai i paragrafi
foreach ($html->find('p') as $paragraph) {
    // Estrai il testo dal paragrafo
    $text = $paragraph->text(); 

    // Verifica se il paragrafo contiene del testo
    if (!empty($text)) {
        // Potresti utilizzare una logica più avanzata per identificare le keyword
        // In questo esempio, estrai le prime 3 parole come keyword
        $words = str_word_count($text, 1);
        $keyword = implode(' ', array_slice($words, 0, 3));

        // Aggiungi le keyword e il testo adattato alle rispettive liste
        $keywords[] = $keyword;
        $adaptedTexts[] = $text;
    }
}



    // ID dell'utente (puoi modificarlo con l'ID dell'utente effettivo)
    $userId = $_GET['username'];

    // Timestamp corrente
    $timestamp = date('Y-m-d H:m:s');

    function sanitizeInputForSQL($input) {
    // Rimuovi gli accenti
    $input = iconv('UTF-8', 'ASCII//TRANSLIT', $input);

    // Rimuovi virgolette singole e doppie
    $input = str_replace(array("'", '"'), ' ', $input);

    // Rimuovi caratteri speciali che potrebbero causare problemi nelle query SQL
    $input = preg_replace('/[^a-zA-Z0-9_]/', ' ', $input);


    return $input;
}


// Utilizza $safeInput nella tua query SQL INSERT


    // Inserisci i dati nel database
    foreach ($keywords as $index => $keyword) {
        $adaptedText = $adaptedTexts[$index];

if (!!$adaptedText && !!$keyword) {
        // Esegui la query di inserimento


$keyword=sanitizeInputForSQL($keyword);
$adaptedText=sanitizeInputForSQL($adaptedText);

$conta_keyword = strlen($keyword);
$conta_adaptedText = strlen($adaptedText);

if ($conta_keyword > 3 && $conta_adaptedText > 4) {

$sql = "SELECT id FROM bot_responses WHERE user_id = :username";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':username', $userId, PDO::PARAM_STR);
$stmt->execute();
$verify_c = $stmt->fetch();

$conta=count($verify_c);

if ($conta < 1000) {

        $sql = "INSERT INTO bot_responses (keyword, response, user_id, timestamp) VALUES ('$keyword', '$adaptedText', '$userId', '$timestamp')";
        $result = $conn->prepare($sql);
        $result->execute();
}
}
}
        if (!$result) {
            echo "Errore nell'inserimento dei dati: " . $conn->error;
        }
    }

    // Chiudi la connessione al database

} else {
    echo "Errore nella richiesta al sito.";
}



} 