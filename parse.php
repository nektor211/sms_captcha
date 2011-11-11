<?php
	$ImgID = rand(1, 84);
	$img=imagecreatefromjpeg("samples/$ImgID.jpg");
	echo "$ImgID";
	$sx=imagesx($img);
	$sy=imagesy($img);
	$bmp=imagecreate($sx,$sy);
	$black=imagecolorallocate($bmp,0,0,0);
	$white=imagecolorallocate($bmp,255,255,255);
	$spaceflag = 0;
	$spacecount = 0;
	$MINROWS = 13;
	$MAXROWS = 30;
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
				if($r>100 && $g<100 && $g<220 && $b>100 && $b<200){
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
					$rows_since_split = 0;
					$split[$splitcount] = $id;
					$splitcount++;
					echo "split at $id long \n";
					
				}
			}
			//spaceflag == 0, test for too long since split
			else if($rows_since_split > $MAXROWS) { //too long no split
				echo "hit ceil at $id \n" ;
				$min = 100;
				$splitRowID = 0;
				for ($i = 0; $i < $rows_since_split - $MINROWS; $i++){
					$tCount = $countmap[($id - $i)];
					if ($tCount <= $min) {
						$min = $tCount;
						$splitRowID = $id - $i;
						echo "min at $splitRowID : $min \n";
					}
				}
				$rows_since_split = 0;
				$split[$splitcount] = $splitRowID;
				$splitcount++;
				$id = $splitRowID;
				echo "split at $id \n";
			}
		}
	}
	$lastsplit = 0;
	for ($id = $ROWS - 1; $count == 0; $id--){
		$count = $countmap[$id];
		$lastsplit = $id;	
	}
	$split[$splitcount] = $lastsplit;
	$myFILE = fopen("tmp/captcha0.txt", "w");
	$splitID = 0;
	foreach ($linemap as $id => $line) {
		fwrite($myFILE, "$id ".$line."\n" );
		if ( ($id < $lastsplit) && ($id == $split[$splitID])){
			$splitID++;
			fclose($myFILE);
			$myFILE = fopen("tmp/captcha$splitID.txt", "w");
		}
	}
	fclose($myFILE);

?>
