<?php
// Fetch sensor box data from OpenSenseMap API
$apiUrl = 'https://api.opensensemap.org/boxes/623f6e24c4de74001c66f4cb';
$response = file_get_contents($apiUrl);
$data = json_decode($response, true);
?>
<!DOCTYPE html>
<html>
<head>
    <title>OpenSenseMap Data</title>
</head>
<body>
    <?php include 'menu.php'; ?>
    <?php include 'box.php'; ?>
</body>
</html>