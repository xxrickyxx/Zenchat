<?php

session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once 'conn/index.php';
require_once '/var/www/html/kloe/public/lib/PHPMailer/src/PHPMailer.php';
require_once '/var/www/html/kloe/public/lib/PHPMailer/src/SMTP.php';
require_once '/var/www/html/kloe/public/lib/PHPMailer/src/Exception.php';

$stato=0;
$username = $_GET['username'];
$sql = "SELECT password, dominio, id FROM utenti WHERE username = :username AND stato = :stato";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':username', $username, PDO::PARAM_STR);
$stmt->bindParam(':stato', $stato, PDO::PARAM_STR);
$stmt->execute();
$verify_user = $stmt->fetch();


$pax=$verify_user['password'];
// Assuming $_POST['password'] contains the user's input password
$password = $_POST['password'];

// Funzione per inviare l'email di registrazione
function sendRegistrationEmail($username) {
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Configura le impostazioni SMTP
        $mail->isSMTP();
        $mail->Host = 'authsmtp.securemail.pro';
        $mail->SMTPAuth = true;
        $mail->Username = 'no-reply@zenchat.it';
        $mail->Password = 'Antonella@1978'; // Replace with the actual password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Set the sender and recipient addresses
        $mail->setFrom('no-reply@zenchat.it', 'Zenchat');
        $mail->addAddress($username);

        // Set the email subject and body
        $mail->Subject = 'Activate account Successful';
        $mail->Body = 'Thank you for activate account!';

        // Send the email
        $mail->send();

      //  echo 'Email has been sent successfully';
        echo "";
    } catch (Exception $e) {
       // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        echo "";
    }

}


$id_utente_da_aggiornare = $verify_user['id'];; // Sostituisci con l'ID effettivo dell'utente

if (!!$verify_user['password']) {


$sql_update = "UPDATE utenti SET stato = 1 WHERE id = :id_utente";
$stmt_update = $conn->prepare($sql_update);
$stmt_update->bindParam(':id_utente', $id_utente_da_aggiornare, PDO::PARAM_INT);
$stmt_update->execute();

// Verifica se ci sono errori nell'esecuzione dell'aggiornamento
if ($stmt_update->errorCode() == 0) {
    // Nessun errore, l'aggiornamento è avvenuto con successo
//    echo "Aggiornamento dello stato avvenuto con successo.";
    sendRegistrationEmail($username);
    // Esegui il redirect con lo stato nell'URL
    $stato = 'Activate account success!'; // Puoi impostare lo stato in base alla tua logica
    $url_redirect = 'https://kloe.zenchat.it/?stato=' . urlencode($stato);
    header('Location: ' . $url_redirect);
    

} else {
    // Ci sono stati errori durante l'aggiornamento
  //  echo "Errore durante l'aggiornamento dello stato: " . implode(", ", $stmt_update->errorInfo());
    $stato = 'Not Activate account! Generic Error.'; // Puoi impostare lo stato in base alla tua logica
    $url_redirect = 'https://kloe.zenchat.it/?stato=' . urlencode($stato);
    header('Location: ' . $url_redirect);
}




	} else {

		//echo "Error_login";
            // Ci sono stati errori durante l'aggiornamento
    //echo "Errore durante l'aggiornamento dello stato: " . implode(", ", $stmt_update->errorInfo());
    $stato = 'Account already active! Error.'; // Puoi impostare lo stato in base alla tua logica
    $url_redirect = 'https://kloe.zenchat.it/?stato=' . urlencode($stato);
    header('Location: ' . $url_redirect);
	}


//var_dump($_POST);