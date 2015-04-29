#!/bin/bash
# 将目录from_path中的非.jpg的图片转为 .jpg，并保存在to_path/

if [[ -z `which convert`  ]]; then
    echo 'The command convert  not found!'
    exit;
fi

echo $0 
echo 'from: ' $1
echo 'to: '$2

if [[ $# != 2 ]]; then
    echo $0 from_path/ to_path/ 
    exit;
fi

mkdir -p $2

files=( $(find $1 -maxdepth 1 -type f -print) )

len_path=`echo $1|wc -c`
len_path=`expr ${len_path} + 1`

for file in ${files[@]}; do
    #取图片的文件名
    filename=`expr substr ${file} ${len_path} 100`;
    #取扩展名前面的分隔符的位置
    pos_exten=`expr index "${filename}" "\."`;
    pos_exten=`expr ${pos_exten} - 1`

    #取图片的文件名(无扩展名)
    filename_=`expr substr ${filename} 1 ${pos_exten}`;

    #如果原文件是.jpg
    if [[ -f "$1/${filename_}.jpg"  ]]; then
        continue;
    fi

    #如果目标文件是.jpg
    if [[ -f "$2/${filename_}.jpg"  ]]; then
        continue;
    fi
    convert "$file" "$2/${filename_}.jpg"
done

