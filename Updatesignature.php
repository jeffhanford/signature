<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Update Signature</title>
</head>

<body>
<?php
//include 'WGAPI.php';
include 'colorscales.php';

function getStats($playerID){
	list($name, $clanID, $personalrating, $battles, $wr, $inclan) = getPlayerInfo($playerID);
	
	if($inclan){
		list($clan, $rank) = getClanInfo($playerID, $clanID);
	}
	
	else{
		$clan = 'ignore';
		$rank = 'ignore';
	}
	
	list($tier, $wn8, $rwn8, $rwr) = getWotlabs($name);
	
	list($exprank, $expvalue) = getExpRecent($playerID);
	list($tank1, $tank2, $tank3) = getMostplayedTanks($playerID);
	
	
	return array($name, $clanID, $personalrating, $battles, $wr, $inclan, $clan, $rank, $tier, $wn8, $rwn8, $rwr, $exprank, $expvalue,$tank1, $tank2, $tank3);
	
}

function makeSig($playerID, $namecolor, $backcolor, $signame, $filename, $settings, $stats){	

	list($name, $clanID, $pr, $battles, $wr, $inclan, $clan, $rank, $tier, $wn8, $rwn8, $rwr,$exprank, $expvalue,$tank1, $tank2, $tank3) = $stats;
	$toptanks = $settings['toptanks'];
	$clansetting = $settings['clan'];
	$gamessetting = $settings['games'];
	
	

	$namet = explode(",", $namecolor);
	$backt = explode(",", $backcolor);
	$namergb = array((int)$namet[0],(int)$namet[1],(int)$namet[2]);
	$backrgb = array((int)$backt[0],(int)$backt[1],(int)$backt[2]);
	
	$image = imagecreatefrompng("./custom/blacksig.png");
	//output header
	//header('Content-Type: image/png'); 
	//output image
	$imagename1 = "./custom/";
	$imagename1 .= (string)$signame;
	$imagename1 .= ".png";
	$src1 = imagecreatefrompng($imagename1);
	imagecopy($image, $src1, 0, 0, 0, 0, 468, 100);
	
	//setup some colours
	$darkred = imagecolorallocate($image, 147,13,13);
	$red = imagecolorallocate($image, 205,51,51);
	$orange = imagecolorallocate($image, 204,122,0);
	$yellow = imagecolorallocate($image, 204,184,0);
	$lightgreen = imagecolorallocate($image, 132,155,36);
	$green = imagecolorallocate($image, 77,115,38);
	$lightblue = imagecolorallocate($image, 64,153,191);
	$blue = imagecolorallocate($image, 57,114,198);
	$blue = imagecolorallocate($image, 57,114,198);
	$lightpurple = imagecolorallocate($image, 121,61,182);
	$purple = imagecolorallocate($image, 64,16,112);
	$white = imagecolorallocate($image, 255,255,255);
	$namec = imagecolorallocate($image, $namergb[0],$namergb[1],$namergb[2]);
	$backc = imagecolorallocate($image, $backrgb[0],$backrgb[1],$backrgb[2]);
	$whitesemi  = imagecolorallocatealpha($image, 255, 255, 255, 60);
	$black = imagecolorallocate($image, 0, 0, 0);
	$pink = imagecolorallocate($image, 255, 20, 147);
	$colors = array($darkred, $red, $orange, $yellow, $lightgreen, 
				$green, $lightblue, $blue, $lightpurple, $purple, $pink);
	
	
	$wn8color = $colors[getwncolor($wn8)];
	$rwn8color = $colors[getwncolor($rwn8)];
	$wrcolor = $colors[getwrcolor($wr)];
	$rwrcolor = $colors[getwrcolor($rwr)];
	
	if ($name == "Mawderator"){
		$name = "Memerator";
	}
	
	
	if($imagename1 == './custom/blacksig.png'){
		imagefilledrectangle($image, 0, 0, 468, 100, $backc);
	}
	
	
	//create our semi transparent white rectangle for text	
	imagefilledrectangle($image, 0, 0, 60, 38, $rwrcolor);
	imagefilledrectangle($image, 60, 0, 120, 38, $wrcolor);
	imagefilledrectangle($image, 0, 38, 60, 76, $rwn8color);
	imagefilledrectangle($image, 60, 38, 120, 76, $wn8color);
	
	
	

	$fontt = './verdanab.ttf';
	imagettftext($image, 5, 0, 6, 11, $white, $fontt, 'RECENT WR');
	imagettftext($image, 14, 0, 3, 32, $white, $fontt, $rwr);
	imagettftext($image, 5, 0, 4, 49, $white, $fontt, 'RECENT WN8');
	imagettftext($image, 14, 0, 3, 70, $white, $fontt, $rwn8);
	
	imagettftext($image, 5, 0, 72, 11, $white, $fontt, 'OVERALL');
	imagettftext($image, 14, 0, 65, 32, $white, $fontt, $wr);
	imagettftext($image, 5, 0, 72, 49, $white, $fontt, 'OVERALL');
	imagettftext($image, 14, 0, 63, 70, $white, $fontt, $wn8);
	
	imagettftext($image, 13, 0, 130, 20, $namec, $fontt, $name);
	
	//imagettftext($image, 10, 0, 130, 70, $namec, $fontt, $tier);
	if ($inclan){
		if($clansetting){
			
			imagettftext($image, 8, 0, 396, 9, $namec, $fontt, $rank);
			imagettftext($image, 10, 0, 396, 32, $namec, $fontt, $clan);
			
			//lets add an image over it too, create instance of image
			$clanimage = './clans/';
			$clanimage .= (string)$clan;
			$clanimage .= '.png';
			$src = imagecreatefrompng($clanimage);
			imagealphablending( $src, true );
			imagesavealpha( $src, true );
			// Copy and merge images together
			imagecopy($image, $src, 396, 34, 0, 0, 64, 64);
		}
	}
	
	
	if (($exprank <=1000) && ($exprank >0)){
		//imagefilledrectangle($image, 0, 76, 120, 100, $pink);
		imagettftext($image, 5, 0, 4, 83, $namec, $fontt, "Exp/Game");
		imagettftext($image, 14, 0, 4, 98, $namec, $fontt, $expvalue);
		imagettftext($image, 5, 0, 64, 83, $namec, $fontt, "Server Rank");
		imagettftext($image, 14, 0, 64, 98, $namec, $fontt, $exprank);
		if($exprank<100){
		$src = imagecreatefrompng("checkmark.png");
			imagealphablending( $src, true );
			imagesavealpha( $src, true );
			imagecopy($image, $src, 104, 82, 0, 0, 16, 16);
		}
		//imagettftext(
		
	}
	if ($name == "vonluckner"){
		imagettftext($image, 72, 10, 150, 110, $red, "./old_stamper.ttf", "[DEAD]");
	}
	
	$batx=130;
	$baty=34;
	$tierx=130;
	$tiery=48;
	
	if($toptanks==true){
		$batx=260;
		$baty=98;
		$tierx=320;
		$tiery=98;
		
		$imtank1 = imagecreatefrompng("./tanks/".$tank1.".png");
		imagealphablending($imtank1, true);
		imagesavealpha($imtank1, true);
		$w = imagesx($imtank1);
		$h = imagesy($imtank1);
		imagecopy($image, $imtank1, 130, 25, 0, 0, $w, $h);
		
		$imtank2 = imagecreatefrompng("./tanks/".$tank2.".png");
		imagealphablending($imtank2, true);
		imagesavealpha($imtank2, true);
		$w = imagesx($imtank2);
		$h = imagesy($imtank2);
		imagecopy($image, $imtank2, 130, 50, 0, 0, $w, $h);
		
		$imtank3 = imagecreatefrompng("./tanks/".$tank3.".png");
		imagealphablending($imtank3, true);
		imagesavealpha($imtank3, true);
		$w = imagesx($imtank3);
		$h = imagesy($imtank3);
		imagecopy($image, $imtank3, 130, 75, 0, 0, $w, $h);
	}
	
	if($gamessetting){
		imagettftext($image, 10, 0, $batx, $baty, $namec, $fontt, $battles);
		imagettftext($image, 10, 0, $tierx, $tiery, $namec, $fontt, $tier);
	}
	
	
	//$blankimage = imagecreatetruecolor(64, 64);
	//imagealphablending( $blankimage, false );
	//imagesavealpha( $blankimage, true );
	
	
	
	if($pr>10000){
		$prclub = imagecreatefrompng("10k.png");
		imagecopymerge($image, $prclub, 378, 82, 0, 0, 16, 16, 100);
	}

	
	$imagename = './sigs/';
	$imagename .= (string)$filename;
	$imagename .='.png';
	
	$imagename = strtolower($imagename);
	
	imagepng($image, $imagename);
	imagedestroy($image);

}

