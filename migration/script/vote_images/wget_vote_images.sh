#!/bin/bash
echo 'start...'`date "+%c"`

mysql -B -uroot -pecnavi jili_db -e "select vote_image from vote where vote_image != '' and  vote_image is not null"| sed '/vote_image/d;s/\(\w\)\(\w\)\(.*\).\(jpg\|png\)/http:\/\/d1909s8qem9bat.cloudfront.net\/vote_image\/\1\/\2\/\1\2\3_s.\4/' | xargs wget -N -P /tmp/vote_images/ 

echo '...done'`date "+%c"`
