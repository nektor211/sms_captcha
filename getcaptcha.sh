url=http://sms.t-zones.cz/open/captcha.jpg
if [ $# = 1 ]; then
echo $1
else
echo 'parmetr' $#
exit
fi	
#wpar=" --cookies=on --keep-session-cookies --load-cookies=${dir}cookie.txt --save-cookies=${dir}cookie.txt "
files=`ls samples/*.jpg |wc -l`

files=$(($files + 1))
filesn=$(($files + $1-1))
#wget $wpar $url
for x in `seq $files $filesn` 
do
  echo $x
  wget $wpar $url -O samples/${x}.jpg
  ./nap.pl 200
done
