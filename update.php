<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Update Player</title>
</head>

<body>
<?php
include 'WGAPI.php';
include 'gif.php';
include 'Updatesignature.php';

//First, we need to update the database with all of the player's information
if (isset($_POST['name'])) {	
	$playername = $_POST['name'];
	
	if (playerExists($playername)){
		$playerID = getPlayerID($playername);
		$namecolor = (string)$_POST['namecolor'];
		$backcolor = (string)$_POST['backcolor'];
		$verified = (int)$_POST['verified'];
		$sigurl = (string)$_POST['sigurl'];
		$delay = (int)$_POST['delay'];
		$settings = (string)$_POST['settings'];
		
		//$test = json_decode($settings, true);
		//file_put_contents("w.txt",$test['toptanks']);
		updatePlayer($playerID, $namecolor, $backcolor, $verified, $sigurl, $delay, $settings);
		list($realname, $playerID, $namecolor, $backcolor, $verified, $signame, $frames, $delay, $settings) = getDB($playerID);
		updateSignature($playerID, $realname, $namecolor, $backcolor, $signame, $frames, $delay, $settings);
	}
}

else if (isset($_GET['add'])){
	$playername = $_GET['name'];
	if (playerExists($playername)){
		$playerID = getPlayerID($playername);
		$namecolor = $_GET['namecolor'];
		$backcolor = $_GET['backcolor'];
		$verified = $_GET['verified'];
		$sigurl = $_GET['sigurl'];
		$delay = $_GET['delay'];
		$frames=0;
		updatePlayer($playerID, $namecolor, $backcolor, $verified, $sigurl, $delay, $settings);
		list($realname, $playerID, $namecolor, $backcolor, $verified, $signame, $frames, $delay, $settings) = getDB($playerID);
		updateSignature($playerID, $realname, $namecolor, $backcolor, $signame, $frames, $delay, $settings);
	}
}

else if(isset($_GET['name'])) {
	$playername = $_GET['name'];
	if (playerExists($playername)){
		$playerID = getPlayerID($playername);
		list($realname, $playerID, $namecolor, $backcolor, $verified, $signame, $frames, $delay, $settings) = getDB($playerID);
		updateSignature($playerID, $realname, $namecolor, $backcolor, $signame, $frames, $delay, $settings);		
	}
}
else{
	list($dbname, $dbpass) = getDbInfo();
	$con=mysqli_connect(ini_get("mysql.default_host"),$dbname,$dbpass);

	$player = mysqli_query($con, "SELECT * FROM signature.player");
	
	while($row = mysqli_fetch_array($player)){
		list($realname, $playerID, $namecolor, $backcolor, $verified, $signame, $frames, $delay, $settings) = $row; 
		updateSignature($playerID, $realname, $namecolor, $backcolor, $signame, $frames, $delay, $settings);
	}
	mysqli_close($con);
}
	
