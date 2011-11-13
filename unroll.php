<?php 

	$featuredir = "splits/c";
	$tagdir = "splits/txt";
	$outdir = "splits";
	
	if (isset($argv[1])) $featuredir = $argv[1];
	if (isset($argv[2])) $tagdir = $argv[2];
	if (isset($argv[3])) $outdir = $argv[3];
	


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

	$myFILEoutT = fopen("$tagdir/tag.txt", "w"); 
	$d = dir("$tagdir");
	while(false != ($entry = $d->read())) {
		if ((strstr($entry, "s")) && (strstr($entry, ".txt"))){
			$myFILEin = fopen("$tagdir/$entry", "r");

			if (!feof($myFILEin)) $i = fgetc($myFILEin);
			echo "writing tag $i from file $entry";
			fwrite($myFILEoutT, "$i\n");
			fclose($myFILEin);		
		}	
	}



	fclose($myFILEout);
	fclose($myFILEoutT);


	$fileF = fopen("$featuredir/features.txt", "r");
	$fileT = fopen("$tagdir/tag.txt", "r");
	$FILEout = fopen("$outdir/data.txt", "w");

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




?>
