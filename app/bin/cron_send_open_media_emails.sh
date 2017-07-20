#!/bin/bash

if [ ! $path_app ]; then
	path=`dirname $(readlink -f $0)`
	path=`dirname ${path}`
	path=`dirname ${path}`
	source ${path}/lib/scripts/paths.sh
fi

source ${path}/lib/scripts/run_check.sh

me=`basename $0`
time_start=`date +%s`
echo "Running: ${me} with pid of $$ - starting on: `date`"

${cmd_cron} send_open_media_emails -q

time_end=`date +%s`
time_diff=$(expr ${time_end} - ${time_start})
echo "Completed: ${me} with pid of $$ - seconds to complete: ${time_diff}"