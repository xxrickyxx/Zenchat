<?php 

require_once '../conn/index.php';

$user_chats = $_GET['user_chats'];

$sql = "SELECT url FROM urlchat WHERE user_chats = '$user_chats'";

// Aggiungi questa riga per visualizzare la query SQL
//echo "Query SQL: " . $sql . PHP_EOL;

$stmt = $conn->prepare($sql);
$stmt->execute();

$url = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Visualizza la struttura dell'array e l'URL ottenuto
//echo "Risultato della query (var_dump):" . PHP_EOL;


// Controlla se ci sono risultati prima di tentare di accedere all'array
if (!empty($url)) {
    // Accedi all'URL nell'array
    $urlValue = $url[0]['url'];
    echo $urlValue;
} else {
    echo "Nessun risultato trovato";
}
