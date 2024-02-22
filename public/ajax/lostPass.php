<?php
session_start();
require_once '../conn/index.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require_once '/var/www/html/kloe/public/lib/PHPMailer/src/PHPMailer.php';
require_once '/var/www/html/kloe/public/lib/PHPMailer/src/SMTP.php';
require_once '/var/www/html/kloe/public/lib/PHPMailer/src/Exception.php';
// Funzione per inviare l'email di registrazione
function sendRegistrationEmail($username, $token) {
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
        $mail->Subject = 'Lost Pass Zenchat';
        $mail->Body = 'Click here to update your password <a href="https://kloe.zenchat.it/lost?token='.$token.'&username='.strip_tags($username).'">Update</a>';

        // Send the email
        $mail->send();

      //  echo 'Email has been sent successfully';
        echo "";
    } catch (Exception $e) {
       // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        echo "";
    }

}
// Assuming $_POST['email'] is set and not empty
$username = $_POST['email'];

// Check if the user has already attempted 3 times today
$attemptsLimit = 3;
$today = date('Y-m-d');
$sqlCheckAttempts = "SELECT COUNT(*) AS attempts FROM lostpass WHERE email = :email AND DATE(data) = :today";
$stmtCheckAttempts = $conn->prepare($sqlCheckAttempts);
$stmtCheckAttempts->bindParam(':email', $username, PDO::PARAM_STR);
$stmtCheckAttempts->bindParam(':today', $today, PDO::PARAM_STR);
$stmtCheckAttempts->execute();
$attemptsResult = $stmtCheckAttempts->fetch(PDO::FETCH_ASSOC);
$attemptsCount = $attemptsResult['attempts'];

if ($attemptsCount >= $attemptsLimit) {
    // User has exceeded the maximum attempts for today
    echo json_encode(['success' => false, 'message' => 'Maximum attempts reached for today.']);
} else {
    // User is allowed to attempt password recovery

    $stato = 1;
    $token = base64_encode($username . date('YmdH:i:s'));

    // Insert a new record for password recovery
    $sql = "INSERT INTO lostpass (email, stato, token) VALUES (:email, :stato, :token)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $username, PDO::PARAM_STR);
    $stmt->bindParam(':stato', $stato, PDO::PARAM_STR);
    $stmt->bindParam(':token', $token, PDO::PARAM_STR);
    $stmt->execute();

    // Check for errors during the insertion
    if ($stmt->errorCode() == 0) {

    	sendRegistrationEmail($username, $token);
        // No errors, return a JSON response with success
        echo json_encode(['success' => true, 'message' => 'success']);

    } else {
        // Errors occurred, return a JSON response with the error
        echo json_encode(['success' => false, 'error' => $stmt->errorInfo()]);
    }
}
