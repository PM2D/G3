<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');
if(!isset($_SESSION['code']) || $USER['jentered']) {
  Header('HTTP/1.1 204 No Content', TRUE, 204);
  exit;
}
$keystring = $keystr = $_SESSION['code'];
$length = strlen($keystring);

// --- CAPTCHA ---

$alphabet = '0123456789abcdefghijklmnopqrstuvwxyz'; # do not change without changing font files!

# CAPTCHA image size (you do not need to change it, whis parameters is optimal)
$width = 90;
$height = 40;

# symbol's vertical fluctuation amplitude divided by 2
$fluctuation_amplitude = 4;

# increase safety by prevention of spaces between symbols
$no_spaces = true;

# CAPTCHA image colors (RGB, 0-255)
$background_color = array(0, 20, 0);
$foreground_color = array(250, 250, 250);

# JPEG quality of CAPTCHA image (bigger is better quality, but larger file size)
$jpeg_quality = 90;

$alphabet_length = strlen($alphabet);

do{
	$font_file = $_SERVER['DOCUMENT_ROOT'].'/ico/_misc/captcha.png';
	$font = imagecreatefrompng($font_file);
	imagealphablending($font, true);
	$fontfile_width = imagesx($font);
	$fontfile_height = imagesy($font)-1;
	$font_metrics = array();
	$symbol = 0;
	$reading_symbol = false;

	// loading font
	for($i=0; $i<$fontfile_width && $symbol<$alphabet_length; $i++){
		$transparent = (imagecolorat($font, $i, 0) >> 24) == 127;
		if(!$reading_symbol && !$transparent){
			$font_metrics[$alphabet{$symbol}] = array('start'=>$i);
			$reading_symbol = true;
			continue;
		}

		if($reading_symbol && $transparent){
			$font_metrics[$alphabet{$symbol}]['end'] = $i;
			$reading_symbol = false;
			$symbol++;
			continue;
		}
	}

	$img=imagecreatetruecolor($width, $height);
	imagealphablending($img, true);
	$white=imagecolorallocate($img, 255, 255, 255);
	$black=imagecolorallocate($img, 0, 0, 0);

	imagefilledrectangle($img, 0, 0, $width-1, $height-1, $white);

	// draw text
	$x = 1;
	for($i=0; $i<$length; $i++){
		$m = $font_metrics[$keystring{$i}];

		$y = mt_rand(-$fluctuation_amplitude, $fluctuation_amplitude)+($height-$fontfile_height)/2+2;

		if($no_spaces){
			$shift = 0;
			if($i>0){
				$shift=10000;
				for($sy=7;$sy<$fontfile_height-20;$sy+=1){
					for($sx=$m['start']-1;$sx<$m['end'];$sx+=1){
		        		$rgb=imagecolorat($font, $sx, $sy);
		        		$opacity=$rgb>>24;
						if($opacity<127){
							$left=$sx-$m['start']+$x;
							$py=$sy+$y;
							if($py>$height) break;
							for($px=min($left,$width-1);$px>$left-12 && $px>=0;$px-=1){
				        		$color=imagecolorat($img, $px, $py) & 0xff;
								if($color+$opacity<190){
									if($shift>$left-$px){
										$shift=$left-$px;
									}
									break;
								}
							}
							break;
						}
					}
				}
				if($shift==10000){
					$shift=mt_rand(4,6);
				}
			}
		}else{
			$shift=1;
		}
		imagecopy($img, $font, $x-$shift, $y, $m['start'], 1, $m['end']-$m['start'], $fontfile_height);
		$x+=$m['end']-$m['start']-$shift;
	}
} while($x>=$width-10); // while not fit in canvas

$center=$x/2;

// credits. To remove, see configuration file
$img2=imagecreatetruecolor($width, $height);
$foreground=imagecolorallocate($img2, $foreground_color[0], $foreground_color[1], $foreground_color[2]);
$background=imagecolorallocate($img2, $background_color[0], $background_color[1], $background_color[2]);
imagefilledrectangle($img2, 0, 0, $width-1, $height-1, $background);		
imagefilledrectangle($img2, 0, $height, $width-1, $height+12, $foreground);

// periods
$rand1=mt_rand(750000,1200000)/10000000;
$rand2=mt_rand(750000,1200000)/10000000;
$rand3=mt_rand(750000,1200000)/10000000;
$rand4=mt_rand(750000,1200000)/10000000;
// phases
$rand5=mt_rand(0,31415926)/10000000;
$rand6=mt_rand(0,31415926)/10000000;
$rand7=mt_rand(0,31415926)/10000000;
$rand8=mt_rand(0,31415926)/10000000;
// amplitudes
$rand9=mt_rand(330,420)/110;
$rand10=mt_rand(330,450)/110;

//wave distortion
for($x=0;$x<$width;$x++){
	for($y=0;$y<$height;$y++){
		$sx=$x+(sin($x*$rand1+$rand5)+sin($y*$rand3+$rand6))*$rand9-$width/2+$center+1;
		$sy=$y+(sin($x*$rand2+$rand7)+sin($y*$rand4+$rand8))*$rand10;
			if($sx<0 || $sy<0 || $sx>=$width-1 || $sy>=$height-1){
			continue;
		}else{
			$color=imagecolorat($img, $sx, $sy) & 0xFF;
			$color_x=imagecolorat($img, $sx+1, $sy) & 0xFF;
			$color_y=imagecolorat($img, $sx, $sy+1) & 0xFF;
			$color_xy=imagecolorat($img, $sx+1, $sy+1) & 0xFF;
		}
		if($color==255 && $color_x==255 && $color_y==255 && $color_xy==255){
			continue;
		}else if($color==0 && $color_x==0 && $color_y==0 && $color_xy==0){
			$newred=$foreground_color[0];
			$newgreen=$foreground_color[1];
			$newblue=$foreground_color[2];
		}else{
			$frsx=$sx-floor($sx);
			$frsy=$sy-floor($sy);
			$frsx1=1-$frsx;
			$frsy1=1-$frsy;
			$newcolor=(
				$color*$frsx1*$frsy1+
				$color_x*$frsx*$frsy1+
				$color_y*$frsx1*$frsy+
				$color_xy*$frsx*$frsy);

			if($newcolor>255) $newcolor=255;
			$newcolor=$newcolor/255;
			$newcolor0=1-$newcolor;
			$newred=$newcolor0*$foreground_color[0]+$newcolor*$background_color[0];
			$newgreen=$newcolor0*$foreground_color[1]+$newcolor*$background_color[1];
			$newblue=$newcolor0*$foreground_color[2]+$newcolor*$background_color[2];
		}

		imagesetpixel($img2, $x, $y, imagecolorallocate($img2, $newred, $newgreen, $newblue));
	}
}

header('Cache-Control: no-cache, no-store, must-revalidate');
if(function_exists('imagejpeg')){
	header('Content-Type: image/jpeg');
	imagejpeg($img2, null, $jpeg_quality);
}elseif(function_exists('imagegif')){
	header('Content-Type: image/gif');
	imagegif($img2);
}elseif(function_exists('imagepng')){
	header('Content-Type: image/x-png');
	imagepng($img2);
}

?>