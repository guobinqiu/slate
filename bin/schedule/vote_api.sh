#! /bin/bash
# crontab 
#  bash ...../vote_api.sh &2>/tmp/a.log &1>/dev/null 

cd $(dirname $0)/../../;
./app/console jili:vote_api  -e prod 
