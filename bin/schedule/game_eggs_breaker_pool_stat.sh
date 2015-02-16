#! /bin/bash
# for  crontab 

cd $(dirname $0)/../../;
./app/console  game:eggsBreaker --pool-alarm  -e prod
