<?php
foreach ($_POST as $key=>$value) {
  if(strstr($key, "____")){
    $txt=substr_replace($key, ".txt", -4);
    if(strlen($value)==7){
	    $v=preg_split('//',$value);
	    $value=trim(implode(' ',$v));
    }
    $file=fopen("$samples/".$txt,"w");
    fwrite($file, $value);
    fclose($file);
  }
}
$od=(int)$_GET['od'];
$do=(int)$_GET['do'];

echo '<style type="text/css">
<!--
  .i {width: 400px; height: 100px;}
//-->
</style>
<form action="tag.php" method="post">';

$d = dir("samples");

while (false !== ($entry = $d->read())) {
  if(strstr($entry, ".jpg")){
    #echo $entry."\n";
    $txt=substr_replace($entry, "txt", -3);
    $png=substr_replace($entry, "png", -3);
    $pod=substr_replace($entry, "____", -4);
    $id=(int)$entry;
    if(($do!=0||$od!=0) &&( $id>$do || $id<$od))continue;
		//echo $txt."\n";
    if(file_exists("samples/$txt")){
	  	
			$lines=file("samples/$txt");
	  	
			if (isset ($lines[0])) {
				$data=chop($lines[0]);
			}
			else {
				$data = "";
			}
		}
		else{
	  	$data="";
		}
	//echo $data."\n";
	echo "<image class='i' src=\"samples/$entry\" /> <image class='i' src=\"tmp/$png\" /><br>\n";
	echo "<input type=\"text\" name=\"$pod\" value=\"$data\"><br>";
  }
}echo "<input type=\"submit\"></form>";
$d->close();
?>
