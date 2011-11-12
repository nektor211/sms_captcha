<?php 
	
	
	$myFILEout = fopen("splits/data.txt", "a");
	
	$d = dir("splits/c");
	
	while(false != ($entry = $d->read()) ) {
		
	 if ((strstr($entry, "c")) && (strstr($entry, ".txt"))) {
			$myFILEin = fopen("splits/c/$entry", "r");
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
	fclose($myFILEout);

?>
