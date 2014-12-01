<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Signature Generator</title>
<link href="../styles/main.css" rel="stylesheet" type="text/css">
<link href="styles/signature.css" rel="stylesheet" type="text/css">


</head>

<body>
<div id="wrapper">

<div id="top">
    <a href="../index.html" id="bannerlink"><img src="../images/Banner.png" alt="" width="893" height="183" id="Banner" title="TiogaBanner" border="0"/></a>
	<nav id="Navigation">
   	  <ul id="Mainnav">
      	<li id="mainnav1"><a href="../blog/">Blog</a></li>
        <li id="mainnav2"><a href="http://www.tioga.moe/blog/?cat=5">Platoon Strategy</a></li>
        <li id="mainnav3"><a href="../calling/introduction.html">Calling Resources</a></li>
        <li id="currentpg"><a href=http://www.tioga.moe/signature/>Signatures</a></li>
      </ul>
    </nav>
</div>

<article id="main">
<div id='aside'>
<h2>How to Use:</h2>
<p>If you want to upload a custom picture, you have to log in with your wargaming ID. This is the best way I could think of to make it so other people couldn't change the background picture on your signature. It says that it can access extra information, but the only thing I use it for is authentication. If you don't believe me that's fine but you won't be able to set your own picture without logging in.</p>

<h3>To Set your own picture:</h3>
<ul>
  <li>Upload your image to imgur</li>
  <li>Copy the direct link to Picture URL box</li>
  <li>For gifs: adjust the speed slider as desired</li>
  <li>Do not use transparent gifs, if your gif looks terrible it probably has transparent layers</li>
  <li>Leave the url field blank to keep the same image you had before</li>
</ul>

<h3>What is a direct link?</h3>
<ul>
<li>http://i.imgur.com/FLxAk08.jpg is a direct link</li>
<li>http://imgur.com/FLxAk08 is not</li>
</ul>

<h3>How to set up your picture</h3>
<p>The picture dimensions are 468x100. Images and gifs will resize, but you will have a better looking product if you resize it yourself.
The image below is a transparent template image of the correct dimensions and a black box where the stats will go. Those of you using Photoshop/Gimp should use this as a template. Any left over transparency turns black.</p>
<img src='http://www.tioga.moe/signature/template.png' border="5">

<h3>Other things to note</h3>
<ul>
  <li>Gifs are reduced to 20 frames or less using an algorithm I developed. If your gif is choppy, manually reduce it yourself and upload that gif.</li>
  <li>If the image the generator gives you is broken or missing, you entered a name that does not exist, I do not have proper error handling for this yet</li>
  <li>If the image does not update after your cache clears, you are trying to update the image of somebody who has logged in with their wargaming ID. Once you have logged in once, you cannot edit your signature unless you are logged in.</li>
</ul>
</div>

<h2 id='login'></h2>
<a id="loginbutton" >Log In</a>
<h2 id='coolstuff'>Create Signature</h2>
<form id="nameform">
Name:</br>
<input name="namefield" type="text" id="namefield"></br>
Text Color:</br>
<input class="color" id="namepick"></br>
Background Color:</br>
<input class="color" id="backpick"></br>
Picture URL:</br>
<input name="sigurlfield" type="text" id="sigurlfield">
</br>
<INPUT TYPE=CHECKBOX NAME="games" id="games">Show games played<BR>
<INPUT TYPE=CHECKBOX NAME="clan" id="clan">Show clan information<BR>
<INPUT TYPE=CHECKBOX NAME="toptanks" id="toptanks">Show top 3 tanks<BR>
Gif Speed</br>
Faster---------Slower</br>
<input type="range" name="points" id="points" value="10" min="1" max="20">
<div id="errors"></div></br>
</form>
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript" src="jscolor/jscolor.js"></script>
<input id="getSigButton" type="button" value="Get Signature"/>
<p id='down'></p>



<p id="playerload"></p>
<p id="nameload"></p>
<p id="backload"></p>

<?php
include 'WGAPI.php';
$down = false;
$tokenurl = getTokenLink();
if(isset($_GET['account_id'])&&isset($_GET['access_token'])) {
	$verified = 0;
	$playerID = $_GET['account_id'];
	$token = $_GET['access_token'];
	$nickname = getRealName($playerID);
	$verified = validLogin($playerID,$token);
}
?>

<script>
var tokenurl = "<?php echo $tokenurl; ?>";
document.getElementById("loginbutton").href = tokenurl;

var urlstart = "http://www.tioga.moe/signature/sigs/";
var urlend = ".png";
var urlend2 = ".gif";


function hexToRgb(hex) {
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return parseInt(result[1], 16).toString() + "," + parseInt(result[2], 16).toString() + "," + parseInt(result[3], 16).toString();
}

