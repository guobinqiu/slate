#! /bin/bash

src=/data/91jili/merge/user_images/
dst=/data/91jili/web/uploads/user/


for subdir in  {{0..9},{a..f}}/{{0..9},{a..f}}/{{0..9},{a..f}};
do
if [[ ! -d ${dst}${subdir} ]]; then
  mkdir -p ${dst}${subdir} 
fi
done;


ls -1 ${src} | while read line
do
  cp -a  ${src}$line ${dst}${line:0:1}/${line:1:1}/${line:2:1}
done

