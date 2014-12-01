<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>updater</title>
</head>

<body>
<?php
include 'WGAPI.php';
$stats = getWotlabs("Tioga060");

echo var_dump($stats);


?>
</body>
</html>