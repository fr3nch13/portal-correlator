#!/bin/bash

if [ ! $path_app ]; then
	path=`dirname $(readlink -f $0)`
	path=`dirname ${path}`
	source ${path}/lib/scripts/paths.sh
fi

##################################################
echo ${hr}
echo
echo "Update the records in the database.";
echo ${hr}

echo "Scan vectors to make sure they have proper records in the hostnames and ip addresses tables.";
${cmd_update} vector_scan