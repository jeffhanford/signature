<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Update Player</title>
</head>

<body>
<?php
include 'WGAPI.php';

//Manually transfers the old database to teh new one, no longer used

list($dbname, $dbpass) = getDbInfo();
$con=mysqli_connect(ini_get("mysql.default_host"),$dbname,$dbpass);

$player = mysqli_query($con, "SELECT * FROM signature.players WHERE verify=1");

while($row = mysqli_fetch_array($player)){
	$playerID = $row[0];
	$nickname = getRealName($playerID);
	$signame = $row[14];
	$frames = $row[15];
	$namecolor = $row[16];
	$backcolor = $row[17];
	$verify = 1;
	mysqli_query($con, "INSERT INTO signature.player (nickname, playerid, namecolor, backcolor, verify, signame, frames)
							VALUES ('" . $nickname . "','" . $playerID . "','" . $namecolor . "','" . $backcolor . "'," . $verify . ",'" . $signame . "'," . $frames .")");	
}
mysqli_close($con);





?>
</body>
</html>