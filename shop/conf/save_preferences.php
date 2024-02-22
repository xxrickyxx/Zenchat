<?php
$preferencesFile = 'cookie.json';

// Get the JSON data from the file (create an empty array if the file doesn't exist yet)
$preferences = file_exists($preferencesFile) ? json_decode(file_get_contents($preferencesFile), true) : [];

// Update preferences based on the POST data
$googleAnalytics = isset($_POST['googleAnalytics']) ? $_POST['googleAnalytics'] : false;
$kloeZenchat = isset($_POST['kloeZenchat']) ? $_POST['kloeZenchat'] : false;
$userChats = isset($_POST['userChats']) ? $_POST['userChats'] : '';

// Check if 'userChats' already exists in any row, if yes, update its value
$found = false;
foreach ($preferences as &$row) {
    if ($row['userChats']==$userChats) {
        // Se 'userChats' esiste già, aggiorna la stringa
        $row['googleAnalytics'] = $googleAnalytics;
        $row['kloeZenchat'] = $kloeZenchat;
        $row['userChats'] = $userChats;
        $found = true;
        break;
    }
}

// If 'userChats' is not found in any row, add a new row
if (!$found) {
    $preferences[] = [
        'googleAnalytics' => $googleAnalytics,
        'kloeZenchat' => $kloeZenchat,
        'userChats' => $userChats,
    ];
}

// Save the updated preferences back to the JSON file
file_put_contents($preferencesFile, json_encode($preferences));

// Return a response (you may customize this based on your needs)
echo json_encode(['status' => 'success', 'message' => 'Preferences saved']);
?>
