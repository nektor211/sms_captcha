<?php
  $img=imagecreatefromjpeg("captcha.jpg");
  $sx=imagesx($img);
  $sy=imagesy($img);
  $bmp=imagecreate($sx,$sy);
  $black=imagecolorallocate($bmp,0,0,0);
  $white=imagecolorallocate($bmp,255,255,255);
  echo "$sx $sy \n";
  
  for($x=0;$x<$sx;++$x){
	  $line="";
	  $count=0;
    for($y=0;$y<$sy;++$y){

	    $rgb = imagecolorat($img, $x, $y);
	    $r = ($rgb >> 16) & 0xFF;
	    $g = ($rgb >> 8) & 0xFF;
	    $b = $rgb & 0xFF;
	    if($r>150 && $g<100 && $b>100 && $b<200){
		    $line.= 1;
		    $count++;
	    }else
              $line.= 0;
    }
	  if($count>0){
		  echo "$line\n";
	  }else echo "\n";
  }
  imagejpeg($bmp,"out.jpg");

?>
