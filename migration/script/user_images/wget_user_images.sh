#!/bin/bash

#/var/www/html/jili-jarod/jili-web/web/uploads/user

src=/data/91jili/merge/user_images/

#mysql -B -uroot -pecnavi jili_db -e "select icon_path from user where icon_path != '' and  vote_image is not null"| sed '/vote_image/d;s/\(\w\)\(\w\)\(.*\).\(jpg\|png\)/http:\/\/d1909s8qem9bat.cloudfront.net\/vote_image\/\1\/\2\/\1\2\3_s.\4/' | xargs wget -N -P /tmp/vote_images/ 


awk -F, '{if( NR>1) print $3}' /data/91jili/merge/panel_91wenwen_panelist_profile_image_for_wget.csv |
xargs -I {}  wget -N  -P ${src} 'http://d1909s8qem9bat.cloudfront.net/user_profile/'{}    




#// <img src="http://d1909s8qem9bat.cloudfront.net/user_profile/6/9/a/69a46212a00f370a50c74e6d52d0dfca69c07bcf_m.jpg" width="60" height="60" alt="赖以's icon">

