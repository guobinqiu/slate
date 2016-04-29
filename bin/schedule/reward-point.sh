#!/bin/bash

DATE=`date +%Y-%m-%d --date="-1 day"`
ACTIONS=(
reward-sop-point
reward-sop-additional-point
reward-fulcrum-point
reward-fulcrum-agreement
reward-cint-point
reward-ssi-point
)

cd $(dirname $0)/../../;
for ACTION in ${ACTIONS[@]}; do
    CMD="./app/console panel:${ACTION} --definitive ${DATE} -e prod"
    ${CMD}
    if [ "$?" -ne 0 ]
    then
        echo "FAILURE: ${CMD}" 1>&2
    fi
done

