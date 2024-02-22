<?php
require_once 'models/User.php';
require_once 'controllers/UserController.php';
require_once 'conn/index.php';

function getUserInfo($conn, $username) {
    $sql = "SELECT data_scadenza, stato FROM utenti WHERE username = '".$username."'";
    $results = $conn->query($sql);
    $userInfo = $results->fetch(PDO::FETCH_ASSOC);

    if (!$userInfo) {
        return null; // Utente non trovato
    }

    return $userInfo;
}

$userModel = new User($conn);
$userController = new UserController($userModel);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $userInfo = getUserInfo($conn, $username);

    if (!$userInfo) {
        // Utente non trovato
                header('Location: /error');
                exit();
    } else {
        $expirationDate = new DateTime($userInfo['data_scadenza']);
        $userState = $userInfo['stato'];

        if ($expirationDate < new DateTime()) {
            // Data di scadenza superata
            if ($userState == '1') {
                session_start();
                $_SESSION['token']=base64_encode($username);
                $_SESSION['pass']=$password;
                header('Location: view/demo');
                exit();
            } else {
                header('Location: view/scaduto');
                exit();
            }
        }

        if ($userController->login($username, $password)) {
            echo "Accesso riuscito!";
        } else {
            echo "Accesso fallito. Riprova.";
        }
    }
}
?>
