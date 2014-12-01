<?php
include('GIFEncoder.class.php');

//header ('Content-type:image/gif');


function createGif($name, $framesnum, $delay){
for ($x=1; $x<=$framesnum; $x++) {
			$filename = './sigs/gifs/' . strtolower($name) . (string)$x. '.png';
			$image = imagecreatefrompng($filename);
			ob_start();
			imagegif($image);
			$frames[]=ob_get_contents();
			$framed[]=$delay; // Delay in the animation.
			ob_end_clean();
			
	} 

		$gif = new GIFEncoder($frames,$framed,0,$framesnum,0,0,0,'bin');	
		$outname = './sigs/' .strtolower($name) . '.gif';
	
		$fp = fopen($outname, 'w');
		fwrite($fp, $gif->GetAnimation());
		fclose($fp);
}

function analyzeGif($path, $playerID){
	require_once('GIFFrameExtractor.php');
	if(GIFFrameExtractor\GifFrameExtractor::isAnimatedGif($path))
	{
	$gfe = new GIFFrameExtractor\GifFrameExtractor();
	$gfe->extract($path);
	$frames = $gfe->getFrameNumber();
	$frameImages =  $gfe->getFrames();
	echo imagecolorat($framesImages[0], 5,5);
	$increment = ceil((float)$frames/20);
	$dimensions = $gfe->getFrameDimensions();

	$index = 0;
	
	
	for ($x=0; $x<$frames; $x = $x + $increment) {
		echo $x;
		$index++;
		$img = $frameImages[$x];
		$img = $img['image'];
		$imagenametemp = './custom/gifs/t'.$playerID.$index.'.png';
		imagepng($img, $imagenametemp);
		$size = getimagesize($imagenametemp);
		$w = $size[0];
		$h = $size[1];
		
		if($index==1){
			$image = imagecreatefrompng("./custom/blacksig.png");
		}
		else{
			$tempnum = $index-1;
			$image = imagecreatefrompng('./custom/gifs/'.$playerID.$tempnum.'.png');
		}
		$imagename = './custom/gifs/'.$playerID.$index.'.png';
		$img = imagecreatefrompng($imagenametemp);
		imagecopyresized($image, $img, 0,0,0,0,468,100,$w,$h);
		imagepng($image, $imagename);
		imagedestroy($img);
		imagedestroy($image);
		unlink($imagenametemp);
	}
	return $index;
	}
	return 0;
}

?>