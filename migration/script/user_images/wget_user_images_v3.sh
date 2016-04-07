#!/bin/bash

# image list file exists here:
src=/data/91jili/merge/user_images/

if [ $# -eq 1 ] && [ -f $1 ] ; then
  images_fn_file=$1
else
  images_fn_file=/data/91jili/merge/user_image_file_.csv
  awk -F, '{if( NR>1) print $3}' /data/91jili/merge/ww_csv/panel_91wenwen_panelist_profile_image.csv  > ${images_fn_file} 
fi

# download to this dir:
dst=/data/91jili/web/uploads/user/
URL_HOST=http://d1909s8qem9bat.cloudfront.net/user_profile


# recaculate  the line count for images file name 
total_lines=`cat ${images_fn_file}|wc -l`

echo 'number of total:' ${total_lines};

count_lacks() 
{
#----- how many files needs to downloads again
  number_of_lacks=0;
  while read line;
  do
      single_imgage_file=${dst}${line} ;
      if [ ! -f ${single_imgage_file} ]; then 
         number_of_lacks=$(( 1 + ${number_of_lacks} )) 
       fi
  done < ${images_fn_file} 
  
  echo number of lacks:${number_of_lacks};
}

count_lacks;


if [ 0 -eq  ${number_of_lacks} ]; then
   exit;
fi

ceil() {
    local dividend=$1
    local dividor=$2
    floor=$[ ${dividend}/${dividor} ];
    if [[ 0 <  $[${dividend} % ${dividor} ]  ]] ; then
      echo `expr 1 + ${floor}`;
    else
      echo ${floor} ;
    fi
}

count_per_file=10000;

count_splited=`ceil ${total_lines} ${count_per_file}`;
echo "number of splited:"  ${count_splited};

OUTPUT_DIR=/tmp/migrate_user_images;
SPLIT_PREFIX=images_

# clean the  OUTPUT_DIR 
mkdir -p ${OUTPUT_DIR}
rm -rf ${OUTPUT_DIR}/*
split -a 5  -l ${count_per_file} ${images_fn_file} ${OUTPUT_DIR}/${SPLIT_PREFIX}





do_wget() 
{
    local images_fn_file=$1;
    local url_host=$2;
    local thread_window=10;                                                                                                                                              
    local counter=0;

    while read line;
    do
        single_imgage_file=${dst}${line} ;
        if [ ! -f ${single_imgage_file} ]; then 
            echo "INFO: ${url_host}/${line}";
            echo "INFO:    ->${single_imgage_file}";
            wget  -c -xP ${dst} --cut-dirs=1 --no-host-directories -T 180 ${url_host}/${line}  &
            counter=$[1+${counter}];
            number_of_downloaded=$[1+${number_of_downloaded}];

            if [[ ${counter} -ge  ${thread_window} ]]; then
                echo 'INFO: jobs list';
                jobs -l
                echo ...
                wait;
                counter=0;
            fi
       fi
    done < ${images_fn_file}
}
number_of_downloaded=0;
# read files
files=(`find ${OUTPUT_DIR} -type f -name "${SPLIT_PREFIX}[a-z][a-z][a-z][a-z][a-z]"`)
for f in ${files[@]}; do
    fn=`basename ${f}`;

    if [ -f ${OUTPUT_DIR}/${fn} ]; then
      echo . 
      echo "INFO: "${fn}
      do_wget ${f}  ${URL_HOST}
    fi
done

echo number of downloaded:${number_of_downloaded};
print_lacks() 
{
#----- how many files needs to downloads again
  number_of_lacks=0;
  while read line;
  do
      single_imgage_file=${dst}${line} ;
      if [ ! -f ${single_imgage_file} ]; then 
         number_of_lacks=$(( 1 + ${number_of_lacks} )) ;
         echo LACK:${single_imgage_file};
       fi
  done < ${images_fn_file} 
  
  echo number of lacks:${number_of_lacks};
}
print_lacks;
echo '...done'

#for subdir in  {{0..9},{a..f}}/{{0..9},{a..f}}/{{0..9},{a..f}};
#do
#if [[ ! -d ${dst}${subdir} ]]; then
#  mkdir -p ${dst}${subdir} 
#fi
#done;

# ${dst}${line:0:1}/${line:1:1}/${line:2:1}
#  cp -a  ${src}$line ${dst}${line:0:1}/${line:1:1}/${line:2:1}


#
#
#ls -1 ${src} | while read line
#do
#  cp -a  ${src}$line ${dst}${line:0:1}/${line:1:1}/${line:2:1}
#done

#mysql -B -uroot -pecnavi jili_db -e "select icon_path from user where icon_path != '' and  vote_image is not null"| sed '/vote_image/d;s/\(\w\)\(\w\)\(.*\).\(jpg\|png\)/http:\/\/d1909s8qem9bat.cloudfront.net\/vote_image\/\1\/\2\/\1\2\3_s.\4/' | xargs wget -N -P /tmp/vote_images/ 
