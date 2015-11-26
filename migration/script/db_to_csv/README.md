### login to jili-web

# ssh login to remote jiil-web

###  change the current Directory


`cd migration/script/db_to_csv`

### generate the dump bash shell & scp to the remote server

`make dump-jl`

`make dump-ww`


### run the dump shell on remote server


```
# on jili-web

cd /data/91jili/merge
screen -S dump
bash bin/jl_csv.sh  1> perf_log_`date +"T%H%M%SD%Y%m%d"`.txt  2>&1



#ssh login to rpa-staff01 from the office LAN ( because IP restrict)


cd /mnt/tmp/merge/
scp -P webuser@www.91jili.com:/data/91jili/merge/bin/ww_csv.sh /mnt/tmp/merge/bin/

screen -S dump
bash bin/ww_csv.sh 1> log/dump_to_csv_log_`date +%F`.txt 2>&1

# wait abount  4min

INPUT  the account password to scp he comparess file.

```



### copy the ww_csv.tar.gz tarball to  jili-web

* Copy the tarball from `rpa-staff01` to `235(dev.rpa-sh)`  
* Copy the tarball from   `235(dev.rpa-sh)`   to `jil-web`

 
### decompress on jili-web
 

```
cd /data/91jili/merge
tar xjvf jl_csv.tar.bz
tar xjvf ww_csv.tar.bz
```




