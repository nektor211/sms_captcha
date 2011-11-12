<?php
/*foreach ($_POST as $key=>$value) {
  if(strstr($key, "____")){
    $txt=substr_replace($key, ".txt", -4);
    $file=fopen("samples/".$txt,"w");
    if(strlen($value)==7){
	    $v=str_split($value);
	    $value=trim(implode(' ',$v));
    }
    fwrite($file, $value);
    fclose($file);
  }
}*/

echo '<form action="tag.php" method="post">';
$d = dir("splits");

$all;

while (false !== ($entry = $d->read())) {
  if(strstr($entry, ".png")){
    #echo $entry."\n";
    $txt=substr(substr_replace($entry, "txt", -5),1);
    $pod=substr_replace($entry, "____", -4);
    $entrya=explode('.',$entry);
    $i=$entrya[1];
    if((!isset($all[$txt])) && file_exists("samples/$txt")){
	  	
			$lines=file("samples/$txt");
			echo '<br>';
	  	
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
	#echo "<input type=\"text\" name=\"$pod\" value=\"".$all[$txt][$i]."\"><br>";
  }
}
//echo "<input type=\"submit\"></form>";
$d->close();
?>
