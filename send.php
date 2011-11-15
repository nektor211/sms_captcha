<?php
  $addr=file("/afs/ms/u/t/tauferp/history/my_sms/addr.txt");
  $n="\n";
  echo'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=windows-1250">
  <title></title>
  </head>
  <body>';
  echo '<form action="send.php" method="post">'.$n;
  echo '<input id="num" type="text" name="num">'.$n;
  echo '<select name="contact" onchange="document.getElementById(\'num\').value=this.value;">'.$n;
  foreach($addr as $key=>$value){
    $data=explode(';', $value);
	  echo '<option value="'.$data[1].'">'.$data[0].'</option>';
	}
	echo '</select>';
  var_dump($addr);
  echo 'aaa';
  echo'  </body>
</html>';
?>



