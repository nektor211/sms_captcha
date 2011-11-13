<?php 
	
	$featuredir = "splits/c"	


	if (isset($1)) $featuredir = $1;
	$myFILEout = fopen("$featuredir/features.txt", "w");
	
	$d = dir("$featuredir");

	while(false != ($entry = $d->read()) ) {
		
	 if ((strstr($entry, "c")) && (strstr($entry, ".txt"))) {
			$myFILEin = fopen("$featuredir/$entry", "r");
			while (!feof($myFILEin)) {
				$c = fgetc($myFILEin);
				if($c == 'r') {
					fwrite($myFILEout, " 1");
				}
				else if(($c == 'w') || ($c == 'b') ) {
					fwrite($myFILEout, " 0");
				}
			}
			fwrite($myFILEout, "\n");

			fclose($myFILEin);
		}
	}
	//fclose($myFILEout);
/*
	$myFILEoutT = fopen("splits/tags.txt", "w"); 
	$d = dir("splits/txt");
	while(false != ($entry = $d->read())) {
		if ((strstr($entry, "s")) && (strstr($entry, ".txt"))){
			$myFILEin = fopen("splits/txt/$entry", "r");
			if (!feof($myFILEin)) $i = fgetc($myFILEin);
			fwrite($myFILEoutT, "$i\n");
			fclose($myFILEin);		
		}	
	}



	fclose($myFILEout);
	fclose($myFILEoutT);


	$fileF = fopen("splits/features.txt", "r");
	$fileT = fopen("splits/tags.txt", "r");
	$FILEout = fopen("data.txt", "w");

	while (!feof($fileT) && !feof($fileF)) {
		$line = trim(fgets($fileF));
		$tag = trim(fgets($fileT));
		if ($tag != 0) {
			fwrite($FILEout, $line." ".$tag."\n");
		}
	}
	fclose($fileF);
	fclose($fileT);
	fclose($FILEout);
*/

fclose($myFILEout);



?>