//Adds the player to the database or updates his information if the verification is met
function updatePlayer($playerID, $namecolor, $backcolor, $verify, $sigurl, $delay, $settings){
	list($dbname, $dbpass) = getDbInfo();
	$con=mysqli_connect(ini_get("mysql.default_host"),$dbname,$dbpass);
	
	//Check if the player is in the database
	$checkname = "SELECT EXISTS(SELECT * FROM signature.player WHERE playerid = '" . $playerID . "')";
	$checked = mysqli_query($con, $checkname);
	$row = mysqli_fetch_array($checked);
	$nameinDB = $row[0];
	
	
	list($nickname, $clanID, $personalrating, $battles, $winrate, $inclan) = getPlayerInfo($playerID);
	
	if($inclan){
		list($clantag, $rank) = getClanInfo($playerID, $clanID);
		$clanurl = getClanEmblem($clanID);
		$checkclan = "SELECT EXISTS(SELECT * FROM signature.clans WHERE tag LIKE '" . $clantag . "')";
		$checkedc = mysqli_query($con, $checkclan);
		$clanrow = mysqli_fetch_array($checkedc);
		$claninDB = $clanrow[0];
		if(!$claninDB){
			echo "" . $clantag . "not in db, adding";
			mysqli_query($con, "INSERT INTO signature.clans (tag, url)
				VALUES ('" . $clantag . "', '" . $clanurl . "')");
			copy($clanurl, './clans/'.$clantag.'.png');
		}
	}
	
	
	
	if ((strpos($sigurl, "http://i.imgur.com") >-1 || strpos($sigurl, "https://i.imgur.com") >-1)){
		if(strpos($sigurl, ".png")>-1 || strpos($sigurl, ".jpg")>-1){
			copyImage($sigurl,$playerID);
			$signame = $playerID;
			$frames = 0;
		}
		if(strpos($sigurl, ".gif")>-1){
			//HANDLE NON ANIMATED GIFS
			$saveloc = "./custom/".$playerID.".gif";
			copy($sigurl,$saveloc);
			$frames = analyzeGif($saveloc,$playerID);
			if($frames==0){
				copyImage($sigurl,$playerID);
			}
			unlink($saveloc);
			$signame=$playerID;	
		}
	}
	
	else{
		$signame ='same';
	}
	

	
	if(!$nameinDB){
		echo "adding ". $nickname;
		if(!isset($frames)){$frames=0;}
		if(!isset($delay)){$delay=0;}
		if ($signame == 'same'){$signame = 'blacksig';}
		$q = ("INSERT INTO signature.player (nickname, playerid, namecolor, backcolor, verify, signame, frames, delay, settings) VALUES ('" . $nickname . "','" . $playerID . "','" . $namecolor . "','" . $backcolor . "'," . $verify . ",'" . $signame . "'," . $frames . "," . $delay .",'".$settings."')");
		echo $q;
		mysqli_query($con, $q);	
	}
	else{
		$data = mysqli_query($con, "SELECT * FROM signature.player WHERE playerid = '".$playerID."'");
		while ($row = mysqli_fetch_array($data)){
			if($row[1] == $playerID){
				$verified = $row[4];
				$newsigname = $row[5];
				if(!isset($frames)){$frames = $row[6];}	
				if(!isset($delay)){$delay = $row[7];}		
			}
		}

		if($signame=='same'){$signame = $newsigname;}
		if (($verified==1 && $verify==1) || $verified==0){
			mysqli_query($con, "UPDATE signature.player SET nickname='".$nickname."',namecolor='".$namecolor."',backcolor='".$backcolor."',verify='".$verify."',frames='".$frames."',delay='".$delay."',signame='".$signame."',settings='".$settings."' WHERE playerid = '".$playerID."'");
		}
	}
	
	mysqli_close($con);
	
}
	


function copyImage($sigurl, $playerid){
	if (strpos($sigurl, ".png")>-1) {$suffix='.png';}
	else if (strpos($sigurl, ".gif")>-1) {$suffix='.gif';}
	else{$suffix='.jpg';}
	
	$imagename = './custom/'.$playerid.$suffix;
	copy($sigurl, $imagename);
	$image = imagecreatefrompng("./custom/blacksig.png");
	
	if ($suffix=='.png'){
		$src1 = imagecreatefrompng($imagename);
	}
	else {
		$src1 = imagecreatefromjpeg($imagename);
		unlink($imagename);
	}
	
	$size = getimagesize($sigurl);
	$w = $size[0];
	$h = $size[1];
	
	imagecopyresampled($image, $src1, 0,0,0,0,468,100,$w,$h);
	$imagename = './custom/'.$playerid.'.png';
	imagepng($image, $imagename);
	imagedestroy($image);
}

function updateSignature($playerID, $realname, $namecolor, $backcolor, $signame, $frames, $delay, $settings){
	$stats = getStats($playerID);
	$filename = strtolower($realname);
	$settings = json_decode($settings, true);
	$lowername = strtolower($realname);
	$mask = "./sigs/".$lowername.'*.*';
	if($frames>0){
		for ($x=1; $x<=$frames; $x++) {
			$filename = 'gifs/' . strtolower($realname) . (string)$x;
			$customname = 'gifs/' . $signame . (string)$x;
			if($realname == "Tioga060"){
				makesig2($customname, $filename, $stats);
			}
			else{
				makeSig($playerID, $namecolor, $backcolor, $customname, $filename, $settings, $stats);
			}
		}
		if($realname == "Tioga060"){
			createGif($realname, $frames, $delay);
		}
		else{						
			array_map('unlink', glob($mask));
			createGif($realname, $frames, $delay);
		}
		
	}
	else{
		array_map('unlink', glob($mask));
		makeSig($playerID, $namecolor, $backcolor, $signame, $filename, $settings, $stats);	
	}
}

function getDB($playerID){
	list($dbname, $dbpass) = getDbInfo();
	$con=mysqli_connect(ini_get("mysql.default_host"),$dbname,$dbpass);
	$player = mysqli_query($con, "SELECT * FROM signature.player");
	
	while($row = mysqli_fetch_array($player)){
		if ($row[1] == $playerID){return $row;}
	}
	mysqli_close($con);
}

?>
</body>
</html>