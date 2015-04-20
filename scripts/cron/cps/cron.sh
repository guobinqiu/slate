#!/bin/bash

echo 'OK';
DIR="$(cd $(dirname $0);pwd)";
#perl -I${DIR}/lib  ${DIR}/duomai.pl
perl -I${DIR}/lib  ${DIR}/yiqifa_html.pl
perl -I${DIR}/lib  ${DIR}/chanet.pl

perl -I${DIR}/lib  ${DIR}/cps.pl