function makesig2($signame, $filename, $stats){
	list($name, $clanID, $personalrating, $battles, $wr, $inclan, $clan, $rank, $tier, $wn8, $rwn8, $rwr, $exprank, $expvalue) = $stats;
	$image = imagecreatefrompng("./custom/blacksig.png");
	
	$darkred = imagecolorallocate($image, 147,13,13);
	$red = imagecolorallocate($image, 205,51,51);
	$orange = imagecolorallocate($image, 204,122,0);
	$yellow = imagecolorallocate($image, 204,184,0);
	$lightgreen = imagecolorallocate($image, 132,155,36);
	$green = imagecolorallocate($image, 77,115,38);
	$lightblue = imagecolorallocate($image, 64,153,191);
	$blue = imagecolorallocate($image, 57,114,198);
	$lightpurple = imagecolorallocate($image, 121,61,182);
	$purple = imagecolorallocate($image, 64,16,112);
	$white = imagecolorallocate($image, 255,255,255);
	$whitesemi  = imagecolorallocatealpha($image, 255, 255, 255, 60);
	$black = imagecolorallocate($image, 0, 0, 0);
	$pink = imagecolorallocate($image, 255, 20, 147);
	$colors = array($darkred, $red, $orange, $yellow, $lightgreen, 
				$green, $lightblue, $blue, $lightpurple, $purple, $pink);
	
	
	$wn8color = $colors[getwncolor($wn8)];
	$rwn8color = $colors[getwncolor($rwn8)];
	$wrcolor = $colors[getwrcolor($wr)];
	$rwrcolor = $colors[getwrcolor($rwr)];
	
	imagefilledrectangle($image, 0, 0, 234, 50, $rwrcolor);
	imagefilledrectangle($image, 234, 0, 468, 50, $wrcolor);
	imagefilledrectangle($image, 0, 50, 234, 100, $rwn8color);
	imagefilledrectangle($image, 234, 50, 468, 100, $wn8color);

	
	$imagename1 = "./custom/";
	$imagename1 .= (string)$signame;
	$imagename1 .= ".png";
	$src1 = imagecreatefrompng($imagename1);
	imagealphablending( $src1, true );
	imagesavealpha( $src1, true );
	imagecopy($image, $src1, 0, 0, 0, 0, 468, 100);
	
	$fontt = './verdanab.ttf';
	
	imagettftext($image, 7, 45, 199, 31, $white, $fontt, $rwr);
	imagettftext($image, 7, -45, 196, 70, $white, $fontt, $rwn8);
	imagettftext($image, 7, -45, 253, 14, $white, $fontt, $wr);
	imagettftext($image, 7, 45, 254, 88, $white, $fontt, $wn8);
	
	$imagename = './sigs/';
	$imagename .= (string)$filename;
	$imagename .='.png';
	
	$imagename = strtolower($imagename);
	
	imagepng($image, $imagename);
	imagedestroy($image);
}



?>
</body>
</html>