#!/usr/bin/php
<?php
  //$ImgID = rand(1, 84);
  if($argc==3){
    $od=$argv[1];
    echo "od $od\n";
    $do=$argv[2];
    echo "do $do\n";
  }else{
    $od=0;
    $do=0;
  }
  
  $d = dir("samples");
  $i = 0;
  while (false !== ($entry = $d->read())) {
  if (strstr($entry, ".jpg")){
    $IID = (int)str_replace(".jpg", "", $entry);
    if(( $od!=0 || $do!=0 )&&( $IID>$do || $IID<$od))continue;
    $filelist[$i]  = $IID;
    echo "$IID\n";
    $i++;
  }

  }$d->close();

 $i = 0;
  
   
$dirx[0]=0;
$diry[0]=1;
$dirc[]=1;
/*$dirx[1]=1;
$diry[1]=1;
$dirc[]=1;
$dirx[2]=1;
$diry[2]=0;
$dirc[]=1;
$dirx[3]=1;
$diry[3]=-1;
$dirc[]=1;
$dirx[]=2;
$diry[]=1;
$dirc[]=1.5;
$dirx[]=1;
$diry[]=2;
$dirc[]=1.5;
$dirx[]=-2;
$diry[]=1;
$dirc[]=1.5;
$dirx[]=-1;
$diry[]=2;
$dirc[]=1.5;*/

