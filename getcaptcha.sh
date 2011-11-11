url=http://sms.t-zones.cz/open/captcha.jpg
#wpar=" --cookies=on --keep-session-cookies --load-cookies=${dir}cookie.txt --save-cookies=${dir}cookie.txt "
for x in `seq 0 2` 
do
  wget $wpar $url -O samples/${x}.jpg
  ./nap.pl 200
done
