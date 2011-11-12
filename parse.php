<?php
	$ImgID = rand(1, 84);
	
	
	$d = dir("samples");
	$i = 0;
	while (false !== ($entry = $d->read())) {
	if (strstr($entry, ".jpg")){
		$IID = str_replace(".jpg", "", $entry);
		$filelist[$i]	= $IID;

		echo "$IID\n";
		$i++;
	}

	}$d->close();

 $i = 0;
  
	 

	
	foreach($filelist as $id => $filename) {
	
	echo "$filename\n";
	$img=imagecreatefromjpeg("samples/"."$filename".".jpg");
	$sx=imagesx($img);
	$sy=imagesy($img);
	$bmp=imagecreate($sx,$sy);
	$black=imagecolorallocate($bmp,0,0,0);
	$white=imagecolorallocate($bmp,255,255,255);
	imagefilledrectangle($bmp, 0, 0, $sx, $sy, $black);
	$spaceflag = 0;
	$spacecount = 0;
	$MINROWS = 13;
	$MAXROWS = 30;
	$rows_since_split = 0;

	
	
	// echo "$sx $sy \n";
  
	for($x=0;$x<$sx;++$x){	//x = row
		$line="";
		$lineraw = "";
		$count=0;
	  	
			for($y=0;$y<$sy;++$y){	//y = col

				$rgb = imagecolorat($img, $x, $y);
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;
				if($r>100 && $g<100 && $g<220 && $b>100 && $b<200){
					$line.= "r";
					imagesetpixel($bmp, $x, $y, $white);
					$count++;
				}
				else if ($r<30 && $g<30 && $b<30) {
					$line.= "b";
				}


				else {
					$line.= "w";
				}
		}
		
		//@@echo "$x $line \n" ;
		$linemap[$x] = $line;
		$countmap[$x] = $count;
		//echo "$count \n";
	  
	}
	imagepng($bmp,"out.png");
	$splitcount = 0;
	$ROWS = count($countmap);
	for ($id = 0; $id < $ROWS; $id++) {
		$count = $countmap[$id];
		//@@echo "$id . $count \n";

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
					//@@echo "split at $id long \n";
					
				}
			}
			//spaceflag == 0, test for too long since split
			else if($rows_since_split > $MAXROWS) { //too long no split
				//@@echo "hit ceil at $id \n" ;
				$min = 100;
				$splitRowID = 0;
				for ($i = 0; $i < $rows_since_split - $MINROWS; $i++){
					$tCount = $countmap[($id - $i)];
					if ($tCount <= $min) {
						$min = $tCount;
						$splitRowID = $id - $i;
						//@@echo "min at $splitRowID : $min \n";
					}
				}
				$rows_since_split = 0;
				$split[$splitcount] = $splitRowID;
				$splitcount++;
				$id = $splitRowID;
				//@@echo "split at $id \n";
			}
		}
	}
	$lastsplit = 0;
	for ($id = $ROWS - 1; $count == 0; $id--){
		$count = $countmap[$id];
		$lastsplit = $id;	
	}
	
	$split[$splitcount] = $lastsplit;
	$myFILE = fopen("splits/c"."$filename".".0.txt", "w");
	$splitID = 0;
	$rowID = 0;
	if (isset($out_image)) {
		unset($out_image);
	}
	foreach ($linemap as $id => $line) {
		$count = $countmap[$id];
		if ($count > 0){
			//$splitID["-1"] = 0;
			if ($splitID == 0) {
				$rowID = $id;
			}
			else {
				$rowID = $id - $split[$splitID-1];
			}
			$out_image[$splitID][$rowID] = $line;
			fwrite($myFILE, "$id ".$line."\n" );
		}
		
		if ( ($id < $lastsplit) && ($id == $split[$splitID])){
			$splitID++;
			fclose($myFILE);
			$myFILE = fopen("splits/c"."$filename".".$splitID.txt", "w");
		}
	}
	fclose($myFILE);
	
	foreach($out_image as $imageID => $imagemap) {
		$IMGW = 30;
		$IMGH = 50;
		$dif = 0;
		$realIMGRows = count($imagemap);
		if ($realIMGRows < $IMGW) {
			$dif = floor(($IMGW - $realIMGRows)/2);		
		}
		$out_image = imagecreatetruecolor($IMGW, $IMGH);
		$black = imagecolorallocate($out_image, 0, 0, 30);
		$white = imagecolorallocate($out_image, 255, 255, 255);

		imagefilledrectangle($out_image, 0, 0, $IMGW, $IMGH, $black);
		$wstart = $dif;
		foreach($imagemap as $id => $line) {

			for ($hpos = 0; $hpos < strlen($line); $hpos++){
			//foreach($line as $wpos => $char){
				$ch = $line[$hpos];
				if ($ch == "r"){
					imagesetpixel($out_image, $id+$wstart, $hpos, $white);				
				}			
			}		
		}

		imagepng($out_image, "splits/s"."$filename".".$imageID.png");


		foreach($imagemap as $id => $line) {
			echo "$id $line \n";
		}
		echo "\n";
	}
	}


?>
