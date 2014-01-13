<?php
$addr=file("/afs/ms/u/t/tauferp/history/my_sms/addr.txt");
$cook="/afs/ms/u/t/tauferp/WWW/cookie.txt";
$gate='http://sms.t-zones.cz';
if($_POST["num"]){
	die("todo");
}
$wpar=" --cookies=on --keep-session-cookies --load-cookies=${cook} --save-cookies=${cook}";
echo `wget $gate/open.jsp $wpar 2>&1`;
$out= shell_exec('ls');
echo $out;
//echo safe_mode();
echo phpinfo();

$n="\n";
  echo'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=windows-1250">
  <title></title>
  </head>
  <body>';
  echo '<form action="send.php" method="post">'.$n;
  echo 'Cislo:<input id="num" type="text" name="num">'.$n;
  echo '<select name="contact" onchange="document.getElementById(\'num\').value=this.value;">'.$n;
	echo '<option value=""></option>';
  foreach($addr as $key=>$value){
    $data=explode(';', $value);
	  echo '<option value="'.$data[1].'">'.$data[0].'</option>';
	}
	echo '</select><br>';
	echo '<textarea name="text"></textarea>';
	
//  var_dump($addr);
  echo 'aaa';
  echo'  </body>
</html>';
?>