$pavel_pokusy=3;//1 slevani, 2 spojovani dle vzdalenosti,3....


  
foreach($filelist as $id => $filename) {
  
  echo "$filename\n";
  $img=imagecreatefromjpeg("samples/"."$filename".".jpg");
  $sx=imagesx($img);
  $sy=imagesy($img);
  $bmp=imagecreate($sx,$sy);
  $black=imagecolorallocate($bmp,0,0,0);
  $white=imagecolorallocate($bmp,255,255,255);
  $black_img=imagecolorallocate($img,0,0,255);
  $blue_img=imagecolorallocate($img,0,0,255);
  $green_img=imagecolorallocate($img,0,100,0);
  $red_img=imagecolorallocate($img,255,0,0);
  $white_img=imagecolorallocate($img,255,255,255);
  imagefilledrectangle($bmp, 0, 0, $sx, $sy, $black);
  $spaceflag = 0;
  $spacecount = 0;
  $MINROWS = 13;
  $MAXROWS = 30;
  $rows_since_split = 0;

  
  
  // echo "$sx $sy \n";
  
  unset ($matrix);
  unset ($stackx);
  unset ($stacky);
  
  for($x=0;$x<$sx;++$x){  //x = row
    /*$line="";
    $lineraw = "";
    $count=0;*/
      
    for($y=0;$y<$sy;++$y){  //y = col
        $rgb = imagecolorat($img, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        $matr[$x][$y]=$r;
        $matg[$x][$y]=$g;
        $matb[$x][$y]=$b;
        if($r>100 && $g<100 && $g<220 && $b>100 && $b<200){
          //$line.= "r";
          //$count++;
          $matrix[$x][$y]="r";
          $stackr_x[]=$x;
          $stackr_y[]=$y;
        }
        else if ($r<30 && $g<30 && $b<30) {
          //$line.= "b";
          $matrix[$x][$y]="b";
          $stackx[]=$x;
          $stacky[]=$y;
        }
        else {
          //$line.= "w";
          $matrix[$x][$y]="w";
        }
    }
    
    //@@echo "$x $line \n" ;
    //$linemap[$x] = $line;
    //$countmap[$x] = $count;
    //echo "$count \n";
    
  }
  
  
  if($pavel_pokusy!=0){  //START pavel

    //pokus o pripojovani souvislych oblasti v cerne
    for($y=0;$y<$sy;++$y){  //x = row
      for($x=0;$x<$sx;++$x){
        echo $matrix[$x][$y];
      }
      echo"\n";
    }
    if($pavel_pokusy==4){
      //odstraneni r s <=1 sousedem - zk
      while($cx=array_shift($stackr_x)){
        $cy=array_shift($stackr_y);
        $neigh=0;
        for($ox=-1;$ox<2;$ox++){
          for($oy=-1;$oy<2;$oy++){
            if(($cx+$ox<$sx)&&($cx+$ox>=0)&&($cy+$oy<$sy)&&($cy+$oy>=0)){
              if($matrix[$cx+$ox][$cy+$oy]=='r')
                $neigh++;
            }
          }
        }
        if($neigh<2){
          $stackrrx[]=$cx;
          $stackrry[]=$cy;
        }
      }
      unset($neigh);

      while(isset($stackrrx) && $rx=array_shift($stackrrx)){
        $ry=array_shift($stackrry);
        $matrix[$rx][$ry]="w";
        $matr[$rx][$ry]=255;
        $matg[$rx][$ry]=255;
        $matb[$rx][$ry]=255;
        $stackx[]=$cx;
        $stacky[]=$cy;
      }  
    }
      for($y=0;$y<$sy;++$y){  //x = row
        for($x=0;$x<$sx;++$x){
          echo $matrix[$x][$y];
        }
        echo"\n";
      }
      //odstraneno
    if($pavel_pokusy==2){
      while($cx=array_shift($stackx)){
        $cy=array_shift($stacky);
        $bestr=10000;
        $best=10000;
        for($i=0;$i<1;$i++){
          $curr=0;
          $end1='';
          $end2='';
          $over=5;
          for($j=0;($cx+$j*$dirx[$i]<$sx)&&($cx+$j*$dirx[$i]>=0)&&($cy+$j*$diry[$i]<$sy)&&($cy+$j*$diry[$i]>=0);$j++){
            $curr+=$dirc[$i];
            if($matrix[$cx+$j*$dirx[$i]][$cy+$j*$diry[$i]]!='b'){
              if($matrix[$cx+$j*$dirx[$i]][$cy+$j*$diry[$i]]=='r' || $over--<0){
                $end1=$matrix[$cx+$j*$dirx[$i]][$cy+$j*$diry[$i]];
                break;
              }
            }
          }
          $over=5;
          for($j=0;($cx-$j*$dirx[$i]<$sx)&&($cx-$j*$dirx[$i]>=0)&&($cy-$j*$diry[$i]<$sy)&&($cy-$j*$diry[$i]>=0);$j++){
            $curr+=$dirc[$i];
            if($matrix[$cx-$j*$dirx[$i]][$cy-$j*$diry[$i]]!='b'){
              if($matrix[$cx-$j*$dirx[$i]][$cy-$j*$diry[$i]]=='r' || $over--<0){
                $end2=$matrix[$cx-$j*$dirx[$i]][$cy-$j*$diry[$i]];
                break;
              }
            }
          }
          if($end1=='r' && $end2=='r' && $curr<$bestr){
            $bestr=$curr;
          }
          if($curr<$best){
            $best=$curr;
          }
        }
        if($bestr<3*$best){
          //file_put_contents('php://stderr', "hmm\n");
          $stackrx[]=$cx;
          $stackry[]=$cy;
        }else{
          //file_put_contents('php://stderr', "$bestr $end1 $end2\n");
          $stackwx[]=$cx;
          $stackwy[]=$cy;
        }
      }
      while(isset($stackrx) && $rx=array_shift($stackrx)){
        $ry=array_shift($stackry);
        $matrix[$rx][$ry]="r";
        imagesetpixel($img,$rx,$ry,$red_img);
      }
      while($wx=array_shift($stackwx)){
        $wy=array_shift($stackwy);
        $matrix[$wx][$wy]="w";
        imagesetpixel($img,$wx,$wy,$white_img);
      }
      echo "\n";
      for($y=0;$y<$sy;++$y){  //x = row
        for($x=0;$x<$sx;++$x){
          echo $matrix[$x][$y];
        }
        echo"\n";
      }

    }

    if($pavel_pokusy==1){
      $stackx[]=-1;
      while($cx=array_shift($stackx)){//pokus o pripojovani souvislych oblasti
        if($cx==-1){
          $change=0;
          while($rx=array_shift($stackrx)){
            $ry=array_shift($stackry);
            $matrix[$rx][$ry]="r";
            $change=1;
          }
          while($wx=array_shift($stackwx)){
            $wy=array_shift($stackwy);
            $matrix[$wx][$wy]="w";
            $change=1;
          }
          if($change){
            $stackx[]=-1;
            continue;
          }else{
            break;
          }
        }
        $cy=array_shift($stacky);
        $neigh['w']=0;
        $neigh['r']=0;
        $neigh['b']=0;
        $sum=0;
        for($ox=-2;$ox<3;$ox++){
          for($oy=-2;$oy<3;$oy++){
            if(($cx+$ox<$sx)&&($cx+$ox>=0)&&($cy+$oy<$sy)&&($cy+$oy>=0)){
              $neigh[$matrix[$cx+$ox][$cy+$oy]]+=5-abs($ox)-abs($oy);
              $sum+=5-abs($ox)-abs($oy);
            }
          }
        }
        if($neigh["r"]>=0.28*$sum){
          $stackrx[]=$cx;
          $stackry[]=$cy;
          echo "$cx $cy\n";
        }elseif($neigh["w"]>=0.5*$sum){
          echo "w$cx $cy\n";
          $stackwx[]=$cx;
          $stackwy[]=$cy;
        }else{
          echo "_$cx $cy\n";
          $stackx[]=$cx;
          $stacky[]=$cy;
        }
      }
    }//pokusy 1
    if($pavel_pokusy==3){
      $abs=3;
      unset ($stackx);
      unset ($stacky);
      for($x=0;$x<$sx;++$x){  //x = row
        for($y=0;$y<$sy;++$y){  //y = col
           $rg=($matr[$x][$y]-$matg[$x][$y])*($matr[$x][$y]-$matg[$x][$y]);
           $rb=($matr[$x][$y]-$matb[$x][$y])*($matr[$x][$y]-$matb[$x][$y]);
           $bg=($matb[$x][$y]-$matg[$x][$y])*($matb[$x][$y]-$matg[$x][$y]);
					   $matrix[$x][$y]='r';
           if($rg+$rb+$bg<500 || ( $matr[$x][$y]<$matb[$x][$y] && $matr[$x][$y]<$matg[$x][$y])){
             imagesetpixel($img,$x,$y,$green_img);
             $matrix[$x][$y]='w';
					 }
					 if($matr[$x][$y]<60 && $matb[$x][$y]<60 && $matg[$x][$y]<60){
             $matrix[$x][$y]='b';
             imagesetpixel($img,$x,$y,$blue_img);
             $stackx[]=$x;
             $stacky[]=$y;
					 }
        }
      }
      $stackx[]=-1;
      
      while(($cx=array_shift($stackx))!=-1){//divani se nahoru
			  $cy=array_shift($stacky);
			  $over=1;
			  $end1='';
			  $end2='';
			  
				for($oy=0;$oy+$cy<$sy&&$over>0;$oy++){
			    if($matrix[$cx][$cy+$oy]=='w')
			      $over--;
			    elseif($matrix[$cx][$cy+$oy]=='r'){
					  $end1='r';
					  $r=$matr[$cx][$cy+$oy];
					  $g=$matg[$cx][$cy+$oy];
					  $b=$matb[$cx][$cy+$oy];
					  break;
					}
				}
			  $over+=1;
				for($oy=0;$oy+$cy>=0&&$over>0;$oy--){
			    if($matrix[$cx][$cy+$oy]=='w')
			      $over--;
			    elseif($matrix[$cx][$cy+$oy]=='r'){
					  $end2='r';
					  $r+=$matr[$cx][$cy+$oy];
					  $g+=$matg[$cx][$cy+$oy];
					  $b+=$matb[$cx][$cy+$oy];
					  break;
					}
				}
				if($end1==$end2&&$end1=='r'){
				  $matrix[$cx][$cy]='r';
				  $matr[$cx][$cy]=$r/2;
				  $matg[$cx][$cy]=$g/2;
				  $matb[$cx][$cy]=$b/2;
				  echo "s1 $cx $cy\n";
            $col=imagecolorallocate($img,$r/2,$g/2,$b/2);
          imagesetpixel($img,$cx,$cy,$col);
				}else{
				  echo "n1 $cx $cy\n";
          $stackx[]=$cx;
          $stacky[]=$cy;
				}
			}
  //imagepng($img,"tmp/$id.png");
  //continue;
			
      $stackx[]=-1;
      
      while($cx=array_shift($stackx)){//barvy okoli
        if($cx==-1){
          $change=0;
          while($rx=array_shift($stackrx)){
            $ry=array_shift($stackry);
            $r=array_shift($stackr);
            $g=array_shift($stackg);
            $b=array_shift($stackb);
            if($r<80 && $g<80 && $b<80){}//todo
            $matr[$rx][$ry]=$r;
            $matg[$rx][$ry]=$g;
            $matb[$rx][$ry]=$b;
            $matrix[$rx][$ry]="r";
            $col=imagecolorallocate($img,$r,$g,$b);
            imagesetpixel($img,$rx,$ry,$col);
            $change=1;
          }
          if($change){
            $stackx[]=-1;
            continue;
          }else{
            break;
          }
        }
        $cy=array_shift($stacky);
        echo ";$cx $cy \n";
        $neigh['w']=0;
        $neigh['r']=0;
        $neigh['b']=0;
        $sum=0;
        $sumc=0;
        $r=0;$g=0;$b=0;

        for($ox=-$abs;$ox<$abs+1;$ox++){
          for($oy=-$abs;$oy<$abs+1;$oy++){
            if(($cx+$ox<$sx)&&($cx+$ox>=0)&&($cy+$oy<$sy)&&($cy+$oy>=0)){
              $me=5-abs($ox)-abs($oy);
              $neigh[$matrix[$cx+$ox][$cy+$oy]]+=$me;
              $sum+=$me;
              if($matrix[$cx+$ox][$cy+$oy]!='b'){
                $sumc+=5-abs($ox)-abs($oy);
                $r+=$me*$matr[$cx+$ox][$cy+$oy];
                $g+=$me*$matg[$cx+$ox][$cy+$oy];
                $b+=$me*$matb[$cx+$ox][$cy+$oy];
              }
            }
          }
        }
        if(5*$sumc>=2*$sum){
          $stackrx[]=$cx;
          $stackry[]=$cy;
          $stackr[]=$r/$sumc;
          $stackg[]=$g/$sumc;
          $stackb[]=$b/$sumc;
          echo "$cx $cy \n";
        }else{
          echo "_$cx $cy\n";
          $stackx[]=$cx;
          $stacky[]=$cy;
        }
      }
  //imagepng($img,"tmp/$id.png");
  //continue;
      
      for($x=0;$x<$sx;++$x){  //x = row
        for($y=0;$y<$sy;++$y){  //y = col
           $rg=($matr[$x][$y]-$matg[$x][$y])*($matr[$x][$y]-$matg[$x][$y]);
           $rb=($matr[$x][$y]-$matb[$x][$y])*($matr[$x][$y]-$matb[$x][$y]);
           $bg=($matb[$x][$y]-$matg[$x][$y])*($matb[$x][$y]-$matg[$x][$y]);
           if($rg+$rb+$bg<500 || ( $matr[$x][$y]<$matb[$x][$y] && $matr[$x][$y]<$matg[$x][$y])){
             imagesetpixel($img,$x,$y,$green_img);//white
             $matrix[$x][$y]="w";
					 }else{
					   if($rg+$rb+$bg<3500 ){
               $matrix[$x][$y]="R";
               imagesetpixel($img,$x,$y,$blue_img);
             }else{
               $matrix[$x][$y]="r";
               //imagesetpixel($img,$x,$y,$red_img);
						 }
					 }
        }
      }
      
      echo "\n";
      for($y=0;$y<$sy;++$y){  //x = row
        for($x=0;$x<$sx;++$x){
          echo $matrix[$x][$y];
        }
        echo"\n";
      }


    }//pokusy 3


  }//END pavel

  //zapsani pokusu do pouzivanych dat
  for($x=0;$x<$sx;++$x){  //x = row
    $line="";
    $lineraw = "";
    $count=0;
      
    for($y=0;$y<$sy;++$y){  //y = col
        if($matrix[$x][$y]=="r"){
          $line.= "r";
          imagesetpixel($bmp, $x, $y, $white);
          $count++;
        }
        else if ($matrix[$x][$y]=="b") {
          $line.= "b";
        }
        else {
          $line.= $matrix[$x][$y];
        }
    }
    
    //echo "$x $line \n" ;
    $linemap[$x] = $line;
    $countmap[$x] = $count;
    //echo "$count \n";
    
  }
//konec pokusu
  
  
  imagepng($img,"tmp/$id.png");
  //continue;
  
  
  $splitcount = 0;
  $ROWS = count($countmap);
  for ($id = 0; $id < $ROWS; $id++) {
    $count = $countmap[$id];
    //@@echo "$id . $count \n";

    //if($spaceflag == 0) {
      $rows_since_split++;
    //}
    if($count == 0){        //zero row
      if($spaceflag == 0) {  //new space
        $spaceflag = 1;
        //$spacecount++;
        continue;
      }
      else {                //continuing space
        continue;
      }
    }
    else {                    //nonzero row
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
    if ($lastsplit == $split[$splitcount-1]) $splitcount--;
  }


  while ($splitcount < 7) {
    $maxlength = 0;
    $maxsplitID = 0;
    foreach($split as $SID => $SRID) {
      if(($SID == 0) || ($SID == 7)) {
        continue;
      }
      else {
        $Slength = $SRID - $split[$SID-1];
      }
      if ($Slength > $maxlength) {
        $maxlength = $Slength;
        $maxsplitID = $SID;
      }
    
    }
    echo "found longest section at $maxsplitID, ";
    $MINROWSred = 10;
    $mincount = 100;
    $minrow = 0;
    $istart = $split[$maxsplitID - 1] + $MINROWSred;
    $istop = $split[$maxsplitID] - $MINROWSred;
    for($i = $istart; $i < $istop; $i++ ) {
      if ($countmap[$i] < $mincount) {
        $mincount = $countmap[$i];
        $minrow = $i;    
      }  
    }
    echo "found minimal row at $minrow \n";
    $i = 0;
    for ($s = $splitcount; $s > 0; $s--) {
      if ($s > $maxsplitID) {
        $split[$s+1] = $split[$s];
      }

      else {
        $split[$s+1] = $split[$s];
        $split[$s] = $minrow;
        $splitcount++;
        echo "split up \n";
        break;
      }
    }
    echo "not enough splits: $splitcount \n";
  }
 	 
	$split[$splitcount] = $lastsplit;

  
	$kuba_pokusy = 1;
	
	if ($kuba_pokusy == 1 ) {
		while ($splitcount >= 8)	{
			$SID = 1;
			$min = 200;
			$minID = -1;
			for ($i = $SID; $i < $splitcount; $i++) {
				$lsum = $split[$i+1] - $split[$i-1];
				if ($lsum < $min){
					$min = $lsum;
					$minID = $i;
				}			
			}
			$i = 0;
			if ($minID >= -1) {
				for ($i = $minID; $i < $splitcount; $i++ ){
					$split[$i] = $split[$i+1];
				}
				$splitcount--;
				$i = 0;
			}
		}		
		for ($i = 0; $i < $splitcount; $i++){
			$clc[$i] = round(($split[$i+1] + $split[$i]) / 2);		
		
			echo "$i. cluster center at $clc[$i]\n ";
			$clcoffset[$i] = 0;
			$clccount[$i] = 0;
		}
		$Fchange = true;
		$limit = 100;
		$num_iter = 0;
		while (($num_iter < $limit) && ($Fchange)) {
			for ($i = 0; $i < $splitcount; $i++) {
				$clcoffset[$i] = 0;
				$clccount[$i] = 0;
			}
			$num_iter++;
			$FChange = false;
			$CLC1 = 0;
			$CLC2 = 1;
			for ($i = $split[0]; $i < $split[$splitcount]; $i++) {
				if ($i >= $clc[$CLC2]) {
					if ($CLC2 < 6) {
						$CLC2++;
						$CLC1++;
					}
				}
				if (abs($i - $clc[$CLC1]) <= abs($i - $clc[$CLC2])) {
					$clcoffset[$CLC1] += ($countmap[$i] * ($i - $clc[$CLC1]));				
					$clccount[$CLC1]+= $countmap[$i];
					//echo "adding $i to $CLC1, offset now $clcoffset[$CLC1] \n";
				}
				else {
					$clcoffset[$CLC2] += ($countmap[$i] * ($i - $clc[$CLC2]));				
					$clccount[$CLC2]+= $countmap[$i];
					//echo "adding $i to $CLC2, offset now $clcoffset[$CLC2] \n";
				}

			}

			for($i = 0; $i < $splitcount; $i++) {
				if (floor($clcoffset[$i] != 0)) $Fchange = true;
				$clc[$i] += floor($clcoffset[$i] / $clccount[$i]);
			}

		}
	
	}
	echo "\nCluster centers: \n";
	for ($i = 0; $i < $splitcount; $i++) {
		echo "$clc[$i]\n";	
	}
	echo "cluster centers end\n";


  $myFILE = fopen("splits/c/c"."$filename"."_0.txt", "w");
  //echo $myFILE."\n";
  $splitID = 1;
  $rowID = 0;
  $written = 0;
  $TXTH = 30;
  $TXTW = 50;

  $nullrow = "";
  for ($i = 0; $i < $TXTW; $i++) {
    $nullrow = $nullrow."w";
  }

  $i = 0;

      //$splitID["-1"] = 0;
  if (isset($out_image)) {
    unset($out_image);
  }
  foreach ($linemap as $id => $line) {
    $count = $countmap[$id];
    if ($count > 0){
      $rowID = $id - $split[$splitID-1];
      $out_image[$splitID][$rowID] = $line;
      //fwrite($myFILE, "$id ".$line."\n" );
      $written++;
    }
    
    if ( ($id <= $lastsplit) && ($id == $split[$splitID])){
      $tdif = 0;
      if ($written < $TXTH) {
        $tdif = floor(($TXTH - $written)/2);
        //fwrite ($myFILE, $tdif."\n");
      }
      //echo $myFILE."\n";
      for($w = 0; $w < $tdif; $w++) {
        fwrite($myFILE, $nullrow."\n");
      }
      foreach ($out_image[$splitID] as $id => $line) {
        if ($w >= 30) break;
        fwrite($myFILE, $line."\n");
        $w++;
      }
      for(; $w < $TXTH; $w++) {
        fwrite($myFILE, $nullrow."\n");
      }
      $w = 0;
      $written = 0;
      $splitID++;
      fclose($myFILE);
      $splitIDout = $splitID - 1;
      if ($splitID <= $splitcount) { 
        $myFILE = fopen("splits/c/c"."$filename"."_$splitIDout.txt", "w");
      }
    }
  }
  //fclose($myFILE);
  
  foreach($out_image as $imageID => $imagemap) {
    if ($imageID != 0) {
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
    $imageIDout = $imageID - 1;
    imagepng($out_image, "splits/png/s"."$filename"."_$imageIDout.png");


    foreach($imagemap as $id => $line) {
      //@@echo "$id $line \n";
    }
    //@@echo "\n";
    }
  }
}


?>
