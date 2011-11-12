<?php
error_reporting(E_ALL);
foreach ($_POST as $key=>$value) {
  if(strstr($key, "_")){
    $txt=substr_replace($key, ".txt", -1);
    $file="splits/txt/".$txt;
    if(!file_exists($file)){
      $fh=fopen($file,"w");
      fwrite($fh, $value);
      fclose($fh);
		}else{
		  $f=file($file);
		  $f[0].='';
		  echo $value.$f[0]."<br>"; 
		  if(trim($f[0])!=trim($value)){
        $fh=fopen($file,"w");
        fwrite($fh, $value);
        fclose($fh);
			}
		}
  }
}

echo '<form action="debug.php" method="post">';
$d = dir("splits");

//$all;
$lasti=0;

while (false !== ($entry = $d->read())) {
  if(strstr($entry, ".png")){
    #echo $entry."\n";
    $txt=substr(substr_replace($entry, "txt", -5),1);
    echo $txt_spl="txt/".substr_replace($entry, "txt", -3);
    $pod=substr_replace($entry, "_", -4);
    $entrya=explode('.',$entry);
    
    $i=$entrya[1];
    if($lasti>$i) echo "<br>";
    $lasti=$i;
    if(file_exists("splits/$txt_spl")){
	  	$lines=file("splits/$txt_spl");
	  	
	  	if (isset ($lines[0])) {
				$all[$txt][$i]=chop($lines[0]);
			}
			else {
				$all[$txt][$i].= "";
			}
	  }elseif((!isset($all[$txt])) && file_exists("samples/$txt")){
	  	
			$lines=file("samples/$txt");
	  	
			if (isset ($lines[0])) {
				$all[$txt]=explode(' ',chop($lines[0]));
				
			}
			else {
				$all[$txt][].= "";
			}
		}
		else{
	  	$all[$txt][].="";
		}
	//echo $data."\n";
	echo "<image src=\"splits/$entry\" />".$all[$txt][$i]."|\n";
	echo "<input type=\"text\" name=\"$pod\" value=\"".$all[$txt][$i]."\" size=1 />";
  }
}
echo "<input type=\"submit\"></form>";
$d->close();
?> 
