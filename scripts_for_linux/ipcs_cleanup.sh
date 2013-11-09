#!/bin/sh
#解决System V Queue爆掉需要清理的问题

for qid in `ipcs -q | grep \`whoami\` | awk '{print $2}'`; do ipcrm -q $qid; done