<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Update Clans</title>
</head>

<body>
<?php
	//Manually called, updates clan icons to newest versions


	include 'WGAPI.php';
	list($dbname, $dbpass) = getDbInfo();
	$con=mysqli_connect(ini_get("mysql.default_host"),$dbname,$dbpass);
	// Check connection
	if (mysqli_connect_errno()) {
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$clanslist = mysqli_query($con,"SELECT * FROM signature.clans");
	while($claninfo = mysqli_fetch_array($clanslist)){
		echo "<pre>";
		echo "Updating " . $claninfo[0] ;
		copy($claninfo[1], './clans/'.$claninfo[0].'.png');
	}
	mysqli_close($con);
		
?>
</body>
</html>