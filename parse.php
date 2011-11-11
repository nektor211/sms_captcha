<?php
	$img=imagecreatefromjpeg("captcha.jpg");
	$sx=imagesx($img);
	$sy=imagesy($img);
	$bmp=imagecreate($sx,$sy);
	$black=imagecolorallocate($bmp,0,0,0);
	$white=imagecolorallocate($bmp,255,255,255);
	$spaceflag = 0;
	$spacecount = 0;
	$MINROWS = 15;
	$MAXROWS = 35;
	$rows_since_split = 0;

	
	
	// echo "$sx $sy \n";
  
	for($x=0;$x<$sx;++$x){	//x = row
		$line="";
		$count=0;
	  	
			for($y=0;$y<$sy;++$y){	//y = col

				$rgb = imagecolorat($img, $x, $y);
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;
				if($r>150 && $g<100 && $b>100 && $b<200){
					$line.= "1";
					$count++;
				}
				else {
					$line.= "0";
				}
		}
		
		echo "$x $line \n" ;
		$linemap[$x] = $line;
		$countmap[$x] = $count;
		//echo "$count \n";
	  
	}
	imagejpeg($bmp,"out.jpg");
  $myFILE = fopen("tmp/captcha0.txt", "w");
	$splitcount = 0;
	$ROWS = count($countmap);
	for ($id = 0; $id < $ROWS; $id++) {
		$count = $countmap[$id];
		echo "$id . $count \n";

		//if($spaceflag == 0) {
			$rows_since_split++;
		//}
		if($count == 0){				//zero row
			if($spaceflag == 0) {	//new space
				$spaceflag = 1;
				//$spacecount++;
				continue;
			}
			else {								//continuing space
				continue;
			}
		}
		else {										//nonzero row
			if ($spaceflag != 0) {  //end of split series
				$spaceflag = 0;
				if (($rows_since_split >= $MINROWS)) {
					fclose($myFILE);
					$rows_since_split = 0;
					$splitcount++;
					$myFILE = fopen("tmp/captcha$splitcount.txt", "w");
					echo "split at $id long \n";
					
				}
			}

			//spaceflag == 0, test for too long since split
			else if($rows_since_split > $MAXROWS) { //too long no split
				$min = 100;
				$splitRowID = 0;
				for ($i = $rows_since_split - $MINROWS; $i > 0; $i--){
					$tCount = $countmap[$id - $i];
					if ($tCount <= $min) {
						$min = $tCount;
						$splitRowID = $id - $i;
					}
				}
					fclose($myFILE);
					$rows_since_split = 0;
					$splitcount++;
					$myFILE = fopen("tmp/captcha$splitcount.txt", "w");
					echo "split at $id \n";
					$id = $splitRowID;
			}
			else {
				fwrite($myFILE, $linemap[$id]."\n" );
			}
		
		}



	}
?>