function getSettings(){
	var toptanksval = document.getElementById('toptanks').checked;
	var gamesval = document.getElementById('games').checked;
	var clanval = document.getElementById('clan').checked;
	var returnarray = {};
	returnarray['toptanks'] = toptanksval;
	returnarray['games'] = gamesval;
	returnarray['clan'] = clanval;
	return JSON.stringify(returnarray);	
}

function checkImgur(x) {
	x = x.toString();
	var errortext = '';
	var returned = false;
    if (x == null || x == "" || x == "none") {
        errortext = 'No Image Specified';
		errortext = errortext.fontcolor('red');
		returned = false;
    }
	else if (x.indexOf("http://i.imgur.com")<0 && x.indexOf("https://i.imgur.com")<0) {
        errortext = 'Not a direct imgur link';
		errortext = errortext.fontcolor('red');
		returned =  false;
    }
	else if (x.indexOf(".jpg") <0 && x.indexOf('.png') <0 && x.indexOf('.gif') <0){
		errortext = 'Not a .png or .jpg';
		errortext = errortext.fontcolor('red');
		returned = false;
	}
	else{
		errortext = "Image Verified";
		errortext = errortext.fontcolor('green');
		returned = true;
	}
	document.getElementById('errors').innerHTML = errortext;
	return returned;
}

function getSig()
{
document.getElementById('sigimg').src = "http://www.tioga.moe/signature/sigload.gif";
var nc = "#" + (document.getElementById('namepick').value).toString();
var bc = "#" + (document.getElementById('backpick').value).toString();
var delay = parseInt(document.getElementById('points').value);
var name = document.getElementById('namefield').value;
var sigurl = document.getElementById('sigurlfield').value;
if(!checkImgur(sigurl)){
	sigurl = 'none';
}
if(sigurl.indexOf('.gif')>0){
	urlend = ".gif";
	urlend2 = ".png";
}

var settings = getSettings();

var url =  '' + urlstart + name.toLowerCase() + urlend;
var url2 =  '' + urlstart + name.toLowerCase() + urlend2;
var namecolor = hexToRgb(nc);
var backcolor = hexToRgb(bc);
var verified = "<?php echo $verified; ?>";

$.ajax(
	   {
		   url: 'update.php',
		   data: {'name': name, 'namecolor': namecolor, 'backcolor': backcolor, 'verified': verified, 'sigurl': sigurl, 'delay': delay, 'settings':settings},
		   type: 'post',
		   success: function(output) 
		   {
				document.getElementById('playerload').innerHTML = "Information loaded for player " + name;
				document.getElementById('sigimg').src = url;
				document.getElementById('sigimg2').src = url2;
		   }
		}
	);
			

}
document.getElementById("getSigButton").onclick = getSig;
</script>

<p>If your signature does not change, it is a client side cache issue.</br> It will update when your cache is cleared.</p>

<div id="playerload"></div>
<img id='sigimg' src='http://www.tioga.moe/signature/sigs/roboooo.png'>
<div id="nothing"></div>
</br>
<img id='sigimg2' src=''>



<script>
	document.getElementById('backpick').value = '000000';
	var verified = "<?php echo $verified; ?>";
	var nickname = "<?php echo $nickname; ?>";
	var playerID = "<?php echo $playerID; ?>";
	var down = "<?php echo $down; ?>";
	document.getElementById('toptanks').checked = true;
	document.getElementById('games').checked = true;
	document.getElementById('clan').checked = true;
	//var down = true;
	if (down){
		var downmessage = 'I am performing maintenance. Please try again later.'.fontcolor('red');
		document.getElementById('down').innerHTML = downmessage;
		document.getElementById('getSigButton').disabled = true;
	}
	if(!verified){
		document.getElementById("login").innerHTML = "Not Logged in";
		document.getElementById('sigurlfield').value = "none";
		document.getElementById('sigurlfield').disabled = true;
		document.getElementById('points').disabled = true;
	}
	else
	{
		//document.getElementById("upform").action += nickname;
		document.getElementById("login").innerHTML = "Logged in as " + nickname;
		document.getElementById("loginbutton").innerHTML = "Log Out";
		document.getElementById("loginbutton").href = "http://www.tioga.moe/signature/signature.php";
		document.getElementById('namefield').value = nickname;
		document.getElementById('namefield').disabled = true;
		document.getElementById('sigurlfield').enabled = true;
	}


	
</script>

<h2>Current Features</h2>
<ul>
  <li>Recent and overall stats</li>
  <li>New color: pink - 71% winrate and 3800 WN8</li>
  <li>Total games played</li>
  <li>Average tier</li>
  <li>Clan tag and rank</li>
  <li>Top 3 most played tanks</li>
  <li>10k Personal Rating club sticker</li>
  <li>Signatures update hourly</li>
  <li>Now supports gif generation from url!</li>
</ul>
<h2>Upcoming Features</h2>
<ul>
  <li>Support for the EU server</li>
  <li>More stickers</li>
  <li>Even more customization</li>
  <li>Suggestions from you!</li>
  <li>and much more!</li>

</ul>
</article>
</div>
</body>
</html>