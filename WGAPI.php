<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>wargamingapi</title>
</head>

<body>
<?php

//include 'signature.php';

//Returns whether or not the player is registered with WOT as boolean
function playerExists($playername){
	$key = getWGAPIkey();
	$contents = file_get_contents('http://api.worldoftanks.com/wot/account/list/?application_id='.$key.'&type=exact&fields=account_id&search='.$playername);
	$array = json_decode($contents, true);
	$data = $array['data'];
	$data = $data[0];

	if (isset($data['account_id'])) {return true;}
	else {return false;}
}

//Returns playerID give playername
function getPlayerID($playername){
	$key = getWGAPIkey();
	$contents = file_get_contents('http://api.worldoftanks.com/wot/account/list/?application_id='.$key.'&type=exact&fields=account_id&search=' . $playername);
	$array = json_decode($contents, true);
	$data = $array['data'];
	$data = $data[0];
	return $data['account_id'];
}

//Returns the true capitalization of the player's nickname given the playerID
function getRealName($playerID){
	$key = getWGAPIkey();
	$contents = file_get_contents("http://api.worldoftanks.com/wot/account/info/?application_id=".$key."&account_id=" . $playerID);
	$array = json_decode($contents, true);
	$data = $array['data'];
	$data = $data[$playerID];
	
	return $data['nickname'];	
}

//Returns the three tank ids of the player's most played tanks' pictures
function getMostplayedTanks($playerID){
	$key = getWGAPIkey();
	$contents = file_get_contents("https://api.worldoftanks.com/wot/account/tanks/?application_id=".$key."&fields=tank_id&account_id=" . $playerID);
	$array = json_decode($contents, true);
	$data = $array['data'];
	$data = $data[$playerID];
	$tank1 = $data[0];
	$tank2 = $data[1];
	$tank3 = $data[2];
	$tank1 = $tank1['tank_id'];
	$tank2 = $tank2['tank_id'];
	$tank3 = $tank3['tank_id'];
	return array((string)$tank1, (string)$tank2, (string)$tank3);
}

//Takes the tankid and returns the url of the picture
function getTankUrl($tankID){
	$key = getWGAPIkey();
	$contents = file_get_contents("https://api.worldoftanks.com/wot/encyclopedia/tanks/?application_id=".$key."&fields=contour_image");
	$array = json_decode($contents, true);
	$tanks = $array['data'];
	$tank = $tanks[$tankID];
	return $tank['contour_image'];
}

//Returns the player's nickname, clanID, pr, battles, winrate, and whether or not they are in a clan given the playerID
//This is where most of the important information is pulled from wargaming's database
function getPlayerInfo($playerID){
	$key = getWGAPIkey();
	$contents = file_get_contents("http://api.worldoftanks.com/wot/account/info/?application_id=".$key."&account_id=" . $playerID);
	$array = json_decode($contents, true);
	$data = $array['data'];
	$data = $data[$playerID];
	
	$nickname = $data['nickname'];
	$clanID = $data['clan_id'];
	$personalrating = $data['global_rating'];
	$battles = $data['statistics'];
	$battles = $battles['all'];
	$battles = $battles['battles'];
	$wins = $data['statistics'];
	$wins = $wins['all'];
	$wins = $wins['wins'];
	$inclan = isset($data['clan_id']);
	
	$winrate = getWinRate($battles, $wins);
	
	return array((string)$nickname,
				(string)$clanID,
				$personalrating,
				(string)$battles,
				(string)$winrate,
				$inclan);
}

//Returns the player's clan tag and ranking given the playerID and clanID
//Assumes that the player is in the given clan
function getClanInfo($playerID, $clanID){
	$key = getWGAPIkey();
	$contents = file_get_contents("http://api.worldoftanks.com/wot/clan/info/?application_id=".$key."&clan_id=".$clanID);
	$array = json_decode($contents, true);
	$data = $array['data'];
	$data = $data[$clanID];
	
	$tag = $data['abbreviation'];
	
	$members = $data['members'];
	foreach ($members as &$player){
		if ($player['account_id']==$playerID){
			$rank = cleanRank($player['role']);
		}
	}
	return array((string)$tag,(string)$rank);
	
}

//Returns the given clanID's clan tag url
function getClanEmblem($clanID){
	$key = getWGAPIkey();
	$contents = file_get_contents("http://api.worldoftanks.com/wot/clan/info/?application_id=".$key."&clan_id=".$clanID . "&fields=emblems.large");
	$array = json_decode($contents, true);
	$data = $array['data'];
	$data = $data[$clanID];
	$data = $data['emblems'];
	$emblem = $data['large'];
	$emblem = str_replace("\\", "", $emblem);
	return (string)$emblem;
}

//Returns whether or not a player is currently in a clan
function isInClan($playerID){
	$clan = getClanID($playerID);
	if (strrpos($clan, "}") > -1) {return false;}
	else {return true;}
}

