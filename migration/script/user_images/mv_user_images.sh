#! /bin/bash

src=/tmp/vote_images/
dst=/data/91jili/web/uploads/vote_image/

rm -rf ${dst}
mkdir -p ${dst}{0..9}/{a..f}/
mkdir -p ${dst}{0..9}/{0..9}/
mkdir -p ${dst}{a..f}/{a..f}/
mkdir -p ${dst}{a..f}/{0..9}/


ls -1 ${src} | while read line
do
  cp  ${src}$line ${dst}${line:0:1}/${line:1:1}/
done

mysql -B -uroot -pecnavi jili_db -e "select vote_image from vote where vote_image != '' and  vote_image is not null"| sed '/vote_image/d;s/\(\w\)\(\w\)\(.*\).\(jpg\|png\)/http:\/\/d1909s8qem9bat.cloudfront.net\/vote_image\/\1\/\2\/\1\2\3_s.\4/' | xargs wget -N -P /tmp/vote_images/ 

