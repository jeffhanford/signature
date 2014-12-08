<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Update Signature</title>
</head>

<body>
<?php

//Takes the player's WN8 ranking as a string input
//Returns the array index representative of the color that corresponds to the player's WN8 ranking
function getwncolor($input){		
	$n = (int)$input;
	if ($n < 300) { return 0; }
	else if (($n >= 300) and ($n < 450)) { return 1; }
	else if (($n >= 450) and ($n < 650)) { return 2; }
	else if (($n >= 650) and ($n < 900)) { return 3; }
	else if (($n >= 900) and ($n < 1200)) { return 4; }
	else if (($n >= 1200) and ($n < 1600)) { return 5; }
	else if (($n >= 1600) and ($n < 2000)) { return 6; }
	else if (($n >= 2000) and ($n < 2450)) { return 7; }
	else if (($n >= 2450) and ($n < 2900)) { return 8; }
	else if (($n >= 2900) and ($n < 3800)) { return 9; }
	else if ($n >= 3800) { return 10; }
	else return 0;
}

//Takes the player's winrate as a string input, formatted as "xx%" (example: 50%)
//Returns the array index representative of the color that corresponds to the player's winrate
function getwrcolor($input){		
	$n = (float)substr($input, 0, strlen($input)-1);
	if ($n < 46.0) { return 0; }
	if ($n >= 46.0 and $n < 46.5) { return 1; }
	if ($n >= 46.5 and $n < 47.5) { return 2; }
	if ($n >= 48.5 and $n < 49.5) { return 3; }
	if ($n >= 49.5 and $n < 51.5) { return 4; }
	if ($n >= 51.5 and $n < 53.5) { return 5; }
	if ($n >= 53.5 and $n < 55.5) { return 6; }
	if ($n >= 55.5 and $n < 59.5) { return 7; }
	if ($n >= 59.5 and $n < 64.5) { return 8; }
	if ($n >= 64.5 and $n < 70.5) { return 9; }
	if ($n >= 70.5) { return 10; }
	return 0;
}
?>
</body>
</html>