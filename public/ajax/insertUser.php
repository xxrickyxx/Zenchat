<?php

session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once '../conn/index.php';
require_once '/var/www/html/kloe/public/lib/PHPMailer/src/PHPMailer.php';
require_once '/var/www/html/kloe/public/lib/PHPMailer/src/SMTP.php';
require_once '/var/www/html/kloe/public/lib/PHPMailer/src/Exception.php';


$username = $_POST['email'];
$sql = "SELECT password, dominio, id FROM utenti WHERE username = :username";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':username', $username, PDO::PARAM_STR);
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
        $mail->Subject = 'Registration Successful';
        $mail->Body = 'Thank you for registering! Check your email to <a href="https://kloe.zenchat.it/activate?token=5656732gfhsadghasdt3&username='.strip_tags($username).'">Activate</a>  your account.';

        // Send the email
        $mail->send();

      //  echo 'Email has been sent successfully';
        echo "";
    } catch (Exception $e) {
       // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        echo "";
    }

}


// Hash the password using SHA-256 (you can choose a different algorithm if needed)
$hashedPassword = hash('sha256', $password);
$stato=0;
$dominio=$username;
$stato_chat=0;
$lg="en";
// Get the current date and time
$currentDateTime = new DateTime();

// Add 3 months to the current date
$currentDateTime->add(new DateInterval('P3M'));

// Format the resulting date in the desired format (Y-m-d H:i:s)
$newDateTime = $currentDateTime->format('Y-m-d H:i:s');

// Output the new date and time
$data_scadenza = $newDateTime;

if (!$verify_user['password']) {

    $sql = "INSERT INTO utenti (username, password, stato, dominio, stato_chat, lg, data_scadenza) VALUES (:username, :password, :stato, :dominio, :stato_chat, :lg, :data_scadenza)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
    $stmt->bindParam(':stato', $stato, PDO::PARAM_STR);
    $stmt->bindParam(':dominio', $dominio, PDO::PARAM_STR);
    $stmt->bindParam(':stato_chat', $stato_chat, PDO::PARAM_STR);
    $stmt->bindParam(':lg', $lg, PDO::PARAM_STR);
    $stmt->bindParam(':data_scadenza', $data_scadenza, PDO::PARAM_STR);
    $stmt->execute();


    // Verifica se ci sono errori nell'esecuzione della query di inserimento
    if ($stmt->errorCode() == 0) {
    	sendRegistrationEmail($username);
        // Nessun errore, restituisci una risposta JSON con successo
        echo json_encode(['success' => true, 'message' => 'success']);
    } else {
        // Ci sono stati errori, restituisci una risposta JSON con l'errore
        echo json_encode(['success' => false, 'error' => $stmt->errorInfo()]);
    }



	} else {

		echo "Error";
	}


//var_dump($_POST);