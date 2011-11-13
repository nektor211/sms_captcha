#!/bin/bash
if [ "$1" == "-h" -o "$#" == "0" ]; then
	echo 'use -t for training, -e for evaluation, -r for running' ;
	exit;
fi;

featuredir="splits/c";
tagdir="splits/txt";
octavedir="bin";


	echo -e "specify dirs? (y/n)";
	read a;
	if [ "$a" == "n" ]; then
		echo -e "using defaults";
	else
		echo "enter feature dir";
		read featruedir;
		echo "enter tag dir";
		read tagdir;
		echo "enter octave dir";
		read octavedir;
	fi;
	
	
if [ "$1" == "-t" -o "$1" == "-e" ]; then
	numlistF=`(for i in \`ls $featuredir\`; do echo $i | sed 's/^c\(.*\)_\(.*\)\.txt$/\1\.\2/'; done;) | sort -n | uniq`

  #echo "$numlistF";
	minF=`echo "$numlistF" | head -n 1;`
	maxF=`echo "$numlistF" | tail -n 1;`

	echo "found fetaures between $minF and  $maxF in $featuredir";
	echo "use all? (y/n)";
	read b;

	if [ "$b" == "y" ]; then
		lowbound=$minF;
		hibound=$maxF;
	else 
		echo "specify lower bound";
		read lowbound;
		echo "specify higher bound";
		read hibound;
	fi;

	outlistF=""
	for i in `echo $numlistF`; do
		cond1=`echo "$i >= $lowbound" | bc`;
		cond2=`echo "$i <= $hibound" | bc`;
		#echo "$cond1 $cond2"
		if (( $cond1 )) && (( $cond2 )); then
			outlistF="$outlistF$i"$'\n';			
			
		fi;
	done;

 	echo "using the following dataset:";
	echo "$outlistF";
	echo "looking for corresponding tags in $tagdir";
	if [ -d "temp" ]; then
		:
	else
		mkdir temp;
	fi;
	
	if [ -d "temp/features" ]; then
		:
	else
		mkdir temp/features;
	fi;
	
	if [ -d "temp/tags" ]; then
		:
	else
		mkdir temp/tags;
	fi;
		
	for i in `echo $outlistF`; do
		tagname=`echo $i | sed 's/^\(.*\)\.\(.*\)$/s\1_\2.txt/'`;
		fname=`echo $i | sed 's/^\(.*\)\.\(.*\)$/c\1_\2.txt/'`;

		if [ -f "$tagdir/$tagname" ]; then
			if [ -f "$featuredir/$fname" ]; then
				cp "$tagdir/$tagname" "temp/tags/$tagname";
				cp "$featuredir/$fname" "temp/features/$featurename";
			else 
				echo "features corresponding to $fname not found in $featuredir, skipping "
			fi;
		else 
			echo "tag corresponding to $tagname not found in $tagdir, skipping"
		fi;
		
	done;
	g2g=1;


	if [ "$1" == "-t" ]; then
		if [ -f "$octavedir/Theta1.mat" -o -f "$octavedir/Theta2.mat" ]; then
			g2g=0;
			echo "data already trained, remove? (cannot retrain if data nor removed)";
			read c;
			if [ "$c" == "y" ]; then
				rm -f "$octavedir/Theta1.mat";
				rm -f "$octavedir/Theta2.mat";
				g2g=1;
	
			fi;
		fi;
		curdir=`pwd`;
		if [ $g2g -eq 1 ]; then
			php unroll.php temp/features temp/tags "$octavedir";
			cd "$octavedir";
			octave ex4train.m
			cd "$curdir";
		fi;
		rm -rf temp;
	
	elif [ "$1" == "-e" ]; then 
		if [ -f "$octavedir/Theta1.mat" -a -f "$octavedir/Theta2.mat" ]; then
			php unroll.php temp/features temp/tags "$octavedir";
			cd "$octavedir";
			octave ex4eval.m
			cd "$curdir";
		else 
			echo "neural netowrk not trained yet, please train before evaluation";
			echo "aborting";			
		fi;
		rm -rf temp;
	
	fi;






fi;