//Cleans the clan ranks to the more common names
function cleanRank($rank)
{
	if($rank == "commander"){
		$rank = "Field\nCommander";
	}
	else if($rank == "vice_leader"){
		$rank = "Deputy\nCommander";
	}
	else if($rank == "leader"){
		$rank = "Commander";
	}
	else if($rank == "personnel_officer"){
		$rank = "Personnel\nOfficer";
	}
	else if($rank == "diplomat"){
		$rank = "Diplomat";
	}
	else if($rank == "treasurer"){
		$rank = "Treasurer";
	}
	else if($rank == "recruiter"){
		$rank = "Recruiter";
	}
	else if($rank == "junior_officer"){
		$rank = "Junior\nOfficer";
	}
	else if($rank == "private"){
		$rank = "Soldier";
	}
	else if($rank == "recruit"){
		$rank = "Recruit";
	}
	else if($rank == "reservist"){
		$rank = "Reservist";
	}
	if(strrpos($rank, "error") !== false){
		$rank = "";
	}
	return (string)$rank;

}

//Divides wins by battles played, rounds to nearest int, and adds a % sign
function getWinRate($battles, $wins){
	//statistics.all.wins
	$winrate = round((float)$wins/(float)$battles, 2)*100;
	return (string)$winrate.'%';
}

//Checks whether or not a given login token is valid
function validLogin($playerID, $token){
	$key = getWGAPIkey();
	$contents = file_get_contents("http://api.worldoftanks.com/wot/account/info/?application_id=".$key."&account_id=".$playerID."&access_token=".$token);
	if (strrpos($contents, "INVALID_ACCESS_TOKEN") > -1) {return 0;}
	else {return 1;}
}

//Gets the wotlabs statistics of a player given their name, assumes the name is correct
function getWotlabs($playername){
	$key = getWotlabskey();
	$wotlabsapi = file_get_contents("http://wotlabs.net/api.php?server=na&player=".$playername."&key=".$key);
	//if ($stats == false){ return false;}
	
	$array = json_decode($wotlabsapi, true);
	$stats = $array["statistics"];
	$tier = (string)round($stats["average_tier"],2); 
	$wn8 = (string)$stats["wn_rating"];
	$rwr = (string)round($stats["recent_win_rate"]) . "%";
	$rwn8 = (string)$stats["recent_wn_rating"];
	$helpfulstats = array(	$tier,
							$wn8,
							$rwn8,
							$rwr);
	return $helpfulstats;
}

//Gets the recent exp/game of a given playerID
function getExpRecent($playerID){
	$key = getWGAPIkey();
	$stats = file_get_contents("https://api.worldoftanks.com/wot/ratings/accounts/?application_id=".$key."&type=28&account_id=" . $playerID);
	$array = json_decode($stats, true);
	$data = $array["data"];
	$data = $data[$playerID];
	$exp = $data["xp_avg"];
	$rank = (int)$exp["rank"];
	$value = (int)$exp["value"];
	$returned = array(
		$rank,
		$value);	
	return $returned;
}

//Gets the token validation link
function getTokenLink(){
	$key = getWGAPIkey();
	$info = file_get_contents("https://api.worldoftanks.com/wot/auth/login/?application_id=".$key."&nofollow=1&redirect_uri=http://www.tioga.moe/signature/signature.php");
	$array = json_decode($info, true);
	$data = $array["data"];
	$location = $data["location"];
	return $location;
}

//No longer used - used to return whether or not wotlabs.net was down
function wotlabsDown(){
	$info = file_get_contents('http://www.downforeveryoneorjustme.com/http://wotlabs.net/na/player/tioga060');
	if (strrpos($info, 'not just you')>-1){
		return true;
	}
	else {return false;}
}

//Copies all tank icons to the server, only used manually every game update
function copyTankIcons(){
	$key = getWGAPIkey();
	$contents = file_get_contents("https://api.worldoftanks.com/wot/encyclopedia/tanks/?application_id=".$key."&fields=contour_image,tank_id");
	$array = json_decode($contents, true);
	$tanks = $array['data'];
	foreach ($tanks as &$tank){
		$id = $tank['tank_id'];
		$image =  $tank['contour_image'];
		
		copy($image, "./tanks/".$id.".png");
	}
}

//Gets the database password and name from the settings file
function getDbInfo(){
	$dbinfo = file_get_contents("./settings.conf");
	$dbinfo = json_decode($dbinfo, true);
	$dbname = (string)$dbinfo["dbname"];
	$dbpass = (string)$dbinfo["dbpass"];
	return(array($dbname, $dbpass));
}

//gets my wargaming API key
function getWGAPIkey(){
	$dbinfo = file_get_contents("./settings.conf");
	$dbinfo = json_decode($dbinfo, true);
	$wgapikey = (string)$dbinfo["wgapikey"];
	return($wgapikey);
}

//gets my wotlabs api key
function getWotlabskey(){
	$dbinfo = file_get_contents("./settings.conf");
	$dbinfo = json_decode($dbinfo, true);
	$wotlabskey = (string)$dbinfo["wotlabskey"];
	return($wotlabskey);
}


?>
</body>
</html>