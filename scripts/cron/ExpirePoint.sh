#!/bin/bash
SHELL_NAME=$( basename "${BASH_SOURCE[0]}" )
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )/../../" && pwd )"
LOG_DIR="${SCRIPT_DIR}/logs"
CURRENT_TIME=`date "+%Y-%m-%d_%H%M%S"`
LOG_FILE="${LOG_DIR}/${SHELL_NAME}.${CURRENT_TIME}.log"

${PROJECT_DIR}/app/console point:expire --env prod --baseDate=now --realMode > $LOG_FILE 2>&1
