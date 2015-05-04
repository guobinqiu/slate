
### 环境准备 
* 数据表已经创建(src/Jili/FronendBundle/Resources/doc/issue680.sql)
* perl和carton

* Imagemagick 


### 如何下载商家
* cd ${当前目录}

```bash
# 将cpanfile中的perl module安装
carton install
```

* 修改config/db.yml中的数据库连接配置
* 修改config/config.yml中的emar,chanet,duomai帐号
* 下载duomai商家

```
perl -I lib duomai.pl > duomai_`date +%Y%m%d%H%M`.log 

```

* 下载chanet商家
```
perl -I lib chanet.pl > chanet_`date +%Y%m%d%H%M`.log 
# 需要输入验证图片的内容

```

* 下载emar商家
```
perl -I lib yiqifa_html.pl > yiqifa_html_`date +%Y%m%d%H%M`.log 
# 需要输入验证图片的内容

```

* 将商家总合到cps_advertisement表
```
perl -I lib cps.pl
```


### 如何下载商家logo
* 将商家总合到cps_advertisement表时已经将logo下载好的。
* 整理logo文件。

```
#1. 将.gif .png转为jpg
#2. 与目前在使用中的 .jpg合并

mkdir /tmp/logos
rm -rf /tmp/logos
bash convert_batch.sh   /data/91jili/cps/chanet/logo /tmp/logos
bash convert_batch.sh   /data/91jili/cps/duomai/logo /tmp/logos

cd  /tmp/logos
find /data/91jili/cps/*/logo/*.jpg -exec cp {} \;

cp -rf /var/www/html/jili/web/images/website_logos/*  /tmp/logos 
cp -rf  /tmp/logos/* /var/www/html/jili/web/images/website_logos/

```

# 如何检查商家下载是否完成

* 查数据表
```
#如chanet
select count(*) from chanet_advertisement where is_activated = true;
select count(*) from chanet_commission where is_activated = true;

```

* cps.pl生成的结果
```
|||rows deactive(1->2): 535
|||rows activated(0->1): 535
|||rows updated emar_advertisement.seleted_at: 349
|||rows updated chanet_advertisement.seleted_at: 76
|||rows updated duomai_advertisement.seleted_at: 110
|||rows deleted (is_activated==2): 535
|||rows website_name_dictionary_index(updated): 535
```


