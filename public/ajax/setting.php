<?php
// Imposta l'intestazione del tipo di contenuto su JSON
header('Content-Type: application/json');

session_start();
require_once '../conn/index.php';

$user_chats = $_POST['email'];

$sql = "SELECT * FROM utenti WHERE username = :user_chats";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_chats', $user_chats, PDO::PARAM_STR);
$stmt->execute();
$verify = $stmt->fetch();

// Function to handle file upload
function handleFileUpload($inputName) {
    // Check if the file is uploaded successfully
    if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] == UPLOAD_ERR_OK) {
        $tempName = $_FILES[$inputName]['tmp_name'];
        $uploadDir = 'upload/'; // Change this to your desired upload directory
        $uploadPath = $uploadDir . date('YmdHms') . basename($_FILES[$inputName]['name']);

        // Check image dimensions
        list($width, $height) = getimagesize($tempName);
        if ($width > 800 || $height > 600) {
            // Image dimensions exceed the limit
            echo json_encode(array('error' => 'Image dimensions must be a maximum of 800x600 pixels.'));
            exit;
        }

        // Check file size (max size: 0.5 megabytes)
        if ($_FILES[$inputName]['size'] > 0.5 * 1024 * 1024) {
            // File size exceeds the limit
            echo json_encode(array('error' => 'Image size must be a maximum of 0.5 megabytes.'));
            exit;
        }

        // Move the uploaded file to the desired directory
        move_uploaded_file($tempName, $uploadPath);

        return $uploadPath; // Return the path to the uploaded file
    } else {
        echo json_encode(array('error' => 'File upload failed.'));
        exit;
    }
}

if (!!$verify) {
    // Check if the row already exists in the 'setting' table
    $sqlCheck = "SELECT * FROM setting WHERE id_user = :id_user";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bindParam(':id_user', $user_chats, PDO::PARAM_STR);
    $stmtCheck->execute();
    $existingRow = $stmtCheck->fetch();

    if ($existingRow) {
        // Update the existing row in the 'setting' table
        $sqlUpdate = "UPDATE setting 
                      SET apigpt = :apigpt, onoffgpt = :onoffgpt, img = :img, chatcolor = :chatcolor, assistantgpt = :assistantgpt, synt = :synt
                      WHERE id_user = :id_user";
        
        $stmtUpdate = $conn->prepare($sqlUpdate);
        
        $imgPath = handleFileUpload('logoxx'); // Get the path to the uploaded image
        
$data = array(
    'id_user' => $user_chats,
    'apigpt' => $_POST['aptGPT'],
    'onoffgpt' => isset($_POST['chatGPTSwitch']) ? $_POST['chatGPTSwitch'] : 0,
    'img' => $imgPath,
    'chatcolor' => $_POST['headerColor'],
    'assistantgpt' => $_POST['assistantgpt'],
    'synt' => $_POST['synt']
);
        
        $stmtUpdate->execute($data);
    } else {
        // Insert a new row in the 'setting' table
        $sqlInsert = "INSERT INTO setting (id_user, apigpt, onoffgpt, img, chatcolor, assistantgpt, synt) 
                      VALUES (:id_user, :apigpt, :onoffgpt, :img, :chatcolor, :assistantgpt, :synt)";
        
        $stmtInsert = $conn->prepare($sqlInsert);
        
        $imgPath = handleFileUpload('logoxx'); // Get the path to the uploaded image
        
$data = array(
    'id_user' => $user_chats,
    'apigpt' => $_POST['aptGPT'],
    'onoffgpt' => isset($_POST['chatGPTSwitch']) ? $_POST['chatGPTSwitch'] : 0,
    'img' => $imgPath,
    'chatcolor' => $_POST['headerColor'],
    'assistantgpt' => $_POST['assistantgpt'], 
    'synt' => $_POST['synt']
);
        
        $stmtInsert->execute($data);
    }

    // Esempio di dati da includere nella risposta JSON
    $responseData = array(
        'status' => 'success',
        'message' => 'Impostazioni salvate correttamente',
        'data' => $data
    );

    // Converte e stampa i dati JSON
    echo json_encode($responseData);
}
?>
