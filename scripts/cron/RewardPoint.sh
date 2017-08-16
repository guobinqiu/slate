SHELL_NAME=$( basename "${BASH_SOURCE[0]}" )
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )/../../" && pwd )"
LOG_DIR="${SCRIPT_DIR}/logs"
CURRENT_TIME=`date "+%Y-%m-%d_%H%M%S"`
LOG_FILE="${LOG_DIR}/${SHELL_NAME}.${CURRENT_TIME}.log"

DATE=`date +%Y-%m-%d --date="-1 day"`
ACTIONS=(
reward-sop-point
reward-sop-additional-point
reward-fulcrum-point
reward-fulcrum-agreement
reward-cint-point
reward-ssi-point
)

for ACTION in ${ACTIONS[@]}; do
    ${PROJECT_DIR}/app/console panel:${ACTION} --env dev ${DATE} >> $LOG_FILE 2>&1
    #${PROJECT_DIR}/app/console panel:${ACTION} --env prod --definitive --resultNotification ${DATE} > $LOG_FILE 2>&1
done

