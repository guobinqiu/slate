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

