<?php
include('GIFEncoder.class.php');



//Takes the filename (string), number of frames (int), and delay (int) as inputs
//Creates an animated gif out of the png files previously created with the designated filename
function createGif($name, $framesnum, $delay){
for ($x=1; $x<=$framesnum; $x++) {
			$filename = './sigs/gifs/' . strtolower($name) . (string)$x. '.png';//png file directory
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


//Takes the filename and playerID as inputs
//Decomposes the gif into 20 or less png frames and saves each of the png frames as files
//Returns the final number of frames
function analyzeGif($path, $playerID){
	require_once('GIFFrameExtractor.php');
	if(GIFFrameExtractor\GifFrameExtractor::isAnimatedGif($path))
	{
	$gfe = new GIFFrameExtractor\GifFrameExtractor();
	$gfe->extract($path);
	$frames = $gfe->getFrameNumber();
	$frameImages =  $gfe->getFrames();
	
	echo imagecolorat($framesImages[0], 5,5); //Used for debugging
	
	$increment = ceil((float)$frames/20);	//Find out how much the for loop as to increment by
											//To keep the number of frames <20
	$dimensions = $gfe->getFrameDimensions();
	
	$index = 0;	
	for ($x=0; $x<$frames; $x = $x + $increment) {//For loop will run 20 or less times
		echo $x;	//debugging
		$index++;
		$img = $frameImages[$x];
		$img = $img['image'];
		$imagenametemp = './custom/gifs/t'.$playerID.$index.'.png';
		imagealphablending( $img, true );
		imagesavealpha( $img, true );
		imagepng($img, $imagenametemp);//Save the image as a png temporarily
		$size = getimagesize($imagenametemp);
		$w = $size[0];
		$h = $size[1];
		
		if($index==1){
			$image = imagecreatefrompng("./custom/blacksig.png");
		}
		else{	//Write over the old png, this will make transparent gifs write correctly
			$tempnum = $index-1;
			$image = imagecreatefrompng('./custom/gifs/'.$playerID.$tempnum.'.png');
		}
		$imagename = './custom/gifs/'.$playerID.$index.'.png';
		$img = imagecreatefrompng($imagenametemp);
		imagealphablending( $img, true );
		imagesavealpha( $img, true );
		
		imagecopyresized($image, $img, 0,0,0,0,468,100,$w,$h);
		imagepng($image, $imagename);
		
		imagedestroy($img);
		imagedestroy($image);
		unlink($imagenametemp);//delete the temporary png
	}
	return $index;		//Return the number of frames
	}
	return 0;
}

?>